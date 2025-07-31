<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Cache\Cache;
use Cake\Utility\Text;
use Cake\View\View;

class MobileTemplateBlockController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        if(!defined('CODE_MOBILE_TEMPLATE') || empty(CODE_MOBILE_TEMPLATE)){            
            if(!$this->request->is('ajax')){
                $this->showErrorPage(ERROR, [
                    MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_giao_dien_mac_dinh')
                ]);
            }else{
                $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_giao_dien_mac_dinh')]);
            }
        }
    }

    public function list()
    {
        $this->js_page = '/assets/js/pages/list_mobile_template_block.js';

        $this->set('path_menu', 'mobile_app');
        $this->set('title_for_layout', __d('admin', 'danh_sach_block'));
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('MobileTemplateBlock');
        $utilities = $this->loadComponent('Utilities');

        $data = $params = $block = [];

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

        // params         
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;


        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        try {
            $block = $this->paginate($table->queryListMobileBlocks($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        } catch (Exception $e) {
            $page = 1;
            $block = $this->paginate($table->queryListMobileBlocks($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        if (!empty($block)) {
            $list_type = [
                PRODUCT => __d('admin', 'danh_sach_san_pham'),
                PRODUCT_DETAIL => __d('admin', 'chi_tiet_san_pham'),
                CATEGORY_PRODUCT => __d('admin', 'danh_muc_san_pham'),
                RATING => __d('admin', 'danh_gia'),
                ARTICLE => __d('admin', 'danh_sach_bai_viet'),      
                ARTICLE_DETAIL => __d('admin', 'chi_tiet_bai_viet'),
                CATEGORY_ARTICLE => __d('admin', 'danh_muc_bai_viet'),
                HTML => 'HTML',
                TEXT => 'TEXT',
                IMAGE => 'IMAGE',
                SLIDER => 'SLIDER'
            ];

            foreach ($block as $k => $item) {
                $block[$k]['type_label'] = null;

                if(!empty($list_type[$item['type']])){
                    $block[$k]['type_label'] = $list_type[$item['type']];
                }
            }
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['MobileTemplateBlock']) ? $this->request->getAttribute('paging')['MobileTemplateBlock'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $block, 
            META => $meta_info
        ]);
    }

    public function add()
    {
        $this->js_page = [
            '/assets/js/pages/mobile_template_block_add.js',
        ];

        $this->set('path_menu', 'mobile_app');
        $this->set('title_for_layout', __d('admin', 'them_block'));
        $this->render('add');
    }

    public function update($code = null)
    {
        $table = TableRegistry::get('MobileTemplateBlock');
        $block_info = $table->find()->where([
            'template_code' => CODE_MOBILE_TEMPLATE,
            'code' => $code
        ])->first();
        if(empty($block_info)) $this->showErrorPage();

        $config = !empty($block_info['config']) ? json_decode($block_info['config'], true) : [];
        $type = !empty($block_info['type']) ? $block_info['type'] : null;

        $elements = $table->getListElementByTypeBlock($type);
        $style_view = $table->getListStyleViewByTypeBlock($type);

        $this->set('block_info', $block_info);
        $this->set('config', $config);
        $this->set('config_data', !empty($config['data']) ? $config['data'] : []);
        $this->set('config_layout', !empty($config['layout']) ? $config['layout'] : []);
        $this->set('code', $code);
        $this->set('type', $type);

        $this->set('elements', $elements);
        $this->set('style_view', $style_view);
        
        $this->css_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css',
            '/assets/plugins/jquery-minicolors/css/jquery.minicolors.css'
        ];

        $this->js_page = [
            '/assets/js/mobile_block_config.js',
            '/assets/js/pages/mobile_template_block_update.js',
            '/assets/plugins/jquery-minicolors/js/jquery.minicolors.min.js',
            '/assets/plugins/global/ace/ace.js',
            '/assets/plugins/global/ace/theme-monokai.js',
            '/assets/plugins/global/ace/mode-json.js',
            '/assets/plugins/global/ace/mode-html.js',
            '/assets/plugins/global/ace/mode-smarty.js',
            '/assets/plugins/global/ace/ext-language_tools.js',

            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js'
        ];

        $this->set('path_menu', 'mobile_app');
        $this->set('title_for_layout', $code);
        $this->render('update');    
    }

    public function create()
    {
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $data = $this->getRequest()->getData();
        $type = !empty($data['type']) ? $data['type'] : null;
        $name = !empty($data['name']) ? $data['name'] : null;

        if(empty($type) || empty($name)){
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('MobileTemplateBlock');
        $utilities = $this->loadComponent('Utilities');

        if(!defined('CODE_MOBILE_TEMPLATE')){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_giao_dien')]);
        }

        $code = strtolower($utilities->generateRandomString(7));
        $data_save = [
            'template_code' => CODE_MOBILE_TEMPLATE,
            'code' => $code,
            'name' => $name,
            'type' => $type,
            'search_unicode' => strtolower($utilities->formatSearchUnicode([$name, $code]))
        ];

        $entity = $table->newEntity($data_save);
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();

            $this->responseJson([CODE => SUCCESS, DATA => ['code' => $save->code]]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function saveMainConfig($code = null)
    {
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $data = $this->getRequest()->getData();

        $table = TableRegistry::get('MobileTemplateBlock');
        $utilities = $this->loadComponent('Utilities');  

        $block = $table->find()->where([
            'template_code' => CODE_MOBILE_TEMPLATE,
            'code' => $code
        ])->first();
        if(empty($block)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_block')]);
        }

        $name = !empty($data['name']) ? $data['name'] : null;

        $data_save = [
            'name' => $name,
            'status' => !empty($data['status']) ? 1 : 0,
            'search_unicode' => strtolower($utilities->formatSearchUnicode([$name, $code]))
        ];

        $block = $table->patchEntity($block, $data_save);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = $table->save($block);
            if (empty($save->id)){
                throw new Exception();
            }
            $conn->commit();

            $this->responseJson([CODE => SUCCESS, DATA => ['code' => $save->code]]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function saveDataConfig($code = null)
    {
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $data = $this->getRequest()->getData();        
        $table = TableRegistry::get('MobileTemplateBlock');        

        $block = $table->find()->where([
            'template_code' => CODE_MOBILE_TEMPLATE,
            'code' => $code
        ])->first();
        $block_type = !empty($block['type']) ? $block['type'] : null;

        if(empty($block)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_block')]);
        }

        $config = !empty($block['config']) ? json_decode($block['config'], true) : [];
        $config['data'] = $data;

        $entity = $table->patchEntity($block, [
            'config' => json_encode($config)
        ]);

        // tạo view html.tpl
        if($block_type == HTML){
            $html_content = !empty($data['config']['html_content']) ? $data['config']['html_content'] : '';
            $path_template = SOURCE_DOMAIN  . DS . 'templates' . DS . 'mobile_' . CODE_MOBILE_TEMPLATE . DS;
            if(!file_exists($path_template . DS . BLOCK)) {
                @mkdir($path_template . DS . BLOCK, 0755, true);
            }

            if(!file_exists($path_template . DS . BLOCK . DS . HTML)) {
                @mkdir($path_template . DS . BLOCK . DS . HTML, 0755, true);
            }

            $dir_file = $path_template . DS . BLOCK . DS . HTML . DS . $code . '.tpl';
            $file = new File($dir_file, false);
            $file->write('{strip}' . $html_content . '{/strip}', 'w', true);
            $file->close();         
        }

        $conn = ConnectionManager::get('default');
        try{

            $conn->begin();
            $save = $table->save($entity);

            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();

            $this->responseJson([CODE => SUCCESS, DATA => [
                'code' => $code,
                'block_type' => $block_type
            ]]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function getHtmlAfterSave($code = '')
    {
        $this->autoRender = false;
        if(empty($code)) return '';

        $path_template = SOURCE_DOMAIN  . DS . 'templates' . DS . 'mobile_' . CODE_MOBILE_TEMPLATE . DS;
        Configure::write('App.paths.templates', $path_template);
        
        $view_builder = $this->viewBuilder();
        $view_builder->enableAutoLayout(false);
        $view_builder->setTemplatePath(BLOCK . DS . HTML);

        if(!defined('LANGUAGE')) define('LANGUAGE', LANGUAGE_DEFAULT);
        if(!defined('PATH_TEMPLATE')) define('LANGUAGE', $path_template);
        if(!defined('CODE_TEMPLATE'))define('LANGUAGE', CODE_MOBILE_TEMPLATE);

        $this->render($code);
    }

    public function genderHtmlAfterSave($code = '')
    {
        $this->autoRender = false;

        if (!$this->getRequest()->is('post') || empty($code)) $this->responseJson([CODE => SUCCESS]);

        $data = $this->getRequest()->getData();
        $html = !empty($data[HTML]) ? $data[HTML] : '';

        $path_template = SOURCE_DOMAIN  . DS . 'templates' . DS . 'mobile_' . CODE_MOBILE_TEMPLATE . DS;
       
        $dir_file = $path_template . DS . BLOCK . DS . HTML . DS . $code . '.html';
        $file = new File($dir_file, false);
        $file->write($html, 'w', true);
        $file->close();
        $this->responseJson([CODE => SUCCESS]);
    }

    public function saveLayoutConfig($code = null)
    {
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $data = $this->getRequest()->getData();

        $table = TableRegistry::get('MobileTemplateBlock');
        $block = $table->find()->where([
            'template_code' => CODE_MOBILE_TEMPLATE,
            'code' => $code
        ])->first();
        if(empty($block)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_block')]);
        }

        $config = !empty($block['config']) ? json_decode($block['config'], true) : [];
        $config['layout'] = $data;

        $entity = $table->patchEntity($block, [
            'config' => json_encode($config)
        ]);

        $conn = ConnectionManager::get('default');
        try{

            $conn->begin();
            $save = $table->save($entity);

            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();

            $this->responseJson([CODE => SUCCESS, DATA => ['code' => $code]]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function loadViewData()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        $data = $this->getRequest()->getData();

        $code = !empty($data['code']) ? $data['code'] : null;
        $data_type = !empty($data['data_type']) ? $data['data_type'] : null;

        $block_info = TableRegistry::get('MobileTemplateBlock')->getInfoBlock($code);        
        $block_type = !empty($block_info['type']) ? $block_info['type'] : null;

        $hidden_filter = false;
        if($block_type == CATEGORY_PRODUCT || $block_type == CATEGORY_ARTICLE){
            $hidden_filter = true;
        }

        $this->set('hidden_filter', $hidden_filter);
        $this->set('data_type', $data_type);
        $this->set('block_type', $block_type);
    }

    public function loadDropdownCategories()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $data = $this->getRequest()->getData();
        $type = !empty($data['type']) ? $data['type'] : null;
        $type = !empty($type) ? str_replace('category_', '', $type) : null;
        
        $this->set('type', $type);
        $this->set('lang', $this->lang);
    }

    public function loadCheckboxCategories()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $data = $this->getRequest()->getData();
        $type = !empty($data['type']) ? $data['type'] : null;
        $type = !empty($type) ? str_replace('category_', '', $type) : null;
        
        $this->set('type', $type);
        $this->set('lang', $this->lang);
    }

    public function loadEditorDataExtendSubMenu()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $this->render('load_editor_data_extend_sub_menu');
    }

    public function loadConfigTypeLoadOfBlock($type = null)
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $this->set('type_load', $type);
        $this->render('config_type_load');
    }

    public function changeStatus()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];
        $status = !empty($data['status']) ? 1 : 0;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('MobileTemplateBlock');

        $blocks = $table->queryListBlocks([FILTER => ['ids' => $ids]])->toArray();
        if(empty($blocks)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_bai_viet')]);
        }

        $patch_data = [];
        foreach ($ids as $k => $block_id) {
            $patch_data[] = [
                'id' => intval($block_id),
                'status' => $status
            ];
        }

        
        $data_blocks = $table->patchEntities($blocks, $patch_data);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $change_status = $table->saveMany($data_blocks);
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

    public function delete()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $ids = !empty($data['ids']) ? $data['ids'] : [];
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('MobileTemplateBlock');

        try{
            foreach($ids as $id){
                $block_info = $table->find()->where(['id' => $id])->select(['id'])->first();
                if(empty($block_info)) continue;                
                $table->delete($block_info);
            }            

            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'xoa_du_lieu_thanh_cong')]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function addElement($code = null)
    {
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $element_name = !empty($data['name']) ? $data['name'] : null;
        $element_code = !empty($data['code']) ? $data['code'] : null;
        $element_code = !empty($element_code) ? Text::slug(strtolower(strtolower($element_code)), '_') : null;
        if(empty($element_code) || empty($element_name)){
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $app_info = TableRegistry::get('MobileApp')->find()->first();
        if (empty($app_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_ton_tai_thong_tin_app')]);
        }

        $block_info = TableRegistry::get('MobileTemplateBlock')->find()->where([
            'template_code' => CODE_MOBILE_TEMPLATE,
            'code' => $code
        ])->select(['code', 'type'])->first();
        $block_type = !empty($block_info['type']) ? $block_info['type'] : null;
        if(empty($block_type)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_block')]);
        }

        $config = !empty($app_info['config']) ? json_decode($app_info['config'], true) : [];
        $element_extend = !empty($config['element_extend']) ? $config['element_extend'] : [];
        $element_extend[$block_type][$element_code] = $element_name;

        $config['element_extend'] = $element_extend;

        $entity = TableRegistry::get('MobileApp')->patchEntity($app_info, ['config' => json_encode($config)]);
        $save = TableRegistry::get('MobileApp')->save($entity);
        if (empty($save->id)) $this->responseJson([MESSAGE => __d('admin', 'cap_nhat_khong_thanh_cong')]);

        $this->responseJson([CODE => SUCCESS, 
            DATA => [
                'element_name' => $element_name,
                'element_code' => $element_code
            ]
        ]);
    }

    public function deleteElement($code = null)
    {
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $element_code = !empty($data['code']) ? $data['code'] : null;    
        if(empty($element_code)){
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $app_info = TableRegistry::get('MobileApp')->find()->first();
        if (empty($app_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_ton_tai_thong_tin_app')]);
        }

        $block_info = TableRegistry::get('MobileTemplateBlock')->find()->where([
            'template_code' => CODE_MOBILE_TEMPLATE,
            'code' => $code
        ])->select(['code', 'type'])->first();
        $block_type = !empty($block_info['type']) ? $block_info['type'] : null;
        if(empty($block_type)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_block')]);
        }

        $config = !empty($app_info['config']) ? json_decode($app_info['config'], true) : [];
        $element_extend = !empty($config['element_extend']) ? $config['element_extend'] : [];
        unset($element_extend[$block_type][$element_code]);

        $config['element_extend'] = $element_extend;
        
        $entity = TableRegistry::get('MobileApp')->patchEntity($app_info, ['config' => json_encode($config)]);
        $save = TableRegistry::get('MobileApp')->save($entity);
        if (empty($save->id)) $this->responseJson([MESSAGE => __d('admin', 'cap_nhat_khong_thanh_cong')]);

        $this->responseJson([CODE => SUCCESS]);
    }

    public function addStyleView($code = null)
    {
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $view_name = !empty($data['name']) ? $data['name'] : null;
        $view_code = !empty($data['code']) ? $data['code'] : null;
        $view_code = !empty($view_code) ? Text::slug(strtolower(strtolower($view_code)), '_') : null;
        if(empty($view_code) || empty($view_name)){
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $app_info = TableRegistry::get('MobileApp')->find()->first();
        if (empty($app_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_ton_tai_thong_tin_app')]);
        }

        $block_info = TableRegistry::get('MobileTemplateBlock')->find()->where([
            'template_code' => CODE_MOBILE_TEMPLATE,
            'code' => $code
        ])->select(['code', 'type'])->first();
        $block_type = !empty($block_info['type']) ? $block_info['type'] : null;
        if(empty($block_type)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_block')]);
        }

        $config = !empty($app_info['config']) ? json_decode($app_info['config'], true) : [];
        $style_view_extend = !empty($config['style_view_extend']) ? $config['style_view_extend'] : [];
        $style_view_extend[$block_type][$view_code] = $view_name;

        $config['style_view_extend'] = $style_view_extend;

        $entity = TableRegistry::get('MobileApp')->patchEntity($app_info, ['config' => json_encode($config)]);
        $save = TableRegistry::get('MobileApp')->save($entity);
        if (empty($save->id)) $this->responseJson([MESSAGE => __d('admin', 'cap_nhat_khong_thanh_cong')]);

        $this->responseJson([CODE => SUCCESS, 
            DATA => [
                'view_name' => $view_name,
                'view_code' => $view_code
            ]
        ]);
    }

    public function deleteStyleView($code = null)
    {
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $view_code = !empty($data['code']) ? $data['code'] : null;    
        if(empty($view_code)){
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $app_info = TableRegistry::get('MobileApp')->find()->first();
        if (empty($app_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_ton_tai_thong_tin_app')]);
        }

        $block_info = TableRegistry::get('MobileTemplateBlock')->find()->where([
            'template_code' => CODE_MOBILE_TEMPLATE,
            'code' => $code
        ])->select(['code', 'type'])->first();
        $block_type = !empty($block_info['type']) ? $block_info['type'] : null;
        if(empty($block_type)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_block')]);
        }

        $config = !empty($app_info['config']) ? json_decode($app_info['config'], true) : [];
        $style_view_extend = !empty($config['style_view_extend']) ? $config['style_view_extend'] : [];
        unset($style_view_extend[$block_type][$view_code]);

        $config['style_view_extend'] = $style_view_extend;
        
        $entity = TableRegistry::get('MobileApp')->patchEntity($app_info, ['config' => json_encode($config)]);
        $save = TableRegistry::get('MobileApp')->save($entity);
        if (empty($save->id)) $this->responseJson([MESSAGE => __d('admin', 'cap_nhat_khong_thanh_cong')]);

        $this->responseJson([CODE => SUCCESS]);
    }

    public function getActionRedirect()
    {
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        $app_info = TableRegistry::get('MobileApp')->find()->first();
        
        $app_id = !empty($app_info['app_id']) ? $app_info['app_id'] : '';
        $code = !empty($data['page']) ? $data['page'] : null;
        $action_type = !empty($data['type'] ) ? $data['type'] : null;
        $form_code = !empty($data['form']) ? $data['form'] : null;
        $page_type = !empty($data['page_type']) ? $data['page_type'] : null;
        $category_product_id = !empty($data['category_product_id']) ? $data['category_product_id'] : null;
        $category_article_id = !empty($data['category_article_id']) ? $data['category_article_id'] : null;
        $product_ids = !empty($data['product_ids']) ? $data['product_ids'] : null;
        $article_ids = !empty($data['article_ids']) ? $data['article_ids'] : null;

        if(empty($code || $form_code) || empty($page_type || $action_type)){
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $url_result = '';
        $action_result = [];
        if($action_type == 'form'){
            $action_result['action'] = $action_type;
            $action_result['code'] = $form_code;

            $url_result = http_build_query([
                'code' => $form_code
            ]);
        }

        if($action_type == 'redirect'){ 
            $action_result = [
                'action'=> $action_type,
                'page_type' => $page_type,
                'code' => $code
            ];

            switch($page_type){
                case CATEGORY_PRODUCT:
                    $action_result['category_id'] = $category_product_id;
                break;            

                case CATEGORY_ARTICLE:
                    $action_result['category_id'] = $category_article_id;
                break;

                case PRODUCT_DETAIL:
                    $action_result['product_id'] = !empty($product_ids[0]) ? intval($product_ids[0]) : null;
                break;

                case ARTICLE_DETAIL:
                    $action_result['article_id'] = !empty($article_ids[0]) ? intval($article_ids[0]) : null;
                break;
            }

            $data_url = [
                'page_type' => $page_type,
                'category_id' => $category_product_id,
                'product_id' => $product_ids,
                'article_id' => $article_ids,
            ];
            $url_result = http_build_query($data_url);             
        }
        $url_result = $app_id .'://' . $action_type . '?' . $url_result;
        
        $this->responseJson([
            CODE => SUCCESS,
            DATA => [
                'action' => json_encode($action_result),
                'data_url' => $url_result
            ]
        ]);
    }
}