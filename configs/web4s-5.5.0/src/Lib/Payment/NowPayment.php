<?php

namespace App\Lib\Payment;

use Cake\ORM\TableRegistry;
use App\Lib\Payment\PaymentUtilities;
use Cake\Core\Exception\Exception;
use Cake\Http\Client;
use Cake\Log\Log;

class NowPayment
{
    protected $payment_url = '';
    protected $config = [];

    public function __construct($params)
    {
        $this->config = TableRegistry::get('PaymentsGateway')->getConfig(NOWPAYMENT);

        // now payment no sandbox mode
        $this->payment_url = 'https://api.nowpayments.io';
    }

    public function sendToGateway($params = []) 
    {
        $utilities = new PaymentUtilities();

        $api_key = !empty($this->config['api_key']) ? $this->config['api_key'] : null;
        if(empty($api_key)) return $utilities->getResponse([MESSAGE => __d('template', 'thong_tin_cau_hinh_chua_hop_le')]);

        $payment_code = !empty($params['payment_code']) ? $params['payment_code'] : '';

        $amount = !empty($params['amount']) ? $params['amount'] : 0;
        $ipn_url = !empty($params['ipn_url']) ? $params['ipn_url'] : '';
        $redirect_url = !empty($params['redirect_url']) ? $params['redirect_url'] : '';
        $cancel_url = !empty($params['cancel_url']) ? $params['cancel_url'] : '';

        $bill_id = !empty($params['bill_id']) ? $params['bill_id'] : '';
        $bill_code = !empty($params['bill_code']) ? $params['bill_code'] : '';
        $order_description = $order_description = !empty($params['order_description']) ? $params['order_description'] : '';

        if(empty($payment_code) || empty($bill_id)){
            return $utilities->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_giao_dich')]);
        }

        $amount = $utilities->transferToUsd($amount);
        $amount = 7;
        $data_post = [
            'price_amount' => $amount,
            'price_currency' => 'usd',
            'order_id' => $bill_code,
            'order_description' => $order_description,
            // 'ipn_callback_url' => $ipn_url,
            'success_url' => $redirect_url,
            'cancel_url' => $cancel_url,
            'is_fixed_rate' => true,
            'is_fee_paid_by_user' => false

        ];

        // create charges
        $http = new Client();
        $response = $http->post($this->payment_url . '/v1/invoice', json_encode($data_post),
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'x-api-key' => $api_key,
                ]
            ]
        );
        $json_result = $response->getStringBody();
        if(empty($json_result) || !$utilities->isJson($json_result)){
            return $utilities->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $result = json_decode($json_result, true);
        $invoice_id = !empty($result['id']) ? $result['id'] : null;
        $pay_url = !empty($result['invoice_url']) ? $result['invoice_url'] : null;

        if(empty($invoice_id)){
            $message = !empty($result[ERROR][MESSAGE]) ? $result[ERROR][MESSAGE] : __d('template', 'du_lieu_khong_hop_le');
            return $utilities->getResponse([MESSAGE => $message]);
        }

        if(empty($pay_url)){
            return $utilities->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_duong_dan_thanh_toan')]);
        }

        // cập nhật mã đơn hàng của coinbase vào db để khi coinbase trả về kết quả giao dịch sẽ lấy thông tin dựa theo mã này
        $table = TableRegistry::get('Payments');
        $payment_info = $table->find()->where(['code' => $payment_code])->select(['id', 'reference'])->first();
        if(empty($payment_info)){
            return $utilities->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_giao_dich')]);
        }

        $entity = $table->patchEntity($payment_info, [
            'reference' => $invoice_id
        ]);

        $update = $table->save($entity);
        if(empty($update->id)){
            throw new Exception(__d('template', 'loi_khi_cap_nhap_thong_tin_giao_dich'));
        }

        return $utilities->getResponse([
            CODE => SUCCESS,
            DATA => [
                'pay_url' => $pay_url
            ]
        ]);
    }

    public function returnResult($params = [])
    {
        $utilities = new PaymentUtilities();

        $api_key = !empty($this->config['api_key']) ? $this->config['api_key'] : null;
        if(empty($api_key)) return $utilities->getResponse([MESSAGE => __d('template', 'thong_tin_cau_hinh_chua_hop_le')]);

        if(empty($params) || !is_array($params)){
            return $utilities->getResponse([
                MESSAGE => __d('template', 'chu_ky_khong_hop_le')
            ]);
        }
        
        $now_payment_id = !empty($params['NP_id']) ? $params['NP_id'] : null;
        if(empty($now_payment_id)){
            return $utilities->getResponse([
                MESSAGE => __d('template', 'chu_ky_khong_hop_le')
            ]);
        }

        // lấy thông tin giao dịch
        $http = new Client();
        $response = $http->get($this->payment_url . '/v1/payment/' . $now_payment_id, [],
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'x-api-key' => $api_key,
                ]
            ]
        );
        $json_result = $response->getStringBody();
        if(empty($json_result) || !$utilities->isJson($json_result)){
            return $utilities->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_giao_dich')]);
        }

        $checkout = json_decode($json_result, true);
        if(empty($checkout['invoice_id'])){
            $message = !empty($checkout[MESSAGE]) ? $checkout[MESSAGE] : __d('template', 'khong_lay_duoc_thong_tin_giao_dich');
            return $utilities->getResponse([MESSAGE => $message]);
        }

        $payment_status = !empty($checkout['payment_status']) ? $checkout['payment_status'] : 'failed';        
        if(!in_array($payment_status, ['finished', 'sending', 'confirmed'])){
            $message = !empty($checkout[MESSAGE]) ? $checkout[MESSAGE] : __d('template', 'giao_dich_khong_thanh_cong');
            return $utilities->getResponse([MESSAGE => $message]);
        }

        // lấy thông tin giao dịch
        $payment_info = TableRegistry::get('Payments')->find()->where([
            'payment_gateway_code' => NOWPAYMENT,
            'reference' => $checkout['invoice_id']
        ])->select(['id', 'code', 'amount', 'status'])->first();

        $payment_code = !empty($payment_info['code']) ? $payment_info['code'] : null;
        if(empty($payment_code)){
            return $utilities->getResponse([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_giao_dich')
            ]);
        }

        $result = [
            CODE => SUCCESS,
            MESSAGE => __d('template', 'giao_dich_thanh_cong'),
            DATA => [
                'code' => $payment_code
            ]
        ];

        return $utilities->getResponse($result);
    }

    public function webhooks($params = []) 
    {
       $utilities = new PaymentUtilities();

        $api_key = !empty($this->config['api_key']) ? $this->config['api_key'] : null;
        if(empty($api_key)) return $utilities->getResponse([MESSAGE => __d('template', 'thong_tin_cau_hinh_chua_hop_le')]);

        if(empty($params) || !is_array($params)){
            return $utilities->getResponse([
                MESSAGE => __d('template', 'du_lieu_khong_hop_le')
            ]);
        }
        
        $now_payment_id = !empty($params[DATA]['NP_id']) ? $params[DATA]['NP_id'] : null;
        if(empty($now_payment_id)){
            return $utilities->getResponse([
                MESSAGE => __d('template', 'du_lieu_khong_hop_le')
            ]);
        }
        
        // lấy thông tin giao dịch
        $http = new Client();
        $response = $http->get($this->payment_url . '/v1/payment/' . $now_payment_id, [],
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'x-api-key' => $api_key,
                ]
            ]
        );
        $json_result = $response->getStringBody();
        if(empty($json_result) || !$utilities->isJson($json_result)){
            return $utilities->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_giao_dich')]);
        }

        $checkout = json_decode($json_result, true);
        if(empty($checkout['invoice_id'])){
            $message = !empty($checkout[MESSAGE]) ? $checkout[MESSAGE] : __d('template', 'khong_lay_duoc_thong_tin_giao_dich');
            return $utilities->getResponse([MESSAGE => $message]);
        }

        $payment_status = !empty($checkout['payment_status']) ? $checkout['payment_status'] : 'failed';        
        if(!in_array($payment_status, ['finished', 'sending', 'confirmed'])){
            $message = !empty($checkout[MESSAGE]) ? $checkout[MESSAGE] : __d('template', 'giao_dich_khong_thanh_cong');
            return $utilities->getResponse([MESSAGE => $message]);
        }

        // lấy thông tin giao dịch
        $payment_info = TableRegistry::get('Payments')->find()->where([
            'payment_gateway_code' => NOWPAYMENT,
            'reference' => $checkout['invoice_id']
        ])->select(['id', 'code', 'amount', 'status'])->first();
        
        $payment_code = !empty($payment_info['code']) ? $payment_info['code'] : null;
        if(empty($payment_code)){
            return $utilities->getResponse([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_giao_dich')
            ]);
        }

        $result = [
            CODE => ERROR,
            MESSAGE => __d('template', 'giao_dich_khong_thanh_cong'),
            DATA => [
                'code' => $payment_code,
                'payment_transaction_no' => $now_payment_id,
                'payment_gateway_response' => json_encode($checkout)
            ]
        ];

        $status_payment = isset($payment_info['status']) ? $payment_info['status'] : '';
        if($status_payment == 1) {
            $result[MESSAGE] = __d('template', 'giao_dich_nay_da_thanh_cong');
            return $utilities->getResponse($result);
        }

        $result[CODE] = SUCCESS;
        $result[MESSAGE] = __d('template', 'giao_dich_thanh_cong');

        return $utilities->getResponse($result);
    }

}

?>