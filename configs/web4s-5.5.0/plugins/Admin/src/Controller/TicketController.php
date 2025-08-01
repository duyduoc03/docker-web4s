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

class TicketController extends AppController {

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
            '/assets/js/pages/list_ticket.js',
            '/assets/plugins/global/lightbox/lightbox.min.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js'
        ];

        $this->set('path_menu', 'ticket');
        $this->set('title_for_layout', __d('admin', 'ticket_ho_tro'));   
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Tickets');
        $utilities = $this->loadComponent('Utilities');

        $data = $params = $tickets = [];

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
            $tickets = $this->paginate($table->queryListTickets($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();

        } catch (Exception $e) {
            $page = 1;
            $tickets = $this->paginate($table->queryListTickets($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $result = [];
        if (!empty($tickets)) {
            foreach ($tickets as $key => $ticket) {
                if (empty($ticket)) continue;

                $result[$key] = $table->formatDataTicketDetail($ticket);
            }
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Tickets']) ? $this->request->getAttribute('paging')['Tickets'] : [];
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
        // lấy thông tin tài login hiện tại
        $user_id = $this->Auth->user('id');
        $user_info = TableRegistry::get('Users')->getDetailUsers($user_id);

        $this->js_page = [
            '/assets/js/pages/ticket.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js'
        ];

        $this->set('path_menu', 'ticket');
        $this->set('user', $user_info);
        $this->set('title_for_layout', __d('admin', 'them_ticket'));
        $this->render('update');
    }

    public function detail($id = null)
    {
        if(empty($id)){
            $this->showErrorPage();
        }

        $table = TableRegistry::get('Tickets');

        $ticket = $table->getDetailTicket($id);
        $ticket = $table->formatDataTicketDetail($ticket);

        if(empty($ticket)){
            $this->showErrorPage();
        }

        // lấy thông tin danh sách lịch sử trả lời ticket
        $logs_ticket = $table->getListLogsTicket($id);

        $this->js_page = [
            '/assets/js/pages/ticket.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js'
        ];

        $this->set('id', $id);
        $this->set('ticket', $ticket);
        $this->set('logs_ticket', $logs_ticket);
        $this->set('title_for_layout', __d('admin', 'chi_tiet_ticket'));
    }

    public function save()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $result = $this->loadComponent('Admin.Ticket')->saveTicket($data);
        exit(json_encode($result));
    }

    public function reply($id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post') || empty($data) || empty($id)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $result = $this->loadComponent('Admin.Ticket')->saveTicket($data, $id);
        exit(json_encode($result));
    }

    public function uploadFiles()
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $data = $this->getRequest()->getData();
        $file = !empty($data['file']) ? $data['file'] : [];

        if(empty($file)) $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);

        $file_upload = [
            'type' => $file->getClientMediaType(),
            'size' => $file->getSize(),
            'error' => $file->getError(),
            'name' => $file->getClientFilename(),
            'tmp_name' => $file->getStream()->getMetadata('uri')
        ];

        $result = $this->loadComponent('Upload')->uploadToCdn($file_upload, 'my-ticket', [
            'origin_name' => true,
            'ignore_logo_attach' => true
        ]);

        $this->responseJson($result);
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

        $table = TableRegistry::get('Tickets');
        $tickets = $table->find()->where([
            'Tickets.id IN' => $ids,
            'Tickets.status IN' => ['done', 'close'],
            'Tickets.deleted' => 0
        ])->select(['Tickets.id', 'Tickets.deleted'])->toArray();
        
        if(empty($tickets)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_ticket_ho_tro')]);
        }

        if (count($ids) != count($tickets)) {
            $this->responseJson([MESSAGE => __d('admin', 'trang_thai_mot_so_ticket_khong_hop_le_vui_long_kiem_tra_lai')]);
        }
        die();

        $patch_data = [];
        foreach ($ids as $k => $ticket_id) {
            $patch_data[] = [
                'id' => $ticket_id,
                'deleted' => 1
            ];
        }
        
        $entities = $table->patchEntities($tickets, $patch_data);
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = $table->saveMany($entities);            
            if (empty($save)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'xoa_thong_tin_ticket_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }
}