<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class MobileAdminHelper extends Helper
{
  
    public function getInfoApp()
    {
        $result = TableRegistry::get('MobileApp')->find()->select(['app_id'])->toList();
        return $result;    
    }

}
