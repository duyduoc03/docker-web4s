<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Utility\Text;
use Cake\Utility\Hash;

class WheelFortuneTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('wheel_fortune');

        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'existing'
                ]
            ]
        ]);

        $this->hasOne('WheelFortuneContent', [
            'className' => 'WheelFortuneContent',
            'foreignKey' => 'wheel_id',
            'propertyName' => 'WheelFortuneContent'
        ]);

        $this->hasMany('ContentMutiple', [
            'className' => 'WheelFortuneContent',
            'foreignKey' => 'wheel_id',
            'joinType' => 'LEFT',
            'propertyName' => 'ContentMutiple'
        ]);

        $this->hasOne('WheelOption', [
            'className' => 'WheelOptions',
            'foreignKey' => 'wheel_id',
            'propertyName' => 'WheelOption'
        ]);

        $this->hasMany('WheelOptionMutiple', [
            'className' => 'WheelOptions',
            'foreignKey' => 'wheel_id',
            'joinType' => 'LEFT',
            'propertyName' => 'WheelOptionMutiple',
            'sort' => ['WheelOptionMutiple.id' => 'ASC']
        ]);

        $this->belongsTo('User', [
            'className' => 'Users',
            'foreignKey' => 'created_by',
            'propertyName' => 'User'
        ]);
    }

    public function queryListWheelFortune($params = []) 
    {
        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;
        $get_user = !empty($params['get_user']) ? $params['get_user'] : false;
        $get_empty_name = !empty($params['get_empty_name']) ? true : false;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $lang = !empty($filter[LANG]) ? $filter[LANG] : TableRegistry::get('Languages')->getDefaultLanguage();
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $status = isset($filter['status']) && $filter['status'] != '' ? intval($filter['status']) : null;
        $ids = !empty($filter['ids']) && is_array($filter['ids']) ? $filter['ids'] : [];
        $not_ids = !empty($filter['not_ids']) && is_array($filter['not_ids']) ? $filter['not_ids'] : [];
        $create_from = !empty($filter['create_from']) ? strtotime(date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $filter['create_from'])))) : null;
        $create_to = !empty($filter['create_to']) ? strtotime(date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $filter['create_to'])))) : null;

        $author_id = !empty($filter['author_id']) ? intval($filter['author_id']) : null;
        // fields select
        switch($field){
            case FULL_INFO:
                $fields = ['WheelFortune.id', 'WheelFortune.winning_chance', 'WheelFortune.check_limit', 'WheelFortune.config_email', 'WheelFortune.config_behavior', 'WheelFortune.check_ip', 'WheelFortune.start_time', 'WheelFortune.end_time', 'WheelFortune.created_by', 'WheelFortune.created', 'WheelFortune.status', 'WheelFortuneContent.name', 'WheelFortuneContent.lang'];
            break;

            case LIST_INFO:
                $fields = ['WheelFortune.id', 'WheelFortuneContent.name'];
            break;

            case SIMPLE_INFO:
            default:
                $fields = ['WheelFortune.id', 'WheelFortune.winning_chance', 'WheelFortune.check_limit', 'WheelFortune.config_email', 'WheelFortune.config_behavior', 'WheelFortune.check_ip', 'WheelFortune.start_time', 'WheelFortune.end_time', 'WheelFortune.created_by', 'WheelFortune.created', 'WheelFortune.status', 'WheelFortuneContent.name', 'WheelFortuneContent.lang'];
            break;
        }

        $where = ['WheelFortune.deleted' => 0];
        
        //contain        
        if(!$get_empty_name){
            $contain = ['WheelFortuneContent'];

            $where['WheelFortuneContent.lang'] = $lang;
        }else{
            $contain = [
                'WheelFortuneContent' => function ($q) use ($lang) {
                    return $q->where([
                        'WheelFortuneContent.lang' => $lang
                    ]);
                }
            ];            
        }

        // filter by conditions  
        if(!empty($keyword)){
            $where['WheelFortuneContent.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!empty($author_id)){
            $where['WheelFortune.author_id'] = $author_id;
        }

        if(!empty($ids)){
            $where['WheelFortune.id IN'] = $ids;
        }

        if(!empty($not_ids)){
            $where['WheelFortune.id NOT IN'] = $not_ids;
        }

        if(!is_null($status)){
            $where['WheelFortune.status'] = $status;
        }

        if(!empty($create_from)){
            $where['WheelFortune.created >='] = $create_from;
        }

        if(!empty($create_to)){
            $where['WheelFortune.created <='] = $create_to;
        }

        if(!empty($get_user)){
            $fields[] = 'User.id';
            $fields[] = 'User.full_name';

            $contain[] = 'User';
        }

        // sort by
        $sort_string = 'WheelFortune.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                case 'wheel_id':
                    $sort_string = 'WheelFortune.id '. $sort_type;
                break;

                case 'name':
                    $sort_string = 'WheelFortuneContent.name '. $sort_type .', WheelFortune.position DESC, WheelFortune.id DESC';
                break;

                case 'status':
                    $sort_string = 'WheelFortune.status '. $sort_type .', WheelFortune.position DESC, WheelFortune.id DESC';
                break;

                case 'created':
                    $sort_string = 'WheelFortune.created '. $sort_type .', WheelFortune.position DESC, WheelFortune.id DESC';
                break;

                case 'updated':
                    $sort_string = 'WheelFortune.updated '. $sort_type .', WheelFortune.position DESC, WheelFortune.id DESC';
                break;

                case 'created_by':
                    $sort_string = 'WheelFortune.created_by '. $sort_type .', WheelFortune.position DESC, WheelFortune.id DESC';
                break;             
            }
        }

        return $this->find()->contain($contain)->where($where)->select($fields)->order($sort_string);
    }

    public function getDetailWheelFortune($id = null, $lang = null, $params = [])
    {
        $result = [];
        if(empty($id) || empty($lang)) return [];

        $get_user = !empty($params['get_user']) ? true : false;
        $status = !empty($params['status']) ? intval($params['status']) : null;

        $contain = [
            'WheelFortuneContent' => function ($q) use ($lang) {
                return $q->where([
                    'WheelFortuneContent.lang' => $lang
                ]);
            },
            'WheelOptionMutiple'
        ];

        $where = [
            'WheelFortune.id' => $id,
            'WheelFortune.deleted' => 0,
        ];

        if(!is_null($status)) {
            $where['WheelFortune.status'] = $status;
        }

        if($get_user){
            $contain[] = 'User';
        }
        
        $result = $this->find()->contain($contain)->where($where)->first();

        return $result;
    }

    public function formatDataWheelFortune($data = [], $lang = null, $type_format = null)
    {
        if(empty($data) || empty($lang)) return [];
        if(empty($type_format) || !in_array($type_format, [SINGLE, MULTIPLE])) $type_format = SINGLE;
        
        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'winning_chance' => !empty($data['winning_chance']) ? intval($data['winning_chance']) : null,
            'check_limit' => !empty($data['check_limit']) ? intval($data['check_limit']) : null,
            'config_email' => !empty($data['config_email']) ? json_decode($data['config_email'], true) : null,
            'config_behavior' => !empty($data['config_behavior']) ? json_decode($data['config_behavior'], true) : null,
            'check_ip' => !empty($data['check_ip']) ? 1 : 0,
            'start_time' => !empty($data['start_time']) ? $data['start_time'] : null,
            'end_time' => !empty($data['end_time']) ? $data['end_time'] : null,
            'status' => !empty($data['status']) ? intval($data['status']) : 0,
            'created' => !empty($data['created']) ? date('H:i - d/m/Y', $data['created']) : null,
            'updated' => !empty($data['updated']) ? date('H:i - d/m/Y', $data['updated']) : null,
        ];

        if(!empty($data['User'])){
            $result['user_full_name'] = !empty($data['User']['full_name']) ? $data['User']['full_name'] : null;
        }

        if(!empty($data['WheelOptionMutiple'])){
            $result['total_option'] = count($data['WheelOptionMutiple']);
            
            foreach($data['WheelOptionMutiple'] as $option){
                $content = !empty($option['content']) ? json_decode($option['content'], true) : null;
                $prize_name = !empty($content['name_'.$lang]) ? $content['name_'.$lang] : null;
                $prize_value = !empty($content['value_'.$lang]) ? $content['value_'.$lang] : null;

                if(strpos($prize_value, '{CDN_URL}') >= 0) {
                    $prize_value = str_replace("{CDN_URL}", CDN_URL, $prize_value);
                }

                $result['options'][] = [
                    'id' => !empty($option['id']) ? intval($option['id']) : null,
                    'content' => !empty($option['content']) ? json_decode($option['content'], true) : null,
                    'prize_name' => $prize_name,
                    'prize_value' => $prize_value,
                    'type_award' => !empty($option['type_award']) ? $option['type_award'] : null,
                    'color' => !empty($option['color']) ? $option['color'] : null,
                    'percent_winning' => !empty($option['percent_winning']) ? $option['percent_winning'] : null,
                    'limit_prize' => !empty($option['limit_prize']) ? $option['limit_prize'] : null,
                    'winning' => !empty($option['winning']) ? $option['winning'] : null
                ];
            }
        }

        if($type_format == SINGLE){
            $result['name'] = !empty($data['WheelFortuneContent']['name']) ? $data['WheelFortuneContent']['name'] : null;
            $result['lang'] = !empty($data['WheelFortuneContent']['lang']) ? $data['WheelFortuneContent']['lang'] : null;
        }

        if($type_format == MULTIPLE){
            $mutiple_language = [];
            if(!empty($data['ContentMutiple'])){
                foreach($data['ContentMutiple'] as $content_mutiple){
                   $content_lang = !empty($content_mutiple['lang']) ? $content_mutiple['lang'] : null;
                   if(empty($content_lang)) continue;

                   $mutiple_language[$content_lang] = $content_mutiple;
                }
            }

            $result['mutiple_language'] = $mutiple_language;
        }

        return $result;
    }

    public function checkNameExist($name = null)
    {
        if(empty($name)) return false;
        $article_info = $this->find()->contain(['WheelFortuneContent'])->where([
            'WheelFortuneContent.name' => $name,
            'WheelFortune.deleted' => 0,
        ])->select(['WheelFortune.id'])->first();
        return !empty($article_info) ? true : false;
    }

    public function getAllNameContent($wheel_id = null)
    {
        if(empty($wheel_id)) return false;

        $wheel_fortune = $this->find()->where([
            'WheelFortune.id' => $wheel_id,
            'WheelFortune.deleted' => 0
        ])->contain(['WheelFortuneContent'])->select(['WheelFortuneContent.lang', 'WheelFortuneContent.name'])->toArray();

        $result = Hash::combine($wheel_fortune, '{*}.WheelFortuneContent.lang', '{*}.WheelFortuneContent.name');

        return !empty($result) ? $result : null;
    }
}