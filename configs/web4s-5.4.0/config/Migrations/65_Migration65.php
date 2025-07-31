<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration65 extends AbstractMigration
{
    /**
     * - cập nhật lại dữ liệu source của bảng order | tăng length từ 20 -> 100
     * 
     */

    public function up()
    {
        if ($this->table('orders')->hasColumn('source')) {
            $this->execute('ALTER TABLE `orders` MODIFY `source` varchar(255) DEFAULT NULL COMMENT "Nguồn đơn hàng"');
        }
    }

    public function down()
    {
   
    }
}