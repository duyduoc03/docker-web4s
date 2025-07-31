<?php
namespace App\Service;

use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Exception\Exception;
use App\Service\GoogleTranslateService;

class BrandService extends AppService
{

    public function updateBrand($brand_id = null, $data = [], $lang = '')
    {
        $utilities = TableRegistry::get('Utilities');
        $table = TableRegistry::get('Brands');

        if(empty($lang)) $lang = TableRegistry::get('Languages')->getDefaultLanguage();
        $brand_id = !empty($brand_id) ? intval($brand_id) : null;        
        if(empty($data) || !is_array($data) || empty($lang)) {
            return $utilities->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $create_new = true;

        // kiểm tra thông tin bài viết
        if(!empty($brand_id)){
            $create_new = false;
            
            $brand_info = $table->getDetailBrand($brand_id, $lang);
            if(empty($brand_info)){
                return $utilities->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }
        }

        $data_save = [];        

        // $image_avatar       
        if(isset($data['image_avatar'])){           
            if (empty($data['image_avatar']) || !is_string($data['image_avatar']) || strpos($data['image_avatar'], '/media/') !== 0) $data['image_avatar'] = '';
            $data_save['image_avatar'] = $data['image_avatar'];
        }
        
        // album ảnh
        if(isset($data['images'])){
            if(empty($data['images']) || !is_array($data['images'])) $data['images'] = [];

            // loại bỏ đường dẫn không hợp lệ
            foreach($data['images'] as $k => $image){
                if(empty($image) || !is_string($image) || strpos($image, '/media/') !== 0) unset($data['images'][$k]);
            }
            $data['images'] = @array_values($data['images']);

            $data_save['images'] = !empty($data['images']) ? json_encode($data['images']) : null;
            $data_save['has_album'] = !empty($data_save['images']) ? 1 : 0;
        }

        // files
        if(isset($data['files'])){
            if(empty($data['files']) || !is_array($data['files'])) $data['files'] = [];

            // loại bỏ đường dẫn không hợp lệ
            foreach($data['files'] as $k => $file){
                if(empty($file) || !is_string($file) || strpos($file, '/media/') !== 0) unset($data['files'][$k]);
            }
            $data['files'] = @array_values($data['files']);

            $data_save['files'] = !empty($data['files']) ? json_encode($data['files']) : null;
            $data_save['has_file'] = !empty($data_save['files']) ? 1 : 0;
        }

        // video
        if(isset($data['url_video'])){
            $data_save['url_video'] = !empty($data['url_video']) && is_string($data['url_video']) ? $data['url_video'] : '';
            $data_save['has_video'] = !empty($data_save['url_video']) ? 1 : 0;
        }

        // type_video
        if(isset($data['type_video'])){
            $data_save['type_video'] = !empty($data['type_video']) && is_string($data['type_video']) ? $data['type_video'] : null;
            
            // nếu url_video rỗng thì type_video cũng rỗng
            if(isset($data_save['url_video']) && empty($data_save['url_video'])) $data_save['type_video'] = null;            
            if(!isset($data_save['url_video']) && empty($brand_info['url_video'])) $data_save['type_video'] = null;
        }

        // position
        if(isset($data['position'])){
            $data_save['position'] = !empty($data['position']) ? intval($data['position']) : 0;
        }

        // status
        if(isset($data['status'])){
            $status = !empty($data['status']) ? intval($data['status']) : 0;
            if(!in_array($status, [0, 1])) $status = 0;

            $data_save['status'] = $status;
        }        

        // name
        if(isset($data['name'])){
            $name = !empty($data['name']) ? trim(strip_tags($data['name'])) : '';            
            if(empty($name)) {
                return $utilities->getResponse([MESSAGE => __d('admin', 'vui_long_nhap_tieu_de')]);
            }

            $data_save['BrandsContent']['name'] = $name;
            $data_save['BrandsContent']['search_unicode'] = strtolower($utilities->formatSearchUnicode([$name]));
        }
        
        // content
        if(isset($data['content'])) $data_save['BrandsContent']['content'] = !empty($data['content']) ? $data['content'] : '';

        // seo_title
        if(isset($data['seo_title'])) $data_save['BrandsContent']['seo_title'] = !empty($data['seo_title']) ? $data['seo_title'] : '';

        // seo_description
        if(isset($data['seo_description'])) $data_save['BrandsContent']['seo_description'] = !empty($data['seo_description']) ? $data['seo_description'] : '';

        // seo_keywords
        if(isset($data['seo_keywords'])) {
            $seo_keywords = '';
            if(!empty($data['seo_keywords']) && is_array($data['seo_keywords'])){
                $seo_keywords = implode(', ', array_filter($data['seo_keywords']));
            }

            $data_save['BrandsContent']['seo_keyword'] = $seo_keywords;
        }

        // chỉ cập nhật $lang khi có các tham số bên BrandsContent
        if(isset($data_save['BrandsContent'])) $data_save['BrandsContent']['lang'] = $lang;
        
        // link
        if(isset($data['link'])){
            $link = !empty($data['link']) ? $utilities->formatToUrl(trim($data['link'])) : '';
            if(empty($link)){
                return $utilities->getResponse([MESSAGE => __d('admin', 'duong_dan_khong_hop_le')]);
            }

            // tạo link không trùng khi thêm mới link
            if($create_new) $link = TableRegistry::get('Links')->getUrlUnique($link);

            // kiểm tra đường dẫn
            $link_id = !empty($brand_info['Links']) ? $brand_info['Links']['id'] : null;
            if(TableRegistry::get('Links')->checkExist($link, $link_id)){
                return $utilities->getResponse([MESSAGE => __d('admin', 'duong_dan_da_ton_tai_tren_he_thong')]);
            }

            $data_save['Links']['url'] = $link;
            $data_save['Links']['type'] = BRAND_DETAIL;
            $data_save['Links']['lang'] = $lang;
        }

        //admin_user_id
        if(isset($data['admin_user_id']) && $create_new){
            $admin_user_id = !empty($data['admin_user_id']) ? intval($data['admin_user_id']) : null;
            $user_info = TableRegistry::get('Users')->getDetailUsers($admin_user_id);

            $data_save['created_by'] = !empty($user_info) ? $admin_user_id : null;
        }

        // nếu thêm mới thì kiểm tra các thông tin bắt buộc
        // và đặt các giá trị mặc định khi tạo mới
        if($create_new){
            // kiểm tra thông tin
            if(empty($data_save['BrandsContent']['name'])){
                return $utilities->getResponse([MESSAGE => __d('admin', 'vui_long_nhap_tieu_de')]);
            }

            if(empty($data_save['Links']['url'])){
                return $utilities->getResponse([MESSAGE => __d('admin', 'duong_dan_khong_hop_le')]);
            }

            // đặt giá trị mặc định
            $data_save = $this->_setDefaultValueBeforeCreateNew($data_save);
        }    

        if(empty($data_save)){
            return $utilities->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        if($create_new){            
            $entity = $table->newEntity($data_save, [
                'associated' => ['BrandsContent', 'Links']
            ]);
        }else{
            $entity = $table->patchEntity($brand_info, $data_save);
        }
    
        $conn = ConnectionManager::get('default');       
        try{
            $conn->begin();
            
            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();            

            return $utilities->getResponse([
                CODE => SUCCESS,
                MESSAGE => __d('admin', 'cap_nhat_thanh_cong'),
                DATA => [
                    'id' => $save->id
                ]
            ]);

        }catch (Exception $e) {
            $conn->rollback();

            return $utilities->getResponse([
                MESSAGE => __d('admin', 'cap_nhat_khong_thanh_cong')
            ]);
        }

    }

    private function _setDefaultValueBeforeCreateNew($data_save = [])
    {
        if(empty($data_save) || !is_array($data_save)) return [];

        // position
        if(!isset($data_save['position'])){
            $position = TableRegistry::get('Brands')->find()->select('id')->max('id');
            $data_save['position'] = $position;
        }

        // seo_title
        if(!isset($data_save['BrandsContent']['name'])){
            // cập nhật seo_title bằng tên bài viết
            if(isset($data_save['BrandsContent']['name'])) $data_save['BrandsContent']['seo_title'] = $data_save['BrandsContent']['name'];
        }

        // status
        if(!isset($data_save['status'])){
            // mặc định trạng thái 1
            $data_save['status'] = 1;         
        }

        return $data_save;
    }

    // chỉ dùng để dịch các bản ghi sau khi vừa tạo mới
    public function translateAfterCreateNew($brand_id = null, $lang = '')
    {
        $utilities = TableRegistry::get('Utilities');
            
        $languages = TableRegistry::get('Languages')->getList();
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        if(empty($settings['language']['auto_translate']) || count($languages) <= 1){
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'cap_nhat_thanh_cong')
            ]);
        }

