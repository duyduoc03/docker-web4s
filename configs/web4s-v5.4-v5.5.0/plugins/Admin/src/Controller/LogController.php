<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Utility\Hash;

class LogController extends AppController {
  
    public function initialize(): void
    {
        parent::initialize();        
    }

    public function list() 
    {
        $this->js_page = [
            '/assets/js/pages/list_log.js'
        ];

        $actions = [
            'add' => __d('admin', 'them_moi'),
            'update' => __d('admin', 'cap_nhat'),
            'update_status' => __d('admin', 'thay_doi_trang_thai'),
            'delete' => __d('admin', 'xoa')
        ];

        $list_type = [
            'data' => __d('admin', 'thay_doi_du_lieu'),
            'template' => __d('admin', 'sua_tep_giao_dien')
        ];

        $users = TableRegistry::get('Users')->find()->where(['deleted' => 0])->select(['id', 'full_name'])->toList();
        $users = Hash::combine($users, '{n}.id', '{n}.full_name');

        $this->set('actions', $actions);
        $this->set('list_type', $list_type);
        $this->set('users', $users);

        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'lich_su_cap_nhat')); 
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        
        $table = TableRegistry::get('Logs');
        $utilities = TableRegistry::get('Utilities');

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        // params
        $params = [
            'get_user' => true
        ];

        $filter = !empty($data[QUERY]) ? $data[QUERY] : [];
        

        // params query
        $params[QUERY] = !empty($data[QUERY]) ? $data[QUERY] : [];

        // params filter
        $params[FILTER] = !empty($data[DATA_FILTER]) ? $data[DATA_FILTER] : [];
        if(!empty($params[QUERY])){
            $params[FILTER] = array_merge($params[FILTER], $params[QUERY]);
        }

        // params sort
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];


        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : 50;

        // filter 
        $action = !empty($filter['action']) ? $filter['action'] : null;
        $type = !empty($filter['type']) ? $filter['type'] : null;
        $user_id = !empty($filter['user_id']) ? intval($filter['user_id']) : null;
        $create_from = $create_to = null;
        if(!empty($filter['create_from']) && $utilities->isDateClient($filter['create_from'])){
            $create_from = strtotime(str_replace('/', '-', $filter['create_from']));
        }

        if(!empty($filter['create_to']) && $utilities->isDateClient($filter['create_to'])){
            $create_to = strtotime(date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $filter['create_to']))));
        }

        try {
            $logs = $this->paginate($table->queryListLogs($params), [
                'limit' => $limit,
                'page' => $page
            ])->toArray();
        } catch (Exception $e) {
            $page = 1;
            $logs = $this->paginate($table->queryListLogs($params), [
                'limit' => $limit,
                'page' => $page
            ])->toArray();
        }            

        $pagination_info = !empty($this->request->getAttribute('paging')['Logs']) ? $this->request->getAttribute('paging')['Logs'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        // format data
        $result = [];
        if(!empty($logs)){
            foreach($logs as $log){
                $log = $table->formatDataLogDetail($log);
                if(empty($log)) continue;

                $result[] = $log;
            }
        }

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
            META => $meta_info
        ]);
    }

    public function listLogDataByRecord()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $data = $this->getRequest()->getData();
        $record_id = !empty($data['record_id']) ? intval($data['record_id']) : null;
        $record_code = !empty($data['record_code']) ? $data['record_code'] : null;
        $sub_type = !empty($data['sub_type']) ? $data['sub_type'] : null;    
        
        $table = TableRegistry::get('Logs');

        if(empty($record_id) && !empty($record_code) && $sub_type == TEMPLATE_PAGE){
            $record_info = TableRegistry::get('TemplatesPage')->find()->where([
                'code' => $record_code,
                'template_code' => CODE_TEMPLATE
            ])->select(['id'])->first();
            $record_id = !empty($record_info['id']) ? $record_info['id'] : null;
        }

        $get_log = true;
        if(empty($record_id) || empty($sub_type)) $get_log = false;

        $result = $pagination_info =[];
        if($get_log){
            // params
            $params = [
                'get_user' => true,
                FILTER => [
                    'type' => DATA,
                    'record_id' => $record_id,
                    'sub_type' => $sub_type
                ]
            ];
            // page and limit
            $page = !empty($data[PAGE]) ? intval($data[PAGE]) : 1;
            $limit = 10;
            
            // query by page
            try {
                $logs = $this->paginate($table->queryListLogs($params), [
                    'limit' => $limit,
                    'page' => $page
                ])->toArray();
            } catch (Exception $e) {
                $page = 1;
                $logs = $this->paginate($table->queryListLogs($params), [
                    'limit' => $limit,
                    'page' => $page
                ])->toArray();
            }
          
            $pagination_info = !empty($this->request->getAttribute('paging')['Logs']) ? $this->request->getAttribute('paging')['Logs'] : [];
            $pagination_info = TableRegistry::get('Utilities')->formatPaginationInfo($pagination_info);

            // format data            
            if(!empty($logs)){
                foreach($logs as $log){
                    $log = $table->formatDataLogDetail($log);
                    if(empty($log)) continue;

                    $result[] = $log;
                }
            }
        }
        
        $this->set('logs', $result);
        $this->set('pagination_info', $pagination_info);
        $this->render('list_log_data_by_record');
    }

    public function loadDataByVersion()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $data = $this->getRequest()->getData();

        $record_id = !empty($data['record_id']) ? intval($data['record_id']) : null;
        $record_code = !empty($data['record_code']) ? $data['record_code'] : null;
        $sub_type = !empty($data['sub_type']) ? $data['sub_type'] : null;
        $version = !empty($data['version']) ? $data['version'] : null;
        if(empty($record_id) && !empty($record_code) && $sub_type == TEMPLATE_PAGE){
            $record_info = TableRegistry::get('TemplatesPage')->find()->where([
                'code' => $record_code,
                'template_code' => CODE_TEMPLATE
            ])->select(['id'])->first();
            $record_id = !empty($record_info['id']) ? $record_info['id'] : null;
        }
        
        if(empty($record_id) || empty($sub_type) || empty($version)) die;
        $log_info = TableRegistry::get('LogsUtilities')->getLogRecordByVersion($sub_type, $record_id, $version);

        $before_entity = !empty($log_info['before_entity']) ? $log_info['before_entity'] : [];    
        $after_entity = !empty($log_info['entity']) ? $log_info['entity'] : [];

        $lang_log = $this->lang;
        $alias = TableRegistry::get('LogsUtilities')->getAliasBySubType($sub_type);

        // format data info
        $before_change = $after_change = [];
        switch($alias){
            case 'Articles':
                $before_change = TableRegistry::get('Articles')->formatDataArticleDetail($before_entity, $this->lang, MULTIPLE);
                $after_change = TableRegistry::get('Articles')->formatDataArticleDetail($after_entity, $this->lang, MULTIPLE);
            break;

            case 'Products':
                $before_change = TableRegistry::get('Products')->formatDataProductDetail($before_entity, $this->lang, MULTIPLE); 
                $after_change = TableRegistry::get('Products')->formatDataProductDetail($after_entity, $this->lang, MULTIPLE);
               
            break;

            case 'Brands':
                $before_change = TableRegistry::get('Brands')->formatDataBrandDetail($before_entity, $this->lang, MULTIPLE);
                $after_change = TableRegistry::get('Brands')->formatDataBrandDetail($after_entity, $this->lang, MULTIPLE);
            break;

            case 'Categories':
               $before_change = TableRegistry::get('Categories')->formatDataCategoryDetail($before_entity, $this->lang, MULTIPLE);
               $after_change = TableRegistry::get('Categories')->formatDataCategoryDetail($after_entity, $this->lang, MULTIPLE);
               
            break;

            case 'Authors':
               $before_change = TableRegistry::get('Authors')->formatDataAuthorDetail($before_entity, $this->lang, MULTIPLE);
               $after_change = TableRegistry::get('Authors')->formatDataAuthorDetail($after_entity, $this->lang, MULTIPLE);
               
            break;

            case 'TemplatesBlock':
            case 'TemplatesPage':
            default:
                $before_change = $before_entity;
                $after_change = $after_entity;
            break;
        }
        
        $view = 'load_data_by_version_' . $sub_type;
        
        $this->set('sub_type', $sub_type);
        $this->set('before_change', $before_change);
        $this->set('after_change', $after_change);

        $this->set('lang_log', $lang_log);
        
        $this->set('record_id', $record_id);
        $this->set('sub_type', $sub_type);
        $this->set('version', $version);

        $this->render($view);
    }

}