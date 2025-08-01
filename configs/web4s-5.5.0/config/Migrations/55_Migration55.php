<?php
declare(strict_types=1);

use Migrations\AbstractMigration;
use Cake\Utility\Hash;

class Migration55 extends AbstractMigration
{

    /**
     * 
     * thêm trạng thái cho cấu hình Email
     */

    public function up()
    {
        // lấy thông tin cấu hình email
        $email_setting = $this->fetchAll('SELECT `code`, `value` FROM `settings` WHERE `group_setting` = "email"');
        $email_setting = Hash::combine($email_setting, '{n}.code', '{n}.value');

        // nếu có cấu hình email thì thêm trạng thái -> đang hoạt động
        if(!empty($email_setting['email']) && !empty($email_setting['application_password'])){       	
            $data = [
                'group_setting' => 'email',
                'code' => 'status',
                'value' => 1
            ];

            $this->table('settings')->insert($data)->saveData();
        }
    }

    public function down()
    {
   
    }
}
