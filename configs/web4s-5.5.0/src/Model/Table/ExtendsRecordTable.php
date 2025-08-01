<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Utility\Text;

class ExtendsRecordTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('extends_record');
        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new'
                ]
            ]
        ]);

        $this->hasOne('ExtendFilter', [
            'className' => 'Extends',
            'foreignKey' => 'record_id',
            'joinType' => 'INNER',
            'propertyName' => 'ExtendFilter'
        ]);

        $this->hasMany('Extends', [
            'className' => 'Extends',
            'foreignKey' => 'record_id',
            'joinType' => 'INNER',
            'propertyName' => 'Extends'
        ]);
    }

    public function queryListExtendRecord($collection_id = null, $params = []) 
    {
        $collections = TableRegistry::get('ExtendsCollection')->getList();
        $collection_info = !empty($collections[$collection_id]) ? $collections[$collection_id] : [];
        $collection_fields = !empty($collection_info['fields']) ? json_decode($collection_info['fields'], true) : [];
        $collection_fields = !empty($collection_fields) ? Hash::combine($collection_fields, '{n}.code', '{n}') : [];
        
        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $lang = !empty($filter[LANG]) ? $filter[LANG] : TableRegistry::get('Languages')->getDefaultLanguage();
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $status = isset($filter['status']) && $filter['status'] != '' ? intval($filter['status']) : null;        
        $filter_by_field = !empty($filter[FILTER_BY_FIELD]) ? $filter[FILTER_BY_FIELD] : [];
        
        $contain = [
            'ExtendFilter',
            'Extends' => function ($q) use ($lang) {
                return $q->where([
                    'Extends.lang IN' => [ALL, $lang]
                ]);
            }
        ]; 

        $where = [
            'ExtendsRecord.collection_id' => $collection_id,
        ];
        
        // filter by conditions  
        if(!empty($keyword)){
            $where['ExtendsRecord.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!is_null($status)){
            $where['ExtendsRecord.status'] = $status;
        }

        if(!empty($filter_by_field)){
            $field = !empty($filter_by_field[FIELD]) ? $filter_by_field[FIELD] : null;
            $condition = !empty($filter_by_field[CONDITION]) ? $filter_by_field[CONDITION] : null;
            $value = !empty($filter_by_field[VALUE]) ? $filter_by_field[VALUE] : null;

            $field_info = !empty($collection_fields[$field]) ? $collection_fields[$field] : [];
            $input_type = !empty($field_info['input_type']) ? $field_info['input_type'] : null;
            $options = !empty($field_info['options']) ? $field_info['options'] : [];

            if(!empty($field) && in_array($input_type, Configure::read('TYPE_FIELD_FILTER_DATA_EXTEND'))){
                if($input_type == TEXT){
                    $where['ExtendsRecord.search_unicode LIKE'] = '%' . Text::slug(strtolower($value), ' ') . '%';
                }

                if($input_type == SINGLE_SELECT){
                    $where['ExtendFilter.field'] = $field;
                    $where['ExtendFilter.value'] = $value;
                }

                if($input_type == MULTIPLE_SELECT){
                    $where['ExtendFilter.field'] = $field;
                    if(is_array($value)){
                        $where['OR'] = [];
                        foreach($value as $value_item){
                            $where['OR'][] = 'ExtendFilter.value LIKE ' . '"%' . $value_item . '%"';
                        }
                    }else{
                        $where['ExtendFilter.value LIKE'] = !empty($value) ? '"%' . $value . '%"' : '';
                    }
                    
                }

                if($input_type == SWITCH_INPUT){
                    $where['ExtendFilter.field'] = $field;
                    $where['ExtendFilter.value'] = $value;
                }

                if(in_array($input_type, [NUMERIC, DATE, DATE_TIME])){
                    $where['ExtendFilter.field'] = $field;
                    switch($condition){
                        case 'gt':
                            $where['ExtendFilter.value >='] = $value;
                        break;

                        case 'lt':
                            $where['ExtendFilter.value <='] = $value;
                        break;

                        default:
                        case 'equal':
                            $where['ExtendFilter.value'] = $value;
                        break;
                    }
                }
            }
        }

        // fields
        $fields = ['ExtendsRecord.id', 'ExtendsRecord.position', 'ExtendsRecord.status', 'ExtendsRecord.created'];        

        // sort by
        $sort_string = 'ExtendsRecord.id DESC';
        if(!empty($sort_field)){
            switch($sort_field){
                case 'status':
                    $sort_string = 'ExtendsRecord.status '. $sort_type;
                break;

                case 'position':
                    $sort_string = 'ExtendsRecord.position '. $sort_type;
                break;
            }
        }
        
        $query = $this->find()->contain($contain)->where($where)->select($fields)->order($sort_string)->group('ExtendsRecord.id');
        return $query;
    }

    public function formatDataRecord($collection_id = null, $data = [], $lang = null, $check_multiple_lang = false)
    {
        if(empty($data) || empty($collection_id)) return [];

        $result = [];        
        if(empty($lang)) $lang = TableRegistry::get('Languages')->getDefaultLanguage();
        $languages = TableRegistry::get('Languages')->getList();

        foreach($data as $k => $record){
            $extends = !empty($record['Extends']) ? $record['Extends'] : [];
            if(empty($extends)) continue;

            $item = [
                'id' => !empty($record['id']) ? $record['id'] : null,
                'position' => !empty($record['position']) ? intval($record['position']) : 0,
                'status' => !empty($record['status']) ? 1 : 0,
                'created' => !empty($record['created']) ? intval($record['created']) : null
            ];

            $mutiple_language = [];
            foreach($extends as $extend){
                $field = !empty($extend['field']) ? $extend['field'] : null;
                $value = !empty($extend['value']) ? $extend['value'] : null;
                $extend_lang = !empty($extend['lang']) ? $extend['lang'] : null;
                if(empty($field) || (!empty($fields_show) && !in_array($field, $fields_show))) continue;
                if($extend_lang != 'all' && $extend_lang != $lang) continue;
                $item[$field] = $value;

                // check multiple language
                if($check_multiple_lang && $extend_lang != 'all' && !empty($languages[$extend_lang])){
                    $mutiple_language[$extend_lang] = true;
                }
                
            }

            if($check_multiple_lang) $item['mutiple_language'] = $mutiple_language;

            $result[] = $item;
        }

        return $result;
    }
}