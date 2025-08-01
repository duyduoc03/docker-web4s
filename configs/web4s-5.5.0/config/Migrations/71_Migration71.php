<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration71 extends AbstractMigration
{
    /**
     * - thêm bảng `authors`
     * 
     */

    public function up()
    {
        // thêm bảng `authors`
        if(!$this->hasTable('authors')) {
            $query = "
                CREATE TABLE `authors`  (
                  `id` int NOT NULL AUTO_INCREMENT,
                  `full_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Tên tác giả',
                  `job_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `phone` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `social` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                  `avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                  `images` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                  `url_video` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `type_video` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `seo_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `seo_description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `seo_keyword` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `position` int NULL DEFAULT NULL,
                  `created` int NULL DEFAULT NULL,
                  `updated` int NULL DEFAULT NULL,
                  `status` int NULL DEFAULT 1,
                  `search_unicode` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                  `deleted` int NULL DEFAULT 0,
                  PRIMARY KEY (`id`) USING BTREE,
                  INDEX `deleted`(`deleted`) USING BTREE,
                  INDEX `status`(`status`, `deleted`) USING BTREE
                ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
            $this->execute($query);
        }

        if(!$this->hasTable('authors')) return true;

        $table = $this->table('articles');
        if (!$table->hasColumn('author_id')) {
            $table->addColumn('author_id', 'integer', [
                'after' => 'main_category_id',
                'limit' => 11,
                'null' => true,
                'comment' => 'ID tác giả'
            ])->update();
        }
    }

    public function down()
    {
   
    }
}