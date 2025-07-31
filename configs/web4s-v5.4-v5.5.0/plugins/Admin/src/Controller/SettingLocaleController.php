<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Filesystem\File;
use Cake\Datasource\ConnectionManager;
use Laminas\Diactoros\UploadedFile;
use Cake\Http\Exception\NotFoundException;
use Cake\Utility\Text;
use App\Service\ArticleService;
use App\Service\CategoryService;
use App\Service\BrandService;
use App\Service\ProductService;
use App\Service\GoogleTranslateService;

class SettingLocaleController extends AppController {

    public $template_path = '';
    public $limit = 10;

    public function initialize(): void
    {
        parent::initialize();
    }

    public function localesSystem()
    {
        $lang_default = TableRegistry::get('Languages')->getDefaultLanguage();
        
        $this->css_page = [
            '/assets/plugins/global/lightbox/lightbox.css',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];
        $this->js_page = [            
            '/assets/plugins/global/lightbox/lightbox.min.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/js/pages/setting_locales_system.js'

        ];
        $this->set('lang_default', $lang_default);
        $this->set('title_for_layout', __d('admin', 'ngon_ngu_he_thong'));        
    }   

    public function getListLocaleLabel()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        $data = $this->request->getData();
        if (empty($data)) {
            $this->showErrorPage('error', [
                MESSAGE => __d('admin', 'khong_du_co_du_lieu')
            ]);
        }

        $type = !empty($data['type']) ? $data['type'] : 'system_po';
        $filter = !empty($data['filter']) ? $data['filter'] : null;
        $page = !empty($data['page']) ? $data['page'] : 1;
        $languages = TableRegistry::get('Languages')->getList();
        $lang_default = TableRegistry::get('Languages')->getDefaultLanguage();
        $result = [
            DATA => [],
            PAGINATION => [],
        ];
        switch ($type) {
            case 'system_po':
                    $result = $this->_getDataResponseByLocalePo($page, $filter, $languages);
                break;
            case 'system_js':
                    $result = $this->_getDataResponseByLocaleJs($page, $filter, $languages);
                break;
            case 'block':
                    $result = $this->_getDataResponseByBlock($page, $filter, $languages);
                break;
            case 'article':
                    $result = $this->_getDataResponseByArticle($page, $filter, $languages);
                break;
            case 'product':
                    $result = $this->_getDataResponseByProduct($page, $filter, $languages);
                break;
            case 'brand':
                    $result = $this->_getDataResponseByBrand($page, $filter, $languages);
                break;
            case 'category_product':
                    $result = $this->_getDataResponseByCategory($page, $filter, $languages, PRODUCT);
                break;
            case 'category_article':
                    $result = $this->_getDataResponseByCategory($page, $filter, $languages, ARTICLE);
                break;
            case 'extend_collection':
                    $result = $this->_getDataResponseByExtendCollection($page, $filter, $languages);
                break;
            case 'attribute':
                    $result = $this->_getDataResponseByAttribute($page, $filter, $languages);
                break;
            default:
                break;
        }
        if (empty($result) || ($result[CODE] == ERROR)) {
            $this->showErrorPage('error', [
                MESSAGE => $result[MESSAGE]
            ]);
        }

