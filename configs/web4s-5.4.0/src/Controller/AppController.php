<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Core\Configure;
use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Routing\Router;
use Cake\I18n\I18n;
use Cake\Http\Cookie\Cookie;
use DateTime;
use Cake\Utility\Hash;

class AppController extends Controller
{
    public $get_structure_layout = true;
    public $out_date = false;

    public $language = 'vi';

    public function initialize(): void
    {
        parent::initialize();
        if (defined('LANGUAGE')) {
            $this->language = LANGUAGE;
        }
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $validate = $this->validateUrlQueryParams();
        if(empty($validate)) return $this->redirect('/');

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        
        // kiểm tra thời hạn sử dụng website
        if (!empty($settings['profile']['end_date']) && intval($settings['profile']['end_date']) < time() ) {
            $this->out_date = true;
        }

        if($this->out_date){
            $this->viewBuilder()->enableAutoLayout(false);
            return $this->render('/outDate/out_date');
        }


        // kiểm tra debug_code 
        $white_list_ips = Configure::read('WHITE_LIST_IP');
        $client_ip = $this->request->clientIp();          
        if (in_array($client_ip, $white_list_ips) && !empty($settings['website_mode']['debug_code'])) {
            Configure::write('debug', true);
        }

        // chạy mirgate website
        $migrates = TableRegistry::get('PhinxLog')->migrates();

        // redirect 301
        $check_redirect = !empty($settings['redirect_301']) ? $settings['redirect_301'] : [];
       
        if(!empty($check_redirect['redirect_https']) && isset($_SERVER['HTTPS']) == false){
            $url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . $url);
            die;
        }

        if(!empty($check_redirect['redirect_301'])){
            $redirect = TableRegistry::get('SeoRedirects')->getRedirectUrl(ltrim($_SERVER['REQUEST_URI'], '/'));
            if(!is_null($redirect)){
                $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/' . $redirect;
                header('HTTP/1.1 301 Moved Permanently');
                header('Location: ' . $url);
                die;
            }
        }  

