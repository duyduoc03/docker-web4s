<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class BrandHelper extends Helper
{   
    /** Lấy danh sách thương hiệu: id=>name
     * 
     * 
     * $lang (*): Mã ngôn ngữ(string)  - ví dụ: 'en'| 'vi'
     * 
     * {assign var = data value = $this->Brand->getListBrands({LANGUAGE})}
     * 
     * 
    */
    public function getListBrands($params = [], $lang = null)
    {
        $languages = TableRegistry::get('Languages')->getList();
        if(empty($lang) || empty($languages[$lang])) $lang = LANGUAGE;

    	$brands = TableRegistry::get('Brands')->getListBrands($lang);
        return !empty($brands) ? $brands : [];
    }


    /** Lấy danh sách thương hiệu
     * 
     * $params['get_user']: lấy thông tin của nhân viên - ví dụ: 'true'| 'false'
     * 
     * 
     * $params[{FIELD}]: Lấy các trường thông tin ví dụ: FULL_INFO | LIST_INFO | SIMPLE_INFO mặc định là SIMPLE_INFO
     * 
     * 
     * $params[{FILTER}]: lọc theo điều kiện truyền vào
     * 
     * 
     * $params[{FILTER}][{KEYWORD}]: lọc theo từ khóa
     * $params[{FILTER}]['ids']: lọc theo danh sách ID thương hiệu - ví dụ: [5, 6, 9, 10] - chú ý truyền dạng mảng "[]"
     * $params[{FILTER}]['not_ids']: lọc theo danh sách loại bỏ ID thương hiệu - ví dụ: [5, 6, 9, 10] - chú ý truyền dạng mảng "[]"
     * 
     * $params[{SORT}][{FIELD}]: sắp xếp dữ liệu theo field - ví dụ: id | brand_id | name | status | position | created | updated | created_by -  mặc định id
     * $params[{SORT}][{SORT}]: sắp xếp tăng dần hoặc giảm dần - ví dụ DESC | ASC - mặc định DESC
     * 
     * 
     * $lang (*): Mã ngôn ngữ(string)  - ví dụ: 'en'| 'vi'
     * 
     * {assign var = data value = $this->Brand->getBrands([
     *      'get_user' => true,
     *      {FIELD} => FULL_INFO,
     *      {FILTER} => [
                'ids' => [1, 4, 5]
     *      ],
     *      {SORT} => [
     *          {FIELD} => 'name',
     *          {SORT} => ASC
     *      ]
     * ], {LANGUAGE})}
     * 
     * 
    */
    public function getBrands($params = [], $lang = null) 
    {
        $result = [];

        if(empty($lang) || empty($languages[$lang])) $lang = LANGUAGE;

        $params[FILTER][STATUS] = 1;
        $params[FIELD] = FULL_INFO;
        $params[LANG] = $lang;

        $brands = TableRegistry::get('Brands')->queryListBrands($params)->toArray();

        if(!empty($brands)){
            foreach($brands as $k => $brand){
                $result[$k] = TableRegistry::get('Brands')->formatDataBrandDetail($brand, LANGUAGE);
            }
        }
        
        return $result;
    }

    /** Lấy chi tiết thương hiệu thông qua id
     * 
     * $id (*): ID thương hiệu(int) - ví dụ: {PAGE_RECORD_ID}
     * $lang (*): Mã ngôn ngữ(string)  - ví dụ: 'en'| 'vi'
     * $params['get_user']: lấy thông tin của nhân viên - ví dụ: 'true'| 'false'
     * 
     * 
     * {assign var = data value = $this->Brand->getInfoBrand({PAGE_RECORD_ID}, [
     *      'get_user' => true,
     *      {FILTER} => [
     *          {LANG} => LANGUAGE
     *      ],
     * ])}
     * 
    */
    public function getInfoBrand($id = null, $params = [])
    {
        if(empty($id)) return [];

        $table = TableRegistry::get('Brands');
        $lang = !empty($params[LANG]) ? $params[LANG] : LANGUAGE;
        $params[FILTER][STATUS] = 1;
    
        $brand = $table->getDetailBrand($id, $lang, $params);        
        if(empty($brand)) return [];

        return $table->formatDataBrandDetail($brand, $lang);
    }

    /** Lấy danh sách thương hiệu áp dụng cho danh mục
     * 
     * {PAGE_CATEGORY_ID} (*): id danh mục
     * $setting (*): Dữ liệu cấu hình thương hiệu áp dụng cho danh mục
     *
     * 
     * {assign var = data value = $this->Brand->getListBrandApplyCategory($setting, {PAGE_CATEGORY_ID})}
     * 
    */
    public function getListBrandApplyCategory($setting = [], $category_id = null, $lang = null)
    {
        $result = [];
        $languages = TableRegistry::get('Languages')->getList();
        if(empty($lang) || empty($languages[$lang])) $lang = LANGUAGE;
        
        $status = !empty($setting['status']) ? intval($setting['status']) : null;

        $apply_brands = !empty($setting['apply_brands']) ? json_decode($setting['apply_brands'], true) : [];
        $apply_brands = !empty($apply_brands[$category_id]) ? explode(',', $apply_brands[$category_id]) : [];

        if(empty($status) || empty($apply_brands)) return $result;

        $brands = TableRegistry::get('Brands')->getListBrands($lang);

        $apply_brands_selected =[];
        foreach ($apply_brands as $apply) {
            if(empty($brands[$apply])) continue;
            $apply_brands_selected[$apply] = $brands[$apply];
        }
       
        $format_brand = [];
        if(!empty($apply_brands_selected)) {
            foreach ($apply_brands_selected as $id_attr => $name) {
                $format_brand[] = [
                    'id' => $id_attr,
                    'name' => $name
                ];
            }
        }
        $result = $format_brand;

        return $result;
    }


    /** Lấy danh sách thương hiệu theo danh mục, dựa theo dữ liệu cập nhật sản phẩm
     * 
     * {PAGE_CATEGORY_ID} (*): id danh mục
     *
     * 
     * {assign var = data value = $this->Brand->getListBrandByCategory({PAGE_CATEGORY_ID})}
     * 
    */
    public function getListBrandByCategoryOld($category_id = null, $lang = null)
    {
        $result = [];
        if(empty($lang)) $lang = LANGUAGE;

        $query = sprintf("
            SELECT b.id, e.name, b.image_avatar, d.url
            FROM products a 
            inner join brands b on a.brand_id = b.id 
            inner join brands_content e on e.brand_id = b.id and e.lang ='%s'
            INNER JOIN links d on b.id = d.foreign_id and d.type = '%s'
            WHERE a.main_category_id = %d
            GROUP BY b.id;
        ", $lang, BRAND_DETAIL, $category_id);

        $brands = TableRegistry::get('Brands')->getConnection()->execute($query)->fetchAll('assoc');

        if (empty($brands)) return $result;

        foreach ($brands as $key => $value) {
            array_push($result, [
                'id' => $value['id'],
                'image_avatar' => $value['image_avatar'],
                'name' => $value['name'],
                'url' => $value['url']
            ]);
        }

        return $result;
    }

    public function getListBrandByCategory($category_id = null, $lang = null)
    {
        $result = [];
        if(empty($lang)) $lang = LANGUAGE;

        $string_cate = $category_id;

        $list_cate = TableRegistry::get('Categories')->find()->where(['parent_id' => $category_id, 'deleted' => 0])->select('id')->toList();
        if(!empty($list_cate)) {
            foreach ($list_cate as $key => $item) {
                if(empty($item['id'])) continue;
                $string_cate =  $string_cate . ', ' . $item['id'];
            }
        }

        $query = sprintf("
            SELECT b.id, e.name, b.image_avatar, d.url
            FROM products a 
            inner join brands b on a.brand_id = b.id 
            inner join brands_content e on e.brand_id = b.id and e.lang ='%s'
            INNER JOIN links d on b.id = d.foreign_id and d.type = '%s'
            inner join categories_product cp on a.id = cp.product_id
            WHERE cp.category_id IN (%s)
            GROUP BY b.id;
        ", $lang, BRAND_DETAIL, $string_cate);

        $brands = TableRegistry::get('Brands')->getConnection()->execute($query)->fetchAll('assoc');

        if (empty($brands)) return $result;

        foreach ($brands as $key => $value) {
            array_push($result, [
                'id' => $value['id'],
                'image_avatar' => $value['image_avatar'],
                'name' => $value['name'],
                'url' => $value['url']
            ]);
        }

        return $result;
    }
}