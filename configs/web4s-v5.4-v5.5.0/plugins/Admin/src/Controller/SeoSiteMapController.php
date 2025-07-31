<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Hash;
use Cake\Filesystem\File;

class SeoSiteMapController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function index()
    {
        $group = 'sitemap';
        $setting_info = TableRegistry::get('Settings')->find()->where([
            'group_setting' => $group
        ])->toArray();  
        $setting_info = Hash::combine($setting_info, '{n}.code', '{n}.value');


        $this->js_page = [
            '/assets/js/pages/seo_sitemap.js'
        ];

        if(file_exists(WWW_ROOT . 'file_sitemap.xml')) {
            $content = file_get_contents(WWW_ROOT . 'file_sitemap.xml');
        }
        
        $this->set('sitemap', $setting_info);
        $this->set('group', $group);
        $this->set('sitemap_manual', !empty($content) ? 1 : 0);
        $this->set('title_for_layout', __d('admin', 'cau_hinh_sitemap'));
        $this->set('path_menu', 'seo_site_map');
    }

    public function uploadFileSitemap()
    {
        $this->layout = false;
        $this->autoRender = false;

        // validate file upload
        $file = !empty($this->request->getData()) ? $this->request->getData('file') : [];
        if(empty($file)){
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $file_name = $file->getClientFilename();
        if($file_name != 'sitemap.xml') {
            $this->responseJson([MESSAGE => __d('admin', 'ten_tep_khong_chinh_xac')]);
        }

        if($file->getClientMediaType() != 'text/xml'){
            $this->responseJson([MESSAGE => __d('admin', 'sai_dinh_dang')]);
        }

        $file_ext = strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
        if($file_ext != 'xml') {
            $this->responseJson([MESSAGE => __d('admin', 'sai_dinh_dang')]);
        }

        // delete old file
        if(file_exists(WWW_ROOT . 'file_sitemap.xml')) {
            $old_file = new File(WWW_ROOT . 'file_sitemap.xml');
            $old_file->delete();
        }

        // upload file
        $upload = $this->loadComponent('Admin.UploadFile')->upload($file, WWW_ROOT, [
            'file_name' => 'file_sitemap.xml',
            'white_list' => ['xml']
        ]);

        if (empty($upload) || (!empty($upload) && $upload['code'] === ERROR)) {
            $this->responseJson([
                MESSAGE => !empty($upload['message']) ? $upload['message'] : __d('admin', 'tai_tep_len_khong_thanh_cong')
            ]);
        }

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'tai_tep_len_thanh_cong')
        ]);        
    }

    public function saveConfigSitemap()
    {
        
    }

}