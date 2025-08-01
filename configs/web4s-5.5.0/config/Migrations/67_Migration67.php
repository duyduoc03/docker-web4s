<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration67 extends AbstractMigration
{
    /**
     * 
     * - thêm field `tracking_source` trong bảng `customers`
     * - thêm field `ip` trong bảng `contacts`
     * 
     */

    public function up()
    {

        if (!$this->table('customers')->hasColumn('tracking_source')) {
            $this->table('customers')->addColumn('tracking_source', 'string', [
                'after' => 'code',
                'limit' => 50,
                'null' => true,
                'comment' => 'Nguồn thông tin'
            ])->update();
        }

        if (!$this->table('contacts')->hasColumn('ip')) {
            $this->table('contacts')->addColumn('ip', 'string', [
                'after' => 'tracking_source',
                'limit' => 50,
                'null' => true
            ])->update();
        }

    }

    public function down()
    {
   
    }
}