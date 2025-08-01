<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration64 extends AbstractMigration
{
    /**
     * - thêm bảng `orders_log`
     * 
     */

    public function up()
    {
        // thêm bảng `orders_log`
        if (!$this->hasTable('orders_log')) {
            $query = "
                CREATE TABLE `orders_log`  (
                  `id` int NOT NULL AUTO_INCREMENT,
                  `order_id` int NULL DEFAULT NULL,
                  `status` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Trạng thái đơn hàng',
                  `updated_by` int NULL DEFAULT NULL,
                  `created` int NULL DEFAULT NULL,
                  PRIMARY KEY (`id`) USING BTREE,
                  INDEX `order_id`(`order_id` ASC) USING BTREE,
                  INDEX `status`(`status` ASC) USING BTREE,
                  INDEX `created_by`(`updated_by` ASC) USING BTREE,
                  INDEX `created`(`created` ASC) USING BTREE
                ) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
            $this->execute($query);
        }
    }

    public function down()
    {
   
    }
}