<?php
declare(strict_types=1);

use Migrations\AbstractMigration;
use Cake\ORM\TableRegistry;

class Migration76 extends AbstractMigration
{

    /**
     * - Khởi tạo bảng `authors_content`
     * - Chuyển dữ liệu cũ từ bên `authors` sang `authors_content` 
     * - Xóa các cột ở `authors` cũ
     */

    public function up()
    {   

        // kiểm tra tồn tại bảng authors
        if (!$this->hasTable('authors')) return true;

        // tạo table author_content
        
            
        // khởi tạo bảng `authors_content` mới
        if (!$this->hasTable('authors_content')){
            $query = "
                CREATE TABLE `authors_content`  (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `author_id` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `job_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                  `seo_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `seo_description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `seo_keyword` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                  `lang` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '1',
                  `search_unicode` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                  PRIMARY KEY (`id`) USING BTREE,
                  INDEX `status`(`lang`) USING BTREE
                ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
            $this->execute($query);
        }
        
        // lấy data cần chuyển sang author_content 
        $table = TableRegistry::getTableLocator()->get('Authors');
        $content_table = TableRegistry::getTableLocator()->get('AuthorsContent');        
        $language = $this->fetchRow('SELECT `code` FROM `languages` WHERE `is_default` = 1 ');
        $lang = !empty($language['code']) ? $language['code'] : 'vi';
        
        $loop = 1000;
        for ($i = 1; $i <= $loop; $i++) { 
            $authors_record = $table->find()->select()->limit(100)->page($i)->toList();
            if(empty($authors_record)) break;

            foreach($authors_record as $row){
                $author_id = !empty($row['id']) ? intval($row['id']) : null;
                if(empty($author_id)) continue;

                // Lưu từng bản ghi vào bảng authors_content
                $author_content = $content_table->newEntity([
                    'author_id' => $author_id,
                    'job_title' => !empty($row['job_title']) ? $row['job_title'] : null,
                    'description' => !empty($row['description']) ? $row['description'] : null,
                    'content' => !empty($row['content']) ? $row['content'] : null,

                    'seo_title' => !empty($row['seo_title']) ? $row['seo_title'] : null,
                    'seo_description' => !empty($row['seo_description']) ? $row['seo_description'] : null,
                    'seo_keyword' => !empty($row['seo_keyword']) ? $row['seo_keyword'] : null,
                    
                    'search_unicode' => !empty($row['search_unicode']) ? $row['search_unicode'] : null,
                    'lang' => $lang
                ]);

                $save = $content_table->save($author_content);
            }
        }        

        // Xóa các cột không còn cần thiết trong bảng authors
        $table_author = $this->table('authors');

        // xóa field `job_title`
        if ($table_author->hasColumn('job_title')) {
            $table_author->removeColumn('job_title')->update();
        }

        // xóa field `description`
        if ($table_author->hasColumn('description')) {
            $table_author->removeColumn('description')->update();
        }

        // xóa field `content`
        if ($table_author->hasColumn('content')) {
            $table_author->removeColumn('content')->update();
        }

        // xóa field `seo_title`
        if ($table_author->hasColumn('seo_title')) {
            $table_author->removeColumn('seo_title')->update();
        }

        // xóa field `seo_description`
        if ($table_author->hasColumn('seo_description')) {
            $table_author->removeColumn('seo_description')->update();
        }

        // xóa field `seo_keyword`
        if ($table_author->hasColumn('seo_keyword')) {
            $table_author->removeColumn('seo_keyword')->update();
        }
    }

    public function down()
    {
    
    }

}
