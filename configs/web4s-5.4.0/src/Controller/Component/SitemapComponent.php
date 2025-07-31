<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Http\Exception\NotFoundException;

class SitemapComponent extends Component
{
	public $controller = null;
    public $table = null;
    public $lastmod_default = '';
    public $domain = '';
    public $limit = 1000;

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();

        $this->table = TableRegistry::get('Links');

        $request = $this->controller->getRequest();
        $this->domain =  $request->scheme() . '://' . $request->host() . '/';

        $this->lastmod_default = date(DATE_ATOM, strtotime(date('Y-m-d')));
    }

    public function getSitemap($type = null, $page = 1)
    {
        if(empty($type)) return [];
        
        $result = [];

        // get page url
        $result = [];
        if($type == PAGE || $type == ALL){
            $this->_pageSitemap($result, $page);
        }

        if(in_array($type, [CATEGORY_PRODUCT, CATEGORY_ARTICLE]) || $type == ALL){
            $this->_categorySitemap($result, $type, $page);
        }

        if($type == PRODUCT || $type == ALL){
            $this->_productSitemap($result, $page);
        }

        if($type == BRAND || $type == ALL){
            $this->_brandSitemap($result, $page);
        }
        
        if($type == ARTICLE || $type == ALL){
            $this->_articleSitemap($result, $page);
        }

        if($type == AUTHOR || $type == ALL){
            $this->_authorSitemap($result, $page);
        }

        if($type == TAG || $type == ALL){
            $this->_tagSitemap($result, $page);
        }

        return $result;
    }

    public function getSiteMapGroup()
    {
        $result = [];

        $this->_pageGroup($result);
        $this->_categoryProductGroup($result);
        $this->_categoryArticleGroup($result);
        $this->_productGroup($result);
        $this->_brandGroup($result);
        $this->_articleGroup($result);
        $this->_authorGroup($result);
        $this->_tagGroup($result);

        return $result;
    }

    // =============================== PAGE
    private function _pageSitemap(&$result = [], $page = 1)
    {
        $pages = TableRegistry::get('TemplatesPage')->find()->where([
            'TemplatesPage.template_code' => defined('CODE_TEMPLATE') ? CODE_TEMPLATE : null,
            'TemplatesPage.page_type' => PAGE,
            'TemplatesPageContent.template_code' => defined('CODE_TEMPLATE') ? CODE_TEMPLATE : null,
            'TemplatesPageContent.lang' => LANGUAGE,
            'OR' => [
                'TemplatesPage.type' => HOME,
                'AND' => [
                    'TemplatesPageContent.url !=' => '',
                    'TemplatesPageContent.url IS NOT' => null
                ]
            ]
        ])->contain(['TemplatesPageContent'])->select([
            'TemplatesPage.created', 
            'TemplatesPageContent.url'
        ])->order('TemplatesPage.id ASC')->toList();

        if(empty($pages)) return $result;

        foreach($pages as $key => $page) {
            $loc = !empty($page['TemplatesPageContent']['url']) ? $this->domain . $page['TemplatesPageContent']['url'] : $this->domain;
            $result[] = [
                'loc' => $loc,
                'lastmod' => !empty($page['created']) ? date(DATE_ATOM, intval($page['created'])) : $this->lastmod_default
            ];
        }

        return $result;
    }

    private function _pageGroup(&$result = [])
    {
        $last_update = TableRegistry::get('TemplatesPage')->find()->where([
            'template_code' => defined('CODE_TEMPLATE') ? CODE_TEMPLATE : null,
            'page_type' => PAGE
        ])->select(['created'])->order('created DESC')->first();
        if(empty($last_update)) return $result;

        $result[] = [
            'loc' => $this->domain . 'sitemap-' . PAGE . '.xml',
            'lastmod' => !empty($last_update['created']) ? date(DATE_ATOM, $last_update['created']) : $this->lastmod_default
        ];

        return $result;
    }

    // =============================== CATEGORY
    private function _categorySitemap(&$result = [], $type = null, $page = 1)
    {
        if(empty($type) || empty($page) || !in_array($type, [
            CATEGORY_PRODUCT, 
            CATEGORY_ARTICLE
        ])) return $result;

        $where = [
            'Links.lang' => LANGUAGE,
            'Links.deleted' => 0,
            'Links.type' => $type,
            'Categories.status' => 1,
            'Categories.deleted' => 0
        ];

        switch($type){
            case CATEGORY_PRODUCT:
                $where['Categories.type'] = PRODUCT;
                break;

            case CATEGORY_ARTICLE:
                $where['Categories.type'] = ARTICLE;
                break;
        }
        
        $links = $this->table->find()->contain(['Categories'])->where($where)->select(['Links.url', 'Links.updated'])->limit(1000)->toList();         
        if(empty($links)) return $result;

        foreach ($links as $key => $link) {
            $result[] = [
                'loc' => !empty($link['url']) ? $this->domain . $link['url'] : $this->domain,
                'lastmod' => !empty($link['updated']) ? date(DATE_ATOM, intval($link['updated'])) : $this->lastmod_default
            ];
        }

        return $result;
    }

    private function _categoryProductGroup(&$result = [])
    {
        $where = [
            'Links.lang' => LANGUAGE,
            'Links.deleted' => 0,
            'Links.type' => CATEGORY_PRODUCT,
            'Categories.type' => PRODUCT,
            'Categories.status' => 1,
            'Categories.deleted' => 0
        ];

        $has_record = $this->table->find()->contain(['Categories'])->where($where)->select(['Links.id'])->first();        
        if(empty($has_record)) return $result;

        $last_update = $this->table->find()->contain(['Categories'])->where($where)->select('Links.updated')->order('Links.updated DESC')->first();

        $result[] = [
            'loc' => $this->domain . 'sitemap-' . CATEGORY_PRODUCT . '.xml',
            'lastmod' => !empty($last_update['updated']) ? date(DATE_ATOM, $last_update['updated']) : $this->lastmod_default
        ];

        return $result;
    }

    private function _categoryArticleGroup(&$result = [])
    {
        $last_update = $this->table->find()->contain(['Categories'])->where([
            'Links.lang' => LANGUAGE,
            'Links.deleted' => 0,
            'Links.type' => CATEGORY_ARTICLE,
            'Categories.type' => ARTICLE,
            'Categories.status' => 1,
            'Categories.deleted' => 0
        ])->select('Links.updated')->order('Links.updated DESC')->first();
        if(empty($last_update)) return $result;

        $result[] = [
            'loc' => $this->domain . 'sitemap-' . CATEGORY_ARTICLE . '.xml',
            'lastmod' => !empty($last_update['updated']) ? date(DATE_ATOM, $last_update['updated']) : $this->lastmod_default
        ];

        return $result;
    }

    // =============================== PRODUCT
    private function _productSitemap(&$result = [], $page = 1)
    {
        $where = [
            'Links.lang' => LANGUAGE,
            'Links.deleted' => 0,
            'Links.type' => PRODUCT_DETAIL,
            'Products.status' => 1,
            'Products.deleted' => 0
        ];

        $query = $this->table->find()->contain(['Products'])->where($where)->select(['Links.url', 'Links.updated']);  
        try {
            $links = $this->controller->paginate($query, [
                'limit' => $this->limit,
                'maxLimit' => $this->limit,
                'page' => $page,
                'order' => [
                    'updated' => 'DESC'
                ]
            ])->toList();

        } catch (NotFoundException $e) {
            $links = [];
        }

        if(empty($links)) return $result;

        foreach ($links as $key => $link) {
            $result[] = [
                'loc' => !empty($link['url']) ? $this->domain . $link['url'] : $this->domain,
                'lastmod' => !empty($link['updated']) ? date(DATE_ATOM, intval($link['updated'])) : $this->lastmod_default
            ];
        }

        return $result;
    }

    private function _productGroup(&$result = [])
    {
        $where = [
            'Links.lang' => LANGUAGE,
            'Links.deleted' => 0,
            'Links.type' => PRODUCT_DETAIL,
            'Products.status' => 1,
            'Products.deleted' => 0
        ];

        $number = $this->table->find()->contain(['Products'])->where($where)->select('Links.updated')->count();
        if(empty($number)) return $result;

        $last_update = $this->table->find()->contain(['Products'])->where($where)->select('Links.updated')->order('Links.updated DESC')->first();

        $total = 0;
        for($i = 1; $i < 1000; $i++){
            if($total> $number) break;
            $loc = $this->domain . 'sitemap-' . PRODUCT . '-' . $i . '.xml';
            if($i == 1) $loc = $this->domain . 'sitemap-' . PRODUCT . '.xml';

            $result[] = [
                'loc' => $loc,
                'lastmod' => !empty($last_update['updated']) ? date(DATE_ATOM, $last_update['updated']) : $this->lastmod_default
            ];

            $total += $this->limit;
        }
        

        return $result;
    }

    // =============================== BRAND
    private function _brandGroup(&$result = [])
    {
        $where = [
            'Links.lang' => LANGUAGE,
            'Links.deleted' => 0,
            'Links.type' => BRAND_DETAIL,
            'Brands.status' => 1,
            'Brands.deleted' => 0
        ];

        $number = $this->table->find()->contain(['Brands'])->where($where)->select('Links.updated')->count();
        if(empty($number)) return $result;

        $last_update = $this->table->find()->contain(['Brands'])->where($where)->select('Links.updated')->order('Links.updated DESC')->first();

        $total = 0;
        for($i = 1; $i < 1000; $i++){
            if($total> $number) break;

            $loc = $this->domain . 'sitemap-' . BRAND . '-' . $i . '.xml';
            if($i == 1) $loc = $this->domain . 'sitemap-' . BRAND . '.xml';

            $result[] = [
                'loc' => $loc,
                'lastmod' => !empty($last_update['updated']) ? date(DATE_ATOM, $last_update['updated']) : $this->lastmod_default
            ];

            $total += $this->limit;
        }
        

        return $result;
    }

    private function _brandSitemap(&$result = [], $page = 1)
    {
        $where = [
            'Links.lang' => LANGUAGE,
            'Links.deleted' => 0,
            'Links.type' => BRAND_DETAIL,
            'Brands.status' => 1,
            'Brands.deleted' => 0
        ];

        $query = $this->table->find()->contain(['Brands'])->where($where)->select(['Links.url', 'Links.updated']);  
        try {
            $links = $this->controller->paginate($query, [
                'limit' => $this->limit,
                'maxLimit' => $this->limit,
                'page' => $page,
                'order' => [
                    'updated' => 'DESC'
                ]
            ])->toList();
        } catch (NotFoundException $e) {
            $links = [];
        }

        if(empty($links)) return $result;

        foreach ($links as $key => $link) {
            $result[] = [
                'loc' => !empty($link['url']) ? $this->domain . $link['url'] : $this->domain,
                'lastmod' => !empty($link['updated']) ? date(DATE_ATOM, intval($link['updated'])) : $this->lastmod_default
            ];
        }

        return $result;
    }

    // =============================== ARTICLE
    private function _articleGroup(&$result = [])
    {
        $where = [
            'Links.lang' => LANGUAGE,
            'Links.deleted' => 0,
            'Links.type' => ARTICLE_DETAIL,
            'Articles.status' => 1,
            'Articles.deleted' => 0
        ];

        $number = $this->table->find()->contain(['Articles'])->where($where)->select('Links.updated')->count();
        if(empty($number)) return $result;

        $last_update = $this->table->find()->contain(['Articles'])->where($where)->select('Links.updated')->order('Links.updated DESC')->first();

        $total = 0;
        for($i = 1; $i < 1000; $i++){
            if($total> $number) break;

            $loc = $this->domain . 'sitemap-' . ARTICLE . '-' . $i . '.xml';
            if($i == 1) $loc = $this->domain . 'sitemap-' . ARTICLE . '.xml';

            $result[] = [
                'loc' => $loc,
                'lastmod' => !empty($last_update['updated']) ? date(DATE_ATOM, $last_update['updated']) : $this->lastmod_default
            ];

            $total += $this->limit;
        }
        

        return $result;
    }

    private function _articleSitemap(&$result = [], $page = 1)
    {
        $where = [
            'Links.lang' => LANGUAGE,
            'Links.deleted' => 0,
            'Links.type' => ARTICLE_DETAIL,
            'Articles.status' => 1,
            'Articles.deleted' => 0
        ];

        $query = $this->table->find()->contain(['Articles'])->where($where)->select(['Links.url', 'Links.updated']);  
        try {
            $links = $this->controller->paginate($query, [
                'limit' => $this->limit,
                'maxLimit' => $this->limit,
                'page' => $page,
                'order' => [
                    'updated' => 'DESC'
                ]
            ])->toList();

        } catch (NotFoundException $e) {
            $links = [];
        }

        if(empty($links)) return $result;

        foreach ($links as $key => $link) {
            $result[] = [
                'loc' => !empty($link['url']) ? $this->domain . $link['url'] : $this->domain,
                'lastmod' => !empty($link['updated']) ? date(DATE_ATOM, intval($link['updated'])) : $this->lastmod_default
            ];
        }

        return $result;
    }

    // author
    private function _authorGroup(&$result = [])
    {
        $where = [
            'Links.lang' => LANGUAGE,
            'Links.deleted' => 0,
            'Links.type' => AUTHOR_DETAIL,
            'Authors.status' => 1,
            'Authors.deleted' => 0
        ];

        $list = $this->table->find()->contain(['Authors'])->where($where)->select()->toList();
        $number = $this->table->find()->contain(['Authors'])->where($where)->select('Links.updated')->count();
        
        if(empty($number)) return $result;

        $last_update = $this->table->find()->contain(['Authors'])->where($where)->select('Links.updated')->order('Links.updated DESC')->first();

        $total = 0;
        for($i = 1; $i < 1000; $i++){
            if($total> $number) break;

            $loc = $this->domain . 'sitemap-' . AUTHOR . '-' . $i . '.xml';
            if($i == 1) $loc = $this->domain . 'sitemap-' . AUTHOR . '.xml';

            $result[] = [
                'loc' => $loc,
                'lastmod' => !empty($last_update['updated']) ? date(DATE_ATOM, $last_update['updated']) : $this->lastmod_default
            ];

            $total += $this->limit;
        }
        

        return $result;
    }

    private function _authorSitemap(&$result = [], $page = 1)
    {
        $where = [
            'Links.lang' => LANGUAGE,
            'Links.deleted' => 0,
            'Links.type' => AUTHOR_DETAIL,
            'Authors.status' => 1,
            'Authors.deleted' => 0
        ];

        $query = $this->table->find()->contain(['Authors'])->where($where)->select(['Links.url', 'Links.updated']);  
        try {
            $links = $this->controller->paginate($query, [
                'limit' => $this->limit,
                'maxLimit' => $this->limit,
                'page' => $page,
                'order' => [
                    'updated' => 'DESC'
                ]
            ])->toList();

        } catch (NotFoundException $e) {
            $links = [];
        }

        if(empty($links)) return $result;

        foreach ($links as $key => $link) {
            $result[] = [
                'loc' => !empty($link['url']) ? $this->domain . $link['url'] : $this->domain,
                'lastmod' => !empty($link['updated']) ? date(DATE_ATOM, intval($link['updated'])) : $this->lastmod_default
            ];
        }

        return $result;
    }

    // =============================== TAG
    private function _tagGroup(&$result = [])
    {
        $where = ['lang' => LANGUAGE];

        $table = TableRegistry::get('Tags');
        $number = $table->find()->where($where)->select(['id'])->count();
        if(empty($number)) return $result;

        $last_update = $table->find()->where($where)->select('id')->order('updated DESC')->first();

        $total = 0;
        for($i = 1; $i < 1000; $i++){
            if($total> $number) break;

            $loc = $this->domain . 'sitemap-' . TAG . '-' . $i . '.xml';
            if($i == 1) $loc = $this->domain . 'sitemap-' . TAG . '.xml';

            $result[] = [
                'loc' => $loc,
                'lastmod' => !empty($last_update['updated']) ? date(DATE_ATOM, $last_update['updated']) : $this->lastmod_default
            ];

            $total += $this->limit;
        }
        

        return $result;
    }

    private function _tagSitemap(&$result = [], $page = 1)
    {
        $table = TableRegistry::get('Tags');

        $query = $table->find()->where([
            'lang' => LANGUAGE
        ])->select(['url', 'updated']);  
        try {
            $tags = $this->controller->paginate($query, [
                'limit' => $this->limit,
                'maxLimit' => $this->limit,
                'page' => $page,
                'order' => [
                    'updated' => 'DESC'
                ]
            ])->toList();

        } catch (Exception $e) {
            $tags = [];
        }
        if(empty($tags)) return $result;

        foreach($tags as $tag){          
            $result[] = [
                'loc' => !empty($tag['url']) ? $this->domain . substr(TAG_PATH, 1) . '/' . $tag['url'] : $this->domain,
                'lastmod' => !empty($tag['updated']) ? date(DATE_ATOM, intval($tag['updated'])) : $this->lastmod_default
            ];
        }

        return $result;        
    }
}
