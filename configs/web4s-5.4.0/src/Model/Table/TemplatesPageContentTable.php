<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

class TemplatesPageContentTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('templates_page_content');
        $this->setPrimaryKey('id');
    }

    public function checkExistUrl($url = null, $code = null)
    {
        if(empty($url)) return false;

        $where = [
            'TemplatesPageContent.url' => trim($url),
        ];
        
        if (defined('CODE_TEMPLATE') && CODE_TEMPLATE !== null) {
            $where['TemplatesPageContent.template_code'] = CODE_TEMPLATE;
        }

        if(!empty($code)){
            $where['TemplatesPageContent.page_code <>'] = $code;
        }

        $page = TableRegistry::get('TemplatesPageContent')->find()->where($where)->first();
        return !empty($page->id) ? true : false;
    }

    public function getSeoInfoTemplate($params = [])
    {
        $lang = !empty($params['lang']) ? $params['lang'] : null;
        $page_code = !empty($params['page_code']) ? $params['page_code'] : null;
        if(empty($lang) || empty($page_code)) return [];

        $where = [
            'TemplatesPageContent.page_code' => $page_code,
            'TemplatesPageContent.lang' => $lang
        ];
        
        if (defined('CODE_TEMPLATE') && CODE_TEMPLATE !== null) {
            $where['TemplatesPageContent.template_code'] = CODE_TEMPLATE;
        }
        
        $result = TableRegistry::get('TemplatesPageContent')->find()->where($where)->select([
            'seo_title', 
            'seo_description',
            'seo_keyword',
            'seo_image'
        ])->first();
        
        return $result;        
    }
}