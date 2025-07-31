<?php
namespace App\Service;

use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Exception\Exception;
use Cake\Core\Configure;

class CategoryService extends AppService
{
    public function updateCategory($category_id = null, $data = [], $type = '', $lang = '')
    {
        $utilities = TableRegistry::get('Utilities');
        $table = TableRegistry::get('Categories');

        $category_id = !empty($category_id) ? intval($category_id) : null;
        if(empty($lang)) $lang = TableRegistry::get('Languages')->getDefaultLanguage();
        
        if(empty($data) || !is_array($data) || empty($lang)) {
            return $utilities->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        if(empty($type) || !in_array($type, Configure::read('LIST_TYPE_CATEGORY'))){
            return $utilities->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $create_new = true;

        // kiểm tra thông tin danh mục
        if(!empty($category_id)){
            $create_new = false;
            
            $category_info = $table->getDetailCategory($type, $category_id, $lang, [
                'get_attributes' => true
            ]);
            if(empty($category_info)){
                return $utilities->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }
        }


        $data_save = [];        

        // $parent_id       
        if(isset($data['parent_id'])){            
            $parent_id = !empty($data['parent_id']) ? intval($data['parent_id']) : null;        
            
            // lấy thông tin danh mục cha            
            if(!empty($parent_id)){
                $parent_info = $table->find()->where([
                    'id' => $parent_id, 
                    'type' => $type,
                    'deleted' => 0
                ])->select(['id', 'path_id'])->first();
                $parent_path_id = !empty($parent_info['path_id']) ? $parent_info['path_id'] : '';

                if(empty($parent_info)) $parent_id = null;
            }

            // $path_id
            $path_id = '';
            if(!empty($parent_path_id)){
                $path_id = $parent_path_id . $parent_id . '|';
            }else{
                $path_id = '|' . $parent_id . '|';
            }

            // nếu $parent_id bằng $category_id thì bỏ qua
            if($parent_id == $category_id) {
                $parent_id = null;
                $path_id = '';
            }

            $data_save['parent_id'] = $parent_id;
            $data_save['path_id'] = $path_id;
        }

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
        }

        // type_video
        if(isset($data['type_video'])){
            $data_save['type_video'] = !empty($data['type_video']) && is_string($data['type_video']) ? $data['type_video'] : null;
            
            // nếu url_video rỗng thì type_video cũng rỗng
            if(isset($data_save['url_video']) && empty($data_save['url_video'])) $data_save['type_video'] = null;            
            if(!isset($data_save['url_video']) && empty($category_info['url_video'])) $data_save['type_video'] = null;
        }

        // position
        if(isset($data['position'])){
            $data_save['position'] = !empty($data['position']) ? intval($data['position']) : 0;
        }        

        // status
        if(isset($data['status'])){
            $status = !empty($data['status']) ? intval($data['status']) : 0;
            if(!in_array($status, [0, 1])) $status = 1;

            $data_save['status'] = $status;
        }        

        // name
        if(isset($data['name'])){
            $name = !empty($data['name']) ? trim(strip_tags($data['name'])) : '';            
            if(empty($name)) {
                return $utilities->getResponse([MESSAGE => __d('admin', 'vui_long_nhap_tieu_de')]);
            }

            $data_save['CategoriesContent']['name'] = $name;
            $data_save['CategoriesContent']['search_unicode'] = strtolower($utilities->formatSearchUnicode([$name]));
        }
        
        // description
        if(isset($data['description'])) $data_save['CategoriesContent']['description'] = !empty($data['description']) ? $data['description'] : '';
        
        // content
        if(isset($data['content'])) $data_save['CategoriesContent']['content'] = !empty($data['content']) ? $data['content'] : '';

        // seo_title
        if(isset($data['seo_title'])) $data_save['CategoriesContent']['seo_title'] = !empty($data['seo_title']) ? $data['seo_title'] : '';

        // seo_description
        if(isset($data['seo_description'])) $data_save['CategoriesContent']['seo_description'] = !empty($data['seo_description']) ? $data['seo_description'] : '';

        // seo_keywords
        if(isset($data['seo_keywords'])) {
            $seo_keywords = '';
            if(!empty($data['seo_keywords']) && is_array($data['seo_keywords'])){
                $seo_keywords = implode(', ', array_filter($data['seo_keywords']));
            }

            $data_save['CategoriesContent']['seo_keyword'] = $seo_keywords;
        }

        // chỉ cập nhật $lang khi có các tham số bên CategoriesContent
        if(isset($data_save['CategoriesContent'])) $data_save['CategoriesContent']['lang'] = $lang;
        
        // link
        if(isset($data['link'])){
            $link = !empty($data['link']) ? $utilities->formatToUrl(trim($data['link'])) : '';
            if(empty($link)){
                return $utilities->getResponse([MESSAGE => __d('admin', 'duong_dan_khong_hop_le')]);
            }

            // tạo link không trùng khi thêm mới link
            if($create_new) $link = TableRegistry::get('Links')->getUrlUnique($link);

            // kiểm tra đường dẫn
            $link_id = !empty($category_info['Links']) ? $category_info['Links']['id'] : null;
            if(TableRegistry::get('Links')->checkExist($link, $link_id)){
                return $utilities->getResponse([MESSAGE => __d('admin', 'duong_dan_da_ton_tai_tren_he_thong')]);
            }

            $type_link = '';
            switch ($type) {
                case PRODUCT:
                    $type_link = CATEGORY_PRODUCT;
                    break;
                
                case ARTICLE:
                    $type_link = CATEGORY_ARTICLE;
                    break;
            }

            $data_save['Links']['url'] = $link;
            $data_save['Links']['type'] = $type_link;
            $data_save['Links']['lang'] = $lang;
        }

        // attributes
        if(isset($data['attributes'])){
            $data_attributes = [];
            if(!empty($data['attributes']) && is_array($data['attributes'])){
                $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($lang), '{n}.id', '{n}', '{n}.attribute_type');
                $all_attributes = !empty($all_attributes[CATEGORY]) ? $all_attributes[CATEGORY] : [];
                foreach($data['attributes'] as $attribute){
                    $attribute_id = !empty($attribute['attribute_id']) ? intval($attribute['attribute_id']) : '';
                    $value = isset($attribute['value']) ? $attribute['value'] : null;
                    if(empty($all_attributes[$attribute_id])) continue;
                    if(is_array($value) || is_object($value)) continue;

                    $data_attributes[] = [
                        'attribute_id' => $attribute_id,
                        'value' => $value
                    ];
                }
            }

            $data_save['CategoriesAttribute'] = $data_attributes;
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

            // type
            $data_save['type'] = $type;
            
            // kiểm tra thông tin
            if(empty($data_save['CategoriesContent']['name'])){
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
                'associated' => ['CategoriesContent', 'Links', 'CategoriesAttribute']
            ]);
        }else{
            $entity = $table->patchEntity($category_info, $data_save);
        }
    
