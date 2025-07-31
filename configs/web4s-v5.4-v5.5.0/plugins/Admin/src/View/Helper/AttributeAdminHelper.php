<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class AttributeAdminHelper extends Helper
{   
    public $helpers = ['Admin.UtilitiesAdmin'];

    public function generateInput($params = [], $lang = null)
    {
        $input_type = !empty($params['input_type']) ? $params['input_type'] : null;
        if(!in_array($input_type, Configure::read('ALL_ATTRIBUTE')) || empty($params['code'])){
            return;
        }
        $attribute_type = !empty($params['attribute_type']) ? $params['attribute_type'] : null;

        $value = !empty($params['value']) ? $params['value'] : null;
        if ($attribute_type == PRODUCT_ITEM && $input_type == MULTIPLE_SELECT) {
            $value = !empty($value) ? json_decode($value, true) : [];
        }

        $view_element = $input_type;
        $code = !empty($params['code']) ? $params['code'] : null;
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

        if($input_type == SPECICAL_SELECT_ITEM){
            $view_element = MULTIPLE_SELECT_ITEM;
        }

        if($input_type == SPECICAL_SELECT_ITEM && !empty($data_input['has_image'])){
            $view_element = SINGLE_SELECT_ITEM;
        }

        return $this->_View->element('Admin.attribute/' . $view_element, $data_input);
    }

    public function getList($lang = null)
    {
        if(empty($lang)) return [];
        $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($lang), '{n}.id', '{n}.name');
        
        return !empty($all_attributes) ? $all_attributes : [];
    }

    public function getListInputType($lang = null)
    {
        if(empty($lang)) return [];
        $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($lang), '{n}.id', '{n}.input_type');
        
        return !empty($all_attributes) ? $all_attributes : [];
    }

    public function getSpecialItem($lang = [])
    {
        if(empty($lang)) return [];

        $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($lang), '{n}.id', '{n}', '{n}.attribute_type');

        $list_attributes_special = [];
        if(!empty($all_attributes[PRODUCT_ITEM])){
            $list_attributes_special = Hash::combine(Collection($all_attributes[PRODUCT_ITEM])->filter(function ($item, $key, $iterator) {
                return $item['input_type'] == SPECICAL_SELECT_ITEM;
            })->toArray(),'{n}.id', '{n}.name');
        }
        
        return !empty($list_attributes_special) ? $list_attributes_special : [];
    }

    public function getListType()
    {
        $result = [
            PRODUCT => __d('admin', 'san_pham'),
            PRODUCT_ITEM => __d('admin', 'phien_ban_san_pham'),
            ARTICLE => __d('admin', 'bai_viet'),
            CATEGORY => __d('admin', 'danh_muc')
        ];

        return $result;
    }

    public function getListTypeInput($attribute_type = null)
    {
        $result = Configure::read('LIST_ATTRIBUTE_NORMAL');

        if($attribute_type == PRODUCT_ITEM){
            $result = Configure::read('ATTRIBUTE_PRODUCT_ITEM');
        }

        return $result;
    } 

    public function formatDataAttributes($attribute = null, $lang_log = null, $type = null)
    {   
        if(empty($attribute)) return [];
        
        if(empty($lang_log)) $lang_log = LANGUAGE;

        $all_options = Hash::combine(TableRegistry::get('AttributesOptions')->getAll($lang_log), '{n}.id', '{n}.name', '{n}.attribute_id'); 
        $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($lang_log), '{n}.id', '{n}', '{n}.attribute_type');

        $attributes_data = !empty($all_attributes[$type]) ? Hash::combine($all_attributes[$type], '{n}.id', '{n}') : []; 
        $citites = TableRegistry::get('Cities')->getListCity();
       
        $result = [];
        foreach($attribute as $attribute_code => $value_attribute){            
            $attribute_id = !empty($value_attribute['id']) ? $value_attribute['id'] : null;
            $attribute_value = !empty($value_attribute['value']) ? $value_attribute['value'] : null;
            $attribute_name = !empty($value_attribute['name']) ? $value_attribute['name'] : null;
            $input_type = !empty($attributes_data[$attribute_id]['input_type']) ? $attributes_data[$attribute_id]['input_type'] : null;
            
            //format lại data thuộc tính danh mục 
            if (!empty($type == 'category')){
                $attributes_data = !empty($all_attributes[$type]) ? Hash::combine($all_attributes[$type], '{n}.code', '{n}') : [];
                $input_type = !empty($attributes_data[$attribute_code]['input_type']) ? $attributes_data[$attribute_code]['input_type'] : null;
                $data_category = [];
                $data_category['value'] = !empty($value_attribute) ? $value_attribute : null;
                $attribute_value = $data_category['value'];
                $attribute[$attribute_code] = $data_category;
            }
            if(empty($attribute_value)) continue;
            
            $data_value = []; 
            switch($input_type){
                case IMAGES:
                case VIDEO:
                case FILES:
                    $value = json_decode($attribute_value, true);
                    foreach ($value as $key => $item) {
                    $data_value[$key] = !empty($item) ? $item : null;
                    }
                    $attribute[$attribute_code]['value'] = $data_value;
                    break;

                case DATE:
                    $value = $attribute_value;
                    if ($type == 'category') {
                        $format = 'd/m/Y';
                        $value = date(strval($format), intval($attribute_value));
                    }               
                    $attribute[$attribute_code]['value'] = $value;
                    break;

                case DATE_TIME:
                    $value = $attribute_value;
                    if ($type == 'category') {
                        $format = 'H:i - d/m/Y';
                        $value = date(strval($format), intval($attribute_value));
                    }                
                    $attribute[$attribute_code]['value'] = $value;
                    break;
                        
                case CITY:
                    $value = json_decode($attribute_value, true);
                    $value_city = !empty($value) ? $citites[$value]  : null;   

                    $attribute[$attribute_code]['value'] = $value_city;
                    break;
                case CITY_DISTRICT:
                    $value = json_decode($attribute_value, true);
                     
                    $city_id = !empty($value['city_id']) ? intval($value['city_id']) : null;
                    
                    $district_id = !empty($value['district_id']) ? intval($value['district_id']) : null;
                    $ward_id = !empty($value['ward_id']) ? intval($value['ward_id']) : null;
                    $value_city = !empty($citites[$city_id]) ? $citites[$city_id]  : null;
                    $districts = TableRegistry::get('Districts')->getListDistrict($city_id);
                    $value_district = !empty($districts[$district_id]) ?  ' - ' . $districts[$district_id] : null;
                    $wards = TableRegistry::get('Wards')->getListWard($district_id);
                    $value_ward = !empty($wards[$ward_id]) ? ' - ' . $wards[$ward_id] : null;
                    
                    $value = $value_city . $value_district . $value_ward;
                    
                    $attribute[$attribute_code]['value'] = $value;
                    break;

                case CITY_DISTRICT_WARD:
                    $value = json_decode($attribute_value, true);
                     
                    $city_id = !empty($value['city_id']) ? intval($value['city_id']) : null;
                    
                    $district_id = !empty($value['district_id']) ? intval($value['district_id']) : null;
                    $ward_id = !empty($value['ward_id']) ? intval($value['ward_id']) : null;
                    $value_city = !empty($citites[$city_id]) ? $citites[$city_id]  : null;
                    $districts = TableRegistry::get('Districts')->getListDistrict($city_id);
                    $value_district = !empty($districts[$district_id]) ?  ' - ' . $districts[$district_id] : null;
                    $wards = TableRegistry::get('Wards')->getListWard($district_id);
                    $value_ward = !empty($wards[$ward_id]) ? ' - ' . $wards[$ward_id] : null;
                    
                    $value = $value_city . $value_district . $value_ward;
                    
                    $attribute[$attribute_code]['value'] = $value;
                    break;

                case SINGLE_SELECT:
                case MULTIPLE_SELECT:
                    $value = !empty($all_options[$attribute_id][$attribute_id]) ? $all_options[$attribute_id][$attribute_id] : [];
                    $attribute[$attribute_code]['value'] = $value;
                    break;
                
                case SWITCH_INPUT:               
                    $value = !empty($attribute_value) ? $attribute_name : null;
                    $attribute[$attribute_code]['value'] = $value;
                    break;

                case TEXT:
                case RICH_TEXT:
                    $value = [htmlentities((string) $attribute_value)];
                    if ($type == 'category') {
                        $value_category = json_decode($attribute_value, true);
                        $value_category = !empty($value_category[$lang_log]) ? $value_category[$lang_log] : null;
                        $value = [htmlentities((string) $value_category)];
                    }
                    $attribute[$attribute_code]['value'] = $value;
                    break;
                   
                case PRODUCT_SELECT:
                    $value = json_decode($attribute_value, true);
                    foreach ($value as $key => $item) {
                        $item_product = TableRegistry::get('Products')->getAllNameContent($item);
                        
                        $data_value[$key] = !empty($item_product) ? $item_product[$lang_log] : null;
                    }
                    $attribute[$attribute_code]['value'] = $data_value;
                    break;

                case ARTICLE_SELECT:
                    $value = json_decode($attribute_value, true);
                    foreach ($value as $key => $item) {
                        $item_article = TableRegistry::get('Articles')->getAllNameContent($item);
                        $data_value[$key] = !empty($item_article) ? $item_article[$lang_log] : null;
                    }
                    $attribute[$attribute_code]['value'] = $data_value;
                    break;

                default:
                    $value = $attribute_value;
            }

            $result[$attribute_code] = [
                    'id' => $attribute_id,
                    'name' => $attribute_name,
                    'value' => !empty($attribute[$attribute_code]['value']) ? $attribute[$attribute_code]['value'] : null,
                    'type' => $input_type
            ];
        }
        return $result;
    }

    public function formatDataAttributesAll($attributes_before, $attributes_after)
    {   
        if(empty($attributes_before) || empty($attributes_before)) return [];
        
        $result = [];
        foreach($attributes_before as $code => $attribute){ 
            $name = !empty($attribute['name']) ? $attribute['name'] : null;
            $value = !empty($attribute['value']) ? $attribute['value'] : [];
            if(empty($code) || empty($value)) continue;

            if(!isset($result[$code])) {
                $result[$code] = [
                    'before' => [
                       'value' => [] 
                    ],
                    'after' => [
                       'value' => []
                    ],
                ];
            }

            $result[$code]['before'] = [
                'name' => $name,
                'value' => $value
            ];
        }

        foreach($attributes_after as $code => $attribute){ 
            $name = !empty($attribute['name']) ? $attribute['name'] : null;
            $value = !empty($attribute['value']) ? $attribute['value'] : [];
            
            if(empty($code) || empty($value)) continue;

            if(!isset($result[$code])) {
                $result[$code] = [
                    'before' => [
                       'value' => [] 
                    ],
                    'after' => [
                       'value' => []
                    ],
                ];
            }

            $result[$code]['after'] = [
                'name' => $name,
                'value' => $value
            ];
        }

        return $result;
    }

    public function formatDataMutipleLanguage($mutiple_language_before, $mutiple_language_after)
    {   

        if(empty($mutiple_language_before) && empty($mutiple_language_after)) return [];

        $result = [];
        foreach($mutiple_language_before as $lang => $item){ 
            if(empty($lang) || empty($item)) continue;

            $item_lang = [];
            foreach($item as $code => $value){
                if (empty($code) || empty($value)) continue;

                $item_lang[$code]['before'] = $value;
            }
            
            $result[$lang] = $item_lang;
        }
        
        foreach($mutiple_language_after as $lang => $item){ 
            if(empty($lang) || empty($item)) continue;
            
            $item_lang = !empty($result[$lang]) ? $result[$lang] : [];
            foreach($item as $code => $value){
                if (empty($code) || empty($value)) continue;

                $item_lang[$code] = !empty($item_lang[$code]) ? $item_lang[$code] : [];
                $item_lang[$code]['after'] = $value;
            }
            
            $result[$lang] = $item_lang;
        }

        return $result;
    }

    public function getAttributeByMainCategory($category_id = null, $type = null, $lang = null)
    {
        $result = TableRegistry::get('Attributes')->getAttributeByMainCategory($category_id, $type, $lang);
        return !empty($result) ? $result : [];
    }

    public function getSpecialAttributeItemByMainCategory($category_id = null, $lang = null)
    {
        $result = TableRegistry::get('Attributes')->getSpecialAttributeItemByMainCategory($category_id, $lang);
        return !empty($result) ? $result : [];
    }
}
