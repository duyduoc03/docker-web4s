<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class SitemapController extends AppController {

    public function initialize(): void
    {
        parent::initialize();

        $this->get_structure_layout = false;

        $response = $this->response->withType('application/xml');
        $this->setResponse($response);

        $this->viewBuilder()->setLayout(SITEMAP);        
    }

    public function index($group = null, $page = 1)
    {    
        $setting_info = TableRegistry::get('Settings')->find()->where([
            'group_setting' => 'sitemap'
        ])->toArray();  
        $setting_info = Hash::combine($setting_info, '{n}.code', '{n}.value');

        $apply_sitemap = !empty($setting_info['apply_sitemap']) ? intval($setting_info['apply_sitemap']) : null;

        if(!empty($apply_sitemap)) {
            $content = null;
            if(file_exists(WWW_ROOT . 'file_sitemap.xml')) {
                $content = file_get_contents(WWW_ROOT . 'file_sitemap.xml');
            }
            
            $this->set('content', $content);
            $this->render('index');
        } else {

            $combine = true;    
            if(empty($group)) $group = ALL;

            if(in_array($group, [
                PAGE, 
                CATEGORY_PRODUCT, 
                CATEGORY_ARTICLE, 
                PRODUCT,
                BRAND,
                ARTICLE, 
                AUTHOR, 
                TAG
            ])) $combine = false;

            $view = 'index';
            if($combine){
                $view = 'group';
                $sitemap = $this->loadComponent('Sitemap')->getSiteMapGroup($page);
            }else{                
                $sitemap = $this->loadComponent('Sitemap')->getSitemap($group, $page);
            }  

            $this->set('sitemap', $sitemap);
            $this->render($view);
        }   
    }

}