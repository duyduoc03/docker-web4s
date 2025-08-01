<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Utility\Hash;
use Cake\Utility\Text;

class LogsUtilitiesTable extends Table
{
    private $json_number_record = 50;
    private $dir_log = SOURCE_DOMAIN . DS . 'system_logs';
    private $version = '';

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->version = strtolower(TableRegistry::get('Utilities')->generateRandomString(12));
    }

    public function writeJsonLogRecord($alias = null, $record_id = null)
    {
        if(empty($alias) || empty($record_id)) return false;
        $dir_file = $this->_getDirFileLog(strtolower($alias), $record_id);
        if(empty($dir_file)) return false;

        $result = [
            'version' => $this->version,
            'dir_file' => $dir_file
        ];
        
        $content = @file_get_contents($dir_file);
        if(empty($content) || !TableRegistry::get('Utilities')->isJson($content)){
            $content = [];
        }else{
            $content = json_decode($content, true);
        }

        $entity = null;
        switch($alias){
            case 'Articles':
                $entity = TableRegistry::get('Articles')->find()->contain([
                    'CategoriesArticle', 
                    'ArticlesAttribute',
                    'TagArticle',
                    'TagsRelation',
                    'ContentMutiple',
                    'LinksMutiple'
                ])->where([
                    'Articles.id' => $record_id,
                    'Articles.deleted' => 0
                ])->first();
            break;

            case 'Products':
                $entity = TableRegistry::get('Products')->find()->contain([
                    'CategoriesProduct', 
                    'ProductsItem',
                    'ProductsAttribute',
                    'ProductsItemAttribute',
                    'ContentMutiple',
                    'TagsRelation',
                    'LinksMutiple'
                ])->where([
                    'Products.id' => $record_id,
                    'Products.deleted' => 0
                ])->first();
            break;

            case 'Brands':
                $entity = TableRegistry::get('Brands')->find()->contain([
                    'ContentMutiple',
                    'LinksMutiple'
                ])->where([
                    'id' => $record_id,
                    'deleted' => 0
                ])->first();
            break;

            case 'Authors':
                $entity = TableRegistry::get('Authors')->find()->contain([
                    'ContentMutiple',
                    'LinksMutiple'
                ])->where([
                    'id' => $record_id,
                    'deleted' => 0
                ])->first();
            break;

            case 'Categories':
                $entity = TableRegistry::get('Categories')->find()->contain([
                    'ContentMutiple',
                    'LinksMutiple',
                    'CategoriesAttribute'
                ])->where([
                    'Categories.id' => $record_id,
                    'Categories.deleted' => 0
                ])->first();
            break;

            case 'TemplatesBlock':
                $entity = TableRegistry::get('TemplatesBlock')->find()->contain([])->where([
                    'TemplatesBlock.id' => $record_id,
                    'TemplatesBlock.deleted' => 0
                ])->first();
            break;

            case 'TemplatesPage':
                $entity = TableRegistry::get('TemplatesPage')->find()->contain([
                    'ContentMutiple',
                    'TemplatesRow',
                    'TemplatesColumn'
                ])->where([
                    'TemplatesPage.id' => $record_id
                ])->first();
            break;
        }

        if(empty($entity)) return false;

        $latest_log = $this->_getLatestLogRecord($alias, $record_id);
        $content[$this->version] = [
            'entity' => $entity,
            'before_entity' => !empty($latest_log['entity']) ? $latest_log['entity'] : []
        ];

        $write = @file_put_contents($dir_file, json_encode($content));
        
        return $result;
    }

    public function getLogRecordByVersion($sub_type = null, $record_id = null, $version = null)
    {
        $result = [];
        $alias = $this->getAliasBySubType($sub_type);
        if(empty($alias) || empty($record_id) || empty($version)) return $result;
        $alias = strtolower($alias);

        $dir_folder = $this->dir_log . DS . $alias;

        for ($i = 0; $i < 1000; $i++) { 
            $dir_file = $dir_folder . DS . $record_id . '_' . $i .'.json';
            if(!file_exists($dir_file)) return $result;

            // đọc dữ liệu trong file log
            $content = @file_get_contents($dir_file);
            if(empty($content) || !TableRegistry::get('Utilities')->isJson($content)) continue;

            $content = json_decode($content, true);

            if(!empty($content[$version])) {
                $result = $content[$version];
                return $result;
            }
        }
        
        return $result;
    } 

    public function getAliasBySubType($sub_type = null)
    {
        if(empty($sub_type)) return null;

        $alias = null;
        switch($sub_type){
            case ARTICLE:
                $alias = 'Articles';
            break;

            case PRODUCT:
                $alias = 'Products';
            break;

            case BRAND:
                $alias = 'Brands';
            break;

            case AUTHOR:
                $alias = 'Authors';
            break;

            case CATEGORY:
                $alias = 'Categories';
            break;

            case ORDER:
                $alias = 'Orders';
            break;

            case AUTHOR:
                $alias = 'Authors';
            break;

            case BLOCK:
                $alias = 'TemplatesBlock';
            break;

            case TEMPLATE_PAGE:
                $alias = 'TemplatesPage';
            break;
        }

        return $alias;
    }

    public function creatDirLog($dir_folder = '')
    {
        if(empty($dir_folder)) return false;
        if(file_exists($dir_folder)) return true;

        // tạo thư mục nếu chưa tồn tại
        $split_dir = explode(DS, str_replace(SOURCE_DOMAIN . DS, '', $dir_folder));
        if(empty($split_dir)) return false;

        $check_dir = SOURCE_DOMAIN;
        foreach($split_dir as $k => $path){
            $check_dir .= DS . $path;

            if(!file_exists($check_dir)){
                @mkdir($check_dir, 0755);
            }
        }

        return true;
    }

    public function _getLatestLogRecord($alias = null, $record_id = null)
    {
        if(empty($alias) || empty($record_id)) return [];
        $alias = strtolower($alias);
        
        $dir_folder = $this->dir_log . DS . $alias;        
        $result = [];
        for ($i = 0; $i < 1000; $i++) {

            $dir_file = $dir_folder . DS . $record_id . '_' . $i .'.json';
            if(!file_exists($dir_file)) return $result;

            // đọc dữ liệu trong file log
            $content = @file_get_contents($dir_file);
            if(empty($content) || !TableRegistry::get('Utilities')->isJson($content)){
                $content = [];
            }else{
                $content = json_decode($content, true);
            }
            
            if(empty($content)) return $result;

            $result = end($content);
        }

        return $result;
    }    

    private function _getDirFileLog($alias = null, $record_id = null)
    {
        if(empty($alias) || empty($record_id)) return '';
        $alias = strtolower($alias);

        $dir_folder = $this->dir_log . DS . $alias;

        // tạo thư mục nếu chưa tồn tại
        $create_folder = $this->creatDirLog($dir_folder);
        if(!$create_folder) return '';

        for ($i = 0; $i < 1000; $i++) { 
            $dir_file = $dir_folder . DS . $record_id . '_' . $i .'.json';
            
            // tạo file log nếu chưa tồn tại
            if(!file_exists($dir_file)) @fopen($dir_file, 'w');
            if(!file_exists($dir_file)) return '';


            // đọc dữ liệu trong file log để tìm file log cuối cùng cần ghi
            $content = @file_get_contents($dir_file);
            if(empty($content) || !TableRegistry::get('Utilities')->isJson($content)){
                $content = [];
            }else{
                $content = json_decode($content, true);
            }

            if(count($content) >= $this->json_number_record) continue;

            return $dir_file;
        }
        
        return '';
    }
        
}