        $conn = ConnectionManager::get('default');       
        try{
            $conn->begin();

            if(isset($data_save['CategoriesAttribute']) && !$create_new){
                TableRegistry::get('CategoriesAttribute')->deleteAll([
                    'category_id' => $category_id
                ]);
            }
            
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
            $position = TableRegistry::get('Categories')->find()->select('id')->max('id');
            $data_save['position'] = $position;
        }

        // seo_title
        if(!isset($data_save['CategoriesContent']['name'])){
            // cập nhật seo_title bằng tên bài viết
            if(isset($data_save['CategoriesContent']['name'])) $data_save['CategoriesContent']['seo_title'] = $data_save['CategoriesContent']['name'];
        }

        // status
        if(!isset($data_save['status'])){
            // mặc định trạng thái 1
            $data_save['status'] = 1;           
        }
        
        return $data_save;
    }

    public function translateAfterCreateNew($category_id = null, $type = '', $lang = '')
    {
        $utilities = TableRegistry::get('Utilities');
            
        $languages = TableRegistry::get('Languages')->getList();
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        if(empty($settings['language']['auto_translate']) || count($languages) <= 1){
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'cap_nhat_thanh_cong')
            ]);
        }

        if(empty($type) || !in_array($type, Configure::read('LIST_TYPE_CATEGORY'))){
            return $utilities->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $category_id = !empty($category_id) ? intval($category_id) : null;
        if(empty($category_id)){
            return $utilities->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        $table = TableRegistry::get('Categories');
        $category_info = $table->getDetailCategory($type, $category_id, $lang, [
            'get_attributes' => true
        ]);
        if(empty($category_info)){
            return $utilities->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }
        
        $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($lang), '{n}.id', '{n}', '{n}.attribute_type');
        $all_attributes = !empty($all_attributes[CATEGORY]) ? Hash::combine($all_attributes[CATEGORY], '{n}.id', '{n}') : [];

        $link = !empty($category_info['Links']['url']) ? $category_info['Links']['url'] : null;
        $data_attribute = !empty($category_info['CategoriesAttribute']) ? $category_info['CategoriesAttribute'] : null;
        
        $name = !empty($category_info['CategoriesContent']['name']) ? $category_info['CategoriesContent']['name'] : '';
        $description = !empty($category_info['CategoriesContent']['description']) ? $category_info['CategoriesContent']['description'] : '';
        $content = !empty($category_info['CategoriesContent']['content']) ? $category_info['CategoriesContent']['content'] : '';
        
        $google_translate = new GoogleTranslateService();
        foreach($languages as $language_code => $language){
            if($language_code == $lang) continue;
            
            // translate title, description and content
            $items = [];
            if(!empty($name)) $items['name'] = $name;

            if(!empty($description) && strlen($description) <= 5000 && !empty($settings['language']['translate_all'])) {
                $items['description'] = $description;
            }

            if(!empty($content) && strlen($content) <= 5000 && !empty($settings['language']['translate_all'])) {
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
                'description' => !empty($translates['description']) ? $translates['description'] : null,
                'content' => !empty($translates['content']) ? $translates['content'] : null,
                'link' => $link_translate,
                'seo_title' => $name_translate
            ];
            
            // translate attribute text richtext and text
            if(!empty($data_attribute) && !empty($settings['language']['translate_all'])){
                foreach($data_attribute as $key => $attribute_item){
                    $attribute_id = !empty($attribute_item['attribute_id']) ? $attribute_item['attribute_id'] : null;
                    $input_type = !empty($all_attributes[$attribute_id]['input_type']) ? $all_attributes[$attribute_id]['input_type'] : null;
                    $code = !empty($all_attributes[$attribute_id]['code']) ? $all_attributes[$attribute_id]['code'] : null;

                    if(empty($code) || empty($attribute_id) || !in_array($input_type, [RICH_TEXT, TEXT])) continue;

                    $value = !empty($attribute_item['value']) && $utilities->isJson($attribute_item['value']) ? json_decode($attribute_item['value'], true) : [];
                    if(empty($value) || empty($value[$lang])) continue;
                    $item_attribute[$code] = $value[$lang];
                    $translates_attribute = $google_translate->translate($item_attribute, $lang, $language_code);

                    $data_translate[$code] = !empty($translates_attribute[$code]) ? $translates_attribute[$code] : '';
                }               
            }

            $save_translate = $this->updateCategory($category_id, $data_translate, $type, $language_code); 
            
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
}
