<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration73 extends AbstractMigration
{

    /**
     * 
     * - add index cho `customer_id` bảng orders
     *
     */

    public function up()
    {
        if ($this->hasTable('orders') && $this->table('orders')->hasColumn('customer_id')) {

            $this->execute("DROP INDEX IF EXISTS `customer_id` ON `orders`;");
            $this->execute("DROP INDEX IF EXISTS `customer_id_2` ON `orders`;");

            $this->execute("ALTER TABLE `orders` ADD INDEX `customer_id`(`customer_id`) USING BTREE;");
            $this->execute("ALTER TABLE `orders` ADD INDEX `customer_id_2`(`customer_id`, `deleted`) USING BTREE;");
        }
    }

    public function down()
    {
    
    }
}
