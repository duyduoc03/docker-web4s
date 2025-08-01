<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Utility\Text;
use Cake\Utility\Hash;
use Cake\Cache\Cache;

class AuthorsTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('authors'); 
        $this->setPrimaryKey('id');
        $this->setDisplayField('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'always'
                ]
            ]
        ]);

        $this->hasOne('AuthorsContent', [
            'className' => 'AuthorsContent',
            'foreignKey' => 'author_id',
            'propertyName' => 'AuthorsContent'
        ]);

        $this->hasOne('Links', [
            'className' => 'Links',
            'foreignKey' => 'foreign_id',
            'propertyName' => 'Links'
        ]);

        $this->hasMany('ContentMutiple', [
            'className' => 'AuthorsContent',
            'foreignKey' => 'author_id',
            'joinType' => 'LEFT',
            'propertyName' => 'ContentMutiple'
        ]);

        $this->hasMany('LinksMutiple', [
            'className' => 'Links',
            'foreignKey' => 'foreign_id',
            'joinType' => 'LEFT',
            'conditions' => [
                'LinksMutiple.type' => AUTHOR_DETAIL,
                'LinksMutiple.deleted' => 0
            ],
            'propertyName' => 'LinksMutiple'
        ]);
    }

    public function queryListAuthors($params = []) 
    {
        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $featured = isset($filter['featured']) && $filter['featured'] != '' ? intval($filter['featured']) : null;
        $lang = !empty($filter[LANG]) ? $filter[LANG] : TableRegistry::get('Languages')->getDefaultLanguage();
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $status = isset($filter['status']) && $filter['status'] != '' ? intval($filter['status']) : null;
        $ids = !empty($filter['ids']) && is_array($filter['ids']) ? $filter['ids'] : [];
        
        // fields select
        switch($field){
            case LIST_INFO:
                $fields = ['Authors.id', 'Authors.full_name' ];
            break;

            case FULL_INFO:            
                $fields = ['Authors.id', 'Authors.full_name','AuthorsContent.content','AuthorsContent.job_title', 'Authors.position','Authors.images' ,'Authors.url_video','Authors.type_video', 'Authors.avatar', 'AuthorsContent.description', 'Authors.phone', 'Authors.email', 'Authors.address', 'AuthorsContent.seo_title','AuthorsContent.seo_description', 'AuthorsContent.seo_keyword', 'Authors.status', 'Authors.created', 'Authors.updated', 'AuthorsContent.lang', 'Links.id', 'Links.url'];
            break;

            case SIMPLE_INFO:
            default:
                $fields = ['Authors.id', 'Authors.full_name', 'AuthorsContent.job_title', 'Authors.position', 'Authors.images' ,'Authors.url_video', 'Authors.phone', 'Authors.email', 'Authors.address', 'Authors.type_video', 'Authors.avatar', 'AuthorsContent.description','AuthorsContent.content', 'Authors.status','Authors.position', 'Authors.created', 'Authors.updated', 'Links.id', 'Links.url'];
            break;
        }

        $sort_string = 'Authors.position DESC, Authors.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                case 'author_id':
                    $sort_string = 'Authors.id '. $sort_type;
                break;

                case 'full_name':
                    $sort_string = 'Authors.full_name '. $sort_type .', Authors.id DESC';
                break;

                case 'status':
                    $sort_string = 'Authors.status '. $sort_type .', Authors.id DESC';
                break;

                case 'position':
                    $sort_string = 'Authors.position '. $sort_type .', Authors.id DESC';
                break;
            }
        }

        // filter by conditions
        $where = [
            'Authors.deleted' => 0,
            'Links.type' => AUTHOR_DETAIL,
            'Links.deleted' => 0,
            'AuthorsContent.lang' => $lang
        ];  
        
        //contain        
        $contain = [
            'AuthorsContent' => function ($q) use ($lang) {
                return $q->where([
                    'AuthorsContent.lang' => $lang
                ]);
            }, 
            'Links' => function ($q) use ($lang) {
                return $q->where([
                    'Links.type' => AUTHOR_DETAIL,
                    'Links.lang' => $lang,
                    'Links.deleted' => 0   
                ]);
            }
        ];

        if(!is_null($status)){
            $where['Authors.status'] = $status;
        }

        if (!empty($keyword)) {
            $keyword_slug = '%' . Text::slug(strtolower($keyword), ' ') . '%';
            $where['OR'] = [
                'Authors.search_unicode LIKE' => $keyword_slug,
                'AuthorsContent.search_unicode LIKE' => $keyword_slug
            ];
        }

        if(!empty($ids)){
            $where['Authors.id IN'] = $ids;
        }
       
        return  $this->find()->contain($contain)->where($where)->select($fields)->order($sort_string);
        
    }

    public function getDetailAuthor($id = null, $lang = null, $params = [])
    {   
        if(empty($id) || empty($lang)) return [];

        $status = !empty($params['status']) ? intval($params['status']) : null;

        $contain = [
            'AuthorsContent' => function ($q) use ($lang) {
                return $q->where([
                    'AuthorsContent.lang' => $lang
                ]);
            }, 
            'Links' => function ($q) use ($lang) {
                return $q->where([
                    'Links.type' => AUTHOR_DETAIL,
                    'Links.lang' => $lang,
                    'Links.deleted' => 0
                ]);
            }];
           
         $where = [
            'Authors.id' => $id,
            'Authors.deleted' => 0
        ];

        if(!is_null($status)) {
            $where['Authors.status'] = $status;
        }

        $result = $this->find()->contain($contain)->where($where)->first();
        return $result;
    }

    public function formatDataAuthorDetail($data = [], $lang = null, $type_format = null)
    {
        if(empty($data) || empty($lang)) return [];
        if(empty($type_format) || !in_array($type_format, [SINGLE, MULTIPLE])) $type_format = SINGLE;
        
        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'avatar' => !empty($data['avatar']) ? $data['avatar'] : null,
            'position' => !empty($data['position']) ? intval($data['position']) : 1,
            'url_video' => !empty($data['url_video']) ? $data['url_video'] : null,
            'type_video' => !empty($data['type_video']) ? $data['type_video'] : null,
            'images' => !empty($data['images']) ? json_decode($data['images'], true) : [],
            'email' => !empty($data['email']) ? $data['email'] : null,
            'phone' => !empty($data['phone']) ? $data['phone'] : null,
            'address' => !empty($data['address']) ? $data['address'] : null,
            'full_name' => !empty($data['full_name']) ? $data['full_name'] : null,
            'featured' => !empty($data['featured']) ? 1 : 0,
            'status' => !empty($data['status']) ? 1 : 0,
            'social' => !empty($data['social']) ? json_decode($data['social'], true) : [],
            'created' => !empty($data['created']) ? date('H:i - d/m/Y', $data['created']) : null,
            'updated' => !empty($data['updated']) ? date('H:i - d/m/Y', $data['updated']) : null,
        ];

        if($type_format == SINGLE){
            $result['job_title'] = !empty($data['AuthorsContent']['job_title']) ? $data['AuthorsContent']['job_title'] : null;
            $result['description'] = !empty($data['AuthorsContent']['description']) ? $data['AuthorsContent']['description'] : null;
            $result['content'] = !empty($data['AuthorsContent']['content']) ? $data['AuthorsContent']['content'] : null;
            $result['seo_title'] = !empty($data['AuthorsContent']['seo_title']) ? $data['AuthorsContent']['seo_title'] : null;
            $result['seo_description'] = !empty($data['AuthorsContent']['seo_description']) ? $data['AuthorsContent']['seo_description'] : null;
            $result['seo_keyword'] = !empty($data['AuthorsContent']['seo_keyword']) ? $data['AuthorsContent']['seo_keyword'] : null;
            $result['lang'] = !empty($data['AuthorsContent']['lang']) ? $data['AuthorsContent']['lang'] : null;

            $result['url_id'] = !empty($data['Links']['id']) ? intval($data['Links']['id']) : null;
            $result['url'] = !empty($data['Links']['url']) ? $data['Links']['url'] : null;
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

            if(!empty($data['LinksMutiple'])){
                foreach($data['LinksMutiple'] as $link_mutiple){
                    $url_lang = !empty($link_mutiple['lang']) ? $link_mutiple['lang'] : null;
                    $url = !empty($link_mutiple['url']) ? $link_mutiple['url'] : null;
                    if(empty($url_lang) || empty($url)) continue;

                    $mutiple_language[$url_lang]['url'] = $url;
                    $mutiple_language[$url_lang]['url_id'] = !empty($link_mutiple['id']) ? $link_mutiple['id'] : null;
                    
                }
            }

            $result['mutiple_language'] = $mutiple_language;
        }

        return $result;

    }

    public function getAuthorsSimple($lang = null) 
    {
        $lang = !empty($lang) ? $lang : TableRegistry::get('Languages')->getDefaultLanguage();

        $cache_key = AUTHOR . '_list_simple_' . $lang;
        $result = Cache::read($cache_key);        
        if(is_null($result)){
            $authors = $this->queryListAuthors([
                FILTER => [
                    STATUS => 1,
                    LANG => $lang
                ]
            ])->toArray();

            $result = [];
            if(!empty($authors)){
                foreach($authors as $k => $author){
                    $result[$k] = $this->formatDataAuthorDetail($author, $lang);
                }
            }

            Cache::write($cache_key, !empty($result) ? $result : []);
        }
        
        return $result;
    }
}