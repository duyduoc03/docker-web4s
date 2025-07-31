<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;
use Cake\Filesystem\File;

class UtilitiesMigrationsTable extends Table
{
    private $dir_sync = ROOT . DS . 'sync';

    public function initialize(array $config): void
    {
        parent::initialize($config);
    }

    public function syncFile($path_origin = null, $path_sync = null)
    {
        // tạo file template
        // đọc thông tin template mặc định
        $url_file = $this->dir_sync . DS . $path_origin;
        $file = new File($url_file, false);

        if(!$file->exists()) return false;

        // check xem ở thư mục template đã có file template shipment.tpl chưa. Nếu chưa có thì thêm
        // get info template
        $template = TableRegistry::get('Templates')->getTemplateDefault();
        $template_code = !empty($template['code']) ? $template['code'] : null;
        if(empty($template_code)) return false;
        
        $path_template = SOURCE_DOMAIN  . DS . 'templates' . DS . $template_code . DS . $path_sync;
        $file_template = new File($path_template, false);

        if($file_template->exists()) return false;

        // khởi tạo file
        $file_template = new File($path_template, true);
        @chmod($path_template, 0755);      // chmode

        $file_content = $file->read();     // đọc nội dung file template mặc định

        $file_template->write($file_content, 'w');     // ghi nội dung file template mặc định vào file template
        $file_template->close();
    }
}