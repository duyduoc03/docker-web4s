<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class ProductAdminHelper extends Helper
{   

    public function getDetailProductItem($product_item_id = null, $lang = null, $params = [])
    {
        if(empty($product_item_id) || empty($lang)) return [];

        $result = TableRegistry::get('ProductsItem')->getDetailProductItem($product_item_id, $lang, $params);
        return $result;
    }

    public function getDetailProduct($product_id = null, $lang = null, $params = [])
    {
        if(empty($product_id) || empty($lang)) return [];
        $product = TableRegistry::get('Products')->getDetailProduct($product_id, $lang, $params);
        $result = TableRegistry::get('Products')->formatDataProductDetail($product, $lang);
        
        return $result;
    }

    public function getAllNameContent($product_id = null)
    {
        if(empty($product_id)) return [];
        $result = TableRegistry::get('Products')->getAllNameContent($product_id);
        return $result;
    }

    public function formatDataItemProduct($items_before = [], $items_after = [])
    {   
        if(empty($items_before) || empty($items_after)) return [];
        
        $all_items = [];
        $data_item = [];
        foreach($items_before as $items_code => $item){ 
            $data_item['code']['before']['name'] = !empty($item['code']) ? __d('admin', 'ma') : null;
            $data_item['code']['before']['value'] = !empty($item['code']) ? $item['code'] : null;
            $data_item['price']['before']['name'] = !empty($item['price']) ? __d('admin', 'gia') : null;
            $data_item['price']['before']['value'] = !empty($item['price']) ? $item['price'] : null;
            $data_item['price_special']['before']['name'] = !empty($item['price_special']) ? __d('admin', 'gia_dac_biet') : null;
            $data_item['price_special']['before']['value'] = !empty($item['price_special']) ? $item['price_special'] : null;
            $data_item['images']['before']['name'] = !empty($item['images']) ? __d('admin', 'album') : null;
            $data_item['images']['before']['value'] = !empty($item['images']) ? $item['images'] : null;
            $data_item['quantity_available']['before']['name'] = !empty($item['quantity_available']) ? __d('admin', 'so_luong') : null;
            $data_item['quantity_available']['before']['value'] = !empty($item['quantity_available']) ? $item['quantity_available'] : null;
            $data_item['position']['before']['name'] = !empty($item['position']) ? __d('admin', 'vi_tri') : null;
            $data_item['position']['before']['value'] = !empty($item['position']) ? $item['position'] : null;
            $data_item['time_special']['before']['name'] = !empty($item['time_special']) ? __d('admin', 'thoi_gian_dien_ra') : null;
            $data_item['time_special']['before']['value'] = !empty($item['time_special']) ? $item['time_special'] : null;

            $all_items[$items_code] = $data_item; 
        }

        foreach($items_after as $items_code => $item){
            $data_item['code']['after']['name'] = !empty($item['code']) ? __d('admin', 'ma') : null;
            $data_item['code']['after']['value'] = !empty($item['code']) ? $item['code'] : null;
            $data_item['price']['after']['name'] = !empty($item['price']) ? __d('admin', 'gia') : null;
            $data_item['price']['after']['value'] = !empty($item['price']) ? $item['price'] : null;
            $data_item['price_special']['after']['name'] = !empty($item['price_special']) ? __d('admin', 'gia_dac_biet') : null;
            $data_item['price_special']['after']['value'] = !empty($item['price_special']) ? $item['price_special'] : null;
            $data_item['images']['after']['name'] = !empty($item['images']) ? __d('admin', 'album') : null;
            $data_item['images']['after']['value'] = !empty($item['images']) ? $item['images'] : null;
            $data_item['quantity_available']['after']['name'] = !empty($item['quantity_available']) ? __d('admin', 'so_luong') : null;
            $data_item['quantity_available']['after']['value'] = !empty($item['quantity_available']) ? $item['quantity_available'] : null;
            $data_item['position']['after']['name'] = !empty($item['position']) ? __d('admin', 'vi_tri') : null;
            $data_item['position']['after']['value'] = !empty($item['position']) ? $item['position'] : null;
            $data_item['time_special']['after']['name'] = !empty($item['time_special']) ? __d('admin', 'thoi_gian_dien_ra') : null;
            $data_item['time_special']['after']['value'] = !empty($item['time_special']) ? $item['time_special'] : null;

            $all_items[$items_code] = $data_item; 
        }
            
        return $all_items;
    }
}
