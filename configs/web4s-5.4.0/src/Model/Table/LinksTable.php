<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Utility\Text;

class LinksTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('links');
        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'always'
                ]
            ]
        ]);

        $this->hasOne('Products', [
            'className' => 'Products',
            'foreignKey' => 'id',
            'bindingKey' => 'foreign_id',
            'joinType' => 'INNER',
            'conditions' => [
                'Products.deleted' => 0
            ],
            'propertyName' => 'Products'
        ]);

        $this->hasOne('Articles', [
            'className' => 'Articles',
            'foreignKey' => 'id',
            'bindingKey' => 'foreign_id',
            'joinType' => 'INNER',
            'conditions' => [
                'Articles.deleted' => 0
            ],
            'propertyName' => 'Articles'
        ]);

        $this->hasOne('Authors', [
            'className' => 'Authors',
            'foreignKey' => 'id',
            'bindingKey' => 'foreign_id',
            'joinType' => 'INNER',
            'conditions' => [
                'Authors.deleted' => 0
            ],
            'propertyName' => 'Authors'
        ]);

        $this->hasOne('Categories', [
            'className' => 'Categories',
            'foreignKey' => 'id',
            'bindingKey' => 'foreign_id',
            'joinType' => 'INNER',
            'conditions' => [
                'Categories.deleted' => 0
            ],
            'propertyName' => 'Categories'
        ]);

        $this->hasOne('Brands', [
            'className' => 'Brands',
            'foreignKey' => 'id',
            'bindingKey' => 'foreign_id',
            'joinType' => 'INNER',
            'conditions' => [
                'Brands.deleted' => 0
            ],
            'propertyName' => 'Brands'
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {

        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');
            
        $validator
            ->scalar('type')
            ->maxLength('type', 20)
            ->requirePresence('type')
            ->notEmptyString('type');

        $validator
            ->scalar('url')
            ->maxLength('url', 255)
            ->requirePresence('url')
            ->notEmptyString('url');

        $validator
            ->scalar('lang')
            ->maxLength('lang', 20)
            ->requirePresence('lang')
            ->notEmptyString('lang');

        return $validator;
    }

    public function getLinkByUrl($url = null, $params = [])
    {
        if(empty($url)) return [];

        $type = !empty($params['type']) ? $params['type'] : null;
        $language = !empty($params[LANGUAGE]) ? $params[LANGUAGE] : null;
        
        $where = [
            'deleted' => 0,
            'url' => trim($url),
        ];

        if(!empty($type)){
            $where['type'] = $type;
        }

        if(!empty($language)){
            $where['lang'] = $language;
        }

        $result = $this->find()->where($where)->first();

        return $result;
    }

    public function getLanguageByUrl($url = null)
    {
        if(empty($url)) return null;

        $link = $this->find()->where([
            'deleted' => 0,
            'url' => trim($url)
        ])->select(['lang'])->first();

        if(empty($link)){
            $template = TableRegistry::get('Templates')->getTemplateDefault();
            $template_code = !empty($template['code']) ? $template['code'] : null;
            if(empty($template_code)) return null;

            $link = TableRegistry::get('TemplatesPageContent')->find()->where([
                'template_code' => $template_code,
                'url' => trim($url)
            ])->select(['lang'])->first();        
        }

        return !empty($link['lang']) ? $link['lang'] : null;
    }

    public function checkExist($url = null, $id = null)
    {
        if(empty($url)) return false;

        $where = [
            'deleted' => 0,
            'url' => trim($url),
        ];

        if(!empty($id)){
            $where['id <>'] = $id;
        }

        $link = $this->find()->where($where)->first();
        return !empty($link->id) ? true : false;
    }

    public function checkExistUrl($url = null, $foreign_id = null, $type = null)
    {
        if(empty($url)) return false;

        $where = [
            'deleted' => 0,
            'url' => trim($url),
        ];

        if(!empty($foreign_id)){
            $where['foreign_id !='] = $foreign_id;
        }

        if(!empty($type)){
            $where['type'] = $type;
        }

        $link = $this->find()->where($where)->first();
        return !empty($link->id) ? true : false;
    }

    public function getInfoLink($params = [])
    {
        $foreign_id = !empty($params['foreign_id']) ? intval($params['foreign_id']) : null;
        $lang = !empty($params['lang']) ? $params['lang'] : null;
        $type = !empty($params['type']) ? $params['type'] : null;

        if(empty($foreign_id) || empty($lang)) return [];
        
        $where = [
            'deleted' => 0,
            'lang' => $lang,
            'foreign_id' => intval($foreign_id),
        ];

        if(!empty($type)){
            $where['type'] = $type;
        }

        $link = $this->find()->where($where)->first();
        return $link;
    }
    
    public function queryListLink($params = []) 
    {
        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $type = !empty($filter['type']) ? $filter['type'] : null;
        $language = !empty($filter['language']) ? $filter['language'] : null;
        $ids = !empty($filter['ids']) ? $filter['ids'] : [];
        $create_from = !empty($filter['create_from']) ? strtotime(date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $filter['create_from'])))) : null;
        $create_to = !empty($filter['create_to']) ? strtotime(date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $filter['create_to'])))) : null;
        
        // fields select
        switch($field){
            case LIST_INFO:
                $fields = ['Links.foreign_id', 'Links.type','Links.url'];
            break;

            case FULL_INFO:
            case SIMPLE_INFO:
            default:
                $fields = ['Links.foreign_id', 'Links.type', 'Links.url','Links.lang', 'Links.created', 'Links.updated'];
            break;
        }
        
        $sort_string = 'Links.foreign_id DESC';
        if(!empty($sort_field)){
            switch($sort_field){
                case 'type':
                    $sort_string = 'Links.type '. $sort_type .', Links.foreign_id ASC';
                break;

                case 'url':
                    $sort_string = 'Links.url '. $sort_type .', Links.url ASC';
                break;

                case 'created':
                    $sort_string = 'Links.created '. $sort_type .', Links.foreign_id ASC';
                break; 
                case 'updated':
                    $sort_string = 'Links.updated '. $sort_type .', Links.foreign_id ASC';
                break;          
            }
        }
        
        // filter by conditions
        $where = ['Links.deleted' => 0];  
        // filter by conditions  
        if(!empty($keyword)){
            $where['Links.url LIKE'] = '%' . Text::slug(strtolower($keyword),'-') . '%';
        }

        if(!empty($type)){
            $where['Links.type'] = $type;
        }

        if(!empty($create_from)){
            $where['Links.created >='] = $create_from;
        }

        if(!empty($create_to)){
            $where['Links.created <='] = $create_to;
        }

        if(!empty($language)){
            $where['Links.lang '] = $language;
        }

        return $this->find()->where($where)->select($fields)->order($sort_string);
    }

    public function getUrlUnique($url = null, $index = 0)
    {   
        if(empty($index)) $index = 0;

        $url_check = $url;
        if($index > 0){
            $url_check = $url . '-'. $index;
        }
        
        if($index >= 100){
            return $url_check;
        }

        $check = $this->checkExist($url_check);

        if($check){
            $index ++;
            $url_check = $this->getUrlUnique($url, $index);
        }
        return $url_check;
    }
}