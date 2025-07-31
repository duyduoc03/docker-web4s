<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Hash;
use App\Service\BrandService;

class BrandController extends AppController {

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
            '/assets/js/pages/list_brand.js',
            '/assets/plugins/global/lightbox/lightbox.min.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js'
        ];

        $this->set('path_menu', 'brand');
        $this->set('title_for_layout', __d('admin', 'thuong_hieu'));   
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Brands');
        $utilities = $this->loadComponent('Utilities');

        $data = $params = $brands = [];

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
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];
        $params['get_user'] = true;

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;

        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        try {
            $brands = $this->paginate($table->queryListBrands($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();

        } catch (Exception $e) {
            $page = 1;
            $brands = $this->paginate($table->queryListBrands($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        // parse data before output
        $result = [];
        if(!empty($brands)){
            $languages = TableRegistry::get('Languages')->getList();
            foreach($brands as $k => $brand){
                $result[$k] = $table->formatDataBrandDetail($brand, $this->lang);
                
                // check multiple language
                $mutiple_language = [];
                if(!empty($languages)){
                    foreach($languages as $lang => $language){
                        if($lang == $this->lang && !empty($brand['name'])){
                            $mutiple_language[$lang] = true;

                        }else{
                            $content = TableRegistry::get('BrandsContent')->find()->where([
                                'brand_id' => !empty($brand['id']) ? intval($brand['id']) : null,
                                'lang' => $lang
                            ])->select(['name'])->first();
                            
                            $mutiple_language[$lang] = !empty($content['name']) ? true : false;
                        }                        
                    }
                }


                $result[$k]['mutiple_language'] = $mutiple_language;
            }
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Brands']) ? $this->request->getAttribute('paging')['Brands'] : [];
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
        $max_record = TableRegistry::get('Brands')->find()->select('id')->max('id');

        $this->set('position', !empty($max_record->id) ? $max_record->id + 1 : 1);
        $this->js_page = [
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/js/seo_analysis.js',
            '/assets/js/pages/brand.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js'
        ];
        $this->set('path_menu', 'brand');
        $this->set('title_for_layout', __d('admin', 'them_thuong_hieu'));
        $this->render('update');
    }

    public function update($id = null)
    {
        $brand = TableRegistry::get('Brands')->getDetailBrand($id, $this->lang, ['get_user' => true]);        
        $brand = TableRegistry::get('Brands')->formatDataBrandDetail($brand, $this->lang);
        if(empty($brand)) $this->showErrorPage();

        $this->set('position', !empty($brand['position']) ? $brand['position'] : 1);
        $this->set('id', $id);
        $this->set('brand', $brand);

        $this->js_page = [
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/plugins/diff-match-patch/diff-match-patch.js',
            '/assets/js/seo_analysis.js',
            '/assets/js/pages/brand.js',
            '/assets/js/log_record.js'
        ];

        $this->set('path_menu', 'brand');
        $this->set('title_for_layout', __d('admin', 'cap_nhat_thuong_hieu'));
    }

    public function detail($id = null)
    {
        if(empty($id)){
            $this->showErrorPage();
        }

        $table = TableRegistry::get('Brands');

        $brand_detail = $table->getDetailBrand($id, $this->lang, ['get_user' => true]);
        if(empty($brand_detail)){
            $this->showErrorPage();
        }

        $brand = $table->formatDataBrandDetail($brand_detail, $this->lang);

        $this->css_page = [
            '/assets/css/pages/wizard/wizard-4.css',
            '/assets/plugins/global/lightbox/lightbox.css'
        ];
        $this->js_page = [
            '/assets/plugins/global/lightbox/lightbox.min.js'
        ];

        $this->set('brand', $brand);
        $this->set('title_for_layout', __d('admin', 'chi_tiet_thuong_hieu'));
    }

    public function save($id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $utilities = TableRegistry::get('Utilities');

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $create_new = !empty($id) ? true : false;

        // định dạng lại data trước khi gọi service
        $data['admin_user_id'] = $this->Auth->user('id');

        $data['images'] = !empty($data['images']) && $utilities->isJson($data['images']) ? json_decode($data['images'], true) : [];
        $data['files'] = !empty($data['files']) && $utilities->isJson($data['files']) ? json_decode($data['files'], true) : [];
        $data['seo_keywords'] = !empty($data['seo_keyword']) && $utilities->isJson($data['seo_keyword']) ? array_column(json_decode($data['seo_keyword'], true), 'value') : [];
        unset($data['seo_keyword']);

        // cập nhật dữ liệu
        $service = new BrandService();
        $update_result = $service->updateBrand($id, $data, $this->lang);
        if(empty($update_result[CODE]) || $update_result[CODE] == ERROR){
            return $this->responseJson($update_result);
        }
        
        $brand_id = !empty($update_result[DATA]['id']) ? intval($update_result[DATA]['id']) : null;

        // dịch các ngôn ngữ khác
        if(empty($create_new) && !empty($brand_id)){
            $service->translateAfterCreateNew($brand_id, $this->lang);
        }
        
        return $this->responseJson([
            CODE => SUCCESS,
            DATA => [
                'id' => $brand_id
            ]
        ]);
    }

    public function rollbackLog()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $record_id = !empty($data['record_id']) ? intval($data['record_id']) : null;
        $version = !empty($data['version']) ? $data['version'] : null;
        if (!$this->getRequest()->is('post') || empty($record_id) || empty($version)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $log_record = TableRegistry::get('LogsUtilities')->getLogRecordByVersion(BRAND, $record_id, $version);
        $data_log = !empty($log_record['before_entity']) ? $log_record['before_entity'] : [];
        if(empty($data_log)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        $table = TableRegistry::get('Brands');

        $brand_info = $table->find()->contain([
            'ContentMutiple',
            'LinksMutiple'
        ])->where([
            'Brands.id' => $record_id,
            'Brands.deleted' => 0
        ])->first();

        if(empty($brand_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        $entity = $table->patchEntity($brand_info, $data_log);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
                 
            $save = $table->save($entity);
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

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $ids = !empty($data['ids']) ? $data['ids'] : [];
        if (!$this->getRequest()->is('post') || empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Brands');

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach($ids as $id){
                
                $brand = $table->get($id);
                if (empty($brand)) {
                    throw new Exception(__d('admin', 'khong_tim_thay_thong_tin_thuong_hieu'));
                }

                $brand = $table->patchEntity($brand, ['id' => $id, 'deleted' => 1], ['validate' => false]);
                $delete = $table->save($brand);
                if (empty($delete)){
                    throw new Exception();
                }

                // delete link
                $delete_link = TableRegistry::get('Links')->updateAll(
                    [  
                        'deleted' => 1
                    ],
                    [  
                        'foreign_id' => $id,
                        'type' => BRAND_DETAIL
                    ]
                );
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'xoa_du_lieu_thanh_cong')]);

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
        $status = !empty($data['status']) ? 1 : 0;

        if (!$this->getRequest()->is('post') || empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Brands');

        $brands = $table->find()->where([
            'Brands.id IN' => $ids,
            'Brands.deleted' => 0
        ])->select(['Brands.id', 'Brands.status'])->toArray();
        
        if(empty($brands)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_thuong_hieu')]);
        }

        $patch_data = [];
        foreach ($ids as $k => $brand_id) {
            $patch_data[] = [
                'id' => $brand_id,
                'status' => $status
            ];
        }

        $entities = $table->patchEntities($brands, $patch_data, ['validate' => false]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $change_status = $table->saveMany($entities);            
            if (empty($change_status)){
                throw new Exception();
            }
            
            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function changePosition()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $id = !empty($data['id']) ? intval($data['id']) : null;
        $value = !empty($data['value']) ? $data['value'] : 0;

        if(!$this->getRequest()->is('post') || empty($id)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Brands');
        $brand = $table->get($id);
        if(empty($brand)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        $brand = $table->patchEntity($brand, ['position' => $value], ['validate' => false]);

        try{
            $save = $table->save($brand);

            if (empty($save->id)){
                throw new Exception();
            }
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    public function autoSuggest()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Brands');
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $filter = !empty($data[FILTER]) ? $data[FILTER] : [];
        $filter[STATUS] = 1;
        
        $brands = $table->queryListBrands([
            FILTER => $filter,
            FIELD => LIST_INFO
        ])->limit(10)->toArray();

        // parse data before output
        $result = [];
        if(!empty($brands)){
            foreach($brands as $k => $brand){
                $result[$k] = $table->formatDataBrandDetail($brand, $this->lang);
            }
        }

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
        ]);
    }
}