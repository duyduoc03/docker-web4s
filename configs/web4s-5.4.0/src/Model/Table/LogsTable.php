<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Utility\Hash;
use Cake\Utility\Text;

class LogsTable extends Table
{
    private $lang = null;
    private $number_log_file = 20;
    private $dir_log = SOURCE_DOMAIN . DS . 'system_logs';

    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('logs');
        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new'
                ]
            ]
        ]);

        $this->belongsTo('User', [
            'className' => 'Users',
            'foreignKey' => 'user_id',
            'propertyName' => 'user'
        ]);

        $this->lang = TableRegistry::get('Languages')->getDefaultLanguage();
    }

    public function queryListLogs($params = [])
    {
        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;
        $get_user = !empty($params['get_user']) ? $params['get_user'] : false;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];

        $action = !empty($filter['action']) ? $filter['action'] : null;
        $type = !empty($filter['type']) ? $filter['type'] : null;
        $sub_type = !empty($filter['sub_type']) ? $filter['sub_type'] : null;
        $record_id = !empty($filter['record_id']) ? intval($filter['record_id']) : null;
        $user_id = !empty($filter['user_id']) ? intval($filter['user_id']) : null;
        $create_from = !empty($filter['create_from']) ? strtotime(date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $filter['create_from'])))) : null;
        $create_to = !empty($filter['create_to']) ? strtotime(date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $filter['create_to'])))) : null;

        // fields select
        switch($field){
            case FULL_INFO:
                $fields = ['Logs.id', 'Logs.action', 'Logs.type', 'Logs.sub_type', 'Logs.record_id', 'Logs.user_id', 'Logs.user_name', 'Logs.description', 'Logs.link', 'Logs.path_file', 'Logs.path_log', 'Logs.version', 'Logs.created'];
            break;

            case LIST_INFO:
                $fields = ['Logs.id', 'Logs.description'];
            break;

            case SIMPLE_INFO:
            default:
                $fields = ['Logs.id', 'Logs.action', 'Logs.type', 'Logs.sub_type', 'Logs.record_id', 'Logs.user_id', 'Logs.user_name', 'Logs.description', 'Logs.version', 'Logs.created'];
            break;
        }

        $where = $contain = [];
        // filter by conditions  

        if(!empty($action)){
            $where['Logs.action'] = $action;
        }

        if(!empty($type)){
            $where['Logs.type'] = $type;
        }

        if(!empty($sub_type)){
            $where['Logs.sub_type'] = $sub_type;
        }

        if(!empty($record_id)){
            $where['Logs.record_id'] = $record_id;
        }

        if(!empty($user_id)){
            $where['Logs.user_id'] = $user_id;
        }

        if(!empty($create_from)){
            $where['Logs.created >='] = $create_from;
        }

        if(!empty($create_to)){
            $where['Logs.created <='] = $create_to;
        }

        if(!empty($get_user)){
            $fields[] = 'User.id';
            $fields[] = 'User.full_name';

            $contain[] = 'User';
        }


        // sort by
        $sort_string = 'Logs.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'Logs.id '. $sort_type;
                break;

                case 'action':
                    $sort_string = 'Logs.action '. $sort_type .', Logs.id DESC';
                break;

                case 'type':
                    $sort_string = 'Logs.type '. $sort_type .', Logs.id DESC';
                break;

                case 'sub_type':
                    $sort_string = 'Logs.sub_type '. $sort_type .', Logs.id DESC';
                break;

                case 'user_id':
                    $sort_string = 'Logs.user_id '. $sort_type .', Logs.id DESC';
                break;

                case 'created':
                    $sort_string = 'Logs.created '. $sort_type .', Logs.id DESC';
                break;         
            }
        }

        $query = $this->find()->contain($contain)->where($where)->select($fields)->order($sort_string);

        return $query;
    }

    public function formatDataLogDetail($data = [])
    {
        if(empty($data)) return [];

        // format created_label
        $created = !empty($data['created']) ? intval($data['created']) : null;

        $created_label = '';
        $parse_label = TableRegistry::get('Utilities')->parseTimestampToLabelTime($created);
        $diff_time = !empty($parse_label['diff_time']) ? $parse_label['diff_time'] : null;
        $created_label =  !empty($parse_label['time']) ? $parse_label['time'] : null;
        if(!in_array($diff_time, ['s', 'i', 'h']) || empty($created_label)) $created_label = date('H:i - d/m/Y', $created);

        // format user_info
        $user_id = !empty($data['user_id']) ? intval($data['user_id']) : null;
        $user_info = !empty($data['user']) ? $data['user'] : [];
        if(!empty($user_info) && is_object($user_info)) $user_info = $user_info->toArray();

        if(empty($user_info)) {
            $user_name = !empty($data['user_name']) && AUTH_USER_ID == 10000 ? $data['user_name'] : 'Super Admin';
            $user_info = [
                'id' => 10000,
                'full_name' => $user_name
            ];
        }

        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'action' => !empty($data['action']) ? $data['action'] : null,
            'type' => !empty($data['type']) ? $data['type'] : null,
            'sub_type' => !empty($data['sub_type']) ? $data['sub_type'] : null,
            'record_id' => !empty($data['record_id']) ? $data['record_id'] : null,
            'user_id' => $user_id,
            'description' => !empty($data['description']) ? $data['description'] : null,
            'link' => !empty($data['link']) ? $data['link'] : null,
            'path_file' => !empty($data['path_file']) ? $data['path_file'] : null,
            'path_log' => !empty($data['path_log']) ? $data['path_log'] : null,
            'version' => !empty($data['version']) ? $data['version'] : null,
            'user' => $user_info,
            'created' => $created,
            'created_label' => $created_label
        ];

        return $result;
    }

    public function writeLog(string $alias = null, string $action = null, int $id = null, $entity = [])
    {
        if(empty($action) || !in_array($action, ['add', 'update', 'update_status', 'delete'])) return;
        if(empty($alias) || empty($id) || empty($entity)) return;

        // chỉ ghi log tài khoản trong quản trị, nên khi tồn tại AUTH_USER_ID mới thực hiện ghi log
        if(!defined('AUTH_USER_ID')) return;

        // lưu dữ liệu cập nhật vào file log json

        $log_info = [];
        $type = DATA;
        $sub_type = null;
        switch($alias){
            case 'Articles':
                $log_info = $this->_article($action, $id, $entity);
                $sub_type = ARTICLE;
            break;

            case 'Products':
                $log_info = $this->_product($action, $id, $entity);
                $sub_type = PRODUCT;
            break;

            case 'Brands':
                $log_info = $this->_brand($action, $id, $entity);
                $sub_type = BRAND;
            break;

            case 'Authors':
                $log_info = $this->_author($action, $id, $entity);
                $sub_type = AUTHOR;
            break;

            case 'Categories':
                $log_info = $this->_category($action, $id, $entity);
                $sub_type = CATEGORY;
            break;

            case 'Orders':
                $log_info = $this->_order($action, $id, $entity);
                $sub_type = ORDER;
            break;

            case 'TemplatesBlock':
                $log_info = $this->_block($action, $id, $entity);
                $sub_type = BLOCK;
            break;

            case 'TemplatesPage':                
                $log_info = $this->_templatePage($action, $id, $entity);
                $sub_type = TEMPLATE_PAGE;
            break;
        }
        
        if(empty($log_info) || empty($log_info['action']) || empty($log_info['description'])) return;

        $write = TableRegistry::get('LogsUtilities')->writeJsonLogRecord($alias, $id);
        $dir_file = !empty($write['dir_file']) ? $write['dir_file'] : null;


        $log_info['user_id'] = AUTH_USER_ID;
        $log_info['user_name'] = AUTH_USER_ID == 10000 ? AUTH_USER_NAME : '';
        $log_info['record_id'] = $id;
        $log_info['type'] = $type;
        $log_info['sub_type'] = $sub_type;
        $log_info['path_log'] = $dir_file;
        $log_info['version'] = !empty($write['version']) ? $write['version'] : null;

        // lưu vào bảng log
        $log_entity = $this->newEntity($log_info);
        $save = $this->save($log_entity);
        if (empty($save->id)) return;      

        return true;
    }    

    private function _isUpdateStatus($action, EntityInterface $entity)
    {
        if($action != 'update' || !isset($entity['status'])) return false;

        $dirty_fields = $entity->getDirty();        
        if(empty($dirty_fields)) return false;

        $dirty_fields = array_flip($dirty_fields);
        unset($dirty_fields['updated']);
        unset($dirty_fields['draft']);

        if(isset($dirty_fields['status']) && count($dirty_fields) == 1) return true;

        return false;
    }

    private function _article($action = null, $id = null, $entity = null)
    {
        // kiểm tra có phải cập nhật trạng thái
        $is_update_status = $this->_isUpdateStatus($action, $entity);

        if($is_update_status) $action = 'update_status';

        // nếu cập nhật field deleted -> xóa
        if(!empty($entity['deleted'])) $action = 'delete';
        
        // lấy tên bài viết (thêm mới hoặc cập nhật)
        $name = !empty($entity['ArticlesContent']['name']) ? $entity['ArticlesContent']['name'] : '';

        // lấy tên bài viết (nhân bản)
        if(empty($name)){
            $name = !empty($entity['ContentMutiple'][0]['name']) ? $entity['ContentMutiple'][0]['name'] : '';
        }

        // lấy tên bài viết trong bảng
        if(empty($name)){
            $content = TableRegistry::get('ArticlesContent')->find()->where([
                'article_id' => $id, 
                'lang' => $this->lang
            ])->select(['name'])->first();

            $name = !empty($content['name']) ? $content['name'] : '';
        }

        // xử lý dữ liệu log
        $description = $link = null;
        if($action == 'add'){
            $description = __d('admin', 'them_moi_{0}_{1}', [strtolower(__d('admin', 'bai_viet')), $name]);
            $link = ADMIN_PATH .'/article/update/' . $id;
        }

        if($action == 'update'){
            $description = __d('admin', 'cap_nhat_{0}_{1}', [strtolower(__d('admin', 'bai_viet')), $name]);
            $link = ADMIN_PATH .'/article/update/' . $id;
        }
        
        if($action == 'update_status'){
            if(!empty($entity['status'])){
                $description = __d('admin', 'kich_hoat_{0}_{1}', [strtolower(__d('admin', 'bai_viet')), $name]);
            }else{
                $description = __d('admin', 'ngung_hoat_dong_{0}_{1}', [strtolower(__d('admin', 'bai_viet')), $name]);
            }
            
            $link = ADMIN_PATH .'/article/update/' . $id;
        }

        if($action == 'delete'){
            $description = __d('admin', 'xoa_{0}_{1}', [strtolower(__d('admin', 'bai_viet')), $name]);
        }

        $result = [
            'action' => $action,
            'description' => $description,
            'link' => $link
        ];

        return $result;
    }

    private function _product($action = null, $id = null, $entity = null)
    {
        // kiểm tra có phải cập nhật trạng thái
        $is_update_status = $this->_isUpdateStatus($action, $entity);
        if($is_update_status) $action = 'update_status';

        // nếu cập nhật field deleted -> xóa
        if(!empty($entity['deleted'])) $action = 'delete';
        
        // lấy tên bài viết (thêm mới hoặc cập nhật)
        $name = !empty($entity['ProductsContent']['name']) ? $entity['ProductsContent']['name'] : '';

        // lấy tên bài viết (nhân bản)
        if(empty($name)){
            $name = !empty($entity['ContentMutiple'][0]['name']) ? $entity['ContentMutiple'][0]['name'] : '';
        }

        // lấy tên sản phẩm trong bảng
        if(empty($name)){
            $content = TableRegistry::get('ProductsContent')->find()->where([
                'product_id' => $id, 
                'lang' => $this->lang
            ])->select(['name'])->first();

            $name = !empty($content['name']) ? $content['name'] : '';
        }

        // xử lý dữ liệu log
        $description = $link = null;
        if($action == 'add'){
            $description = __d('admin', 'them_moi_{0}_{1}', [strtolower(__d('admin', 'san_pham')), $name]);
            $link = ADMIN_PATH .'/product/update/' . $id;
        }

        if($action == 'update'){
            $description = __d('admin', 'cap_nhat_{0}_{1}', [strtolower(__d('admin', 'san_pham')), $name]);
            $link = ADMIN_PATH .'/product/update/' . $id;
        }
        
        if($action == 'update_status'){
            $status = !empty($entity['status']) ? intval($entity['status']) : 0;
            if($status == 1){
                $description = __d('admin', 'kich_hoat_{0}_{1}', [strtolower(__d('admin', 'san_pham')), $name]);
            }elseif ($status == 2){
                $description = __d('admin', 'ngung_kinh_doanh_{0}_{1}', [strtolower(__d('admin', 'san_pham')), $name]);
            }else{
                $description = __d('admin', 'ngung_hoat_dong_{0}_{1}', [strtolower(__d('admin', 'san_pham')), $name]);
            }

            $link = ADMIN_PATH .'/product/update/' . $id;
        }

        if($action == 'delete'){
            $description = __d('admin', 'xoa_{0}_{1}', [strtolower(__d('admin', 'san_pham')), $name]);
        }

        $result = [
            'action' => $action,
            'description' => $description,
            'link' => $link
        ];

        return $result;
    }

    private function _brand($action = null, $id = null, $entity = null)
    {
        // kiểm tra có phải cập nhật trạng thái
        $is_update_status = $this->_isUpdateStatus($action, $entity);

        if($is_update_status) $action = 'update_status';

        // nếu cập nhật field deleted -> xóa
        if(!empty($entity['deleted'])) $action = 'delete';
        
        // lấy tên thương hiệu (thêm mới hoặc cập nhật)
        $name = !empty($entity['BrandsContent']['name']) ? $entity['BrandsContent']['name'] : '';
        if(empty($name)){
            $content = TableRegistry::get('BrandsContent')->find()->where([
                'brand_id' => $id, 
                'lang' => $this->lang
            ])->select(['name'])->first();

            $name = !empty($content['name']) ? $content['name'] : '';
        }

        // xử lý dữ liệu log
        $description = $link = null;
        if($action == 'add'){
            $description = __d('admin', 'them_moi_{0}_{1}', [strtolower(__d('admin', 'thuong_hieu')), $name]);
            $link = ADMIN_PATH .'/brand/update/' . $id;
        }

        if($action == 'update'){
            $description = __d('admin', 'cap_nhat_{0}_{1}', [strtolower(__d('admin', 'thuong_hieu')), $name]);
            $link = ADMIN_PATH .'/brand/update/' . $id;
        }
        
        if($action == 'update_status'){
            if(!empty($entity['status'])){
                $description = __d('admin', 'kich_hoat_{0}_{1}', [strtolower(__d('admin', 'thuong_hieu')), $name]);
            }else{
                $description = __d('admin', 'ngung_hoat_dong_{0}_{1}', [strtolower(__d('admin', 'thuong_hieu')), $name]);
            }
            
            $link = ADMIN_PATH .'/brand/update/' . $id;
        }

        if($action == 'delete'){
            $description = __d('admin', 'xoa_{0}_{1}', [strtolower(__d('admin', 'thuong_hieu')), $name]);
        }

        $result = [
            'action' => $action,
            'description' => $description,
            'link' => $link
        ];

        return $result;
    }

    private function _author($action = null, $id = null, $entity = null)
    {
        // kiểm tra có phải cập nhật trạng thái
        $is_update_status = $this->_isUpdateStatus($action, $entity);

        if($is_update_status) $action = 'update_status';

        // nếu cập nhật field deleted -> xóa
        if(!empty($entity['deleted'])) $action = 'delete';
        
        // lấy tên thương hiệu (thêm mới hoặc cập nhật)
        $full_name = !empty($entity['Authors']['full_name']) ? $entity['Authors']['full_name'] : '';
        if(empty($name)){
            $content = TableRegistry::get('Authors')->find()->where([
                'id' => $id
            ])->select(['full_name'])->first();

            $full_name = !empty($content['full_name']) ? $content['full_name'] : '';
        }

        // xử lý dữ liệu log
        $description = $link = null;
        if($action == 'add'){
            $description = __d('admin', 'them_moi_{0}_{1}', [strtolower(__d('admin', 'tac_gia')), $full_name]);
            $link = ADMIN_PATH .'/author/update/' . $id;
        }

        if($action == 'update'){
            $description = __d('admin', 'cap_nhat_{0}_{1}', [strtolower(__d('admin', 'tac_gia')), $full_name]);
            $link = ADMIN_PATH .'/author/update/' . $id;
        }
        
        if($action == 'update_status'){
            if(!empty($entity['status'])){
                $description = __d('admin', 'kich_hoat_{0}_{1}', [strtolower(__d('admin', 'tac_gia')), $full_name]);
            }else{
                $description = __d('admin', 'ngung_hoat_dong_{0}_{1}', [strtolower(__d('admin', 'tac_gia')), $full_name]);
            }
            
            $link = ADMIN_PATH .'/author/update/' . $id;
        }

        if($action == 'delete'){
            $description = __d('admin', 'xoa_{0}_{1}', [strtolower(__d('admin', 'tac_gia')), $full_name]);
        }

        $result = [
            'action' => $action,
            'description' => $description,
            'link' => $link
        ];

        return $result;
    }

    private function _category($action = null, $id = null, $entity = null)
    {
        // kiểm tra có phải cập nhật trạng thái
        $is_update_status = $this->_isUpdateStatus($action, $entity);
        if($is_update_status) $action = 'update_status';

        // nếu cập nhật field deleted -> xóa
        if(!empty($entity['deleted'])) $action = 'delete';
        
        // lấy tên danh mục
        $name = !empty($entity['CategoriesContent']['name']) ? $entity['CategoriesContent']['name'] : '';
        if(empty($name)){
            $content = TableRegistry::get('CategoriesContent')->find()->where([
                'category_id' => $id, 
                'lang' => $this->lang
            ])->select(['name'])->first();

            $name = !empty($content['name']) ? $content['name'] : '';
        }

        // lấy loại danh mục
        $type = !empty($entity['type']) ? $entity['type'] : '';        
        if(empty($type)){
            $category = TableRegistry::get('Categories')->find()->where([
                'id' => $id, 
            ])->select(['id', 'type'])->first();

            $type = !empty($category['type']) ? $category['type'] : '';
        }
       
        // xử lý dữ liệu log
        $description = $link = null;
        if($action == 'add'){
            $description = __d('admin', 'them_moi_{0}_{1}', [strtolower(__d('admin', 'danh_muc')), $name]);

            if(!empty($type)) $link = ADMIN_PATH . "/category/$type/update/" . $id;
        }

        if($action == 'update'){
            $description = __d('admin', 'cap_nhat_{0}_{1}', [strtolower(__d('admin', 'danh_muc')), $name]);
            if(!empty($type)) $link = ADMIN_PATH . "/category/$type/update/" . $id;
        }
        
        if($action == 'update_status'){
            if(!empty($entity['status'])){
                $description = __d('admin', 'kich_hoat_{0}_{1}', [strtolower(__d('admin', 'danh_muc')), $name]);
            }else{
                $description = __d('admin', 'ngung_hoat_dong_{0}_{1}', [strtolower(__d('admin', 'danh_muc')), $name]);
            }
            
            if(!empty($type)) $link = ADMIN_PATH . "/category/$type/update/" . $id;
        }

        if($action == 'delete'){
            $description = __d('admin', 'xoa_{0}_{1}', [strtolower(__d('admin', 'danh_muc')), $name]);
        }

        $result = [
            'action' => $action,
            'description' => $description,
            'link' => $link
        ];

        return $result;
    }

    private function _order($action = null, $id = null, $entity = null)
    {
        // kiểm tra có phải cập nhật trạng thái
        $is_update_status = $this->_isUpdateStatus($action, $entity);
        if($is_update_status) $action = 'update_status';

        // nếu cập nhật field deleted -> xóa
        if(!empty($entity['deleted'])) $action = 'delete';
        
        // lấy mã đơn hàng
        $code = !empty($entity['code']) ? $entity['code'] : '';
        if(empty($code)){
            $order = TableRegistry::get('Orders')->find()->where([
                'id' => $id,
            ])->select(['code'])->first();

            $code = !empty($order['code']) ? $order['code'] : '';
        }

        // xử lý dữ liệu log
        $description = $link = null;
        if($action == 'add'){
            $description = __d('admin', 'them_moi_{0}_{1}', [strtolower(__d('admin', 'don_hang')), $code]);
            $link = ADMIN_PATH .'/order/detail/' . $id;
        }

        if($action == 'update'){
            $description = __d('admin', 'cap_nhat_{0}_{1}', [strtolower(__d('admin', 'don_hang')), $code]);
            $link = ADMIN_PATH .'/order/detail/' . $id;
        }
        
        if($action == 'update_status'){
            $description = __d('admin', 'thay_doi_trang_thai_{0}_{1}', [strtolower(__d('admin', 'don_hang')), $code]);   
            $link = ADMIN_PATH .'/order/detail/' . $id;
        }

        if($action == 'delete'){
            $description = __d('admin', 'xoa_{0}_{1}', [strtolower(__d('admin', 'don_hang')), $code]);
        }

        $result = [
            'action' => $action,
            'description' => $description,
            'link' => $link
        ];
        
        return $result;
    }

    private function _block($action = null, $id = null, $entity = null)
    {

        // kiểm tra có phải cập nhật trạng thái
        $is_update_status = $this->_isUpdateStatus($action, $entity);
        if($is_update_status) $action = 'update_status';

        // nếu cập nhật field deleted -> xóa
        if(!empty($entity['deleted'])) $action = 'delete';

        // lấy code và tên block
        $code = !empty($entity['code']) ? $entity['code'] : '';
        $name = !empty($entity['name']) ? $entity['name'] : '';
        if(empty($code) || empty($name)){
            $block = TableRegistry::get('Blocks')->find()->where([
                'id' => $id,
            ])->select(['code', 'name'])->first();

            $code = !empty($block['code']) ? $block['code'] : '';
            $name = !empty($block['name']) ? $block['name'] : '';
        }

        // xử lý dữ liệu log
        $description = $link = null;
        if($action == 'add'){
            $description = __d('admin', 'them_moi_{0}_{1}', ['BLOCK', $name]);
            $link = ADMIN_PATH .'/template/block/update/' . $code;
        }

        if($action == 'update'){
            $description = __d('admin', 'cap_nhat_{0}_{1}', ['BLOCK', $name]);
            $link = ADMIN_PATH .'/template/block/update/' . $code;
        }
        
        if($action == 'update_status'){
            $description = __d('admin', 'thay_doi_trang_thai_{0}_{1}', ['BLOCK', $name]);
            $link = ADMIN_PATH .'/template/block/update/' . $code;
        }

        if($action == 'delete'){
            $description = __d('admin', 'xoa_{0}_{1}', ['BLOCK', $name]);
        }

        $result = [
            'action' => $action,
            'description' => $description,
            'link' => $link
        ];

        return $result;
    }

    private function _templatePage($action = null, $id = null, $entity = null)
    {
        // kiểm tra có phải cập nhật trạng thái
        $is_update_status = $this->_isUpdateStatus($action, $entity);
        if($is_update_status) $action = 'update_status';

        // nếu cập nhật field deleted -> xóa
        if(!empty($entity['deleted'])) $action = 'delete';
        
        // lấy tên trang
        $name = !empty($entity['name']) ? $entity['name'] : '';
        if(empty($code) || empty($name)){
            $page_info = TableRegistry::get('TemplatesPage')->find()->where(['id' => $id])->first();
            $name = !empty($page_info['name']) ? $page_info['name'] : '';
        }

        // xử lý dữ liệu log
        $description = $link = null;
        if($action == 'add'){
            $description = __d('admin', 'them_moi_{0}_{1}', ['TEMPLATE_PAGE', $name]);
            $link = ADMIN_PATH .'/template/customize';
        }

        if($action == 'update'){
            $description = __d('admin', 'cap_nhat_{0}_{1}', ['TEMPLATE_PAGE', $name]);
            $link = ADMIN_PATH .'/template/customize';
        }

        if($action == 'delete'){
            $description = __d('admin', 'xoa_{0}_{1}', ['TEMPLATE_PAGE', $name]);
        }

        $result = [
            'action' => $action,
            'description' => $description,
            'link' => $link
        ];
        
        return $result;
    }

    public function writeLogChangeFile($action = null, $dir_file = null)
    {
        if(empty($action) || !in_array($action, ['add', 'update', 'delete'])) return false;
        if(empty($dir_file) || !file_exists($dir_file)) return false;
        if(strpos($dir_file, SOURCE_DOMAIN . DS . 'templates') === false) return false;
        if(is_dir($dir_file)) return;

        $filename = basename($dir_file);
        $dir_log_file_origin = str_replace(SOURCE_DOMAIN . DS . 'templates', $this->dir_log, $dir_file);
        $dir_log_file = $dir_log_file_origin . '_' . time();

        // tạo thư mục chứ file log nếu chưa tồn tại
        $create = TableRegistry::get('LogsUtilities')->creatDirLog(dirname($dir_log_file));
        if(!$create) return;

        // copy file cũ sang file log
        $copy = @copy($dir_file, $dir_log_file);
        if(empty($copy)) return false;

        // kiểm tra số lượng file log của tệp (nếu vượt quá 20 tệp thì xóa tệp log cũ đi)
        $files = glob($dir_log_file_origin . '_*');
        if(count($files) > $this->number_log_file){
            $number_delete = count($files) - $this->number_log_file;
            $i = 0;
            foreach($files as $old_file){
                $i ++;
                if($i > $number_delete) continue;
                @unlink($old_file);
            }
        }

        // lưu path file
        $path_file = TableRegistry::get('Utilities')->dirToPath($dir_file);
        $path_log = TableRegistry::get('Utilities')->dirToPath($dir_log_file);

        $description = __d('admin', 'cap_nhat_tep_{0}', [$filename]);
        if($action == 'add'){
            $description = __d('admin', 'them_moi_tep_{0}', [$filename]);
        }

        if($action == 'delete'){
            $description = __d('admin', 'xoa_tep_{0}', [$filename]);
        }

        $data_save = [
            'action' => $action,
            'type' => TEMPLATE,
            'user_id' => AUTH_USER_ID,
            'description' => $description,
            'link' => null,
            'path_file' => $path_file,
            'path_log' => $path_log
        ];

        // lưu vào bảng log
        $entity = $this->newEntity($data_save);
        $save = $this->save($entity);
        if (empty($save->id)) return false;
        
        return true;
    }

    public function getListLogChangeFileTemplate($dir_file = null)
    {
        if(empty($dir_file) || !file_exists($dir_file)) return [];
        if(strpos($dir_file, SOURCE_DOMAIN . DS . 'templates') === false) return [];
        if(is_dir($dir_file)) return [];

        $log_dir = str_replace(SOURCE_DOMAIN . DS . 'templates', SOURCE_DOMAIN . DS . 'system_logs', $dir_file);
        $files = glob($log_dir . '_*');

        if(empty($files)) return [];
        $files = array_reverse($files);
        
        $utilities = TableRegistry::get('Utilities');
        $result = [];
        foreach($files as $file){
            $result[] = [
                'filename' => basename($file),
                'dir' => $file,
                'path' => $utilities->dirToPath($file)
            ];
        }

        return $result;
    }
}