<?php
declare(strict_types=1);

namespace App\View\Helper;
use Cake\View\Helper;
use Cake\Core\Exception\Exception;
use Cake\ORM\TableRegistry;

class UserHelper extends Helper
{
    public function getDetailUser($user_id = null, $param = [])
    {
        if(empty($user_id)) return [];

        $table = TableRegistry::get('Users');

        $user = $table->getDetailUsers($user_id, $param);
        
        return $table->formatDataUserDetail($user);
    }
}
