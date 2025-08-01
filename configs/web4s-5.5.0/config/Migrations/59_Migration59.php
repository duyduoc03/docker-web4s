<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration59 extends AbstractMigration
{
    /**
     * 
     * - thêm field `position` trong bảng `extends_record`
     * 
     */

    public function up()
    {
        $table = $this->table('extends_record');        
        
        if (!$table->hasColumn('position')) {
            $table->addColumn('position', 'integer', [
                'after' => 'collection_id',
                'limit' => 11,                
                'null' => true,
            ])->update();
        }
    }

    public function down()
    {
   
    }
}