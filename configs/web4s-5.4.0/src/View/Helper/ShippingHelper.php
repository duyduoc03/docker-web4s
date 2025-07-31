<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class ShippingHelper extends Helper
{
    /** Lấy thông tin vận đơn
     * 
     * 
     * {assign var = data value = $this->Shipping->getInfoShipping($id)}
    */
    public function getInfoShipping($id = null)
    {
        if(empty($id)) return [];
        $shipping_info = TableRegistry::get('Shippings')->getDetailShippings($id);

        return $shipping_info;
    }
}
