<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Cake\Datasource\ConnectionManager;

class WheelFortuneController extends AppController {

    public function list()
    {
        $this->css_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];
        $this->js_page = [            
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/js/pages/list_wheel_fortune.js'

        ];

        $this->set('path_menu', 'wheel_fortune');
        $this->set('title_for_layout', __d('admin', 'danh_sach_vong_quay'));
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('WheelFortune');
        $utilities = $this->loadComponent('Utilities');

        $data = $params = $wheel_fortune = [];

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
        $params['get_empty_name'] = true;

        
        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;


        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        try {
            $wheel_fortune = $this->paginate($table->queryListWheelFortune($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        } catch (Exception $e) {
            $page = 1;
            $wheel_fortune = $this->paginate($table->queryListWheelFortune($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        // parse data before output
        $result = [];
        if(!empty($wheel_fortune)){
            $languages = TableRegistry::get('Languages')->getList();
            foreach($wheel_fortune as $k => $wheel_fortune){
                $result[$k] = $table->formatDataWheelFortune($wheel_fortune, $this->lang);
                
                // check multiple language
                $mutiple_language = [];
                if(!empty($languages)){
                    foreach($languages as $lang => $language){
                        if($lang == $this->lang && !empty($wheel_fortune['name'])){
                            $mutiple_language[$lang] = true;

                        }else{
                            $content = TableRegistry::get('WheelFortuneContent')->find()->where([
                                'wheel_id' => !empty($wheel_fortune['id']) ? intval($wheel_fortune['id']) : null,
                                'lang' => $lang
                            ])->select(['name'])->first();
                            
                            $mutiple_language[$lang] = !empty($content['name']) ? true : false;
                        }                        
                    }
                }


                $result[$k]['mutiple_language'] = $mutiple_language;
            }
        }

        if(!empty($data['export'])) {
            // return $this->exportExcelwheelFortune($result);
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['WheelFortune']) ? $this->request->getAttribute('paging')['WheelFortune'] : [];
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
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css',
            '/assets/plugins/jquery-minicolors/css/jquery.minicolors.css'
        ];

        $this->js_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/plugins/jquery-minicolors/js/jquery.minicolors.min.js',
            '/assets/js/pages/wheel_fortune.js'
        ];

        $this->set('path_menu', 'wheel_fortune');
        $this->set('title_for_layout', __d('admin', 'them_vong_quay'));
        $this->render('update');
    }

    public function update($id = null)
    {
        $table = TableRegistry::get('WheelFortune');

        $wheel_fortune = $table->getDetailWheelFortune($id, $this->lang);
        if(empty($wheel_fortune)) $this->showErrorPage();

        $wheel_fortune = $table->formatDataWheelFortune($wheel_fortune, $this->lang);
        
        $this->css_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css',
            '/assets/plugins/jquery-minicolors/css/jquery.minicolors.css'
        ];

        $this->js_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/plugins/jquery-minicolors/js/jquery.minicolors.min.js',
            '/assets/js/pages/wheel_fortune.js'
        ];

        $this->set('path_menu', 'wheel_fortune');
        $this->set('id', $id);
        $this->set('wheel_fortune', $wheel_fortune);
        
        $this->set('title_for_layout', __d('admin', 'cap_nhat_vong_quay'));
    }

