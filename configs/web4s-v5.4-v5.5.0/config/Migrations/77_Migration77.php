<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration77 extends AbstractMigration
{

    /**
     * - thêm  column `vat` vào bảng `products`
     * - thêm bảng `wheel_fortune`
     * - thêm bảng `wheel_fortune_content`
     * - thêm bảng `wheel_fortune_log`
     * - thêm bảng `wheel_options`
     */

    public function up()
    {
        // thêm column 'vat'
        $table = $this->table('products');
        if (!$table->hasColumn('vat')) {
            $table->addColumn('vat', 'integer', [
                'limit' => 2,
                'default' => 0,
                'after' => 'view',
                'null' => true,
                'comment' => 'Vat chung sản phẩm'
            ])->update();
        }

        if (!$this->hasTable('wheel_fortune')) {
            $query = "
                    CREATE TABLE `wheel_fortune`  (
                        `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                        `winning_chance` int(3) NULL DEFAULT NULL,
                        `check_limit` int(1) NULL DEFAULT NULL,
                        `check_ip` int(1) NULL DEFAULT NULL,
                        `config_email` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                        `config_behavior` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                        `start_time` int(11) NULL DEFAULT NULL,
                        `end_time` int(11) NULL DEFAULT NULL,
                        `status` int(1) NULL DEFAULT 0,
                        `created_by` int(11) NULL DEFAULT NULL,
                        `created` int(11) NULL DEFAULT NULL,
                        `updated` int(11) NULL DEFAULT NULL,
                        `deleted` int(1) NULL DEFAULT 0,
                        PRIMARY KEY (`id`) USING BTREE,
                        INDEX `wheel_fortune`(`id`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic";
            $this->execute($query);
        }

        if (!$this->hasTable('wheel_fortune_content')) {
            $query = "
                    CREATE TABLE `wheel_fortune_content`  (
                        `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                        `wheel_id` int(11) NULL DEFAULT NULL,
                        `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `search_unicode` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `lang` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        PRIMARY KEY (`id`) USING BTREE,
                        INDEX `wheel_fortune_content`(`id`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic";
            $this->execute($query);
        }

        if (!$this->hasTable('wheel_fortune_log')) {
            $query = "
                    CREATE TABLE `wheel_fortune_log`  (
                        `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                        `wheel_id` int(11) NULL DEFAULT NULL,
                        `full_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `phone` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `ip` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `winning` int(1) NULL DEFAULT NULL,
                        `prize_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `prize_value` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                        `created` int(11) NULL DEFAULT NULL,
                        `lang` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        PRIMARY KEY (`id`) USING BTREE,
                        INDEX `wheel_fortune_log`(`id`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic";
            $this->execute($query);
        }

        if (!$this->hasTable('wheel_options')) {
            $query = "
                    CREATE TABLE `wheel_options`  (
                        `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                        `wheel_id` int(11) NULL DEFAULT NULL,
                        `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                        `type_award` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `color` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `percent_winning` int(3) NULL DEFAULT NULL,
                        `limit_prize` int(3) NULL DEFAULT NULL,
                        `winning` int(5) NULL DEFAULT NULL,
                        PRIMARY KEY (`id`) USING BTREE,
                        INDEX `wheel_options`(`id`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic";
            $this->execute($query);
        }
    }

    public function down()
    {

    }
}
