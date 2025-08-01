<?php
declare(strict_types=1);

use Migrations\AbstractMigration;
use Cake\Filesystem\File;
use Cake\ORM\TableRegistry;

class Migration70 extends AbstractMigration
{
    /**
     * 
     * - tạo file template print đơn hàng
     * 
     */

    public function up()
    {
        // đọc thông tin template print mặc định
        $url_print = ROOT . DS . 'sync/print/html' . DS . 'order.tpl';
        $file_print = new File($url_print, false);

        if(!$file_print->exists()) return false;

        // check xem ở thư mục template đã có file template print shipment.tpl chưa. Nếu chưa có thì thêm
        // get info template
        $template = TableRegistry::get('Templates')->getTemplateDefault();
        $template_code = !empty($template['code']) ? $template['code'] : null;
        if(empty($template_code)) return false;
        
        $path_print_template = SOURCE_DOMAIN  . DS . 'templates' . DS . $template_code . DS . 'Print' . DS . 'order.tpl';
        $file_print_template = new File($path_print_template, false);

        if($file_print_template->exists()) return false;

        // khởi tạo file
        $file_print_template = new File($path_print_template, true);
        chmod($path_print_template, 0755);      // chmode

        $print_content = $file_print->read();     // đọc nội dung file template print mặc định

        $file_print_template->write($print_content, 'w');     // ghi nội dung file template print mặc định vào file template
        $file_print_template->close();
    }

    public function down()
    {
   
    }
}