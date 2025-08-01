<?php
declare(strict_types=1);

use Migrations\AbstractMigration;
use Cake\ORM\TableRegistry;

class Migration72 extends AbstractMigration
{
    /**
     * - migrate lại - thêm dữ liệu mẫu in vận đơn
     * - migrate lại - tạo file template print vận đơn
     * - tạo file template tác giả
     * - thêm dữ liệu schema brand
     * 
     */

    public function up()
    {
        $table = $this->table('print_templates');
        $table_utilities_migrations = TableRegistry::get('UtilitiesMigrations');
        
        // thêm row 'shipment'
        $shipment_row = $this->fetchRow('SELECT * FROM `print_templates` WHERE `code` = "SHIPMENT"');
        if (empty($shipment_row)) {
            $save = $table->insert([
                'code'  => 'SHIPMENT',
                'name' => 'Vận đơn',
                'title_print' => 'In vận đơn',
                'template' => 'shipment.tpl'
            ]);
            $table->saveData();
        }

        // thêm row 'order'
        $shipment_row = $this->fetchRow('SELECT * FROM `print_templates` WHERE `code` = "ORDER"');
        if (empty($shipment_row)) {
            $save = $table->insert([
                'code'  => 'ORDER',
                'name' => 'Đơn đặt hàng',
                'title_print' => 'Hóa đơn bán hàng',
                'template' => 'order.tpl'
            ]);
            $table->saveData();
        }

        // tạo file template print vận đơn
        // tạo file template print đơn hàng
        // tạo file schema brand
        // thêm folder tác giả
        
        $path_syncs = [
            [
                'path_origin' => 'print/html' . DS . 'shipment.tpl',
                'path_sync' => 'Print' . DS . 'shipment.tpl',
            ],
            [
                'path_origin' => 'print/html' . DS . 'order.tpl',
                'path_sync' => 'Print' . DS . 'order.tpl',
            ],
            [
                'path_origin' => 'block/author' . DS . 'view.tpl',
                'path_sync' => 'block/author' . DS . 'view.tpl',
            ],
            [
                'path_origin' => 'block/author' . DS . 'item.tpl',
                'path_sync' => 'block/author' . DS . 'item.tpl',
            ],
            [
                'path_origin' => 'block/author_detail' . DS . 'item.tpl',
                'path_sync' => 'block/author_detail' . DS . 'item.tpl',
            ],
            [
                'path_origin' => 'block/author_detail' . DS . 'item.tpl',
                'path_sync' => 'block/author_detail' . DS . 'item.tpl',
            ],
            [
                'path_origin' => 'element/schema' . DS . 'brand.tpl',
                'path_sync' => 'element/schema' . DS . 'brand.tpl',
            ]
        ];

        foreach ($path_syncs as $key => $sync) {
            $path_origin = !empty($sync['path_origin']) ? $sync['path_origin'] : null;
            $path_sync = !empty($sync['path_sync']) ? $sync['path_sync'] : null;

            if (empty($path_origin) || empty($path_sync)) continue;

            $table_utilities_migrations->syncFile($path_origin, $path_sync);
        }
    }

    public function down()
    {
   
    }
}