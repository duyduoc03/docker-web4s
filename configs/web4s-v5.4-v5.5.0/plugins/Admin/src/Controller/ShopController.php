<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\I18n\Time;
use Cake\Datasource\ConnectionManager;
use Cake\Collection\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ShopController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function list()
    {
        $this->css_page = [
            '/assets/plugins/global/lightbox/lightbox.css',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [
            '/assets/plugins/global/lightbox/lightbox.min.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/js/pages/list_shop.js'            
        ];

        $this->set('path_menu', 'shop');
        $this->set('title_for_layout', __d('admin', 'he_thong_cua_hang'));
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Shops');
        $utilities = $this->loadComponent('Utilities');

        $data = $params = $shops = [];

        $limit = PAGINATION_LIMIT_ADMIN;
        $page = 1;
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        // params query
        $params[QUERY] = !empty($data[QUERY]) ? $data[QUERY] : [];  

        // params filter
        $params[FILTER] = !empty($data[DATA_FILTER]) ? $data[DATA_FILTER] : [];
        if(!empty($params[QUERY])){
            $params[FILTER] = array_merge($params[FILTER], $params[QUERY]);
        }

        $params[FILTER][LANG] = !empty($params[FILTER][LANG]) ? $params[FILTER][LANG] : TableRegistry::get('Languages')->getDefaultLanguage();

        // params         
        $params['get_empty_name'] = true;

        // params         
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];
        
        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;

        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;
        
        try {            
            $shops = $this->paginate($table->queryListShops($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();

        } catch (Exception $e) {
            $shops = $this->paginate($table->queryListShops($params), [
                'limit' => $limit,
                'page' => 1,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        // parse data before output
        $result = [];
        if(!empty($shops)){
            $languages = TableRegistry::get('Languages')->getList();
            foreach($shops as $k => $shop){
                $result[$k] = $table->formatDataShopDetail($shop, $this->lang);
                
                // check multiple language
                $mutiple_language = [];
                if(!empty($languages)){
                    foreach($languages as $lang => $language){
                        if($lang == $this->lang && !empty($shop['name'])){
                            $mutiple_language[$lang] = true;

                        }else{
                            $content = TableRegistry::get('ShopsContent')->find()->where([
                                'shop_id' => !empty($shop['id']) ? intval($shop['id']) : null,
                                'lang' => $lang
                            ])->select(['name'])->first();
                            
                            $mutiple_language[$lang] = !empty($content['name']) ? true : false;
                        }                        
                    }
                }


                $result[$k]['mutiple_language'] = $mutiple_language;
            }
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Shops']) ? $this->request->getAttribute('paging')['Shops'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
            META => $meta_info
        ]);
    }

    public function add()
    {
        $this->css_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [
            '/assets/plugins/custom/tinymce/tinymce.bundle.js',
            '/assets/js/seo_analysis.js',
            '/assets/js/pages/shop.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js'
        ];

        $this->set('position', !empty($max_record->id) ? $max_record->id + 1 : 1);
        $this->set('path_menu', 'shop');
        $this->set('title_for_layout', __d('admin', 'them_moi_cua_hang'));
        $this->render('update');
    }

    public function update($id = null)
    {
        $table = TableRegistry::get('Shops');    

        $shop = $table->getDetailShop($id, $this->lang, ['get_user' => true]);
        $shop = $table->formatDataShopDetail($shop, $this->lang);
        if(empty($shop)){
            $this->showErrorPage();
        }

        $this->set('id', $id);
        $this->set('position', !empty($shop['position']) ? $shop['position'] : 1);
        $this->set('shop', $shop);

        $this->css_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [
            '/assets/plugins/custom/tinymce/tinymce.bundle.js',
            '/assets/js/pages/shop.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js'
        ];

        $this->set('path_menu', 'shop');
        $this->set('title_for_layout', __d('admin', 'cap_nhat_cua_hang'));
    }

    public function save($id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
   
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $table = TableRegistry::get('Shops');
        $utilities = $this->loadComponent('Utilities');

        $shop_info = [];
        if(!empty($id)){
            $shop_info = $table->getDetailShop($id, $this->lang);

            if(empty($shop_info)){
                $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }
        }

        // validate data
        if(empty($data['name'])){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_ten_cua_hang')]);
        }

        if(empty($data['address'])){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_thong_tin_dia_chi')]);
        }

        $name = !empty($data['name']) ? trim($data['name']) : null;
        $phone = !empty($data['phone']) ? trim($data['phone']) : null;
        $hotline = !empty($data['hotline']) ? trim($data['hotline']) : null;
        $email = !empty($data['email']) ? trim($data['email']) : null;
        $address = !empty($data['address']) ? trim($data['address']) : null;
        $gmap = !empty($data['gmap']) ? trim($data['gmap']) : null;

        $data_save = [
            'city_id' => !empty($data['city_id']) ? intval($data['city_id']) : null,
            'district_id' => !empty($data['district_id']) ? intval($data['district_id']) : null,
            'position' => !empty($data['position']) ? intval($data['position']) : 0,
            'status' => 1,
            'deleted' => 0,
        ];
        
        $data_save['ShopsContent'] = [
            'name' => $name,
            'phone' => $phone,
            'hotline' => $hotline,
            'hours_operation' => !empty($data['hours_operation']) ? $data['hours_operation'] : null,
            'email' => $email,
            'address' => $address,
            'gmap' => $gmap,
            'lang' => $this->lang,
            'search_unicode' => strtolower($utilities->formatSearchUnicode([$name, $phone, $hotline, $email, $address]))
        ];

        // merge data with entity 
        if(empty($id)){
            $data_save['created_by'] = $this->Auth->user('id');
            $shop = $table->newEntity($data_save, [
                'associated' => ['ShopsContent']
            ]);
        }else{            
            $shop = $table->patchEntity($shop_info, $data_save);
        }

        // show error validation in model
        if($shop->hasErrors()){
            $list_errors = $utilities->errorModel($shop->getErrors());            
            $this->responseJson([
                MESSAGE => !empty($list_errors[0]) ? $list_errors[0] : null,
                DATA => $list_errors
            ]);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = $table->save($shop);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function delete()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Shops');
        $shops = $table->find()->where([
            'Shops.id IN' => $ids,
            'Shops.deleted' => 0
        ])->select(['Shops.id', 'Shops.deleted'])->toArray();
        
        if(empty($shops)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_cua_hang')]);
        }

        $patch_data = [];
        foreach ($ids as $k => $shop_id) {
            $patch_data[] = [
                'id' => $shop_id,
                'deleted' => 1
            ];
        }
        
        $entities = $table->patchEntities($shops, $patch_data);
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = $table->saveMany($entities);            
            if (empty($save)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'xoa_thong_tin_cua_hang_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function changeStatus()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];
        $status = !empty($data['status']) ? intval($data['status']) : 0;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Shops');
        $shops = $table->find()->where([
            'Shops.id IN' => $ids,
            'Shops.deleted' => 0
        ])->select(['Shops.id', 'Shops.status'])->toArray();
        
        if(empty($shops)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_cua_hang')]);
        }

        $patch_data = [];
        foreach ($ids as $k => $shop_id) {
            $patch_data[] = [
                'id' => $shop_id,
                'status' => $status
            ];
        }
        
        $data_entities = $table->patchEntities($shops, $patch_data);
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $change_status = $table->saveMany($data_entities);            
            if (empty($change_status)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_trang_thai_cua_hang_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }
}