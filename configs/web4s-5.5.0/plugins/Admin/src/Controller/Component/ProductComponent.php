<?php

namespace Admin\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Hash;

class ProductComponent extends Component
{
	public $controller = null;
    public $components = ['System', 'Utilities', 'Admin.Tag', 'Admin.Translate'];
    public $lang = null;

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();

        $this->lang = $this->controller->lang;
    }
  
    public function saveProduct($data_save = [], $id = null, $product_old = [])
    {
        if(empty($data_save)) return $this->System->getResponse();

        $associated = [
            'ProductsContent', 
            'Links', 
            'CategoriesProduct', 
            'ProductsItem', 
            'ProductsAttribute' , 
            'TagsRelation'
        ];

        // translate
        $languages = TableRegistry::get('Languages')->getList();

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $setting_language = !empty($settings['language']) ? $settings['language'] : [];
        if(empty($id) && !empty($setting_language['auto_translate']) && count($languages) > 1){
            $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($this->lang), '{n}.id', '{n}', '{n}.attribute_type');
            $all_attributes = !empty($all_attributes[PRODUCT]) ? Hash::combine($all_attributes[PRODUCT], '{n}.id', '{n}') : [];

            $data_save['ContentMutiple'][] = !empty($data_save['ProductsContent']) ? $data_save['ProductsContent'] : [];
            $data_save['LinksMutiple'][] = !empty($data_save['Links']) ? $data_save['Links'] : [];


            $name = !empty($data_save['ProductsContent']['name']) ? $data_save['ProductsContent']['name'] : null;
            $description = !empty($data_save['ProductsContent']['description']) ? $data_save['ProductsContent']['description'] : null;
            $content = !empty($data_save['ProductsContent']['content']) ? $data_save['ProductsContent']['content'] : null;
            $link = !empty($data_save['Links']['url']) ? $data_save['Links']['url'] : null;

            foreach($languages as $language_code => $language){
                if($language_code == $this->lang) continue;
         
                // translate title, description and content
                $items = [];
                if (!empty($name)) $items['name'] = $name;
                if (!empty($description) && strlen($description) <= 5000 && !empty($setting_language['translate_all'])) {
                    $items['description'] = $description;
                }

                if (!empty($content) && strlen($content) <= 5000 && !empty($setting_language['translate_all'])) {
                    $items['content'] = $content;
                }

                if(empty($items)) continue;
                $translates = !empty($items) ? $this->Translate->translate($items, $this->lang, $language_code) : [];
                 
                $name_translate = !empty($translates['name']) ? $translates['name'] : $name;
                $description_translate = !empty($translates['description']) ? $translates['description'] : null;
                $content_translate = !empty($translates['content']) ? $translates['content'] : null;
                
                // link translate
                $link_translate = $this->Utilities->formatToUrl($name_translate);
                if(empty($link_translate)) continue;

                $link_translate = TableRegistry::get('Links')->getUrlUnique($link_translate);
                if($link_translate == $link) $link_translate .= '-1';

                // translate attribute text richtext and text
                if(!empty($data_save['ProductsAttribute'])){
                    foreach($data_save['ProductsAttribute'] as $key => $attribute_item){
                        $attribute_id = !empty($attribute_item['attribute_id']) ? $attribute_item['attribute_id'] : null;
                        $input_type = !empty($all_attributes[$attribute_id]['input_type']) ? $all_attributes[$attribute_id]['input_type'] : null;
                        if(empty($attribute_id) || !in_array($input_type, [RICH_TEXT, TEXT])) continue;

                        $value = !empty($attribute_item['value']) && $this->Utilities->isJson($attribute_item['value']) ?json_decode($attribute_item['value'], true) : [];
                        if(empty($value) || empty($value[$this->lang])) continue;

                        $translates = $this->Translate->translate([$value[$this->lang]], $this->lang, $language_code);
                        $value[$language_code] = !empty($translates[0]) ? $translates[0] : '';

                        $data_save['ProductsAttribute'][$key]['value'] = json_encode($value);
                    }
                }

                // set value after translate
                $record_translate = [
                    'name' => $name_translate,
                    'lang' => $language_code,
                    'search_unicode' => strtolower($this->Utilities->formatSearchUnicode([$name_translate]))
                ];

                if(!empty($setting_language['translate_all'])){
                    $record_translate = [
                        'name' => $name_translate,
                        'description' => $description_translate,
                        'content' => $content_translate,
                        'seo_title' => $name_translate,
                        'lang' => $language_code,
                        'search_unicode' => strtolower($this->Utilities->formatSearchUnicode([$name_translate]))
                    ];
                }

                $record_translate['lang'] = $language_code;
                $record_translate['search_unicode'] = strtolower($this->Utilities->formatSearchUnicode([$name_translate]));
                
                // set data_save
                $data_save['ContentMutiple'][] = $record_translate;
                $data_save['LinksMutiple'][] = [
                    'type' => PRODUCT_DETAIL,
                    'url' => $link_translate,
                    'lang' => $language_code,
                ];

                $associated = [
                    'ContentMutiple',
                    'LinksMutiple',
                    'CategoriesProduct',
                    'ProductsItem',
                    'ProductsAttribute',
                    'TagsRelation'
                ];
            }
        }

        $result = [];
        $products_table = TableRegistry::get('Products');
        $products_item_table = TableRegistry::get('ProductsItem');
        $tags_table = TableRegistry::get('Tags');

        $clear_items_id = [];

        // merge data with entity
        if(empty($id)){
            $entity = $products_table->newEntity($data_save, [
                'associated' => $associated
            ]);

        }else{
            $entity = $products_table->patchEntity($product_old, $data_save);

            // get old product_item_id
            $old_items_id = $products_item_table->find()->where(['product_id' => $id, 'deleted' => 0])->select('id')->toArray();

            // get new product_item_id save
            $new_items_id = [];
            if(!empty($data_save['ProductsItem'])){
                foreach($data_save['ProductsItem'] as $item){
                    if(!empty($item['id'])){
                        $new_items_id[] = intval($item['id']);
                    }                    
                }
            }
            
            foreach($old_items_id as $old){
                if(!in_array($old->id, $new_items_id)){
                    $clear_items_id[] = $old->id;
                }
            }
        }

        $products_item_attribute = !empty($data_save['products_item_attribute']) ? $data_save['products_item_attribute'] : [];
        if(isset($data_save['products_item_attribute'])){
            unset($data_save['products_item_attribute']);
        }
        
        $lang = !empty($data_save['ProductsContent']['lang']) ? $data_save['ProductsContent']['lang'] : null;
        
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            if(!empty($id)){
                TableRegistry::get('CategoriesProduct')->deleteAll(['product_id' => $id]);
                TableRegistry::get('ProductsAttribute')->deleteAll(['product_id' => $id]);
                TableRegistry::get('ProductsItemAttribute')->deleteAll(['product_id' => $id]);
                TableRegistry::get('TagsRelation')->deleteAll([
                    'foreign_id' => $id,
                    'type' => PRODUCT_DETAIL
                ]);

                if(!empty($clear_items_id)){
                    foreach($clear_items_id as $item_id){
                        $item_info = $products_item_table->find()->where(['ProductsItem.id' => $item_id])->first();
                        if(empty($item_info)) continue;

                        $exist_in_order = TableRegistry::get('OrdersItem')->checkItemProductExist($item_id);
                        if(!empty($exist_in_order)){
                            $entity_item = $products_item_table->patchEntity($item_info, ['deleted' => 1], ['validate' => false]);
                            $delete_item = $products_item_table->save($entity_item);
                        }else{
                            $delete_item = $products_item_table->delete($item_info);
                        }
                    }
                }
            }

            // save data
            $save = $products_table->save($entity);

            if (empty($save->id)){
                throw new Exception();
            }

            $products_item_saved = !empty($save['ProductsItem']) ? $save['ProductsItem'] : [];

            $data_attribute = [];
            if(count($products_item_saved) == count($products_item_attribute)){
                $product_id = $save->id;
                foreach($products_item_saved as $k_item => $item){
                    $product_item_id = $item->id;
                    if(!empty($products_item_attribute[$k_item])){
                        foreach($products_item_attribute[$k_item] as $item_attribute){

                            $data_attribute[] = [
                                'product_id' => $product_id,
                                'product_item_id' => !empty($item_attribute['product_item_id']) ? intval($item_attribute['product_item_id']) : $product_item_id,
                                'attribute_id' => !empty($item_attribute['attribute_id']) ? intval($item_attribute['attribute_id']) : null,
                                'value' => !empty($item_attribute['value']) ? $item_attribute['value'] : null,
                            ];
                        }
                    }
                }
            }
            
            if(!empty($data_attribute)){
                $attributes_entities = TableRegistry::get('ProductsItemAttribute')->newEntities($data_attribute);
                $save_attribute = TableRegistry::get('ProductsItemAttribute')->saveMany($attributes_entities, ['associated' => false]);

                if (empty($save_attribute)){
                    throw new Exception();
                }
            }

            $conn->commit();            
            return $this->System->getResponse([
                CODE => SUCCESS, 
                MESSAGE => __d('admin', 'cap_nhat_thong_tin_san_pham_thanh_cong'),
                DATA => $save
            ]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }

    public function saveManyProduct($data_save = [], $product_old = [])
    {
        $result = [];
        $products_table = TableRegistry::get('Products');
        $products_item_table = TableRegistry::get('ProductsItem');
        $tags_table = TableRegistry::get('Tags');
        
        if(empty($data_save)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_co_du_lieu')]);
        }

        $products_item_attribute = [];
        foreach ($data_save as $k_data_save => $v_data_save) {
            $products_item_attribute[] = !empty($v_data_save['products_item_attribute']) ? $v_data_save['products_item_attribute'] : [];
            if(isset($v_data_save['products_item_attribute'])){
                unset($data_save[$k_data_save]['products_item_attribute']);
            }
        }
        
        if(!empty($data_save) && empty($product_old)) {
            $product = $products_table->newEntities($data_save, [
                'associated' => ['ProductsContent', 'Links', 'CategoriesProduct', 'ProductsItem', 'ProductsAttribute', 'ProductsItemAttribute', 'TagsRelation']
            ]);
        }


        if(!empty($data_save) && !empty($product_old)) {
            $product = $products_table->patchEntities($product_old, $data_save);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            // save data
            $save = $products_table->saveMany($product);
            if (empty($save)){
                throw new Exception();
            }  

            $data_attribute = [];
            foreach ($save as $k_save => $v_save) {
                $products_item = !empty($v_save['ProductsItem']) ? $v_save['ProductsItem'] : [];
                
                if(count($products_item) == count($products_item_attribute[$k_save])){
                    if(!empty($products_item)) {
                        foreach ($products_item as $k_products_item => $product_item) {
                            $product_item_id = !empty($product_item['id']) ? $product_item['id'] : null;
                            $product_id = !empty($product_item['product_id']) ? $product_item['product_id'] : null;
                            if(!empty($products_item_attribute[$k_save][$k_products_item])) {
                                foreach ($products_item_attribute[$k_save][$k_products_item] as $k_item_attribute => $v_item_attribute) {
                                    $data_attribute[] = [
                                        'product_id' => $product_id,
                                        'product_item_id' => $product_item_id,
                                        'attribute_id' => !empty($v_item_attribute['attribute_id']) ? intval($v_item_attribute['attribute_id']) : null,
                                        'value' => isset($v_item_attribute['value']) ? $v_item_attribute['value'] : null
                                    ];
                                
                                }
                            }
                        }
                    }
                }
            }

            if(!empty($data_attribute)){
                $attributes_entities = TableRegistry::get('ProductsItemAttribute')->newEntities($data_attribute);
                $save_attribute = TableRegistry::get('ProductsItemAttribute')->saveMany($attributes_entities, ['associated' => false]);

                if (empty($save_attribute)){
                    throw new Exception();
                }
            }


            $conn->commit();            
            return $this->System->getResponse([
                CODE => SUCCESS
            ]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }
}
