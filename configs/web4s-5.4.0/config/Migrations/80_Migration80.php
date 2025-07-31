<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration80 extends AbstractMigration
{

    /**
     * Modify character and length column table
     */

    public function up()
    {
        $query = "ALTER TABLE `articles_content` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
                    ALTER TABLE `articles_content` CHANGE `description` `description` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
                    ALTER TABLE `products_content` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
                    ALTER TABLE `products_content` CHANGE `description` `description` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
                    ALTER TABLE `categories_content` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
                    ALTER TABLE `categories_content` CHANGE `description` `description` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

                    ALTER TABLE `templates_block` CHANGE `collection_data_extend` `collection_data_extend` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
                    ALTER TABLE `templates_block` CHANGE `data_extend` `data_extend` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
                    ALTER TABLE `templates_block` CHANGE `normal_data_extend` `normal_data_extend` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
                    ALTER TABLE `templates_block` CHANGE `config` `config` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

                    ALTER TABLE `brands_content` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
                    ALTER TABLE `comments` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
                    ALTER TABLE `contacts` CHANGE `value` `value` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;";
            $this->execute($query);
    }

    public function down()
    {

    }
}
