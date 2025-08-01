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
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Cache\Cache;
use Cake\Utility\Text;

class ExtendCollectionController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function list()
    {
        $this->css_page = [
            '/assets/plugins/global/lightbox/lightbox.css',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [            
            '/assets/plugins/global/lightbox/lightbox.min.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/js/pages/list_extend_collection.js'
        ];

        $this->set('path_menu', 'extend');
        $this->set('title_for_layout', __d('admin', 'bang_du_lieu'));
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $data = $params = $extends_collection = [];

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
            $collections = $this->paginate(TableRegistry::get('ExtendsCollection')->queryListExtendCollection($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        } catch (Exception $e) {
            $page = 1;
            $collections = $this->paginate(TableRegistry::get('ExtendsCollection')->queryListExtendCollection($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['ExtendsCollection']) ? $this->request->getAttribute('paging')['ExtendsCollection'] : [];
        $meta_info = TableRegistry::get('Utilities')->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $collections, 
            META => $meta_info
        ]);
    }

    public function add()
    {        
        $this->css_page = [
            '/assets/css/pages/wizard/wizard-1.css',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [            
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/js/pages/extend_collection.js'
        ];

        $this->set('step', 1);
        $this->set('title_for_layout', __d('admin', 'tao_bang_du_lieu'));
        $this->render('update');
    }

    public function update($collection_id = null)
    {
        $step = $this->request->getQuery('step');

        $table = TableRegistry::get('ExtendsCollection');

        $collection_info = $table->find()->where([
            'id' => $collection_id,
            'deleted' => 0
        ])->first();

        if(empty($collection_info)) $this->showErrorPage();
        $fields = !empty($collection_info['fields']) ? json_decode($collection_info['fields'], true) : [];
        $fields = !empty($fields) ? Hash::combine($fields, '{n}.code', '{n}') : [];
        $form_config = !empty($collection_info['form_config']) ? json_decode($collection_info['form_config'], true) : [];

        $collection_info['fields'] = $fields;
        $collection_info['form_config'] = $table->mergeFieldsToFormConfig($form_config, $fields);

        $this->set('id', $collection_id);
        $this->set('collection_info', $collection_info);        
        $this->set('step', !empty($step) ? intval($step) : 1);
        
        $this->css_page = [            
            '/assets/css/pages/wizard/wizard-1.css',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [            
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/js/pages/extend_collection.js'
        ];


        $this->set('title_for_layout', __d('admin', 'bang_du_lieu'));
        $this->render('update');
    }

    public function save($id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $utilities = TableRegistry::get('Utilities');
        $table = TableRegistry::get('ExtendsCollection');

        $name = !empty($data['name']) ? $data['name'] : null;
        $code = !empty($data['code']) ? strtolower(Text::slug(strtolower($data['code']), '')) : null;
        $description = !empty($data['description']) ? $data['description'] : null;

        $fields = !empty($data['fields']) ? $data['fields'] : [];      

        if(empty($name) || empty($code) || empty($fields)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $exist_name = $table->checkNameExist($name, $id);
        if($exist_name) $this->responseJson([MESSAGE => __d('admin', 'ten_ban_ghi_da_ton_tai')]);

        $exist_code = $table->checkCodeExist($code, $id);
        if($exist_code) $this->responseJson([MESSAGE => __d('admin', 'ma_ban_ghi_da_ton_tai')]);
       
        $exist_field_view = false;
        foreach($fields as $k => $field){

            $field_view = !empty($field['view']) ? $field['view'] : null;
            $input_type = !empty($field['input_type']) ? $field['input_type'] : null;
            $options = !empty($field['options']) ? json_decode($field['options'], true) : [];

            if(empty($input_type)) continue;

            if (!empty($field['view'])) {
                $exist_field_view = true;
            }
           
            if(!in_array($input_type, [SINGLE_SELECT, MULTIPLE_SELECT])) $fields[$k]['options'] = null;
            if(!in_array($input_type, [TEXT, RICH_TEXT])) $fields[$k]['mutiple_language'] = null;
            if($input_type == RICH_TEXT) $fields[$k]['view'] = null;

            if(empty($options)) continue;

            $options_format = [];
            foreach($options as $option){
                $value = !empty($option['value']) ? $option['value'] : null;
                if(empty($value)) continue;

                $option_code = strtolower(Text::slug($value, '_'));
                $options_format[$option_code] = $value;
            }
            $fields[$k]['options'] = $options_format;
        }

        // validate
        if ($exist_field_view == false) {
            $this->responseJson([MESSAGE => __d('admin', 'bat_buoc_lua_chon_it_nhat_mot_truong_hien_thi_danh_sach')]);
        }
        $data_save = [
            'name' => $name,
            'code' => $code,
            'description' => $description,
            'fields' => !empty($fields) ? json_encode($fields) : null,
            'search_unicode' => strtolower($utilities->formatSearchUnicode([$name]))
        ];

        if(empty($id)) {
            $data_save['status'] = 2;
            
            $entity = $table->newEntity($data_save);
        }else{
            $collection_info = $table->find()->where(['id' => $id])->first();
            if(empty($collection_info)){
                $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }

            $entity = $table->patchEntity($collection_info, $data_save);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([
                CODE => SUCCESS, 
                DATA => [
                    'id' => $save->id
                ]
            ]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    public function saveFormConfig($id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post') || empty($data) || empty($id)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        $form_config = !empty($data['config']) ? json_decode($data['config'], true) : [];
        if(empty($form_config['rows'])) $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);


        // kiểm tra xem có field nào đc cài đặt 2 lần
        $exist = false;
        $list_fields = [];

        foreach($form_config['rows'] as $row){
            $columns = !empty($row['columns']) ? $row['columns'] : [];
            if(empty($columns)) continue;

            foreach($columns as $column){
                $fields = !empty($column['field']) ? $column['field'] : [];
                if(empty($fields)) continue;

                foreach($fields as $field){
                    if(in_array($field, $list_fields)) {
                        $exist = true;
                    }else{
                        $list_fields[] =$field;
                    }                    
                }
            }
        }
        
        if($exist) $this->responseJson([MESSAGE => __d('admin', 'khong_the_cau_hinh_1_truong_nhieu_lan_tren_form')]);

        $table = TableRegistry::get('ExtendsCollection');

        $collection_info = $table->find()->where(['id' => $id])->first();
        if(empty($collection_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }
        
        $data_save = [
            'form_config' => !empty($form_config) ? json_encode($form_config) : null,
            'status' => 1
        ];
        $entity = $table->patchEntity($collection_info, $data_save);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([
                CODE => SUCCESS, 
                DATA => [
                    'id' => $save->id
                ]
            ]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    public function changeStatus()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];
        $status = !empty($data['status']) ? 1 : 0;

        if (!$this->getRequest()->is('post') || empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('ExtendsCollection');

        $records = $table->find()->where([
            'id IN' => $ids
        ])->select(['id', 'status'])->toArray();
        
        if(empty($records)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_ban_ghi')]);
        }

        $patch_data = [];
        foreach ($ids as $k => $collection_id) {
            $patch_data[] = [
                'id' => $collection_id,
                'status' => $status
            ];
        }

        $entities = $table->patchEntities($records, $patch_data, ['validate' => false]);
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $change_status = $table->saveMany($entities);
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
    
    public function delete()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];

        if (!$this->getRequest()->is('post') || empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('ExtendsCollection');

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach($ids as $id){
                $collection_info = $table->get($id);
                if (empty($collection_info)) {
                    throw new Exception(__d('admin', 'khong_lay_duoc_thong_tin_bang_du_lieu'));
                }

                $delete = $table->delete($collection_info);
                if (empty($delete)){
                    throw new Exception();
                }

                TableRegistry::get('ExtendsRecord')->deleteAll(['collection_id' => $id]);
                TableRegistry::get('Extends')->deleteAll(['collection_id' => $id]);
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'xoa_du_lieu_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }
}