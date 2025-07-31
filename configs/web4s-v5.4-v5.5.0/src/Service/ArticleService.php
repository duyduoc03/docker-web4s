<?php
namespace App\Service;

use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Exception\Exception;
use App\Service\GoogleTranslateService;

class ArticleService extends AppService
{

    public function updateArticle($article_id = null, $data = [], $lang = '')
    {
        $utilities = TableRegistry::get('Utilities');
        $table = TableRegistry::get('Articles');

        if(empty($lang)) $lang = TableRegistry::get('Languages')->getDefaultLanguage();
        $article_id = !empty($article_id) ? intval($article_id) : null;        
        if(empty($data) || !is_array($data) || empty($lang)) {
            return $utilities->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $create_new = true;

        // kiểm tra thông tin bài viết
        if(!empty($article_id)){
            $create_new = false;
            
            $article_info = $table->getDetailArticle($article_id, $lang, [
                'get_categories' => true,
                'get_tags' => true,
                'get_attributes' => true
            ]);

            if(empty($article_info)){
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
            if(!isset($data_save['url_video']) && empty($article_info['url_video'])) $data_save['type_video'] = null;
        }

        // position
        if(isset($data['position'])){
            $data_save['position'] = !empty($data['position']) ? intval($data['position']) : 0;
        }        

        // categories
        if(isset($data['categories'])){
            $all_categories = TableRegistry::get('Categories')->getAll(ARTICLE, $lang);
            $data_categories = [];
            $category_ids = !empty($data['categories']) && is_array($data['categories']) ? array_filter($data['categories']) : [];
            foreach($category_ids as $category_id){
                $category_id = !empty($category_id) ? intval($category_id) : null;
                if(empty($all_categories[$category_id])) continue;

                $data_categories[] = [
                    'article_id' => $article_id,
                    'category_id' => $category_id
                ];
            }

            $data_save['CategoriesArticle'] = $data_categories;
        }

        // main_category_id
        if(isset($data['main_category_id'])){
            $all_categories = TableRegistry::get('Categories')->getAll(ARTICLE, $lang);
            $main_category_id = !empty($data['main_category_id']) ? intval($data['main_category_id']) : null;

            if(empty($all_categories[$main_category_id])) $main_category_id = null;
            // nếu $main_category_id không có trong $categories thì lấy từ $categories đầu tiên
            if(empty($main_category_id) && !empty($data_categories)){
                $main_category_id = !empty($data_categories[0]['category_id']) ? intval($data_categories[0]['category_id']) : null;
            }

            $data_save['main_category_id'] = $main_category_id;
        }
        
        // view 
        if(isset($data['view'])){
            if (is_numeric($data['view'])) {
                $data_save['view'] = intval($data['view']);
            } elseif (is_string($data['view'])) {
                $data_save['view'] = floatval(str_replace(',', '', $data['view']));
            } else {
                $data_save['view'] = null;
            }
        }

        // featured
        if(isset($data['featured'])){
            $data_save['featured'] = !empty($data['featured']) ? 1 : 0;
        }

        // catalogue
        if(isset($data['catalogue'])){
            $data_save['catalogue'] = !empty($data['catalogue']) ? 1 : 0;
        }

        // author_id
        if(isset($data['author_id'])){
            $author_id = !empty($data['author_id']) ? intval($data['author_id']) : null;
            $author_info = TableRegistry::get('Authors')->getDetailAuthor($author_id, $lang);
            $data_save['author_id'] = !empty($author_info) ? $author_id : null;
        }
        
        // seo_score
        if(isset($data['seo_score'])) {
            $data_save['seo_score'] = !empty($data['seo_score']) && is_string($data['seo_score']) ? $data['seo_score'] : '';
        }
       
        // keyword_score
        if(isset($data['keyword_score'])) {
            $data_save['keyword_score'] = !empty($data['keyword_score']) && is_string($data['keyword_score']) ? $data['keyword_score'] : '';
        }

        // draft
        if(isset($data['draft'])){
            $draft = !empty($data['draft']) ? 1 : 0; 
            $data_save['draft'] = $draft;
        }

        // status
        if(isset($data['status'])){
            $status = !empty($data['status']) ? intval($data['status']) : 0;
            if(!in_array($status, [-1, 0, 1, 2])) $status = 0;

            $data_save['status'] = $status;
        }        

        // name
        if(isset($data['name'])){
            $name = !empty($data['name']) ? trim(strip_tags($data['name'])) : '';            
            if(empty($name)) {
                return $utilities->getResponse([MESSAGE => __d('admin', 'vui_long_nhap_tieu_de')]);
            }

            $data_save['ArticlesContent']['name'] = $name;
            $data_save['ArticlesContent']['search_unicode'] = strtolower($utilities->formatSearchUnicode([$name]));
        }
        
        // description
        if(isset($data['description'])) $data_save['ArticlesContent']['description'] = !empty($data['description']) ? $data['description'] : '';
        
        // content
        if(isset($data['content'])) $data_save['ArticlesContent']['content'] = !empty($data['content']) ? $data['content'] : '';

        // seo_title
        if(isset($data['seo_title'])) $data_save['ArticlesContent']['seo_title'] = !empty($data['seo_title']) ? $data['seo_title'] : '';

        // seo_description
        if(isset($data['seo_description'])) $data_save['ArticlesContent']['seo_description'] = !empty($data['seo_description']) ? $data['seo_description'] : '';

        // seo_keywords
        if(isset($data['seo_keywords'])) {
            $seo_keywords = '';
            if(!empty($data['seo_keywords']) && is_array($data['seo_keywords'])){
                $seo_keywords = implode(', ', array_filter($data['seo_keywords']));
            }

            $data_save['ArticlesContent']['seo_keyword'] = $seo_keywords;
        }

        // chỉ cập nhật $lang khi có các tham số bên ArticlesContent
        if(isset($data_save['ArticlesContent'])) $data_save['ArticlesContent']['lang'] = $lang;
        
        // link
        if(isset($data['link'])){
            $link = !empty($data['link']) ? $utilities->formatToUrl(trim($data['link'])) : '';
            if(empty($link)){
                return $utilities->getResponse([MESSAGE => __d('admin', 'duong_dan_khong_hop_le')]);
            }

            // tạo link không trùng khi thêm mới link
            if($create_new) $link = TableRegistry::get('Links')->getUrlUnique($link);

            // kiểm tra đường dẫn
            $link_id = !empty($article_info['Links']) ? $article_info['Links']['id'] : null;
            if(TableRegistry::get('Links')->checkExist($link, $link_id)){
                return $utilities->getResponse([MESSAGE => __d('admin', 'duong_dan_da_ton_tai_tren_he_thong')]);
            }

            $data_save['Links']['url'] = $link;
            $data_save['Links']['type'] = ARTICLE_DETAIL;
            $data_save['Links']['lang'] = $lang;
        }

        // attributes
        if(isset($data['attributes'])){
            $data_attributes = [];
            if(!empty($data['attributes']) && is_array($data['attributes'])){
                $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($lang), '{n}.id', '{n}', '{n}.attribute_type');
                $all_attributes = !empty($all_attributes[ARTICLE]) ? $all_attributes[ARTICLE] : [];
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

            $data_save['ArticlesAttribute'] = $data_attributes;
        }

        // tags
        if(isset($data['tags'])){
            $data_tags = [];
            $tags = !empty($data['tags']) && is_array($data['tags']) ? $data['tags'] : [];

            $tag_service = new TagService();
            foreach ($tags as $tag) {
                $tag_result = $tag_service->getIdOrCreateNewTag($tag, $lang);
                $tag_id = !empty($tag_result[DATA]['id']) ? intval($tag_result[DATA]['id']) : null;
                if(empty($tag_id)) continue;
            
                $data_tags[] = [
                    'type' => ARTICLE_DETAIL,
                    'tag_id' => $tag_id,
                    'foreign_id' => $article_id
                ];
            }

            $data_save['TagsRelation'] = $data_tags;
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
            if(empty($data_save['ArticlesContent']['name'])){
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
                'associated' => ['ArticlesContent', 'Links', 'CategoriesArticle', 'ArticlesAttribute', 'TagsRelation']
            ]);
        }else{
            $entity = $table->patchEntity($article_info, $data_save);
        }
    
        $conn = ConnectionManager::get('default');       
        try{
            $conn->begin();

            if(isset($data_save['CategoriesArticle']) && !$create_new){
                TableRegistry::get('CategoriesArticle')->deleteAll([
                    'article_id' => $article_id
                ]);
            }

            if(isset($data_save['TagsRelation']) && !$create_new){
                TableRegistry::get('TagsRelation')->deleteAll([
                    'foreign_id' => $article_id,
                    'type' => ARTICLE_DETAIL
                ]);
            }

            if(isset($data_save['ArticlesAttribute']) && !$create_new){
                TableRegistry::get('ArticlesAttribute')->deleteAll([
                    'article_id' => $article_id
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
            $position = TableRegistry::get('Articles')->find()->select('id')->max('id');
            $data_save['position'] = $position;
        }

        // seo_title
        if(!isset($data_save['ArticlesContent']['name'])){
            // cập nhật seo_title bằng tên bài viết
            if(isset($data_save['ArticlesContent']['name'])) $data_save['ArticlesContent']['seo_title'] = $data_save['ArticlesContent']['name'];
        }

        // status
        if(!isset($data_save['status'])){
            // mặc định trạng thái 1
            $data_save['status'] = 1;

            // nếu bài nháp thì status -> 0
            $draft = isset($data_save['draft']) ? $data_save['draft'] : 0;
            if(!empty($draft)) $data_save['status'] = 0;
            
            // nếu có cấu hình duyệt bài viết thì trạng thái về chờ duyệt
            $settings = TableRegistry::get('Settings')->getSettingWebsite();
            if(!empty($settings['approved_article']['approved']) && empty($draft)) $data_save['status'] = -1;   
        }

        return $data_save;
    }

    // chỉ dùng để dịch các bản ghi sau khi vừa tạo mới
    public function translateAfterCreateNew($article_id = null, $lang = '')
    {
        $utilities = TableRegistry::get('Utilities');
            
        $languages = TableRegistry::get('Languages')->getList();
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        if(empty($settings['language']['auto_translate']) || count($languages) <= 1){
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'cap_nhat_thanh_cong')
            ]);
        }

        $article_id = !empty($article_id) ? intval($article_id) : null;
        if(empty($article_id)){
            return $utilities->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        $table = TableRegistry::get('Articles');
        $article_info = $table->getDetailArticle($article_id, $lang, [
            'get_attributes' => true
        ]);
        if(empty($article_info)){
            return $utilities->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }
        
        $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($lang), '{n}.id', '{n}', '{n}.attribute_type');
        $all_attributes = !empty($all_attributes[ARTICLE]) ? Hash::combine($all_attributes[ARTICLE], '{n}.id', '{n}') : [];

        $link = !empty($article_info['Links']['url']) ? $article_info['Links']['url'] : null;
        $data_attribute = !empty($article_info['ArticlesAttribute']) ? $article_info['ArticlesAttribute'] : null;
        
        $name = !empty($article_info['ArticlesContent']['name']) ? $article_info['ArticlesContent']['name'] : '';
        $description = !empty($article_info['ArticlesContent']['description']) ? $article_info['ArticlesContent']['description'] : '';
        $content = !empty($article_info['ArticlesContent']['content']) ? $article_info['ArticlesContent']['content'] : '';

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
                'article_id' => $article_id,
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

            $save_translate = $this->updateArticle($article_id, $data_translate, $language_code); 
            
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
