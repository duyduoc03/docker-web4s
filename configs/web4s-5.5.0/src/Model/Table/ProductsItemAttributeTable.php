<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Utility\Hash;

class ProductsItemAttributeTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('products_item_attribute');
        $this->setPrimaryKey('id');

        $this->hasOne('Attributes', [
            'className' => 'Publishing.Attributes',
            'foreignKey' => 'id',
            'bindingKey' => 'attribute_id',
            'joinType' => 'INNER',
            'propertyName' => 'Attributes'
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }

    // format dữ liệu thuộc tính phiên bản sản phẩm
    public function formatDataProductAttributeItems($data_item_attribute = [], $items = [], $lang = null)
    {
        if(empty($data_item_attribute) || empty($items)|| empty($lang)) return [];
     
        $attributes_table = TableRegistry::get('Attributes');
        $options_table = TableRegistry::get('AttributesOptions');

        $all_attributes = Hash::combine($attributes_table->getAll($lang), '{n}.id', '{n}', '{n}.attribute_type');
    
        $all_attributes_item = !empty($all_attributes[PRODUCT_ITEM]) ? $all_attributes[PRODUCT_ITEM] : [];
        $all_options = $options_table->getAll($lang);

        // sắp xếp thuộc tính phiên bản sản phẩm trước khi format , kiểu săp xếp: special_select_item DESC -> has_image DESC -> position DESC -> id DESC
        if(!empty($all_attributes_item)){
            foreach ($all_attributes_item as $k => $attribute) {

                // thêm cột sort
                $special = !empty($attribute['input_type']) && $attribute['input_type'] == 'special_select_item' ? 1 : 0;
                $has_image = !empty($attribute['has_image']) ? 1 : 0;
                $position = !empty($attribute['position']) ? intval($attribute['position']) : 0;
                $id = !empty($attribute['id']) ? intval($attribute['id']) : 0;
                $sort = $special . '_' . $has_image . '_' . str_pad(strval($position), 8, '0', STR_PAD_LEFT) . '_' . str_pad(strval($id), 8, '0', STR_PAD_LEFT);

                $attribute['sort'] = $sort;
                $all_attributes_item[$k] = $attribute;
            }

            //sắp xếp
            array_multisort(array_column($all_attributes_item, 'sort'), SORT_DESC, $all_attributes_item);
        }

        // format dữ liệu
        $attributes_apply = $attributes_special = [];
        foreach($items as $k => $item){
            $product_item_id = !empty($item['id']) ? intval($item['id']) : null;
            $code = !empty($item['code']) ? $item['code'] : null;
            $price = !empty($item['price']) ? floatval($item['price']) : null;
            $price_special = !empty($item['price_special']) ? floatval($item['price_special']) : null;
            $apply_special = !empty($item['apply_special']) ? true : false;
            $quantity_available = !empty($item['quantity_available']) ? intval($item['quantity_available']) : null;
            
            if (!empty($item['images']) && TableRegistry::get('Utilities')->isJson($item['images'])){
                $item['images'] = json_decode($item['images'], true);
            }
            $images = !empty($item['images']) ? $item['images'] : [];

            $list_special_code = $list_extend_name = $list_normal = $attributes_of_item = [];
            
            foreach($all_attributes_item as $attribute_info){
                unset($attribute_info['sort']);

                $input_type = !empty($attribute_info['input_type']) ? $attribute_info['input_type'] : null;
                $attribute_id = !empty($attribute_info['id']) ? intval($attribute_info['id']) : null;
                $attribute_code = !empty($attribute_info['code']) ? $attribute_info['code'] : null;

                foreach($data_item_attribute as $item_attribute){
                    if(empty($item_attribute['product_item_id']) || $item_attribute['product_item_id'] != $product_item_id) continue;                    
                    if(empty($item_attribute['attribute_id']) || $item_attribute['attribute_id'] != $attribute_id) continue;

                    if(empty($attributes_apply[$attribute_id])){
                        $attributes_apply[$attribute_id] = $attribute_info;
                        $attributes_apply[$attribute_id]['options'] = [];
                    }

                    $value = !empty($item_attribute['value']) ? $item_attribute['value'] : null;

                    if($input_type == SPECICAL_SELECT_ITEM && !empty($value) && !empty($all_options[$value])){
                        $option_id = $value;

                        $option_info = $all_options[$option_id];
                        $option_code = !empty($option_info['code']) ? $option_info['code'] : null;
                        $option_name = !empty($option_info['name']) ? $option_info['name'] : null;
                        if(!empty($attribute_info['has_image']) && !empty($option_info)){
                           $option_info['image'] = !empty($images[0]) ? $images[0] : null;
                        }

                        $attributes_apply[$attribute_id]['options'][$option_id] = $option_info;

                        if(empty($list_special_code[$attribute_code])){
                            $list_special_code[$attribute_code] = $option_code;
                            $list_extend_name[] = $option_name;
                        }
                    }else{
                        // format value attribute
                        $value_format = $attributes_table->formatValueAttribute($input_type, $value, $lang);
                        if($input_type == SINGLE_SELECT){
                            $option_selected = $value_format;
                            $option_info = !empty($all_options[$option_selected]) ? $all_options[$option_selected] : [];
                            $value_format = !empty($option_info['name']) ? $option_info['name'] : null;
                        }

                        if($input_type == MULTIPLE_SELECT && is_array($value_format)){
                            $options_selected = $value_format;
                            $value_format = [];
                            foreach($options_selected as $option_selected){
                                $option_info = !empty($all_options[$option_selected]) ? $all_options[$option_selected] : [];
                                $name = !empty($option_info['name']) ? $option_info['name'] : null;
                                $value_format[] = $name;
                            }
                            $value_format = implode(',', array_filter($value_format));
                        }

                        $attribute_info['value_format'] = $value_format;
                        $attribute_info['value'] = $value;
                        

                        $list_normal[$attribute_code] = $attribute_info;
                    }
                    $attributes_of_item[] = [                        
                        'code' => $attribute_code,
                        'attribute_id' => $attribute_id,
                        'input_type' => $input_type,
                        'value' => $value
                    ];
                }
            }

            $special_code = null;
            if(!empty($list_special_code)){
                $explode_code = [];
                foreach($list_special_code as $attribute_code => $option_code){
                    $explode_code[] = $attribute_code;
                    $explode_code[] = $option_code;
                }
                $special_code = implode('_', $explode_code);
            }

            $item['special_code'] = $special_code;
            $item['extend_name'] = !empty($list_extend_name) ? implode(' - ', array_filter($list_extend_name)) : null;
            $item['attributes_normal'] = $list_normal;
            $item['attributes'] = $attributes_of_item;
            
            $items[$k] = $item;
            $attributes_special[$special_code] = $item;
        }

        // kiểm tra nếu có trên 2 thuộc tính áp dụng ảnh đại diện thì bỏ đi 1 thuộc tính

        if(!empty($attributes_apply)){
            $has_image = false;
            foreach($attributes_apply as $k => $attribute){
                if(!empty($attribute['has_image']) && $has_image){
                    $attributes_apply[$k]['has_image'] = 0;
                }

                if(!empty($attribute['has_image']) && !$has_image){
                    $has_image = true;
                }
            }
        }
 
        return [
            'items' => $items,
            'attributes_item_apply' => $attributes_apply,
            'attributes_item_special' => $attributes_special
        ];
    }

}