<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration56 extends AbstractMigration
{

    /**
     * - thêm bảng `addons`
     * - thêm bảng `extends`
     * - thêm bảng `extends_collection`
     * - thêm bảng `extends_record`
     * 
     * - Thêm field vào bảng `templates_block`
            + collection_data_extend
     * 
     */

    public function up()
    {
        // thêm bảng `addons`
        if (!$this->hasTable('addons')) {
            $query = "
                CREATE TABLE `addons`  (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Tên addon',
                  `code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Mã addon',
                  PRIMARY KEY (`id`) USING BTREE,
                  INDEX `id`(`id`) USING BTREE,
                  INDEX `name`(`name`) USING BTREE,
                  INDEX `code`(`code`) USING BTREE
                ) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;";
            $this->execute($query);
        }

        // thêm bảng `extends`
        if (!$this->hasTable('extends')) {
            $query = "
                CREATE TABLE `extends`  (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `collection_id` int(11) NULL DEFAULT NULL,
                  `record_id` int(11) NULL DEFAULT NULL COMMENT 'ID record',
                  `field` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Mã field',
                  `value` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Giá trị',
                  `lang` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Nếu field không đa ngôn ngữ -> lang = all',
                  PRIMARY KEY (`id`) USING BTREE,
                  INDEX `collection_id`(`collection_id`) USING BTREE,
                  INDEX `collection_id_2`(`collection_id`, `record_id`) USING BTREE,
                  INDEX `collection_id_3`(`collection_id`, `record_id`, `lang`) USING BTREE
                ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
            $this->execute($query);
        }

        // thêm bảng `extends_collection`
        if (!$this->hasTable('extends_collection')) {
            $query = "
                CREATE TABLE `extends_collection`  (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Tên bảng dữ liệu',
                  `code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `fields` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Json danh sách field và kiểu dữ liệu, kiểu input',
                  `form_config` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Cấu hình cấu trúc form',
                  `status` int(11) NULL DEFAULT 1 COMMENT '0 -> không hoạt động, 1->hoạt động, 2-> đang cấu hình',
                  `search_unicode` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `deleted` int(1) NULL DEFAULT 0,
                  PRIMARY KEY (`id`) USING BTREE,
                  INDEX `deleted`(`deleted`) USING BTREE,
                  INDEX `status`(`status`, `deleted`) USING BTREE
                ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
            $this->execute($query);
        }

        // thêm bảng `extends_record`
        if (!$this->hasTable('extends_record')) {
            $query = "
                CREATE TABLE `extends_record`  (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `collection_id` int(11) NULL DEFAULT NULL,
                  `status` int(1) NULL DEFAULT 1,
                  `search_unicode` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                  `created` int(11) NULL DEFAULT NULL,
                  PRIMARY KEY (`id`) USING BTREE,
                  INDEX `collection_id`(`collection_id`) USING BTREE,
                  INDEX `collection_id_2`(`collection_id`, `status`) USING BTREE,
                  INDEX `collection_id_3`(`collection_id`, `created`) USING BTREE
                ) ENGINE = MyISAM AUTO_INCREMENT = 27 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
            $this->execute($query);
        }

        // thêm field `kiotviet_id` vào bảng `templates_block` 
        $table_templates_block = $this->table('templates_block');
        if (!$table_templates_block->hasColumn('collection_data_extend')) {
            $table_templates_block->addColumn('collection_data_extend', 'text', [
                'after' => 'normal_data_extend',
                'limit' => 0,
                'null' => true,
                'comment' => ''
            ])->update();
        }

        // cập nhật lại field email trong bảng email_token
        $table_email_token = $this->table('email_token');
        if ($table_email_token->hasColumn('email')) {
            $this->execute('ALTER TABLE `email_token` MODIFY `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;');
        }
    }

    public function down()
    {
   
    }
}
