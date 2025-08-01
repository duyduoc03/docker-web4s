<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use App\Service\CategoryService;
use App\Service\AttributeService;

class CategoryController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    // tách 2 function listCategoryProduct và listJsonCategoryArticle nhằm múc đích phân quyền cho các action này
    public function listCategoryProduct()
    {
        $type = PRODUCT;
        
        $this->js_page = [
            '/assets/js/pages/list_category.js',
            '/assets/plugins/global/lightbox/lightbox.min.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js'
        ];
        $this->set('type', $type);
        $this->set('path_menu', 'category_' . $type);        
        $this->set('title_for_layout', __d('admin', 'danh_muc_san_pham'));
        $this->render('list');
    }

    public function listCategoryArticle()
    {
        $type = ARTICLE;

        $this->js_page = [
            '/assets/js/pages/list_category.js',
            '/assets/plugins/global/lightbox/lightbox.min.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js'
        ];
        $this->set('type', $type);
        $this->set('path_menu', 'category_' . $type);    
        $this->set('title_for_layout', __d('admin', 'danh_muc_bai_viet'));
        $this->render('list');
    }

    public function listJsonCategoryProduct()
    {
        $this->listJson(PRODUCT);
    }

    public function listJsonCategoryArticle()
    {
        $this->listJson(ARTICLE);
    }

    public function listJson($type = null)
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $utilities = $this->loadComponent('Utilities');

        $data = $params = $categories = [];

        $limit = 1000;
        $page = 1;
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        // params query
        $params[QUERY] = !empty($data[QUERY]) ? $data[QUERY] : [];

        // params filter
        $params[FILTER] = !empty($data[DATA_FILTER]) ? $data[DATA_FILTER] : [];
        if(!empty($params[QUERY])){
            $params[FILTER] = array_merge($params[FILTER], $params[QUERY]);
        }
        
        $params[FILTER][TYPE] = $type;
        $params[FILTER][LANG] = $this->lang;

        // params         
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];
        $params['get_user'] = true;
        $params['get_empty_name'] = true;

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;


        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;
        $categories = TableRegistry::get('Categories')->queryListCategories($params)->all()->nest('id', 'parent_id')->toArray();
        
        if(!empty($categories)){
            $categories = TableRegistry::get('Categories')->parseDataCategories($categories, 0);
        }
        
        $categories = !empty($categories) ? array_values($categories) : [];

        // parse data before output
        $result = [];
        if(!empty($categories)){
            $languages = TableRegistry::get('Languages')->getList();
            foreach($categories as $k => $category){
                $result[$k] = $category;
                
                // check multiple language
                $mutiple_language = [];                
                if(!empty($languages)){
                    foreach($languages as $lang => $language){
                        if($lang == $this->lang && !empty($category['name'])){
                            $mutiple_language[$lang] = true;

                        }else{
                            $content = TableRegistry::get('CategoriesContent')->find()->where([
                                'category_id' => !empty($category['id']) ? intval($category['id']) : null,
                                'lang' => $lang
                            ])->select(['name'])->first();
                            
                            $mutiple_language[$lang] = !empty($content['name']) ? true : false;
                        }                        
                    }
                }

                $result[$k]['mutiple_language'] = $mutiple_language;
            }
        }

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
            META => $utilities->formatPaginationInfo()
        ]);
    }

    public function add($type = null)
    {
        if(!in_array($type, Configure::read('LIST_TYPE_CATEGORY'))) {
            $this->showErrorPage();
        }

        $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($this->lang), '{n}.id', '{n}', '{n}.attribute_type');
        $all_attributes = !empty($all_attributes[CATEGORY]) ? $all_attributes[CATEGORY] : [];

        $all_options = [];
        if(!empty($all_attributes)){
            $all_options = Hash::combine(TableRegistry::get('AttributesOptions')->getAll($this->lang), '{n}.id', '{n}.name','{n}.attribute_id');
        }

        $max_record = TableRegistry::get('Categories')->find()->select('id')->max('id');

        $this->css_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/js/seo_analysis.js',
            '/assets/js/pages/category.js'
        ];
        $this->set('type', $type);
        $this->set('all_attributes', $all_attributes);
        $this->set('all_options', $all_options);
        $this->set('position', !empty($max_record->id) ? $max_record->id + 1 : 1);
        
        $this->set('path_menu', 'category_' . $type);
        $this->set('title_for_layout', __d('admin', 'them_danh_muc'));
        $this->render('update');
    }

    public function update($type = null, $id = null)
    {
        if(!in_array($type, Configure::read('LIST_TYPE_CATEGORY'))) {
            $this->showErrorPage();
        }
                
        $category = TableRegistry::get('Categories')->getDetailCategory($type, $id, $this->lang, [
            'get_user' => true,
            'get_attributes' => true
        ]);        

        $category = TableRegistry::get('Categories')->formatDataCategoryDetail($category);
        if(empty($category)) $this->showErrorPage();

        $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($this->lang), '{n}.id', '{n}', '{n}.attribute_type');
        $all_attributes = !empty($all_attributes[CATEGORY]) ? $all_attributes[CATEGORY] : [];

        $all_options = [];
        if(!empty($all_attributes)){
            $all_options = Hash::combine(TableRegistry::get('AttributesOptions')->getAll($this->lang), '{n}.id', '{n}.name','{n}.attribute_id');
        }
        $this->set('path_menu', 'category_' . $type);
        $this->set('id', $id);
        $this->set('all_attributes', $all_attributes);
        $this->set('all_options', $all_options);
        $this->set('position', !empty($category['position']) ? $category['position'] : 1);
        $this->set('category', $category);
        $this->set('type', $type);

        $this->css_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/plugins/diff-match-patch/diff-match-patch.js',
            '/assets/js/seo_analysis.js',
            '/assets/js/pages/category.js',            
            '/assets/js/log_record.js'
        ];

        $this->set('title_for_layout', __d('admin', 'cap_nhat_danh_muc'));
    }

    public function detail($type = null, $id = null)
    {
        if(!in_array($type, Configure::read('LIST_TYPE_CATEGORY'))) {
            $this->showErrorPage();
        }
        
        $table = TableRegistry::get('Categories');
        $category_detail = $table->getDetailCategory($type, $id, $this->lang, [
            'get_user' => true
        ]);

        $category = $table->formatDataCategoryDetail($category_detail);
        if(empty($category)){
            $this->showErrorPage();
        }

        $this->css_page = [
            '/assets/css/pages/wizard/wizard-4.css',
            '/assets/plugins/global/lightbox/lightbox.css'
        ];
        $this->js_page = [
            '/assets/plugins/global/lightbox/lightbox.min.js'
        ];

        $this->set('type', $type);
        $this->set('category', $category);
        $this->set('title_for_layout', __d('admin', 'chi_tiet_danh_muc'));
    }

    public function save($type = null, $id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $utilities = TableRegistry::get('Utilities');

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];    
        if (!$this->getRequest()->is('post') || empty($data) || !in_array($type, Configure::read('LIST_TYPE_CATEGORY'))) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $create_new = !empty($id) ? true : false;

        // định dạng lại data trước khi gọi service
        $data['admin_user_id'] = $this->Auth->user('id');
        $data['images'] = !empty($data['images']) && $utilities->isJson($data['images']) ? json_decode($data['images'], true) : [];
        $data['seo_keywords'] = !empty($data['seo_keyword']) && $utilities->isJson($data['seo_keyword']) ? array_column(json_decode($data['seo_keyword'], true), 'value') : [];
        unset($data['seo_keyword']);

        // attributes
        $attribute_service = new AttributeService();
        $data['attributes'] = $attribute_service->formatAttributesFromDataForm($data, $this->lang, CATEGORY, $id);

        // cập nhật dữ liệu
        $service = new CategoryService();
        $update_result = $service->updateCategory($id, $data, $type, $this->lang);

        if(empty($update_result[CODE]) || $update_result[CODE] == ERROR){
            return $this->responseJson($update_result);
        }        

        $category_id = !empty($update_result[DATA]['id']) ? intval($update_result[DATA]['id']) : null;

        // dịch các ngôn ngữ khác
        if(empty($create_new) && !empty($category_id)){
            $service->translateAfterCreateNew($category_id, $type, $this->lang);
        }
        
        return $this->responseJson([
            CODE => SUCCESS,
            DATA => [
                'id' => $category_id
            ]
        ]);        
    }

    public function rollbackLog()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $record_id = !empty($data['record_id']) ? intval($data['record_id']) : null;
        $version = !empty($data['version']) ? $data['version'] : null;

        $category_type = PRODUCT;
        if (!$this->getRequest()->is('post') || empty($record_id) || empty($version)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $log_record = TableRegistry::get('LogsUtilities')->getLogRecordByVersion(CATEGORY, $record_id, $version);        
        $data_log = !empty($log_record['before_entity']) ? $log_record['before_entity'] : [];
        if(empty($data_log)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        $table = TableRegistry::get('Categories');
    
        $category_info = $table->find()->contain([
            'ContentMutiple', 
            'LinksMutiple',
            'CategoriesAttribute'
        ])->where([
            'Categories.id' => $record_id,
            'Categories.deleted' => 0
        ])->first();

        if(empty($category_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        $entity = $table->patchEntity($category_info, $data_log);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
                 
            $clear_attributes = TableRegistry::get('CategoriesAttribute')->deleteAll(['category_id' => $record_id]);
                    
            $save = $table->save($entity);
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

    public function delete($type = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $ids = !empty($data['ids']) ? $data['ids'] : [];
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($ids) || !is_array($ids) || empty($type)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $type = 'category_' . $type;

        $categories_table = TableRegistry::get('Categories');
        $links_table = TableRegistry::get('Links');

        try{
            foreach($ids as $id){

                // delete category
                $category = $categories_table->get($id);
                $category = $categories_table->patchEntity($category, ['id' => $id, 'deleted' => 1]);
                $delete_category = $categories_table->save($category);
                if (empty($delete_category)){
                    throw new Exception();
                }

                // delete link
                $delete_link = $links_table->updateAll(
                    [  
                        'deleted' => 1
                    ],
                    [  
                        'foreign_id' => $id,
                        'type' => $type
                    ]
                );
                
                //update parent_id and path_id of child category
                $child_categories = $categories_table->getCategoriesChild($id);
                $child_ids = Hash::extract($child_categories, '{n}.id');
                if(!empty($child_ids)){
                    $update_child = $categories_table->updateAll(
                        [  
                            'path_id' => null,
                            'parent_id' => null
                        ],
                        [  
                            'id IN' => $child_ids
                        ]
                    );
                }
            }
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'xoa_du_lieu_thanh_cong')]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function changeStatus()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $ids = !empty($data['ids']) ? $data['ids'] : [];
        $status = !empty($data['status']) ? 1 : 0;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Categories');

        $categories = $table->find()->where([
            'Categories.id IN' => $ids,
            'Categories.deleted' => 0
        ])->select(['Categories.id', 'Categories.status'])->toArray();
        
        if(empty($categories)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_danh_muc')]);
        }

        $patch_data = [];
        foreach ($ids as $k => $category_id) {
            $patch_data[] = [
                'id' => $category_id,
                'status' => $status
            ];
        }
        $data_categories = $table->patchEntities($categories, $patch_data);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $change_status = $table->saveMany($data_categories);
            if (empty($change_status)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function duplicate($type = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($ids) || empty($type) || !is_array($ids) || !in_array($type, Configure::read('LIST_TYPE_CATEGORY'))) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $type = 'category_' . $type;

        $table = TableRegistry::get('Categories');

        $data_dulicate = [];
        foreach($ids as $id){          
            $category = $table->find()->contain([
                'ContentMutiple',
                'LinksMutiple' => function ($q) use ($type) {
                    return $q->where([
                        'LinksMutiple.type' => $type,
                        'LinksMutiple.deleted' => 0
                    ]);
                }
            ])
            ->where([
                'Categories.id' => $id,
                'Categories.deleted' => 0,
            ])->first()->toArray();

            if(empty($category)) continue;


            // format data before mere entity
            unset($category['id']);
            unset($category['created_by']);
            unset($category['created']);
            unset($category['updated']);
            if(!empty($category['ContentMutiple'])){
                foreach($category['ContentMutiple'] as $k_content => $content){
                    $name = $this->getNameUnique($content['name'], 1);
                    $category['ContentMutiple'][$k_content]['name'] = $name;

                    unset($category['ContentMutiple'][$k_content]['id']);
                    unset($category['ContentMutiple'][$k_content]['category_id']);
                }
            }

            if(!empty($category['LinksMutiple'])){
                foreach($category['LinksMutiple'] as $k_link => $link){
                    $category['LinksMutiple'][$k_link]['url'] = $this->getUrlUnique($link['url'], 1);

                    unset($category['LinksMutiple'][$k_link]['id']);
                    unset($category['LinksMutiple'][$k_link]['foreign_id']);
                }
            }

            $data_dulicate[] = $category;
        }

        $category_entities = $table->newEntities($data_dulicate, [
            'associated' => ['ContentMutiple', 'LinksMutiple']
        ]);

        try{
            // save data
            $save = $table->saveMany($category_entities);  
            
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'nhan_ban_du_lieu_thanh_cong')]);
        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }        
    }

    public function changePosition()
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $id = !empty($data['id']) ? intval($data['id']) : null;
        $value = !empty($data['value']) ? $data['value'] : 0;
        $name = !empty($data['name']) ? $data['name'] : '';
        
        // validate data
        if (empty($id) || empty($name)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Categories');

        $data_save['position'] = $value;
        $category = $table->patchEntity($table->get($id), $data_save);

        try{
            // save data
            $save = $table->save($category);

            if (empty($save->id)){
                throw new Exception();
            }
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    public function autoSuggest()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Categories');
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $filter = !empty($data[FILTER]) ? $data[FILTER] : [];
        $filter[STATUS] = 1;
        $filter[TYPE] = PRODUCT;

        $categories = $table->queryListCategories([
            FILTER => $filter,
            FIELD => LIST_INFO,
        ])->limit(10)->toArray();

        $result = [];
        if(!empty($categories)){
            foreach ($categories as $key => $category) {
                $result[] = $table->formatDataCategoryDetail($category);
            }
        }

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
        ]);
    }

    private function getNameUnique($name = null, $index = 1)
    {
        $name_check = $name . ' ('. $index .')';
        if($index == 100){
            return $name_check;
        }   
        $check = TableRegistry::get('Categories')->checkNameExist($name_check);
        
        if($check){
            $index ++;
            $name_check = $this->getNameUnique($name, $index);
        }
        return $name_check;
    }

    private function getUrlUnique($url = null, $index = 1)
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
}