<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Datasource\ConnectionManager;
use Laminas\Diactoros\UploadedFile;

class SettingController extends AppController {

    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Admin.UploadFile');
    }

    public function dashboard() 
    {
        $user = $this->Auth->user();
        $supper_admin = false;
        if(!empty($user['supper_admin'])){
            $supper_admin = true;
        }

        $this->js_page = [
            '/assets/js/pages/clear_cache.js'
        ];

        $this->set('title_for_layout', __d('admin', 'cau_hinh'));
        $this->set('path_menu', 'setting');
        $this->set('supper_admin', $supper_admin);

        $this->render('dashboard');
    }

    public function save($group = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        if (!$this->getRequest()->is('post') || empty($group) || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Settings');

        $settings = $table->find()->where([
            'group_setting' => $group
        ])->toArray();

        $settings_format = Hash::combine($settings, '{n}.code', '{n}.id');
        
        switch($group){
            case 'website_info':
                $languages = TableRegistry::get('Languages')->getList();
                if (empty($languages)) {
                    $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
                }

                foreach ($languages as $lang => $language) {
                    $sub_branch = !empty($data['sub_branch']) && !empty($data['sub_branch'][$lang]) ? $data['sub_branch'][$lang] : [];

                    $tmp_branch = [];
                    foreach ($sub_branch as $value) {
                        if(!empty($value['sub_name']) || !empty($value['sub_phone']) || !empty($value['sub_email']) || !empty($value['sub_address'])) {
                            array_push($tmp_branch, $value);
                        }
                    }

                    $data[$lang . '_sub_branch'] = !empty($tmp_branch) ? json_encode($tmp_branch) : null;
                }

                unset($data['sub_branch']);

                // upload favicon
                if(!empty($data['favicon_select'])) {
                    $this->_uploadFaviconBase64($data['favicon_select']);
                    $data['favicon'] = null;
                }
            break;

            case 'seo_info':
                $list_keyword = !empty($data['seo_keyword']) ? array_column(json_decode($data['seo_keyword'], true), 'value') : null;
                $data['seo_keyword'] = !empty($list_keyword) ? implode(', ', $list_keyword) : null;
            break;

            case 'email':
                $data['admin_receive_notification'] = !empty($data['admin_receive_notification']) ? 1 : 0;
            break;

            case 'profile':
                $data['admin_url'] = !empty($data['admin_url']) ? $data['admin_url'] : null;
            break;

             case 'approved_article':
             case 'approved_product':
                $data['role_id'] = !empty($data['role_id']) ? implode('|', $data['role_id']) : null;
            break;

            case 'point':
                $type_discount = !empty($data['type_discount']) ? $data['type_discount'] : null;
                $order_discount = isset($data['order_discount']) ? $data['order_discount'] : null;

                if (!empty($type_discount) && $type_discount == 1 && isset($order_discount) && $order_discount != '' && $order_discount <= 0) {
                    $this->responseJson([MESSAGE => __d('admin', 'chiet_khau_%_don_hang_phai_lon_hon_1_%')]);
                }

                if (!empty($type_discount) && $type_discount == 1 && isset($order_discount) && $order_discount != '' && $order_discount > 99) {
                    $this->responseJson([MESSAGE => __d('admin', 'chiet_khau_%_don_hang_phai_nho_hon_99_%')]);
                }

                $data['point_to_money'] = !empty($data['point_to_money']) ? str_replace(',', '', $data['point_to_money']) : null;
                $data['condition_refund_order'] = !empty($data['condition_refund_order']) ? str_replace(',', '', $data['condition_refund_order']) : null;
            break;

            case 'attendance':
                $list_keyword = !empty($data['seo_keyword']) ? array_column(json_decode($data['seo_keyword'], true), 'value') : null;
                $data['point_config'] = !empty($data['point_config']) ? implode(',',$data['point_config']) : null;
            break;

            case 'sms_brandname':
                $data['default_partner'] = !empty($data['default_partner']) ? $data['default_partner'] : null;
            break;

            case 'affiliate':
                $commissions = [];

                if (!empty($data['commissions'])) {
                    foreach ($data['commissions'] as $key => $item) {
                        array_push($commissions, [
                            'name' => !empty($item['name']) ? $item['name'] : null,
                            'key' => !empty($item['key']) ? intval($item['key']) : 0,
                            'image' => !empty($item['image']) ? $item['image'] : null,
                            'source' => !empty($item['source']) ? $item['source'] : null,
                            'number_referral' => !empty($item['number_referral']) ? intval(str_replace(',', '', $item['number_referral'])) : null,
                            'total_order' => !empty($item['total_order']) ? floatval(str_replace(',', '', $item['total_order'])) : null,
                            'profit' => !empty($item['profit']) ? $item['profit'] : null,
                            'status_discount_sale' => !empty($item['status_discount_sale']) ? intval($item['status_discount_sale']) : 0,
                            'profit_sale' => !empty($item['profit_sale']) ? $item['profit_sale'] : null,
                            'description' => !empty($item['description']) ? $item['description'] : null,
                        ]);
                    }

                    $data['commissions'] = json_encode($commissions);
                }
            break;

            case 'attributes_category':

                $category_id = !empty($data['category_id']) ? intval($data['category_id']) : null;

                $attributes = !empty($data['attributes']) ? implode(',', $data['attributes']) : null;
                $options = !empty($data['options']) ? $data['options'] : null;
                
                // trường hợp nếu cập nhật trạng thái thì pass qua | nếu cập nhật cấu hình mà chưa chọn danh mục áp dụng thì thông báo lỗi
                if (empty($category_id) && !isset($data['status'])) {
                    $this->responseJson([MESSAGE => __d('admin', 'vui_long_chon_danh_muc_ap_dung')]);
                }

                // lấy dữ liệu cấu hình thuộc tính áp dụng theo danh mục
                $settings = TableRegistry::get('Settings')->getSettingWebsite();
                $setting_attributes_category = !empty($settings['attributes_category']) ? $settings['attributes_category'] : [];
                
                $apply_attributes = !empty($setting_attributes_category['apply_attributes']) ? json_decode($setting_attributes_category['apply_attributes'], true) : [];
                $apply_options = !empty($setting_attributes_category['apply_options']) ? json_decode($setting_attributes_category['apply_options'], true) : [];


                $apply_attributes[$category_id] = $attributes;
                if (empty($attributes)){
                    unset($apply_attributes[$category_id]);
                }

                $apply_options[$category_id] = $options;
                if (empty($options)){
                    unset($apply_options[$category_id]);
                }
                $data['apply_attributes'] = json_encode($apply_attributes);
                $data['apply_options'] = json_encode($apply_options);

                unset($data['category_id']);
                unset($data['attributes']);
                unset($data['options']);
            break;

            case 'article_attributes_category':

                $category_id = !empty($data['category_id']) ? intval($data['category_id']) : null;
                $attributes = !empty($data['attributes']) ? implode(',', $data['attributes']) : null;
                $options = !empty($data['options']) ? $data['options'] : null;

                // trường hợp nếu cập nhật trạng thái thì pass qua | nếu cập nhật cấu hình mà chưa chọn danh mục áp dụng thì thông báo lỗi
                if (empty($category_id) && !isset($data['status'])) {
                    $this->responseJson([MESSAGE => __d('admin', 'vui_long_chon_danh_muc_ap_dung')]);
                }

                // lấy dữ liệu cấu hình thuộc tính áp dụng theo danh mục
                $settings = TableRegistry::get('Settings')->getSettingWebsite();
                $setting_attributes_category = !empty($settings['article_attributes_category']) ? $settings['article_attributes_category'] : [];

                $apply_attributes = !empty($setting_attributes_category['apply_attributes']) ? json_decode($setting_attributes_category['apply_attributes'], true) : [];
                $apply_options = !empty($setting_attributes_category['apply_options']) ? json_decode($setting_attributes_category['apply_options'], true) : [];
                $apply_attributes[$category_id] = $attributes;
                if (empty($attributes)){
                    unset($apply_attributes[$category_id]);
                }
                $apply_options[$category_id] = $options;
                if (empty($options)){
                    unset($apply_options[$category_id]);
                }

                $data['apply_attributes'] = json_encode($apply_attributes);
                $data['apply_options'] = json_encode($apply_options);

                unset($data['category_id']);
                unset($data['attributes']);
                unset($data['options']);
            break;

            case 'item_attributes_category':

                $category_id = !empty($data['category_id']) ? intval($data['category_id']) : null;
                $attributes = !empty($data['attributes']) ? implode(',', $data['attributes']) : null;
                $options = !empty($data['options']) ? $data['options'] : null;
                // trường hợp nếu cập nhật trạng thái thì pass qua | nếu cập nhật cấu hình mà chưa chọn danh mục áp dụng thì thông báo lỗi
                if (empty($category_id) && !isset($data['status'])) {
                    $this->responseJson([MESSAGE => __d('admin', 'vui_long_chon_danh_muc_ap_dung')]);
                }

                // lấy dữ liệu cấu hình thuộc tính áp dụng theo danh mục
                $settings = TableRegistry::get('Settings')->getSettingWebsite();
                $setting_attributes_category = !empty($settings['item_attributes_category']) ? $settings['item_attributes_category'] : [];

                $apply_attributes = !empty($setting_attributes_category['apply_attributes']) ? json_decode($setting_attributes_category['apply_attributes'], true) : [];

                $apply_options = !empty($setting_attributes_category['apply_options']) ? json_decode($setting_attributes_category['apply_options'], true) : [];

                $apply_attributes[$category_id] = $attributes;
                if (empty($attributes)){
                    unset($apply_attributes[$category_id]);
                }

                $apply_options[$category_id] = $options;
                if (empty($options)){
                    unset($apply_options[$category_id]);
                }

                $data['apply_attributes'] = json_encode($apply_attributes);
                $data['apply_options'] = json_encode($apply_options);

                unset($data['category_id']);
                unset($data['attributes']);
                unset($data['options']);
            break;

            case 'brands_category':

                $category_id = !empty($data['category_id']) ? intval($data['category_id']) : null;
                $attributes = !empty($data['attributes']) ? implode(',', $data['attributes']) : null;

                // trường hợp nếu cập nhật trạng thái thì pass qua | nếu cập nhật cấu hình mà chưa chọn danh mục áp dụng thì thông báo lỗi
                if (empty($category_id) && !isset($data['status'])) {
                    $this->responseJson([MESSAGE => __d('admin', 'vui_long_chon_danh_muc_ap_dung')]);
                }

                // lấy dữ liệu cấu hình thương hiểu áp dụng theo danh mục
                $settings = TableRegistry::get('Settings')->getSettingWebsite();
                $brands_item = !empty($settings['brands_category']) ? $settings['brands_category'] : [];

                $apply_attributes = !empty($brands_item['apply_brands']) ? json_decode($brands_item['apply_brands'], true) : [];
 
                $apply_attributes[$category_id] = $attributes;
                if (empty($attributes)){
                    unset($apply_attributes[$category_id]);
                }

                $data['apply_brands'] = json_encode($apply_attributes);

                unset($data['category_id']);
                unset($data['attributes']);
                
            break;

            case 'send_message':
                if (!empty($data['slack'])) {
                    $data['slack'] = json_encode($data['slack']);
                }

                if (!empty($data['telegram'])) {
                    $data['telegram'] = json_encode($data['telegram']);
                }

                if (!empty($data['apply'])) {
                    $data['apply'] = json_encode($data['apply']);
                }
            break;

            case 'qr_bank_transaction':
            case 'qr_normal':
                $config = !empty($data['config']) ? $data['config'] : null;
                $data['config'] = json_encode($config);
                unset($data['type']);
            break;

            case 'store_kiotviet':
                $config = !empty($data['config']) ? $data['config'] : null;
                $data['config'] = json_encode($config);
            break;
        }

        $data_save = [];    
        if(!empty($data)){
            foreach ($data as $code => $value) {
                $data_save[] = [
                    'id' => !empty($settings_format[$code]) ? intval($settings_format[$code]) : null,
                    'group_setting' => $group,
                    'code' => $code,
                    'value' => $value
                ];
            }
        }
       

        $data_save = $table->patchEntities($settings, $data_save);

        try{
            // save data
            $save = $table->saveMany($data_save);

            $this->responseJson([CODE => SUCCESS]);
        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    private function _uploadFaviconBase64($src = null)
    {        
        if(empty($src) || gettype($src) != 'string') return false;
        if(strpos($src, 'data:') != 0) return false;

        $src = str_replace('data:image/vnd.microsoft.icon;base64,', '', $src);
        $src = str_replace('data:image/x-icon;base64,', '', $src);
        $bin = base64_decode($src);

        $size = getImageSizeFromString($bin);
        if (empty($size['mime']) || strpos($size['mime'], 'image/vnd.microsoft.icon') !== 0) return false;

        $ext = substr($size['mime'], 6);
        if ($ext != 'vnd.microsoft.icon') return false;

        $img_file = WWW_ROOT . 'favicon.ico';
        file_put_contents($img_file, $bin);
        
        return true;
    }

    public function websiteInfo()
    {
        $group = 'website_info';
        $setting_info = TableRegistry::get('Settings')->getSettingByGroup($group);
        $setting_info = TableRegistry::get('Settings')->formatDataWebsiteInfoByLang($setting_info);

        $sub_branch = [];
        if (!empty($setting_info)) {
            foreach ($setting_info as $lang => $setting) {
                if (!empty($setting['sub_branch'])) {
                    $sub_branch[$lang] = json_decode($setting['sub_branch'], true);
                }
            }
        }

        $languages = TableRegistry::get('Languages')->getList();
        $lang = $this->lang;      

        $this->set('group', $group);
        $this->set('website_info', $setting_info);
        $this->set('sub_branch', $sub_branch);
        $this->set('lang', $this->lang);
        $this->set('languages', $languages);
        $this->set('title_for_layout', __d('admin', 'thong_tin_website'));

        $this->js_page = [
            '/assets/js/pages/setting_website_info.js'
        ];

        $this->set('path_menu', 'setting');
        $this->render('website_info');
    }

    public function link()
    {
        $group = 'link';
        $link = TableRegistry::get('Settings')->getSettingByGroup($group);

        $this->set('group', $group);
        $this->set('link', $link);

        $this->set('path_menu', 'setting');
        $this->js_page = [
            '/assets/js/pages/setting_link.js'
        ];

        $this->set('title_for_layout', __d('admin', 'duong_dan_tinh'));
        $this->render('link');
    }

    public function embedCode()
    {
        $group = 'embed_code';
        $setting_info = TableRegistry::get('Settings')->getSettingByGroup($group);

        $this->set('path_menu', 'template');
        $this->set('group', $group);
        $this->set('embed_code', $setting_info);

        $this->js_page = [
            '/assets/plugins/global/ace/ace.js',
            '/assets/plugins/global/ace/theme-monokai.js',
            '/assets/plugins/global/ace/mode-html.js',
            '/assets/js/pages/setting_embed_code.js'
        ];

        $this->set('title_for_layout', __d('admin', 'ma_nhung'));
        $this->render('embed_code');
    }

    public function clearCache()
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        TableRegistry::get('App')->deleteAllCache();
        
        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xoa_cache_thanh_cong')
        ]);        
    }

    public function clearData() 
    {
        // chỉ cho phép tài khoản root vào chức năng này
        $user = $this->Auth->user();
        if(empty($user['supper_admin'])){
            $this->showErrorPage('denied');
        }

        $this->js_page = [
            '/assets/js/pages/clear_data.js'
        ];
        $this->set('title_for_layout', __d('admin', 'xoa_du_lieu'));
        $this->render('clear_data');
    }

    public function processClearData()
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        
        if (empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'chua_chon_muc_xoa_du_lieu')]);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            if(!empty($data['category_product'])){
                $query_category_product = 
                '
                    SET FOREIGN_KEY_CHECKS = 0;
                    DELETE FROM links WHERE type = "'. CATEGORY_PRODUCT .'";
                    TRUNCATE TABLE categories_product;
                    DELETE FROM categories_content WHERE category_id IN (
                        SELECT id FROM categories WHERE type = "'. PRODUCT .'"
                    );
                    DELETE FROM categories WHERE type = "'. PRODUCT .'";
                    SET FOREIGN_KEY_CHECKS = 1;
                ';
                $conn->execute($query_category_product);
            }

            if(!empty($data['category_article'])){
                $query_category_article = 
                ' 
                    SET FOREIGN_KEY_CHECKS = 0;
                    DELETE FROM links WHERE type = "'. CATEGORY_ARTICLE .'";
                    TRUNCATE TABLE categories_article;
                    DELETE FROM categories_content WHERE category_id IN (
                        SELECT id FROM categories WHERE type = "'. ARTICLE .'"
                    );
                    DELETE FROM categories WHERE type = "'. ARTICLE .'";
                    SET FOREIGN_KEY_CHECKS = 1;
                ';
                $conn->execute($query_category_article);
            }

            if(!empty($data['article'])){
                $query_article = 
                '
                    SET FOREIGN_KEY_CHECKS = 0;
                    DELETE from links where type = "'. ARTICLE_DETAIL .'";
                    TRUNCATE TABLE articles_content;
                    TRUNCATE TABLE categories_article;
                    TRUNCATE TABLE articles_attribute;
                    TRUNCATE TABLE articles;
                    DELETE from tags_relation where type = "'. ARTICLE_DETAIL .'";
                    SET FOREIGN_KEY_CHECKS = 1;
                ';
                $conn->execute($query_article);
            }

            if(!empty($data['tag'])){
                $query_tag = 
                '
                    SET FOREIGN_KEY_CHECKS = 0;
                    TRUNCATE TABLE tags;
                    TRUNCATE TABLE tags_relation;
                    SET FOREIGN_KEY_CHECKS = 1;
                ';
                $conn->execute($query_tag);
            }

            if(!empty($data['brand'])){
                $query_brand = 
                '
                    SET FOREIGN_KEY_CHECKS = 0;
                    DELETE from links where type = "'. BRAND_DETAIL .'";
                    TRUNCATE TABLE brands;
                    TRUNCATE TABLE brands_content;
                    SET FOREIGN_KEY_CHECKS = 1;
                ';
                $conn->execute($query_brand);
            }

            if(!empty($data['product'])){
                $query_product = 
                '
                    SET FOREIGN_KEY_CHECKS = 0;
                    DELETE from links where type = "'. PRODUCT_DETAIL .'";
                    TRUNCATE TABLE products_content;
                    TRUNCATE TABLE categories_product;
                    TRUNCATE TABLE products_attribute;
                    TRUNCATE TABLE products_item;
                    TRUNCATE TABLE products_item_attribute;
                    TRUNCATE TABLE products_attribute;
                    TRUNCATE TABLE products;
                    TRUNCATE TABLE wishlists;
                    TRUNCATE TABLE products_partner_quantity;
                    TRUNCATE TABLE products_partner_store;
                    DELETE from tags_relation where type = "'. PRODUCT_DETAIL .'";
                    SET FOREIGN_KEY_CHECKS = 1;
                ';
                $conn->execute($query_product);
            }

            if(!empty($data['customer'])){
                $query_customer = 
                ' 
                    SET FOREIGN_KEY_CHECKS = 0;
                    TRUNCATE TABLE customers;
                    TRUNCATE TABLE customers_account;
                    TRUNCATE TABLE customers_address;
                    TRUNCATE TABLE customers_bank;
                    TRUNCATE TABLE wishlists;
                    SET FOREIGN_KEY_CHECKS = 1;
                ';
                $conn->execute($query_customer);
            }

            if(!empty($data['comment'])){
                $query_customer = 
                ' 
                    SET FOREIGN_KEY_CHECKS = 0;
                    TRUNCATE TABLE comments;
                    TRUNCATE TABLE comments_like;
                    SET FOREIGN_KEY_CHECKS = 1;
                ';
                $conn->execute($query_customer);
            }

            if(!empty($data['counter'])){
                $query_customer = 
                ' 
                    SET FOREIGN_KEY_CHECKS = 0;
                    TRUNCATE TABLE counters;
                    TRUNCATE TABLE log_access;
                    SET FOREIGN_KEY_CHECKS = 1;
                ';
                $conn->execute($query_customer);
            }

            if(!empty($data['order'])){
                $query_order = 
                ' 
                    SET FOREIGN_KEY_CHECKS = 0;
                    TRUNCATE TABLE payments;
                    TRUNCATE TABLE shippings;
                    TRUNCATE TABLE orders_contact;
                    TRUNCATE TABLE orders_item;
                    TRUNCATE TABLE orders;
                    SET FOREIGN_KEY_CHECKS = 1;
                ';
                $conn->execute($query_order);
            }

            if(!empty($data['attribute'])){
                $query_attribute = 
                ' 
                    SET FOREIGN_KEY_CHECKS = 0;
                    TRUNCATE TABLE attributes_options_content;
                    TRUNCATE TABLE attributes_options;
                    TRUNCATE TABLE attributes_content;
                    TRUNCATE TABLE attributes;

                    TRUNCATE TABLE categories_attribute;
                    TRUNCATE TABLE articles_attribute;
                    TRUNCATE TABLE products_attribute;

                    SET FOREIGN_KEY_CHECKS = 1;
                ';
                $conn->execute($query_attribute);
            }

            if(!empty($data['contact'])){
                $query_contact = 
                ' 
                    SET FOREIGN_KEY_CHECKS = 0;
                    TRUNCATE TABLE contacts;
                    SET FOREIGN_KEY_CHECKS = 1;
                ';
                $conn->execute($query_contact);
            }

            if(!empty($data['notification'])){
                $query_contact = 
                ' 
                    SET FOREIGN_KEY_CHECKS = 0;
                    TRUNCATE TABLE notifications;
                    TRUNCATE TABLE notifications_sent;
                    TRUNCATE TABLE notifications_subscribe;
                    SET FOREIGN_KEY_CHECKS = 1;
                ';
                $conn->execute($query_contact);
            }

            if(!empty($data['nhnotification'])){
                $query_contact = 
                ' 
                    SET FOREIGN_KEY_CHECKS = 0;
                    TRUNCATE TABLE nh_notifications;
                    SET FOREIGN_KEY_CHECKS = 1;
                ';
                $conn->execute($query_contact);
            }

            if(!empty($data['promotion'])){
                $query_contact = 
                ' 
                    SET FOREIGN_KEY_CHECKS = 0;
                    TRUNCATE TABLE promotions;
                    TRUNCATE TABLE promotions_coupon;
                    SET FOREIGN_KEY_CHECKS = 1;
                ';
                $conn->execute($query_contact);
            }

            if(!empty($data['point'])){
                $query_contact = 
                ' 
                    SET FOREIGN_KEY_CHECKS = 0;
                    TRUNCATE TABLE customers_point;
                    TRUNCATE TABLE customers_point_history;
                    TRUNCATE TABLE customers_point_tick;
                    TRUNCATE TABLE customers_point_tomoney;
                    SET FOREIGN_KEY_CHECKS = 1;
                ';
                $conn->execute($query_contact);
            }

            if(!empty($data['affiliate'])){
                $query_contact = 
                ' 
                    SET FOREIGN_KEY_CHECKS = 0;
                    TRUNCATE TABLE customers_affiliate;
                    TRUNCATE TABLE customers_affiliate_order;
                    TRUNCATE TABLE customers_affiliate_request;
                    SET FOREIGN_KEY_CHECKS = 1;
                ';
                $conn->execute($query_contact);
            }

            if(!empty($data['logs'])){
                $query_contact = 
                ' 
                    SET FOREIGN_KEY_CHECKS = 0;
                    TRUNCATE TABLE logs;
                    SET FOREIGN_KEY_CHECKS = 1;
                ';
                $conn->execute($query_contact);
            }

            $this->responseJson([
                CODE => SUCCESS,
                MESSAGE => __d('admin', 'xoa_du_lieu_thanh_cong')
            ]);

            $conn->commit();

            TableRegistry::get('App')->deleteAllCache();
        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }  
    }

    public function changeMode()
    {   
        // chỉ cho phép tài khoản root vào chức năng này
        $user = $this->Auth->user();
        if(empty($user['supper_admin'])){
            $this->showErrorPage('denied');
        }
        
        $group = 'website_mode';
        $website_mode = TableRegistry::get('Settings')->getSettingByGroup($group); 

        $this->set('group', $group);
        $this->set('website_mode', $website_mode);
        $this->set('title_for_layout', __d('admin', 'doi_che_do_website'));

        $this->js_page = [
            '/assets/js/pages/setting_change_mode.js'
        ];

        $this->set('path_menu', 'setting');
        $this->render('change_mode');
    }

    public function recaptcha()
    {
        $group = 'recaptcha';
        $recaptcha = TableRegistry::get('Settings')->getSettingByGroup($group);       

        $this->set('group', $group);
        $this->set('recaptcha', $recaptcha);

        $this->js_page = [
            '/assets/js/pages/setting_recaptcha.js'
        ];

        $this->set('title_for_layout', 'reCAPTCHA v3');
        $this->set('path_menu', 'setting');
        $this->render('recaptcha');
    }

    public function product()
    {
        $group = 'product';
        $product = TableRegistry::get('Settings')->getSettingByGroup($group);
     
        $this->set('group', $group);
        $this->set('product', $product);
        $this->set('title_for_layout', __d('admin', 'cau_hinh_san_pham'));

        $this->js_page = [
            '/assets/js/pages/setting_product.js'
        ];

        $this->set('path_menu', 'setting');
        $this->render('product');
    }

    public function affiliate()
    {
        $group = 'affiliate';
        $affiliate = TableRegistry::get('Settings')->getSettingByGroup($group);
        $commissions = !empty($affiliate['commissions']) ? json_decode($affiliate['commissions'], true) : [];
     
        $this->set('group', $group);
        $this->set('affiliate', $affiliate);
        $this->set('commissions', $commissions);
        $this->set('title_for_layout', __d('admin', 'cau_hinh_chuong_trinh_affiliate'));

        $this->js_page = [
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/js/pages/setting_affiliate.js'
        ];

        $this->set('path_menu', 'setting');
        $this->render('affiliate');
    }

    public function order()
    {
        $group = 'order';
        $order = TableRegistry::get('Settings')->getSettingByGroup($group);

        $this->set('group', $group);
        $this->set('order', $order);
        $this->set('title_for_layout', __d('admin', 'cau_hinh_don_hang'));

        $this->js_page = [
            '/assets/js/pages/setting_product.js'
        ];

        $this->set('path_menu', 'setting');
        $this->render('order');
    }

    public function point()
    {
        $point = TableRegistry::get('Settings')->getSettingByGroup('point');
        $attendance = TableRegistry::get('Settings')->getSettingByGroup('attendance');
        
        $attendance['point_config'] = !empty($attendance['point_config']) ? explode(",", $attendance['point_config']) : [];

        $this->set('attendance', $attendance);
        $this->set('point', $point);
        $this->set('title_for_layout', __d('admin', 'cau_hinh_tich_diem'));

        $this->js_page = [
            '/assets/js/pages/setting_point.js'
        ];

        $this->set('path_menu', 'setting');
        $this->render('point');
    }

    public function social()
    {
        $group = 'social';
        $social = TableRegistry::get('Settings')->getSettingByGroup($group);
     
        $this->set('group', $group);
        $this->set('social', $social);
        $this->set('title_for_layout', __d('admin', 'cau_hinh_mang_xa_hoi'));

        $this->js_page = [
            '/assets/js/pages/setting_social.js'
        ];

        $this->set('path_menu', 'setting');
        $this->render('social');
    }

    public function customer()
    {
        $group = 'customer';
        $customer = TableRegistry::get('Settings')->getSettingByGroup($group);
     
        $this->set('group', $group);
        $this->set('customer', $customer);
        $this->set('title_for_layout', __d('admin', 'cau_hinh_khach_hang'));

        $this->js_page = [
            '/assets/js/pages/setting_customer.js'
        ];

        $this->set('path_menu', 'setting');
        $this->render('customer');
    }

    public function api()
    {
        $group = 'api';
        $api = TableRegistry::get('Settings')->getSettingByGroup($group);

        $this->set('group', $group);
        $this->set('api', $api);
        $this->set('title_for_layout', __d('admin', 'thiet_lap_thong_tin_api'));

        $this->js_page = [
            '/assets/js/pages/setting_api.js'
        ];

        $this->set('path_menu', 'setting');
        $this->render('api');
    }

    public function approved()
    {
        $approved_product = TableRegistry::get('Settings')->getSettingByGroup('approved_product');
        if(!empty($approved_product['role_id'])){
            $approved_product['role_id'] = explode('|', $approved_product['role_id']);
        }
        $approved_article = TableRegistry::get('Settings')->getSettingByGroup('approved_article');
        if(!empty($approved_article['role_id'])){
            $approved_article['role_id'] = explode('|', $approved_article['role_id']);
        }

        $roles = TableRegistry::get('Roles')->queryListRoles([FIELD => LIST_INFO])->toArray();
        $roles = Hash::combine($roles, '{n}.id', '{n}.name');

        $this->set('approved_product', $approved_product);
        $this->set('approved_article', $approved_article);
        $this->set('roles', $roles);

        $this->set('title_for_layout', __d('admin', 'thiet_lap_quyen_duyet_bai_viet'));

        $this->js_page = [
            '/assets/js/pages/setting_approved.js'
        ];

        $this->set('path_menu', 'setting');
        $this->render('approved');
    }

    public function plugin()
    {
        // chỉ cho phép tài khoản root vào chức năng này
        $user = $this->Auth->user();
        if(empty($user['supper_admin'])){
            $this->showErrorPage('denied');
        }
        
        $this->js_page = '/assets/js/pages/plugin.js';

        $this->set('path_menu', 'mobile_app');
        $this->set('title_for_layout', __d('admin', 'danh_sach_plugin'));
    }

    public function pluginJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Plugins');
        $utilities = $this->loadComponent('Utilities');

        $data = $params = $block = [];

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

        // params         
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;


        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        try {
            $plugin = $this->paginate($table->queryListPlugin($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        } catch (Exception $e) {
            $page = 1;
            $plugin = $this->paginate($table->queryListPlugin($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Plugins']) ? $this->request->getAttribute('paging')['Plugins'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $plugin, 
            META => $meta_info
        ]);
    }

    public function changeStatusPlugin()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];
        $status = !empty($data['status']) ? intval($data['status']) : 0;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Plugins');

        $plugins = $table->find()->where([
            'Plugins.id IN' => $ids
        ])->select(['Plugins.id', 'Plugins.status'])->toArray();
        
        if(empty($plugins)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_plugin')]);
        }

        $patch_data = [];
        foreach ($ids as $k => $plugins_id) {
            $patch_data[] = [
                'id' => $plugins_id,
                'status' => $status,
                'draft' => 0
            ];
        }
        
        $data_entities = $table->patchEntities($plugins, $patch_data);
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $change_status = $table->saveMany($data_entities);            
            if (empty($change_status)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_trang_thai_san_pham_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function smsBrandname()
    {
        $sms_brandname = TableRegistry::get('Settings')->getSettingByGroup('sms_brandname');

        $sms_brandname['fpt_telecom'] = !empty($sms_brandname['fpt_telecom']) ? json_decode($sms_brandname['fpt_telecom'], true) : [];
        $sms_brandname['esms'] = !empty($sms_brandname['esms']) ? json_decode($sms_brandname['esms'], true) : [];

        $this->js_page = [
            '/assets/js/pages/sms_brandname.js'
        ];

        $this->set('sms_brandname', $sms_brandname);
        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'sms_brandname'));

    }
    
    public function saveFptTelecom()
    {
        $group = 'sms_brandname';
        $code = 'fpt_telecom';

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];        
        if (!$this->getRequest()->is('post') || empty($data[$code])) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Settings');        

        $setting = $table->find()->where([
            'group_setting' => $group, 
            'code' => $code
        ])->first();

        $data_save = [
            'group_setting' => $group,
            'code' => $code,
            'value' => json_encode($data[$code])
        ];
        
        if(!empty($setting)){
            $entity = $table->patchEntity($setting, $data_save);
        }else{
            $entity = $table->newEntity($data_save);
        }

        try{
            $save = $table->save($entity);

            $this->responseJson([CODE => SUCCESS]);
        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    public function saveEsms()
    {
        $group = 'sms_brandname';
        $code = 'esms';

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];        
        if (!$this->getRequest()->is('post') || empty($data[$code])) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Settings');        

        $setting = $table->find()->where([
            'group_setting' => $group, 
            'code' => $code
        ])->first();

        $data_save = [
            'group_setting' => $group,
            'code' => $code,
            'value' => json_encode($data[$code])
        ];
        
        if(!empty($setting)){
            $entity = $table->patchEntity($setting, $data_save);
        }else{
            $entity = $table->newEntity($data_save);
        }

        try{
            $save = $table->save($entity);

            $this->responseJson([CODE => SUCCESS]);
        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    public function notification()
    {
        $group = 'notification';
        $notification = TableRegistry::get('Settings')->getSettingByGroup($group);

        $this->js_page = [
            '/assets/js/pages/setting_notification.js'
        ];

        $this->set('group', $group);
        $this->set('notification', $notification);
        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'gui_thong_bao'));

    }

    public function sendMessages()
    {
        $group = 'send_message';
        $send_message = TableRegistry::get('Settings')->getSettingByGroup($group);
        $slack = !empty($send_message['slack']) ? json_decode($send_message['slack'], true) : [];
        $telegram = !empty($send_message['telegram']) ? json_decode($send_message['telegram'], true) : [];
        $apply = !empty($send_message['apply']) ? json_decode($send_message['apply'], true) : [];

        $this->js_page = [
            '/assets/js/pages/send_message.js'
        ];

        $this->set('group', $group);
        $this->set('slack', $slack);
        $this->set('telegram', $telegram);
        $this->set('apply', $apply);
        $this->set('title_for_layout', __d('admin', 'thiet_lap_thong_tin_api'));
        $this->set('path_menu', 'setting');
    }

    public function cdnPath()
    {
        $table = TableRegistry::get('Settings');

        $group = 'profile';
        $profile = $table->find()->where([
            'group_setting' => $group
        ])->toArray();

        $profile = Hash::combine($profile, '{n}.code', '{n}.value');

        $this->set('profile', $profile);
        $this->set('group', $group);

        $this->js_page = [
            '/assets/js/pages/setting_cdn.js'
        ];

        $this->set('title_for_layout', __d('admin', 'duong_dan_cdn'));
        $this->set('path_menu', 'setting');
    }

    public function adminPath()
    {
        $table = TableRegistry::get('Settings');

        $group = 'profile';
        $profile = $table->find()->where([
            'group_setting' => $group
        ])->toArray();

        $profile = Hash::combine($profile, '{n}.code', '{n}.value');

        $this->set('profile', $profile);
        $this->set('group', $group);

        $this->js_page = [
            '/assets/js/pages/setting_admin_path.js'
        ];

        $this->set('title_for_layout', __d('admin', 'duong_dan_dang_nhap_admin'));
        $this->set('path_menu', 'setting');
    }

    public function language()
    {
        $table = TableRegistry::get('Settings');

        $group = 'language';
        $setting = $table->find()->where([
            'group_setting' => $group
        ])->toArray();

        $setting = Hash::combine($setting, '{n}.code', '{n}.value');

        $this->set('setting', $setting);
        $this->set('group', $group);

        $this->js_page = [
            '/assets/js/pages/setting_language.js'
        ];

        $this->set('title_for_layout', __d('admin', 'ngon_ngu'));
        $this->set('path_menu', 'setting');
    }

    public function replaceSearchUnicode()
    {
        $this->set('title_for_layout', 'Cập nhật Search Unicode');
        $this->set('path_menu', 'setting');
    }

    public function attribute()
    {
        $table = TableRegistry::get('Settings');

        $group = 'attribute';
        $setting = $table->find()->where([
            'group_setting' => $group
        ])->toArray();

        $setting = Hash::combine($setting, '{n}.code', '{n}.value');

        $this->set('setting', $setting);
        $this->set('group', $group);

        $this->set('title_for_layout', __d('admin', 'thuoc_tinh_mo_rong'));
        $this->set('path_menu', 'setting');
    }

    public function embedAttribute($type = null)
    {
        if(!in_array($type, [ARTICLE, PRODUCT])){
            $this->showErrorPage();
        }

        $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($this->lang), '{n}.id', '{n}', '{n}.attribute_type');
        $attributes = !empty($all_attributes[$type]) ? $all_attributes[$type] : [];
        $attributes = Hash::combine($attributes, '{n}.code', '{n}.name');

        $path_template = TableRegistry::get('Templates')->getPathTemplate();
        $folder = new Folder($path_template . 'embed_attribute', false);
        $list_views = $folder->find('.*\.tpl', true);
        $views = [];
        if(!empty($list_views)){
            foreach($list_views as $view){
                $views[$view] = $view;
            }
        }

        $group = 'attribute_' . $type;        
        $setting = TableRegistry::get('Settings')->find()->where([
            'group_setting' => $group
        ])->toArray();

        $setting = Hash::combine($setting, '{n}.code', '{n}.value');

        $this->set('attributes', $attributes);
        $this->set('views', $views);
        $this->set('setting', $setting);
        $this->set('group', $group);

        $this->js_page = [
            '/assets/js/pages/setting_embed_attribute.js'
        ];

        $title_for_layout = __d('admin', 'ma_nhung_thuoc_tinh_bai_viet');
        if($type == PRODUCT) $title_for_layout = __d('admin', 'ma_nhung_thuoc_tinh_san_pham');
        $this->set('title_for_layout', $title_for_layout);
        $this->set('type', $type);
        $this->set('path_menu', 'setting');
        $this->render('embed_attribute');
    }
    
    public function replaceContent()
    {
        $this->js_page = [
            '/assets/js/pages/setting_replace_content.js'
        ];
        $this->set('title_for_layout', __d('admin', 'thay_the_noi_dung'));
        $this->set('path_menu', 'setting');
    }   
    public function replaceContentSave()
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $types = !empty($data['type']) ? $data['type'] : [];
        if (empty($types)) {
            $this->responseJson([MESSAGE => __d('admin', 'chua_chon_noi_dung_can_thay_the')]);
        }

        $find = !empty($data['find']) ? $data['find'] : null;
        $replace = !empty($data['replace']) ? $data['replace'] : null;
        if (empty($find) || empty($replace)) {
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_thong_tin')]);
        }

        $find = htmlentities($find, ENT_QUOTES, "UTF-8");
        $replace = htmlentities($replace, ENT_QUOTES, "UTF-8");

        foreach ($types as $type) {
            $replace_content = $this->_replaceContent($type, $find, $replace);
            if(!empty($replace_content[CODE]) && $replace_content[CODE] == ERROR ) {
                $this->responseJson([MESSAGE => __d('admin', 'thay_the_noi_dung_{0}_khong_thanh_cong',[$type]) ]);
            }
        }
        
        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'thay_the_noi_dung_thanh_cong')
        ]);
    }

    private function _replaceContent($type = null, $find = null, $replace = null)
    {
        if(empty($type) || empty($find) || empty($replace) || !in_array($type, Configure::read('LIST_REPLACE_CONTENT'))) return [];

        if (!empty($type == 'category')) $table = TableRegistry::get('CategoriesContent');
        if (!empty($type == 'product')) $table = TableRegistry::get('ProductsContent');
        if (!empty($type == 'article')) $table = TableRegistry::get('ArticlesContent');
        if (!empty($type == 'brand')) $table = TableRegistry::get('BrandsContent');

        if(empty($table)) return [];

        $limit = 50;
        for ($page = 1; $page <= 10000; $page++) { 
            try{
                $records = $this->paginate($table->find()->where()->select(['id', 'content']), [
                    'limit' => $limit,
                    'page' => $page
                ])->toArray();

                if(empty($records)) break;

            }catch (Exception $e) {
                break;
            }
            
            $data_update = [];
            foreach($records as $info) {
                $id = !empty($info['id']) ? intval($info['id']) : null;
                $content = !empty($info['content']) ? $info['content'] : null;
                if(empty($id) || empty($content) || strpos($content, $find) === false) continue;
                $data_update[] = [
                    'id' => $id,
                    'content' => str_replace($find, $replace, $content)
                ];
            }
            if(empty($data_update)) continue;
            // cập nhật dữ liệu
            $entities = $table->patchEntities($records, $data_update, ['validate' => false]);
            
            try{
                $save = $table->saveMany($entities);

                if (empty($save)) throw new Exception();
                
            }catch (Exception $e) {
                $this->responseJson([MESSAGE => __d('admin', 'thay_the_noi_dung_{0}_khong_thanh_cong', [$type])]);
                break;
            }
        }
    }

    public function translateLocale()
    {
        $languages = TableRegistry::get('Languages')->find()->where(['deleted' => 0])->select(['code', 'name'])->toList();
        $languages = !empty($languages) ? Hash::combine($languages, '{n}.code', '{n}.name') : [];

        $this->js_page = [
            '/assets/js/pages/setting_translate_locale.js?v=' . time()
        ];

        $this->set('languages', $languages);
        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', 'Dịch tệp đa ngôn ngữ');
    }

    public function translateLocaleProcess()
    {   
        set_time_limit(0);
        $this->layout = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $data = $this->getRequest()->getData();
        $lang_to = !empty($data['lang_to']) ? $data['lang_to'] : null;
        $lang_from = !empty($data['lang_from']) ? $data['lang_from'] : null;
        $type = !empty($data['type']) ? $data['type'] : null;
        $file_to = !empty($data['file_to']) ? $data['file_to'] : null;
        $file_from = !empty($data['file_from']) ? $data['file_from'] : null;
        
        $file = ['file_to' => $file_to , 'file_from' => $file_from ];
        
        if(empty($lang_to) && empty($lang_from)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        if(empty($type)) $this->responseJson([MESSAGE => __d('admin', 'chua_chon_kieu_tep')]);
        
        if($type == 'js'){
            $this->_translateJs($lang_to, $lang_from, $file, $type);            
        }elseif($type == 'po'){
            $this->_translatePo($lang_to, $lang_from, $file, $type);
        }else{
            $this->_translateBlock($lang_to, $lang_from, $file_from, $type);
        } 
    }

    private function _translatePo($lang_to = null, $lang_from = null, $file = null, $type = null)
    {   
        set_time_limit(0);
        if(empty($lang_to) && empty($lang_from)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_ngon_ngu_can_dich')]);
        }
        
        if (!empty($file['file_from']) && $file['file_from']->getError() === UPLOAD_ERR_OK) {
            $name_file_from = $file['file_from']->getClientFilename();
        
            // Lấy đuôi file từ tên file
            $file_extension_from = pathinfo($name_file_from, PATHINFO_EXTENSION);
            if ($file_extension_from != $type) $this->responseJson([MESSAGE => __d('admin', 'vui_long_chon_dung_dinh_dang_tep')]);

            $file_translate = $file['file_from']->getStream()->getMetadata('uri');
        }else{
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_file_can_dich')]);
        }

        // lấy nội dung file dịch
        $content = @file_get_contents($file_translate);
        if ($content === false) $this->responseJson([MESSAGE => __d('admin', 'khong_the_doc_noi_dung_tep_tin')]);

        $split_content = !empty($content) ? array_filter(explode(PHP_EOL, $content)) : [];
        if(empty($split_content)) $this->responseJson([MESSAGE => 'file_dich_khong_co_noi_dung']);

        // lấy nội dung file dịch sang đã có
        if (!empty($file['file_to']) && $file['file_to']->getError() === UPLOAD_ERR_OK) {
            $name_file_to = $file['file_to']->getClientFilename();
        
            // Lấy đuôi file từ tên file
            $file_extension_to = pathinfo($name_file_to, PATHINFO_EXTENSION);
            if ($file_extension_to != $type) $this->responseJson([MESSAGE => __d('admin', 'vui_long_chon_dung_dinh_dang_tep')]);

            $file_translated = $file['file_to']->getStream()->getMetadata('uri');
        }

        $split_translated = [];
        if(!empty($file_translated)){
            $content_translated = @file_get_contents($file_translated);
            $split_translated = !empty($content_translated) ? array_filter(explode(PHP_EOL, $content_translated)) : [];
        }

        // tạo folder chứa file dịch
        if(!file_exists(TMP . 'locales_translate')) mkdir(TMP . 'locales_translate', 0755);
        
        // lấy thông tin file dịch ngôn ngữ
        $file_dir = TMP . 'locales_translate' . DS . $lang_from . '.po';
        
        $file_translate_object = fopen($file_dir, 'w') or $this->responseJson([MESSAGE => __d('admin', 'khong_tao_duoc_file_dich')]);
        
        // Lấy ds key đã dịch ở file cũ
        $translated = [];
        if(!empty($split_translated)){
            $i = 0;
            foreach($split_translated as $val){
                // Bỏ qua các dòng trống hoặc chỉ chứa khoảng trắng
                if (trim($val) === '') continue;
                
                $even = $i % 2 == 0 ? true : false;

                if($even){
                    $key = $this->_getKeyLocalePo($val);

                    $translated[$key] = '';
                }else{
                    $value = $this->_getMsgLocalePo($val);
                    
                    $last_key = array_key_last($translated);
                    $translated[$last_key] = $value;                    
                }

                $i++;
            }
        }

        // kiểm tra key dịch
        $i = 0;
        $origin = [];
        $list_translate = [];
        foreach($split_content as $val){
            // Bỏ qua các dòng trống hoặc chỉ chứa khoảng trắng
            if (trim($val) === '') continue;

            $even = $i % 2 == 0 ? true : false;
            
            if($even){
                $key = $this->_getKeyLocalePo($val);
                $origin[$key] = '';
            }else{
                $value = $this->_getMsgLocalePo($val);

                $last_key = array_key_last($origin);
                $origin[$last_key] = $value;

                // nếu key chưa dịch thì add
                if(empty($translated[$last_key])) $list_translate[$last_key] = $value;                
            }

            $i++;
        }
        
        if(!empty($list_translate)){
            $result_translated = $this->loadComponent('Admin.Translate')->translate($list_translate, $lang_to, $lang_from);
            if(empty($result_translated)) $this->responseJson([MESSAGE => __d('admin', 'dich_khong_thanh_cong')]);
        }
        $content_write = '';
        foreach($origin as $key => $val){

            $content_write .= "msgid \"$key\"" . PHP_EOL;

            if(!empty($result_translated[$key])){
                $content_write .= "msgstr \"$result_translated[$key]\"" . PHP_EOL;                
            }elseif(!empty($translated[$key])){
                $content_write .= "msgstr \"$translated[$key]\"" . PHP_EOL;
            }else{
                $content_write .= "msgstr \"$key\"" . PHP_EOL;
            }
            
            $content_write .= PHP_EOL;
        }
        
        // ghi file
        @fwrite($file_translate_object, $content_write);

        $this->responseJson([
            CODE => SUCCESS,
            DATA => ['lang'=> $lang_from, 'type' => $type],
            MESSAGE => __d('admin','dich_thanh_cong')
        ]);
    }

    private function _translateJs($lang_to = null, $lang_from = null, $file = null, $type = null)
    {   
        if(empty($lang_to) && empty($lang_from)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_ngon_ngu_can_dich')]);
        }

        if (!empty($file['file_from']) && $file['file_from']->getError() === UPLOAD_ERR_OK) {
            $name_file_from = $file['file_from']->getClientFilename();
        
            // Lấy đuôi file từ tên file
            $file_extension_from = pathinfo($name_file_from, PATHINFO_EXTENSION);
            if ($file_extension_from != $type) $this->responseJson([MESSAGE => __d('admin', 'vui_long_chon_dung_dinh_dang_tep')]);

            $file_translate = $file['file_from']->getStream()->getMetadata('uri');
        }else{
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_file_can_dich')]);
        }

        // lấy nội dung file dịch mặc định
        $content = @file_get_contents($file_translate);
        $content = !empty($content) ? trim(ltrim($content, 'var locales =')) : null;
        if(empty($content)) $this->responseJson([MESSAGE => __d('admin', 'file_dich_khong_co_noi_dung')]);

        $content = ltrim($content, '{');
        $content = rtrim($content, '}');
        $content = trim($content);
        if(empty($content)) $this->responseJson([MESSAGE => __d('admin', 'file_dich_khong_co_noi_dung')]);

        $split_content = !empty($content) ? explode(',', $content) : [];
        
        if(empty($split_content)) $this->responseJson([MESSAGE => __d('admin','file_dich_khong_co_noi_dung')]);
        
        // lấy nội dung file dịch sang đã có
        if (!empty($file['file_to']) && $file['file_to']->getError() === UPLOAD_ERR_OK) {
            $name_file_to = $file['file_to']->getClientFilename();
        
            // Lấy đuôi file từ tên file
            $file_extension_to = pathinfo($name_file_to, PATHINFO_EXTENSION);
            if ($file_extension_to != $type) $this->responseJson([MESSAGE => __d('admin', 'vui_long_chon_dung_dinh_dang_tep')]);

            $file_translated = $file['file_to']->getStream()->getMetadata('uri');
        }

        $split_translated = [];
        if(!empty($file_translated)){
            $content_translated = @file_get_contents($file_translated);
            $content_translated = !empty($content_translated) ? trim(ltrim($content_translated, 'var locales =')) : null;
            if(!empty($content_translated)){
                $content_translated = ltrim($content_translated, '{');
                $content_translated = rtrim($content_translated, '}');
                $content_translated = trim($content_translated);

                $split_translated = !empty($content_translated) ? array_filter(explode(PHP_EOL, $content_translated)) : [];
            }
        }  
        
        // tạo folder chứa file dịch
        if(!file_exists(TMP . 'locales_translate')) mkdir(TMP . 'locales_translate', 0755);
    
        // lấy thông tin file dịch ngôn ngữ
        $file_dir = TMP . 'locales_translate' . DS . $lang_from . '.js';
        
        $file_translate_object = fopen($file_dir, 'w') or $this->responseJson([MESSAGE => __d('admin','khong_tao_duoc_file_dich')]);

        // Lấy ds key đã dịch ở file cũ
        $translated = [];
        if(!empty($split_translated)){
            foreach($split_translated as $val){
                $split_val = array_filter(explode(':', $val));
                if(empty($split_val) || count($split_val) != 2) continue;

                $key = !empty($split_val[0]) ? trim($split_val[0]) : null;
                $value = !empty($split_val[1]) ? trim($split_val[1]) : null;
                if(empty($key) || empty($value)) continue;               

                $key = str_replace('\'', '', $key);
                $key = trim(rtrim($key, ','));

                $value = str_replace('\'', '', $value);
                $value = trim(rtrim($value, ','));

                $translated[$key] = $value; 
            }
        }

        // kiểm tra key dịch
        $origin = [];
        $list_translate = [];
        foreach($split_content as $val){
            $split_val = array_filter(explode(':', $val));
            if(empty($split_val) || count($split_val) != 2) continue;

            $key = !empty($split_val[0]) ? trim($split_val[0]) : null;
            $value = !empty($split_val[1]) ? trim($split_val[1]) : null;
            if(empty($key) || empty($value)) continue;

            $key = str_replace('\'', '', $key);
            $key = trim(rtrim($key, ','));

            $value = str_replace('\'', '', $value);
            $value = trim(rtrim($value, ','));

            $origin[$key] = $value; 

            // nếu key chưa dịch thì add
            if(empty($translated[$key])) $list_translate[$key] = $value;
        }


        if(!empty($list_translate)){
            $result_translated = $this->loadComponent('Admin.Translate')->translate($list_translate, $lang_to, $lang_from);
            if(empty($result_translated)) $this->responseJson([MESSAGE => __d('admin','dich_thanh_cong')]);
        }
        
        if(empty($origin)) $this->responseJson([MESSAGE => __d('admin','khong_lay_duoc_noi_dung_dich')]);

        $content_write = 'var locales = {' . PHP_EOL;
        foreach($origin as $key => $val){
            if(!empty($result_translated[$key])){
                $content_write .= $key . ": '" . $result_translated[$key] . "'," . PHP_EOL;
            }elseif(!empty($translated[$key])){
                $content_write .= $key . ": '" . $translated[$key] . "'," . PHP_EOL;
            }else{
                $content_write .= $key . ": '" . $key . "'," . PHP_EOL;
            }
        }

        $content_write .= '}';

        // ghi file
        $write = @fwrite($file_translate_object, $content_write);

        $this->responseJson([
            CODE => SUCCESS,
            DATA => ['lang'=> $lang_from, 'type' => $type],
            MESSAGE => __d('admin','dich_thanh_cong')
        ]);
    }

    private function _translateBlock($lang_to = null, $lang_from = null, $file_from = null)
    {   
        if(empty($lang_to) || empty($lang_from) || empty($type) || empty($file_from)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        if(empty($file_from)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_file_can_dich')]);
        }
        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin','dich_thanh_cong')
        ]);
    }

    private function _getKeyLocalePo($str = '')
    {
        if(empty($str)) return '';
        
        $str = str_replace('msgid', '', $str);
        $str = trim($str);

        if(empty($str)) return '';

        $str = rtrim($str, '"');
        $str = ltrim($str, '"');

        return $str;
        
    }

    private function _getMsgLocalePo($str = '')
    {
        if(empty($str)) return '';
        
        $str = str_replace('msgstr', '', $str);        
        $str = trim($str);
        
        if(empty($str)) return '';

        $str = rtrim($str, '"');
        $str = ltrim($str, '"');

        $str = str_replace('"', '\'', $str);
        return $str;

    }

    public function downloadFile()
    {
        $file_sql = new File(TMP . 'locales_translate', false);
        if(empty($file_sql->exists())){
            $this->responseJson([MESSAGE => __d('admin', 'khong_ton_tai_file_locales_translate')]);
        }

        return $this->getResponse()->withFile(TMP . 'export/data.zip', [
            'download' => true,
            'name' => 'data.zip',
        ]);
    }

    public function localesSystem()
    {
        $this->js_page = [
            // '/assets/js/pages/locales_system.js'
        ];
        $this->set('title_for_layout', __d('admin', 'ngon_ngu_he_thong'));
        $this->set('path_menu', 'locales_system');
        
    }   

}