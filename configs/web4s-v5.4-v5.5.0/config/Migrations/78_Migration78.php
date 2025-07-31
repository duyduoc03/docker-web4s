<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration78 extends AbstractMigration
{

    /**
     * - thêm  column `vat` vào bảng `products`
     */

    public function up()
    {
        // thêm row 'contact_lucky_spin' trong bảng `email_templates`       
        $table = $this->table('email_templates'); 
        $row = $this->fetchRow('SELECT * FROM `email_templates` WHERE `code` = "CONTACT_LUCKY_SPIN"');
        if (empty($row)) {
            $table->insert([
                'code'  => 'CONTACT_LUCKY_SPIN',
                'name' => 'Khách hàng tham gia quay thưởng',
                'title_email' => 'Khách hàng tham gia quay thưởng',
                'template' => 'contact_lucky_spin.tpl'
            ]);

            $table->saveData();
        }
    }

    public function down()
    {

    }
}
