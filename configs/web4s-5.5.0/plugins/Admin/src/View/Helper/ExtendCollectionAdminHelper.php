<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class ExtendCollectionAdminHelper extends Helper
{   
    public function generateInput($params = [])
    {
        $code = !empty($params['code']) ? $params['code'] : null;
        $input_type = !empty($params['input_type']) ? $params['input_type'] : null;
        $value = !empty($params['value']) ? $params['value'] : null;

        $list_type = Configure::read('LIST_TYPE_INPUT_DATA_EXTEND');
        if(empty($code) || empty($input_type) || empty($list_type[$input_type])) return;

        if($input_type == MULTIPLE_SELECT && !is_array($value)) $value = !empty($value) ? json_decode($value, true) : [];

        $data_input = [
            'code' => $code,
            'id' => !empty($params['id']) ? $params['id'] : $code,
            'name' => !empty($params['name']) ? $params['name'] : $code,
            'value' => $value,
            'label' => !empty($params['label']) ? $params['label'] : null,
            'has_image' => !empty($params['has_image']) ? 1 : 0,
            'required' => !empty($params['required']) ? 1 : 0,
            'options' =>!empty($params['options']) ? $params['options'] : [],
            'class' => !empty($params['class']) ? $params['class'] : null,
            'disabled' => !empty($params['disabled']) ? true : false
        ];

        return $this->_View->element('Admin.attribute/' . $input_type, $data_input);
    }

    public function getListTypeInput()
    {
        return Configure::read('LIST_TYPE_INPUT_DATA_EXTEND');
    }

    public function getListActived()
    {
        return TableRegistry::get('ExtendsCollection')->getListActived();
    }

    public function getFieldsForDropdownFilterData(string $collection_code)
    {
        if(empty($collection_code) && is_string($collection_code)) return [];

        $collection_info = TableRegistry::get('ExtendsCollection')->find()->where([
            'code' => $collection_code,
            'deleted' => 0
        ])->select(['id', 'fields'])->first();
        $collection_fields = !empty($collection_info['fields']) ? json_decode($collection_info['fields'], true) : [];

        // chỉ show những fields có type đc phép lọc
        $allow = [TEXT, SINGLE_SELECT, MULTIPLE_SELECT, SWITCH_INPUT, DATE, DATE_TIME];
        $fields = [];
        if(!empty($collection_fields)){
            foreach($collection_fields as $field){
                $field_code = !empty($field['code']) ? $field['code'] : null;
                $field_name = !empty($field['name']) ? $field['name'] : null;
                $input_type = !empty($field['input_type']) ? $field['input_type'] : null;
                if(empty($input_type) || !in_array($input_type, $allow)) continue;
                $fields[$field_code] = $field_name;
            }
        }
        
        return $fields;
    }
}
