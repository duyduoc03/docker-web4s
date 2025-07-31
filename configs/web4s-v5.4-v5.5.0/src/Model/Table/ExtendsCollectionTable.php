<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;
use Cake\Utility\Text;
use Cake\Utility\Hash;
use Cake\Core\Configure;

class ExtendsCollectionTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('extends_collection');
        $this->setPrimaryKey('id');
    }

    public function queryListExtendCollection($params = []) 
    {
        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $status = isset($filter['status']) && $filter['status'] != '' ? intval($filter['status']) : null;
        
        // fields select
        switch($field){
            case FULL_INFO:
                $fields = ['ExtendsCollection.id', 'ExtendsCollection.name', 'ExtendsCollection.code', 'ExtendsCollection.description', 'ExtendsCollection.fields', 'ExtendsCollection.form_config', 'ExtendsCollection.status'];
            break;

            case LIST_INFO:
                $fields = ['ExtendsCollection.id', 'ExtendsCollection.name'];
            break;

            case SIMPLE_INFO:
            default:
                $fields = ['ExtendsCollection.id', 'ExtendsCollection.name', 'ExtendsCollection.code', 'ExtendsCollection.status'];
            break;
        }

        $contain = [];
        $where = ['ExtendsCollection.deleted' => 0];
        
        // filter by conditions  
        if(!empty($keyword)){
            $where['ExtendsCollection.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!is_null($status)){
            $where['ExtendsCollection.status'] = $status;
        }

        // sort by
        $sort_string = 'ExtendsCollection.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'name':
                    $sort_string = 'ExtendsCollection.name '. $sort_type;
                break;

                case 'status':
                    $sort_string = 'ExtendsCollection.status '. $sort_type;
                break;           
            }
        }

        return $this->find()->contain($contain)->where($where)->select($fields)->order($sort_string);
    }

    public function getListActived()
    {
        $cache_key = EXTEND_COLLECTION . '_list_actived';
        $result = Cache::read($cache_key);

        if(is_null($result)){
            $result = $this->find()->where([
                'status' => 1, 
                'deleted' => 0
            ])->select(['id', 'name', 'code'])->toList();
            Cache::write($cache_key, !empty($result) ? $result : []);
        }

        return $result;
    }

    public function getList()
    {
        $cache_key = EXTEND_COLLECTION . '_list';
        $result = Cache::read($cache_key);

        if(is_null($result)){
            $result = $this->find()->where([
                'status' => 1, 
                'deleted' => 0
            ])->select(['id', 'name', 'code', 'fields'])->toList();
            $result = !empty($result) ? Hash::combine($result, '{n}.id', '{n}') : [];

            Cache::write($cache_key, $result);
        }

        return $result;
    }

    public function checkNameExist($name = null, $id = null)
    {
        if(empty($name)) return false;
        $where = [
            'name' => $name,
            'deleted' => 0
        ];
        if(!empty($id)) $where['id <>'] = $id;
        $collection_info = $this->find()->where($where)->select(['id'])->first();
        return !empty($collection_info) ? true : false;
    }

    public function checkCodeExist($code = null, $id = null)
    {
        if(empty($code)) return false;
        $where = [
            'code' => $code,
            'deleted' => 0
        ];
        if(!empty($id)) $where['id <>'] = $id;

        $collection_info = $this->find()->where($where)->select(['id'])->first();
        return !empty($collection_info) ? true : false;
    }

    public function mergeFieldsToFormConfig($form_config = [], $fields = [], $data_value = [])
    {
        if(empty($form_config['rows'])) return [];
        if(empty($fields)) return $form_config;
        
        foreach ($form_config['rows'] as $k_row => $rows) {
            if(empty($rows['columns'])) continue;

            foreach ($rows['columns'] as $k_col => $column) {
                $field_in_column = !empty($column['field']) ? $column['field'] : [];
                if(empty($field_in_column)) continue;

                $form_config['rows'][$k_row]['columns'][$k_col]['field'] = [];
                foreach ($field_in_column as $k_field => $field_code) {
                    if(!empty($fields[$field_code])) {
                        $fields[$field_code]['value'] = !empty($data_value[$field_code]) ? $data_value[$field_code] : null;
                        $form_config['rows'][$k_row]['columns'][$k_col]['field'][] = $fields[$field_code];
                    }
                }   
            }   
        }

        return $form_config;
    }

    public function getStructureRowOfExtend($id = null)
    {
    	if(empty($id)) return [];

        $collection = $this->find()->where([
            'ExtendsCollection.id' => $id,
            'ExtendsCollection.deleted' => 0
        ])->first();

        if(empty($collection)) return [];

        $fields = !empty($collection['fields']) ? json_decode($collection['fields'], 1) : [];
        $form_config = !empty($collection['form_config']) ? json_decode($collection['form_config'], 1) : [];
        if(empty($form_config)) return [];
        
        foreach ($form_config['rows'] as $k_row => $rows) {
            foreach ($rows['columns'] as $k_col => $columns) {
                foreach ($columns['field'] as $k_field => $field) {
                    foreach ($fields as $item_field) {
                        if(empty($item_field['code'])) continue;

                        if($item_field['code'] == $field) {
                            $form_config['rows'][$k_row]['columns'][$k_col]['field'][$k_field] = [
                                'name' => !empty($item_field['name']) ? $item_field['name'] : null,
                                'code' => !empty($item_field['code']) ? $item_field['code'] : null,
                                'input_type' => !empty($item_field['input_type']) ? $item_field['input_type'] : null,
                            ];
                        }
                    }   
                }   
            }   
        }

        return $form_config;
    }

    public function getFieldsShowInList($fields = [])
    {
        if(empty($fields)) return [];

        $result = [];
        foreach($fields as $field){
            $code = !empty($field['code']) ? $field['code'] : null;
            $view = !empty($field['view']) ? true : false;
            if(empty($code) || !$view) continue;
            $result[] = $code;
        }
        if(empty($result)) return [];

        $result[] = 'status';        
        return $result;
    }

    public function getDataByBlockConfigCollection($collection_config = [], $lang = null)
    {
        if(empty($collection_config)) return [];
        
        $number_record = !empty($collection_config['number_record']) ? intval($collection_config['number_record']) : 20;
        $sort_field = !empty($collection_config['sort_field']) ? $collection_config['sort_field'] : null;
        $sort_type = !empty($collection_config['sort_type']) ? $collection_config['sort_type'] : null;

        $collection_code = !empty($collection_config['extend_collection']) ? $collection_config['extend_collection'] : null;
        $type = !empty($collection_config['get_data_type']) ? $collection_config['get_data_type'] : null;
        $filter_field = !empty($collection_config['collection_field']) ? $collection_config['collection_field'] : null;
        $filter_condition = !empty($collection_config['collection_field_condition']) ? $collection_config['collection_field_condition'] : null;
        $filter_value = !empty($collection_config['collection_field_value']) ? $collection_config['collection_field_value'] : null;

        if(empty($collection_code)) return [];

        $collection_info = $this->find()->where([
            'code' => $collection_code,
            'deleted' => 0
        ])->select(['id', 'fields'])->first();

        $collection_id = !empty($collection_info['id']) ? $collection_info['id'] : null;
        $fields = !empty($collection_info['fields']) ? json_decode($collection_info['fields'], true) : [];
        $fields = !empty($fields) ? Hash::combine($fields, '{n}.code', '{n}') : [];
        if(empty($collection_id) ||  empty($fields)) return [];

        if(empty($lang)) $lang = TableRegistry::get('Languages')->getDefaultLanguage();

        // sort
        $params = [
            FILTER => [
                LANG => $lang,
                STATUS => 1
            ]
        ];

        if(!empty($sort_field)){
            $params[SORT] = [
                FIELD => $sort_field,
                SORT => in_array($sort_type, [DESC, ASC]) ? $sort_type : DESC
            ];
        }


        // filter
        if($type == 'filter_by_field' && !empty($fields[$filter_field])){
            $params[FILTER][FILTER_BY_FIELD] = [
                FIELD => $filter_field,
                CONDITION => $filter_condition,
                VALUE => $filter_value
            ];
        }

        $table_records = TableRegistry::get('ExtendsRecord');
        $records = $table_records->queryListExtendRecord($collection_id, $params)->limit($number_record)->toList();
        $records = $table_records->formatDataRecord($collection_id, $records, $lang);

        return $records;
    }
}