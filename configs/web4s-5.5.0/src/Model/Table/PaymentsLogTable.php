<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;
use Cake\Utility\Hash;

class PaymentsLogTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('payments_log');

        $this->setPrimaryKey('id');  

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new'
                ]
            ]
        ]);

        $this->belongsTo('User', [
            'className' => 'Users',
            'foreignKey' => 'updated_by',
            'propertyName' => 'User'
        ]);
    }

    public function queryListPaymentsLog($params = []) 
    {
        // get info params
        $get_user = !empty($params['get_user']) ? $params['get_user'] : false;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $payment_id = !empty($filter['payment_id']) ? $filter['payment_id'] : null;

        $fields = ['PaymentsLog.id', 'PaymentsLog.payment_id', 'PaymentsLog.status', 'PaymentsLog.amount', 'PaymentsLog.reference', 'PaymentsLog.note', 'PaymentsLog.updated_by', 'PaymentsLog.created'];

        $sort_string = 'PaymentsLog.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'PaymentsLog.id '. $sort_type;
                break;

                case 'created':
                    $sort_string = 'PaymentsLog.created '. $sort_type .', PaymentsLog.id DESC';
                break;

                case 'updated_by':
                    $sort_string = 'PaymentsLog.updated_by '. $sort_type .', PaymentsLog.id DESC';
                break;             
            }
        }

        // filter by conditions
        $where = [];
        $contain = [];

        if(!empty($payment_id)){
            $where['PaymentsLog.payment_id'] = $payment_id;
        }

        if(!empty($get_user)){
            $fields[] = 'User.id';
            $fields[] = 'User.full_name';

            $contain[] = 'User';
        }

        return $this->find()->contain($contain)->where($where)->select($fields)->order($sort_string);
    }

    public function formatDataPaymentLogDetail($data = [])
    {
        if (empty($data)) return [];

        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'payment_id' => !empty($data['payment_id']) ? intval($data['payment_id']) : null,
            'amount' => !empty($data['amount']) ? floatval($data['amount']) : null,
            'reference' => !empty($data['reference']) ? $data['reference'] : null,
            'note' => !empty($data['note']) ? $data['note'] : null,
            'status' => isset($data['status']) ? intval($data['status']) : null,
            'created' => !empty($data['created']) ? intval($data['created']) : null,

            'updated_by' => !empty($data['updated_by']) ? intval($data['updated_by']) : null,
            'user_full_name' => null,
        ];

        if (!empty($data['User'])) {
            $result['user_full_name'] = !empty($data['User']['full_name']) ? $data['User']['full_name'] : null;
        }

        return $result;
    }

    public function saveLog($payment_info = null)
    {
        if (empty($payment_info)) return false;
        $author_id = defined('AUTH_USER_ID') ? AUTH_USER_ID : null;

        $id = !empty($payment_info['id']) ? intval($payment_info['id']) : null;
        $amount = !empty($payment_info['amount']) ? floatval($payment_info['amount']) : null;
        $reference = !empty($payment_info['reference']) ? $payment_info['reference'] : null;
        $note = !empty($payment_info['note']) ? $payment_info['note'] : null;
        $status = isset($payment_info['status']) ? intval($payment_info['status']) : null;

        $data_save = [
            'payment_id' => $id,
            'amount' => $amount,
            'reference' => $reference,
            'note' => $note,
            'status' => $status,
            'updated_by' => $author_id,
        ];

        $entity = $this->newEntity($data_save);
        $save = $this->save($entity);
        if (empty($save->id)) return false;

        return true;
    }
}