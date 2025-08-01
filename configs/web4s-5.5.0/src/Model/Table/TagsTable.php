<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;

class TagsTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('tags');
        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'always'
                ]
            ]
        ]);

        $this->hasMany('TagsRelation', [
            'className' => 'TagsRelation',
            'foreignKey' => 'tag_id',
            'joinType' => 'LEFT',
            'propertyName' => 'TagsRelation'
        ]);
    }

    public function queryListTags($params = []) 
    {
        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];

        $lang = !empty($filter[LANG]) ? $filter[LANG] : null;
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $ids = !empty($filter['ids']) ? $filter['ids'] : [];

        // fields select
        switch($field){
            case FULL_INFO:
                $fields = ['Tags.id', 'Tags.name', 'Tags.url', 'Tags.content', 'Tags.seo_title', 'Tags.seo_description', 'Tags.seo_keyword', 'Tags.lang'];
            break;

            case LIST_INFO:
                $fields = ['Tags.id', 'Tags.name'];
            break;

            case SIMPLE_INFO:
            default:
                $fields = ['Tags.id', 'Tags.name', 'Tags.url', 'Tags.lang'];
            break;
        }

        $sort_string = 'Tags.id ASC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                case 'tag_id':
                    $sort_string = 'Tags.id '. $sort_type;
                break;

                case 'name':
                    $sort_string = 'Tags.name '. $sort_type .', Tags.id ASC';
                break;

                case 'lang':
                    $sort_string = 'Tags.lang '. $sort_type .', Tags.id ASC';
                break;
            }
        }

        // filter by conditions
        $where = [];    

        if(!empty($keyword)){
            $where['Tags.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!empty($ids)){
            $where['Tags.id IN'] = $ids;
        }

        if(!empty($lang)){
            $where['Tags.lang'] = $lang;
        }

        return $this->find()->where($where)->select($fields)->order($sort_string);
    }

    public function checkTagExist($name = null, $id = null)
    {
        if(empty($tag)) return false;

        $where = ['Tags.name' => $name];
        if(!empty($id)){
            $where['Tags.id !='] = $id;
        }
        $result = TableRegistry::get('Tags')->find()->where($where)->first();

        return !empty($result) ? true : false;
    }

    public function checkUrlTagExist($url = null, $id = null)
    {
        if(empty($url)) return false;

        $where = ['Tags.url' => $url];
        if(!empty($id)){
            $where['Tags.id !='] = $id;
        }

        $result = $this->find()->where($where)->first();

        return !empty($result) ? true : false;
    }

    public function getUrlUnique($url = null, $index = 0)
    {
        if(empty($url)) return null;

        $result = $url;
        if($index > 0){
            $result .= '-'. $index;
        }

        if($index >= 100) return $result;

        $check = $this->checkUrlTagExist($result);
        
        if($check){
            $index ++;
            $result = $this->getUrlUnique($url, $index);
        }

        return $result;
    }

    public function getTagByUrl($url = null, $params = [])
    {
        if(empty($url)) return [];

        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;
        switch($field){
            case FULL_INFO:
                $fields = ['Tags.id', 'Tags.name', 'Tags.url', 'Tags.content', 'Tags.seo_title', 'Tags.seo_description', 'Tags.seo_keyword', 'Tags.lang'];
            break;

            case LIST_INFO:
                $fields = ['Tags.id', 'Tags.name'];
            break;

            case SIMPLE_INFO:
            default:
                $fields = ['Tags.id', 'Tags.name', 'Tags.url', 'Tags.lang'];
            break;
        }

        $result = $this->find()->where(['Tags.url' => $url])->select($fields)->first();

        return !empty($result) ? $result : [];
    }

    public function getDetailTag($id = null, $lang = null)
    {
        $result = [];
        if(empty($id) || empty($lang)) return [];        

        $result = $this->find()->where([
            'Tags.id' => $id,
            'Tags.lang' => $lang
        ])->first();

        return $result;
    }

    public function saveTag($data = [], $id = null)
    {
        
        $utilities = TableRegistry::get('Utilities');
        $table = TableRegistry::get('Tags');
        
        if(empty($data)) {
            return $utilities->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $name = !empty($data['name']) ? trim($data['name']) : null;
        $link = !empty($data['link']) ? $utilities->formatToUrl(trim($data['link'])) : null;
        $lang = !empty($data['lang']) ? trim($data['lang']) : null;

        // validate data
        if(empty($name)){
            return $utilities->getResponse([MESSAGE => __d('admin', 'vui_long_nhap_ten_the')]);
        }

        if(empty($link)){
            return $utilities->getResponse([MESSAGE => __d('admin', 'vui_long_nhap_duong_dan')]);
        }

        if(empty($lang)){
            return $utilities->getResponse([MESSAGE => __d('admin', 'vui_long_chon_ngon_ngu')]);
        }        

        if($table->checkTagExist($name, $id)){
            return $utilities->getResponse([MESSAGE => __d('admin', 'ten_the_da_ton_tai_tren_he_thong')]);
        }

        if($table->checkUrlTagExist($link, $id)){
            return $utilities->getResponse([MESSAGE => __d('admin', 'duong_dan_da_ton_tai_tren_he_thong')]);
        }

        $languages = TableRegistry::get('Languages')->getList();
        if(empty($languages[$lang])){
            return $utilities->getResponse([MESSAGE => __d('admin', 'ngon_ngu_khong_hop_le')]);
        }

        if(!empty($id)){
            $tag = $table->find()->where(['Tags.id' => $id])->first();
            if(empty($tag)){
                return $utilities->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }
        }

        // format data before save
        $list_keyword = !empty($data['seo_keyword']) ? array_column(json_decode($data['seo_keyword'], true), 'value') : null;
        $seo_keyword = !empty($list_keyword) ? implode(', ', $list_keyword) : null;
        
        $seo_title = !empty($data['seo_title']) ? $data['seo_title'] : $name;
        if(empty($id)){
            $settings = TableRegistry::get('Settings')->getSettingByGroup(TAG);
            
            $prefix_seo_title = !empty($settings['prefix_seo_title']) ? $settings['prefix_seo_title'] : null;
            $suffixes_seo_title = !empty($settings['suffixes_seo_title']) ? $settings['suffixes_seo_title'] : null;
            $seo_title = $prefix_seo_title . $seo_title . $suffixes_seo_title;
        }        

        $data_save = [
            'name' => $name,
            'url' => $link,
            'content' => !empty($data['content']) ? $data['content'] : null,
            'seo_title' => $seo_title,
            'seo_description' => !empty($data['seo_description']) ? $data['seo_description'] : null,
            'seo_keyword' => $seo_keyword,
            'lang' => $lang,
            'search_unicode' => strtolower($utilities->formatSearchUnicode([$name]))
        ];
        // merge data with entity 
        if(empty($id)){
            $tag = $table->newEntity($data_save);
        }else{            
            $tag = $table->patchEntity($tag, $data_save);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = $table->save($tag);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            return $utilities->getResponse([
                CODE => SUCCESS, 
                DATA => [
                    'id' => $save->id
                ]
            ]);

        }catch (Exception $e) {
            $conn->rollback();
            return $utilities->getResponse([MESSAGE => $e->getMessage()]);
        }
    }
}