<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration69 extends AbstractMigration
{
    /**
     * - thêm bảng `payments_log`
     * 
     */

    public function up()
    {
        // thêm bảng `payments_log`
        if (!$this->hasTable('payments_log')) {
            $query = "
                CREATE TABLE `payments_log`  (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `payment_id` int(11) NULL DEFAULT NULL,
                  `status` int(1) NULL DEFAULT NULL COMMENT '0-> hủy, 1-> thành công, 2-> chờ duyệt',
                  `amount` decimal(15, 2) NULL DEFAULT NULL COMMENT 'Số tiền',
                  `reference` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Mã tham chiếu',
                  `note` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Ghi chú giao dịch',
                  `updated_by` int(11) NULL DEFAULT NULL COMMENT 'Người thay đổi',
                  `created` int(11) NULL DEFAULT NULL,
                  PRIMARY KEY (`id`) USING BTREE,
                  INDEX `payment_id`(`payment_id`) USING BTREE,
                  INDEX `status`(`status`) USING BTREE,
                  INDEX `amount`(`amount`) USING BTREE,
                  INDEX `updated_by`(`updated_by`) USING BTREE,
                  INDEX `created`(`created`) USING BTREE
                ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;";
            $this->execute($query);
        }
    }

    public function down()
    {
   
    }
}