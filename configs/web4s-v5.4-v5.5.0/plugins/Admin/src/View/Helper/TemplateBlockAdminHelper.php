<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class TemplateBlockAdminHelper extends Helper
{   

    public function formatDataConfig($config = null, $lang_log = null)
    {
        if(empty($config)) return [];

        if(empty($lang_log)) $lang_log = LANGUAGE;

        $data_config = json_decode($config, true);
       
        $data_config['cache'] = !empty($data_config['cache']) ? __d('admin', 'co') : __d('admin', 'khong');
        $data_config['has_pagination'] = !empty($data_config['has_pagination']) ? __d('admin', 'co') : __d('admin', 'khong');
        $data_config['html_content'] = !empty($data_config['html_content']) ? htmlentities($data_config['html_content']) : null;
        $data_type = !empty($data_config['data_type']) ? $data_config['data_type'] : null;
        $data_ids = !empty($data_config['data_ids']) ? $data_config['data_ids'] : null;
        $data_item = !empty($data_config['item']) ? $data_config['item'] : [];

        foreach ($data_item as $key => $item) {
            $data_item[$key] = !empty($item) ? json_encode($item) : null;
        }
        $data_config['item'] = implode(', ', $data_item);

        switch($data_type){
                case PRODUCT:
                    foreach ($data_ids as $key => $item) {
                        $item_product = TableRegistry::get('Products')->getAllNameContent($item);
                        
                        $data_ids[$key] = !empty($item_product) ? $item_product[$lang_log] : null;
                    }
                    $data_config['data_ids'] = implode(', ', $data_ids);
                    break;

                case ARTICLE:
                    foreach ($data_ids as $key => $item) {
                        $item_article = TableRegistry::get('Articles')->getAllNameContent($item);
                        $data_ids[$key] = !empty($item_article) ? $item_article[$lang_log] : null;
                    }
                    $data_config['data_ids'] = implode(', ', $data_ids);
                    break;

                case 'category_product':
                case 'category_article':
                    foreach ($data_ids as $key => $item) {
                        $item_category = TableRegistry::get('Categories')->getAllNameContent($item);
                        $data_ids[$key] = !empty($item_category) ? $item_category[$lang_log] : null;
                    }
                    $data_config['data_ids'] = implode(', ', $data_ids);
                    break;    
            }

        return $data_config;
    }
}