    public function detail($id = null)
    {
        if(empty($id)){
            $this->showErrorPage();
        }

        $table = TableRegistry::get('WheelFortune');

        $wheel_fortune_detail = $table->getDetailWeelFortune($id, $this->lang);

        if(empty($wheel_fortune_detail)){
            $this->showErrorPage();
        }

        $wheel_fortune = $table->formatDataWheelFortune($wheel_fortune_detail, $this->lang);

        $this->css_page = [
            '/assets/css/pages/wizard/wizard-4.css',
            '/assets/plugins/global/lightbox/lightbox.css'
        ];
        $this->js_page = [
            '/assets/plugins/global/lightbox/lightbox.min.js'
        ];

        $this->set('path_menu', 'wheel_fortune');
        $this->set('wheel_fortune', $wheel_fortune);
        $this->set('title_for_layout', __d('admin', 'chi_tiet_vong_quay'));
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
        $table = TableRegistry::get('WheelFortune');

        if(!empty($id)){
            $wheel_fortune = $table->getDetailWheelFortune($id, $this->lang);

            if(empty($wheel_fortune)){
                $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }
        }

        $options = !empty($data['options']) ? $data['options'] : [];

        // validate data
        if(empty($data['name'])){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_ten_vong_quay')]);
        }

        if(empty($options)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_giai_thuong')]);
        }

        if(count($options) < 2){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_them_giai_thuong')]);
        }

        // format data
        $data_save = [
            'winning_chance' => !empty($data['winning_chance']) ? intval($data['winning_chance']) : null,
            'check_limit' => !empty($data['check_limit']) ? 1 : 0,
            'config_email' => !empty($data['config_email']) ? json_encode($data['config_email']) : null,
            'config_behavior' => !empty($data['config_behavior']) ? json_encode($data['config_behavior']) : null,
            'check_ip' => !empty($data['check_ip']) ? 1 : 0,
            'start_time' => !empty($data['start_time']) ? strtotime(date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $data['start_time'])))) : null,
            'end_time' => !empty($data['end_time']) ? strtotime(date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $data['end_time'])))) : null,
            'status' => 1
        ];

        $name = !empty($data['name']) ? trim(strip_tags($data['name'])) : null;
        $data_content = [
            'name' => $name,
            'lang' => $this->lang,
            'search_unicode' => strtolower($utilities->formatSearchUnicode([$name]))
        ];
        
        // lay thong tin option cu
        if(!empty($id)) {
            $list_option = TableRegistry::get('WheelOptions')->find()->where(['wheel_id' => $id])->toArray();
            $old_option = Hash::combine($list_option, '{n}.id', '{n}.winning');
        }

        // lưu data option giai thuong
        $data_option = [];
        foreach ($options as $key => $option) {
            $option_id = !empty($option['id']) ? intval($option['id']) : null;
            $option['content'] = !empty($option['content']) ? json_encode($option['content']) : null;
            if(!empty($option['type_award']) && $option['type_award'] == 'nothing') $option['limit_prize'] = null;

            // lay thong tin luot quay thuong cu => data moi
            if(!empty($old_option) && !empty($option_id)) $option['winning'] = $old_option[$option_id];

            unset($option['id']);

            $data_option[] = $option;   
        }

        // translate
        $languages = TableRegistry::get('Languages')->getList();

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $setting_language = !empty($settings['language']) ? $settings['language'] : [];
        if(empty($id) && !empty($setting_language['auto_translate']) && count($languages) > 1){
            $data_save['ContentMutiple'][] = $data_content;
            $data_save['WheelOptionMutiple'] = $data_option;

            $translate_component = $this->loadComponent('Admin.Translate');
            foreach($languages as $language_code => $language){
                if($language_code == $this->lang) continue;
         
                // translate title
                $items = [];
                if (!empty($name)) $items['name'] = $name;

                if(empty($items)) continue;
                $translates = !empty($items) ? $translate_component->translate($items, $this->lang, $language_code) : [];
                 
                $name_translate = !empty($translates['name']) ? $translates['name'] : $name;

                // set value after translate
                $record_translate = [
                    'name' => $name_translate,
                    'lang' => $language_code,
                    'search_unicode' => strtolower($utilities->formatSearchUnicode([$name_translate]))
                ];

                if(!empty($setting_language['translate_all'])){
                    $record_translate = [
                        'name' => $name_translate,
                        'lang' => $language_code,
                        'search_unicode' => strtolower($utilities->formatSearchUnicode([$name_translate]))
                    ];
                }

                $record_translate['lang'] = $language_code;
                $record_translate['search_unicode'] = strtolower($utilities->formatSearchUnicode([$name_translate]));
                
                // set data_save
                $data_save['ContentMutiple'][] = $record_translate;
            }

            $associated = ['ContentMutiple', 'WheelOptionMutiple'];
            
        }else{
            $associated = ['WheelFortuneContent', 'WheelOptionMutiple'];
            $data_save['WheelFortuneContent'] = $data_content;
            $data_save['WheelOptionMutiple'] = $data_option;
        }
        

        // merge data with entity 
        if(empty($id)){
            $data_save['created_by'] = $this->Auth->user('id');

            $entity = $table->newEntity($data_save, [
                'associated' => $associated
            ]);
        }else{
            $entity = $table->patchEntity($wheel_fortune, $data_save);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            if(!empty($id)) $clear_options = TableRegistry::get('WheelOptions')->deleteAll(['wheel_id' => $id]);

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

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];

        if (!$this->getRequest()->is('post') || empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('WheelFortune');

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach($ids as $id){

                // delete wheel_fortune
                $wheel_fortune = $table->get($id);
                if (empty($wheel_fortune)) {
                    throw new Exception(__d('admin', 'khong_tim_thay_thong_tin_vong_quay'));
                }

                $wheel_fortune = $table->patchEntity($wheel_fortune, ['id' => $id, 'deleted' => 1], ['validate' => false]);
                $delete = $table->save($wheel_fortune);
                if (empty($delete)){
                    throw new Exception();
                }
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

        $table = TableRegistry::get('WheelFortune');

        $wheel_fortune = $table->find()->where([
            'WheelFortune.id IN' => $ids,
            'WheelFortune.deleted' => 0
        ])->select(['WheelFortune.id', 'WheelFortune.status'])->toArray();
        
        if(empty($wheel_fortune)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_vong_quay')]);
        }

        $patch_data = [];
        foreach ($ids as $k => $wheel_fortune_id) {
            $patch_data[] = [
                'id' => $wheel_fortune_id,
                'status' => $status
            ];
        }

        $entities = $table->patchEntities($wheel_fortune, $patch_data, ['validate' => false]);
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

        $table = TableRegistry::get('WheelFortune');
        $wheel_fortune = $table->get($id);
        if(empty($wheel_fortune)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        $wheel_fortune = $table->patchEntity($wheel_fortune, ['position' => $value], ['validate' => false]);

        try{
            $save = $table->save($wheel_fortune);

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
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('WheelFortune');
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $filter = !empty($data[FILTER]) ? $data[FILTER] : [];
        $filter[LANG] = $this->lang;
        
        $wheel_fortune = $table->queryListWheelFortune([
            FILTER => $filter,
            FIELD => FULL_INFO
        ])->limit(10)->toArray();

        $result = [];
        if(!empty($wheel_fortune)){
            foreach($wheel_fortune as $wheel_fortune){
                $result[] = $table->formatDataWheelFortune($wheel_fortune, $this->lang);
            }
        }
  
        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
        ]);
    }

    public function statistics($wheel_id = null)
    {
        $this->viewBuilder()->enableAutoLayout(false);

        if (!$this->getRequest()->is('post') || empty($wheel_id)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table_option = TableRegistry::get('WheelOptions');
        $table_log = TableRegistry::get('WheelFortuneLog');

        $query_total = $table_option->find()->where(['wheel_id' => $wheel_id]);
        $query_option = $table_option->find()->where(['wheel_id' => $wheel_id]);

        $winning = $query_total->select([
            'number_winning' => $query_total->func()->sum('winning'),
            'total_prize' => $query_total->func()->sum('limit_prize')
        ])->first();

        $option_winning = $query_option->select([
            'content',
            'limit_prize',
            'number_winning' => $query_option->func()->sum('winning')
        ])->group(['id'])->toArray();

        $prize_winning = [];
        foreach ($option_winning as $item) {
            $content = !empty($item['content']) ? json_decode($item['content'], true) : [];

            $prize_winning[] = [
                'prize_name' => !empty($content['name_' . $this->lang]) ? $content['name_' . $this->lang] : null,
                'limit_prize' => !empty($item['limit_prize']) ? intval($item['limit_prize']) : 0,
                'number_winning' => !empty($item['number_winning']) ? intval($item['number_winning']) : 0
            ];
        }

        $number_winning = !empty($winning['number_winning']) ? intval($winning['number_winning']) : 0;
        $total_prize = !empty($winning['total_prize']) ? intval($winning['total_prize']) : 0;

        $number_log = $table_log->find()->where(['wheel_id' => $wheel_id, 'lang' => $this->lang])->count();

        $rate_winning = $number_winning > 0 ? round($number_log/$number_winning * 100, 2) : 0;

        $this->set('prize_winning', $prize_winning);
        $this->set('number_log', $number_log);
        $this->set('number_winning', $number_winning);
        $this->set('total_prize', $total_prize);
        $this->set('rate_winning', $rate_winning);
        $this->render('element_statistics');
    }
}