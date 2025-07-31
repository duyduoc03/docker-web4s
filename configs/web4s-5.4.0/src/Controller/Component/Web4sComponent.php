<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Http\Client;
use Cake\Core\Configure;
use Firebase\JWT\JWT;
use Cake\Utility\Hash;
use Cake\I18n\Time;
use UnexpectedValueException;

class Web4sComponent extends Component
{
	/**
     * The request.
     *
     * @var object
     */
    protected $request;

	public $controller = null;
	public $components = ['System', 'Utilities'];
    
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
        $this->request = $this->controller->getRequest();
    }

    protected function getResponse($params = [])
    {
        return $this->System->getResponse($params);
    }

	public function user()
    {

        $data = $this->request->getData();

        $result = array(CODE => ERROR, MESSAGE => '');

        $token = !empty($data['token']) ? $data['token'] : null;
        $redirect = !empty($data['redirect']) ? $data['redirect'] : null;
        $username = !empty($data['username']) ? trim(strip_tags($data['username'])) : null;
        $password = !empty($data['password']) ? $data['password'] : null;

        // kiểm tra dùng tài khoản mặc định của hệ thống 
        $account_deny = Configure::read('ACCOUNT_DENY');
        if(array_key_exists($username, $account_deny) && $account_deny[$username] == $password) {
        	return $this->getResponse([
                STATUS => 301,
                MESSAGE => __d('admin', 'thong_bao_doi_mat_khau_mac_dinh')
            ]);
        }

        $table = TableRegistry::get('Users');

        $parse_url = !empty(parse_url($this->request->referer(), PHP_URL_QUERY)) ? parse_url($this->request->referer(), PHP_URL_QUERY) : '';       
        parse_str($parse_url, $query_params);
        $nh_login = !empty($query_params['nh-login']) ? $query_params['nh-login'] : null;

        // đăng nhập supper admin
        if($nh_login == 1){
            try{
                $url = CRM_URL . '/api/webroot-account';
                $http = new Client();
                
                $response = $http->post($url, [
                    'username' => $username,
                    'password' => $password
                ]);

                $json = $response->getJson();
            }catch (NetworkException $e) {
            	return $this->getResponse([
	                MESSAGE => __d('admin', 'tai_khoan_hoac_mat_khau_khong_dung')
	            ]);
            }

            if(!isset($json[CODE]) || $json[CODE] != SUCCESS) {
            	return $this->getResponse([
	                MESSAGE => __d('admin', 'tai_khoan_hoac_mat_khau_khong_dung')
	            ]);
            }

            $id = !empty($json[DATA]['id']) ? $json[DATA]['id'] : null;
            $name_account = !empty($json[DATA]['full_name']) ? $json[DATA]['full_name'] : null;
            if(empty($name_account)) {
                return $this->getResponse([
                    MESSAGE => __d('admin', 'khong_lay_duoc_ten_tai_khoan')
                ]);
            }

            $user_info = [
                'id' => 10000,
                'supper_admin' => 1,
                'username' => 'root',
                'full_name' => 'Super Admin - ' . $id . ' ' . $name_account
            ];

            return $this->getResponse([
                CODE => SUCCESS,
                MESSAGE => __d('admin', 'dang_nhap_thanh_cong'),
                DATA => $user_info
            ]);
        }

        // lấy thông tin theo username
        $user_info = $table->find()->where([
            'username' => $username,
            'deleted' => 0
        ])->select([
            'id',  
            'username', 
            'role_id', 
            'full_name', 
            'image_avatar', 
            'language_admin',
            'status', 
            'login_error',
            'config'
        ])->first();            

        $login_error = !empty($user_info['login_error']) ? intval($user_info['login_error']) : 0;
        $status = !empty($user_info['status']) ? intval($user_info['status']) : 1;

        // kiểm tra username và status tài khoản
        if(empty($user_info)) {
        	return $this->getResponse([
                MESSAGE => __d('admin', 'tai_khoan_khong_ton_tai')
            ]);
        }

        if($status == 0) {
            return $this->getResponse([
                MESSAGE => __d('admin', 'tai_khoan_da_ngung_hoat_dong')
            ]);
        }
        if($status == 2 || $status != 1) {
            return $this->getResponse([
                MESSAGE => __d('admin', 'tai_khoan_cua_ban_hien_dang_bi_khoa')
            ]);
        }

        // kiểm tra mật khẩu
        $identify = $this->controller->Auth->identify();
        $password_correct = !empty($identify['username']) ? true : false;

        // sai mật khẩu
        if(!$password_correct){
            // cập nhật số lần đăng nhập sai
            $login_error ++;
            $data_update = [
                'login_error' => $login_error
            ];

            // khóa tài khoản khi số lần đăng nhập sai vượt quá mức cho phép
            if($login_error >= MAX_LOGIN_ERROR) $data_update['status'] = 2;
            $entity = $table->patchEntity($user_info, $data_update);
            $update = $table->save($entity);
            if(empty($update->id)) {
            	return $this->getResponse([
	                MESSAGE => __d('admin', 'tai_khoan_hoac_mat_khau_khong_dung')
	            ]);
            }

            // thông báo số lần đăng nhập sai còn lại
            $remaining_login = MAX_LOGIN_ERROR - $login_error;
            if($remaining_login < 0) $remaining_login = 0;

            return $this->getResponse([
                MESSAGE => __d('admin', 'mat_khau_khong_chinh_xac_he_thong_se_khoa_tai_khoan_sau_{0}_lan_nua', [$remaining_login])
            ]);
        }
       
        // cập nhật lại login_error về 0 sau khi đăng nhập đúng
        if(!empty($user_info['login_error'])) {
            $entity = $table->patchEntity($user_info, ['login_error' => 0]);
            $table->save($entity);
        }

        $user_info['config'] = !empty($user_info['config']) ? json_decode($user_info['config'], true) : [];

        return $this->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'dang_nhap_thanh_cong'),
            DATA => $user_info
        ]);
    }

    public function validateAuthCrm()
    {

        $header_auth = !empty($this->request->getHeader('Authorization')[0]) ? $this->request->getHeader('Authorization')[0] : null;
        if(empty($header_auth)){
            return $this->getResponse([
                STATUS => 401,
                MESSAGE => 'Auth Bearer chưa hợp lệ'
            ]);
        };

        list($auth_type, $bearer_token) = explode(' ', $header_auth, 2);
        if($auth_type != 'Bearer' || empty($bearer_token)){
            return $this->getResponse([
                STATUS => 401,
                MESSAGE => 'Auth Bearer chưa hợp lệ'
            ]);
        }

        $token = $bearer_token;
        $jwt = new JWT();
        
        try {
            $result = (array)$jwt->decode($bearer_token, CRM_WEB4S_SECRET_KEY, ['HS256']);

        } catch (UnexpectedValueException $e) {

            return $this->getResponse([
                STATUS => 401,
                MESSAGE => $e->getMessage()
            ]);
        }

        return $this->getResponse([
        	CODE => SUCCESS,
            DATA => $result
        ]);
    }

    public function sendTicketToCrm($data = [])
    {
        if(empty($data)) {
        	return $this->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $ip = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
        $domain = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;

        $files = !empty($data['files']) ? json_decode($data['files'], true) : [];
        if (!empty($files)) {
            foreach ($files as $key => $file) {
                $files[$key]['url'] = !empty($file['url']) ? CDN_URL . $file['url'] : null;
            }
        }

        $data_send = array(
            'parent_id' => !empty($data['crm_parent_id']) ? intval($data['crm_parent_id']) : null,
            'full_name' => !empty($data['full_name']) ? $data['full_name'] : null,
            'email' => !empty($data['email']) ? $data['email'] : null,
            'phone' => !empty($data['phone']) ? $data['phone'] : null,

            'code' => !empty($data['code']) ? $data['code'] : null,
            'title' => !empty($data['title']) ? $data['title'] : null,
            'department' => !empty($data['department']) ? $data['department'] : null,
            'priority' => !empty($data['priority']) ? $data['priority'] : null,

            'content' => !empty($data['content']) ? $data['content'] : null,
            'files' => !empty($files) ? json_encode($files) : null,

            'domain' => $domain,
            'ip' => $ip
        );

        $jwt = new JWT();
        $token = $jwt->encode($data_send, CRM_WEB4S_SECRET_KEY, 'HS256');
        $url = CRM_URL . '/api/ticket/save';

        $http = new Client();
        $result = $http->post($url, $data_send, 
            [
                'headers' => [
                    'Authorization' => "Bearer $token"
                ]
            ]
        );

        $result = $result->getJson();
        return !empty($result) ? $result : [];
    }
}