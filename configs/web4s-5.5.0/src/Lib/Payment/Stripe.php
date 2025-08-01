<?php

namespace App\Lib\Payment;

use Cake\ORM\TableRegistry;
use App\Lib\Payment\PaymentUtilities;
use Cake\Core\Exception\Exception;
use Cake\Http\Client;
use Cake\Log\Log;

class Stripe
{
    protected $payment_url = '';
    protected $config = [];

    public function __construct($params)
    {
        $this->config = TableRegistry::get('PaymentsGateway')->getConfig(STRIPE);

        $this->payment_url = 'https://api.stripe.com';
    }

    public function sendToGateway($params = []) 
    {
        $utilities = new PaymentUtilities();

        $secret_key = !empty($this->config['secret_key']) ? $this->config['secret_key'] : null;
        if(empty($secret_key)) {
            return $utilities->getResponse([MESSAGE => __d('template', 'thong_tin_cau_hinh_chua_hop_le')]);
        }

        $payment_code = !empty($params['payment_code']) ? $params['payment_code'] : '';

        $amount = !empty($params['amount']) ? $params['amount'] : 0;
        $ipn_url = !empty($params['ipn_url']) ? $params['ipn_url'] : '';
        $redirect_url = !empty($params['redirect_url']) ? $params['redirect_url'] . '?code=' . $payment_code : '';
        $cancel_url = !empty($params['cancel_url']) ? $params['cancel_url'] : '';

        $bill_id = !empty($params['bill_id']) ? $params['bill_id'] : '';
        $bill_code = !empty($params['bill_code']) ? $params['bill_code'] : '';
        $bill_items = !empty($params['bill_items']) && is_array($params['bill_items']) ? $params['bill_items'] : [];
        $order_description = $order_description = !empty($params['order_description']) ? $params['order_description'] : '';

        $full_name = !empty($params['full_name']) ? $params['full_name'] : '';
        $email = !empty($params['email']) ? $params['email'] : '';
        $phone = !empty($params['phone']) ? $params['phone'] : '';

        $lang = !empty($params['lang']) ? $params['lang'] : 'en';
        $currency = defined('CURRENCY_CODE') ? CURRENCY_CODE : 'USD';
        if($lang != 'vi') $lang = 'en';

        if(empty($payment_code) || empty($bill_id)){
            return $utilities->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_giao_dich')]);
        }

        if(empty($bill_items)){
            return $utilities->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $line_items = [];
        foreach($bill_items as $item){
            $name = !empty($item['name']) ? $item['name'] : '';

            $price = !empty($item['price']) ? $item['price'] : 0;
            if($lang == 'en' && !empty($price)) {
                $price = $utilities->transferToUsd($price);
                $price = intval($price * 100); // convert to cent
            }

            $quantity = !empty($item['quantity']) ? intval($item['quantity']) : 0;
            $image = !empty($item['image']) ? CDN_URL . $item['image'] : '';

            if(empty($name) || empty($quantity)) {
                return $utilities->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
            }

            $item_format = [
                'price_data' => [
                    'currency' => $currency,
                    'unit_amount' => $price,
                    'product_data' => [
                        'name' => $name,    
                    ]
                ],
                'quantity' => $quantity
            ];

            if(!empty($image)) $item_format['price_data']['product_data']['images'][] = $image;

            $line_items[] = $item_format;
        }
        
        if(empty($line_items)){
            return $utilities->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }


        $data_post = [
            'mode' => 'payment',
            'currency' => $currency,
            'client_reference_id' => $payment_code,
            'cancel_url' => $cancel_url,
            'success_url' => $redirect_url,
            'line_items' => $line_items,
            'locale' => $lang
        ];

        if(!empty($email)) $data_post['customer_email'] = $email;

        // create charges
        $http = new Client();
        $response = $http->post($this->payment_url . '/v1/checkout/sessions', $data_post,
            [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Bearer ' . $secret_key,
                ]
            ]
        );
        $json_result = $response->getStringBody();        
        if(empty($json_result) || !$utilities->isJson($json_result)){
            return $utilities->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $result = json_decode($json_result, true);
        $checkout_id = !empty($result['id']) ? $result['id'] : null;
        $pay_url = !empty($result['url']) ? $result['url'] : null;

        if(empty($checkout_id)){
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
            'reference' => $checkout_id
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

        $secret_key = !empty($this->config['secret_key']) ? $this->config['secret_key'] : null;
        if(empty($secret_key)) {
            return $utilities->getResponse([MESSAGE => __d('template', 'thong_tin_cau_hinh_chua_hop_le')]);
        }

        if(empty($params) || !is_array($params)){
            return $utilities->getResponse([
                MESSAGE => __d('template', 'chu_ky_khong_hop_le')
            ]);
        }        

        $payment_code = !empty($params['code']) ? $params['code'] : null;
        if(empty($payment_code)){
            return $utilities->getResponse([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_giao_dich')
            ]);
        }

        // lấy thông tin giao dịch
        $payment_info = TableRegistry::get('Payments')->find()->where([
            'payment_gateway_code' => STRIPE,
            'code' => $payment_code
        ])->select(['id', 'code', 'amount', 'status', 'reference'])->first();

        $checkout_id = !empty($payment_info['reference']) ? $payment_info['reference'] : null;
        if(empty($checkout_id)){
            return $utilities->getResponse([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_giao_dich')
            ]);
        }

        // kiểm tra thông tin bên stripe
        $http = new Client();
        $response = $http->get($this->payment_url . '/v1/checkout/sessions/' . $checkout_id, [],
            [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Bearer ' . $secret_key,
                ]
            ]
        );
        $json_result = $response->getStringBody();
        if(empty($json_result) || !$utilities->isJson($json_result)){
            return $utilities->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_giao_dich')]);
        }

        $checkout = json_decode($json_result, true);        
        if(empty($checkout['id']) || $checkout['id'] != $checkout_id){
            $message = !empty($checkout[MESSAGE]) ? $checkout[MESSAGE] : __d('template', 'khong_lay_duoc_thong_tin_giao_dich');
            return $utilities->getResponse([MESSAGE => $message]);
        }

        $payment_status = !empty($checkout['status']) ? $checkout['status'] : 'expired';
        if($payment_status != 'complete'){
            $message = !empty($checkout[MESSAGE]) ? $checkout[MESSAGE] : __d('template', 'giao_dich_khong_thanh_cong');
            return $utilities->getResponse([MESSAGE => $message]);
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

        $secret_key = !empty($this->config['secret_key']) ? $this->config['secret_key'] : null;
        if(empty($secret_key)) {
            return $utilities->getResponse([MESSAGE => __d('template', 'thong_tin_cau_hinh_chua_hop_le')]);
        }        
        if(empty($params) || !is_array($params)){
            return $utilities->getResponse([
                MESSAGE => __d('template', 'du_lieu_khong_hop_le')
            ]);
        }

        $payment_code = !empty($params[DATA]['code']) ? $params[DATA]['code'] : null;
        if(empty($payment_code)){
            return $utilities->getResponse([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_giao_dich')
            ]);
        }

        // lấy thông tin giao dịch
        $payment_info = TableRegistry::get('Payments')->find()->where([
            'payment_gateway_code' => STRIPE,
            'code' => $payment_code
        ])->select(['id', 'code', 'amount', 'status', 'reference'])->first();
        $checkout_id = !empty($payment_info['reference']) ? $payment_info['reference'] : null;
        if(empty($checkout_id)){
            return $utilities->getResponse([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_giao_dich')
            ]);
        }


        // kiểm tra thông tin bên stripe
        $http = new Client();
        $response = $http->get($this->payment_url . '/v1/checkout/sessions/' . $checkout_id, [],
            [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Bearer ' . $secret_key,
                ]
            ]
        );
        $json_result = $response->getStringBody();
        if(empty($json_result) || !$utilities->isJson($json_result)){
            return $utilities->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_giao_dich')]);
        }

        $checkout = json_decode($json_result, true);        
        if(empty($checkout['id']) || $checkout['id'] != $checkout_id){
            $message = !empty($checkout[MESSAGE]) ? $checkout[MESSAGE] : __d('template', 'khong_lay_duoc_thong_tin_giao_dich');
            return $utilities->getResponse([MESSAGE => $message]);
        }

        $payment_status = !empty($checkout['status']) ? $checkout['status'] : 'expired';
        if($payment_status != 'complete'){
            $message = !empty($checkout[MESSAGE]) ? $checkout[MESSAGE] : __d('template', 'giao_dich_khong_thanh_cong');
            return $utilities->getResponse([MESSAGE => $message]);
        }

        $result = [
            CODE => ERROR,
            MESSAGE => __d('template', 'giao_dich_khong_thanh_cong'),
            DATA => [
                'code' => $payment_code,
                'payment_transaction_no' => $checkout_id,
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