<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Cache\Cache;

class LayoutBuilderController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function beforeRender(EventInterface $event)
    {
        parent::beforeRender($event);

        $this->viewBuilder()->setLayout('layout_builder');
    }
    
    public function index()
    {
        if($this->request->is('mobile') || $this->request->is('tablet')) {
            die('Only supported on desktop');
        }

        $this->set('title_for_layout', __d('admin', 'cau_hinh_giao_dien'));
    }

    public function loadConfigBlockModal()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        
        $data = $this->getRequest()->getData();
        $code = !empty($data['code']) ? $data['code'] : null;
        if (!$this->getRequest()->is('post') || empty($code)) die(__d('admin', 'du_lieu_khong_hop_le'));

        $block_info = TableRegistry::get('TemplatesBlock')->find()->where([
            'template_code' => CODE_TEMPLATE,
            'code' => $code,
            'deleted' => 0
        ])->first();

        if(empty($block_info)) die(__d('admin', 'khong_lay_duoc_thong_tin_ban_ghi'));

        $config = !empty($block_info['config']) ? json_decode($block_info['config'], true) : [];
        $type = !empty($block_info['type']) ? $block_info['type'] : null;
        
        $files = $files_view = $files_item = [];
        $file_first_content = null;
        $path_first_file = null;
        
        if(!empty($type) && $type != HTML){
            $path_view = $this->loadComponent('Block')->getPathViewBlock($code);            
            if(!empty($path_view)){
                $folder = new Folder($path_view, false);
                $list_files = $folder->find('.*\.tpl', true);

                if(!empty($list_files)){
                    foreach ($list_files as $k => $file) {
                        $files[$file] = $file;
                        if(strpos($file, 'view') > -1){
                            $files_view[$file] = $file;
                        }

                        if(strpos($file, 'sub') > -1){
                            $files_item[$file] = $file;
                        }
                    }
                }
            }

            $file_first = !empty($block_info['view']) ? $block_info['view'] : reset($files);
            if(!empty($file_first)){
                $file_first_obj = new File($path_view . DS . $file_first, false);
                $file_first_content = $file_first_obj->read();
            }

            $dir_file = $path_view . DS . $file_first;
            $path_first_file = TableRegistry::get('Utilities')->dirToPath($dir_file);
        }

        $this->set('block_info', $block_info);
        $this->set('config', $config);
        $this->set('code', $code);
        $this->set('files', $files);
        $this->set('files_view', $files_view);
        $this->set('files_item', $files_item);
        $this->set('type', !empty($block_info['type']) ? $block_info['type'] : null);
        $this->set('file_first_content', !empty($file_first_content) ? $file_first_content : '');
        $this->set('path_first_file', $path_first_file);

        $this->render('TemplateBlock/block_config');
    }
}