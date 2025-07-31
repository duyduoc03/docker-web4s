<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration57 extends AbstractMigration
{

    /**
     * - thêm bảng `shops`
     * - thêm bảng `shops_content`
     * 
     */

    public function up()
    {
        // thêm bảng `shops`
        if (!$this->hasTable('shops')) {
            $query = "
                CREATE TABLE `shops`  (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `district_id` int(11) NULL DEFAULT NULL,
                  `city_id` int(11) NULL DEFAULT NULL,
                  `created` int(11) NULL DEFAULT NULL,
                  `updated` int(11) NULL DEFAULT NULL,
                  `created_by` int(2) NULL DEFAULT NULL,
                  `position` int(11) NULL DEFAULT NULL,
                  `status` int(1) NULL DEFAULT NULL,
                  `deleted` int(1) NULL DEFAULT NULL,
                  PRIMARY KEY (`id`) USING BTREE,
                  INDEX `id`(`id`) USING BTREE,
                  INDEX `city_id`(`city_id`) USING BTREE,
                  INDEX `status`(`status`) USING BTREE,
                  INDEX `deleted`(`deleted`) USING BTREE
                ) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;";
            $this->execute($query);
        }

        // thêm bảng `shops_content`
        if (!$this->hasTable('shops_content')) {
            $query = "
                CREATE TABLE `shops_content`  (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `shop_id` int(11) NULL DEFAULT NULL,
                  `name` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `hours_operation` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `phone` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `hotline` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `address` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `lang` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `search_unicode` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                  PRIMARY KEY (`id`) USING BTREE,
                  INDEX `id`(`id`) USING BTREE,
                  INDEX `name`(`name`(255)) USING BTREE,
                  INDEX `city_id`(`address`(255)) USING BTREE,
                  INDEX `category_id`(`hours_operation`) USING BTREE
                ) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;";
            $this->execute($query);
        }
    }

    public function down()
    {
   
    }
}
