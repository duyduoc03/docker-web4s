<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Utility\Hash;

class DataExtendHelper extends Helper
{
    public function getCollections()
    {
        return TableRegistry::get('ExtendsCollection')->getList();
    }

    public function getDetailCollection($collection_code = null)
    {
        if(empty($collection_code)) return [];

        $collections = TableRegistry::get('ExtendsCollection')->getList();
        $collections = !empty($collections) ? Hash::combine($collections, '{n}.code', '{n}') : [];
        return !empty($collections[$collection_code]) ? $collections[$collection_code] : [];
    }

    public function getData($collection_code = null, $params = [], $lang = null)
    {
        if(empty($collection_code)) return [];

        $collection_info = $this->getDetailCollection($collection_code);   
        $collection_id = !empty($collection_info['id']) ? intval($collection_info['id']) : null;
        if(empty($collection_info)) return [];
              
        $limit = !empty($params[LIMIT]) ? intval($params[LIMIT]) : 20;
        $page = !empty($params[PAGE]) ? intval($params[PAGE]) : 1;

        $table = TableRegistry::get('ExtendsRecord');
        $records = $table->queryListExtendRecord($collection_id, $params)->limit($limit)->page($page)->toList();

        return $table->formatDataRecord($collection_id, $records, $lang);
    }
}