<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration81 extends AbstractMigration
{

    /**
     * 
     * - thêm  row 'user_name' trong bảng `logs` và `orders_log`
     * 
     */

    public function up()
    {
        $logs_table = $this->table('logs');
        $orders_log_table = $this->table('orders_log');
        // thêm  column `user_name` vào bảng `logs`

        if (!$logs_table->hasColumn('user_name')) {
            $logs_table->addColumn('user_name', 'string', [
                'limit' => 50,
                'after' => 'user_id',
                'null' => true
            ])->update();
        }

        // thêm  column `user_name` vào bảng `orders_log`
        if (!$orders_log_table->hasColumn('user_name')) {
            $orders_log_table->addColumn('user_name', 'string', [
                'limit' => 50,
                'after' => 'updated_by',
                'null' => true
            ])->update();
        }
    }

    public function down()
    {
   
    }
}
