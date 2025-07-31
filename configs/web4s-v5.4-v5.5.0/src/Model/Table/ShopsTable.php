<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Utility\Hash;
use Cake\Cache\Cache;
use Cake\Utility\Text;

class ShopsTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('shops');

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
            'className' => 'Users',
            'foreignKey' => 'created_by',
            'propertyName' => 'User'
        ]);

        $this->hasOne('ShopsContent', [
            'className' => 'ShopsContent',
            'foreignKey' => 'shop_id',
            'propertyName' => 'ShopsContent'
        ]);

        $this->hasMany('ContentMutiple', [
            'className' => 'ShopsContent',
            'foreignKey' => 'shop_id',
            'joinType' => 'LEFT',
            'propertyName' => 'ContentMutiple'
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }

    public function queryListShops($params = []) 
    {
        $table = TableRegistry::get('Shops');

        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;
        $get_empty_name = !empty($params['get_empty_name']) ? true : false;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $lang = !empty($filter[LANG]) ? $filter[LANG] : TableRegistry::get('Languages')->getDefaultLanguage();
        $keyword = !empty($filter[KEYWORD]) ? trim($filter[KEYWORD]) : null;
        $status = isset($filter[STATUS]) && $filter[STATUS] != '' ? intval($filter[STATUS]) : null;

        $city_id = !empty($filter['city_id']) ? intval($filter['city_id']) : null;

        switch($field){
            case LIST_INFO:
                $fields = ['Shops.id', 'ShopsContent.name'];
            break;

            case SIMPLE_INFO:
            case FULL_INFO:
            default:
                $fields = ['Shops.id', 'ShopsContent.name', 'ShopsContent.phone', 'ShopsContent.hotline', 'ShopsContent.email', 'ShopsContent.hours_operation', 'Shops.city_id', 'Shops.district_id', 'ShopsContent.address', 'ShopsContent.gmap', 'Shops.created_by', 'Shops.created', 'Shops.updated', 'Shops.position', 'Shops.status'];
            break;
        }

        $where = ['Shops.deleted' => 0];   
        //contain        
        if(!$get_empty_name){
            $contain = ['ShopsContent'];

            $where['ShopsContent.lang'] = $lang;
        }else{
            $contain = [
                'ShopsContent' => function ($q) use ($lang) {
                    return $q->where([
                        'ShopsContent.lang' => $lang
                    ]);
                }
            ];            
        }       

        // filter by conditions
        if(!empty($keyword)){
            $where['ShopsContent.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!is_null($status)){
            $where['Shops.status'] = $status;
        }

        if(!empty($city_id)){
            $where['Shops.city_id'] = $city_id;
        }

        if(!empty($filter['district_id'])){
            $where['Shops.district_id'] = $filter['district_id'];
        } 

        // sort by
        $sort_string = 'Shops.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'Shops.id '. $sort_type;
                break;

                case 'name':
                    $sort_string = 'ShopsContent.name '. $sort_type .', Shops.position DESC, Shops.id DESC';
                break;

                case 'status':
                    $sort_string = 'Shops.status '. $sort_type .', Shops.position DESC, Shops.id DESC';
                break;

                case 'position':
                    $sort_string = 'Shops.position '. $sort_type .', Shops.id DESC';
                break;

                case 'created':
                    $sort_string = 'Shops.created '. $sort_type .', Shops.position DESC, Shops.id DESC';
                break;

                case 'updated':
                    $sort_string = 'Shops.updated '. $sort_type .', Shops.position DESC, Shops.id DESC';
                break;         
            }
        }

        return $table->find()->contain($contain)->where($where)->select($fields)->group('Shops.id')->order($sort_string);
    }

    public function getDetailShop($id = null, $lang = null, $params = [])
    {
        $result = [];
        if(empty($id) || empty($lang)) return [];        

        $get_user = !empty($params['get_user']) ? true : false;
        $status = !empty($params['status']) ? intval($params['status']) : null;

        $contain = [
            'ShopsContent' => function ($q) use ($lang) {
                return $q->where([
                    'ShopsContent.lang' => $lang
                ]);
            }
        ];

        $where = [
            'Shops.id' => $id,
            'Shops.deleted' => 0,
        ];
        if(!is_null($status)) {
            $where['Shops.status'] = $status;
        }

        if($get_user){
            $contain[] = 'User';
        }

        $result = $this->find()->contain($contain)->where($where)->first();

        return $result;
    }

    public function formatDataShopDetail($data = [], $lang = null)
    {
        if(empty($data) || empty($lang)) return [];
        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'city_id' => !empty($data['city_id']) ? intval($data['city_id']) : null,
            'district_id' => !empty($data['district_id']) ? intval($data['district_id']) : null,
            'city_name' => null,
            'district_name' => null,

            'created_by' => !empty($data['created_by']) ? intval($data['created_by']) : null,
            'created_by_user' => !empty($data['User']['full_name']) ? $data['User']['full_name'] : null,
            'created' => !empty($data['created']) ? date('H:i - d/m/Y', $data['created']) : null,
            'updated' => !empty($data['updated']) ? date('H:i - d/m/Y', $data['updated']) : null,
            'position' => !empty($data['position']) ? intval($data['position']) : null,
            'status' => isset($data['status']) ? intval($data['status']) : null,
            
            'name' => !empty($data['ShopsContent']['name']) ? $data['ShopsContent']['name'] : null,
            'phone' => !empty($data['ShopsContent']['phone']) ? $data['ShopsContent']['phone'] : null,
            'hotline' => !empty($data['ShopsContent']['hotline']) ? $data['ShopsContent']['hotline'] : null,
            'hours_operation' => !empty($data['ShopsContent']['hours_operation']) ? $data['ShopsContent']['hours_operation'] : null,
            'email' => !empty($data['ShopsContent']['email']) ? $data['ShopsContent']['email'] : null,
            'address' => !empty($data['ShopsContent']['address']) ? $data['ShopsContent']['address'] : null,
            'gmap' => !empty($data['ShopsContent']['gmap']) ? $data['ShopsContent']['gmap'] : null,
            'lang' => !empty($data['ShopsContent']['lang']) ? $data['ShopsContent']['lang'] : null,
        ];

        if (!empty($data['city_id'])) {
            $cities = TableRegistry::get('Cities')->getListCity();
            $result['city_name'] = !empty($cities) && !empty($cities[$data['city_id']]) ? $cities[$data['city_id']] : null;
        }

        if (!empty($data['city_id']) && !empty($data['district_id'])) {
            $districts = TableRegistry::get('Districts')->getListDistrict($data['city_id']);
            $result['district_name'] = !empty($districts) && !empty($districts[$data['district_id']]) ? $districts[$data['district_id']] : null;
        }

        if(!empty($data['User'])){
            $result['user_full_name'] = !empty($data['User']['full_name']) ? $data['User']['full_name'] : null;
        }

        return $result;
    }

    public function getAllNameContent($shop_id = null)
    {
        if(empty($shop_id)) return false;

        $shop = $this->find()->where([
            'Shops.id' => $shop_id,
            'Shops.deleted' => 0
        ])->contain(['ShopsContent'])->select(['ShopsContent.lang', 'ShopsContent.name'])->toArray();

        $result = Hash::combine($shop, '{*}.ShopsContent.lang', '{*}.ShopsContent.name');

        return !empty($result) ? $result : null;
    }

    public function getListShops($lang = null)
    {
        if(empty($lang)) $lang = TableRegistry::get('Languages')->getDefaultLanguage();

        $cache_key = SHOP . '_list' . '_' . $lang ;
        $result = Cache::read($cache_key);
        if(is_null($result)){
            $shops = $this->queryListShops([
                FILTER => [
                    'status' => 1,
                    LANG => $lang
                ],
                FIELD => LIST_INFO

            ])->limit(1000)->toArray();  

            $result = [];
            if(!empty($shops)) {
                foreach ($shops as $shop) {
                    if(empty($shop['ShopsContent']['name'])) continue;
                    $result[$shop['id']] = $shop['ShopsContent']['name'];
                }
            }
            Cache::write($cache_key, !empty($result) ? $result : []);
        }

        return $result;
    }
}