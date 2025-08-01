<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Exception\Exception;
use Cake\Utility\Security;
use Cake\Http\Client;
use Cake\Cache\Cache;
use Cake\Http\Response;

class GoogleSheetComponent extends Component
{
    public $controller = null;
    public $components = ['System', 'Utilities'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }

    public function getToken($data = [])
    {
        if(empty($data)) return [];

        $code = !empty($data['code']) ? $data['code'] : null;
        $refresh_token = !empty($data['refresh_token']) ? $data['refresh_token'] : null;

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $social = !empty($settings['social']) ? $settings['social'] : [];
        $client_id = !empty($social['google_client_id']) ? $social['google_client_id'] : null;
        $client_secret = !empty($social['google_secret']) ? $social['google_secret'] : null;

        if(empty($client_id) || empty($client_secret)) return [];

        $http = new Client();
        if(!empty(($code))) {
            $response = $http->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/admin/contact/google-auth-return',
                'grant_type' => 'authorization_code'
            ]);

            if(!$response->getStatusCode() == 200){
                $this->log(__d('template', 'khong_lay_duoc_ma_access_token'));
                return [];
            } 

        }else{
            if(empty($refresh_token)) {
                $this->log(__d('template', 'ma_refresh_token_khong_duoc_trong'));
                return [];
            }

            $response = $http->post('https://oauth2.googleapis.com/token', [
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'refresh_token' => $refresh_token,
                'grant_type' => 'refresh_token'
            ]);

            if($response->getStatusCode() != 200 || empty($response->getStringBody())){
                $this->log(__d('template', 'khong_lay_duoc_ma_refresh_token'));
                return [];
            }
        }

        $result_token = json_decode($response->getStringBody(), true);

        $result = [
            'access_token' => !empty($result_token['access_token']) ? $result_token['access_token'] : null,
            'expires_in' => !empty($result_token['expires_in']) ? intval($result_token['expires_in']) : 0
        ];

        if(!empty($result_token['refresh_token'])) {
            $result['refresh_token'] = $result_token['refresh_token'];
        }

        return $result;
    }

    public function syncTitleForm($id_form = null, $google_sheet_config = []) 
    {
        if(empty($id_form) || empty(($google_sheet_config))) return false;

        $contact_form = TableRegistry::get('ContactsForm')->find()->where([
            'id' => $id_form,
            'deleted' => 0
        ])->select(['id', 'name', 'fields'])->first();

        if(empty($contact_form)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_form')]);
        }

        $name_form = !empty($contact_form['name']) ? $contact_form['name'] : [];
        $fields = !empty($contact_form['fields']) ? json_decode($contact_form['fields'], 1) : [];

        $spreadsheet_id = !empty($google_sheet_config['spreadsheet_id']) ? $google_sheet_config['spreadsheet_id'] : null;
        $access_token = !empty($google_sheet_config['access_token']) ? $google_sheet_config['access_token'] : null;

        $http = new Client();

        // kiểm tra id spreadsheets đúng hay chưa
        $response_check = $http->get('https://sheets.googleapis.com/v4/spreadsheets/' . $spreadsheet_id,
            [],
            [
                'headers' => [
                    'Authorization' => "Bearer " . $access_token,
                    'Content-Type' => 'application/json',
                ]
            ]
        );
        $result_check = json_decode($response_check->getStringBody(), 1);
        if (!empty($result_check[ERROR][CODE]) && $result_check[ERROR][CODE] == 404) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'ma_bang_tinh_khong_dung_vui_long_kiem_tra_lai')]);
        }

        if (!empty($result_check[ERROR][CODE]) && $result_check[ERROR][CODE] == 403) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'ban_chua_cap_quyen_truy_cap_voi_bang_tinh')]);
        }

        if($response_check->getStatusCode() != 200 || empty($response_check->getStringBody())){
            return $this->System->getResponse([MESSAGE => __d('admin', 'ma_bang_tinh_khong_dung_vui_long_kiem_tra_lai')]);
        }

        // thay đổi tên sheet đầu tiên theo tên new form
        $response_update = $http->post('https://sheets.googleapis.com/v4/spreadsheets/' . $spreadsheet_id . ':batchUpdate',
            json_encode([
                'requests' => [
                    'updateSheetProperties' => [
                        'properties' => [
                            'sheetId' => 0, // id sheet đầu tiên
                            'title' => $name_form // tên sheet mới
                        ],
                        'fields' => 'title' // áp dụng cho thuộc tính title khi thay đổi
                    ]
                ]
            ]), 
            [
                'headers' => [
                    'Authorization' => "Bearer " . $access_token,
                    'Content-Type' => 'application/json',
                ]
            ]
        );
        if($response_update->getStatusCode() != 200 || empty($response_update->getStringBody())){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_thay_doi_duoc_ten_trang_tinh')]);
        }


        // kiểm tra google sheet đã có dữ liệu hay chưa, nếu chưa thì tiếp tục thêm , có thông báo lỗi
        $response_check = $http->get('https://sheets.googleapis.com/v4/spreadsheets/' . $spreadsheet_id . '/values/' . $name_form,
            [],
            [
                'headers' => [
                    'Authorization' => "Bearer " . $access_token,
                    'Content-Type' => 'application/json',
                ]
            ]
        );

        if($response_check->getStatusCode() != 200 || empty($response_check->getStringBody())){
            $this->log(__d('template', 'khong_lay_duoc_du_lieu_trang'));
            return $this->System->getResponse([MESSAGE => __d('admin', 'cau_hinh_bang_tinh_khong_thanh_cong')]);
            // return false;
        }

        $result_check = json_decode($response_check->getStringBody(), true);
        $values = !empty(($result_check['values'])) ? $result_check['values'] : [];
        if(!empty($values)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'trang_tinh_dang_co_du_lieu_vui_long_xoa_du_lieu_hoac_tao_bang_tinh_moi_de_cau_hinh')]);
        }
        

        // thêm tiêu đề cột form vào sheet
        $array_title = [];
        if(!empty(($fields))) {
            foreach ($fields as $key => $field) {
                if(!empty($field['label'])) {
                    array_push($array_title, $field['label']); 
                }
            }
        }

        $link = 'https://sheets.googleapis.com/v4/spreadsheets/'.$spreadsheet_id.'/values/'.$name_form.':append?valueInputOption=RAW';
        $response = $http->post($link,
            json_encode([
                "values" => [
                    $array_title
                ]
            ]), 
            [
                'headers' => [
                    'Authorization' => "Bearer " . $access_token,
                    'Content-Type' => 'application/json',
                ]
            ]
        );

        if($response->getStatusCode() != 200 || empty($response->getStringBody())){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_them_duoc_du_lieu_tren_trang_tinh')]);
        }

        return $this->System->getResponse([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);
    }

    public function appendData($id = null)
    {
        if(!$this->controller->getRequest()->is('post') || empty($id)){
           return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $api = !empty($options['api']) ? true : false;

        $table = TableRegistry::get('Contacts');
        $table_setting = TableRegistry::get('Settings');

        $contact_info = $table->getDetailContact($id, ['get_form' => true]);
        $id_form = !empty($contact_info['ContactsForm']['id']) ? $contact_info['ContactsForm']['id'] : null;
        $name_form = !empty($contact_info['ContactsForm']['name']) ? $contact_info['ContactsForm']['name'] : null;
        $form_config = !empty($contact_info['ContactsForm']['google_sheet_config']) ? json_decode($contact_info['ContactsForm']['google_sheet_config'], 1) : null;
        $spreadsheet_id = !empty($form_config['spreadsheet_id']) ? $form_config['spreadsheet_id'] : null;
        $contact = !empty($contact_info['value']) ? json_decode($contact_info['value'], 1) : null;

        $social = $table_setting->getSettingByGroup('social');
        $google_sheet_config = !empty($social['google_sheet_config']) ? json_decode($social['google_sheet_config'], 1) : [];
        $access_token = !empty($google_sheet_config['access_token']) ? $google_sheet_config['access_token'] : null;
        $refresh_token = !empty($google_sheet_config['refresh_token']) ? $google_sheet_config['refresh_token'] : null;
        $expires_in = !empty($google_sheet_config['expires_in']) ? $google_sheet_config['expires_in'] : 0;

        if(empty($refresh_token) || empty($name_form) || empty($contact) || empty($spreadsheet_id)){
            $this->log(__d('template', 'khong_tim_thay_thong_tin_lien_he'));
            return false;
        }

        if($expires_in < time() && !empty($refresh_token)) {

            // lưu thông tin access_token mới
            $result_token = $this->getToken(['refresh_token' => $refresh_token]);
            if(empty($result_token)){
                
                $this->log(__d('template', 'khong_lay_duoc_ma_cau_hinh'));

                $revoke_token = $this->revokeAccessToken($access_token);
                if(empty($revoke_token)) return false;

                return false;
            }else{
                $access_token = !empty($result_token['access_token']) ? $result_token['access_token'] : null;
                $expires_in = !empty($result_token['expires_in']) ? time() + intval($result_token['expires_in']) : null;

                $google_sheet_setting['access_token'] = $access_token;
                $google_sheet_setting['expires_in'] = $expires_in;

                $save_config = $table_setting->saveGoogleSheetConfig($google_sheet_setting);
                if(!$save_config) return false;
            }   
        }

        $value = [];
        foreach ($contact as $key => $val) {
            array_push($value, $val);
        }

        $data = [
            "values" => [
                $value
            ]
        ];
        $link = 'https://sheets.googleapis.com/v4/spreadsheets/'.$spreadsheet_id.'/values/'.$name_form.':append?valueInputOption=RAW';

        $http = new Client();
        $response = $http->post($link,
            json_encode($data), 
            [
                'headers' => [
                    'Authorization' => "Bearer " . $access_token,
                    'Content-Type' => 'application/json',
                ]
            ]
        );

        if($response->getStatusCode() != 200 || empty($response->getStringBody())){
            $this->log(__d('template', 'khong_them_duoc_du_lieu_tren_google_sheet'));
            return false;
        }

        return true;
    }

    public function configSpreadsheet($data = null)
    {
        if(!$this->controller->getRequest()->is('post') || empty($data)){
           return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $id_form = !empty($data['form_id']) ? intval($data['form_id']) : null;
        $spreadsheet_id = !empty($data['spreadsheet_id']) ? $data['spreadsheet_id'] : null;

        if(empty($id_form)) return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        if(empty($spreadsheet_id)) return $this->System->getResponse([MESSAGE => __d('admin', 'vui_long_nhap_ma_bang_tinh')]);

        $table_contact = TableRegistry::get('ContactsForm');
        $table_setting = TableRegistry::get('Settings');

        $contact_form = $table_contact->find()->where(['id' => $id_form, 'deleted' => 0])->select(['id', 'google_sheet_config'])->first();
        if(empty($contact_form)) return $this->System->getResponse([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_form')]);


        // lấy mã access_token 
        $setting = $table_setting->getSettingByGroup('social');
        $google_sheet_config = !empty($setting['google_sheet_config']) ? json_decode($setting['google_sheet_config'], 1) : [];
        $email = !empty($google_sheet_config['email']) ? $google_sheet_config['email'] : null;
        $access_token = !empty($google_sheet_config['access_token']) ? $google_sheet_config['access_token'] : null;
        $refresh_token = !empty($google_sheet_config['refresh_token']) ? $google_sheet_config['refresh_token'] : null;
        $expires_in = !empty($google_sheet_config['expires_in']) ? intval($google_sheet_config['expires_in']) : null;

        if(empty($email) || empty($access_token) || empty($refresh_token)) return $this->System->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_uy_quyen')]);

        // dùng khi KH cấu hình cùng email và access_token đã quá hạn
        if($expires_in < time() && !empty($refresh_token)) {
            
            // lưu thông tin access_token mới
            $result_token = $this->getToken(['refresh_token' => $refresh_token]);
            if(empty($result_token)){

                $google_sheet_config = []; // set cau hinh ve null
                $table_setting->saveGoogleSheetConfig($google_sheet_config);

                $this->revokeAccessToken($access_token);

                return $this->System->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_ma_uy_quyen')]);
            }else{

                $access_token = !empty($result_token['access_token']) ? $result_token['access_token'] : null;
                $expires_in = !empty($result_token['expires_in']) ? time() + intval($result_token['expires_in']) : null;

                $google_sheet_config['access_token'] = $access_token;
                $google_sheet_config['expires_in'] = $expires_in;

                $table_setting->saveGoogleSheetConfig($google_sheet_setting);
            }   
        }

        // lưu thông tin cấu hình google sheet
        $save_config = [
            'spreadsheet_id' => $spreadsheet_id,
            'email' => $email
        ];

        $entity_form = $table_contact->patchEntity($contact_form, ['google_sheet_config' => json_encode($save_config)]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $result = $this->syncTitleForm($id_form, ['spreadsheet_id' => $spreadsheet_id, 'access_token' => $access_token]);

            if(!empty($result[CODE]) && $result[CODE] == ERROR) {
                $message = !empty($result[MESSAGE]) ? $result[MESSAGE] : __d('admin', 'cau_hinh_bang_tinh_khong_thanh_cong');
                return $this->System->getResponse([MESSAGE => $message]);
            }

            $save = $table_contact->save($entity_form);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            
            return $this->System->getResponse([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }

    public function cancelConfigGoogleSheet($form_id = null)
    {
        if(!$this->controller->getRequest()->is('post') || empty($form_id)){
           return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('ContactsForm');
        $table_setting = TableRegistry::get('Settings');

        $contact_form = $table->find()->where(['id' => $form_id, 'deleted' => 0])->select(['id'])->first();
        if(empty($contact_form)) return $this->System->getResponse([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_form')]);

        // lấy mã access_token 
        $setting = $table_setting->getSettingByGroup('social');
        $google_sheet_config = !empty($setting['google_sheet_config']) ? json_decode($setting['google_sheet_config'], 1) : [];
        $email = !empty($google_sheet_config['email']) ? $google_sheet_config['email'] : null;
        $access_token = !empty($google_sheet_config['access_token']) ? $google_sheet_config['access_token'] : null;
        $refresh_token = !empty($google_sheet_config['refresh_token']) ? $google_sheet_config['refresh_token'] : null;
        $expires_in = !empty($google_sheet_config['expires_in']) ? intval($google_sheet_config['expires_in']) : null;

        if(empty($email) || empty($access_token) || empty($refresh_token)) return $this->System->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_uy_quyen')]);

        // dùng khi KH cấu hình cùng email và access_token đã quá hạn
        if($expires_in < time() && !empty($refresh_token)) {
            // lấy access_token mới để revoke
            $result_token = $this->getToken(['refresh_token' => $refresh_token]);
            $access_token = !empty($result_token['access_token']) ? $result_token['access_token'] : null;

            if(empty($access_token)){
                return $this->System->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_cap_quyen')]);
            }
        }

        $revoke_token = $this->revokeAccessToken($access_token);
        if(empty($revoke_token)) return $this->System->getResponse([MESSAGE => __d('admin', 'huy_cau_hinh_khong_thanh_cong')]);

        // bỏ thiết lập ủy quyền email
        $google_sheet_config = [];

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save_config = $table_setting->saveGoogleSheetConfig($google_sheet_config);
            if(empty($save_config)) throw new Exception();
            
            $conn->commit();
            return $this->System->getResponse([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }

    public function revokeAccessToken($access_token = null)
    {
        if(empty($access_token)) return false;

        $http = new Client();
        $response = $http->post("https://oauth2.googleapis.com/revoke?token=" . $access_token,
            [
                'token' => $access_token,
            ]
        );

        if($response->getStatusCode() != 200 || empty($response->getStringBody())) return false;

        return true;
    }

}