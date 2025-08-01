<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class ShopAdminHelper extends Helper
{   
    public function getAllNameContent($shop_id = null)
    {
        if(empty($shop_id)) return [];
        $result = TableRegistry::get('Shops')->getAllNameContent($shop_id);
        return $result;
    }

    public function getListShops()
    {
        $result = TableRegistry::get('Shops')->getListShops();
        return !empty($result) ? $result : [];
    }
}
