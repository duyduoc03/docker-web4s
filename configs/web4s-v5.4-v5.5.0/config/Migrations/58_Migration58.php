<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration58 extends AbstractMigration
{
    /**
     * 
     * - thêm field `login_error` trong bảng `users`
     * 
     */

    public function up()
    {
        $users_table = $this->table('users');        
        
        if (!$users_table->hasColumn('login_error')) {
            $users_table->addColumn('login_error', 'integer', [
                'after' => 'status',
                'limit' => 5,                
                'null' => true,
                'comment' => 'Số lần tài khoản login sai'
            ])->update();
        }
    }

    public function down()
    {
   
    }
}