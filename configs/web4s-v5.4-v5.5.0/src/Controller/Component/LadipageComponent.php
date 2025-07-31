<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

class LadipageComponent extends Component
{
    public $controller = null;
    public $site_url = null;
    public $secret_key = null;
    public $components = ['System', 'Utilities'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();

        $request = $this->controller->getRequest();
        $this->site_url =  $request->scheme() . '://' . $request->host() . '/';

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $api_setting = !empty($settings['api']) ? $settings['api'] : [];
        $this->secret_key = !empty($api_setting['secret_key']) ? $api_setting['secret_key'] : ''; 
    }

    protected function sendJson($params) {
        return $this->System->getResponse($params);
    }

    protected function get_id_by_slug($page_slug) 
    {        
        $where = [
            'deleted' => 0,
            'url' => trim($page_slug),
        ];

        $page = TableRegistry::get('Links')->find()->where($where)->first();

        return $page;
    } 

    public function get_web_page($request, $post = 0) {
        $data = array('message' => '', 'content' => '');

        if (function_exists('curl_exec')) {
            $ch = curl_init();
            if ($post == 1) {
                curl_setopt($ch, CURLOPT_POST,1);
            }
            curl_setopt($ch, CURLOPT_USERAGENT, self::getRandomUserAgent());
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_URL, $request);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            $response = curl_exec($ch);
            if (!$response) {
                $data['message'] = 'cURL Error Number ' . curl_errno($ch) . ' : ' . curl_error($ch);
            } else {
                $data['content'] = $response;
            }
            curl_close($ch);
        }

        return $response;
    }

    public static  function getRandomUserAgent()
    {
        $userAgents = array(
            "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6)    Gecko/20070725 Firefox/2.0.0.6",
            "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)",
            "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)",
            "Opera/9.20 (Windows NT 6.0; U; en)",
            "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; en) Opera 8.50",
            "Mozilla/4.0 (compatible; MSIE 6.0; MSIE 5.5; Windows NT 5.1) Opera 7.02 [en]",
            "Mozilla/5.0 (Macintosh; U; PPC Mac OS X Mach-O; fr; rv:1.7) Gecko/20040624 Firefox/0.9",
            "Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/48 (like Gecko) Safari/48"       
        );
        $random = rand(0,count($userAgents)-1);
        return $userAgents[$random];
    }

    public function sniffRequests($params) {
        
        $act_product = !empty($params['action']) ? trim($params['action']) : null;
        $ladipage_key = !empty($params['ladipage_key']) ? trim($params['ladipage_key']) : null;
        $url = !empty($params['url']) ? trim($params['url']) : null;

        if (empty($act_product)) {
            return $this->sendJson([
                STATUS => 400,
                MESSAGE => __d('template', 'LadiPage action is not set or incorrect.')
            ]);
        }
        
        switch($act_product) {
            case 'publishldp': 
                return $this->publishLadiPage($params);
                break;

            case 'checkurl':

                $page_info = $this->get_id_by_slug($url);

                if (empty($page_info)) {
                    return $this->sendJson([
                        CODE => SUCCESS,
                        STATUS => 206,
                        MESSAGE => __d('template', 'URL does not exist.')
                    ]);
                }else{
                    return $this->sendJson([
                        CODE => SUCCESS,
                        STATUS => 205,
                        MESSAGE => __d('template', 'URL already exists.')
                    ]);
                }
                break;

            default:
                return $this->sendJson([
                    STATUS => 400,
                    MESSAGE => __d('template', 'LadiPage action is not set or incorrect.')
                ]);
        }
    }

    public function publishLadiPage($params) {
        
        if (empty($params['content'])) {
            return $this->sendJson([
                STATUS => 190,
                MESSAGE => __d('template', 'content cannot be empty')
            ]);
        }

        if (empty($params['title'])) {
            return $this->sendJson([
                STATUS => 190,
                MESSAGE => __d('template', 'title cannot be empty')
            ]);
        }

        if (empty($params['url'])) {
            return $this->sendJson([
                STATUS => 190,
                MESSAGE => __d('template', 'Page URL invalid!')
            ]);
        }

        $url = $params['url'];
        $title = $params['title'];
        $content = base64_decode($params['content']);


        /*$url = sprintf("https://api.ladipage.com/2.0/get-source-by-ladipage-key?ladipage_key=%s", $ladipage_key);
        $jsonString = file_get_contents($url);
        if (!$jsonString) {
            $jsonString = $this->get_web_page($url);
        }

        if (empty($jsonString)) {
            return $this->sendJson([
                STATUS => 500,
                MESSAGE => __d('template', 'Can not update HTML from this LadiPage Key. Please try publish again')
            ]);
        }

        $response = json_decode($jsonString);

        if (empty($response->code) || $response->code != 200) {
            return $this->sendJson([
                STATUS => 500,
                MESSAGE => $response->message
            ]);
        }

        $data = $response->data;
        if (!isset($data->url) || $data->url == '') {
            return $this->sendJson([
                STATUS => 403,
                MESSAGE => __d('template', 'Page URL invalid!')
            ]);
        }*/

        $pageId = null;
        $page = $this->get_id_by_slug($url);

        if (!empty($page)) {
            if ($page['type'] != LADI_DETAIL) {
                return $this->sendJson([
                    STATUS => 403,
                    MESSAGE => __d('template', 'Page URL invalid!')
                ]);
            }

            $pageId = $page['foreign_id'];
        }

        $table = TableRegistry::get('Ladipages');
        $lang = TableRegistry::get('Languages')->getDefaultLanguage();
        $data_save = [
            'name' => $title . ' - LadiPage',
            // 'ladipage_key' => $ladipage_key,
            'content' => trim($content),
            'status' => 1
        ];

        $data_save['Links'] = [
            'type' => LADI_DETAIL,
            'url' => $url,
            'lang' => $lang,
        ];

        // merge data with entity 
        if(empty($pageId)){
            $entity = $table->newEntity($data_save, [
                'associated' => ['Links']
            ]);
        } else {

            $ladipage = $table->getDetailLadipage($pageId, $lang);

            $entity = $table->patchEntity($ladipage, $data_save);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();

            return $this->sendJson([
                CODE => SUCCESS,
                STATUS => 200,
                MESSAGE => __d('template', "Publish successfully! Page URL: " . $this->site_url . $url)
            ]);

        } catch (Exception $e) {
            $conn->rollback();

            return $this->sendJson([
                STATUS => 500,
                MESSAGE => $e->getMessage()
            ]);
        }
    }    
}
