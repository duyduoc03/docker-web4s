<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Http\Response;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Http\Client;
use Cake\Cache\Cache;
use Cake\Utility\Hash;


class ContactFormController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function list()
    {
        $this->js_page = '/assets/js/pages/list_contact_form.js';
        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'form_lien_he'));
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('ContactsForm');
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

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;
        
        // sort 
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;
        
        try {
            $result = $this->paginate($table->queryListContactsForm($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        } catch (Exception $e) {
            $result = $this->paginate($table->queryListContactsForm($params), [
                'limit' => $limit,
                'page' => 1,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Payments']) ? $this->request->getAttribute('paging')['Payments'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
            META => $meta_info
        ]);
    }

    public function add()
    {
        $this->js_page = [
            '/assets/js/pages/contact_form.js',
        ];

        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'them_form_lien_he'));
        $this->render('update');
    }

    public function update($id = null)
    {
        $form = TableRegistry::get('ContactsForm')->find()->where(['id' => $id, 'deleted' => 0])->first();
        if(empty($form)){
            return $this->redirect(ADMIN_PATH . '/404');
        }

        $setting = TableRegistry::get('Settings')->getSettingByGroup('social');
        $google_sheet_config = !empty($setting['google_sheet_config']) ? json_decode($setting['google_sheet_config'], 1) : [];
        
        $form['fields'] = !empty($form['fields']) ? json_decode($form['fields'], true) : [];
        
        $this->set('path_menu', 'setting');
        $this->set('id', $id);      
        $this->set('form', $form);
        $this->set('google_sheet_config', $google_sheet_config);

        $this->js_page = [
            '/assets/js/pages/contact_form.js',
        ];
        $this->set('title_for_layout', __d('admin', 'cap_nhat_form_lien_he'));
    }

    public function save($id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $utilities = $this->loadComponent('Utilities');        
        $table = TableRegistry::get('ContactsForm');

        if(!empty($id)){
            $form = $table->find()->where(['id' => $id, 'deleted' => 0])->first();
            if(empty($form)){
                $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }
        }

        // validate data
        if(empty($data['name'])){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_ten_form')]);
        }

        if(empty($data['fields']) || !is_array($data['fields'])){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_day_du_cac_truong_thong_tin')]);
        }

        $fields = !empty($data['fields']) ? $data['fields'] : [];        
        foreach ($fields as $key => $field) {
            if(empty($field['code'])){
                $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_day_du_cac_truong_thong_tin')]);
            }

            if(empty($field['label'])){
                $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_day_du_cac_truong_thong_tin')]);
            }

            $fields[$key]['options'] = json_decode($field['options'], true);
        }
        
        
        $code = !empty($data['code']) ? trim($data['code']) : $utilities->generateRandomString();
        $data_save = [
            'name' => !empty($data['name']) ? trim($data['name']) : null,
            'code' => $code,
            'send_email' => !empty($data['send_email']) ? 1 : 0,
            'template_email_code' => !empty($data['template_email_code']) ? $data['template_email_code'] : null,
            'fields' => !empty($fields) ? json_encode($fields) : null,
            'google_sheet_status' => !empty($data['google_sheet_status']) ? 1 : 0,
            'search_unicode' => strtolower($utilities->formatSearchUnicode([$data['name'], $code]))
        ];

        // merge data with entity 
        if(empty($id)){
            $data_save['created_by'] = $this->Auth->user('id');
            $form = $table->newEntity($data_save);
        }else{            
            $form = $table->patchEntity($form, $data_save);
        }

        // show error validation in model
        if($form->hasErrors()){
            $list_errors = $utilities->errorModel($form->getErrors());
            
            $this->responseJson([
                MESSAGE => !empty($list_errors[0]) ? $list_errors[0] : null,
                DATA => $list_errors
            ]);             
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();           
            
            $save = $table->save($form);
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

        $table = TableRegistry::get('ContactsForm');

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach($ids as $id){
                $form = $table->find()->where(['id' => $id])->first();
                if (empty($form)) {
                    throw new Exception(__d('admin', 'khong_tim_thay_thong_tin_form'));
                }

                $form = $table->patchEntity($form, ['deleted' => 1], ['validate' => false]);
                $delete = $table->save($form);
                if (empty($delete)){
                    throw new Exception();
                }

            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'xoa_du_lieu_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function getLinkOauthGoogleSheet()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        $this->showErrorPage();
        
        $pathname = !empty($data['pathname']) ? $data['pathname'] : null;
        $array_pathname = explode('/', $pathname);
        $id_form = intval(end($array_pathname));

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($pathname) || empty($id_form)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table_contact = TableRegistry::get('ContactsForm');

        $contact_form = $table_contact->find()->where([
            'id' => $id_form,
            'deleted' => 0
        ])->select(['id'])->first();

        if(empty($contact_form)) $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_form')]);

        $setting = TableRegistry::get('Settings')->getSettingByGroup('social');
        $client_id = !empty($setting['google_client_id']) ? $setting['google_client_id'] : null;
        if(empty($client_id)) $this->responseJson([MESSAGE => __d('admin', 'vui_long_cau_hinh_mang_xa_hoi_google')]);

        $google_sheet_config = !empty($setting['google_sheet_config']) ? json_decode($setting['google_sheet_config'], 1) : [];
        if(!empty($google_sheet_config['email']) && !empty($google_sheet_config['refresh_token'])){
            $this->responseJson([MESSAGE => __d('admin', 'ban_da_cau_hinh_uy_quyen_google_sheet')]);
        }

        $redirect_uri = urlencode($this->request->scheme() . '://' . $this->request->host() . '/admin/contact/google-auth-return');
        $response_type = 'code';
        $scope = 'https://www.googleapis.com/auth/spreadsheets https://www.googleapis.com/auth/userinfo.email';
        $state = urlencode($this->request->scheme() . '://' . $this->request->host() . $pathname);
        $url = "https://accounts.google.com/o/oauth2/v2/auth?client_id=$client_id&redirect_uri=$redirect_uri&response_type=code&scope=$scope&state=$state&access_type=offline";

        $this->responseJson([
            CODE => SUCCESS, 
            DATA => [
                'url' => $url
            ]
        ]);
    }

    public function googleAuthReturn() 
    {
        $this->layout = false;
        $this->autoRender = false;

        $request = $this->getRequest();
        $redirect = $request->getQuery('state');  
        $code = $request->getQuery('code');

        if(empty($redirect)) return $this->redirect('/admin/error');

        $array_redirect = explode('/', $redirect);
        $id_form = intval(end($array_redirect));

        if(empty($id_form)) return $this->redirect($redirect . '?message=' . __d('admin', 'loi_xu_ly_du_lieu'));

        $table_setting = TableRegistry::get('Settings');
        $table_contact = TableRegistry::get('ContactsForm');

        $contact_form = $table_contact->find()->where([
            'id' => $id_form,
            'deleted' => 0
        ])->select(['id', 'fields'])->first();

        if(empty($contact_form)) return $this->redirect($redirect . '?message=' . __d('admin', 'khong_tim_thay_thong_tin_form'));

        // call api lấy mã access_token, refresh_token
        $result_token = $this->loadComponent('GoogleSheet')->getToken(['code' => $code]);
        if(empty($result_token)) $this->redirect($redirect . '?message=' . __d('admin', 'khong_lay_duoc_thong_tin_uy_quyen'));

        $access_token = !empty($result_token['access_token']) ? $result_token['access_token'] : null;

        // lấy thông tin email ủy quyền
        $http = new Client();
        $response = $http->get('https://www.googleapis.com/oauth2/v3/userinfo?access_token='.$access_token);

        $userinfo = json_decode($response->getStringBody(), true);
        $email = !empty($userinfo['email']) ? $userinfo['email'] : null;

        if(empty($email)) return $this->redirect($redirect . '?message=' . __d('admin', 'khong_lay_duoc_email_cap_quyen'));

        // lưu thông tin ủy quyền tk vào setting
        $setting = $table_setting->getSettingByGroup('social');
        $google_sheet_setting = !empty($setting['google_sheet_config']) ? json_decode($setting['google_sheet_config'], 1) : [];

        if(!empty($google_sheet_setting['email'])) return $this->redirect($redirect . '?message=' . __d('admin', 'ban_da_cau_hinh_uy_quyen_google_sheet_vui_long_huy_cau_hinh_hien_tai_de_thiet_lap_uy_quyen_moi'));

        $google_sheet_setting = [
            'email' => $email,
            'refresh_token' => !empty($result_token['refresh_token']) ? $result_token['refresh_token'] : null,
            'access_token' => $access_token,
            'expires_in' => !empty($result_token['expires_in']) ? time() + intval($result_token['expires_in']) : 0
        ];

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save_setting = $table_setting->saveGoogleSheetConfig($google_sheet_setting);
            if(empty($save_setting)) throw new Exception();

            $conn->commit();
            
            $this->redirect($redirect);

        }catch (Exception $e) {
            $conn->rollback();
            $this->redirect($redirect . '?message=' . __d('admin', 'loi_xu_ly_du_lieu'));
        }
    }

    public function deauthorizeEmail()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $form_id = !empty($data['form_id']) ? intval($data['form_id']) : null;
        if(empty($form_id)) $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);

        $result = $this->loadComponent('GoogleSheet')->cancelConfigGoogleSheet($form_id);
        if(!empty($result[CODE]) && $result[CODE] == ERROR) {
            $this->responseJson([MESSAGE => !empty($result[MESSAGE]) ? $result[MESSAGE] : __d('admin', 'cap_nhat_khong_thanh_cong')]);
        }

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'cap_nhat_thanh_cong')
        ]);
    }

    public function configSpreadsheet()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $result = $this->loadComponent('GoogleSheet')->configSpreadsheet($data);

        if(!empty($result[CODE]) && $result[CODE] == ERROR) {
            $this->responseJson([MESSAGE => !empty($result[MESSAGE]) ? $result[MESSAGE] : __d('admin', 'cau_hinh_bang_tinh_khong_thanh_cong')]);
        }

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'cap_nhat_thanh_cong')
        ]);
    }

    public function cancelConfigSpreadsheet() 
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $form_id = !empty($data['form_id']) ? intval($data['form_id']) : null;
        if(empty($form_id)) $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);

        $table = TableRegistry::get('ContactsForm');

        $contact_form = $table->find()->where(['id' => $form_id, 'deleted' => 0])->select(['id', 'google_sheet_config'])->first();
        if(empty($contact_form)) $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_form')]);

        $entity = $table->patchEntity($contact_form, ['google_sheet_config' => null]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = $table->save($entity);
            if(empty($save->id)) throw new Exception();
            
            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

}