        $this->set('data', !empty($result[DATA]) ? $result[DATA] : []);
        $this->set('languages', $languages);
        $this->set('lang_default', $lang_default);
        $this->set('type', $type);
        $this->set('pagination', !empty($result[PAGINATION]) ? $result[PAGINATION] : []);
        $this->render('element_load_list');    

    }

    // dữ liệu thuộc tính mở rộng
    private function _getDataResponseByAttribute($page = 1, $filter = [], $languages = [])
    {
        if(empty($languages)) {
            return [
                CODE => ERROR,
                MESSAGE => __d('admin', 'khong_co_ngon_ngu_nao_duoc_chon')
            ];
        }

        $utilities = TableRegistry::get('Utilities');
        $keyword = !empty($filter['keyword']) ? $filter['keyword'] : null;
        $where = [
            'Attributes.deleted' => 0
        ];
        $contain = [
            'ContentMutiple' => function ($q) {
                return $q->select(['attribute_id', 'name', 'lang']);
            }
        ];
        if (!empty($keyword)) {
            $contain[] = 'AttributesContent';
            $where['AttributesContent.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }
        
        $attributes = $this->paginate(TableRegistry::get('Attributes')->find()->select(['Attributes.id'])->contain($contain)->order(['Attributes.id' => 'DESC'])
        ->where($where), [
            'limit' => $this->limit,
            'page' => $page
        ])->toArray();

        $data_result = [];
        if (!empty($attributes)) {
            foreach ($attributes as $k => $attribute) {
                $attribute_id = !empty($attribute['id']) ? intval($attribute['id']) : null;
                $name_by_lang = [];
                if (!empty($attribute['ContentMutiple'])) {
                    foreach ($attribute['ContentMutiple'] as $content_item) {
                        $name = !empty($content_item['name']) ? $content_item['name'] : null;
                        $name_by_lang[$content_item['lang']] = $name;
                    }
                }
                $data_record = [];
                foreach ($languages as $lang => $language) {
                    $name = !empty($name_by_lang[$lang]) ? $name_by_lang[$lang] : null;
                    $data_record[$lang] = [
                        'label' => !empty($name) ? $name : null,
                        'id' => $attribute_id,
                        'type' => 'attribute',
                        'edit_url' => ADMIN_PATH . '/setting/attribute/update/' . $attribute_id
                    ];
                }

                $data_result[$attribute_id] = $data_record;
            }
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Attributes']) ? $this->request->getAttribute('paging')['Attributes'] : [];
        
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        return[
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $data_result, 
            PAGINATION => $meta_info
        ];
            
    }

    // ngôn ngữ file po
    private function _getDataResponseByLocalePo($page = 1, $filter = [], $languages = [])
    {   
        if(empty($languages)) {
            return [
                CODE => ERROR,
                MESSAGE => __d('admin', 'khong_co_ngon_ngu_nao_duoc_chon')
            ];
        }
        
        $path = $this->_getListLocaleFilesPo();
        
        if (empty($path)) {
            return [
                CODE => ERROR,
                MESSAGE => __d('admin', 'khong_tim_thay_file_locale')
            ];
        }

        // lấy danh sách tất cả các label locales
        $all_translations = [];
        foreach ($languages as $lang => $lang_name) {
            $filePath = "{$path}/{$lang}/template.po";
    
            if (!file_exists($filePath)) {
                continue;
            }
    
            $translations = $this->_getDataFromFilePo($filePath);
            if (empty($translations)) {
                continue;
            }

            foreach ($translations as $key => $translation) {
                if (!isset($all_translations[$key])) {
                    $all_translations[$key] = [];
                }

                $all_translations[$key][$lang] = [
                    'label' => !empty($translation['msgstr']) ? $translation['msgstr'] : null,
                    'id' => !empty($translation['msgid']) ? $translation['msgid'] : null,
                    'type' => 'locales_po',
                    'url' => null
                ];
            }
        }
        // lọc danh sách theo từ khóa
        if (!empty($filter['keyword'])) {
            $keyword = strtolower($filter['keyword']);
            $all_translations = array_filter($all_translations, function ($translations, $key) use ($keyword) {
                // Kiểm tra từ khóa trong key
                if (strpos(strtolower($key), $keyword) !== false) {
                    return true;
                }
                
                // Kiểm tra từ khóa trong các label của các ngôn ngữ
                foreach ($translations as $lang => $data) {
                    if (isset($data['label']) && strpos(strtolower($data['label']), $keyword) !== false) {
                        return true;
                    }
                }
                
                return false;
            }, ARRAY_FILTER_USE_BOTH);
        }

        // phân trang
        $total = count($all_translations);        
        $data_result = $this->_paginateArray($all_translations, $page, $this->limit);
        
        return [
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $data_result,
            PAGINATION => [
                'page' => $page,
                'pages' => ceil($total / $this->limit),
                'perpage' => $this->limit,
                'current' => count($data_result),
                'total' => $total
            ]
        ];
    }

    // lấy file js trong folder locales của js
    private function _getListLocaleFilesJs()
    {
        $template = TableRegistry::get('Templates')->getTemplateDefault();
        $template_code = !empty($template['code']) ? $template['code'] : null;

        if (empty($template_code)) return '';
        $path = SOURCE_DOMAIN . DS . 'templates' . DS . $template_code . DS . 'assets' . DS . 'js' . DS . 'locales' . DS;
        
        if (!file_exists($path)) return '';

        return $path; 
    }

    // ngôn ngữ file js
    private function _getDataResponseByLocaleJs($page = 1, $filter = [], $languages = [])
    {
        if(empty($languages)) {
            return [
                CODE => ERROR,
                MESSAGE => __d('admin', 'khong_co_ngon_ngu_nao_duoc_chon')
            ];
        }
        
        $path = $this->_getListLocaleFilesJs();

        if (empty($path)) {
            return [
                CODE => ERROR,
                MESSAGE => __d('admin', 'khong_tim_thay_file_locale')
            ];
        }

        // lấy danh sách tất cả các label locales
        $all_translations = [];
        foreach ($languages as $lang => $lang_name) {
            $filePath = "{$path}{$lang}.js";
            
            if (!file_exists($filePath)) {
                continue;
            }
            $translations = $this->_getDataFromFileJs($filePath);
            
            if (empty($translations)) {
                continue;
            }

            foreach ($translations as $key => $translation) {
                if (!isset($all_translations[$key])) {
                    $all_translations[$key] = [];
                }
                $all_translations[$key][$lang] = [
                    'label' => !empty($translation['msgstr']) ? $translation['msgstr'] : null,
                    'id' => !empty($translation['msgid']) ? $translation['msgid'] : null,
                    'type' => 'locales_js'
                ];
            }
        }
        // lọc danh sách theo từ khóa
        if (!empty($filter['keyword'])) {
            $keyword = strtolower($filter['keyword']);
            $all_translations = array_filter($all_translations, function ($translations, $key) use ($keyword) {
                // Kiểm tra từ khóa trong key
                if (strpos(strtolower($key), $keyword) !== false) {
                    return true;
                }
                
                // Kiểm tra từ khóa trong các label của các ngôn ngữ
                foreach ($translations as $lang => $data) {
                    if (isset($data['label']) && strpos(strtolower($data['label']), $keyword) !== false) {
                        return true;
                    }
                }
                
                return false;
            }, ARRAY_FILTER_USE_BOTH);
        }
        // phân trang
        $total = count($all_translations);
        $data_result = $this->_paginateArray($all_translations, $page, $this->limit);

        return [
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $data_result,
            PAGINATION => [
                'page' => $page,
                'pages' => ceil($total / $this->limit),
                'perpage' => $this->limit,
                'current' => count($data_result),
                'total' => $total
            ]
        ];
    }

    // ngôn ngữ bài viết
    public function _getDataResponseByArticle($page = 1, $filter = [], $languages = [])
    {
        if(empty($languages)) {
            return [
                CODE => ERROR,
                MESSAGE => __d('admin', 'khong_co_ngon_ngu_nao_duoc_chon')
            ];
        }
        $utilities = TableRegistry::get('Utilities');
        $keyword = !empty($filter['keyword']) ? $filter['keyword'] : null;
        $where = [
            'Articles.deleted' => 0
        ];
        $contain = [
            'ContentMutiple' => function ($q) {
                return $q->select(['article_id', 'name', 'lang']);
            }
        ];
        if (!empty($keyword)) {
            $contain[] = 'ArticlesContent';
            $where['ArticlesContent.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }
        
        $articles = $this->paginate(TableRegistry::get('Articles')->find()->select(['Articles.id'])->contain($contain)->order(['Articles.id' => 'DESC'])
        ->where($where), [
            'limit' => $this->limit,
            'page' => $page
        ])->toArray();

        $data_result = [];
        if (!empty($articles)) {
            foreach ($articles as $k => $article) {
                $article_id = !empty($article['id']) ? intval($article['id']) : null;
                $name_by_lang = [];
                if (!empty($article['ContentMutiple'])) {
                    foreach ($article['ContentMutiple'] as $content_item) {
                        $name = !empty($content_item['name']) ? $content_item['name'] : null;
                        $name_by_lang[$content_item['lang']] = $name;
                    }
                }
                $data_record = [];
                foreach ($languages as $lang => $language) {
                    $name = !empty($name_by_lang[$lang]) ? $name_by_lang[$lang] : null;
                    $data_record[$lang] = [
                        'label' => !empty($name) ? $name : null,
                        'id' => $article_id,
                        'type' => 'article',
                        'edit_url' => ADMIN_PATH . '/article/update/' . $article_id . '?lang=' . $lang
                    ];
                }

                $data_result[$article_id] = $data_record;
            }
        }
        
        $pagination_info = !empty($this->request->getAttribute('paging')['Articles']) ? $this->request->getAttribute('paging')['Articles'] : [];
        
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        return [
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $data_result, 
            PAGINATION => $meta_info
        ];
    }

    // ngôn ngữ thương hiệu
    public function _getDataResponseByBrand($page = 1, $filter = [], $languages = [])
    {
        if(empty($languages)) {
            return [
                CODE => ERROR,
                MESSAGE => __d('admin', 'khong_co_ngon_ngu_nao_duoc_chon')
            ];
        }
        
        $utilities = TableRegistry::get('Utilities');
        $keyword = !empty($filter['keyword']) ? $filter['keyword'] : null;
        $where = [
            'Brands.deleted' => 0
        ];
        $contain = [
            'ContentMutiple' => function ($q) {
                return $q->select(['brand_id', 'name', 'lang']);
            }
        ];
        if (!empty($keyword)) {
            $contain[] = 'BrandsContent';
            $where['BrandsContent.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }
        
        $brands = $this->paginate(TableRegistry::get('Brands')->find()->select(['Brands.id'])->contain($contain)->order(['Brands.id' => 'DESC'])
        ->where($where), [
            'limit' => $this->limit,
            'page' => $page
        ])->toArray();
        
        $data_result = [];
        if (!empty($brands)) {
            foreach ($brands as $k => $brand) {
                $brand_id = !empty($brand['id']) ? intval($brand['id']) : null;
                $name_by_lang = [];
                if (!empty($brand['ContentMutiple'])) {
                    foreach ($brand['ContentMutiple'] as $content_item) {
                        $name = !empty($content_item['name']) ? $content_item['name'] : null;
                        $name_by_lang[$content_item['lang']] = $name;
                    }
                }
                $data_record = [];
                foreach ($languages as $lang => $language) {
                    $name = !empty($name_by_lang[$lang]) ? $name_by_lang[$lang] : null;
                    $data_record[$lang] = [
                        'label' => !empty($name) ? $name : null,
                        'id' => $brand_id,
                        'type' => 'brand',
                        'edit_url' => ADMIN_PATH . '/brand/update/' . $brand_id . '?lang=' . $lang
                    ];
                }

                $data_result[$brand_id] = $data_record;
            }
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Brands']) ? $this->request->getAttribute('paging')['Brands'] : [];
        
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        return[
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $data_result, 
            PAGINATION => $meta_info
        ];
    }

    // ngôn ngữ sản phẩm
    public function _getDataResponseByProduct($page = 1, $filter = [], $languages = [])
    {
        if(empty($languages)) {
            return [
                CODE => ERROR,
                MESSAGE => __d('admin', 'khong_co_ngon_ngu_nao_duoc_chon')
            ];
        }

        $utilities = TableRegistry::get('Utilities');
        $keyword = !empty($filter['keyword']) ? $filter['keyword'] : null;
        $where = [
            'Products.deleted' => 0
        ];
        $contain = [
            'ContentMutiple' => function ($q) {
                return $q->select(['product_id', 'name', 'lang']);
            }
        ];
        if (!empty($keyword)) {
            $contain[] = 'ProductsContent';
            $where['ProductsContent.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }
        
        $products = $this->paginate(TableRegistry::get('Products')->find()->select(['Products.id'])->contain($contain)->order(['Products.id' => 'DESC'])
        ->where($where), [
            'limit' => $this->limit,
            'page' => $page
        ])->toArray();
        
        $data_result = [];
        if (!empty($products)) {
            foreach ($products as $k => $product) {
                $product_id = !empty($product['id']) ? intval($product['id']) : null;
                $name_by_lang = [];
                if (!empty($product['ContentMutiple'])) {
                    foreach ($product['ContentMutiple'] as $content_item) {
                        $name = !empty($content_item['name']) ? $content_item['name'] : null;
                        $name_by_lang[$content_item['lang']] = $name;
                    }
                }
                $data_record = [];
                foreach ($languages as $lang => $language) {
                    $name = !empty($name_by_lang[$lang]) ? $name_by_lang[$lang] : null;
                    $data_record[$lang] = [
                        'label' => !empty($name) ? $name : null,
                        'id' => $product_id,
                        'type' => 'product',
                        'edit_url' => ADMIN_PATH . '/product/update/' . $product_id . '?lang=' . $lang
                    ];
                }

                $data_result[$product_id] = $data_record;
            }
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Products']) ? $this->request->getAttribute('paging')['Products'] : [];
        
        $meta_info = $utilities->formatPaginationInfo($pagination_info);
        
        return[
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $data_result, 
            PAGINATION => $meta_info
        ];
    }

    // ngôn ngữ danh mục
    public function _getDataResponseByCategory($page = 1, $filter = [], $languages = [], $type_category = 'product')
    {
        if(empty($languages)) {
            return [
                CODE => ERROR,
                MESSAGE => __d('admin', 'khong_co_ngon_ngu_nao_duoc_chon')
            ];
        }

        $utilities = TableRegistry::get('Utilities');
        $keyword = !empty($filter['keyword']) ? $filter['keyword'] : null;
        $where = [
            'Categories.deleted' => 0,
            'Categories.type' => $type_category
        ];
        $contain = [
            'ContentMutiple' => function ($q) {
                return $q->select(['category_id', 'name', 'lang']);
            }
        ];
        if (!empty($keyword)) {
            $contain[] = 'CategoriesContent';
            $where['CategoriesContent.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }
        
        $categories = $this->paginate(TableRegistry::get('Categories')->find()->select(['Categories.id'])->contain($contain)->order(['Categories.id' => 'DESC'])
        ->where($where), [
            'limit' => $this->limit,
            'page' => $page
        ])->toArray();

        $data_result = [];
        if (!empty($categories)) {
            foreach ($categories as $k => $category) {
                $category_id = !empty($category['id']) ? intval($category['id']) : null;
                $name_by_lang = [];
                if (!empty($category['ContentMutiple'])) {
                    foreach ($category['ContentMutiple'] as $content_item) {
                        $name = !empty($content_item['name']) ? $content_item['name'] : null;
                        $name_by_lang[$content_item['lang']] = $name;
                    }
                }
                $data_record = [];
                foreach ($languages as $lang => $language) {
                    $name = !empty($name_by_lang[$lang]) ? $name_by_lang[$lang] : null;
                    $data_record[$lang] = [
                        'label' => !empty($name) ? $name : null,
                        'id' => $category_id,
                        'type' => $type_category,
                        'edit_url' => ADMIN_PATH . '/category/' . $type_category . '/update/' . $category_id . '?lang=' . $lang
                    ];
                }

                $data_result[$category_id] = $data_record;
            }
        }
        $pagination_info = !empty($this->request->getAttribute('paging')['Categories']) ? $this->request->getAttribute('paging')['Categories'] : [];
        
        $meta_info = $utilities->formatPaginationInfo($pagination_info);
        
        return[
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $data_result, 
            PAGINATION => $meta_info
        ];
    }

    // ngôn ngữ block
    public function _getDataResponseByBlock($page = 1, $filter = [], $languages = [])
    {
        if(empty($languages)) {
            return [
                CODE => ERROR,
                MESSAGE => __d('admin', 'khong_co_ngon_ngu_nao_duoc_chon')
            ];
        }

        $table = TableRegistry::get('TemplatesBlock');
        $utilities = TableRegistry::get('Utilities');
        $keyword = !empty($filter['keyword']) ? $filter['keyword'] : null;
        
        // Lấy tất cả blocks có dữ liệu locale
        $blocks = $table->find()->select([
            'TemplatesBlock.id',
            'TemplatesBlock.name',
            'TemplatesBlock.normal_data_extend', 
            'TemplatesBlock.code',
            'TemplatesBlock.status'
        ])->where([
            'TemplatesBlock.deleted' => 0,
            'TemplatesBlock.normal_data_extend !=' => '',
            'TemplatesBlock.normal_data_extend IS NOT' => null,
            'TemplatesBlock.normal_data_extend NOT LIKE' => '%"locale":{}%',
            'TemplatesBlock.normal_data_extend NOT LIKE' => '%"locale":{"":""}%'
        ])->limit(1000)->toArray();

        
        $all_translations = [];
        if (!empty($blocks)) {
            foreach ($blocks as $block) {
                $code = !empty($block['code']) ? $block['code'] : null;
                $normal_data_extend = !empty($block['normal_data_extend']) ? $block['normal_data_extend'] : '';
                
                if (empty($normal_data_extend) || !$utilities->isJson($normal_data_extend)) {
                    continue;
                }
                
                $data_extend = json_decode($normal_data_extend, true);
                if (empty($data_extend) || !isset($data_extend['locale'])) {
                    continue;
                }
                $translations = $this->_processDataExtendForBlock($data_extend, $languages);
                if (!is_array($translations)) {
                    continue;
                }
                
                foreach ($translations as $key => $translation) {
                    $data_record = [];
                    foreach ($languages as $lang => $language) {
                        $data_record[$lang] = [
                            'id' => $key,
                            'label' => !empty($translation[$lang]) ? $translation[$lang] : '',
                            'type' => 'block',
                            'edit_url' => ADMIN_PATH . '/template/block/update/' . $code,
                            'code' => $code
                        ];
                    }
                    $all_translations[] = $data_record;
                }
            }
        }
        // Lọc danh sách theo từ khóa
        if (!empty($keyword)) {
            $keyword = strtolower($keyword);
            $all_translations = array_filter($all_translations, function ($translations, $key) use ($keyword) {
                // Kiểm tra từ khóa trong key
                if (strpos(strtolower($key), $keyword) !== false) {
                    return true;
                }
                
                // Kiểm tra từ khóa trong các label của các ngôn ngữ
                foreach ($translations as $lang => $data) {
                    if (isset($data['label']) && strpos(strtolower($data['label']), $keyword) !== false) {
                        return true;
                    }
                }
                
                return false;
            }, ARRAY_FILTER_USE_BOTH);
        }

        // Phân trang
        $total = count($all_translations);
        $data_result = $this->_paginateArray($all_translations, $page, $this->limit);
        
        return [
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $data_result,
            PAGINATION => [
                'page' => $page,
                'pages' => ceil($total / $this->limit),
                'perpage' => $this->limit,
                'current' => count($data_result),
                'total' => $total
            ]
        ];
    }
    
    // xử lý dữ liệu data extends của block
    private function _processDataExtendForBlock($data = [], $languages = []) 
    {        
        if (empty($data['locale']) || !is_array($data['locale'])) return [];

        $result = [];
        foreach ($data['locale'] as $lang => $item) {
            if (!is_array($item)) continue;
            foreach ($item as $key => $value) {
                if (empty($key)) continue;
                if (!isset($result[$key])) {
                    $result[$key] = [];
                    foreach ($languages as $lang_code => $lang_name) {
                        $result[$key][$lang_code] = '';
                    }
                }
                $result[$key][$lang] = $value; 
            }
        }

        return $result;
    }

    // ngôn ngữ collection
    public function _getDataResponseByExtendCollection($page = 1, $filter = [], $languages = [])
    {
        if(empty($languages)) {
            return [
                CODE => ERROR,
                MESSAGE => __d('admin', 'khong_co_ngon_ngu_nao_duoc_chon')
            ];
        }

        $table_extend = TableRegistry::get('Extends');
        $table_collection = TableRegistry::get('ExtendsCollection');

        $keyword = !empty($filter['keyword']) ? $filter['keyword'] : null;
        
        $collections = $table_collection->find()->limit(1000)->toArray();

        $data_fields = [];
        if (!empty($collections)) {
            foreach ($collections as $k => $collection) {
                
                $id = !empty($collection['id']) ? intval($collection['id']) : null;
                $collection_code = !empty($collection['code']) ? $collection['code'] : null;
                $fields = !empty($collection['fields']) ? json_decode($collection['fields'], true) : [];
                if (!empty($fields)) {
                    foreach ($fields as $field) {
                        $view = !empty($field['view']) ? $field['view'] : null;
                        $input_type = !empty($field['input_type']) ? $field['input_type'] : null;
                        $multiple_language = !empty($field['multiple_language']) ? $field['multiple_language'] : null;
                        $code = !empty($field['code']) ? $field['code'] : null;

                        if ($view != '1' || $input_type != 'text' || $multiple_language != '1') {
                            continue;
                        }
                        
                        $data_fields[] = [
                            'view' => $view,
                            'input_type' => $input_type,
                            'multiple_language' => $multiple_language,
                            'code' => $code,
                            'collection_id' => $id,
                            'collection_code' => $collection_code
                        ];
                    }
                }
            }
        }
        $all_translations = [];
        if (!empty($data_fields)) {
            foreach ($data_fields as $k => $field) {
                $code = !empty($field['code']) ? $field['code'] : null;
                $collection_code = !empty($field['collection_code']) ? $field['collection_code'] : null;
                $collection_id = !empty($field['collection_id']) ? $field['collection_id'] : null;
                $input_type = !empty($field['input_type']) ? $field['input_type'] : null;

                $extend = $table_extend->find()->where([
                    'field' => $code,
                    'collection_id' => $collection_id
                ])->toArray();

                if(empty($extend)) continue;
                
                $record_translations = [];
                foreach ($extend as $item) {
                    $record_id = !empty($item['record_id']) ? intval($item['record_id']) : null;
                    if (empty($record_id)) continue;

                    $lang = !empty($item['lang']) ? $item['lang'] : null;
                    if (empty($lang)) continue;

                    if (!isset($record_translations[$record_id])) {
                        $record_translations[$record_id] = [];
                    }
                    $record_translations[$record_id][$lang] = $item['value'];
                }

                foreach ($record_translations as $record_id => $translations) {
                    $all_translations[$record_id] = [];
                    foreach ($languages as $lang => $lang_name) {
                        $all_translations[$record_id][$lang] = [
                            'label' => isset($translations[$lang]) ? $translations[$lang] : '',
                            'id' => $record_id,
                            'type' => 'extend_collection',
                            'code' => $code,
                            'edit_url' => ADMIN_PATH . '/extend-data/' . $collection_code . '/update/' . $record_id . '?lang=' . $lang
                        ];
                    }
                }
            }
        }
        
        // Lọc danh sách theo từ khóa
        if (!empty($filter['keyword'])) {
            $keyword = strtolower($filter['keyword']);
            
            $all_translations = array_filter($all_translations, function ($translations, $key) use ($keyword) {
                // Kiểm tra từ khóa trong key
                if (strpos(strtolower($key), $keyword) !== false) {
                    return true;
                }
                
                // Kiểm tra từ khóa trong các label của các ngôn ngữ
                foreach ($translations as $lang => $data) {
                    if (isset($data['label']) && strpos(strtolower($data['label']), $keyword) !== false) {
                        return true;
                    }
                }
                
                return false;
            }, ARRAY_FILTER_USE_BOTH);
        }

        // push data phân trang
        $total = !empty($all_translations) ? count($all_translations) : 0;
        $data_result = $this->_paginateArray($all_translations, $page, $this->limit);
        
        return [
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $data_result,
            PAGINATION => [
                'page' => $page,
                'pages' => !empty($total) ? ceil($total / $this->limit) : 0,
                'perpage' => $this->limit,
                'current' => count($data_result),
                'total' => $total
            ]
        ];
    }

    // lấy danh sách file po theo ngôn ngữ
    private function _getListLocaleFilesPo()
    {
        $template = TableRegistry::get('Templates')->getTemplateDefault();
        $template_code = !empty($template['code']) ? $template['code'] : null;

        if (empty($template_code)) return '';
        $path = SOURCE_DOMAIN . DS . 'templates' . DS . $template_code . DS . 'locales';
        
        if (!file_exists($path)) return '';

        return $path;
    }

    // phân trang của file po và js
    private function _paginateArray($array, $page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;  // Tính record bắt đầu 
        $counter = 0;
        $result = [];
        foreach ($array as $key => $value) {
            if ($counter >= $offset && count($result) < $limit) {
                $result[$key] = $value;
            }
            $counter++;
            if (count($result) >= $limit) {
                break;
            }
        }
        
        return $result;
    }
    // lấy data label locales của file po
    private function _getDataFromFilePo($dir_file = '')
    {
        if (empty($dir_file) || !file_exists($dir_file)) {
            return [];
        }

        $locales = file($dir_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $translations = [];
        $msgid = null;
        $msgstr = null;
        foreach ($locales as $key => $locale) {
            $locale = trim($locale);

            if (preg_match('/^msgid\s+"(.*)"/', $locale, $matches)) {
                $msgid = $matches[1];
            } elseif (preg_match('/^msgstr\s+"(.*)"/', $locale, $matches)) {
                $msgstr = $matches[1];
                if ($msgid !== null) {
                    $translations[$msgid] = [
                        'msgid' => $msgid,  
                        'msgstr' => $msgstr
                    ];
                }
            }
        }
        return $translations;
    }

    // lấy data label locales của file js
    private function _getDataFromFileJs($dir_file = '')
    {
        if (empty($dir_file) || !file_exists($dir_file)) {
            return [];
        }

        $content = file_get_contents($dir_file);
        if (empty($content)) {
            return [];
        }
        
        $translations = [];
        if (preg_match('/var\s+locales\s*=\s*{([\s\S]*?)}\s*;?/', $content, $matches)) {
            
            $localeContent = $matches[1];
            $lines = explode("\n", $localeContent);
            
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || $line === '}') continue;
                
                $line = rtrim($line, ',');
                
                if (preg_match('/^([a-zA-Z0-9_]+)\s*:\s*[\'"](.*)[\'"]$/', $line, $matches)) {
                    $key = $matches[1];
                    $value = $matches[2];
                    $translations[$key] = [
                        'msgid' => $key,  
                        'msgstr' => $value
                    ];
                }
            }
        }
        return $translations;
    }

    // cập nhật dữ liệu locales
    public function saveLocaleTranslation()
    {
        $this->autoRender = false;
        $this->viewBuilder()->enableAutoLayout(false);
        
        $postData  = $this->request->getData();

        $data = !empty($postData['data']) ? $postData['data'] : 'null';
        $type_view = !empty($postData['type_view']) ? $postData['type_view'] : 'null';

        if (!$this->getRequest()->is('post') || empty($data) || empty($type_view)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        switch ($type_view) {
            case 'system_po':
                $result = $this->_saveFilePo($data);
                break;
            case 'system_js':
                $result = $this->_saveFileJs($data);
                break;
            case 'block':
                $result = $this->_saveBlock($data);
                break;
            case 'article':
                $result = $this->_saveArticle($data);
                break;
            case 'brand':
                $result = $this->_saveBrand($data);
                break;
            case 'product':
                $result = $this->_saveProduct($data);
                break;
            case 'category_product':
                $result = $this->_saveCategory($data);
                break;
            case 'category_article':
                $result = $this->_saveCategory($data);
                break;
            case 'attribute':
                $result = $this->_saveAttribute($data);
                break;
            case 'extend_collection':
                $result = $this->_saveExtendCollection($data);
                break;
            default:
                break;
        }
    }

    // cập nhật dữ liệu mở rộng
    private function _saveExtendCollection($data)
    {
        if (empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        $utilities = TableRegistry::get('Utilities');
        $table = TableRegistry::get('Extends');
        $table_record = TableRegistry::get('ExtendsRecord');

        foreach ($data as $item) {
            $id = !empty($item['id']) ? intval($item['id']) : null;
            $type = !empty($item['type']) ? $item['type'] : null;
            $code = !empty($item['code']) ? $item['code'] : null;
            $translations = !empty($item['translations']) ? $item['translations'] : null;

            if (empty($id) || empty($type) || empty($translations)) {
                continue;
            }

            $extend_info = $table->find()->where([
                'record_id' => $id,
                'field' => $code
            ])->first();

            if(empty($extend_info)) {
                $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }

            $collection_id = !empty($extend_info['collection_id']) ? $extend_info['collection_id'] : null;
            $record_id = !empty($extend_info['record_id']) ? $extend_info['record_id'] : null;

            if(empty($code) || empty($collection_id) || empty($record_id)) {
                $errors[] = __d('admin', 'du_lieu_khong_hop_le');
                continue;
            }

            $record_info = $table_record->find()->where([
                'id' => $record_id,
                'collection_id' => $collection_id
            ])->first();

            if(empty($record_info)) {
                $errors[] = __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi');
                continue;
            }

            // Process each language translation
            foreach ($translations as $langCode => $value) {
                $extend = $table->find()->where([
                    'record_id' => $record_id,
                    'collection_id' => $collection_id,
                    'field' => $code,
                    'lang' => $langCode
                ])->first();

                $record_data = [
                    'search_unicode' => strtolower($utilities->formatSearchUnicode([$value])),
                    'Extends' => [
                        [
                            'id' => !empty($extend['id']) ? $extend['id'] : null,
                            'collection_id' => $collection_id,
                            'field' => $code,
                            'value' => $value,
                            'lang' => $langCode
                        ]
                    ]
                ];

                // // Update ExtendsRecord for this language
                $entity = $table_record->patchEntity($record_info, $record_data);
                $save = $table_record->save($entity);
                if (empty($save->id)) {
                    $errors[] = __d('admin', 'cap_nhat_khong_thanh_cong');
                    continue;
                }
            }
        }

        if (!empty($errors)) {
            $this->responseJson([
                CODE => ERROR,
                MESSAGE => !empty($errors[0]) ? $errors[0] : null,
                DATA => $errors
            ]);
        }
        $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);
    }

    // cập nhật thuộc tính mở rộng
    private function _saveAttribute($data)
    {
        if (empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $utilities = TableRegistry::get('Utilities');
        $table = TableRegistry::get('Attributes');

        foreach ($data as $item) {
            $id = !empty($item['id']) ? intval($item['id']) : null;
            $type = !empty($item['type']) ? $item['type'] : null;
            $translations = !empty($item['translations']) ? $item['translations'] : null;
            
            if (empty($id) || empty($type) || empty($translations)) {
                continue;
            }

            $attribute_info = $table->find()->where([
                'id' => $id,
                'deleted' => 0
            ])->first();

            if(empty($attribute_info)){
                $errors[] = __d('admin', 'khong_lay_duoc_thong_tin_thuoc_tinh');
                continue;
            }

            $update_data = [
                'code' => !empty($attribute_info['code']) ? $attribute_info['code'] : '',
                'attribute_type' => !empty($attribute_info['attribute_type']) ? $attribute_info['attribute_type'] : '',
                'input_type' => !empty($attribute_info['input_type']) ? $attribute_info['input_type'] : 'text',
                'has_image' => !empty($attribute_info['has_image']) ? $attribute_info['has_image'] : null,
                'required' => !empty($attribute_info['required']) ? intval($attribute_info['required']) : 0,
                'ContentMutiple' => []
            ];

            foreach ($translations as $langCode => $value) {
                $update_data['ContentMutiple'][] = [
                    'name' => $value,
                    'lang' => $langCode,
                    'search_unicode' =>  strtolower($utilities->formatSearchUnicode([$value]))
                ];
            }

            $entity = $table->patchEntity($attribute_info, $update_data);
            
            // xóa nội dung cũ của thuộc tính có id và lang cần cập nhật
            if(!empty($entity) && !empty($translations)){
                foreach($translations as $langCode => $value) {
                    TableRegistry::get('AttributesContent')->deleteAll([
                        'attribute_id' => $id,
                        'lang' => $langCode
                    ]);    
                }
            }
            $save = $table->save($entity);
            
            if (empty($save->id)) {
                $errors[] = __d('admin', 'cap_nhat_khong_thanh_cong');
                continue;
            }
        }

        if (!empty($errors)) {
            $this->responseJson([
                CODE => ERROR,
                MESSAGE => !empty($errors[0]) ? $errors[0] : null,
                DATA => $errors
            ]);
        }

        $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);
    }

    // cập nhật danh mục
    private function _saveCategory($data)
    {
        if (empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $lang_default = TableRegistry::get('Languages')->getDefaultLanguage();
        $utilities = TableRegistry::get('Utilities');
        $table = TableRegistry::get('Categories');

        $categories = $errors = [];    
        foreach ($data as $item) {
            $id = !empty($item['id']) ? intval($item['id']) : null;
            $type = !empty($item['type']) ? $item['type'] : null;
            $translations = !empty($item['translations']) ? $item['translations'] : null;
            if (empty($id) || empty($type) || empty($translations) || !is_array($translations)) continue;

            foreach ($translations as $langCode => $value) {
                $categories[] = [
                    'category_id' => $id,
                    'name' => $value,
                    'type' => $type,
                    'lang' => $langCode
                ];
            }
        }

        if (empty($categories)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_co_du_lieu_cap_nhat')]);
        }

        foreach ($categories as $category) {
            $id = !empty($category['category_id']) ? intval($category['category_id']) : null;
            $name = !empty($category['name']) ? $category['name'] : null;
            $lang = !empty($category['lang']) ? $category['lang'] : null;
            $type = !empty($category['type']) ? $category['type'] : null;
            
            // Lấy thông báo lỗi từ input tiếng Việt mặc định
            if(empty($name)){
                if ($lang != $lang_default) {
                    $category_content_info = TableRegistry::get('CategoriesContent')->find()->where([
                        'category_id' => $id,
                        'lang' => $lang_default
                    ])->first();
                }
                $name_default = !empty($category_content_info['name']) ? $category_content_info['name'] : 'danh mục';

                $errors[] = __d('admin', 'vui_long_nhap_ten_{0}', $name_default);
                continue;
            }

            $data = [
                'name' => $name
            ];

            if(!empty($id)){
                $category_info = TableRegistry::get('CategoriesContent')->find()->where([
                    'category_id' => $id,
                    'lang' => $lang
                ])->first();
            }
            if(empty($category_info)){
                $data['link'] = !empty($name) ? $utilities->formatToUrl(trim($name)) : null;
                $data['seo_title'] = $name;
            }

            $service = new CategoryService();
            $save = $service->updateCategory($id,  $data, $type, $lang); 
            
            if(empty($save[CODE]) || $save[CODE] == ERROR){  
                $errors[] = !empty($save[MESSAGE]) ? $save[MESSAGE] : __d('admin', 'cap_nhat_khong_thanh_cong');
                continue;
            }
        }

        if (!empty($errors)) {
            $this->responseJson([
                CODE => ERROR,
                MESSAGE => !empty($errors[0]) ? $errors[0] : null,
                DATA => $errors
            ]);
        }

        $this->responseJson([
            CODE => SUCCESS, 
            MESSAGE => __d('admin', 'cap_nhat_thanh_cong')
        ]);
    }

    // cập nhật bài viết
    private function _saveArticle($data)
    {
        if (empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        $utilities = TableRegistry::get('Utilities');
        $table = TableRegistry::get('Articles');
        $lang_default = TableRegistry::get('Languages')->getDefaultLanguage();

        $list_article = $errors = [];
        foreach ($data as $item) {
            $id = !empty($item['id']) ? intval($item['id']) : null;
            $type = !empty($item['type']) ? $item['type'] : null;
            $translations = !empty($item['translations']) ? $item['translations'] : null;

            if (empty($id) || empty($type) || empty($translations)) {
                continue;
            }

            foreach ($translations as $langCode => $value) {
                $list_article[] = [
                    'article_id' => $id,
                    'name' => $value,
                    'type' => $type,
                    'lang' => $langCode
                ];
            }
        }

        if (empty($list_article)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_co_du_lieu_cap_nhat')]);
        }

        foreach ($list_article as $article) {
            $id = !empty($article['article_id']) ? intval($article['article_id']) : null;
            $lang = !empty($article['lang']) ? $article['lang'] : null;
            $name = !empty($article['name']) ? $article['name'] : null;

            // Lấy thông báo lỗi từ input tiếng Việt mặc định
            if(empty($name)){
                if ($lang != $lang_default) {
                    $article_content_info = TableRegistry::get('ArticlesContent')->find()->where([
                        'article_id' => $id,
                        'lang' => $lang_default
                    ])->first();
                }
                $name_default = !empty($article_content_info['name']) ? $article_content_info['name'] : 'bai_viet';

                $errors[] = __d('admin', 'vui_long_nhap_ten_{0}', $name_default);
                continue;
            }

            $data = [
                'name' => $name
            ];

            if(!empty($id)){
                $article_info = $table->getDetailArticle($id, $lang);    
                if(empty($article_info)){
                    $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
                }
            }
            if(empty($article_info)){
                $data['link'] = !empty($name) ? $utilities->formatToUrl(trim($name)) : null;
                $data['seo_title'] = $name;
            }   

            $article_service = new ArticleService();
            $save = $article_service->updateArticle($id, $data, $lang); 

            if(empty($save[CODE]) || $save[CODE] == ERROR ){
                $errors[] = !empty($save[MESSAGE]) ? $save[MESSAGE] : __d('admin', 'cap_nhat_khong_thanh_cong');
                continue;
            }
        }

        if (!empty($errors)) {
            $this->responseJson([
                CODE => ERROR,
                MESSAGE => !empty($errors[0]) ? $errors[0] : null,
                DATA => $errors
            ]);
        }

        $this->responseJson([
            CODE => SUCCESS, 
            MESSAGE => __d('admin', 'cap_nhat_thanh_cong')
        ]);
    }

    // cập nhật thương hiệu
    private function _saveBrand($data)
    {
        if (empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        $utilities = TableRegistry::get('Utilities');
        $table = TableRegistry::get('Brands');
        $lang_default = TableRegistry::get('Languages')->getDefaultLanguage();

        $list_brand = $errors = [];
        foreach ($data as $item) {
            $id = !empty($item['id']) ? intval($item['id']) : null;
            $type = !empty($item['type']) ? $item['type'] : null;
            $translations = !empty($item['translations']) ? $item['translations'] : null;

            if (empty($id) || empty($type) || empty($translations)) {
                continue;
            }

            foreach ($translations as $langCode => $value) {
                $list_brand[] = [
                    'brand_id' => $id,
                    'name' => $value,
                    'type' => $type,
                    'lang' => $langCode
                ];
            }
        }
        if (empty($list_brand)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_co_du_lieu_cap_nhat')]);
        }
        foreach ($list_brand as $brand) {
            $id = !empty($brand['brand_id']) ? intval($brand['brand_id']) : null;
            $lang = !empty($brand['lang']) ? $brand['lang'] : null;
            $name = !empty($brand['name']) ? $brand['name'] : null;
            
            if(empty($name)){
                if ($lang != $lang_default) {
                    $brand_content_info = TableRegistry::get('BrandsContent')->find()->where([
                        'brand_id' => $id,
                        'lang' => $lang_default
                    ])->first();
                }
                $name_default = !empty($brand_content_info['name']) ? $brand_content_info['name'] : 'thuong_hieu';

                $errors[] = __d('admin', 'vui_long_nhap_ten_{0}', $name_default) . " ({$lang})";
                continue;
            }

            $data = [
                'name' => $name
            ];

            if(!empty($id)){
                $brand_info = $table->getDetailBrand($id, $lang);
                if(empty($brand_info)){
                    $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
                }
            }
            if(empty($brand_info)){
                $data['link'] = !empty($name) ? $utilities->formatToUrl(trim($name)) : null;
                $data['seo_title'] = $name;
            }
            
            $brand_service = new BrandService();
            $save = $brand_service->updateBrand($id, $data, $lang); 
            
            if(empty($save[CODE]) || $save[CODE] == ERROR ){
                $errors[] = !empty($save[MESSAGE]) ? $save[MESSAGE] : __d('admin', 'cap_nhat_khong_thanh_cong');
                continue;
            }
        }

        if (!empty($errors)) {
            $this->responseJson([
                CODE => ERROR,
                MESSAGE => !empty($errors[0]) ? $errors[0] : null,
                DATA => $errors
            ]);
        }

        $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);
    }

    // cập nhật sản phẩm
    private function _saveProduct($data)
    {
        if (empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        $utilities = TableRegistry::get('Utilities');
        $table = TableRegistry::get('Products');
        $lang_default = TableRegistry::get('Languages')->getDefaultLanguage();

        $list_product = $errors = [];
        foreach ($data as $item) {
            $id = !empty($item['id']) ? intval($item['id']) : null;
            $type = !empty($item['type']) ? $item['type'] : null;
            $translations = !empty($item['translations']) ? $item['translations'] : null;

            if (empty($id) || empty($type) || empty($translations)) {
                continue;
            }

            foreach ($translations as $langCode => $value) {
                $list_product[] = [
                    'product_id' => $id,
                    'name' => $value,
                    'type' => $type,
                    'lang' => $langCode
                ];
            }
        }
        if (empty($list_product)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_co_du_lieu_cap_nhat')]);
        }
        foreach ($list_product as $product) {
            $id = !empty($product['product_id']) ? intval($product['product_id']) : null;
            $lang = !empty($product['lang']) ? $product['lang'] : null;
            $name = !empty($product['name']) ? $product['name'] : null;
            
            // Lấy thông báo lỗi từ input mặc định
            if(empty($name)){
                if ($lang != $lang_default) {
                    $product_content_info = TableRegistry::get('ProductsContent')->find()->where([
                        'product_id' => $id,
                        'lang' => $lang_default
                    ])->first();
                }
                $name_default = !empty($product_content_info['name']) ? $product_content_info['name'] : 'san_pham';

                $errors[] = __d('admin', 'vui_long_nhap_ten_{0}', $name_default) . " ({$lang})";
                continue;
            }

            $data = [
                'name' => $name
            ];

            if(!empty($id)){
                $product_info = $table->getDetailProduct($id, $lang);
                if(empty($product_info)){
                    $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
                }
            }
            if(empty($product_info)){
                $data['link'] = !empty($name) ? $utilities->formatToUrl(trim($name)) : null;
                $data['seo_title'] = $name;
            }
            
            $product_service = new ProductService();
            $save = $product_service->updateProduct($id, $data, $lang); 
            
            if(empty($save[CODE]) || $save[CODE] == ERROR){  
                $errors[] = !empty($save[MESSAGE]) ? $save[MESSAGE] : __d('admin', 'cap_nhat_khong_thanh_cong') . " ({$lang})";
                continue;
            }
        }

        if (!empty($errors)) {
            $this->responseJson([
                CODE => ERROR,
                MESSAGE => !empty($errors[0]) ? $errors[0] : null,
                DATA => $errors
            ]);
        }

        $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);
    }
    
    // cập nhật dữ liệu file po
    private function _saveFilePo($data)
    {
        $this->autoRender = false;
        $this->viewBuilder()->enableAutoLayout(false);
        
        $data = $this->request->getData('data');
        if (!$this->getRequest()->is('post') || empty($data)) {
            return $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $path = $this->_getListLocaleFilesPo();
        if (empty($path)) {
            return $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_file_locale')]);
        }

        $errors = [];
        $file_updates = []; // Lưu trữ các cập nhật theo file

        // Nhóm các cập nhật theo file để tránh đọc/ghi file nhiều lần
        foreach ($data as $item) {
            $id = $item['id'] ?? null;
            $type = $item['type'] ?? null;
            $translations = $item['translations'] ?? null;

            if (empty($id) || empty($type) || empty($translations)) {
                continue;
            }

            foreach ($translations as $langCode => $value) {
                $lang_path = "{$path}/{$langCode}";
                $file_path = "{$lang_path}/template.po";
                
                // Tạo thư mục ngôn ngữ nếu chưa tồn tại
                if (!is_dir($lang_path)) {
                    if (!mkdir($lang_path, 0755, true)) {
                        $errors[] = __d('admin', 'khong_the_tao_thu_muc_ngon_ngu') . " ({$langCode})";
                        continue;
                    }
                }

                if (!isset($file_updates[$file_path])) {
                    $file_updates[$file_path] = [];
                }
                
                $file_updates[$file_path][$id] = [
                    'msgid' => $id,
                    'msgstr' => $value
                ];
            }
        }

        // Xử lý từng file một lần
        foreach ($file_updates as $file_path => $translations) {
            // Đọc dữ liệu hiện tại từ file (nếu tồn tại)
            $current_translations = [];
            if (file_exists($file_path)) {
                $current_translations = $this->_getDataFromFilePo($file_path);
            }
            
            // Cập nhật chỉ những bản dịch cần thiết
            foreach ($translations as $id => $translation) {
                $current_translations[$id] = $translation;
            }
            
            // Lưu file chỉ một lần với tất cả cập nhật
            if (!$this->_saveDataToFilePo($file_path, $current_translations)) {
                $errors[] = __d('admin', 'cap_nhat_khong_thanh_thanh_cong');
            }
        }
        
        if (!empty($errors)) {
            return $this->responseJson([
                CODE => ERROR,
                MESSAGE => !empty($errors[0]) ? $errors[0] : null,
                DATA => $errors
            ]);
        }

        return $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);
    }
    
   
    // lưu dữ liệu vào file po
    private function _saveDataToFilePo($file_path, $new_translations)
    {
        if (empty($new_translations) || !is_array($new_translations)) {
            return true;
        }
        
        // Tạo nội dung mới
        $new_content = '';
        foreach ($new_translations as $item) {
            if (!empty($item['msgid']) && isset($item['msgstr'])) {
                
                $msgid =  str_replace('\\', '\\\\', $item['msgid']);
                $msgstr = str_replace('\\', '\\\\', $item['msgstr']);
                
                $new_content .= 'msgid "' . $msgid . '"' . "\n";
                $new_content .= 'msgstr "' . $msgstr . '"' . "\n\n";
            }
        }

        // Kiểm tra xem nội dung có thay đổi không
        $old_content = @file_get_contents($file_path);
        if ($old_content === $new_content) {
            return true; // Không có thay đổi, không cần lưu
        }
        
        // Ghi log thay đổi file
        TableRegistry::get('Logs')->writeLogChangeFile('update', $file_path);

        // Lưu file
        $file = new File($file_path, true);
        return $file->write($new_content);
    }

    // cập nhật dữ liệu block
    private function _saveBlock($data)
    {
        if (empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('TemplatesBlock');
        $utilities = TableRegistry::get('Utilities');

        $errors = [];
        foreach ($data as $item) {
            $id = !empty($item['id']) ? $item['id'] : null;
            $type = !empty($item['type']) ? $item['type'] : null;
            $code = !empty($item['code']) ? $item['code'] : null;
            $translations = !empty($item['translations']) ? $item['translations'] : null;
            
            if (empty($id) || empty($type) || empty($translations) || empty($code)) {
                continue;
            }

            $block_info = $table->find()->where([
                'template_code' => CODE_TEMPLATE,
                'code' => $code,
                'deleted' => 0
            ])->select(['id', 'name', 'code', 'template_code', 'normal_data_extend'])->first();

            if(empty($block_info)){
                $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_block')]);
            }

            $normal_data_extend = !empty($block_info['normal_data_extend']) && $utilities->isJson($block_info['normal_data_extend']) ? json_decode($block_info['normal_data_extend'], true) : [];

            if (!isset($normal_data_extend['locale'])) {
                $normal_data_extend['locale'] = [];
            }

            foreach ($translations as $langCode => $value) {
                if (!isset($normal_data_extend['locale'][$langCode])) {
                    $normal_data_extend['locale'][$langCode] = [];
                }
                
                // Store translation as direct string value
                $normal_data_extend['locale'][$langCode][$id] = $value;
            }

            $normal_data_extend = json_encode($normal_data_extend);

            $entity = $table->patchEntity($block_info, [
                'normal_data_extend' => $normal_data_extend
            ]);
            
            $save = $table->save($entity);

            if (empty($save->id)){
                $errors[] = __d('admin', 'cap_nhat_khong_thanh_cong');
                continue;
            }
        }

        if (!empty($errors)) {
            $this->responseJson([
                CODE => ERROR,
                MESSAGE => !empty($errors[0]) ? $errors[0] : null,
                DATA => $errors
            ]);
        }
        $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);
    }

    // cập nhật dữ liệu file js
    private function _saveFileJs($data)
    {
        $this->autoRender = false;
        $this->viewBuilder()->enableAutoLayout(false);
        
        $data = $this->request->getData('data');

        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $path = $this->_getListLocaleFilesJs();
        if (empty($path)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_file_locale')]);
        }

        $lang_translations = $errors = [];
        foreach ($data as $item) {
            $id = !empty($item['id']) ? $item['id'] : null;
            $translations = !empty($item['translations']) ? $item['translations'] : null;
            
            if (empty($id) || empty($translations)) {
                continue;
            }

            foreach ($translations as $langCode => $value) {
                if (!isset($lang_translations[$langCode])) {
                    $lang_translations[$langCode] = [];
                }
                $lang_translations[$langCode][$id] = $value;
            }
        }

        foreach ($lang_translations as $langCode => $translations) {
            $file_path = "{$path}{$langCode}.js";
            
            // Đọc dữ liệu hiện tại từ file (nếu tồn tại)
            $existing_translations = [];
            if (file_exists($file_path)) {
                $file_content = file_get_contents($file_path);
                if (!empty($file_content)) {
                    if (preg_match('/var\s+locales\s*=\s*{([\s\S]*?)}\s*;?/', $file_content, $matches)) {
                        $localeContent = $matches[1];
                        $lines = explode("\n", $localeContent);
                        
                        foreach ($lines as $line) {
                            $line = trim($line);
                            if (empty($line) || $line === '}') continue;
                            
                            $line = rtrim($line, ',');
                            
                            if (preg_match('/^([a-zA-Z0-9_]+)\s*:\s*[\'"](.*)[\'"]$/', $line, $matches)) {
                                $key = $matches[1];
                                $val = $matches[2];
                                $existing_translations[$key] = $val;
                            }
                        }
                    }
                }
            }
            
            // Merge với dữ liệu mới
            $existing_translations = array_merge($existing_translations, $translations);

            // Tạo nội dung file mới
            $new_content = "var locales = {\n";
            foreach ($existing_translations as $key => $val) {
                $new_content .= "\t{$key}: '{$val}',\n";
            }
            $new_content = rtrim($new_content, ",\n") . "\n};\n";

            // Ghi log thay đổi file
            TableRegistry::get('Logs')->writeLogChangeFile('update', $file_path);
            
            $file = new File($file_path, true);
            $save = $file->write($new_content);
            if (empty($save)) {
                $errors[] = __d('admin', 'cap_nhat_khong_thanh_thanh_cong');
                continue;
            }
        }

        if (!empty($errors)) {
            $this->responseJson([
                CODE => ERROR,
                MESSAGE => !empty($errors[0]) ? $errors[0] : null,
                DATA => $errors
            ]);
        }
        $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);
    }

    public function translateLabel()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $label = !empty($data['label']) ? $data['label'] : null;
        $target_lang = !empty($data['target_lang']) ? $data['target_lang'] : null;

        if (empty($label) || empty($target_lang) || !$this->getRequest()->is('post')) {
            $this->responseJson([CODE => ERROR, MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
            return;
        }

        $lang_default = TableRegistry::get('Languages')->getDefaultLanguage();
        
        if ($target_lang === $lang_default) {
            $this->responseJson([CODE => ERROR, MESSAGE => __d('admin', 'ban_khong_the_dich_sang_ngon_ngu_mac_dinh')]);
            return;
        }
        
        $translate_service = new GoogleTranslateService();
        $translated = $translate_service->translate([$label], $lang_default, $target_lang);

        if (!empty($translated) && isset($translated[0])) {
            $result = [$target_lang => $translated[0]];
            $this->responseJson([CODE => SUCCESS, DATA => $result]);
        } else {
            $this->responseJson([CODE => ERROR, MESSAGE => __d('admin', 'khong_the_dich_duoc')]);
        }
    }

    public function translateMultipleLabels()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $labels = !empty($data['labels']) ? $data['labels'] : [];
        $lang = !empty($data['lang']) ? $data['lang'] : null;

        if (empty($labels) || !is_array($labels) || empty($lang) || !$this->getRequest()->is('post')) {
            $this->responseJson([CODE => ERROR, MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
            return;
        }

        $lang_default = TableRegistry::get('Languages')->getDefaultLanguage();

        if(empty($lang_default)) {
            $this->responseJson([CODE => ERROR, MESSAGE => __d('admin', 'khong_tim_thay_ngon_ngu_mac_dinh')]);
            return;
        }

        // Dịch tất cả labels sang ngôn ngữ đích
        $result = [];
        foreach($labels as $index => $label) {
            $translate_service = new GoogleTranslateService();
            $translated = $translate_service->translate([$label], $lang_default, $lang);
            $result[] = !empty($translated) ? $translated : $label;
        }

        $this->responseJson([CODE => SUCCESS, DATA => $result]);
    }
}
