<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Event\EventInterface;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Datasource\ConnectionManager;

class UserController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow('ajaxLogin');
        $this->Auth->allow('forgotPassword');
        $this->Auth->allow('ajaxForgotPassword');
        $this->Auth->allow('verifyForgotPassword');
        $this->Auth->allow('ajaxVerifyForgotPassword');
        $this->Auth->allow('resendVerifyCode');
    }

    public function checkLinkLoginAdmin()
    {
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $profile = !empty($settings['profile']) ? $settings['profile'] : [];

        $admin_url = !empty($profile['admin_url']) ? $profile['admin_url'] : null;

        if (empty($admin_url)) return true;

        $request_url = $_SERVER['REQUEST_URI'];
        if (!empty($request_url) && $request_url == ADMIN_PATH . '?nh-login=1') return true;

        $redirect_url = $_SERVER['REDIRECT_URL'];
        $redirect_url = !empty($redirect_url) ? str_replace(ADMIN_PATH . '/', '', $redirect_url) : null;

        if (!empty($redirect_url) && $redirect_url == $admin_url) return true;

        return false;
    }

    public function login()
    {
        $this->viewBuilder()->setLayout('account');

        $request = $this->getRequest();
        $params = $request->getQuery();
        if ($this->Auth->user()) {
            $url_redirect = $this->Auth->redirectUrl();
            if(empty($url_redirect) || $url_redirect == '/'){
                $url_redirect = ADMIN_PATH . '/main';
            }            
            return $this->redirect($url_redirect);
        }

        $check_link_admin = $this->checkLinkLoginAdmin();
        if (!$check_link_admin) {
            return $this->redirect('/404');
        }

        $this->js_page = [
            '/admin/assets/js/pages/login.js'
        ];

        // sinh token captcha ở form login
        $token = TableRegistry::get('Utilities')->generateRandomString(20);
        $request->getSession()->write('login_token', $token);

        $this->set('token', $token);
        $this->set('redirect', !empty($params['redirect']) ? $params['redirect'] : null);
    }

    public function ajaxLogin()
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }
        
        $data = $this->getRequest()->getData();

        $token = !empty($data['token']) ? $data['token'] : null;
        $redirect = !empty($data['redirect']) ? $data['redirect'] : null;

        // kiểm tra mã xác nhận
        if(empty($token)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_xac_nhan_truoc_khi_dang_nhap')]);
        }

        $my_token = $this->getRequest()->getSession()->read('login_token');
        if($token != $my_token){
            $this->responseJson([MESSAGE => __d('admin', 'ma_xac_nhan_khong_chinh_xac_hoac_da_het_han')]);
        }        

        $user = $this->loadComponent('Web4s')->user();

        if ($user[CODE] != SUCCESS) {
            $this->responseJson($user);
        }

        $user_info = $user[DATA];

        // cập nhật thông tin cho user_info
        if(is_object($user_info)) $user_info = $user_info->toArray();
        $this->Auth->setUser($user_info);

        // chuyển hướng đang nhập
        $url_redirect = $this->Auth->redirectUrl();
        if(empty($url_redirect) || $url_redirect == '/'){
            $url_redirect = ADMIN_PATH . '/main';
        }

        if(!empty($redirect) && $redirect != '/'){
            $url_redirect = $redirect;
        }        

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'dang_nhap_thanh_cong'),
            DATA => [
                'user' => $user_info,
                'url_redirect' => $url_redirect
            ]
        ]);
    }

    public function logout() 
    {
        $request = $this->getRequest();

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $profile = !empty($settings['profile']) ? $settings['profile'] : [];

        $admin_url = !empty($profile['admin_url']) ? $profile['admin_url'] : null;

        $url_redirect = $this->Auth->logout();
        if (!empty($admin_url)) {
            $url_redirect = $url_redirect . '/' . $admin_url;
        }
        
        
        $request->getSession()->delete('language_admin');
        $request->getSession()->delete(LANG);
        return $this->redirect($url_redirect);
    }

    public function forgotPassword()
    {
        $this->viewBuilder()->setLayout('account');

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

        $email = !empty($data['email']) ? trim($data['email']) : null;
        if(empty($email)) {
            $this->responseJson([MESSAGE => __d('template', 'vui_long_nhap_dia_chi_email')]);
        }

        $user_info = TableRegistry::get('Users')->find()->where([
            'email' => $email,
            'deleted' => 0
        ])->select(['id', 'email', 'status'])->first();
        if(empty($user_info['id'])) {
            $this->responseJson([MESSAGE => __d('template', 'thong_tin_tai_khoan_khong_ton_tai')]);
        }

        if(empty($user_info['status'])) {
            $this->responseJson([MESSAGE => __d('template', 'tai_khoan_cua_ban_hien_dang_bi_khoa')]);
        }

        $params_email = [
            'to_email' => $email,
            'code' => ADMIN_FORGOT_PASSWORD,
            'id_record' => $user_info['id'],
            'title_email' => __d('template', 'quen_mat_khau'),
            'generate_token' => AD_FORGOT_PASSWORD
        ];

        $send_email = $this->loadComponent('Admin.Email')->send($params_email);
        if(!empty($send_email[CODE]) && $send_email[CODE] == ERROR) {
            $this->responseJson([MESSAGE => __d('template', 'khong_gui_duoc_email')]);
        }

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('template', 'gui_ma_xac_nhan_thanh_cong'),
            DATA => [
                'email' => $email
            ]
        ]);
    }

    public function verifyForgotPassword()
    {
        $params = $this->request->getQueryParams();
        $email = !empty($params['email']) ? $params['email'] : null;

        $check_email = TableRegistry::get('Users')->checkExistEmail($email);

        if(empty($email) || !$check_email) {
            return $this->redirect('/404');
        }

        $this->viewBuilder()->setLayout('account');

        $this->js_page = [
            '/admin/assets/js/pages/verify_forgot_password.js'
        ];

        $this->set('title_for_layout', __d('template', 'xac_nhan_quen_mat_khau'));
        $this->render('verify_forgot_password');
    }

    public function ajaxVerifyForgotPassword()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $email = !empty($data['email']) ? trim($data['email']) : null;
        $code = !empty($data['code']) ? trim($data['code']) : null;
        $new_password = !empty($data['new_password']) ? trim($data['new_password']) : null;
        $confirm_password = !empty($data['confirm_password']) ? trim($data['confirm_password']) : null;

        if(empty($email)) {
            $this->responseJson([MESSAGE => __d('template', 'vui_long_nhap_dia_chi_email')]);
        }

        if(empty($code)) {
            $this->responseJson([MESSAGE => __d('template', 'vui_long_nhap_ma_xac_nhan')]);
        }

        if(empty($new_password) || empty($confirm_password)) {
            $this->responseJson([MESSAGE => __d('template', 'vui_long_nhap_day_du_thong_tin')]);
        }

        if($new_password != $confirm_password){
            $this->responseJson([MESSAGE => __d('template', 'xac_nhan_mat_khau_khong_chinh_xac')]);
        }

        $table = TableRegistry::get('Users');

        $user_info = $table->find()->where([
            'email' => $email,
            'deleted' => 0
        ])->select(['id', 'email', 'status'])->first();
        if(empty($user_info['id'])) {
            $this->responseJson([MESSAGE => __d('template', 'thong_tin_tai_khoan_khong_ton_tai')]);
        }

        if(empty($user_info['status'])) {
            $this->responseJson([MESSAGE => __d('template', 'tai_khoan_cua_ban_hien_dang_bi_khoa')]);
        }

        $email_token = TableRegistry::get('EmailToken');
        $token_info = $email_token->find()->where([
            'email' => $email,
            'code' => $code,
            'type' => AD_FORGOT_PASSWORD,
            'status' => 0,
            'end_time >=' => time()
        ])->first();

        if(empty($token_info)) {
            $this->responseJson([MESSAGE => __d('template', 'thong_tin_khong_chinh_xac_hoac_ma_xac_nhan_da_het_han')]);
        }

        $entity_token = $email_token->patchEntity($token_info, ['status' => 1]);

        $password_hasher = new DefaultPasswordHasher();
        $data_save = [
            'password' => $password_hasher->hash(trim($new_password)),
            'login_error' => 0,
            'updated' => time()
        ];

        // nếu tk bị khóa vì nhập sai mật khẩu, thì mở lại tài khoản
        if(!empty($user_info['status']) && $user_info['status'] == 2) $data_save['status'] = 1;
        $entity_user = $table->patchEntity($user_info, $data_save);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
                        
            $save_password = $table->save($entity_user);
            if (empty($save_password->id)){
                throw new Exception();
            }

            $save = $email_token->save($entity_token);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();

            $this->responseJson([
                CODE => SUCCESS, 
                MESSAGE => __d('template', 'thay_doi_mat_khau_thanh_cong')
            ]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function resendVerifyCode()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $email = !empty($data['email']) ? trim($data['email']) : null;
        $generate_token = !empty($data['generate_token']) ? trim($data['generate_token']) : null;

        if(empty($email)){
            $this->responseJson([MESSAGE => __d('template', 'vui_long_nhap_dia_chi_email')]);
        }

        if(empty($generate_token) || !in_array($generate_token, Configure::read('TYPE_TOKEN'))) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $session = $this->request->getSession();
        $time_verify_admin = $session->read('time_verify_admin');

        if(!empty($time_verify_admin) && (($time_verify_admin + 60) > time())) {
            $this->responseJson([MESSAGE => __d('template', 'thoi_gian_giua_hai_lan_gui_la_mot_phut')]);
        }

        $table = TableRegistry::get('Users');

        $user_info = $table->find()->where([
            'email' => $email,
            'deleted' => 0
        ])->select(['id', 'email', 'status'])->first();
        if(empty($user_info['id'])) {
            $this->responseJson([MESSAGE => __d('template', 'thong_tin_tai_khoan_khong_ton_tai')]);
        }

        if(empty($user_info['status'])) {
            $this->responseJson([MESSAGE => __d('template', 'tai_khoan_cua_ban_hien_dang_bi_khoa')]);
        }

        $session->write('time_verify_admin', time());

        $params_email = [
            'to_email' => $email,
            'code' => 'resend_verify_code',
            'generate_token' => $generate_token,
            'title_email' => __d('template', 'quen_mat_khau')
        ];

        $result = $this->loadComponent('Admin.Email')->send($params_email);
        if(!empty($result[CODE]) && $result[CODE] == ERROR) {
            $this->responseJson([MESSAGE => __d('template', 'khong_gui_duoc_email')]);
        }

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('template', 'gui_ma_xac_nhan_thanh_cong')
        ]);
    }

    public function list() 
    {
        $this->css_page = '/assets/plugins/global/lightbox/lightbox.css';
        $this->js_page = [
            '/assets/js/pages/list_user.js',
            '/assets/plugins/global/lightbox/lightbox.min.js'
        ];
        
        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'tai_khoan'));
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Users');
        $utilities = $this->loadComponent('Utilities');

        $data = $params = [];
        $limit = PAGINATION_LIMIT_ADMIN;
        $page = 1;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        // params query
        $params[QUERY] = !empty($data[QUERY]) ? $data[QUERY] : [];

        // params filter
        $params[FILTER] = !empty($data[DATA_FILTER]) ? $data[DATA_FILTER] : [];
        if(!empty($params[QUERY])){
            $params[FILTER] = array_merge($params[FILTER], $params[QUERY]);
        }

        // params sort         
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;

        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        try {
            $users = $this->paginate($table->queryListUsers($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();

        } catch (Exception $e) {
            $page = 1;
            $users = $this->paginate($table->queryListUsers($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Users']) ? $this->request->getAttribute('paging')['Users'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $users, 
            META => $meta_info
        ]);
    }

    public function add()
    {
        $roles = TableRegistry::get('Roles')->find()->where(['deleted' => 0])->select(['id', 'name'])->toArray();
        $list_role = Hash::combine($roles, '{n}.id', '{n}.name');

        $this->set('list_role', $list_role);

        $this->js_page = '/assets/js/pages/user.js';
        $this->set('title_for_layout', __d('admin', 'them_tai_khoan'));
        $this->render('update');
    }

    public function update($id = null)
    {
        if(empty($id)){
            $this->showErrorPage();
        }

        $table = TableRegistry::get('Users');
        $user_info = $table->getDetailUsers($id, [
            'get_role' => true
        ]);

        $user = $table->formatDataUserDetail($user_info);

        if(empty($user_info)){
            $this->showErrorPage();
        }

        $this->set('user', $user);
        $this->set('id', $id);
        
        $this->js_page = '/assets/js/pages/user.js';
        $this->set('title_for_layout', __d('admin', 'cap_nhat_tai_khoan'));
    }

    public function detail($id = null)
    {
        if(empty($id)){
            $this->showErrorPage();
        }

        $user_info = TableRegistry::get('Users')->getDetailUsers($id, [
            'get_role' => true
        ]);   
        $user = TableRegistry::get('Users')->formatDataUserDetail($user_info);

        if(empty($user)){
            $this->showErrorPage();
        }
    

        $this->css_page = [
            '/assets/css/pages/wizard/wizard-4.css',
            '/assets/plugins/global/lightbox/lightbox.css'
        ];
        $this->js_page = [
            '/assets/plugins/global/lightbox/lightbox.min.js'
        ];
       
        $this->set('user', $user);
        $this->set('title_for_layout', __d('admin', 'chi_tiet_tai_khoan'));
    }

    public function save($id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        if (empty($data) || !$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        } 

        $utilities = $this->loadComponent('Utilities');
        $users_table = TableRegistry::get('Users');

        $username = !empty($data['username']) ? trim($data['username']) : null;
        $email = !empty($data['email']) ? trim($data['email']) : null;
        $full_name = !empty($data['full_name']) ? trim($data['full_name']) : null;
        $phone = !empty($data['phone']) ? trim($data['phone']) : null;
        $address = !empty($data['address']) ? trim($data['address']) : null;
        $birthday = !empty($data['birthday']) ? trim($data['birthday']) : null;

        // validate data
        if(!empty($username)){
            $exist_username = $users_table->checkExistUsername($username, $id);           
            if($exist_username){
                $this->responseJson([MESSAGE => __d('admin', 'ten_dang_nhap_da_ton_tai_tren_he_thong')]);
            }
        }

        if(!empty($email)){
            $exist_email = $users_table->checkExistEmail(trim($email), $id);
            if($exist_email){
                $this->responseJson([MESSAGE => __d('admin', 'email_da_ton_tai_tren_he_thong')]);
            }
        }

        if(!empty($birthday) ){
            if(!$utilities->isDateClient($birthday)){
                $this->responseJson([MESSAGE => __d('admin', 'ngay_sinh') . ' - ' . __d('admin', 'chua_dung_dinh_dang_ngay_thang')]);
            }

            $birthday = $utilities->stringDateClientToInt($birthday);
        }

        $data_save = [
            'username' => $username,
            'email' => $email,
            'full_name' => $full_name,
            'phone' => $phone,
            'address' => $address,
            'birthday' => $birthday,
            'role_id' => !empty($data['role_id']) ? intval($data['role_id']) : null,
            'image_avatar' => !empty($data['image_avatar']) ? $data['image_avatar'] : null,
            'search_unicode' => strtolower($utilities->formatSearchUnicode([$username, $email, $full_name, $phone, $address]))
        ];

        if(empty($id)){
            if($data['password'] != $data['verify_password']){
                $this->responseJson([MESSAGE => __d('admin', 'xac_nhan_mat_khau_khong_chinh_xac')]);
            }

            $password_hasher = new DefaultPasswordHasher();
            $data_save['password'] = $password_hasher->hash(trim($data['password']));
        }        

        // merge data with entity   
        if(empty($id)){
            $data_save['created_by'] = $this->Auth->user('id');
            $user = $users_table->newEntity($data_save);
        }else{
            $user = $users_table->getDetailUsers($id);   
            if(empty($user)){
                $this->responseJson([MESSAGE => __d('admin', 'thong_tin_tai_khoan_khong_ton_tai')]);
            }
            $user = $users_table->patchEntity($user, $data_save);
        }    

        // show error validation in model
        if($user->hasErrors()){
            $list_errors = $utilities->errorModel($user->getErrors());
            $this->responseJson([
                MESSAGE => !empty($list_errors[0]) ? $list_errors[0] : null,
                DATA => $list_errors
            ]);             
        }
        
        $conn = ConnectionManager::get('default');
        try {
            $conn->begin();

            // save data
            $save = $users_table->save($user);    
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        } catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function changePassword($id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        if (empty($data) || empty($id) || !$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $new_password = !empty($data['new_password']) ? $data['new_password'] : null;
        $re_password = !empty($data['re_password']) ? $data['re_password'] : null;

        if(empty($new_password) || empty($re_password)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_day_du_thong_tin')]);
        }

        if($new_password != $re_password){
            $this->responseJson([MESSAGE => __d('admin', 'xac_nhan_mat_khau_khong_chinh_xac')]);
        }

        $user_table = TableRegistry::get('Users');   
        $user_info = $user_table->getDetailUsers($id);

        if (empty($user_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_tai_khoan')]);
        }
        
        $password_hasher = new DefaultPasswordHasher();
        $data_user = $user_table->patchEntity($user_info, [
            'password' => $password_hasher->hash($new_password)
        ]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = $user_table->save($data_user);

            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    public function delete()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $users_table = TableRegistry::get('Users');
        try{
            $users_table->updateAll(
                [  
                    'deleted' => 1
                ],
                [  
                    'id IN' => $ids
                ]
            );

            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'xoa_du_lieu_thanh_cong')]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function changeStatus()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];
        $status = !empty($data['status']) ? 1 : 0;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $users_table = TableRegistry::get('Users');
        try{
            $users_table->updateAll(
                [  
                    'status' => $status
                ],
                [  
                    'id IN' => $ids
                ]
            );            

            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function autoSuggest()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Users');
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $filter = !empty($data[FILTER]) ? $data[FILTER] : [];
        
        $users = $table->queryListUsers([
            FILTER => $filter,
            FIELD => FULL_INFO
        ])->limit(10)->toArray();

        $result = [];
        if(!empty($users)){
            foreach($users as $user){
                $result[] = $table->formatDataUserDetail($user);
            }
        }
  
        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
        ]);
    }

    public function profile()
    {
        $id = $this->Auth->user('id');  
        if(empty($id)) {
            $this->showErrorPage();
        }
        
        $user_info = TableRegistry::get('Users')->getDetailUsers($id, [
            'get_role' => true
        ]);

        $user = TableRegistry::get('Users')->formatDataUserDetail($user_info);
        if(empty($user)){
            $this->showErrorPage();
        }
        
        $this->js_page = '/assets/js/pages/user_profile.js';
        $this->set('user', $user);
        $this->set('title_for_layout', __d('admin', 'cap_nhat_tai_khoan'));
    }

    public function profileSave()
    {
        $this->layout = false;
        $this->autoRender = false;

        $id = $this->Auth->user('id');
        $data = $this->getRequest()->getData();

        if (empty($data) || empty($id) || !$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $utilities = $this->loadComponent('Utilities');
        $table = TableRegistry::get('Users');

        $user_info = $table->getDetailUsers($id);   
        if(empty($user_info)){
            $this->showErrorPage();
        }

        if(!empty($data['birthday']) ){
            if(!$utilities->isDateClient($data['birthday'])){
                $this->responseJson([MESSAGE => __d('admin', 'ngay_sinh') . ' - ' . __d('admin', 'chua_dung_dinh_dang_ngay_thang')]);
            }

            $data['birthday'] = $utilities->stringDateClientToInt(trim($data['birthday']));
        }

        $full_name = !empty($data['full_name']) ? trim($data['full_name']) : null;
        $phone = !empty($data['phone']) ? trim($data['phone']) : null;
        $address = !empty($data['address']) ? trim($data['address']) : null;

        $data_save = [
            'full_name' => $full_name,
            'phone' => $phone,
            'address' => $address,
            'birthday' => !empty($data['birthday']) ? $data['birthday'] : null,
            'image_avatar' => !empty($data['image_avatar']) ? $data['image_avatar'] : null,
            'search_unicode' => strtolower($utilities->formatSearchUnicode([$user_info['username'], $user_info['email'], $full_name, $phone, $address]))
        ];

        $user_save = $table->patchEntity($user_info, $data_save);
   
        // show error validation in model
        if($user_save->hasErrors()){
            $list_errors = $utilities->errorModel($user_save->getErrors());
            $this->responseJson([
                MESSAGE => !empty($list_errors[0]) ? $list_errors[0] : null,
                DATA => $list_errors
            ]);             
        }
        
        $conn = ConnectionManager::get('default');     
        try{
            $conn->begin();

            // save data
            $save = $table->save($user_save);    
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function profileChangePassword()
    {
        $id = $this->Auth->user('id');  
        if(empty($id)) {
            $this->showErrorPage();
        }
        
        $user_info = TableRegistry::get('Users')->getDetailUsers($id, [
            'get_role' => true
        ]);

        $user = TableRegistry::get('Users')->formatDataUserDetail($user_info);
        if(empty($user)){
            $this->showErrorPage();
        }
        
        $this->js_page = '/assets/js/pages/user_profile.js';
        $this->set('user', $user);
        $this->set('title_for_layout', __d('admin', 'thay_doi_mat_khau'));
    }
    public function changePasswordProfile()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $id = $this->Auth->user('id');

        if (empty($data) || empty($id) || !$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $old_password = !empty($data['old_password']) ? trim($data['old_password']) : null;
        $new_password = !empty($data['new_password']) ? trim($data['new_password']) : null;
        $re_password = !empty($data['re_password']) ? trim($data['re_password']) : null;

        if(empty($old_password) || empty($new_password) || empty($re_password)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_day_du_thong_tin')]);
        }

        if($new_password != $re_password){
            $this->responseJson([MESSAGE => __d('admin', 'xac_nhan_mat_khau_khong_chinh_xac')]);
        }

        $user_table = TableRegistry::get('Users');   
        $user_info = $user_table->find()->where(['id' => $id])->first();

        if (empty($user_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_tai_khoan')]);
        }

        $password_hasher = new DefaultPasswordHasher();
        if(!$password_hasher->check($old_password, $user_info['password'])){
            $this->responseJson([MESSAGE => __d('admin', 'mat_khau_cu_nhap_khong_chinh_xac')]);
        }

        if($password_hasher->check($new_password, $user_info['password'])) {
            $this->responseJson([MESSAGE => __d('admin', 'mat_khau_thay_doi_khong_the_giong_mat_khau_cu')]);
        }

        $data_user = $user_table->patchEntity($user_info, [
            'password' => $password_hasher->hash($new_password)
        ]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = $user_table->save($data_user);

            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    public function languageAdmin()
    {
        $id = $this->Auth->user('id');  
        if(empty($id)) {
            $this->showErrorPage();
        }
        
        $user_info = TableRegistry::get('Users')->getDetailUsers($id, [
            'get_role' => true
        ]);

        $user = TableRegistry::get('Users')->formatDataUserDetail($user_info);
        if(empty($user)){
            $this->showErrorPage();
        }
        
        $this->js_page = '/assets/js/pages/user_profile.js';
        $this->set('user', $user);
        $this->set('title_for_layout', __d('admin', 'ngon_ngu_quan_tri'));
    }

    public function saveLanguageAdmin()
    {

        $this->layout = false;
        $this->autoRender = false;

        $id = $this ->Auth->user('id');
        $data = $this->getRequest()->getData();

        $session = $this->request->getSession();        
        $data_language = $session->read('language_admin');
       
        if (empty($data) || empty($id) || !$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $table = TableRegistry::get('Users');
        $user_info = $table->getDetailUsers($id); 

        if(empty($user_info)){
            $this->showErrorPage();
        }
        $language_admin = !empty($data['language_admin']) ? $data['language_admin'] : null;
        $data_save = [
            'language_admin' => $language_admin,
        ];
         
        $user_save = $table->patchEntity($user_info, $data_save);
        
        $conn = ConnectionManager::get('default');    

        try{
            $conn->begin();

            // save data
            $save = $table->save($user_save);    
            if (empty($save->id)){
                throw new Exception();
            }
            $session->write('language_admin', $language_admin);
            $conn->commit();
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }

    }
  
    public function saveConfigSettingView()
    {
        $this->layout = false;
        $this->autoRender = false;

        $id = $this ->Auth->user('id');
        $data = $this->getRequest()->getData();
        if (empty($data) || empty($id) || !$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        if(!empty($this->Auth->user('supper_admin'))){
            $this->responseJson([
            CODE => SUCCESS
            ]);
        }
        
        $path_menu = !empty($data['path_menu']) ? $data['path_menu'] : null;
        $type = !empty($data['type']) ? $data['type'] : null;
        $fields = !empty($data['fields']) ? $data['fields'] : [];
        $filter = !empty($data['filter']) ? $data['filter'] : [];
        $sorts = !empty($data['sorts']) ? array_keys($data['sorts']) : [];

        $settings = Configure::read('SETTING_FOR_USER');
        $setting_view = !empty($settings['list_view'][$path_menu][$type]) ? $settings['list_view'][$path_menu][$type] : [];        
        if(empty($path_menu) || empty($type) || !in_array($type, [FIELD, FILTER]) || empty($setting_view)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $table = TableRegistry::get('Users'); 
        $user_info = $table->find()->where([
            'id' => $id,
            'deleted' => 0
        ])->select(['id', 'config'])->first();
        $user_config = !empty($user_info['config']) ? json_decode($user_info['config'], true) : [];
        if(empty($user_info)) $this->responseJson([MESSAGE => __d('admin', 'thong_tin_tai_khoan_khong_ton_tai')]);
        
        // xử lý dữ liệu cấu hình
        if($type == FIELD){
            $config = [];
            foreach($setting_view as $field_code => $item){
                $sort = @array_search($field_code, $sorts);

                $item['show'] = !empty($fields[$field_code]) ? 1 : 0;
                $item['sort'] = !empty($sort) ? $sort : 0;      
                $config[$field_code] = $item;            
            }
            $user_config['list_view'][$path_menu][$type] = $config;
        }
        
        if($type == FILTER){
            $config = [];
            foreach($setting_view as $filter_code => $item){
                if(empty($filter[$filter_code])) continue;
                $item['show'] = 1;               
                $config[$filter_code] = $item;
            }
            $user_config['list_view'][$path_menu][$type] = $config;
        }

        $entity = $table->patchEntity($user_info, ['config' => json_encode($user_config)]);
        $update = $table->save($entity);    
        if (empty($update->id)){
            $this->responseJson([MESSAGE => __d('admin', 'cap_nhat_khong_thanh_cong')]);
        }
        
        $auth_user = $this->Auth->user();
        $auth_user['config'] = $user_config;

        $this->Auth->setUser($auth_user);

        $this->responseJson([CODE => SUCCESS]);
    }
}
