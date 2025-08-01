<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration75 extends AbstractMigration
{

    /**
     * - table `ladipages`
     */

    public function up()
    {   

        if (!$this->hasTable('ladipages')) {
            $query = "
                    CREATE TABLE `ladipages` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                      `status` int(1) DEFAULT NULL,
                      `ladipage_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                      `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                      `created` int(11) DEFAULT NULL,
                      `updated` int(11) DEFAULT NULL,
                      `deleted` int(11) DEFAULT 0,
                      PRIMARY KEY (`id`) USING BTREE
                    ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;";
                    
            $this->execute($query); 
        }

    }

    public function down()
    {

    }
}
