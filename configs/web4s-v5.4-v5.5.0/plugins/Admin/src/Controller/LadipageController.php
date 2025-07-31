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

class LadipageController extends AppController {

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
            '/assets/js/pages/list_ladipage.js',
            '/assets/plugins/global/lightbox/lightbox.min.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js'
        ];

        $this->set('path_menu', 'ladipage');
        $this->set('title_for_layout', __d('admin', 'Ladipage'));   
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Ladipages');
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
            $ladipages = $this->paginate($table->queryListLadipage($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();

        } catch (Exception $e) {
            $page = 1;
            $ladipages = $this->paginate($table->queryListLadipage($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        // parse data before output
        $result = [];
        if(!empty($ladipages)){
            foreach($ladipages as $k => $brand){
                $result[$k] = $table->formatDataLadipageDetail($brand, $this->lang);
            }
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Ladipages']) ? $this->request->getAttribute('paging')['Ladipages'] : [];
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
        $max_record = TableRegistry::get('Ladipages')->find()->select('id')->max('id');

        $this->set('position', !empty($max_record->id) ? $max_record->id + 1 : 1);
        $this->js_page = [
            '/assets/plugins/global/ace/ace.js',
            '/assets/plugins/global/ace/theme-monokai.js',
            '/assets/plugins/global/ace/mode-html.js',
            '/assets/js/seo_analysis.js',
            '/assets/js/pages/ladipage.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js'
        ];
        $this->set('path_menu', 'ladipage');
        $this->set('title_for_layout', __d('admin', 'Thêm landing'));
        $this->render('update');
    }

    public function update($id = null)
    {
        $ladipage = TableRegistry::get('Ladipages')->getDetailLadipage($id, $this->lang, ['get_user' => true]);        

        $ladipage = TableRegistry::get('Ladipages')->formatDataLadipageDetail($ladipage, $this->lang);

        if(empty($ladipage)){
            $this->showErrorPage();
        }

        $this->set('id', $id);
        $this->set('ladipage', $ladipage);

        $this->js_page = [
            '/assets/plugins/global/ace/ace.js',
            '/assets/plugins/global/ace/theme-monokai.js',
            '/assets/plugins/global/ace/mode-html.js',
            '/assets/js/seo_analysis.js',
            '/assets/js/pages/ladipage.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js'
        ];
        $this->set('path_menu', 'ladipage');
        $this->set('title_for_layout', __d('admin', 'Cập nhật landing'));
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

        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $utilities = $this->loadComponent('Utilities');
        $table = TableRegistry::get('Ladipages');        

        if(!empty($id)){
            $ladipage = $table->getDetailLadipage($id, $this->lang);

            if(empty($ladipage)){
                $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }
        }

        // validate data
        if(empty($data['name'])){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_tieu_de')]);
        }

        $link = !empty($data['link']) ? $utilities->formatToUrl(trim($data['link'])) : null;
        if(empty($link)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_duong_dan')]);
        }

        $link_id = !empty($ladipage['Links']) ? $ladipage['Links']['id'] : null;
        if(TableRegistry::get('Links')->checkExist($link, $link_id)){
            $this->responseJson([MESSAGE => __d('admin', 'duong_dan_da_ton_tai_tren_he_thong')]);
        }

        $status = isset($ladipage['status']) ? intval($ladipage['status']) : 1;
        $name = !empty($data['name']) ? trim(strip_tags($data['name'])) : null;
        
        $data_save = [
            'name' => $name,
            'content' => !empty($data['content']) ? $data['content'] : null,
            'status' => $status
        ];

        $data_save['Links'] = [
            'type' => LADI_DETAIL,
            'url' => $link,
            'lang' => $this->lang,
        ];

        // merge data with entity 
        if(empty($id)){
            $entity = $table->newEntity($data_save, [
                'associated' => ['Links']
            ]);
        }else{            
            $entity = $table->patchEntity($ladipage, $data_save);
        }

        // show error validation in model
        if($entity->hasErrors()){
            $list_errors = $utilities->errorModel($entity->getErrors());            
            $this->responseJson([
                MESSAGE => !empty($list_errors[0]) ? $list_errors[0] : null,
                DATA => $list_errors
            ]);
        }

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

        $table = TableRegistry::get('Ladipages');

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach($ids as $id){
                
                $ladi = $table->get($id);
                if (empty($ladi)) {
                    throw new Exception(__d('admin', 'khong_tim_thay_thong_tin_thuong_hieu'));
                }

                $ladi = $table->patchEntity($ladi, ['id' => $id, 'deleted' => 1], ['validate' => false]);
                $delete = $table->save($ladi);
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
                        'type' => LADI_DETAIL
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

        $table = TableRegistry::get('Ladipages');

        $ladi = $table->find()->where([
            'Ladipages.id IN' => $ids,
            'Ladipages.deleted' => 0
        ])->select(['Ladipages.id', 'Ladipages.status'])->toArray();
        
        if(empty($ladi)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_thuong_hieu')]);
        }

        $patch_data = [];
        foreach ($ids as $k => $ladi_id) {
            $patch_data[] = [
                'id' => $ladi_id,
                'status' => $status
            ];
        }

        $entities = $table->patchEntities($ladi, $patch_data, ['validate' => false]);

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

    public function publishLP()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (empty($data['ladipage_key'])) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $ladipage_key = $data['ladipage_key'];

        $result = $this->loadComponent('Ladipage')->publishLadiPage($ladipage_key);

        $this->responseJson($result);
    }
}