        $brand_id = !empty($brand_id) ? intval($brand_id) : null;
        if(empty($brand_id)){
            return $utilities->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        $table = TableRegistry::get('Brands');
        $brand_info = $table->getDetailBrand($brand_id, $lang);
        if(empty($brand_info)){
            return $utilities->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }
               
        $link = !empty($brand_info['Links']['url']) ? $brand_info['Links']['url'] : null;
        
        $name = !empty($brand_info['BrandsContent']['name']) ? $brand_info['BrandsContent']['name'] : '';
        $content = !empty($brand_info['BrandsContent']['content']) ? $brand_info['BrandsContent']['content'] : '';
        
        $google_translate = new GoogleTranslateService();
        foreach($languages as $language_code => $language){
            if($language_code == $lang) continue;
            
            // translate title, description and content
            $items = [];
            if(!empty($name)) $items['name'] = $name;           

            if(!empty($content) && strlen($content) <= 5000 && !empty($setting_language['translate_all'])) {
                $items['content'] = $content;
            }
            if(empty($items)) continue;
            $translates = !empty($items) ? $google_translate->translate($items, $lang, $language_code) : [];

            $name_translate = !empty($translates['name']) ? $translates['name'] : $name;
            
            // link translate
            $link_translate = $utilities->formatToUrl($name_translate);
            if(empty($link_translate)) continue;

            $link_translate = TableRegistry::get('Links')->getUrlUnique($link_translate);
            if($link_translate == $link) $link_translate .= '-1';

            $data_translate = [
                'name' => $name_translate,
                'content' => !empty($translates['content']) ? $translates['content'] : null,
                'link' => $link_translate,
                'seo_title' => $name_translate
            ];            

            $save_translate = $this->updateBrand($brand_id, $data_translate, $language_code);             
            if(empty($save_translate[CODE]) || $save_translate[CODE] == ERROR){
                $message = !empty($save_translate[MESSAGE]) ? $save_translate[MESSAGE] . ' - ' . $language : __d('admin', 'cap_nhat_khong_thanh_cong');
                return $utilities->getResponse([MESSAGE => $message]);
            }            
        }
        
        return $utilities->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'cap_nhat_thanh_cong')
        ]);
    }

    public function save($id = null, $data = [], $lang = null, $user_id = null)
    {   
        if(empty($lang)) $lang = TableRegistry::get('Languages')->getDefaultLanguage();
        if (empty($data) || empty($lang) || empty($user_id)) {
            return $this->responseData([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $user_admin_id = !empty($data['user_admin_id']) ? $data['user_admin_id'] : 100000;

        $utilities = TableRegistry::get('Utilities');
        $table = TableRegistry::get('Brands');

        if(!empty($id)){
            $brand = $table->getDetailBrand($id, $lang, [
                'get_user' => false
            ]);

            if(empty($brand)){
                return $this->responseData([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }
        }

        // validate data
        if(empty($data['name'])){
            return $this->responseData([MESSAGE => __d('admin', 'vui_long_nhap_tieu_de')]);
        }

        $data_link = [];
        if(!empty($data['link'])){
            $link = !empty($data['link']) ? $utilities->formatToUrl(trim($data['link'])) : null;
            if(empty($link)){
                return $this->responseData([MESSAGE => __d('admin', 'vui_long_nhap_duong_dan')]);
            }

            $link_id = !empty($brand['Links']['id']) ? $brand['Links']['id'] : null;
            if(TableRegistry::get('Links')->checkExist($link, $link_id)){
                return $this->responseData([MESSAGE => __d('admin', 'duong_dan_da_ton_tai_tren_he_thong')]);
            }
        }

        // format data before save
        $list_keyword = !empty($data['seo_keyword']) && $utilities->isJson($data['seo_keyword']) ? array_column(json_decode($data['seo_keyword'], true), 'value') : null;
        $seo_keyword = !empty($list_keyword) ? implode(', ', $list_keyword) : null;

        $url_video = !empty($data['url_video']) ? $data['url_video'] : null;
        $type_video = null;
        if(isset($data['url_video'])){
            $type_video = !empty($data['type_video']) ? $data['type_video'] : null;
        }

        $files = [];
        if(isset($data['files']) && $utilities->isJson($data['files'])){
            foreach (json_decode($data['files'], true) as $key => $file) {
                $files[] = str_replace(CDN_URL , '', $file);
            }
        }

        $status = isset($brand['status']) ? intval($brand['status']) : 1;
        
        if (isset($data['image_avatar'])){
            $data_save['image_avatar'] = $data['image_avatar'];
        }

        if (isset($data['images'])){
            $data_save['images'] = $data['images'];
        }

        if (isset($data['url_video']) && isset($url_video)) {
            $data_save['url_video'] = $url_video;
        }

        if (isset($data['type_video']) && isset($type_video)) {
            $data_save['type_video'] = $type_video;
        }

        if (isset($data['files']) && isset($files)) {
            $data_save['files'] = json_encode($files);
        }

        if (isset($data['position'])){
            $data_save['position'] = intval($data['position']);
        }

        if (isset($data['status']) && isset($status)) {
            $data_save['status'] = $status;
        }

        $name = !empty($data['name']) ? trim(strip_tags($data['name'])) : null;
        $seo_title = !empty($data['seo_title']) ? trim(strip_tags($data['seo_title'])) : null;
        $seo_description = !empty($data['seo_description']) ? trim(strip_tags($data['seo_description'])) : null;
        $content = !empty($data['content']) ? $data['content'] : null;

        if (!empty($name)){
            $data_content['name'] = $name;
            $data_content['search_unicode'] = strtolower($utilities->formatSearchUnicode([$name]));
        }

        if (isset($data['content'])){
            $data_content['content'] = $content;
        }

        if (isset($data['seo_title'])){
            $data_content['seo_title'] = $seo_title;
        }

        if (isset($data['seo_description'])){
            $data_content['seo_description'] = $seo_description;
        }

        if (isset($data['seo_keyword'])){
            $data_content['seo_keyword'] = $seo_keyword;
        }

        $data_content['lang'] = $lang;

        $data_link = [];
        if(!empty($link)){
            $data_link = [
                'type' => BRAND_DETAIL,
                'url' => $link,
                'lang' => $lang,
            ];
        }

        // translate
        $languages = TableRegistry::get('Languages')->getList();

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $setting_language = !empty($settings['language']) ? $settings['language'] : [];
        if(empty($id) && !empty($setting_language['auto_translate']) && count($languages) > 1){
            $data_save['ContentMutiple'][] = $data_content;
            $data_save['LinksMutiple'][] = $data_link;

            foreach($languages as $language_code => $language){
                if($language_code == $lang) continue;
         
                // translate title and content
                $items = [];
                if (!empty($name)) $items['name'] = $name;

                if (!empty($content) && strlen($content) <= 5000 && !empty($settings['language']['translate_all'])) {
                    $items['content'] = $content;
                }

                if(empty($items)) continue;
                $translates = !empty($items) ? $utilities->translate($items, $lang, $language_code) : [];
                 
                $name_translate = !empty($translates['name']) ? $translates['name'] : $name;
                $content_translate = !empty($translates['content']) ? $translates['content'] : null;
                
                // link translate
                $link_translate = $utilities->formatToUrl($name_translate);
                if(empty($link_translate)) continue;

                $link_translate = TableRegistry::get('Links')->getUrlUnique($link_translate);
                if($link_translate == $link) $link_translate .= '-1';

                // set value after translate

                if (!empty($name_translate)){
                    $record_translate['name'] = $name_translate;
                    $record_translate['seo_title'] = $name_translate;
                    $record_translate['search_unicode'] = strtolower($utilities->formatSearchUnicode([$name_translate])); 
                }

                $record_translate['lang'] = $language_code;

                if(!empty($setting_language['translate_all']) && !empty($content_translate)){
                    $record_translate['content'] = $content_translate;
                }
                
                // set data_save
                $data_save['ContentMutiple'][] = $record_translate;
                $data_save['LinksMutiple'][] = [
                    'type' => BRAND_DETAIL,
                    'url' => $link_translate,
                    'lang' => $language_code,
                ];
            }

            $associated = ['ContentMutiple', 'LinksMutiple'];
        }else{
            $associated = ['BrandsContent', 'Links'];

            $data_save['BrandsContent'] = $data_content;
            if(!empty($data_link)){
                $data_save['Links'] = $data_link;
            }
        }
        // merge data with entity 
        if(empty($id)){
            $data_save['created_by'] = $user_admin_id;
            $entity = $table->newEntity($data_save, [
                'associated' => $associated
            ]);
        }else{            
            $entity = $table->patchEntity($brand, $data_save);
        }
        try{
            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            return $this->responseData([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            return $this->responseData([MESSAGE => $e->getMessage()]);  
        }
        
    }
}
