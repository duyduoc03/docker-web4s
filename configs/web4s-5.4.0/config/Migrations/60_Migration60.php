<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration60 extends AbstractMigration
{
    /**
     * 
     * - thêm field `config` trong bảng `users`
     * 
     */

    public function up()
    {
        $table = $this->table('users');
        
        if (!$table->hasColumn('config')) {
            $table->addColumn('config', 'text', [
                'after' => 'address',
                'null' => true,
                'comment' => 'Cấu hình riêng của tài khoản'
            ])->update();
        }
    }

    public function down()
    {
   
    }
}