<?php
namespace App\Service;

use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Core\Exception\Exception;
use Cake\I18n\Date;
use Cake\I18n\Time;

class AttributeService extends AppService
{    
    public function formatAttributesFromDataForm($data = [], $lang = '', $type = '', $record_id = '')
    {
        $utilities = TableRegistry::get('Utilities');

    	if(empty($data) || empty($type) || empty($lang)) return [];
    	if(!in_array($type, [ARTICLE, PRODUCT, PRODUCT_ITEM, CATEGORY])) return [];
        
    	$all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($lang), '{n}.id', '{n}', '{n}.attribute_type');
        $all_attributes = !empty($all_attributes[$type]) ? $all_attributes[$type] : [];

        if(empty($all_attributes)) return [];

        $result = [];
        foreach ($all_attributes as $attribute_id => $attribute) {            
            $code = !empty($attribute['code']) ? $attribute['code'] : null;
            $input_type = !empty($attribute['input_type']) ? $attribute['input_type'] : null;
            if(empty($input_type)) continue;

            $value = !empty($data[$code]) ? $data[$code] : null;
            switch ($input_type) {
                case NUMERIC:
                    $value = !empty($value) ? floatval(str_replace(',', '', $value)) : 0;
                    break;

                case DATE:
                    if(!$utilities->isDateClient($value)){
                        $value = null;
                    }
                    $value = !empty($value) ? $utilities->stringDateClientToInt($value) : null;
                    break;

                case DATE_TIME:
                    if(!empty($value)){
                        $time = Time::createFromFormat('d/m/Y - H:i', $value, null);
                        $time = $time->format('Y-m-d H:i:s');
                        $value = strtotime($time);
                    }
                    break;

                case SWITCH_INPUT:
                    $value = !empty($value) ? 1 : 0;
                    break;

                case TEXT:
                case RICH_TEXT:
                case IMAGE:
                case IMAGES:
                case FILES:
                    $value = !empty($value) ? trim($value) : '';
                    break;

                case SINGLE_SELECT:
                    $value = !empty($value) ? intval($value) : null;
                    break;

                case MULTIPLE_SELECT:
                case PRODUCT_SELECT:
                case ARTICLE_SELECT:
                case CITY_DISTRICT:
                case CITY_DISTRICT_WARD:
                case VIDEO:
                case ALBUM_IMAGE:
                case ALBUM_VIDEO:
                    $value = !empty($value) ? json_encode($value) : null;
                    break;

                case CITY:
                    $value = !empty($value) ? $value : null;
                    break;

                default:
                    $value = null;
                break;
            }

            if(in_array($input_type, [TEXT, RICH_TEXT])){
                $text_value = [];
                // nếu bản ghi cũ thì vẫn giữ nội dung của ngôn ngữ khác, chỉ ghi đè của ngôn ngữ hiện tại
                if(!empty($record_id)){
                	switch ($type) {
                		case ARTICLE:
                			$record_attribute = TableRegistry::get('ArticlesAttribute')->find()->where([
		                        'article_id' => $record_id,
		                        'attribute_id' => $attribute_id
		                    ])->first();
                			break;
                		case PRODUCT:
                			$record_attribute = TableRegistry::get('ProductsAttribute')->find()->where([
		                        'product_id' => $record_id,
		                        'attribute_id' => $attribute_id
		                    ])->first();
                			break;
                        case CATEGORY:
                            $record_attribute = TableRegistry::get('CategoriesAttribute')->find()->where([
                                'category_id' => $record_id,
                                'attribute_id' => $attribute_id
                            ])->first();
                            break;
                	}
                    $text_value = !empty($record_attribute['value']) ? json_decode($record_attribute['value'], true) : [];
                }
                
                if(!is_array($text_value)) $text_value = [];
                $text_value[$lang] = $value;
                $value = !empty($text_value) ? json_encode($text_value) : null;
            }

            $result[] = [
                'attribute_id' => $attribute_id,
                'value' => $value
            ];
        }

        return $result;
    }

    public function formatAttributesProductItemFromDataForm($items = [], $lang = '')
    {        
    	if(empty($items) || !is_array($items)) return [];

        $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($lang), '{n}.code', '{n}', '{n}.attribute_type');
        $all_attributes = !empty($all_attributes[PRODUCT_ITEM]) ? $all_attributes[PRODUCT_ITEM] : [];
        if(empty($all_attributes)) return [];

        $utilities = TableRegistry::get('Utilities');

        $result = $mapping = [];

        foreach($items as $item){
            $product_item_id = !empty($item['id']) ? intval($item['id']) : null;

            // nếu không có thuộc tính thì push mảng rỗng để số phần tử $items với $items_attribute sau format là như nhau
            if(empty($item['attribute']) || !is_array($item['attribute'])) {
                $result[] = [];
                continue;
            }

            $attribute_item = $check_code = [];
            foreach($item['attribute'] as $attribute){
                $code = !empty($attribute['attribute_code']) ? $attribute['attribute_code'] : '';
                $attribute_id = !empty($all_attributes[$code]['id']) ? intval($all_attributes[$code]['id']) : null;
                $input_type = !empty($all_attributes[$code]['input_type']) ? $all_attributes[$code]['input_type'] : null;
                $value = !empty($attribute['value']) ? $attribute['value'] : null;
                if(empty($code) || empty($attribute_id)) continue;                            
                
                switch ($input_type) {
                    case NUMERIC:
                        $value = !empty($value) ? floatval(str_replace(',', '', $value)) : 0;
                        break;

                    case DATE:
                        if(!$utilities->isDateClient($value)){
                            $value = null;
                        }
                        $value = !empty($value) ? $utilities->stringDateClientToInt($value) : null;
                        break;

                    case DATE_TIME:
                        if(!empty($value)){
                            $time = Time::createFromFormat('d/m/Y - H:i', $value, null);
                            $time = $time->format('Y-m-d H:i:s');
                            $value = strtotime($time);
                        }
                        break;

                    case SWITCH_INPUT:
                        $value = !empty($value) ? 1 : 0;
                        break;

                    case TEXT:
                    case RICH_TEXT:
                        $value = !empty($value) ? trim($value) : '';
                        break;

                    case SINGLE_SELECT:
                        $value = !empty($value) ? intval($value) : null;
                        break;

                    case MULTIPLE_SELECT:
                        $value = !empty($value) ? json_encode($value) : null;
                        break;
                    case SPECICAL_SELECT_ITEM:
                        $value = !empty($value) ? intval($value) : '';
                        $check_code[] = $attribute['attribute_code'];
                        $check_code[] = $value;
                    break;
                }

                $attribute_item[] = [
                    'product_item_id' => $product_item_id,
                    'attribute_id' => $attribute_id,
                    'value' => $value
                ];
            }

            // kiểm tra thuộc tính phiên bản có giá trị giống nhau đã tồn tại
            if(!empty($check_code) && in_array(implode('_', $check_code), $mapping)) {
                $result[] = [];
                continue;
            }
            $mapping[] = implode('_', $check_code);

            $result[] = $attribute_item;
        }

        return $result;

    }
}
