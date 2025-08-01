<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration62 extends AbstractMigration
{
    /**
     * 
     * - thêm field `version` trong bảng `logs`
     * 
     */

    public function up()
    {
        $table = $this->table('logs');
        
        if (!$table->hasColumn('version')) {
            $table->addColumn('version', 'string', [
                'after' => 'path_log',
                'limit' => 20,
                'null' => true,
            ])->update();
        }
    }

    public function down()
    {
   
    }
}