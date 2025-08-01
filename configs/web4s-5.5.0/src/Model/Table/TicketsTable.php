<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Utility\Hash;
use Cake\Cache\Cache;
use Cake\Utility\Text;

class TicketsTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('tickets');
        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'always'
                ]
            ]
        ]);

        $this->belongsTo('User', [
            'className' => 'Publishing.Users',
            'foreignKey' => 'created_by',
            'joinType' => 'LEFT',
            'propertyName' => 'User'
        ]);
    }

    public function queryListTickets($params = []) 
    {
        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;
        $get_user = !empty($params['get_user']) ? $params['get_user'] : false;
        $get_parent = !empty($params['get_parent']) ? true : false;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $lang = !empty($filter[LANG]) ? $filter[LANG] : TableRegistry::get('Languages')->getDefaultLanguage();
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $department = !empty($filter['department']) ? $filter['department'] : null;
        $priority = !empty($filter['priority']) ? $filter['priority'] : null;
        $status = isset($filter['status']) && $filter['status'] != '' ? $filter['status'] : null;
       
        $ids = !empty($filter['ids']) && is_array($filter['ids']) ? $filter['ids'] : [];
        $not_ids = !empty($filter['not_ids']) && is_array($filter['not_ids']) ? $filter['not_ids'] : [];
        
        // fields select
        switch($field){
            case LIST_INFO:
                $fields = ['Tickets.id', 'Tickets.title'];
            break;

            case SIMPLE_INFO:
            default:
            case FULL_INFO:
                $fields = ['Tickets.id', 'Tickets.code', 'Tickets.full_name', 'Tickets.email', 'Tickets.phone', 'Tickets.title', 'Tickets.department', 'Tickets.priority', 'Tickets.content', 'Tickets.files', 'Tickets.status', 'Tickets.created_by', 'Tickets.created', 'Tickets.updated'];
            break;
        }

        $where = [
            'Tickets.deleted' => 0
        ];
        
        //contain        
        $contain = [];

        // filter by conditions  
        if (!$get_parent) {
            $where['Tickets.parent_id IS'] = NULL;
        }

        if(!empty($keyword)){
            $where['Tickets.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!empty($ids)){
            $where['Tickets.id IN'] = $ids;
        }

        if(!empty($not_ids)){
            $where['Tickets.id NOT IN'] = $not_ids;
        }

        if(!empty($department)){
            $where['Tickets.department'] = $department;
        }

        if(!empty($priority)){
            $where['Tickets.priority'] = $priority;
        }

        if(!is_null($status)){
            $where['Tickets.status'] = $status;
        }

        if(!empty($get_user)){
            $fields[] = 'User.id';
            $fields[] = 'User.full_name';

            $contain[] = 'User';
        }

        // sort by
        $sort_string = 'Tickets.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                case 'ticket_id':
                    $sort_string = 'Tickets.id '. $sort_type;
                break;

                case 'title':
                    $sort_string = 'Tickets.title '. $sort_type .', Tickets.id DESC';
                break;

                case 'status':
                    $sort_string = 'Tickets.status '. $sort_type .', Tickets.id DESC';
                break;

                case 'created':
                    $sort_string = 'Tickets.created '. $sort_type .', Tickets.id DESC';
                break;

                case 'updated':
                    $sort_string = 'Tickets.updated '. $sort_type .', Tickets.id DESC';
                break;

                case 'created_by':
                    $sort_string = 'Tickets.created_by '. $sort_type .', Tickets.id DESC';
                break;             
            }
        }

        return $this->find()->contain($contain)->where($where)->select($fields)->group('Tickets.id')->order($sort_string);
    }

    public function getDetailTicket($id = null, $params = [])
    {   
        if(empty($id)) return [];

        $get_parent = !empty($params['get_parent']) ? true : false;
           
        $where = [
            'Tickets.id' => $id,
            'Tickets.deleted' => 0
        ];

        if (!$get_parent) {
            $where['Tickets.parent_id IS'] = NULL;
        }

        $result = $this->find()->where($where)->first();
        return $result;
    }

    public function formatDataTicketDetail($data = [])
    {
        if (empty($data)) return [];

        $departments = [
            SALE => __d('admin', 'phong_kinh_doanh'),
            SUPPORT => __d('admin', 'phong_ky_thuat')
        ];

        $priorities = [
            LOW => __d('admin', 'thap'),
            MEDIUM => __d('admin', 'trung_binh'),
            HIGH => __d('admin', 'cao')
        ];

        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'parent_id' => !empty($data['parent_id']) ? intval($data['parent_id']) : null,
            'code' => !empty($data['code']) ? $data['code'] : null,
            'full_name' => !empty($data['full_name']) ? $data['full_name'] : null,
            'email' => !empty($data['email']) ? $data['email'] : null,
            'phone' => !empty($data['phone']) ? $data['phone'] : null,
            'title' => !empty($data['title']) ? $data['title'] : null,
            'department' => !empty($data['department']) ? $data['department'] : null,
            'department_name' => null,
            'priority' => !empty($data['priority']) ? $data['priority'] : null,
            'priority_name' => null,
            'content' => !empty($data['content']) ? $data['content'] : null,
            'files' => !empty($data['files']) ? json_decode($data['files'], true) : [],
            'status' => !empty($data['status']) ? $data['status'] : null,
            'created_by' => !empty($data['created_by']) ? intval($data['created_by']) : null,
            'created' => !empty($data['created']) ? $data['created'] : null,
            'user_full_name' => null
        ];

        if (!empty($data['department'])) {
            $result['department_name'] = !empty($departments[$data['department']]) ? $departments[$data['department']] : null;
        }

        if (!empty($data['priority'])) {
            $result['priority_name'] = !empty($priorities[$data['priority']]) ? $priorities[$data['priority']] : null;
        }

        if(!empty($data['User'])){
            $result['user_full_name'] = !empty($data['User']['full_name']) ? $data['User']['full_name'] : null;
        }

        return $result;
    }

    public function getListLogsTicket($id = null)
    {
        if(empty($id)) return [];

        $tickets = $this->find()->where([
            'OR' => [
                'id' => $id,
                'parent_id' => $id
            ],
            'deleted' => 0
        ])->order(['Tickets.created DESC'])->toArray();

        $result = [];
        if (!empty($tickets)) {
            foreach ($tickets as $key => $ticket) {
                $result[$key] = $this->formatDataTicketDetail($ticket);
            }
        }

        return $result;
    }
}