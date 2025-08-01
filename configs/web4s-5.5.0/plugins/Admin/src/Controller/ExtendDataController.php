<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Text;

class ExtendDataController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function list($collection_code = null)
    {
        if(empty($collection_code)) $this->showErrorPage();
        $table = TableRegistry::get('ExtendsCollection');
        $collection_info = TableRegistry::get('ExtendsCollection')->find()->where([
            'code' => $collection_code,
            'deleted' => 0
        ])->select(['id', 'name', 'code', 'fields', 'form_config'])->first();
        $name = !empty($collection_info['name']) ? $collection_info['name'] : null;
        $fields = !empty($collection_info['fields']) ? json_decode($collection_info['fields'], true) : [];
        if(empty($fields)) $this->showErrorPage();
       
        $list_input_type = [];
        $this->css_page = [
            '/assets/plugins/global/lightbox/lightbox.css',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [
            '/assets/plugins/global/lightbox/lightbox.min.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/js/pages/list_extend_data.js',
        ];

        $this->set('code', $collection_code);
        $this->set('fields', json_encode($fields));

        $this->set('path_menu', 'extend_data_' . $collection_code);
        $this->set('title_for_layout', __d('admin', 'du_lieu_mo_rong') .": ". $name);    
    }

    public function listJson($collection_code = null)
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        // get info collection
        $collection_info = TableRegistry::get('ExtendsCollection')->find()->where([
            'code' => $collection_code,
            'deleted' => 0
        ])->select(['id', 'code', 'fields'])->first();
        $collection_id = !empty($collection_info['id']) ? intval($collection_info['id']) : null;
        $collection_fields = !empty($collection_info['fields']) ? json_decode($collection_info['fields'], true) : [];
        $collection_fields = !empty($collection_fields) ? Hash::combine($collection_fields, '{n}.code', '{n}') : [];
        if(empty($collection_id) || empty($collection_fields)) $this->responseJson([CODE => SUCCESS]);
        
        // field show
        $fields_show = TableRegistry::get('ExtendsCollection')->getFieldsShowInList($collection_fields);
        if(empty($fields_show)) $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);

        $table = TableRegistry::get('ExtendsRecord');
        

        // params filter
        $filter = !empty($data[DATA_FILTER]) ? $data[DATA_FILTER] : [];
        if(!empty($data[QUERY])) $filter = array_merge($filter, $data[QUERY]);

        // sort 
        $sort_field = !empty($data[SORT][FIELD]) ? $data[SORT][FIELD] : null;
        $sort_type = !empty($data[SORT][SORT]) ? $data[SORT][SORT] : null;
        
        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;


        $params = [
            FILTER => $filter,
            SORT => !empty($data[SORT]) ? $data[SORT] : []
        ];

        $query = $table->queryListExtendRecord($collection_id, $params);

        try {            
            $records = $this->paginate($query, [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toList();

        } catch (Exception $e) {
            $page = 1;
            $records = $this->paginate($query, [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toList();
        }

        // parse data before output
        $result = [];
        $check_multiple_lang = true;
        $result = !empty($records) ? TableRegistry::get('ExtendsRecord')->formatDataRecord($collection_id, $records, $this->lang, $check_multiple_lang) : [];
        
        $pagination_info = !empty($this->request->getAttribute('paging')['ExtendsRecord']) ? $this->request->getAttribute('paging')['ExtendsRecord'] : [];
        $meta_info = TableRegistry::get('Utilities')->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
            META => $meta_info
        ]);

    }

    public function add($collection_code = null)
    {
        if(empty($collection_code)) $this->showErrorPage();

        $collection_info = TableRegistry::get('ExtendsCollection')->find()->where([
            'code' => $collection_code,
            'deleted' => 0
        ])->select(['id', 'code', 'fields', 'form_config'])->first();

        $collection_id = !empty($collection_info['id']) ? intval($collection_info['id']) : null;
        $fields = !empty($collection_info['fields']) ? json_decode($collection_info['fields'], true) : [];
        $fields = !empty($fields) ? Hash::combine($fields, '{n}.code', '{n}') : [];
        $form_config = !empty($collection_info['form_config']) ? json_decode($collection_info['form_config'], true) : [];
        if(empty($collection_id) || empty($fields) || empty($form_config)) $this->showErrorPage();

        $form_config = TableRegistry::get('ExtendsCollection')->mergeFieldsToFormConfig($form_config, $fields);

        $this->set('code', $collection_code);
        $this->set('fields', $fields);
        $this->set('form_config', $form_config);

        $this->css_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/js/pages/extend_data.js'
        ];

        $this->set('path_menu', 'extend_data_' . $collection_code);
        $this->set('title_for_layout', __d('admin', 'du_lieu_mo_rong'));
        $this->render('update');
    }

    public function update($collection_code = null, $id = null)
    {
        if(empty($collection_code) || empty($id)) $this->showErrorPage();        

        // collection info
        $collection_info = TableRegistry::get('ExtendsCollection')->find()->where([
            'code' => $collection_code,
            'deleted' => 0
        ])->select(['id', 'code', 'fields', 'form_config'])->first();

        $collection_id = !empty($collection_info['id']) ? intval($collection_info['id']) : null;
        $fields = !empty($collection_info['fields']) ? json_decode($collection_info['fields'], true) : [];
        $fields = !empty($fields) ? Hash::combine($fields, '{n}.code', '{n}') : [];
        $form_config = !empty($collection_info['form_config']) ? json_decode($collection_info['form_config'], true) : [];
        if(empty($collection_id) || empty($fields) || empty($form_config)) $this->showErrorPage();

        // record info
        $lang = $this->lang;
        $record_info = TableRegistry::get('ExtendsRecord')->find()->contain([
            'Extends' => function ($q) use ($lang) {
                return $q->where([
                    'Extends.lang IN' => [$lang, 'all']
                ]);
            }
        ])->where([
            'ExtendsRecord.id' => $id,
            'ExtendsRecord.collection_id' => $collection_id,
        ])->first();
        
        $extends = !empty($record_info['Extends']) ? $record_info['Extends'] : [];
        if(empty($record_info)) $this->showErrorPage();

        $record = [
            'id' => !empty($record_info['id']) ? $record_info['id'] : null,
            'status' => !empty($record_info['status']) ? 1 : 0
        ];

        foreach($extends as $extend){
            $field = !empty($extend['field']) ? $extend['field'] : null;
            $value = !empty($extend['value']) ? $extend['value'] : null;
            if(empty($field)) continue;

            $record[$field] = $value;
        }

        // set data to fields
        $form_config = TableRegistry::get('ExtendsCollection')->mergeFieldsToFormConfig($form_config, $fields, $record);

        $this->set('id', $id);
        $this->set('code', $collection_code);
        $this->set('fields', $fields);
        $this->set('form_config', $form_config);
        $this->set('form_config', $form_config);

        $this->css_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/js/pages/extend_data.js'
        ];

        $this->set('path_menu', 'extend_data_' . $collection_code);
        $this->set('title_for_layout', __d('admin', 'du_lieu_mo_rong'));
        $this->render('update');
    }

    public function save($collection_code = null, $id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);

        $data = $this->getRequest()->getData();

        $collection_info = TableRegistry::get('ExtendsCollection')->find()->where([
            'code' => $collection_code,
            'status' => 1,
            'deleted' => 0
        ])->select(['id', 'code', 'fields'])->first();
        $collection_id = !empty($collection_info['id']) ? intval($collection_info['id']) : null;
        $fields = !empty($collection_info['fields']) ? json_decode($collection_info['fields'], true) : [];
        $fields = !empty($fields) ? Hash::combine($fields, '{n}.code', '{n}') : [];
        if(empty($collection_id) || empty($fields)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_bang_du_lieu')]);
        }

        $table = TableRegistry::get('ExtendsRecord');

        $record_info = $extends = [];
        if(!empty($id)){
            $lang = $this->lang;
            $record_info = $table->find()->contain([
                'Extends' => function ($q) use ($lang) {
                    return $q->where([
                        'Extends.lang IN' => [$lang, 'all']
                    ]);
                }
            ])->where(['id' => $id])->first();

            $extends = !empty($record_info['Extends']) ? $record_info['Extends'] : [];
            $extends = Hash::combine($extends, '{n}.field', '{n}.id');
            if(empty($record_info) ) {
                $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }
            
        }
        
        // validate
        foreach($fields as $field){
            $field_code = !empty($field['code']) ? $field['code'] : null;
            $field_name = !empty($field['name']) ? $field['name'] : null;
            $input_type = !empty($field['input_type']) ? $field['input_type'] : null;

            if(empty($field_code)) continue;

            $required = !empty($field['required']) ? true : false;

            // không check required switch_input
            if($input_type == SWITCH_INPUT) $required = false;

            if($input_type == VIDEO && empty($data[$field_code]['url'])) {
                $this->responseJson([MESSAGE => __d('admin', 'truong_{0}_khong_duoc_de_trong', [$field_name])]);
            }

            if($required && empty($data[$field_code])) {
                $this->responseJson([MESSAGE => __d('admin', 'truong_{0}_khong_duoc_de_trong', [$field_name])]);
            }
        }

        $data_extends = [];
        $arr_unicode = [];
        foreach($data as $field => $value){
            $field_info = !empty($fields[$field]) ? $fields[$field] : [];
            $input_type = !empty($field_info['input_type']) ? $field_info['input_type'] : null;
        
            if(empty($field_info)) continue;

            if(gettype($value) == 'array') $value = json_encode($value);
            $item = [
                'id' => !empty($extends[$field]) ? $extends[$field] : null,
                'collection_id' => $collection_id,
                'field' => $field,                
                'value' => $value,
                'lang' => !empty($field_info['multiple_language']) ? $this->lang : 'all'
            ];
            $data_extends[] = $item;

            if($input_type == TEXT) $arr_unicode[] = $value;
        }

        if(empty($data_extends)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }            

        $data_save = [
            'search_unicode' => strtolower(TableRegistry::get('Utilities')->formatSearchUnicode($arr_unicode)),
            'Extends' => $data_extends
        ];
 
        if(!empty($id)){            
            $entity = $table->patchEntity($record_info, $data_save);
        }else{
            $data_save['collection_id'] = $collection_id;
            $data_save['status'] = 1;

            $max_position = $table->find()->select('position')->max('position');
            $position = !empty($max_position['position']) ? intval($max_position['position']) : 0;
            $data_save['position'] = $position + 1;

            $entity = $table->newEntity($data_save, [
                'associated' => ['Extends']
            ]);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, DATA => $save]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }        
    }
    
    public function delete($collection_code = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];

        if (!$this->getRequest()->is('post') || empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $collection_info = TableRegistry::get('ExtendsCollection')->find()->where([
            'code' => $collection_code,
            'status' => 1,
            'deleted' => 0
        ])->select(['id'])->first();
        $collection_id = !empty($collection_info['id']) ? intval($collection_info['id']) : null;
        if(empty($collection_id)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_bang_du_lieu')]);
        }

        $table = TableRegistry::get('ExtendsRecord');        

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach($ids as $id){
                $record = $table->find()->where(['id' => $id])->first();
                if (empty($record)) {
                    throw new Exception(__d('admin', 'khong_tim_thay_thong_tin_ban_ghi'));
                }

                $delete = $table->delete($record);
                if (empty($delete)){
                    throw new Exception();
                }

                TableRegistry::get('Extends')->deleteAll(['record_id' => $id]);
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'xoa_du_lieu_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function changeStatus($collection_code = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];
        $status = !empty($data['status']) ? 1 : 0;

        if (!$this->getRequest()->is('post') || empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $collection_info = TableRegistry::get('ExtendsCollection')->find()->where([
            'code' => $collection_code,
            'status' => 1,
            'deleted' => 0
        ])->select(['id'])->first();
        $collection_id = !empty($collection_info['id']) ? intval($collection_info['id']) : null;
        if(empty($collection_id)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_bang_du_lieu')]);
        }

        $table = TableRegistry::get('ExtendsRecord');

        $records = $table->find()->where([
            'id IN' => $ids
        ])->select(['id', 'status'])->toArray();
        
        if(empty($records)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_ban_ghi')]);
        }

        $patch_data = [];
        foreach ($ids as $k => $record_id) {
            $patch_data[] = [
                'id' => $record_id,
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

    public function changePosition($collection_code = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $id = !empty($data['id']) ? intval($data['id']) : null;
        $value = !empty($data['value']) ? $data['value'] : 0;

        if(!$this->getRequest()->is('post') || empty($id)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $collection_info = TableRegistry::get('ExtendsCollection')->find()->where([
            'code' => $collection_code,
            'status' => 1,
            'deleted' => 0
        ])->select(['id'])->first();
        $collection_id = !empty($collection_info['id']) ? intval($collection_info['id']) : null;
        if(empty($collection_id)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_bang_du_lieu')]);
        }

        $table = TableRegistry::get('ExtendsRecord');
        $record_info = $table->get($id);
        if(empty($record_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        $entity = $table->patchEntity($record_info, ['position' => $value], ['validate' => false]);
        $update = $table->save($entity);
        if (empty($update->id)){
            $this->responseJson([MESSAGE => __d('admin', 'cap_nhat_khong_thanh_cong')]);
        }

        $this->responseJson([CODE => SUCCESS, DATA => ['id' => $update->id]]);
    }
}