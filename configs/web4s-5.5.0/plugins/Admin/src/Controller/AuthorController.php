<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Event\EventInterface;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;

class AuthorController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
    }

    public function list() 
    {
        
        $this->css_page = [
            '/assets/plugins/global/lightbox/lightbox.css',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];
        $this->js_page = [            
            '/assets/plugins/global/lightbox/lightbox.min.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/js/pages/list_author.js'

        ];
        $this->set('path_menu', 'author');
        $this->set('title_for_layout', __d('admin', 'danh_sach_tac_gia'));
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Authors');
        $utilities = $this->loadComponent('Utilities');

        $data = $params = $authors = [];
        $limit = PAGINATION_LIMIT_ADMIN;
        $page = 1;
        
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        // params query
        $params[QUERY] = !empty($data[QUERY]) ? $data[QUERY] : [];

        // params filter
        $params[FILTER] = !empty($data[DATA_FILTER]) ? $data[DATA_FILTER] : [];

        if(!empty($params[QUERY])){
            $params[FILTER] = array_merge($params[FILTER], $params[QUERY]);
        }

        $params[FILTER][LANG] = !empty($params[FILTER][LANG]) ? $params[FILTER][LANG] : $this->lang;

        // params sort         
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];
        $params['get_empty_name'] = true;

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;

        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;
        
        try {
            $authors = $this->paginate($table->queryListAuthors($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();

        } catch (Exception $e) {
            $page = 1;
            $authors = $this->paginate($table->queryListAuthors($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }
        
        $result = [];
        if(!empty($authors)){
            $languages = TableRegistry::get('Languages')->getList();
            foreach($authors as $k => $author){
                $result[$k] = $table->formatDataAuthorDetail($author, $this->lang);
                
                // check multiple language
                $mutiple_language = [];
                if(!empty($languages)){
                    foreach($languages as $lang => $language){
                        if($lang == $this->lang){
                            $mutiple_language[$lang] = false;
                            $content = TableRegistry::get('AuthorsContent')->find()->where([
                                'author_id' => !empty($author['id']) ? intval($author['id']) : null,
                                'lang' => $lang
                            ])->select(['job_title', 'description', 'content'])->first();
                            
                
                            if(!empty($content['job_title']) || !empty($content['description']) || !empty($content['content'])){
                                $mutiple_language[$lang] = true;
                            } 
                        }                        
                    }
                }

                $result[$k]['mutiple_language'] = $mutiple_language;

            }
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Authors']) ? $this->request->getAttribute('paging')['Authors'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
            META => $meta_info
        ]);
    }
    
    public function add()
    {
        $this->css_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/js/pages/author.js',
            '/assets/js/seo_analysis.js',            
        ];

        $max_record = TableRegistry::get('Authors')->find()->select('id')->max('id');

        $this->set('position', !empty($max_record->id) ? $max_record->id + 1 : 1);
        $this->set('title_for_layout', __d('admin', 'them_tac_gia'));
        $this->set('path_menu', 'author_add');
        $this->render('update');
    }

    public function update($id = null)
    {
        if(empty($id)) $this->showErrorPage();
        
        $table = TableRegistry::get('Authors');
        $author = $table->getDetailAuthor($id, $this->lang);
        $author_info = $table->formatDataAuthorDetail($author, $this->lang);

        if(empty($author_info)) $this->showErrorPage();

        $config_social = !empty($author['social']) ? $author['social'] : null;

        $this->set('author', $author_info);
        $this->set('id', $id);
        $this->set('position', !empty($author['position']) ? $author['position'] : 1);
        
        $this->css_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/js/pages/author.js',
            '/assets/js/seo_analysis.js',
            '/assets/plugins/diff-match-patch/diff-match-patch.js',
            '/assets/js/log_record.js'
        ];

        $this->set('title_for_layout', __d('admin', 'cap_nhat_tac_gia'));
    }

    public function save($id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
       
        if (empty($data) || !$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        } 
        
        $utilities = $this->loadComponent('Utilities');
        $table = TableRegistry::get('Authors');

        $author_info = [];
        if(!empty($id)){
            $author_info = $table->getDetailAuthor($id, $this->lang);
            if(empty($author_info)){
                $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }
        }

        $full_name = !empty($data['full_name']) ? trim(strip_tags($data['full_name'])) : null;
        $email = !empty($data['email']) ? trim($data['email']) : null;
        $phone = !empty($data['phone']) ? trim($data['phone']) : null;

        $job_title = !empty($data['job_title']) ? trim(strip_tags($data['job_title'])) : null;
        $avatar = !empty($data['avatar']) ? $data['avatar'] : null;
        $description = !empty($data['description']) ? $data['description'] : null;
        $content = !empty($data['content']) ? $data['content'] : null;
        $images = !empty($data['images']) ? $data['images'] : null;
        

        $url_video = !empty($data['url_video']) ? $data['url_video'] : null;
        $type_video = null;
        if(!empty($url_video)){
            $type_video = !empty($data['type_video']) ? $data['type_video'] : null;
        }

        $seo_title = !empty($data['seo_title']) ? trim(strip_tags($data['seo_title'])) : null;
        $seo_description = !empty($data['seo_description']) ? trim(strip_tags($data['seo_description'])) : null;

        $list_keyword = !empty($data['seo_keyword']) ? array_column(json_decode($data['seo_keyword'], true), 'value') : null;
        $seo_keyword = !empty($list_keyword) ? implode(', ', $list_keyword) : null;
        
        $social = !empty($data['social']) ? $data['social'] : [];

        $social['others'] = [];

        foreach ($social['others_name'] as $index => $name_social) {
            $url_social = isset($social['others_url'][$index]) ? $social['others_url'][$index] : '';

            if (empty($name_social) && empty($url_social)) continue;
            $social['others'][] = [
                'name' => $name_social,
                'url' => $url_social,
            ];
        }

        if(!empty($social['others_name'])) unset($social['others_name']);
        if(!empty($social['others_url'])) unset($social['others_url']);

        $unique_id = TableRegistry::get('Utilities')->generateRandomString(4);

        // validate data
        if(empty($full_name)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_thong_tin_ten_tac_gia')]);
        }

        $link = !empty($data['link']) ? $utilities->formatToUrl($data['link']) : '';
        if(empty($id) || empty($link)){
            $link = $this->_getUniqueLink($full_name);
        }        

        $link_id = !empty($author_info['Links']['id']) ? $author_info['Links']['id'] : null;
        if(TableRegistry::get('Links')->checkExist($link, $link_id)){
            $this->responseJson([MESSAGE => __d('admin', 'duong_dan_da_ton_tai_tren_he_thong')]);
        }

        $data_save = [
            'full_name' => $full_name,            
            'phone' => $phone,            
            'email' => $email,
            'address' => !empty($data['address']) ? $data['address'] : null,
            'images' => !empty($data['images']) ? $data['images'] : null,
            'avatar' => $avatar,
            'url_video' => $url_video,
            'type_video' => $type_video,
            'position' => !empty($data['position']) ? intval($data['position']) : 1,
            'social' => json_encode($social, true),
            'search_unicode' => strtolower($utilities->formatSearchUnicode([$full_name, $phone, $email]))
        ];

        $data_content = [
            'job_title' => $job_title,
            'content' => $content,
            'description' => $description,
            'seo_title' => $seo_title,
            'seo_description' => $seo_description,
            'seo_keyword' => $seo_keyword,
            'lang' => $this->lang,
            'search_unicode' => strtolower($utilities->formatSearchUnicode([$job_title]))
        ];

        $data_link = [
            'id' => $link_id,
            'type' => AUTHOR_DETAIL,
            'url' => $link,
            'lang' => $this->lang,
        ];

        // translate
        $languages = TableRegistry::get('Languages')->getList();

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $setting_language = !empty($settings['language']) ? $settings['language'] : [];
        
        if(empty($id) && !empty($setting_language['auto_translate']) && count($languages) > 1){
            $data_save['ContentMutiple'][] = $data_content;
            $data_save['LinksMutiple'][] = $data_link;

            $translate_component = $this->loadComponent('Admin.Translate');
            foreach($languages as $language_code => $language){
                if($language_code == $this->lang) continue;
         
                // translate title and content
                $items = [];
                
                if (!empty($content) && strlen($content) <= 5000 && !empty($setting_language['translate_all'])) {
                    $items['content'] = $content;
                    $items['description'] = $description;
                    $items['job_title'] = $job_title;
                }
                
                if(empty($items)) continue;
                $translates = !empty($items) ? $translate_component->translate($items, $this->lang, $language_code) : [];
                
                $content_translate = !empty($translates['content']) ? $translates['content'] : null;
                $description_translate = !empty($translates['description']) ? $translates['description'] : null;
                $job_title_translate = !empty($translates['job_title']) ? $translates['job_title'] : null;
                
                // link translate
                $link_translate = $this->_getUniqueLink($full_name, $language_code);
                if(empty($link_translate)) continue;

                // set value after translate
                if(!empty($setting_language['translate_all'])){
                    $record_translate = [
                        'content' => $content_translate,
                        'description' => $description_translate,
                        'job_title' => $job_title_translate,
                        'lang' => $language_code
                    ];
                }

                // set data_save
                $data_save['ContentMutiple'][] = $record_translate;
                $data_save['LinksMutiple'][] = [
                    'type' => AUTHOR_DETAIL,
                    'url' => $link_translate,
                    'lang' => $language_code,
                ];
            }

            $associated = ['ContentMutiple', 'LinksMutiple'];
            
        }else{
            $associated = ['AuthorsContent', 'Links'];

            $data_save['AuthorsContent'] = $data_content;
            $data_save['Links'] = $data_link;
        }

        // merge data with entity 
        if(empty($id)){
            $entity = $table->newEntity($data_save, [
                'associated' => $associated
            ]);
        }else{            
            $entity = $table->patchEntity($author_info, $data_save);
        }  

        $conn = ConnectionManager::get('default');
        try {
            $conn->begin();

            $save = $table->save($entity);    
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        } catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    private function _getUniqueLink($full_name = '', $lang = '')
    {
        if(empty($full_name)) $full_name = TableRegistry::get('Utilities')->generateRandomString(6);
        if(empty($lang)) $lang = $this->lang;

        $random_code = TableRegistry::get('Utilities')->generateRandomString(4);
        $prefix_link = 'author-' . strtolower($random_code . $lang);
        $link = $prefix_link . '-' . $this->loadComponent('Utilities')->formatToUrl($full_name);

        $link = TableRegistry::get('Links')->getUrlUnique($link);

        return $link;
    }

    public function delete()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];
        
        if (!$this->getRequest()->is('post') || empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Authors');

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach($ids as $id){

                // delete
                $author = $table->find()->where([
                    'Authors.id' => $id,
                    'Authors.deleted' => 0
                ])->select(['Authors.id', 'Authors.deleted'])->first();

                if (empty($author)) {
                    throw new Exception(__d('admin', 'khong_tim_thay_thong_tin_tac_gia'));
                }

                $entity = $table->patchEntity($author, ['id' => $id, 'deleted' => 1], ['validate' => false]);
                $delete = $table->save($entity);
                if (empty($delete)){
                    throw new Exception();
                }
            }
            
            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'xoa_du_lieu_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function changePosition()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $id = !empty($data['id']) ? intval($data['id']) : null;
        $value = !empty($data['value']) ? $data['value'] : 0;

        if(!$this->getRequest()->is('post') || empty($id)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Authors');
        $author = $table->find()->where([
                    'Authors.id' => $id,
                    'Authors.deleted' => 0
                ])->select(['Authors.id', 'Authors.position'])->first();

        if(empty($author)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        $author = $table->patchEntity($author, ['position' => $value], ['validate' => false]);

        try{
            $save = $table->save($author);

            if (empty($save->id)){
                throw new Exception();
            }
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    public function changeStatus()
    {
        $this->layout = false;
        $this->autoRender = false;
 
        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];
        $status = !empty($data['status']) ? 1 : 0;

        if (!$this->getRequest()->is('post') || empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Authors');

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach($ids as $id){
                $author = $table->find()->where([
                    'Authors.id' => $id,
                    'Authors.deleted' => 0
                ])->select(['Authors.id', 'Authors.status'])->first();

                if (empty($author)) {
                    throw new Exception(__d('admin', 'khong_tim_thay_thong_tin_tac_gia'));
                }
                
                $authors = $table->patchEntity($author, ['id' => $id, 'status' => $status]);
                $save = $table->save($author);
                if (empty($save->id)){
                    throw new Exception();
                }
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();

            $message = !empty($e->getMessage()) ? $e->getMessage() : __d('admin', 'cap_nhat_khong_thanh_cong');
            $this->responseJson([MESSAGE => $message]);  
        }
    }

    public function autoSuggest()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Authors');
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $filter = !empty($data[FILTER]) ? $data[FILTER] : [];
        
        $authors = $table->queryListAuthors([
            FILTER => $filter,
            FIELD => LIST_INFO
        ])->limit(10)->toArray();        
  
        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $authors, 
        ]);
    }

    public function rollbackLog()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $record_id = !empty($data['record_id']) ? intval($data['record_id']) : null;
        $version = !empty($data['version']) ? $data['version'] : null;
        if (!$this->getRequest()->is('post') || empty($record_id) || empty($version)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $log_record = TableRegistry::get('LogsUtilities')->getLogRecordByVersion(AUTHOR, $record_id, $version);
        $data_log = !empty($log_record['before_entity']) ? $log_record['before_entity'] : [];
        if(empty($data_log)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        $table = TableRegistry::get('Authors');

        $author_info = $table->find()->contain([
            'ContentMutiple',
            'LinksMutiple'
        ])->where([
            'Authors.id' => $record_id,
            'Authors.deleted' => 0
        ])->first();

        if(empty($author_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        $entity = $table->patchEntity($author_info, $data_log);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
                 
            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }

    }

}