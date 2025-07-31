<?php

namespace App\Controller\Component;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Http\Client;

class SystemComponent extends Component
{
	public $controller = null;
    public $components = ['System', 'Utilities'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }
  
	public function getLanguageAdmin()
	{
        $request = $this->controller->getRequest();

		$params = $request->getQueryParams();
		$session = $request->getSession();

		$table = TableRegistry::get('Languages');
		$list_languages = $table->getList();
		if(!empty($params[LANG]) && !empty($list_languages[$params[LANG]])){
			$session->write(LANG, $params[LANG]);
		}
		
		$lang = $session->read(LANG);
		if(empty($lang)){
			$lang = $table->getDefaultLanguage();
			$session->write(LANG, $lang);
		}
		
		return $lang;
	}

    public function getLanguageFrontend($url = null)
    {

        $request = $this->controller->getRequest();
        $lang = $request->getSession()->read(LANG_FRONTEND);
        $language = null;
        
        if(empty($lang)){
            $lang = TableRegistry::get('Languages')->getDefaultLanguage();
            $request->getSession()->write(LANG_FRONTEND, $lang);
        }

        if (!empty($url)) {
            $language = TableRegistry::get('Links')->getLanguageByUrl($url);
        }

        if (!empty($language) && $lang != $language) {
            $lang = $language;
            $request->getSession()->write(LANG_FRONTEND, $lang);
        }
    
        return $lang;
    }

    public function getCurrencyFrontend()
    {
        $request = $this->controller->getRequest();
        $code = $request->getSession()->read(CURRENCY_PARAM);

        $result = [];
        $exchange_rate = 1;
        if(empty($code)){
            $currency_info = TableRegistry::get('Currencies')->getDefaultCurrency();    
        }else{
            $list_currencies = TableRegistry::get('Currencies')->getAll();
            $currency_info = !empty($list_currencies[$code]) ? $list_currencies[$code] : [];            
            if(empty($currency_info['is_default'])){
                $exchange_rate = !empty($currency_info['exchange_rate']) ? floatval($currency_info['exchange_rate']) : null;
            }            
        }

        if(empty($currency_info) || empty($currency_info['status'])) return [];

        return [
            'code' => !empty($currency_info['code']) ? $currency_info['code'] : null,
            'unit' => !empty($currency_info['unit']) ? $currency_info['unit'] : null,
            'exchange_rate' => $exchange_rate,
        ];
    }

	public function getNameUnique($class_name_table, $name = null, $index = 1)
    {
        $name_check = $name . ' ('. $index .')';
        if($index == 100){
            return $name_check;
        }

        $check = TableRegistry::get($class_name_table)->checkNameExist($name_check);
        if($check){
            $index ++;
            $name_check = $this->getNameUnique($class_name_table, $name, $index);
        }
        return $name_check;
    }

	public function getUrlUnique($url = null, $index = 1)
    {
        $url_check = $url . '-'. $index;
        if($index == 100){
            return $url_check;
        }

        $check = TableRegistry::get('Links')->checkExist($url_check);

        if($check){
            $index ++;
            $url_check = $this->getUrlUnique($url, $index);
        }
        return $url_check;
    }

    public function getResponse($params = []) 
    {
        $code = ERROR;
        if(!empty($params[CODE]) && in_array($params[CODE], [SUCCESS, ERROR])){
            $code = $params[CODE];
        }

        $message = !empty($params[MESSAGE]) ? $params[MESSAGE] : null;
        if(empty($params[MESSAGE]) && $code == ERROR){
            $message = __d('template', 'cap_nhat_khong_thanh_cong');
        }

        if(empty($params[MESSAGE]) && $code == SUCCESS){
            $message = __d('template', 'cap_nhat_thanh_cong');
        }
        
        $result = [
            CODE => $code,
            STATUS => !empty($params[STATUS]) ? intval($params[STATUS]) : 200,
            MESSAGE => $message
        ];

        if(isset($params[DATA])){
            $result[DATA] = !empty($params[DATA]) ? $params[DATA] : [];
        }

        if(isset($params[META])){
            $result[META] = !empty($params[META]) ? $params[META] : [];
        }

        return $result;
    }

    public function getProfileWebsite()
    {
        $profile = [
            'id' => 1,
            'full_name' => 'Lê Văn A',
            'created' => null,
            'expired_date' => null,
            'storage_cdn' => 10000
        ];

        return $profile;
    }

    public function readInfoSystemWebsite($profile_info = [], $params = [])
    {   
        $request = $this->controller->getRequest();
        $domain =  $request->host();

        $website_id = !empty($profile_info['website_id']) ? $profile_info['website_id'] : [];
        if(empty($domain)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_cau_hinh')]);
        }
        $get_usage_file = !empty($params['get_usage_file']) ? $params['get_usage_file'] : true;

        $dir_file = SOURCE_DOMAIN . DS . 'infomation.json';

        // kiem tra co file infomation.json
        $file_info = new File($dir_file, true);
        if(empty($file_info->path)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_tim_thay_tep_cau_hinh_cua_giao_dien')]);
        }

        if ($get_usage_file === 'true') {
            // doc noi dung file infomation.json
            $json_config = !empty($file_info->read()) ? trim($file_info->read()) : null;
            if(empty($json_config) || !$this->Utilities->isJson($json_config)){
                $json_config = $this->getDataForInfomationJsonFile($domain);
                $file_info->write(json_encode($json_config, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT), 'w');
                $file_info->close();
            }
            $file_info->close();

            $now_date = date('Y-m-d H:i:s');
            // kiem tra thoi han cua file
            $time = !empty($json_config['creation_time']) ? $json_config['creation_time'] : null;
            $end_time = !empty($time) ? date('Y-m-d H:i:s', strtotime("+1 days", $time)) : null;

            // neu thoi gian config + 1 > ngay hien tai thi lay thong tin config
            if (!empty($time) && !empty($end_time) && $end_time > $now_date) {
                return $json_config;
            }
        }

        // luu thong tin vao file config
        $data_config = $this->getDataForInfomationJsonFile($domain);
        $file_info->write(json_encode($data_config, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT), 'w');
        $file_info->close();

        return $data_config;
    }

    public function getDataForInfomationJsonFile($domain = null)
    {
        // call api lay dung luong dang su dung tren cdn
        $quota_used = 0;
        try{
            $url = CRM_URL . '/api/get-disk-space-cdn';
            $http = new Client();
            
            $response = $http->post($url, [
                'domain' => $domain
            ]);
            $json = $response->getJson();
            $quota_used = !empty($json['data']['quota_used']) ? floatval($json['data']['quota_used']) : 0;
        }catch (NetworkException $e) {
            $quota_used = 0;
        }

        $now_date = date('Y-m-d H:i:s');

        return [
            'creation_time' => !empty(strtotime($now_date)) ? strtotime($now_date) : '',
            'template_code' => defined('CODE_TEMPLATE') ? CODE_TEMPLATE : null,
            'cdn_disk_usage' => $quota_used
        ];
    }
}