        //chuyển hướng nếu đường dẫn có dấu / ở sau cùng
        if(!empty(strpos($_SERVER['REQUEST_URI'], '/', -1)) && strpos($_SERVER['REQUEST_URI'], '/', -1) == strlen($_SERVER['REQUEST_URI']) - 1){
            $url_redirect = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . rtrim($_SERVER['REQUEST_URI'], '/');
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . $url_redirect);
            die;
        }
                
        // get info template
        $template = TableRegistry::get('Templates')->getTemplateDefault();
        $template_code = !empty($template['code']) ? $template['code'] : null;
        if(empty($template_code)) die('Không lấy được thông tin giao diện');
        
        $path_template = SOURCE_DOMAIN  . DS . 'templates' . DS . $template_code . DS;
        $url_template = '/templates/' . $template_code . '/';
        $folder_template = new Folder($path_template);

        if(empty($folder_template->path)) die('Không tìm thấy thư mục chứa giao diện');

        // set config path
        Configure::write('App.paths.templates', $path_template);
        Configure::write('App.paths.locales', $path_template . 'locales' . DS);

        // mobile template
        $mobile_template_code = null;
        $addons = TableRegistry::get('Addons')->getList();
        if(!empty($addons[MOBILE_APP]) && !defined('CODE_MOBILE_TEMPLATE')){
            $mobile_template = TableRegistry::get('MobileTemplate')->getTemplateDefault();
            $mobile_template_code = !empty($mobile_template['code']) ? $mobile_template['code'] : null;
        }        

        $url = !empty($this->request->getUri()) ? substr($this->request->getUri()->getPath(), 1) : null;
        $url = !empty($url) ? urldecode($url) : '';
        $language = $this->loadComponent('System')->getLanguageFrontend($url);        
        if(empty($language)) die('Không lấy được thông tin ngôn ngữ của Website');

        // currency
        $currency_default = TableRegistry::get('Currencies')->getDefaultCurrency();
        $currency_info = $this->loadComponent('System')->getCurrencyFrontend();
        if(empty($currency_info)) die('Chưa cài đặt đơn vị tiền tệ mặc định của Website');

        // define variable
        I18n::setLocale($language);
        define('LANGUAGE', $language);
        define('WEBSITE_MODE', !empty($settings['website_mode']['type']) ? $settings['website_mode']['type'] : DEVELOP);
        define('PATH_TEMPLATE', $path_template);
        define('URL_TEMPLATE', $url_template);
        define('CODE_TEMPLATE', $template_code);

        define('CODE_MOBILE_TEMPLATE', $mobile_template_code);        
        define('CURRENCY_CODE', !empty($currency_info['code']) ? $currency_info['code'] : null);
        define('CURRENCY_RATE', !empty($currency_info['exchange_rate']) ? $currency_info['exchange_rate'] : null);
        define('CURRENCY_UNIT', !empty($currency_info['unit']) ? $currency_info['unit'] : null);

        define('CURRENCY_CODE_DEFAULT', !empty($currency_default['code']) ? $currency_default['code'] : null);
        define('CURRENCY_UNIT_DEFAULT', !empty($currency_default['unit']) ? $currency_default['unit'] : null);

        $params = $this->request->getQueryParams();
        $layout_mode = !empty($params['nh-mode']) ? $params['nh-mode'] : null;
        $device = !empty($params['nh-device']) ? $params['nh-device'] : null;
        if($layout_mode == 'layout-builder' && !empty($device)){
            switch($device){
                case 'desktop':
                    define('DEVICE', 0);
                    define('TABLET', 0);
                break;

                case 'mobile':
                    define('DEVICE', 1);
                break;
            }
        }

        if(!defined('DEVICE')) define('DEVICE', $this->request->is('mobile') ? 1 : 0);
        if(!defined('TABLET')) define('TABLET', $this->request->is('tablet') ? 1 : 0);
        
        if(!defined('CDN_URL')){
            if(!empty($settings['profile']['cdn_url'])){
                define('CDN_URL', $settings['profile']['cdn_url']);
            }else{
                define('CDN_URL', $this->request->scheme() . '://cdn.' . $this->request->host());
            }
        }

        if(!defined('TAG_PATH')){
            if(!empty($settings['tag']['prefix_url'])){
                define('TAG_PATH', '/'.$settings['tag']['prefix_url']);
            }else{
                define('TAG_PATH', '/tag');
            }
        }

        // kiểm tra có phải đang load trên google insight hoặc gt metrix k
        $google_insight = false;
        $user_agent = $this->request->getHeaderLine('User-Agent');        
        if(strpos($user_agent, 'Chrome-Lighthouse') !== false ||
            strpos($user_agent, 'GTmetrix') !== false
        ){
            $google_insight = true;
        }

        define('GOOGLE_INSIGHT', $google_insight);
    }

    private function validateUrlQueryParams()
    {
        $params = $this->request->getQueryParams();        
        if(empty($params) || !is_array($params)) return true;

        foreach($params as $key => $value){
            if(!is_string($value)) return false;
            
            
            $strip_tags = strip_tags($value);            
            if($strip_tags != $value) return false;
        }

        return true;
    }

    public function beforeRender(EventInterface $event)
    {
        parent::beforeRender($event);

        $this->viewBuilder()->setClassName('Smarty');

        if($this->out_date) return;

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $addons = TableRegistry::get('Addons')->getList();

        if (!$this->request->is('ajax') && $this->get_structure_layout) {
            $error = null;            

            $page = $this->getPageByUrl();

            $url = !empty($page['url']) ? $page['url'] : '';
            $code = !empty($page['code']) ? $page['code'] : null;
            $type = !empty($page['type']) ? $page['type'] : null;
            $version = !empty($page['version']) ? $page['version'] : null;
            $page_record_id = !empty($page['page_record_id']) ? intval($page['page_record_id']) : null;
            $product_id = !empty($page['product_id']) ? intval($page['product_id']) : null;
            $article_id = !empty($page['article_id']) ? intval($page['article_id']) : null;
            $tag_id = !empty($page['tag_id']) ? intval($page['tag_id']) : null;
            $page_category_id = !empty($page['category_id']) ? intval($page['category_id']) : null;
            $page_brand_id = !empty($page['brand_id']) ? intval($page['brand_id']) : null;

            $page_categories_id = [];
            if(!empty($page_category_id)){
                $page_categories_id = TableRegistry::get('Categories')->getAllChildCategoryId($page_category_id);
            }
            $page_info = !empty($page['page_info']) ? $page['page_info'] : [];

            define('PAGE_URL', $url);
            define('PAGE_RECORD_ID', $page_record_id);
            define('PAGE_TAG_ID', $tag_id);
            define('PAGE_CATEGORY_ID', $page_category_id);
            define('PAGE_BRAND_ID', $page_brand_id);
            define('PAGE_CATEGORIES_ID', $page_categories_id);
            define('PAGE_TYPE', $type);
            define('PAGE_CODE', $code);
            define('PAGE_VERSION', $version);
            
            $structure = $blocks = $seo_info = $breadcrumb = $schema_data = $data_init = [];
            $cache_page = false;

            
            $session = $this->request->getSession();

            if (!empty($page['ladi_id']) && $page['type'] == LADI_DETAIL) {
                
                $ladipage = TableRegistry::get('Ladipages')->find()->where([
                    'id' => $page['ladi_id'],
                    'status' => 1,
                    'deleted' => 0
                ])->first();

                if(!empty($ladipage)) {
                    echo $ladipage['content']; exit();
                }

                $error = '404';
            }
            
            if(!empty($code)){
                // get structure of page
                $structure_page = $this->getDataForTemplate();
                $blocks = !empty($structure_page['blocks']) ? $structure_page['blocks'] : [];
                $structure = !empty($structure_page['structure']) ? $structure_page['structure'] : [];
                $cache_page = !empty($structure_page['cache']) ? true :  false;


                //seo info
                $seo_infomation = $this->getSeoInfomation([
                    'page_info' => $page_info,
                    'category_id' => $page_category_id,
                    'brand_id' => $page_brand_id,
                    'product_id' => $product_id,
                    'article_id' => $article_id,
                    'tag_id' => $tag_id
                ]);
                
                $seo_info = !empty($seo_infomation['seo_info']) ? $seo_infomation['seo_info'] : null;
                
                $breadcrumb = !empty($seo_infomation['breadcrumb']) ? $seo_infomation['breadcrumb'] : null;
                $schema_data = !empty($seo_infomation['schema_data']) ? $seo_infomation['schema_data'] : null;                

                // get data init for layout
                $member_info = null;
                if(!empty($session->read(MEMBER))){
                    $member = $session->read(MEMBER);
                    $member_info = [
                        'id' => !empty($member['id']) ? intval($member['id']) : null,
                        'account_id' => !empty($member['account_id']) ? intval($member['account_id']) : null,
                        'code' => !empty($member['code']) ? $member['code'] : null,
                        'full_name' => !empty($member['full_name']) ? $member['full_name'] : null,
                        'email' => !empty($member['email']) ? $member['email'] : null,
                        'phone' => !empty($member['phone']) ? $member['phone'] : null,
                        'address' => !empty($member['full_address']) ? $member['full_address'] : null
                    ];
                }                

                $recaptcha = !empty($settings['recaptcha']) ? $settings['recaptcha'] : null;
                $social = !empty($settings['social']) ? $settings['social'] : null;
                $embed_code = !empty($settings['embed_code']) ? $settings['embed_code'] : null;
                // không load embed code khi google tool kiểm tra
                if(!empty(GOOGLE_INSIGHT)) $embed_code = null;

                                
                //lấy danh sách sản phẩm, bài viết yêu thích trong cookie
                $wishlist_total = !empty($this->request->getCookie(WISHLIST)) ? json_decode($this->request->getCookie(WISHLIST), true) : null;

                // nếu đã đăng nhập thì lấy ds yêu thích từ database
                if(!empty($member_info['account_id'])){
                    $wishlist_total = TableRegistry::get('Wishlists')->wishlistTotal($member_info['account_id']);
                }

                $data_init = [
                    'device' => DEVICE,
                    'member' => $member_info,                    
                    'social' => !empty($social) ? $social : null,
                    'template' => [
                        'code' => CODE_TEMPLATE,
                        'url' => URL_TEMPLATE
                    ],
                    'cdn_url' => CDN_URL,
                    'wishlist' => $wishlist_total,
                    'recaptcha' => !empty($recaptcha['use_recaptcha']) ? $recaptcha : null,
                    'embed_code' => !empty($embed_code['load_embed']) ? $embed_code : null
                ];

                // lấy cấu hình sản phẩm
                if(!empty($addons[PRODUCT])){
                    $cart = $session->read(CART);                    
                    $data_init['cart'] = !empty($cart['items']) ? $cart : null;
                    $data_init['product'] = !empty($settings['product']) ? $settings['product'] : [];
                }

                // lấy cấu hình thông báo
                if(!empty($addons[NOTIFICATION])){
                    // kiểm tra hệ thống đã có thông báo nào chưa
                    $notification_table = TableRegistry::get('Notifications');
                    $setting_notification = !empty($settings['notification']) ? $settings['notification'] : null;
                    $exist_notification = $notification_table->checkExistNotification(WEBSITE);
                    $last_time = $notification_table->getLastTimeNotification(WEBSITE);

                    $data_init['notification'] = [
                        'web_push_certificates' => !empty($setting_notification['web_push_certificates']) ? $setting_notification['web_push_certificates'] : null,
                        'exist' => $exist_notification,
                        'last_time' => $last_time
                    ];
                }

                // write cookie viewed
                $this->writeCookieViewed(PAGE_TYPE, PAGE_RECORD_ID);

                // cập nhật lượt truy cập
                TableRegistry::get('LogAccess')->updateLogAccess();

                // tracking source redirect to website
                $this->writeCookieTrackingSource();
            }else{
                $error = '404';
            }            

            // show amdin bar
            $nh_admin_bar = null;
            $auth_admin = $session->read('Auth.User');
            if(!empty($auth_admin) && DEVICE == 0 && empty($this->request->getQuery('nh-mode'))){
                // load file js show admin bar
                if(!defined('ADMIN_PATH')) define('ADMIN_PATH', '/admin');

                $nh_admin_bar = '<script nh-script="admin-bar" nh-admin-path="'. ADMIN_PATH .'" src="'. ADMIN_PATH .'/assets/frontend-admin-bar/admin-bar.js" type="text/javascript"></script>';
            }            

            $this->set('data_init', $data_init);
            $this->set('seo_info', $seo_info);
            $this->set('breadcrumb', $breadcrumb);
            $this->set('schema_data', $schema_data);
            
            $this->set('cache_page', $cache_page);
            $this->set('page_code', PAGE_CODE);
            $this->set('structure', $structure);
            $this->set('blocks', $blocks);

            $this->set('nh_admin_bar', $nh_admin_bar);
        }        

        if(!empty($error)){
            // đọc cấu hình chuyển hướng trang lỗi
            $redirect_page_error = !empty($settings['redirect_page_error']) ? $settings['redirect_page_error'] : null;
            
            if(!empty($redirect_page_error['redirect_page_error']) && $error == '404'){
                $redirect_page_type = !empty($redirect_page_error['redirect_page_type']) ? $redirect_page_error['redirect_page_type'] : '404';

                // chuyển hướng về trang chủ
                if($redirect_page_type == 'home' && !empty($url)){
                    return $this->redirect('/');
                }
                
                // chuyển hướng về trang 404
                if($redirect_page_type == '404' && !empty($url) && $url != '404'){
                    return $this->redirect('/404');
                }
            }
            
            $response_status = 403;
            if($error == '404') $response_status = 404;
            $this->response = $this->response->withStatus($response_status);

            $this->viewBuilder()->setLayout($error);
        }        
    }

    public function getPageByUrl($path = null)
    {
        if(empty($path)){
            $path = !empty($this->request->getUri()) ? urldecode($this->request->getUri()->getPath()) : null;
        }   

        $url = $this->clearPrefixAndSuffixUrl($path);
        $result = ['url' => $url];

        $page_type = null;
        if(empty($url)) {
            $page_type = HOME;
        }

        if(!empty($path) && defined('TAG_PATH') && strpos($path, TAG_PATH . '/') === 0){
            $page_type = TAG;
        }

        if(!empty($path) && strpos($path, '/' . ORDER .'/') === 0){
            $page_type = ORDER;
        }

        if(!empty($path) && strpos($path, '/' . MEMBER .'/') === 0){
            $page_type = MEMBER;
        }

        $page_params = [
            'page_type' => PAGE,
            'lang' => $this->language,
            'get_content' => true,
            'type' => $page_type
        ];
        
        if(empty($page_type)){
            $page_params['url'] = $url;
        }

        // get info template of page
        $page_info = TableRegistry::get('TemplatesPage')->getInfoPage($page_params);

        $page_record_id = $category_id = $brand_id = $article_id = $product_id = $tag_id = $author_id = $ladi_id = null;
        if(!empty($page_info['type']) && in_array($page_info['type'], [PRODUCT, ARTICLE])){
            $category_id = !empty($page_info['category_id']) ? intval($page_info['category_id']) : null;
        }

        if($page_type == TAG){
            $tag = TableRegistry::get('Tags')->getTagByUrl(str_replace(TAG_PATH . '/', '', $path));
            $tag_id = !empty($tag['id']) ? intval($tag['id']) : null;
        }

        if(empty($page_info)){
            $link_info = TableRegistry::get('Links')->getLinkByUrl($url);
            $type_link = !empty($link_info['type']) ? str_replace('category_', '', $link_info['type']) : null;            
            if(empty($type_link)) return $result;

            // get list category id apply for this page            
            if(!empty($type_link) && in_array($type_link, [PRODUCT, ARTICLE])){
                $category_id = !empty($link_info['foreign_id']) ? intval($link_info['foreign_id']) : null;

                $category = TableRegistry::get('Categories')->find()->where([
                    'id' => $category_id,
                    'status' => 1,
                    'deleted' => 0
                ])->select(['id'])->first();
                if(empty($category)) return $result;

                $page_record_id = $category_id;
            }

            if($type_link == BRAND_DETAIL){
                $brand_id = !empty($link_info['foreign_id']) ? intval($link_info['foreign_id']) : null;

                $brand = TableRegistry::get('Brands')->find()->where([
                    'id' => $brand_id,
                    'status' => 1,
                    'deleted' => 0
                ])->select(['id'])->first();
                if(empty($brand)) return $result;

                $page_record_id = $brand_id;
                $type_link = PRODUCT;
            }

            if(!empty($type_link) && $type_link == PRODUCT_DETAIL){
                $product_id = !empty($link_info['foreign_id']) ? intval($link_info['foreign_id']) : null;

                $product = TableRegistry::get('Products')->find()->where([
                    'id' => $product_id,
                    'status <>' => 0,
                    'deleted' => 0
                ])->select(['id', 'main_category_id'])->first();
                if(empty($product)) return $result;
                $main_category_id = !empty($product['main_category_id']) ? intval($product['main_category_id']) : null;

                $related_category = TableRegistry::get('CategoriesProduct')->find()->where([
                    'product_id' => $product_id
                ])->order('category_id ASC')->first();

                $category_id = !empty($related_category['category_id']) ? intval($related_category['category_id']) : null;
                // ưu tiên lấy danh mục chính
                if(!empty($main_category_id)) {
                    $category_id = $main_category_id;
                }
                $page_record_id = $product_id;
            }

            if(!empty($type_link) && $type_link == ARTICLE_DETAIL){
                $article_id = !empty($link_info['foreign_id']) ? intval($link_info['foreign_id']) : null;
                $article = TableRegistry::get('Articles')->find()->where([
                    'id' => $article_id,
                    'status' => 1,
                    'deleted' => 0
                ])->select(['id', 'main_category_id'])->first();
                if(empty($article)) return $result;
                $main_category_id = !empty($article['main_category_id']) ? intval($article['main_category_id']) : null;

                $related_category = TableRegistry::get('CategoriesArticle')->find()->where([
                    'article_id' => $article_id
                ])->order('category_id ASC')->first();

                $category_id = !empty($related_category['category_id']) ? intval($related_category['category_id']) : null;
                // ưu tiên lấy danh mục chính
                if(!empty($main_category_id)) {
                    $category_id = $main_category_id;
                }
                $page_record_id = $article_id;
            }

            if(!empty($type_link) && $type_link == AUTHOR_DETAIL){
                $author_id = !empty($link_info['foreign_id']) ? intval($link_info['foreign_id']) : null;
                $author_info = TableRegistry::get('Authors')->find()->where([
                    'id' => $author_id,
                    'status' => 1,
                    'deleted' => 0
                ])->select(['id'])->first();
                if(empty($author_info)) return $result;
                $page_record_id = $author_id;
            }

            if(!empty($type_link) && $type_link == LADI_DETAIL){
                $ladi_id = !empty($link_info['foreign_id']) ? intval($link_info['foreign_id']) : null;
                $page_record_id = $ladi_id;
            }
            
            $page_info = TableRegistry::get('TemplatesPage')->filterPage([
                'type' => $type_link,
                'category_id' => $category_id
            ]);
        }

        return [
            'url' => $url,
            'code' => !empty($page_info['code']) ? $page_info['code'] : null,
            'type' => !empty($page_info['type']) ? $page_info['type'] : null,
            'version' => !empty($page_info['version']) ? $page_info['version'] : null,
            'page_record_id' => $page_record_id,
            'article_id' => $article_id,
            'product_id' => $product_id,
            'tag_id' => $tag_id,
            'category_id' => $category_id,
            'brand_id' => $brand_id,
            'author_id' => $author_id,
            'ladi_id' => $ladi_id,
            'page_info' => $page_info
        ];
    }

    protected function clearPrefixAndSuffixUrl($url = null)
    {
        // xóa ký tự slash "/" ở đầu url
        $url = !empty($url) ? $this->loadComponent('Utilities')->str_replace_first('/', '', $url) : '';
        $url = rtrim($url, '/');
        // xóa .html, .php, .asp, .htm ở cuối url
        $url = !empty($url) ? str_replace('.html', '', $url) : '';
        $url = !empty($url) ? str_replace('.htm', '', $url) : '';
        $url = !empty($url) ? str_replace('.php', '', $url) : '';
        $url = !empty($url) ? str_replace('.asp', '', $url) : '';
        $url = !empty($url) ? str_replace('.aspx', '', $url) : '';

        return $url;
    }

    protected function getSeoInfomation($params = [])
    {
        $page_info = !empty($params['page_info']) ? $params['page_info'] : [];
        $page_code = !empty($page_info['code']) ? $page_info['code'] : null;

        $category_id = !empty($params['category_id']) ? intval($params['category_id']) : null;
        $brand_id = !empty($params['brand_id']) ? intval($params['brand_id']) : null;
        $product_id = !empty($params['product_id']) ? intval($params['product_id']) : null;
        $article_id = !empty($params['article_id']) ? intval($params['article_id']) : null;
        $tag_id = !empty($params['tag_id']) ? intval($params['tag_id']) : null;

        $seo_info = $breadcrumb = $schema_data = [];
        $seo_title = $seo_description = $seo_keyword = $seo_image = null;

        $setting_info = TableRegistry::get('Settings')->getSettingWebsite();
        $website_info = !empty($setting_info['website_info']) ? $setting_info['website_info'] : [];
        $website_info = TableRegistry::get('Settings')->formatDataWebsiteInfoByLang($website_info);
        $website_info = !empty($website_info[LANGUAGE]) ? $website_info[LANGUAGE] : [];
        
        $company_logo = !empty($website_info['company_logo']) ? $website_info['company_logo'] : null;

        if(!empty($category_id)){
            $type = null;
            if(strpos(PAGE_TYPE, PRODUCT) > -1){
                $type = PRODUCT;
            }

            if(strpos(PAGE_TYPE, ARTICLE) > -1){
                $type = ARTICLE;
            }

            $category_info = TableRegistry::get('Categories')->getDetailCategory($type, $category_id, LANGUAGE);
            $category_info = TableRegistry::get('Categories')->formatDataCategoryDetail($category_info);

            // check category has parent id
            if(!empty($category_info['path_id'])){
                $list_parent_id = array_filter(explode('|', $category_info['path_id']));
                foreach ($list_parent_id as $parent_id) {
                    $parent_info = TableRegistry::get('Categories')->getDetailCategory($type, $parent_id, LANGUAGE);
                    $parent_info = TableRegistry::get('Categories')->formatDataCategoryDetail($parent_info);
                    if(!empty($parent_info)){
                        $breadcrumb[] = [
                            'name' => !empty($parent_info['name']) ? $parent_info['name'] : null,
                            'url' => !empty($parent_info['url']) ? $parent_info['url'] : null
                        ];
                    }
                }
            }

            $category_name = !empty($category_info['name']) ? $category_info['name'] : null;
            $breadcrumb[] = [
                'name' => $category_name,
                'url' => !empty($category_info['url']) ? $category_info['url'] : null
            ];
            
            // seo info of category
            $seo_info = [
                'title' => !empty($category_info['seo_title']) ? $category_info['seo_title'] : $category_name,
                'description' => !empty($category_info['seo_description']) ? $category_info['seo_description'] : null,
                'keywords' => !empty($category_info['seo_keyword']) ? $category_info['seo_keyword'] : null,
                'image' => !empty($category_info['image_avatar']) ? $category_info['image_avatar'] : $company_logo
            ];
        }
        
        if(!empty($brand_id)){

            $brand_info = TableRegistry::get('Brands')->getDetailBrand($brand_id, LANGUAGE);
            $brand_info = TableRegistry::get('Brands')->formatDataBrandDetail($brand_info, LANGUAGE);
            
            $brand_name = !empty($brand_info['name']) ? $brand_info['name'] : null;
            $breadcrumb[] = [
                'name' => $brand_name,
                'url' => !empty($brand_info['url']) ? $brand_info['url'] : null
            ];
            
            // seo info of category
            $seo_info = [
                'title' => !empty($brand_info['seo_title']) ? $brand_info['seo_title'] : $brand_name,
                'description' => !empty($brand_info['seo_description']) ? $brand_info['seo_description'] : null,
                'keywords' => !empty($brand_info['seo_keyword']) ? $brand_info['seo_keyword'] : null,
                'image' => !empty($brand_info['image_avatar']) ? $brand_info['image_avatar'] : $company_logo
            ];

            $schema_data[BRAND] = $brand_info;
        }

        if(!empty($product_id)){
            $product_info = TableRegistry::get('Products')->getDetailProduct($product_id, LANGUAGE, [STATUS_ITEM => 1]);
            $product_info = TableRegistry::get('Products')->formatDataProductDetail($product_info, LANGUAGE);

            $product_name = !empty($product_info['name']) ? $product_info['name'] : null;
            $breadcrumb[] = [
                'name' => $product_name,
                'url' => !empty($product_info['url']) ? $product_info['url'] : null
            ];

            // seo info of product
            $seo_info = [
                'title' => !empty($product_info['seo_title']) ? $product_info['seo_title'] : $product_name,
                'description' => !empty($product_info['seo_description']) ? $product_info['seo_description'] : null,
                'keywords' => !empty($product_info['seo_keyword']) ? $product_info['seo_keyword'] : null,
                'image' => !empty($product_info['all_images'][0]) ? $product_info['all_images'][0] : $company_logo
            ];

            $rating_info = TableRegistry::get('Comments')->getSchemaRating($product_id, PRODUCT_DETAIL);
            $product_info['ratings'] = !empty($rating_info) ? $rating_info : [];

            $schema_data[PRODUCT_DETAIL] = $product_info;
        }

        if(!empty($article_id)){
            $articles_info = TableRegistry::get('Articles')->getDetailArticle($article_id, LANGUAGE);
            $articles_info = TableRegistry::get('Articles')->formatDataArticleDetail($articles_info, LANGUAGE);
            
            $articles_name = !empty($articles_info['name']) ? $articles_info['name'] : null;
            $breadcrumb[] = [
                'name' => !empty($articles_info['name']) ? $articles_info['name'] : $articles_name,
                'url' => !empty($articles_info['url']) ? $articles_info['url'] : null
            ];

            // seo info of article
            $seo_info = [
                'title' => !empty($articles_info['seo_title']) ? $articles_info['seo_title'] : $articles_name,
                'description' => !empty($articles_info['seo_description']) ? $articles_info['seo_description'] : null,
                'keywords' => !empty($articles_info['seo_keyword']) ? $articles_info['seo_keyword'] : null,
                'image' => !empty($articles_info['image_avatar']) ? $articles_info['image_avatar'] : $company_logo
            ];

            $rating_info = TableRegistry::get('Comments')->getSchemaRating($article_id, ARTICLE_DETAIL);
            $articles_info['ratings'] = !empty($rating_info) ? $rating_info : [];

            $schema_data[ARTICLE_DETAIL] = $articles_info;
        }

        if(!empty($tag_id)){
            $tag_info = TableRegistry::get('Tags')->getDetailTag($tag_id, LANGUAGE);
            
            $tag_name = !empty($tag_info['name']) ? $tag_info['name'] : null;
            $breadcrumb[] = [
                'name' => $tag_name,
                'url' => !empty($tag_info['url']) ? $tag_info['url'] : null
            ];

            // seo info of article
            $seo_info = [
                'title' => !empty($tag_info['seo_title']) ? $tag_info['seo_title'] : $tag_name,
                'description' => !empty($tag_info['seo_description']) ? $tag_info['seo_description'] : null,
                'keywords' => !empty($tag_info['seo_keyword']) ? $tag_info['seo_keyword'] : null,
                'image' => !empty($tag_info['image_avatar']) ? $tag_info['image_avatar'] : $company_logo
            ];
        }

        if(!empty($author_id)){
            $author_info = TableRegistry::get('Authors')->getDetailAuthor($author_id);
            $author_info = TableRegistry::get('Authors')->formatDataAuthorDetail($author_info);
            
            $author_name = !empty($author_info['name']) ? $author_info['name'] : null;
            $breadcrumb[] = [
                'name' => $author_name,
                'url' => !empty($author_info['url']) ? $author_info['url'] : null
            ];

            // seo info of article
            $seo_info = [
                'title' => !empty($author_info['seo_title']) ? $author_info['seo_title'] : $author_name,
                'description' => !empty($author_info['seo_description']) ? $author_info['seo_description'] : null,
                'keywords' => !empty($author_info['seo_keyword']) ? $author_info['seo_keyword'] : null,
                'image' => !empty($author_info['avatar']) ? $author_info['avatar'] : $company_logo
            ];


            $schema_data[] = $author_info;
        }

        if(!empty($page_code) && empty($seo_info)){
            $page_seo_info = TableRegistry::get('TemplatesPageContent')->getSeoInfoTemplate([
                'page_code' => $page_code,
                'lang' => LANGUAGE
            ]);

            $page_name = !empty($page_info['name']) ? $page_info['name'] : null;
                
            $seo_info = [
                'title' => !empty($page_seo_info['seo_title']) ? $page_seo_info['seo_title'] : $page_name,
                'description' => !empty($page_seo_info['seo_description']) ? $page_seo_info['seo_description'] : null,
                'keywords' => !empty($page_seo_info['seo_keyword']) ? $page_seo_info['seo_keyword'] : null,
                'image' => !empty($page_seo_info['seo_image']) ? $page_seo_info['seo_image'] : $company_logo
            ];
            
            $breadcrumb[] = [
                'name' => !empty($page_seo_info['seo_title']) ? $page_seo_info['seo_title'] : $page_name,
                'url' => PAGE_URL
            ];
        }

        // lay du lieu cho the alternate
        $languages = TableRegistry::get('Languages')->getList();

        $alternate = [];
        if(!empty(PAGE_RECORD_ID) && !empty(PAGE_TYPE)) {
            $alternate = TableRegistry::get('Links')->find()->where([
                'deleted' => 0,
                'foreign_id' => PAGE_RECORD_ID,
                'type' => in_array(PAGE_TYPE, [PRODUCT, ARTICLE]) ? 'category_' . PAGE_TYPE : PAGE_TYPE,
                'lang IN' => array_keys($languages)
            ])->select(['lang', 'url'])->toArray();        
        }elseif(!empty(PAGE_CODE)){
            $alternate = TableRegistry::get('TemplatesPageContent')->find()->where([
                'page_code' => PAGE_CODE,
                'lang IN' => array_keys($languages),
                'url <>' => '',
                'template_code' => defined('CODE_TEMPLATE') ? CODE_TEMPLATE : null
            ])->select(['lang', 'url'])->toArray();
        }
        $seo_info['alternate'] = $alternate;
        $seo_info['site_name'] = !empty($website_info['site_name']) ? $website_info['site_name'] : null;
        
        return [
            'breadcrumb' => $breadcrumb,
            'seo_info' => $seo_info,
            'schema_data' => $schema_data
        ];
    }

    protected function getDataForTemplate()
    {
        // get structure of page
        $devive = DEVICE;
        if(!empty(TABLET)){
            $devive = 2;
        }
        $structure_page = $this->getStructurePage(PAGE_CODE, $devive, true, PAGE_RECORD_ID);
        $blocks = !empty($structure_page['blocks']) ? $structure_page['blocks'] : [];
        $structure = !empty($structure_page['structure']) ? $structure_page['structure'] : [];
        $cache_page = !empty($structure_page['cache']) ? true :  false;

        // replace structure of desktop to mobile or tablet
        if(DEVICE == 1){
            $get_layout = $get_content = false;
            if(empty($structure['header']) && empty($structure['footer'])){
                $get_layout = true;
            }

            if(empty($structure['content'])){
                $get_content = true;
            }

            $structure_desktop = [];
            if($get_layout || $get_content){
                $structure_desktop = $this->getStructurePage(PAGE_CODE, 0, $get_layout, PAGE_RECORD_ID);
                $cache_page = !empty($structure_desktop['cache']) ? true :  false;
            }

            if($get_layout){
                $structure['header'] = !empty($structure_desktop['structure']['header']) ? $structure_desktop['structure']['header'] : [];
                $structure['footer'] = !empty($structure_desktop['structure']['footer']) ? $structure_desktop['structure']['footer'] : [];
                $blocks = array_merge($blocks, $structure_desktop['blocks']);
            }

            if($get_content){
                $structure['content'] = !empty($structure_desktop['structure']['content']) ? $structure_desktop['structure']['content'] : [];
                $blocks = array_merge($blocks, $structure_desktop['blocks']);
            }
        }

        return [
            'cache' => $cache_page,
            'structure' => $structure,
            'blocks' => $blocks
        ];
    }
    
    protected function getStructurePage($page_code = null, $device = 0, $get_layout = false, $id_record = null)
    {
        if(empty($page_code)) return [];
        $structure_page = TableRegistry::get('TemplatesRow')->getStructureRowOfPage($page_code, $device, $get_layout, $id_record);
        $list_block = !empty($structure_page['blocks']) ? $structure_page['blocks'] : [];

        // get list block used in website
        $blocks = [];        
        $cache = true;
        if(!empty($list_block)){
            $block_component = $this->loadComponent('Block');
            foreach ($list_block as $block_code => $block_info) {
                $block_type = !empty($block_info['type']) ? $block_info['type'] : '';
                $config = !empty($block_info['config']) ? $block_info['config'] : [];

                if(empty($block_info) || empty($block_info['status']) ||empty($block_type)) continue;

                // generate view of block HTML
                if($block_type == HTML){
                    $view_file = new File(PATH_TEMPLATE . BLOCK . DS . HTML . DS . $block_code . '.tpl', false);

                    if(!$view_file->exists()){                        
                        $html_content = !empty($config['html_content']) ? '{strip}' . $config['html_content'] . '{/strip}' : '';
                        $view_file->write($html_content, 'w', true);
                    }
                    $view_file->close();
     
                }

                // get data of block
                $block_info['data_block'] = $block_component->getDataBlock($block_info);

                // check block use cache                
                $cache_block = !empty($config['cache']) ? true : false;
                if(!$cache_block){
                    $cache = false;
                }

                if (in_array($block_type, [TAB_PRODUCT, TAB_ARTICLE])) {
                    $config_first = !empty($config['item']) ? reset($config['item']) : [];

                    $config = [
                        'class' => !empty($config['class']) ? $config['class'] : null,
                        'cache' => !empty($config['cache']) ? $config['cache'] : null,
                        'item' => !empty($config['item']) ? $config['item'] : null,
                        'data_ids' => !empty($config_first['data_ids']) ? $config_first['data_ids'] : [],
                        'data_type' => !empty($config_first['data_type']) ? $config_first['data_type'] : null,
                        'filter_data' => !empty($config_first['filter_data']) ? $config_first['filter_data'] : null,
                        NUMBER_RECORD => !empty($config[NUMBER_RECORD]) ? intval($config[NUMBER_RECORD]) : 12,
                        HAS_PAGINATION => !empty($config_first[HAS_PAGINATION]) ? $config_first[HAS_PAGINATION] : null,
                        SORT_FIELD => !empty($config_first[SORT_FIELD]) ? $config_first[SORT_FIELD] : null,
                        SORT_TYPE => !empty($config_first[SORT_TYPE]) ? $config_first[SORT_TYPE] : null
                    ];

                    $block_info['config'] = !empty($config) ? $config : [];
                }

                // push block info to result
                $blocks[$block_code] = $block_info;
            }
        }

        return [
            'cache' => $cache,
            'structure' => !empty($structure_page['structure']) ? $structure_page['structure'] : [],
            'blocks' => $blocks
        ];
    }

    protected function writeCookieViewed($type = null, $id_record = null)
    {
        if(empty($type) || empty($id_record) || !in_array($type, [PRODUCT_DETAIL, ARTICLE_DETAIL])) return;

        $cookie_key = null;
        switch($type){
            case PRODUCT_DETAIL;
                $cookie_key = PRODUCTS_VIEWED;
            break;

            case ARTICLE_DETAIL;
                $cookie_key = ARTICLES_VIEWED;
            break;
        }

        if(empty($cookie_key)) return;

        $viewed = !empty($this->request->getCookie($cookie_key)) ? json_decode($this->request->getCookie($cookie_key), true) : [];

        if(!in_array($id_record, $viewed)){
            $viewed[] = $id_record;            
        }

        $this->response = $this->response->withCookie(Cookie::create(
            $cookie_key,
            json_encode($viewed),
            [
                'expires' => new DateTime('+1 days'),
                'path' => ''
            ]
        ));
    }

    protected function getVisitSource() {
        $traffic_ref = !empty($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) : 'N/A';
        $traffic = $this->request->getQueryParams();
        
        if (!empty($traffic['utm_source']) && !empty($traffic['utm_medium'])) {
            
            if($traffic['utm_source'] == "facebook" AND $traffic['utm_medium'] == "cpc") {
                return 'Facebook';
            } 

            if($traffic['utm_source'] == "google" AND $traffic['utm_medium'] == "banner") {
                return 'GDN';
            } 

            if($traffic['utm_source'] == "google" AND $traffic['utm_medium'] == "cpc") {
                return 'ADW';
            } 

            if($traffic['utm_source'] == "newsletter" AND $traffic['utm_medium'] == "email") {
                return 'E-Mail';
            } 
            if($traffic['utm_source'] == "newspaper" AND $traffic['utm_medium'] == "cpc") {
                return 'Newspaper';
            }

            if($traffic['utm_source'] == "webnhanhoa" AND $traffic['utm_medium'] == "banner") {
                return 'Web NH';
            }

            return $traffic['utm_source'];
        }

        if (preg_match('/(www\\.)?google\\./', $traffic_ref)){
            $traffic_source = 'Google Search';

            if (!empty($traffic['gad_source']) || !empty($traffic['gclid'])) {
                $traffic_source = 'Google Adword';
            }
        }

        if (preg_match('/(www\\.)?facebook\\./', $traffic_ref)){
            return 'Facebook';
        } 

        if (preg_match('/(www\\.)?yahoo\\./', $traffic_ref)){
            return 'Yahoo!';
        }

        if (preg_match('/(www\\.)?bing\\./', $traffic_ref)){
            return 'Bing';
        }

        $ref = @parse_url($traffic_ref);
        if (!empty($ref['host']) && !preg_match('/'. $this->request->host() .'/', $ref['host'])) {
            return $ref['host'];
        }

        return 'Direct';
    }

    public function writeCookieTrackingSource()
    {
        if (isset($_COOKIE[TRAFFIC_SOURCE]) ) return true;

        $source = $this->getVisitSource();            
        // create cookie
        $this->response = $this->response->withCookie(Cookie::create(
            TRAFFIC_SOURCE,
            $source,
            [
                'expires' => new DateTime('+30 days'),
                'path' => ''
            ]
        ));
    
    }

    protected function showErrorPage($params = [])
    {
        $message = !empty($params[MESSAGE]) ? $params[MESSAGE] : __d('template', 'xu_ly_du_lieu_khong_thanh_cong');
        $title = !empty($params['title']) ? $params['title'] : __d('template', 'xu_ly_du_lieu_khong_thanh_cong');

        $this->set('message', $message);
        $this->set('title_for_layout', $title);
        $this->render('../Page/error');
    }

    protected function responseJson($params = []) 
    {
        $result = TableRegistry::get('Utilities')->getResponse($params);    
        exit(json_encode($result));
    }

}
