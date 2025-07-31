<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration63 extends AbstractMigration
{
    /**
     * 
     * - thêm field `tracking_source` trong bảng `contacts`
     * - thêm field `google_sheet_config`, `google_sheet_google_sheet_status` trong bảng `contacts_form`
     * 
     */

    public function up()
    {

        if (!$this->table('contacts')->hasColumn('tracking_source')) {
            $this->table('contacts')->addColumn('tracking_source', 'string', [
                'after' => 'value',
                'limit' => 50,
                'null' => true,
                'comment' => 'Nguồn thông tin'
            ])->update();
        }

        if (!$this->table('contacts_form')->hasColumn('google_sheet_config')) {
            $this->table('contacts_form')->addColumn('google_sheet_config', 'text', [
                'after' => 'fields',
                'null' => true,
                'comment' => 'Cấu hình bảng tính'
            ])->update();
        }

        if (!$this->table('contacts_form')->hasColumn('google_sheet_status')) {
            $this->table('contacts_form')->addColumn('google_sheet_status', 'integer', [
                'after' => 'google_sheet_config',
                'limit' => 1,
                'null' => true
            ])->update();
        }

    }

    public function down()
    {
   
    }
}