<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;

class LadipagesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ladipages');
        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'always'
                ]
            ]
        ]);
        
        $this->hasOne('Links', [
            'className' => 'Links',
            'foreignKey' => 'foreign_id',
            'propertyName' => 'Links'
        ]);
    }

    public function queryListLadipage($params = [])
    {
        // get info params
    	$fields = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];

        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;        

    	switch ($fields) {
    		case FULL_INFO:
    			$fields = ['Ladipages.id', 'Ladipages.name', 'Ladipages.ladipage_key', 'Ladipages.status', 'Ladipages.created', 'Ladipages.updated', 'Links.id', 'Links.url'];
    		break;

    		case LIST_INFO:
    			$fields = ['Ladipages.id', 'Ladipages.name'];
    		break;

    		case SIMPLE_INFO:
    		default:
    			$fields = ['Ladipages.id', 'Ladipages.name', 'Ladipages.ladipage_key', 'Ladipages.status', 'Ladipages.created', 'Ladipages.updated', 'Links.id', 'Links.url'];
    		break;
    	}

        $contain = ['Links'];

        $sort_string = 'Ladipages.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'Ladipages.id '. $sort_type;
                break;

                case 'name':
                    $sort_string = 'Ladipages.name '. $sort_type .', Ladipages.status DESC';
                break;       
            }
        }

        $where = ['Ladipages.deleted' => 0];
        $where['Links.type'] = LADI_DETAIL;
        $where['Links.deleted'] = 0;

        if(!empty($keyword)){
            $where['Ladipages.name LIKE'] = '%' . $keyword . '%';
        }
        
        return $this->find()->contain($contain)->where($where)->select($fields)->order($sort_string);
    }


    public function getDetailLadipage($id = null, $lang = null, $params = [])
    {
        $result = [];
        if(empty($id) || empty($lang)) return [];        

        $status = !empty($params['status']) ? intval($params['status']) : null;

        $contain = [
            'Links' => function ($q) use ($lang) {
                return $q->where([
                    'Links.type' => LADI_DETAIL,
                    'Links.lang' => $lang,
                    'Links.deleted' => 0
                ]);
            }
        ];


        $where = [
            'Ladipages.id' => $id,
            'Ladipages.deleted' => 0,
        ];
        if(!is_null($status)) {
            $where['Ladipages.status'] = $status;
        }

        $result = $this->find()->contain($contain)->where($where)->first();

        return $result;
    }

    public function formatDataLadipageDetail($data = [], $lang = null)
    {
        if(empty($data) || empty($lang)) return [];

        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'name' => !empty($data['name']) ? $data['name'] : null,
            'ladipage_key' => !empty($data['ladipage_key']) ? $data['ladipage_key'] : null,
            'content' => !empty($data['content']) ? $data['content'] : null,
            'created' => !empty($data['created']) ? date('d/m/Y H:i', $data['created']) : null,
            'updated' => !empty($data['updated']) ? date('d/m/Y H:i', $data['updated']) : null,
            'position' => !empty($data['position']) ? intval($data['position']) : null,
            'status' => isset($data['status']) ? intval($data['status']) : null,
            'url_id' => !empty($data['Links']['id']) ? intval($data['Links']['id']) : null,
            'url' => !empty($data['Links']['url']) ? $data['Links']['url'] : null,
        ];

        return $result;
    }

    public function checkExistName($name = null, $id = null)
    {
        if(empty($name)) return false;

        $where = [
            'deleted' => 0,
            'name' => $name,            
        ];

        if(!empty($id)){
            $where['id !='] = $id;
        }
        $ladi = $this->find()->where($where)->first();

        return !empty($ladi->id) ? true : false;
    }
}