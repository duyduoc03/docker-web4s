<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration79 extends AbstractMigration
{

    /**
     * - thêm  bảng `tickets`
     */

    public function up()
    {
        if (!$this->hasTable('tickets')) {
            $query = "
                CREATE TABLE `tickets`  (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `parent_id` int(11) NULL DEFAULT NULL,
                  `crm_id` int(11) NULL DEFAULT NULL COMMENT 'ID Ticket Crm',
                  `code` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Mã Ticket',
                  `full_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `phone` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `department` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'SALE, SUPPORT',
                  `priority` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'LOW, MEDIUM, HIGH',
                  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                  `files` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Tệp đính kèm',
                  `status` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'new' COMMENT 'NEW : Mới\r\nASSIGNED : Đã tiếp nhận\r\nIN_PROGRESS : Đang xử lý,\r\nWAITING_CUSTOMER : Chờ phản hồi khách hàng\r\nRESOLVED : Đã xử lý\r\nCLOSED : Đóng',
                  `created_by` int(11) NULL DEFAULT NULL,
                  `crm_staff_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Tên nhân viên trả lời Ticket bên CRM',
                  `created` int(11) NULL DEFAULT NULL,
                  `updated` int(11) NULL DEFAULT NULL,
                  `deleted` int(1) NULL DEFAULT 0,
                  `search_unicode` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
                  PRIMARY KEY (`id`) USING BTREE,
                  INDEX `id`(`id`) USING BTREE,
                  INDEX `parent_id`(`parent_id`) USING BTREE,
                  INDEX `crm_id`(`crm_id`) USING BTREE,
                  INDEX `code`(`code`) USING BTREE,
                  INDEX `title`(`title`) USING BTREE,
                  INDEX `status`(`status`) USING BTREE,
                  INDEX `created_by`(`created_by`) USING BTREE,
                  INDEX `deleted`(`deleted`) USING BTREE
                ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
            ";
            $this->execute($query);
        }
    }

    public function down()
    {

    }
}
