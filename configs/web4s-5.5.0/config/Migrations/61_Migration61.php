<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration61 extends AbstractMigration
{
    /**
     * 
     * - thêm field `gmap` trong bảng `shops_content`
     * 
     */

    public function up()
    {   

        if(!$this->hasTable('shops_content')) return true;

        $table = $this->table('shops_content');
        if (!$table->hasColumn('gmap')) {
            $table->addColumn('gmap', 'string', [
                'after' => 'address',
                'limit' => 500,
                'null' => true,
                'comment' => 'Địa chỉ google map'
            ])->update();
        }
    }

    public function down()
    {
   
    }
}