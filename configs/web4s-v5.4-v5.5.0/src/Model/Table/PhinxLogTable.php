<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use Migrations\Migrations;
use Cake\Filesystem\Folder;
use Cake\Cache\Cache;
use Cake\Utility\Hash;

class PhinxLogTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('phinxlog');
        $this->setPrimaryKey('id');    
    }

    public function migrates()
    {
        $migrations = new Migrations();

        $files = $this->_getListMigrations();
        $logs = $this->_getListPhinxLog();

        $last_file = !empty($files) ? end($files) : null;
        $last_log = !empty($logs) ? end($logs) : null;

        if(!empty($last_file) && !empty($last_log) && ($last_file != $last_log)){
            $run = $migrations->migrate();
            TableRegistry::get('App')->deleteCacheDataByKey(PHINX_LOG);
        }
    }

    private function _getListMigrations()
    {
        $dir = CONFIG . 'Migrations';

        if(!file_exists($dir)) return [];

        $folder = new Folder($dir, false);
        $files = $folder->find('.*\.php', true);
        if(empty($files)) return [];
        
        $result = [];
        foreach ($files as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            $split = !empty($name) ? explode('_', $name) : null;

            if(empty($split) || count($split) != 2) continue;

            $version = !empty($split[0]) ? intval($split[0]) : null;
            $migrate_name = !empty($split[1]) ? $split[1] : null;
            
            if(empty($version) || empty($migrate_name)) continue;
            
            $result[$version] = $migrate_name;
        }
        if(!empty($result)){
            ksort($result);
        }

        return $result;
    }

    private function _getListPhinxLog()
    {
        $cache_key = PHINX_LOG . '_all_';

        $result = Cache::read($cache_key);    
        if(is_null($result)){
            $logs = $this->find()->where()->select(['version', 'migration_name'])->toArray();
            $result = !empty($logs) ? Hash::combine($logs, '{n}.version', '{n}.migration_name') : [];
            Cache::write($cache_key, $result);
        }

        return $result;   
    }
}