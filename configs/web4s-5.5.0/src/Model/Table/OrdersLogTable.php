<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;
use Cake\Utility\Hash;

class OrdersLogTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('orders_log');

        $this->setPrimaryKey('id');  

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new'
                ]
            ]
        ]);

        $this->hasOne('OrdersContact', [
            'className' => 'Publishing.OrdersContact',
            'foreignKey' => 'order_id',
            'bindingKey' => 'order_id',
            'joinType' => 'LEFT',
            'propertyName' => 'OrdersContact'
        ]);

        $this->belongsTo('User', [
            'className' => 'Users',
            'foreignKey' => 'updated_by',
            'propertyName' => 'User'
        ]);
    }

    public function queryListOrdersLog($params = []) 
    {
        // get info params
        $get_contact = !empty($params['get_contact']) ? true : false;
        $get_user = !empty($params['get_user']) ? $params['get_user'] : false;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];

        $customer_id = !empty($filter['customer_id']) ? intval($filter['customer_id']) : null;
        $order_id = !empty($filter['order_id']) ? $filter['order_id'] : null;

        $fields = ['OrdersLog.id', 'OrdersLog.order_id', 'OrdersLog.status', 'OrdersLog.updated_by', 'OrdersLog.user_name', 'OrdersLog.created'];

        $sort_string = 'OrdersLog.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'OrdersLog.id '. $sort_type;
                break;

                case 'created':
                    $sort_string = 'OrdersLog.created '. $sort_type .', OrdersLog.id DESC';
                break;

                case 'updated_by':
                    $sort_string = 'OrdersLog.updated_by '. $sort_type .', OrdersLog.id DESC';
                break;             
            }
        }

        // filter by conditions
        $where = [];
        $contain = [];

        if(!empty($customer_id)){
            $where['OrdersLog.updated_by'] = $customer_id;
            $where['OrdersLog.is_admin'] = 0;
        }

        if(!empty($order_id)){
            $where['OrdersLog.order_id'] = $order_id;
        }

        if(!empty($get_contact)){
            $fields[] = 'OrdersContact.customer_id';
            $fields[] = 'OrdersContact.full_name';
            $fields[] = 'OrdersContact.phone';
            $fields[] = 'OrdersContact.email';

            $contain[] = 'OrdersContact';
        }

        if(!empty($get_user)){
            $fields[] = 'User.id';
            $fields[] = 'User.full_name';

            $contain[] = 'User';
        }

        return $this->find()->contain($contain)->where($where)->select($fields)->order($sort_string);
    }

    public function formatDataOrderLogDetail($data = [])
    {
        if (empty($data)) return [];

        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'order_id' => !empty($data['order_id']) ? intval($data['order_id']) : null,
            'status' => !empty($data['status']) ? $data['status'] : null,
            'created' => !empty($data['created']) ? intval($data['created']) : null,

            'updated_by' => !empty($data['updated_by']) ? intval($data['updated_by']) : null,
            'user_full_name' => null,

            'customer_id' => null,
            'full_name' => null,
            'phone' => null,
            'email' => null,

            'description' => null,
            'description_admin' => null,
        ];

        if (!empty($data['User'])) {
            $result['user_full_name'] = !empty($data['User']['full_name']) ? $data['User']['full_name'] : null;
        }

        if (!empty($data['user_name']) && AUTH_USER_ID == 10000) {
            $result['user_full_name'] = !empty($data['user_name']) ? $data['user_name'] : null;
        }

        if (!empty($data['OrdersContact'])) {
            $result['customer_id'] = !empty($data['OrdersContact']['customer_id']) ? $data['OrdersContact']['customer_id'] : null;
            $result['full_name'] = !empty($data['OrdersContact']['full_name']) ? $data['OrdersContact']['full_name'] : null;
            $result['phone'] = !empty($data['OrdersContact']['phone']) ? $data['OrdersContact']['phone'] : null;
            $result['email'] = !empty($data['OrdersContact']['email']) ? $data['OrdersContact']['email'] : null;
        }

        switch ($data['status']) {
            case NEW_ORDER:
                $description = __d('admin', 'ban_da_khoi_tao_don_hang_tren_website');
                $description_admin = __d('admin', 'khach_hang_{0}_da_khoi_tao_don_hang_tren_website', [$result['full_name']]);

                if (!empty($result['updated_by'])) {
                    $description = __d('admin', 'admin_da_khoi_tao_don_hang_tren_website');
                    $description_admin = __d('admin', 'admin_{0}_da_khoi_tao_don_hang_tren_website', [$result['user_full_name']]);
                }

                break;
            
            case CONFIRM:
                $description = __d('admin', 'don_hang_cua_ban_da_duoc_xac_nhan');
                $description_admin = __d('admin', 'admin_{0}_da_xac_nhan_don_hang', [$result['user_full_name']]);

                break;

            case PACKAGE:
                $description = __d('admin', 'don_hang_cua_ban_dang_duoc_dong_goi');
                $description_admin = __d('admin', 'admin_{0}_dang_xu_ly_dong_goi_don_hang', [$result['user_full_name']]);

                break;

            case EXPORT:
                $description = __d('admin', 'don_hang_cua_ban_da_xuat_kho');
                $description_admin = __d('admin', 'admin_{0}_da_xu_ly_xuat_kho_don_hang', [$result['user_full_name']]);

            case DONE:
                $description = __d('admin', 'don_hang_cua_ban_da_xu_ly_thanh_cong');
                $description_admin = __d('admin', 'admin_{0}_da_xac_nhan_xu_ly_don_hang_thanh_cong', [$result['user_full_name']]);

                break;

            case CANCEL:
                $description = __d('admin', 'ban_da_xac_nhan_huy_don_hang');
                $description_admin = __d('admin', 'khach_hang_{0}_da_xac_nhan_huy_don_hang', [$result['full_name']]);

                if (!empty($result['updated_by'])) {
                    $description = __d('admin', 'admin_da_xac_nhan_huy_don_hang');
                    $description_admin = __d('admin', 'admin_{0}_da_xac_nhan_huy_don_hang', [$result['user_full_name']]);
                }

                break;
        }

        $result['description'] = !empty($description) ? $description : null;
        $result['description_admin'] = !empty($description_admin) ? $description_admin : null;

        return $result;
    }

    public function saveLog($order_id)
    {
        if (empty($order_id)) return false;
        $author_id = defined('AUTH_USER_ID') ? AUTH_USER_ID : null;
        $user_name = defined('AUTH_USER_NAME') ? AUTH_USER_NAME : null;

        $order_info = TableRegistry::get('Orders')->find()->where(['id' => $order_id])->first();
        if (empty($order_info)) return false;

        $status = !empty($order_info['status']) ? $order_info['status'] : null;
        if (!empty($order_info['status']) && $order_info['status'] == DRAFT) return false;

        // check xem đã có log đơn hàng vs trạng thái hiện tại chưa
        // nếu có rồi thì bỏ qua
        $order_log = $this->find()->where([
            'order_id' => $order_id,
            'status' => $status
        ])->first();
        if (!empty($order_log)) return true;

        $data_save = [
            'order_id' => $order_id,
            'status' => $status,
            'updated_by' => $author_id,
            'user_name' => $user_name
        ];

        $entity = $this->newEntity($data_save);
        $save = $this->save($entity);
        if (empty($save->id)) return false;

        return true;
    }
}