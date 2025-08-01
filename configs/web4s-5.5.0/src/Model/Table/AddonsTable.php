<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;
use Cake\Utility\Hash;

class AddonsTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('addons');

        $this->setPrimaryKey('id');  
    }

    public function getList()
    {
        $cache_key = ADDON . '_list';
        $result = Cache::read($cache_key);

        if(is_null($result)){
            $addons = TableRegistry::get('Addons')->find()->select(['code', 'name'])->toArray();
            $result =  Hash::combine($addons, '{n}.code', '{n}.name');

            if (empty($result)) $result = $this->getListAddonsDefault();

            Cache::write($cache_key, !empty($result) ? $result : []);
        }

        if(defined('SUPPER_ADMIN') && SUPPER_ADMIN) {
            $result['interface_configuration'] = 'Cấu hình giao diện';
            $result['interface_edit'] = 'Chỉnh sửa giao diện';
        }

        return $result;
    }

    public function getListAddonsDefault()
    {
        $table = TableRegistry::get('Addons');
        $plugins = TableRegistry::get('Plugins')->getList();

        $data_addons = [
            'interface_configuration' => 'Cấu hình giao diện',
            'interface_edit' => 'Chỉnh sửa giao diện',
            'customer' => 'Quản lý khách hàng',
        ];

        if (!empty($plugins)) {
            foreach ($plugins as $code => $name) {
                if (empty($code) || empty($name)) continue;

                $data_addons[$code] = $name;
            }
        }

        if (!empty($plugins[PRODUCT])) {
            $data_addons['order'] = 'Quản lý đơn hàng';
            $data_addons['report'] = 'Báo cáo doanh thu';
            $data_addons['bank'] = 'Thẻ ngân hàng';
            $data_addons['vnpay'] = 'Vnpay';
            $data_addons['momo'] = 'Momo';
            $data_addons['zalopay'] = 'Zalopay';
            $data_addons['onepay'] = 'Onepay';
            $data_addons['paypal'] = 'Paypal';
            $data_addons['shipping_ghn'] = 'Giao hàng nhanh';
            $data_addons['shipping_ghtk'] = 'Giao hàng tiết kiệm';
            $data_addons['template_print'] = 'Mẫu in';
            $data_addons['template_email'] = 'Mẫu email';
            $data_addons['currency'] = 'Tiền tệ';
        }

        if (empty($data_addons)) return [];

        // Xóa hết các addon hiện tại
        $table->deleteAll(['id IS NOT' => NULL]);

        // format data save
        $data_save = [];
        foreach ($data_addons as $code => $addon) {
            if (empty($code) || empty($addon)) continue;
            
            $data_save[] = [
                'code' => $code,
                'name' => $addon
            ];
        }

        // lưu thông tin addon mới
        $entities = $table->newEntities($data_save);
        $save = $table->saveMany($entities);

        return $data_addons;
    }
}