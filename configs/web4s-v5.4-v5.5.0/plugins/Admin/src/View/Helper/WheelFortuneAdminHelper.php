<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class WheelFortuneAdminHelper extends Helper
{   
	public function getDetailWheel($wheel_id = null, $lang = null, $params = [])
    {
        $table = TableRegistry::get('WheelFortune');
        $wheel_info = $table->getDetailWheelFortune($wheel_id, $lang, $params);

        $result = [];
        if(!empty($wheel_info)){
        	$result = $table->formatDataWheelFortune($wheel_info, $lang);
        }
        return $result;
    }

    public function getAllNameContent($wheel_id = null)
    {
        if(empty($wheel_id)) return [];
        $result = TableRegistry::get('WheelFortune')->getAllNameContent($wheel_id);
        return $result;
    }
}
