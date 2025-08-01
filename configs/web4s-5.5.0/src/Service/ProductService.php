<?php
namespace App\Service;

use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Exception\Exception;
use Cake\I18n\Time;

class ProductService extends AppService
{
    public function updateProduct($product_id = null, $data = [], $lang = '')
    {
        $utilities = TableRegistry::get('Utilities');
        $table = TableRegistry::get('Products');

        if(empty($lang)) $lang = TableRegistry::get('Languages')->getDefaultLanguage();
        $product_id = !empty($product_id) ? intval($product_id) : null;        
        if(empty($data) || !is_array($data) || empty($lang)) {
            return $utilities->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $create_new = true;

        // kiểm tra thông tin bài viết
        if(!empty($product_id)){
            $create_new = false;
            
            $product_info = $table->getDetailProduct($product_id, $lang, [
                'get_categories' => true,
                'get_tags' => true,
                'get_attributes' => true,
                'get_item_attributes' => true
            ]);

            if(empty($product_info)){
                return $utilities->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }
        }

        $data_save = [];

        // brand_id
        if(isset($data['brand_id'])){           
            $brand_id = !empty($data['brand_id']) ? intval($data['brand_id']) : null;
            // kiểm tra brand_id có hợp lệ
            if(!empty($brand_id)) {
                $brand_info = TableRegistry::get('Brands')->find()->where([
                    'id' => $brand_id,
                    'deleted' => 0
                ])->select(['id'])->first();

                if(empty($brand_info)) $brand_id = null;
            }

            $data_save['brand_id'] = $brand_id;
        }

        // url_video
        if(isset($data['url_video'])){
            $data_save['url_video'] = !empty($data['url_video']) && is_string($data['url_video']) ? $data['url_video'] : '';
        }

        // type_video
        if(isset($data['type_video'])){
            $data_save['type_video'] = !empty($data['type_video']) && is_string($data['type_video']) ? $data['type_video'] : null;
            
            // nếu url_video rỗng thì type_video cũng rỗng
            if(isset($data_save['url_video']) && empty($data_save['url_video'])) $data_save['type_video'] = null;
            if(!isset($data_save['url_video']) && empty($product_info['url_video'])) $data_save['type_video'] = null;
        }

        // width 
        if(isset($data['width'])){
            if (is_numeric($data['width'])) {
                $data_save['width'] = floatval($data['width']);
            } elseif (is_string($data['width'])) {
                $data_save['width'] = floatval(str_replace(',', '', $data['width']));
            } else {
                $data_save['width'] = null;
            }
        }

        // height 
        if(isset($data['height'])){
            if (is_numeric($data['height'])) {
                $data_save['height'] = floatval($data['height']);
            } elseif (is_string($data['height'])) {
                $data_save['height'] = floatval(str_replace(',', '', $data['height']));
            } else {
                $data_save['height'] = null;
            }
        }

        // length 
        if(isset($data['length'])){
            if (is_numeric($data['length'])) {
                $data_save['length'] = floatval($data['length']);
            } elseif (is_string($data['length'])) {
                $data_save['length'] = floatval(str_replace(',', '', $data['length']));
            } else {
                $data_save['length'] = null;
            }
        }

        // weight 
        if(isset($data['weight'])){
            if (is_numeric($data['weight'])) {
                $data_save['weight'] = floatval($data['weight']);
            } elseif (is_string($data['weight'])) {
                $data_save['weight'] = floatval(str_replace(',', '', $data['weight']));
            } else {
                $data_save['weight'] = null;
            }
        }

        // width_unit
        if(isset($data['width_unit'])){
            $data_save['width_unit'] = !empty($data['width_unit']) && is_string($data['width_unit']) ? $data['width_unit'] : null;

            // đặt giá trị mặc định
            if(!empty($data_save['width_unit']) && !in_array($data_save['width_unit'], ['cm', 'mm', 'm'])) {
                $data_save['width_unit'] = 'cm';
            }

            // nếu $width rỗng thì $width_unit cũng rỗng
            if(isset($data_save['width']) && empty($data_save['width'])) $data_save['width_unit'] = null;
            if(!isset($data_save['width']) && empty($product_info['width'])) $data_save['width_unit'] = null;
        }

        // height_unit
        if(isset($data['height_unit'])){
            $data_save['height_unit'] = !empty($data['height_unit']) && is_string($data['height_unit']) ? $data['height_unit'] : null;

            // đặt giá trị mặc định
            if(!empty($data_save['height_unit']) && !in_array($data_save['height_unit'], ['cm', 'mm', 'm'])) {
                $data_save['height_unit'] = 'cm';
            }

            // nếu $height rỗng thì $height_unit cũng rỗng
            if(isset($data_save['height']) && empty($data_save['height'])) $data_save['height_unit'] = null;
            if(!isset($data_save['height']) && empty($product_info['height'])) $data_save['height_unit'] = null;
        }

        // length_unit
        if(isset($data['length_unit'])){
            $data_save['length_unit'] = !empty($data['length_unit']) && is_string($data['length_unit']) ? $data['length_unit'] : null;

            // đặt giá trị mặc định
            if(!empty($data_save['length_unit']) && !in_array($data_save['length_unit'], ['cm', 'mm', 'm'])) {
                $data_save['length_unit'] = 'cm';
            }

            // nếu $length rỗng thì $length_unit cũng rỗng
            if(isset($data_save['length']) && empty($data_save['length'])) $data_save['length_unit'] = null;
            if(!isset($data_save['length']) && empty($product_info['length'])) $data_save['length_unit'] = null;
        }

        // weight_unit
        if(isset($data['weight_unit'])){
            $data_save['weight_unit'] = !empty($data['weight_unit']) && is_string($data['weight_unit']) ? $data['weight_unit'] : null;

            // đặt giá trị mặc định
            if(!empty($data_save['weight_unit']) && !in_array($data_save['weight_unit'], ['g', 'kg'])) {
                $data_save['weight_unit'] = 'g';
            }

            // nếu $weight rỗng thì $weight_unit cũng rỗng
            if(isset($data_save['weight']) && empty($data_save['weight'])) $data_save['weight_unit'] = null;
            if(!isset($data_save['weight']) && empty($product_info['weight'])) $data_save['weight_unit'] = null;
        }

        // files
        if(isset($data['files'])){
            if(empty($data['files']) || !is_array($data['files'])) $data['files'] = [];

            // loại bỏ đường dẫn không hợp lệ
            foreach($data['files'] as $k => $file){
                if(empty($file) || !is_string($file) || strpos($file, '/media/') !== 0) unset($data['files'][$k]);
            }
            $data['files'] = @array_values($data['files']);

            $data_save['files'] = !empty($data['files']) ? json_encode($data['files']) : null;
        }

        // position
        if(isset($data['position'])){
            if (is_numeric($data['position'])) {
                $data_save['position'] = intval($data['position']);
            } elseif (is_string($data['position'])) {
                $data_save['position'] = intval(str_replace(',', '', $data['position']));
            } else {
                $data_save['position'] = null;
            }

            if($data_save['position'] < 0) $data_save['position'] = 0;
        }

        // vat
        if(isset($data['vat'])){
            if (is_numeric($data['vat'])) {
                $data_save['vat'] = intval($data['vat']);
            } elseif (is_string($data['vat'])) {
                $data_save['vat'] = intval(str_replace(',', '', $data['vat']));
            } else {
                $data_save['vat'] = null;
            }
            if($data_save['vat'] < 0) $data_save['vat'] = 0;
        }

        // categories
        if(isset($data['categories'])){
            $all_categories = TableRegistry::get('Categories')->getAll(PRODUCT, $lang);
            $data_categories = [];
            $category_ids = !empty($data['categories']) && is_array($data['categories']) ? array_filter($data['categories']) : [];
            foreach($category_ids as $category_id){
                $category_id = !empty($category_id) ? intval($category_id) : null;
                if(empty($all_categories[$category_id])) continue;

                $data_categories[] = [
                    'product_id' => $product_id,
                    'category_id' => $category_id
                ];
            }

            $data_save['CategoriesProduct'] = $data_categories;
        }

        // main_category_id
        if(isset($data['main_category_id'])){
            $all_categories = TableRegistry::get('Categories')->getAll(PRODUCT, $lang);
            $main_category_id = !empty($data['main_category_id']) ? intval($data['main_category_id']) : null;

            if(empty($all_categories[$main_category_id])) $main_category_id = null;
            // nếu $main_category_id không có trong $categories thì lấy từ $categories đầu tiên
            if(empty($main_category_id) && !empty($data_categories)){
                $main_category_id = !empty($data_categories[0]['category_id']) ? intval($data_categories[0]['category_id']) : null;
            }

            $data_save['main_category_id'] = $main_category_id;
        }    

        // featured
        if(isset($data['featured'])){
            $data_save['featured'] = !empty($data['featured']) ? 1 : 0;
        }

        // catalogue
        if(isset($data['catalogue'])){
            $data_save['catalogue'] = !empty($data['catalogue']) ? 1 : 0;
        }
        
        // seo_score
        if(isset($data['seo_score'])) {
            $data_save['seo_score'] = !empty($data['seo_score']) && is_string($data['seo_score']) ? $data['seo_score'] : '';
        }
       
        // keyword_score
        if(isset($data['keyword_score'])) {
            $data_save['keyword_score'] = !empty($data['keyword_score']) && is_string($data['keyword_score']) ? $data['keyword_score'] : '';
        }

        // draft
        if(isset($data['draft'])){
            $draft = !empty($data['draft']) ? 1 : 0; 
            $data_save['draft'] = $draft;
        }

        // status
        if(isset($data['status'])){
            $status = !empty($data['status']) ? intval($data['status']) : 0;
            if(!in_array($status, [-1, 0, 1, 2])) $status = 0;

            $data_save['status'] = $status;
        }        

        // name
        if(isset($data['name'])){
            $name = !empty($data['name']) ? trim(strip_tags($data['name'])) : '';            
            if(empty($name)) {
                return $utilities->getResponse([MESSAGE => __d('admin', 'vui_long_nhap_tieu_de')]);
            }

            $data_save['ProductsContent']['name'] = $name;
            $data_save['ProductsContent']['search_unicode'] = strtolower($utilities->formatSearchUnicode([$name]));
        }
        
        // description
        if(isset($data['description'])) $data_save['ProductsContent']['description'] = !empty($data['description']) ? $data['description'] : '';
        
        // content
        if(isset($data['content'])) $data_save['ProductsContent']['content'] = !empty($data['content']) ? $data['content'] : '';

        // seo_title
        if(isset($data['seo_title'])) $data_save['ProductsContent']['seo_title'] = !empty($data['seo_title']) ? $data['seo_title'] : '';

        // seo_description
        if(isset($data['seo_description'])) $data_save['ProductsContent']['seo_description'] = !empty($data['seo_description']) ? $data['seo_description'] : '';

        // seo_keywords
        if(isset($data['seo_keywords'])) {
            $seo_keywords = '';
            if(!empty($data['seo_keywords']) && is_array($data['seo_keywords'])){
                $seo_keywords = implode(', ', array_filter($data['seo_keywords']));
            }

            $data_save['ProductsContent']['seo_keyword'] = $seo_keywords;
        }

        // chỉ cập nhật $lang khi có các tham số bên ProductsContent
        if(isset($data_save['ProductsContent'])) $data_save['ProductsContent']['lang'] = $lang;
        
        // link
        if(isset($data['link'])){
            $link = !empty($data['link']) ? $utilities->formatToUrl(trim($data['link'])) : '';
            if(empty($link)){
                return $utilities->getResponse([MESSAGE => __d('admin', 'duong_dan_khong_hop_le')]);
            }

            // tạo link không trùng khi thêm mới link
            if($create_new) $link = TableRegistry::get('Links')->getUrlUnique($link);

            // kiểm tra đường dẫn
            $link_id = !empty($product_info['Links']) ? $product_info['Links']['id'] : null;
            if(TableRegistry::get('Links')->checkExist($link, $link_id)){
                return $utilities->getResponse([MESSAGE => __d('admin', 'duong_dan_da_ton_tai_tren_he_thong')]);
            }

            $data_save['Links']['url'] = $link;
            $data_save['Links']['type'] = PRODUCT_DETAIL;
            $data_save['Links']['lang'] = $lang;
        }

        // attributes
        if(isset($data['attributes'])){
            $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($lang), '{n}.id', '{n}', '{n}.attribute_type');
            $all_attributes = !empty($all_attributes[PRODUCT]) ? $all_attributes[PRODUCT] : [];

            $data_attributes = [];
            if(!empty($data['attributes']) && is_array($data['attributes']) && !empty($all_attributes)){                
                foreach($data['attributes'] as $attribute){
                    $attribute_id = !empty($attribute['attribute_id']) ? intval($attribute['attribute_id']) : '';
                    $value = isset($attribute['value']) ? $attribute['value'] : null;
                    if(empty($all_attributes[$attribute_id])) continue;
                    if(is_array($value) || is_object($value)) continue;

                    $data_attributes[] = [
                        'attribute_id' => $attribute_id,
                        'value' => $value
                    ];
                }
            }

            $data_save['ProductsAttribute'] = $data_attributes;
        }

        // tags
        if(isset($data['tags'])){
            $data_tags = [];
            $tags = !empty($data['tags']) && is_array($data['tags']) ? $data['tags'] : [];

            $tag_service = new TagService();
            foreach ($tags as $tag) {
                $tag_result = $tag_service->getIdOrCreateNewTag($tag, $lang);
                $tag_id = !empty($tag_result[DATA]['id']) ? intval($tag_result[DATA]['id']) : null;
                if(empty($tag_id)) continue;
            
                $data_tags[] = [
                    'type' => PRODUCT_DETAIL,
                    'tag_id' => $tag_id,
                    'foreign_id' => $product_id
                ];
            }

            $data_save['TagsRelation'] = $data_tags;
        }

        //admin_user_id
        if(isset($data['admin_user_id']) && $create_new){
            $admin_user_id = !empty($data['admin_user_id']) ? intval($data['admin_user_id']) : null;
            $user_info = TableRegistry::get('Users')->getDetailUsers($admin_user_id);

            $data_save['created_by'] = !empty($user_info) ? $admin_user_id : null;
        }

        // items        
        if(isset($data['items'])){
            $items = !empty($data['items']) && is_array($data['items']) ? $data['items'] : [];

            $data_items = $list_code =[];
            $item_index = 0;
            foreach($items as $k => $item){
                $product_item_id = !empty($item['id']) ? intval($item['id']) : null;

                // product_code
                $code = !empty($item['code']) && is_string($item['code']) ? trim($item['code']) : null;
                if(empty($code)) $code = $utilities->generateRandomString(10);
                if(in_array($code, $list_code)){
                    return $utilities->getResponse([MESSAGE => __d('admin', 'ma_phien_ban_san_pham_khong_the_trung_nhau_vui_long_dieu_chinh_lai')]);
                }
                $list_code[] = $code;

                // price
                $price = 0;
                if(!empty($item['price']) && is_numeric($item['price'])){
                    $price = floatval($item['price']);
                }

                if(!empty($item['price']) && is_string($item['price'])){
                    $price = floatval(str_replace(',', '', $item['price']));
                }
                
                // price_special
                $price_special = 0;
                if(!empty($item['price_special']) && is_numeric($item['price_special'])){
                    $price_special = floatval($item['price_special']);
                }

                if(!empty($item['price_special']) && is_string($item['price_special'])){
                    $price_special = floatval(str_replace(',', '', $item['price_special']));
                }

                // nếu price=0 thì price_special = 0;
                if(empty($price)) $price_special = 0;

                if(!empty($price) && $price <= $price_special){
                    return $utilities->getResponse([MESSAGE => __d('admin', 'gia_dac_biet_cua_san_pham_phai_nho_hon_gia_ban')]);
                }

                //discount_percent
                $discount_percent = 0;            
                if(!empty($price) && !empty($price_special) && $price > $price_special){
                    $discount_percent = round(($price - $price_special) / $price * 100);
                }
                
                // quantity_available
                $quantity_available = 0;
                if(!empty($item['quantity_available']) && is_numeric($item['quantity_available'])){
                    $quantity_available = intval($item['price']);
                }

                if(!empty($item['quantity_available']) && is_string($item['quantity_available'])){
                    $quantity_available = intval(str_replace(',', '', $item['quantity_available']));
                }

                //time
                $time_start_special = !empty($item['time_start_special']) ? intval($item['time_start_special']) : null;
                $time_end_special = !empty($item['time_end_special']) ? intval($item['time_end_special']) : null;
                if($time_start_special > $time_end_special) $time_start_special = null;

                // images
                $images = !empty($item['images']) && is_array($item['images']) ? $item['images'] : [];
                foreach($images as $k_image => $image){
                    if(empty($image) || !is_string($image) || strpos($image, '/media/') !== 0) unset($images[$k_image]);
                }
                $images = !empty($images) ? @array_values($images) : [];

                $data_items[] = [
                    'id' => $product_item_id,
                    'code' => $code,
                    'price' => $utilities->formatToDecimal($price),
                    'discount_percent' => $utilities->formatToDecimal($discount_percent),
                    'price_special' => $utilities->formatToDecimal($price_special),
                    'time_start_special' => $time_start_special,
                    'time_end_special' => $time_end_special,
                    'quantity_available' => $quantity_available,
                    'position' => $item_index + 1,
                    'status' => 1,
                    'images' => !empty($images) ? json_encode($images) : null
                ];

                $item_index ++;
            }

            $data_save['ProductsItem'] = $data_items;            
        }

        // nếu thêm mới thì kiểm tra các thông tin bắt buộc
        // và đặt các giá trị mặc định khi tạo mới
        if($create_new){
            // kiểm tra thông tin
            if(empty($data_save['ProductsContent']['name'])){
                return $utilities->getResponse([MESSAGE => __d('admin', 'vui_long_nhap_tieu_de')]);
            }

            if(empty($data_save['Links']['url'])){
                return $utilities->getResponse([MESSAGE => __d('admin', 'duong_dan_khong_hop_le')]);
            }

            if(empty($data_save['ProductsItem'])){
                return $utilities->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_phien_ban_san_pham')]);
            }

            // đặt giá trị mặc định
            $data_save = $this->_setDefaultValueBeforeCreateNew($data_save);
        }else{

            // lấy item_id bị xóa khỏi sản phẩm khi cập nhật
            if(!empty($data_save['ProductsItem'])){
                $old_ids = TableRegistry::get('ProductsItem')->find()->where([
                    'product_id' => $product_id,
                    'deleted' => 0
                ])->select(['id'])->toArray();
                $old_ids = !empty($old_ids) ? Hash::extract($old_ids, '{n}.id') : [];
                $item_ids = !empty($data_save['ProductsItem']) ? Hash::extract($data_save['ProductsItem'], '{n}.id') : [];

                // lấy các id cũ không còn trong danh sách mới
                $clear_items_id = array_diff($old_ids, $item_ids);
            }
        }
 
        if(empty($data_save)){
            return $utilities->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        
        if($create_new){            
            $entity = $table->newEntity($data_save, [
                'associated' => ['ProductsContent', 'Links', 'CategoriesProduct', 'ProductsAttribute', 'TagsRelation', 'ProductsItem']
            ]);
        }else{
            $entity = $table->patchEntity($product_info, $data_save);
        }

        $conn = ConnectionManager::get('default');       
        try{
            $conn->begin();

            if(isset($data_save['CategoriesProduct']) && !$create_new){
                TableRegistry::get('CategoriesProduct')->deleteAll([
                    'product_id' => $product_id
                ]);
            }

            if(isset($data_save['TagsRelation']) && !$create_new){
                TableRegistry::get('TagsRelation')->deleteAll([
                    'foreign_id' => $product_id,
                    'type' => PRODUCT_DETAIL
                ]);
            }

            if(isset($data_save['ProductsAttribute']) && !$create_new){
                TableRegistry::get('ProductsAttribute')->deleteAll([
                    'product_id' => $product_id
                ]);
            }

            // nếu có cập nhật phiên bản sản phẩm thì sẽ xóa hết thuộc tính của phiên bản
            if(isset($data_save['ProductsItem']) && !$create_new){
                TableRegistry::get('ProductsItemAttribute')->deleteAll([
                    'product_id' => $product_id
                ]);
            }
            
            // xóa product items
            if(!empty($clear_items_id)){
                foreach($clear_items_id as $item_id){
                    $item_info = TableRegistry::get('ProductsItem')->find()->where(['ProductsItem.id' => $item_id])->first();
                    if(empty($item_info)) continue;

                    // nếu có trong đơn hàng thì chỉ xóa mềm, ngược lại thì xóa cứng
                    $exist_in_order = TableRegistry::get('OrdersItem')->checkItemProductExist($item_id);
                    if(!empty($exist_in_order)){
                        $entity_item = TableRegistry::get('ProductsItem')->patchEntity($item_info, ['deleted' => 1], ['validate' => false]);
                        TableRegistry::get('ProductsItem')->save($entity_item);
                    }else{
                        TableRegistry::get('ProductsItem')->delete($item_info);
                    }
                }
            }            
            
            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }
        
            // cập nhật thuộc tính mở rộng của phiên bản sau khi cập nhật
            $product_id = $save->id;
            if(isset($data_save['ProductsItem']) && isset($data['items_attribute']) && is_array($data['items_attribute'])){

                $items_saved = !empty($save['ProductsItem']) ? $save['ProductsItem'] : [];                
                if(count($items_saved) == count($data['items_attribute'])){
                    $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($lang), '{n}.id', '{n}', '{n}.attribute_type');
                    $all_attributes = !empty($all_attributes[PRODUCT_ITEM]) ? $all_attributes[PRODUCT_ITEM] : [];

                    $data_items_attribute = [];
                    foreach($items_saved as $k_item => $item){
                        $product_item_id = !empty($item['id']) ? intval($item['id']) : null;
                        $product_id = !empty($item['product_id']) ? intval($item['product_id']) : null;                        
                        $items_attributes = !empty($data['items_attribute'][$k_item]) ? $data['items_attribute'][$k_item] : [];
                        if(
                            empty($product_id) || 
                            empty($product_item_id) ||
                            empty($items_attributes) || 
                            !is_array($items_attributes)
                        ) continue;

                        foreach($items_attributes as $item_attribute){
                            $attribute_id = !empty($item_attribute['attribute_id']) ? intval($item_attribute['attribute_id']) : null;
                            $value = !empty($item_attribute['value']) ? $item_attribute['value'] : null;
                            if(empty($attribute_id) || empty($all_attributes[$attribute_id])) continue;
                            if(is_array($value) || is_object($value)) continue;

                            $data_items_attribute[] = [
                                'product_id' => $product_id,
                                'product_item_id' => $product_item_id,
                                'attribute_id' => $attribute_id,
                                'value' => $value
                            ];
                        } 
                    }
      
                    if(!empty($data_items_attribute)){
                        $entities = TableRegistry::get('ProductsItemAttribute')->newEntities($data_items_attribute);
                        $save_attribute = TableRegistry::get('ProductsItemAttribute')->saveMany($entities, ['associated' => false]);

                        if (empty($save_attribute)){
                            throw new Exception();
                        }
                    }
                }
            }

            $conn->commit();            

            return $utilities->getResponse([
                CODE => SUCCESS,
                MESSAGE => __d('admin', 'cap_nhat_thanh_cong'),
                DATA => [
                    'id' => $save->id
                ]
            ]);

        }catch (Exception $e) {
            $conn->rollback();

            return $utilities->getResponse([
                MESSAGE => __d('admin', 'cap_nhat_khong_thanh_cong')
            ]);
        }

    }

    private function _setDefaultValueBeforeCreateNew($data_save = [])
    {
        if(empty($data_save) || !is_array($data_save)) return [];

        // position
        if(!isset($data_save['position'])){
            $position = TableRegistry::get('Products')->find()->select('id')->max('id');
            $data_save['position'] = $position;
        }

        // seo_title
        if(!isset($data_save['ProductsContent']['name'])){
            // cập nhật seo_title bằng tên sản phẩm
            if(isset($data_save['ProductsContent']['name'])) $data_save['ProductsContent']['seo_title'] = $data_save['ProductsContent']['name'];
        }

        // status
        if(!isset($data_save['status'])){
            // mặc định trạng thái 1
            $data_save['status'] = 1;

            // nếu bài nháp thì status -> 0
            $draft = isset($data_save['draft']) ? $data_save['draft'] : 0;
            if(!empty($draft)) $data_save['status'] = 0;
            
            // nếu có cấu hình duyệt bài viết thì trạng thái về chờ duyệt
            $settings = TableRegistry::get('Settings')->getSettingWebsite();
            if(!empty($settings['approved_product']['approved']) && empty($draft)) $data_save['status'] = -1;   
        }

        return $data_save;
    }    

    // chỉ dùng để dịch các bản ghi sau khi vừa tạo mới
    public function translateAfterCreateNew($product_id = null, $lang = '')
    {
        $utilities = TableRegistry::get('Utilities');
            
        $languages = TableRegistry::get('Languages')->getList();
        $settings = TableRegistry::get('Settings')->getSettingWebsite();

        if(empty($settings['language']['auto_translate']) || count($languages) <= 1){
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'cap_nhat_thanh_cong')
            ]);
        }

        $product_id = !empty($product_id) ? intval($product_id) : null;
        if(empty($product_id)){
            return $utilities->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        $table = TableRegistry::get('Products');
        $product_info = $table->getDetailProduct($product_id, $lang, [
            'get_attributes' => true
        ]);
        if(empty($product_info)){
            return $utilities->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }
        
        $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($lang), '{n}.id', '{n}', '{n}.attribute_type');
        $all_attributes = !empty($all_attributes[PRODUCT]) ? Hash::combine($all_attributes[PRODUCT], '{n}.id', '{n}') : [];

        $link = !empty($product_info['Links']['url']) ? $product_info['Links']['url'] : null;
        $data_attribute = !empty($product_info['ProductsAttribute']) ? $product_info['ProductsAttribute'] : null;
        
        $name = !empty($product_info['ProductsContent']['name']) ? $product_info['ProductsContent']['name'] : '';
        $description = !empty($product_info['ProductsContent']['description']) ? $product_info['ProductsContent']['description'] : '';
        $content = !empty($product_info['ProductsContent']['content']) ? $product_info['ProductsContent']['content'] : '';
       
        $google_translate = new GoogleTranslateService();
        foreach($languages as $language_code => $language){
            if($language_code == $lang) continue;
            
            // translate title, description and content
            $items = [];
            if(!empty($name)) $items['name'] = $name;

            if(!empty($description) && strlen($description) <= 5000 && !empty($settings['language']['translate_all'])) {
                $items['description'] = $description;
            }

            if(!empty($content) && strlen($content) <= 5000 && !empty($settings['language']['translate_all'])) {
                $items['content'] = $content;
            }
            if(empty($items)) continue;
            $translates = !empty($items) ? $google_translate->translate($items, $lang, $language_code) : [];
            $name_translate = !empty($translates['name']) ? $translates['name'] : $name;
            
            // link translate
            $link_translate = $utilities->formatToUrl($name_translate);
            if(empty($link_translate)) continue;

            $link_translate = TableRegistry::get('Links')->getUrlUnique($link_translate);
            if($link_translate == $link) $link_translate .= '-1';

            $data_translate = [
                'name' => $name_translate,
                'description' => !empty($translates['description']) ? $translates['description'] : null,
                'content' => !empty($translates['content']) ? $translates['content'] : null,
                'link' => $link_translate,
                'seo_title' => $name_translate
            ];
            
            // translate attribute text richtext and text
            if(!empty($data_attribute) && !empty($settings['language']['translate_all'])){
                foreach($data_attribute as $key => $attribute_item){
                    $attribute_id = !empty($attribute_item['attribute_id']) ? $attribute_item['attribute_id'] : null;
                    $input_type = !empty($all_attributes[$attribute_id]['input_type']) ? $all_attributes[$attribute_id]['input_type'] : null;
                    $code = !empty($all_attributes[$attribute_id]['code']) ? $all_attributes[$attribute_id]['code'] : null;

                    if(empty($code) || empty($attribute_id) || !in_array($input_type, [RICH_TEXT, TEXT])) continue;

                    $value = !empty($attribute_item['value']) && $utilities->isJson($attribute_item['value']) ? json_decode($attribute_item['value'], true) : [];
                    if(empty($value) || empty($value[$lang])) continue;
                    $item_attribute[$code] = $value[$lang];
                    $translates_attribute = $google_translate->translate($item_attribute, $lang, $language_code);

                    $data_translate[$code] = !empty($translates_attribute[$code]) ? $translates_attribute[$code] : '';
                }               
            }

            $save_translate = $this->updateProduct($product_id, $data_translate, $language_code); 
            if(empty($save_translate[CODE]) || $save_translate[CODE] == ERROR){
                $message = !empty($save_translate[MESSAGE]) ? $save_translate[MESSAGE] . ' - ' . $language : __d('admin', 'cap_nhat_khong_thanh_cong');
                return $utilities->getResponse([MESSAGE => $message]);
            }    
        }

        return $utilities->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'cap_nhat_thanh_cong')
        ]);
    }

}
