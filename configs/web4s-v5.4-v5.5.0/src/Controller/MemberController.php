<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Configure;
use Google;
use Cake\Http\Client;
use App\Lib\SignIn\SignInWithApple;

class MemberController extends AppController 
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $action_check = [
            'dashboard', 
            'address',
            'saveAddress',
            'profile', 
            'saveProfile', 
            'isDefault', 
            'deleteAddress', 
            'changePassword',
            'ajaxChangePassword',
            'order',
            'orderDetail',
            'cancelOrder',
            'uploadAvatar',
            'deleteAvatar',
            'attendance',
            'attendanceTick',
            'promotion',
            'changePhone',
            'changeEmail',
            'ajaxChangePhone',
            'ajaxChangeEmail',
            'listBank',
            'deleteBank',
            'saveBank'
        ];

        $session = $this->request->getSession();  
        $member = $session->read(MEMBER);

        if(in_array($this->request->getParam('action'), $action_check) && !empty($member['customer_id'])) {
            if($this->loadComponent('Member')->memberDoesntExistLogout($member['customer_id'])){
                if($this->request->is('ajax')){
                    $this->responseJson([
                        STATUS => 403,
                        MESSAGE => __d('template', 'het_phien_lam_viec_vui_long_dang_nhap_lai_tai_khoan')
                    ]);
                }else{
                    return $this->redirect('/member/login?redirect=' . urlencode($this->request->getPath()), 303);
                }
            }
        }

        if (in_array($this->request->getParam('action'), $action_check) && empty($member['customer_id'])){
            if($this->request->is('ajax')){
                $this->responseJson([
                    STATUS => 403,
                    MESSAGE => __d('template', 'het_phien_lam_viec_vui_long_dang_nhap_lai_tai_khoan')
                ]);
            }else{
                return $this->redirect('/member/login?redirect=' . urlencode($this->request->getPath()), 303);
            }
        }

    }

	public function login() 
	{
        $request = $this->request;
        $member_info = $request->getSession()->read(MEMBER);
        $redirect = $request->getQuery('redirect');
        if(!empty($member_info)){
            $this->redirect(!empty($redirect) ? $redirect : 'member/dashboard');
        }

        $this->set('redirect', $redirect);
        $this->set('title_for_layout', __d('template', 'dang_nhap'));
        $this->render('login');
    }

    public function ajaxLogin() 
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('template', 'phuong_thuc_khong_hop_le')]);
        }
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        
        $result = $this->loadComponent('Member')->login($data);
        $this->responseJson($result);
    }

    public function socialLogin()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $result = $this->loadComponent('Member')->socialLogin($data);
        $this->responseJson($result);
    }

    public function register() 
    {
        $this->set('title_for_layout', __d('template', 'dang_ky_tai_khoan'));
        $this->render('register');
    }

    public function ajaxRegister()
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('template', 'phuong_thuc_khong_hop_le')]);
        }

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $result = $this->loadComponent('Member')->register($data);
        $this->responseJson($result);
    }

    public function dashboard() 
    {
        $member = $this->request->getSession()->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        $customers_table = TableRegistry::get('Customers');

        $member_info = $customers_table->getDetailCustomer($customer_id, [
            'get_default_address' => true
        ]);
        $member_info = $customers_table->formatDataCustomerDetail($member_info);

        $orders = TableRegistry::get('Orders')->queryListOrders([
            FILTER => [
                TYPE => ORDER,
                'customer_id' => $customer_id
            ]
        ])->limit(5)->toArray();

        $this->set('order', $orders);
        $this->set('member', $member_info);
        $this->set('title_for_layout', __d('template', 'quan_ly_tai_khoan'));
    }

    public function address() 
    {
        $member = $this->request->getSession()->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;

        $table = TableRegistry::get('Customers');

        $member_info = $table->getDetailCustomer($customer_id, [
            'get_list_address' => true
        ]);
        $member_info = $table->formatDataCustomerDetail($member_info);

        $this->set('member', $member_info);
        $this->set('title_for_layout', __d('template', 'so_dia_chi'));
    }

    public function saveAddress()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $result = $this->loadComponent('Member')->saveAddress($data);
        $this->responseJson($result);
    }

    public function profile() 
    {
        $member = $this->request->getSession()->read(MEMBER);

        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        $customers_table = TableRegistry::get('Customers');

        $member_info = $customers_table->getDetailCustomer($customer_id, [
            'get_list_address' => true
        ]);
        $member_info = $customers_table->formatDataCustomerDetail($member_info);

        $this->set('member', $member_info);
        $this->set('title_for_layout', __d('template', 'thong_tin_ca_nhan'));
    }

    public function saveProfile()
    {
        $this->layout = false;
        $this->autoRender = false;

        $member = $this->request->getSession()->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $result = $this->loadComponent('Member')->updateProfile($data);
        $this->responseJson($result);
    }

    public function isDefault()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();        
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $result = $this->loadComponent('Member')->setDefaultAddress($data);
        $this->responseJson($result);
    }

    public function deleteAddress()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();        
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $result = $this->loadComponent('Member')->deleteAddress($data);
        $this->responseJson($result);
    }

    public function changePassword() 
    {
        $member = $this->request->getSession()->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        $member_info = TableRegistry::get('Customers')->getDetailCustomer($customer_id, [
            'get_default_address' => true
        ]);
        $member_info = TableRegistry::get('Customers')->formatDataCustomerDetail($member_info);

        $this->set('member', $member_info);
        $this->set('title_for_layout', __d('template', 'thay_doi_mat_khau'));
    }

    public function ajaxChangePassword() 
    {
        $this->layout = false;
        $this->autoRender = false;
        
        $data = $this->getRequest()->getData();

        $result = $this->loadComponent('Member')->changePassword($data);
        $this->responseJson($result);
    }

    public function order() 
    {
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $result = $this->loadComponent('Member')->listOrders($data);
        $data_result = !empty($result[DATA]) ? $result[DATA] : [];

        $order = !empty($data_result['orders']) ? $data_result['orders'] : [];
        $pagination = !empty($data_result[PAGINATION]) ? $data_result[PAGINATION] : [];

        $member = $this->request->getSession()->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        $member_info = TableRegistry::get('Customers')->getDetailCustomer($customer_id, [
            'get_default_address' => true
        ]);
        $member_info = TableRegistry::get('Customers')->formatDataCustomerDetail($member_info);

        $this->set('member', $member_info);
        $this->set('order', $order);
        $this->set('pagination', $pagination);
        $this->set('title_for_layout', __d('template', 'don_hang_cua_ban'));

        if($this->request->is('ajax')){
            $this->viewBuilder()->enableAutoLayout(false);
            $this->render('list_order_element');
        }else{
            $this->render('list_order');
        }
    }

    public function orderDetail($code = null)
    {
        $member = $this->request->getSession()->read(MEMBER);        
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;

        $customers_table = TableRegistry::get('Customers');
        $orders_table = TableRegistry::get('Orders');
        $orders_log_table = TableRegistry::get('OrdersLog');

        $member = $customers_table->getDetailCustomer($customer_id);
        $member_info = $customers_table->formatDataCustomerDetail($member);

        $check_order = $orders_table->find()->contain(['OrdersContact'])->where([
            'OrdersContact.customer_id' => $customer_id,
            'Orders.code' => $code,
            'Orders.deleted' => 0
        ])->select(['Orders.id'])->first();

        $order_id = !empty($check_order['id']) ? intval($check_order['id']) : null;
        if(empty($order_id)){
            return $this->showErrorPage([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_don_hang'),
                'title' => __d('template', 'thong_tin_don_hang')
            ]);
        }

        $order = $orders_table->getDetailOrder($order_id, [
            'get_items' => true,
            'get_contact' => true
        ]);
        $order_info = $orders_table->formatDataOrderDetail($order, LANGUAGE);

        if(empty($order_info)){
            return $this->showErrorPage([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_don_hang'),
                'title' => __d('template', 'thong_tin_don_hang')
            ]);
        }

        // order log info
        $orders_log = $orders_log_table->queryListOrdersLog([
            'get_contact' => true,
            'get_user' => true,
            FILTER => [
                'order_id' => $order_id
            ]
        ])->toArray();

        if (!empty($orders_log)) {
            foreach ($orders_log as $key => $log) {
                $orders_log[$key] = $orders_log_table->formatDataOrderLogDetail($log);
            }
        }

        $this->set('member', $member_info);
        $this->set('order', $order_info);
        $this->set('title_for_layout', __d('template', 'thong_tin_don_hang'));
    }

    public function cancelOrder() 
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $result = $this->loadComponent('Member')->cancelOrder($data);
        $this->responseJson($result);
    }

    public function forgotPassword()
    {
        $this->set('title_for_layout', __d('template', 'quen_mat_khau'));
        $this->render('forgot_password');
    }

    public function ajaxForgotPassword()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $result = $this->loadComponent('Member')->forgotPassword($data);
        $this->responseJson($result);
    }

    public function verifyForgotPassword()
    {
        $this->set('title_for_layout', __d('template', 'quen_mat_khau'));
        $this->render('verify_forgot_password');
    }

    public function ajaxVerifyForgotPassword()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'phuong_thuc_khong_hop_le')]);
        }

        $result = $this->loadComponent('Member')->verifyForgotPassword($data);
        $this->responseJson($result);    
    }

    public function logout() 
    {
        $this->layout = false;
        $this->autoRender = false;

        $this->loadComponent('Member')->logout();
        return $this->redirect('/');
    }

    public function reloadMiniMember()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        $this->render('mini');
    }

    public function uploadAvatar()
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('template', 'phuong_thuc_khong_hop_le')]);
        }
        
        $file = !empty($_FILES['file']) ? $_FILES['file'] : [];
        if (empty($file)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }
 
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $data_upload = [
            'path' => !empty($data['path']) ? $data['path'] : CUSTOMER,
            'file' => $file
        ];
        $result = $this->loadComponent('Member')->updateAvatar($data_upload);
        $this->responseJson($result);        
    }

    public function deleteAvatar()
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('template', 'phuong_thuc_khong_hop_le')]);
        }

        $member = $this->request->getSession()->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;

        if(empty($customer_id)){
            $this->responseJson([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);
        }

        $customers_table = TableRegistry::get('Customers');

        $customers_info = $customers_table->find()->where(['id' => $customer_id])->first();
        if(empty($customers_info)){
            $this->responseJson([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);
        }

        $customer = $customers_table->patchEntity($customers_info, [
            'id' => $customer_id,
            'avatar' => null
        ], ['validate' => false]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = $customers_table->save($customer);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }

        $this->responseJson([
            CODE => SUCCESS, 
            MESSAGE => __d('template', 'xoa_anh_dai_dien_thanh_cong')
        ]);
    }

    public function verifyEmail() 
    {
        $this->set('title_for_layout', __d('template', 'kich_hoat_tai_khoan'));
        $this->render('verify_email');
    }

    public function ajaxVerifyEmail()
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('template', 'phuong_thuc_khong_hop_le')]);
        }

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $email = !empty($data['email']) ? trim($data['email']) : null;
        $code = !empty($data['code']) ? trim($data['code']) : null;

        if(empty($email)) {
            $this->responseJson([MESSAGE => __d('template', 'vui_long_nhap_dia_chi_email')]);
        }

        // check account
        $table_account = TableRegistry::get('CustomersAccount');
        $account_info = $table_account->find()->contain(['Customer'])->where([
            'Customer.email' => $email,
            'Customer.deleted' => 0,
            'CustomersAccount.status' => 2
        ])->first();

        if(empty($account_info)){
            $this->responseJson([MESSAGE => __d('template', 'khong_the_kich_hoat_tai_khoan_nay')]);
        }

        // check recaptcha
        $token = !empty($data[TOKEN_RECAPTCHA]) ? $data[TOKEN_RECAPTCHA] : null;
        $check_recaptcha = $this->loadComponent('ReCaptcha')->check($token);
        if($check_recaptcha[CODE] != SUCCESS){
            $this->responseJson([MESSAGE => $check_recaptcha[MESSAGE]]);
        }

        //verifyEmail
        if(empty($code)) {
            $this->responseJson([MESSAGE => __d('template', 'vui_long_nhap_ma_xac_nhan')]);
        }

        $table_email_token = TableRegistry::get('EmailToken');

        $email_token_info = $table_email_token->find()->where([
            'email' => $email,
            'code' => $code,
            'type' => 'active_account',
            'status' => 0,
            'end_time >=' => time()
        ])->first();

        if(empty($email_token_info)) {
            $this->responseJson([MESSAGE => __d('template', 'thong_tin_kich_hoat_khong_ton_tai')]);
        }

        $email_token = $table_email_token->patchEntity($email_token_info, ['status' => 1]);

        $account = $table_account->patchEntity($account_info, ['status' => 1]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
                        
            $save = $table_email_token->save($email_token);
            if (empty($save->id)){
                throw new Exception();
            }

            $save_account = $table_account->save($account);
            if (empty($save_account->id)){
                throw new Exception();
            }

            $conn->commit();

            $this->responseJson([
                CODE => SUCCESS, 
                MESSAGE => __d('template', 'ban_da_kich_hoat_thanh_cong_tai_khoan')
            ]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function success()
    {
        $this->set('title_for_layout', __d('template', 'dang_ky_thanh_cong'));
        $this->render('success');
    }
    
    public function resendVerifyCode()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }
        
        $result = $this->loadComponent('Member')->resendVerifyCode($data);
        $this->responseJson($result);
    }

    public function attendance()
    {
        $member = $this->request->getSession()->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        $member_info = TableRegistry::get('Customers')->getDetailCustomer($customer_id, [
            'get_default_address' => true
        ]);
        $member_info = TableRegistry::get('Customers')->formatDataCustomerDetail($member_info);

        $attendance = $this->loadComponent('CustomersPointFrontend')->processAttendance($customer_id);
        $customer_point = TableRegistry::get('CustomersPoint')->getInfoCustomerPoint($customer_id);
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $config_point = !empty($settings['point']) ? $settings['point'] : [];
   
        $this->set('member', $member_info);
        $this->set('customer_point', $customer_point);
        $this->set('config_point', $config_point);
        $this->set('attendance', $attendance);
        
        $this->set('title_for_layout', __d('template', 'diem_danh_tich_diem'));
    }

    public function attendanceTick()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->request->getData();
        if (!$this->getRequest()->is('post') && empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'phuong_thuc_khong_hop_le')]);
        }

        $result = $this->loadComponent('CustomersPointFrontend')->attendanceTick($data);
        $this->responseJson($result);
    }

    public function promotion()
    {
        $member = $this->request->getSession()->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        $member_info = TableRegistry::get('Customers')->getDetailCustomer($customer_id, [
            'get_default_address' => true
        ]);
        $member_info = TableRegistry::get('Customers')->formatDataCustomerDetail($member_info);

        $this->set('member', $member_info);
        $this->set('title_for_layout', __d('template', 'phieu_giam_gia'));
        $this->render('promotion');
    }

    public function changePhone() 
    {
        $member = $this->request->getSession()->read(MEMBER);

        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        $customers_table = TableRegistry::get('Customers');

        $member_info = $customers_table->getDetailCustomer($customer_id, [
            'get_list_address' => true
        ]);
        $member_info = $customers_table->formatDataCustomerDetail($member_info);

        $this->set('member', $member_info);
        $this->set('title_for_layout', __d('template', 'thay_doi_so_dien_thoai'));
    }

    public function ajaxChangePhone()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }
        
        $result = $this->loadComponent('Member')->changeImportantInfo($data);
        $this->responseJson($result);
    }

    public function changeEmail() 
    {
        $member = $this->request->getSession()->read(MEMBER);

        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        $customers_table = TableRegistry::get('Customers');

        $member_info = $customers_table->getDetailCustomer($customer_id, [
            'get_list_address' => true
        ]);
        $member_info = $customers_table->formatDataCustomerDetail($member_info);

        $this->set('member', $member_info);
        $this->set('title_for_layout', __d('template', 'thay_doi_email'));
    }

    public function ajaxChangeEmail()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }
        
        $result = $this->loadComponent('Member')->changeImportantInfo($data);
        $this->responseJson($result);
    }

    public function getVerifyCode() 
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }
        
        $result = $this->loadComponent('Member')->getVerifyCode($data);
        $this->responseJson($result);
    }

    public function listBank() 
    {
        $affiliate = $this->loadComponent('Member')->listBankOfPartner();
        $affiliate = !empty($affiliate[DATA]) ? $affiliate[DATA] : [];

        $member = $this->request->getSession()->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        $member_info = TableRegistry::get('Customers')->getDetailCustomer($customer_id, [
            'get_default_address' => true
        ]);
        $member_info = TableRegistry::get('Customers')->formatDataCustomerDetail($member_info);

        $this->set('member', $member_info);
        $this->set('affiliate', $affiliate);
        $this->set('title_for_layout', __d('template', 'tai_khoan_ngan_hang'));
    }

    public function deleteBank()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }
        
        $result = $this->loadComponent('Member')->deleteBank($data);
        $this->responseJson($result);
    }

    public function saveBank()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }
        
        $result = $this->loadComponent('Member')->saveBank($data);
        $this->responseJson($result);
    }

    public function oauthGoogle()
    {
        $this->layout = false;
        $this->autoRender = false;

        $request = $this->getRequest();
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $social = !empty($settings['social']) ? $settings['social'] : [];
        $client_id = !empty($social['google_client_id']) ? $social['google_client_id'] : [];
        $client_secret = !empty($social['google_secret']) ? $social['google_secret'] : [];

        $state = $request->getQuery('state');  
        $code = $request->getQuery('code');  

        if(empty($client_id) || empty($client_secret) || empty($code) || empty($state)) {
            return $this->showErrorPage([
                MESSAGE => __d('template', 'du_lieu_khong_hop_le'),
                'title' => __d('template', 'dang_nhap_mang_xa_hoi')
            ]);
        }
        
        $expl_state = explode('_', $state);
        $csrf_token = !empty($expl_state[0]) ? urldecode($expl_state[0]) : null;
        $redirect = !empty($expl_state[1]) ? urldecode($expl_state[1]) : null;

        // if($csrf_token != $request->getAttribute('csrfToken')) {
        //     return $this->showErrorPage([
        //         MESSAGE => __d('template', 'du_lieu_khong_hop_le'),
        //         'title' => __d('template', 'dang_nhap_mang_xa_hoi')
        //     ]);
        // }

        $http = new Client();
        $response = $http->post('https://oauth2.googleapis.com/token', [
            'code' => $code,
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'redirect_uri' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/member/oauth/google',
            'grant_type' => 'authorization_code'
        ]);

        if(!$response->getStatusCode() == 200) {
            return $this->showErrorPage([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tra_ve'),
                'title' => __d('template', 'dang_nhap_mang_xa_hoi')
            ]);
        }

        $result_token = json_decode($response->getStringBody(), true);
        $token = !empty($result_token['id_token']) ? $result_token['id_token'] : null;

        if(empty($token)) {
            return $this->showErrorPage([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_token'),
                'title' => __d('template', 'dang_nhap_mang_xa_hoi')
            ]);
        }

        $client = new Google\Client(['client_id' => $client_id]);
        $decode_token = $client->verifyIdToken($token);

        $data = [
            'social_id' => !empty($decode_token['sub']) ? $decode_token['sub'] : null,
            'type' => 'google',
            'full_name' => !empty($decode_token['name']) ? $decode_token['name'] : null,
            'email' => !empty($decode_token['email']) ? $decode_token['email'] : null,
            'picture' => !empty($decode_token['picture']) ? $decode_token['picture'] : null,
            'redirect' => !empty($redirect) ? $redirect : null,
        ];

        $result = $this->loadComponent('Member')->socialLogin($data);
        if(!empty($result['code']) && $result['code'] == ERROR) {
            return $this->showErrorPage([
                MESSAGE => __d('template', 'loi_dang_nhap_mang_xa_hoi'),
                'title' => __d('template', 'dang_nhap_mang_xa_hoi')
            ]);
        }

        if(empty($redirect)) $redirect = '/';

        return $this->redirect($redirect);
    }

    public function oauthFacebook()
    {
        $this->layout = false;
        $this->autoRender = false;
        $request = $this->getRequest();

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $social = !empty($settings['social']) ? $settings['social'] : [];
        $client_id = !empty($social['facebook_app_id']) ? $social['facebook_app_id'] : [];
        $client_secret = !empty($social['facebook_secret']) ? $social['facebook_secret'] : [];

        $state = $request->getQuery('state');  
        $code = $request->getQuery('code');  

        if(empty($client_id) || empty($client_secret) || empty($code) || empty($state)) {
            return $this->showErrorPage([
                MESSAGE => __d('template', 'du_lieu_khong_hop_le'),
                'title' => __d('template', 'dang_nhap_mang_xa_hoi')
            ]);
        }
        
        $expl_state = explode('_', $state);
        $csrf_token = !empty($expl_state[0]) ? urldecode($expl_state[0]) : null;
        $redirect = !empty($expl_state[1]) ? urldecode($expl_state[1]) : null;

        /*if($csrf_token != $request->getAttribute('csrfToken')) {
            return $this->showErrorPage([
                MESSAGE => __d('template', 'du_lieu_khong_hop_le'),
                'title' => __d('template', 'dang_nhap_mang_xa_hoi')
            ]);
        }*/

        $http = new Client();

        $response = $http->get('https://graph.facebook.com/v14.0/oauth/access_token', [
            'code' => $code,
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'redirect_uri' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/member/oauth/facebook'
        ]);

        if(!$response->getStatusCode() == 200) {
            return $this->showErrorPage([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tra_ve'),
                'title' => __d('template', 'dang_nhap_mang_xa_hoi')
            ]);
        }

        $result_token = json_decode($response->getStringBody(), true);
        $token = !empty($result_token['access_token']) ? $result_token['access_token'] : null;

        if(empty($token)) {
            return $this->showErrorPage([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_token'),
                'title' => __d('template', 'dang_nhap_mang_xa_hoi')
            ]);
        }

        $response_me = $http->get('https://graph.facebook.com/me', [
            'access_token' => $token,
            'fields' => 'id, name, picture, email'
        ]);

        if(!$response_me->getStatusCode() == 200) {
            return $this->showErrorPage([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tra_ve'),
                'title' => __d('template', 'dang_nhap_mang_xa_hoi')
            ]);
        }
        
        $decodeToken = json_decode($response_me->getStringBody(), true);
        
        $data = [
            'social_id' => !empty($decodeToken['id']) ? $decodeToken['id'] : null,
            'type' => 'facebook',
            'full_name' => !empty($decodeToken['name']) ? $decodeToken['name'] : null,
            'email' => !empty($decodeToken['email']) ? $decodeToken['email'] : null,
            'picture' => !empty($decodeToken['picture']['data']['url']) ? $decodeToken['picture']['data']['url'] : null,
            'redirect' => !empty($redirect) ? $redirect : null,
        ];

        $result = $this->loadComponent('Member')->socialLogin($data);

        if(!empty($result['code']) && $result['code'] == ERROR) {
            return $this->showErrorPage([
                MESSAGE => __d('template', 'loi_dang_nhap_mang_xa_hoi'),
                'title' => __d('template', 'dang_nhap_mang_xa_hoi')
            ]);
        }

        if(empty($redirect)) $redirect = '/';

        return $this->redirect($redirect);
    } 

    public function getUrlAppleLogin()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->request->getData();
        $type = !empty($data['type']) ? $data['type'] : '';

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $social = !empty($settings['social']) ? $settings['social'] : [];

        $apple_client_id = !empty($social['apple_client_id']) ? $social['apple_client_id'] : '';
        if(empty($apple_client_id)){
            $this->responseJson([
                MESSAGE => __d('template', 'thong_tin_cau_hinh_chua_hop_le')
            ]);
        }

        $query = [
            'client_id' => $apple_client_id,
            'response_type' => 'id_token code',
            'scope' => 'email name',
            'redirect_uri' => $this->request->scheme() . '://' . $this->request->host() . '/member/oauth/apple',
            'state' => $type,
            'nonce' => '',
            'response_mode' => 'form_post'
        ];

        $url = 'https://appleid.apple.com/auth/authorize?' . http_build_query($query);

        $this->responseJson([
            CODE => SUCCESS,
            DATA => [
                'url' => $url
            ]
        ]);
    }

    public function oauthApple()
    {
        $this->layout = false;
        $this->autoRender = false;

        $request = $this->getRequest();
        $params = $request->getData();

        $state = !empty($params['state']) ? $params['state'] : '';
        $code = !empty($params['code']) ? $params['code'] : '';
        $id_token = !empty($params['id_token']) ? $params['id_token'] : '';


        // get user info by token
        @list($header_b64, $payload_b64, $sign) = explode('.', $id_token);
        $user_info = !empty($payload_b64) ? json_decode(base64_decode($payload_b64), true) : [];
        $apple_id = !empty($user_info['sub']) ? $user_info['sub'] : '';
        
        // get user info by code
        // $user_info = $this->_getUserInfoAppleByCode($code);

        $social_params = [
            'social_id' => $apple_id,
            'type' => 'apple',
            'email' => !empty($user_info['email']) ? $user_info['email'] : ''
        ];

        $result = $this->loadComponent('Member')->socialLogin($social_params);
        if(empty($result['code']) || $result['code'] == ERROR) {
            return $this->showErrorPage([
                MESSAGE => __d('template', 'loi_dang_nhap_mang_xa_hoi'),
                'title' => __d('template', 'dang_nhap_mang_xa_hoi')
            ]);
        }

        $redirect = '/'; 

        return $this->redirect($redirect);
    }

    private function _getUserInfoAppleByCode($code = '')
    {
        if(empty($code)) return [];

        $header = [
            'alg' => 'ES256',
            'kid' => 'AM7MW29WLV', // key id
            'typ' => 'JWT'
        ];
        
        $payload = [
            'iss' => '48YZ25386U', // team id
            'iat' => time(),
            'exp' => time() + (86400 * 150),
            'aud' => 'https://appleid.apple.com',
            'sub' => 'com.vpnsec.web' // client id
        ];

        $private_key = '-----BEGIN PRIVATE KEY-----
MIGTAgEAMBMGByqGSM49AgEGCCqGSM49AwEHBHkwdwIBAQQgKCqiIIMmFbitPw6U
cnUb5hTdWfv9DlYbFTLMX/xc0L+gCgYIKoZIzj0DAQehRANCAAQSGjGI+JPUrYsu
hiFJ6DFtuEoNn95/uqAHjzSx7e6Iqjr3mRCT7SU8BL79PDeaw+jV3mzZr8zibysA
NEJSSvr+
-----END PRIVATE KEY-----';

        $jwt = new JWT();
        try{
            $token = $jwt->encode($payload, $private_key, 'ES256', null, $header);
        }catch (Exception $e) {
            $utilities->getResponse([
                STATUS => 401,
                MESSAGE => $e->getMessage()
            ]);
        }

        // get token
        $data_post = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->request->scheme() . '://' . $this->request->host() . '/member/oauth/apple',
            'client_id' => 'com.vpnsec.web',
            'client_secret' => $token
        ];

        $http = new Client();
        $response = $http->post('https://appleid.apple.com/auth/token', $data_post, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'type' => 'json'
            ]
        ]);
        $response = $response->getJson();
        $id_token = !empty($response['id_token']) ? $response['id_token'] : '';


        @list($header_b64, $payload_b64, $sign) = explode('.', $id_token);
        $user_info = !empty($payload_b64) ? json_decode(base64_decode($payload_b64), true) : [];
        $apple_id = !empty($user_info['sub']) ? $user_info['sub'] : '';

        return $user_info;
    }

}