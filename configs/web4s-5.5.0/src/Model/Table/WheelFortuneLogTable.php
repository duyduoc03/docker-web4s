<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Utility\Text;
use Cake\Utility\Hash;

class WheelFortuneLogTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('wheel_fortune_log');

        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'existing'
                ]
            ]
        ]);

        $this->belongsTo('WheelFortune', [
            'className' => 'WheelFortuneContent',
            'foreignKey' => 'wheel_id',
            'bindingKey' => 'id',
            'propertyName' => 'WheelFortune'
        ]);
    }

    public function queryListWheelFortuneLog($params = []) 
    {
        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $lang = !empty($filter[LANG]) ? $filter[LANG] : TableRegistry::get('Languages')->getDefaultLanguage();
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $wheel_id = !empty($filter['wheel_id']) ? intval($filter['wheel_id']) : null;
        $create_from = !empty($filter['create_from']) ? strtotime(date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $filter['create_from'])))) : null;
        $create_to = !empty($filter['create_to']) ? strtotime(date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $filter['create_to'])))) : null;

        // fields select
        switch($field){
            case FULL_INFO:
                $fields = ['WheelFortuneLog.id', 'WheelFortuneLog.wheel_id', 'WheelFortuneLog.full_name', 'WheelFortuneLog.phone', 'WheelFortuneLog.email', 'WheelFortuneLog.ip', 'WheelFortuneLog.winning', 'WheelFortuneLog.prize_name', 'WheelFortuneLog.prize_value', 'WheelFortuneLog.created', 'WheelFortuneLog.lang'];
            break;

            case LIST_INFO:
                $fields = ['WheelFortuneLog.id', 'WheelFortuneLog.prize_name'];
            break;

            case SIMPLE_INFO:
            default:
                $fields = ['WheelFortuneLog.id', 'WheelFortuneLog.wheel_id', 'WheelFortuneLog.full_name', 'WheelFortuneLog.phone', 'WheelFortuneLog.email', 'WheelFortuneLog.ip', 'WheelFortuneLog.winning', 'WheelFortuneLog.prize_name', 'WheelFortuneLog.prize_value', 'WheelFortuneLog.created', 'WheelFortuneLog.lang'];
            break;
        }

        $where = [];

        // filter by conditions  
        if(!empty($keyword)){
            $where['WheelFortuneLog.prize_name LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!empty($wheel_id)){
            $where['WheelFortuneLog.wheel_id'] = $wheel_id;
        }

        if(!empty($create_from)){
            $where['WheelFortuneLog.created >='] = $create_from;
        }

        if(!empty($create_to)){
            $where['WheelFortuneLog.created <='] = $create_to;
        }

        // sort by
        $sort_string = 'WheelFortuneLog.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                case 'wheel_id':
                    $sort_string = 'WheelFortuneLog.id '. $sort_type;
                break;

                case 'created':
                    $sort_string = 'WheelFortuneLog.created '. $sort_type .', WheelFortuneLog.id DESC';
                break;           
            }
        }

        return $this->find()->where($where)->select($fields)->order($sort_string);
    }

    public function getDetailWheelFortuneLog($id = null, $lang = null, $params = [])
    {
        $result = [];
        if(empty($id) || empty($lang)) return [];

        $where = [
            'WheelFortuneLog.id' => $id,
            'WheelFortuneLog.lang' => $lang
        ];

        $result = $this->find()->contain([])->where($where)->first();

        return $result;
    }

    public function formatDataWheelFortuneLog($data = [], $lang = null)
    {
        if(empty($data) || empty($lang)) return [];
            
        $wheel_id = !empty($data['wheel_id']) ? intval($data['wheel_id']) : null;
        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'wheel_id' => $wheel_id,
            'full_name' => !empty($data['full_name']) ? $data['full_name'] : null,
            'phone' => !empty($data['phone']) ? $data['phone'] : null,
            'email' => !empty($data['email']) ? $data['email'] : null,
            'ip' => !empty($data['ip']) ? $data['ip'] : null,
            'winning' => !empty($data['winning']) ? 1 : 0,
            'prize_name' => !empty($data['prize_name']) ? $data['prize_name'] : null,
            'prize_value' => !empty($data['prize_value']) ? $data['prize_value'] : null,
            'created' => !empty($data['created']) ? date('H:i - d/m/Y', $data['created']) : null,
            'lang' => !empty($data['lang']) ? $data['lang'] : null
        ];

        if(!empty($wheel_id)) {
            $wheel_info = TableRegistry::get('WheelFortuneContent')->find()->where(['wheel_id' => $wheel_id, 'lang' => $lang])->first();
            $result['wheel_name'] = !empty($wheel_info['name']) ? $wheel_info['name'] : null;
        }

        return $result;
    }
}