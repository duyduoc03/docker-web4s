<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\I18n\Time;
use Cake\Datasource\ConnectionManager;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CommentController extends AppController {

    protected $limit_import = 50;
    public function initialize(): void
    {
        parent::initialize();
    }

    public function list()
    {
        $this->css_page = [
            '/assets/css/pages/todo/todo.css',
            '/assets/plugins/global/lightbox/lightbox.css',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];
        $this->js_page = [
            '/assets/plugins/global/lightbox/lightbox.min.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/js/pages/list_comment.js'
        ];

        $this->set('path_menu', 'comment');
        $this->set('title_for_layout', __d('admin', 'danh_sach_binh_luan'));
    }

    public function listCommentByRecord($type = null)
    {
        $comments = [];
        if(empty($type)) return $comments;

        $table = TableRegistry::get('Comments');
        $utilities = $this->loadComponent('Utilities');

        $data = $params = [];

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        // params query
        $params[QUERY] = !empty($data[QUERY]) ? $data[QUERY] : [];

        // params filter
        $params[FILTER] = !empty($data[DATA_FILTER]) ? $data[DATA_FILTER] : [];
        if(!empty($params[QUERY])){
            $params[FILTER] = array_merge($params[FILTER], $params[QUERY]);
        }

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : 10;
        
        // sort 
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        if (empty($params[FILTER]['parent_id'])) $params['get_only_parent'] = true;

        // validate params
        if(empty($params[FILTER]['foreign_id'])) $this->responseJson([CODE => SUCCESS]);

        try {
            $comment = $this->paginate($table->queryListComments($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        } catch (Exception $e) {
            $page = 1;
            $comment = $this->paginate($table->queryListComments($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $comments = [];
        if(!empty($comment)){
            foreach($comment as $item){
                $comment = $table->parseDetailComment($item);
                $comments[] = $comment;
            }
        }
        
        $pagination_info = !empty($this->request->getAttribute('paging')['Comments']) ? $this->request->getAttribute('paging')['Comments'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $comments, 
            META => $meta_info
        ]);
    }

    public function listJson()
    {
        $table = TableRegistry::get('Comments');
        $utilities = $this->loadComponent('Utilities');

        $data = $params = [];

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        // params query
        $params[QUERY] = !empty($data[QUERY]) ? $data[QUERY] : [];

        // params filter
        $params[FILTER] = !empty($data[DATA_FILTER]) ? $data[DATA_FILTER] : [];
        if(!empty($params[QUERY])){
            $params[FILTER] = array_merge($params[FILTER], $params[QUERY]);
        }

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : 10;
        
        // sort 
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        if(!empty($data['export']) && $data['export'] == 'all') {
            $limit = 100000;
        }

        try {
            $comments = $this->paginate($table->queryListComments($params), [
                'limit' => $limit,
                'maxLimit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        } catch (Exception $e) {
            $page = 1;
            $comments = $this->paginate($table->queryListComments($params), [
                'limit' => $limit,
                'maxLimit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $result = [];
        if(!empty($comments)){
            foreach($comments as $item){
                $item_format = $table->parseDetailComment($item);

                // lấy tên bản ghi
                $type = !empty($item_format['type']) ? $item_format['type'] : null;
                $foreign_id = !empty($item_format['foreign_id']) ? $item_format['foreign_id'] : null;

                if(!empty($foreign_id) && $type == PRODUCT_DETAIL){
                    $product_info = TableRegistry::get('Products')->getDetailProduct($foreign_id, $this->lang);

                    $item_format['record_name'] = !empty($product_info['ProductsContent']['name']) ? $product_info['ProductsContent']['name'] : '';
                }

                if(!empty($foreign_id) && $type == ARTICLE_DETAIL){
                    $article_info = TableRegistry::get('Articles')->getDetailArticle($foreign_id, $this->lang);

                    $item_format['record_name'] = !empty($article_info['ArticlesContent']['name']) ? $article_info['ArticlesContent']['name'] : '';
                }

                $result[] = $item_format;
            }
        }

        if(!empty($data['export'])) {
            return $this->exportExcelComment($result);
        }
        
        $pagination_info = !empty($this->request->getAttribute('paging')['Comments']) ? $this->request->getAttribute('paging')['Comments'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
            META => $meta_info
        ]);
    }

    public function exportExcelComment($data = [])
    {
        if(empty($data)) return false;

        $spreadsheet = $this->initializationExcel($data);
        $writer = new Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();
        
        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
            META => [
                'name' => __d('admin', 'thong_tin_binh_luan_') . time()
            ]
        ]);
    }

    // khởi tạo file excel
    // Dùng để export dữ liệu excel và download file excel mẫu
    public function initializationExcel($data = [])
    {
        $languages = TableRegistry::get('Languages')->getList(); 
        $domain = 'https://' . $this->request->host();

        $data_dropdown = [
            'languages' => !empty($languages) ? implode(',', $languages) : __d('admin', 'tieng_viet'),
            'true_false' => __d('admin', 'co') .','.__d('admin', 'khong'),
            'status' => __d('admin', 'hoat_dong') .','.__d('admin', 'ngung_hoat_dong'),
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getActiveSheet()->setTitle(__d('admin', 'thong_tin_binh_luan'));

        $arr_header = [
            'id' => __d('admin', 'id'),
            'full_name' => __d('admin', 'ten_khach_hang'),
            'phone' => __d('admin', 'so_dien_thoai'),
            'email' => __d('admin', 'email'),
            'type' => __d('admin', 'bai_viet_san_pham'),
            'type_comment' => __d('admin', 'loai_binh_luan'),
            'record_name' => __d('admin', 'tieu_de'),
            'content' => __d('admin', 'noi_dung'),
            'status' => __d('admin', 'trang_thai')
        ];

        if (empty($arr_header)) return false;

        $column = $column_old = $column_end = 'A';
        $row = 1;

        foreach ($arr_header as $key => $header) {
            $sheet->setCellValue($column . $row, $header);
            $sheet->getStyle($column . $row)->getFont()->setBold(true);
            $sheet->getStyle($column . $row)->getAlignment()->setVertical('center');

            switch ($key) {
                case 'id':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(25, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'record_name':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(250, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'content':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(200, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'status':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(90, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                default: 
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
            }

            $column_old = $column_end = $column;
            $column++;
        }

        // style excel
        $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(25);
        $spreadsheet->getActiveSheet()->getStyle('A1:' . $column_end . '1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('fcb789');

        $row_excel = 2;
        foreach ($data as $key => $item) { 
            // thêm dữ liệu full vào row excel
            $colum_excel = 'A';
            foreach ($arr_header as $code => $header) {

                switch ($code) {
                    case 'id':
                        $sheet->setCellValue($colum_excel . $row_excel, !empty($item[$code]) ? $item[$code] : '');
                        $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

                        break;
                    case 'record_name':
                        $record_name = !empty($item['record_name']) ? strip_tags($item['record_name']) : '';
                        $url = !empty($item['url']) ? $domain . '/' . strip_tags($item['url']) : '';

                        $sheet->setCellValue($colum_excel . $row_excel, $record_name);
                        $spreadsheet->getActiveSheet()->getStyle($colum_excel . $row_excel)->getAlignment()->setWrapText(true);
                        $spreadsheet->getActiveSheet()->getCell($colum_excel . $row_excel)->getHyperlink()->setUrl($url);

                        break;
                    case 'type':
                        $sheet->setCellValue($colum_excel . $row_excel, !empty($item['type']) && $item['type'] == PRODUCT_DETAIL  ? __d('admin', 'san_pham') : __d('admin', 'bai_viet'));
                        $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

                        break;
                    case 'type_comment':
                        $sheet->setCellValue($colum_excel . $row_excel, !empty($item['type_comment']) && $item['type_comment'] == COMMENT  ? __d('admin', 'binh_luan') : __d('admin', 'danh_gia'));
                        $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

                        break;
                    case 'status':
                        $sheet->setCellValue($colum_excel . $row_excel, !empty($item['status']) ? __d('admin', 'hoat_dong') : __d('admin', 'ngung_hoat_dong'));

                        $validation = $spreadsheet->getActiveSheet()->getCell($colum_excel.$row_excel)->getDataValidation();
                        $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::TYPE_LIST );
                        $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::STYLE_INFORMATION );
                        $validation->setAllowBlank(false);
                        $validation->setShowInputMessage(true);
                        $validation->setShowErrorMessage(true);
                        $validation->setShowDropDown(true);
                        $validation->setErrorTitle('Input error');
                        $validation->setError('Value is not in list.');
                        $validation->setPromptTitle('Pick from list');
                        $validation->setPrompt('Please pick a value from the drop-down list.');
                        $validation->setFormula1('"' . $data_dropdown['status'] . '"');

                        $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

                        break;
                    case 'content':
                        $sheet->setCellValue($colum_excel . $row_excel, !empty($item['content']) ? strip_tags($item['content']) : '');
                        $spreadsheet->getActiveSheet()->getStyle($colum_excel . $row_excel)->getAlignment()->setWrapText(true);

                        break;
                    
                    default:
                        $sheet->setCellValue($colum_excel . $row_excel, !empty($item[$code]) ? $item[$code] : '');
                        break;
                }

                $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setVertical('center');
                $colum_excel ++;
            }

            $row_excel ++;
        }

        return $spreadsheet;
    }

    public function uploadFile()
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('template', 'phuong_thuc_khong_hop_le')]);
        }

        $file = !empty($_FILES['file']) ? $_FILES['file'] : [];
        if(empty($file)){
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }
        
        $result_upload = $this->loadComponent('Upload')->uploadToCdn($file, COMMENT, [
            'ignore_logo_attach' => true
        ]);

        if(empty($result_upload[CODE]) || $result_upload[CODE] != SUCCESS){
            $this->responseJson([
                MESSAGE => !empty($result_upload[MESSAGE]) ? $result_upload[MESSAGE] : null
            ]);
        }

        $this->responseJson([
            CODE => SUCCESS, 
            DATA => $result_upload[DATA] ? $result_upload[DATA] : []
        ]);
    }

    public function adminReply()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        $id = !empty($data['id']) ? $data['id'] : null;
        $content = !empty($data['content']) ? $data['content'] : null;

        if(!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        if(empty($content)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_noi_dung_binh_luan')]);   
        }

        if(empty($id)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_binh_luan')]);   
        }

        $utilities = $this->loadComponent('Utilities');
        $table = TableRegistry::get('Comments');

        $comment = $table->find()->where(['id' => $id, 'deleted' => 0])->first();
        if(empty($comment)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_binh_luan')]);   
        }

        $parent_id = !empty($comment['parent_id']) ? intval($comment['parent_id']) : null;        
        if(!empty($parent_id)){
            $parent_info = $table->find()->where([
                'id' => $parent_id, 
                'deleted' => 0
            ])->first();

            if(empty($parent_info)){
                $this->responseJson([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_binh_luan')]);
            }
        }else{
            $parent_info = $comment;
            $parent_id = $id;
        }        

        $images = [];
        if(!empty($data['images'])){
            foreach (json_decode($data['images'], true) as $key => $image) {
                $images[] = str_replace(CDN_URL , '', $image);
            }
        }

        $info_user = $this->Auth->user();

        $data_comment = [
            'type_comment' => !empty($parent_info['type_comment']) ? $parent_info['type_comment'] : null,
            'type' => !empty($parent_info['type']) ? $parent_info['type'] : null,
            'foreign_id' => !empty($parent_info['foreign_id']) ? intval($parent_info['foreign_id']) : null,            
            'parent_id' => $parent_id,
            'full_name' => !empty($info_user['full_name']) ? $info_user['full_name'] : null,            
            'email' => !empty($info_user['email']) ? $info_user['email'] : null,
            'phone' => null,
            'content' => $content,
            'url' => !empty($parent_info['url']) ? $parent_info['url'] : null,
            'parent_id' => $parent_id,
            'images' => $images,
            'status' => 1,
            'is_admin' => 1,
            'admin_user_id' => !empty($info_user['id']) ? $info_user['id'] : null,
            'foreign_id' => !empty($parent_info['foreign_id']) ? intval($parent_info['foreign_id']) : null,
            'type' => !empty($parent_info['type']) ? $parent_info['type'] : null
        ];

        $add_comment = $this->loadComponent('Comment')->addComment($data_comment);
        die(json_encode($add_comment));        
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

        $table = TableRegistry::get('Comments');
        $comment_component = $this->loadComponent('Comment');

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach($ids as $id){
                $comment = $table->find()->where([
                    'id' => $id,
                    'deleted' => 0
                ])->select(['id', 'type_comment', 'type', 'parent_id', 'foreign_id', 'status'])->first();
                if (empty($comment)) {
                    throw new Exception(__d('admin', 'khong_tim_thay_thong_tin_binh_luan'));
                }

                $comment = $table->patchEntity($comment, ['id' => $id, 'status' => $status]);
                $save = $table->save($comment);
                if (empty($save->id)){
                    throw new Exception();
                }

                if(!empty($comment['parent_id'])) {
                    $update_reply = $comment_component->updateNumberReply($comment['parent_id']);
                    if (!$update_reply){
                        throw new Exception();
                    }
                }

                if($comment['type'] == PRODUCT_DETAIL){
                    $update_product_comment = $comment_component->updateInfoComment($comment['foreign_id'], PRODUCT_DETAIL);
                    if (!$update_product_comment){
                        throw new Exception();
                    }
                }

                if($comment['type'] == ARTICLE_DETAIL){
                    $update_article_comment = $comment_component->updateInfoComment($comment['foreign_id'], ARTICLE_DETAIL);
                    if (!$update_article_comment){
                        throw new Exception();
                    }
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

    public function delete()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];

        if (!$this->getRequest()->is('post') || empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Comments');
        $comment_component = $this->loadComponent('Comment');

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach($ids as $id){
                $comment = $table->find()->where([
                    'id' => $id,
                    'deleted' => 0
                ])->select(['id', 'type_comment', 'type', 'parent_id', 'foreign_id'])->first();

                if (empty($comment)) {
                    throw new Exception(__d('admin', 'khong_tim_thay_thong_tin'));
                }

                $delete = $table->delete($comment);
                if (empty($delete)){
                    throw new Exception();
                }

                if(!empty($comment['parent_id'])) {
                    $update_reply = $comment_component->updateNumberReply($comment['parent_id']);
                    if (!$update_reply){
                        throw new Exception();
                    }
                }

                if($comment['type'] == PRODUCT_DETAIL){
                    $update_product_comment = $comment_component->updateInfoComment($comment['foreign_id'], PRODUCT_DETAIL);
                    if (!$update_product_comment){
                        throw new Exception();
                    }
                }

                if($comment['type'] == ARTICLE_DETAIL){
                    $update_product_comment = $comment_component->updateInfoComment($comment['foreign_id'], ARTICLE_DETAIL);
                    if (!$update_product_comment){
                        throw new Exception();
                    }
                }
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function importDataByExcel()
    {
        $this->layout = false;
        $this->autoRender = false;

        $excel_file = !empty($_FILES['excel_file']) ? $_FILES['excel_file'] : [];

        if (!$this->getRequest()->is('post') || empty($excel_file)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }
        $upload = $this->loadComponent('Upload');
        $files = !empty($excel_file['tmp_name']) ? $excel_file['tmp_name'] : null;

        /**  Identify the type of $inputFileName  **/
        $file_type = \PhpOffice\PhpSpreadsheet\IOFactory::identify($files);
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($file_type);
        $spreadsheet = $reader->load($files);

        $data_excel = $spreadsheet->getActiveSheet()->toArray();

        if (empty($data_excel) || count($data_excel) < 2) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        // xóa dữ liệu tiêu đề và header
        unset($data_excel[0]);

        $utilities = $this->loadComponent('Utilities');
        $table = TableRegistry::get('Comments');

        $languages = TableRegistry::get('Languages')->getList();
        
        $arr_type_comment = [
            'comment' => __d('admin', 'binh_luan'),
            'rating' => __d('admin', 'danh_gia')
        ];

        $arr_type = [
            'product_detail' => __d('admin', 'san_pham'),
            'article_detail' => __d('admin', 'bai_viet')
        ];

        $arr_header = [
            'full_name',
            'phone',
            'email',
            'type_comment',
            'type',
            'foreign_id',
            'content',
            'images',
            'rating'
        ];

        // loại bọ các trường thông tin null trong data_excel
        $data_excel = array_filter(array_map('array_filter', $data_excel));

        $data_comments = [];
        $city_id = $district_id = $id = null;
        if (!empty($data_excel)) {
            foreach ($data_excel as $key => $val) {

                $check_type =  null;

                // đọc dữ liệu từ excel
                foreach ($arr_header as $k => $code) {
                    switch ($code) {
                        case 'full_name':
                            $full_name = !empty($val[$k]) ? trim($val[$k]) : null;
                            if (empty($full_name)) $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_ho_va_ten')]);

                            $data_comments[$key][$code] = $full_name;

                            break;
                        case 'phone':
                            $phone = !empty($val[$k]) ? trim($val[$k]) : null;
                            if (empty($phone)) $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_so_dien_thoai')]);

                            $data_comments[$key][$code] = $phone;

                            break;
                        case 'email':
                            $email = !empty($val[$k]) ? trim($val[$k]) : null;
                            if (empty($email)) $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_dia_chi_email_khach_hang')]);

                            $data_comments[$key][$code] = $email;

                            break;
                        case 'type_comment':
                            $type_comment = !empty($val[$k]) ? trim($val[$k]) : null;
                            if (empty($type_comment)) $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_loai_binh_luan')]);

                            foreach ($arr_type_comment as $key_type => $value) {
                                if($value === $type_comment) $data_comments[$key][$code] = $key_type;
                            }

                            break;
                        case 'type':
                            $type = !empty($val[$k]) ? trim($val[$k]) : null;
                            if (empty($type)) $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_loai_san_pham_hoac_bai_viet')]);

                            foreach ($arr_type as $key_type => $value) {
                                if($value === $type) {
                                    $data_comments[$key][$code] = $key_type;
                                    $check_type = $key_type;
                                }
                            }

                            break;
                        case 'foreign_id':
                            $foreign_id = !empty($val[$k]) ? trim($val[$k]) : null;
                            if (empty($foreign_id)) $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_id_san_pham_hoac_bai_viet')]);

                            $message = null;
                            if($check_type == PRODUCT_DETAIL) {
                                $table_comment = TableRegistry::get('Products');
                                $message = __d('admin', 'id_san_pham_{0}_khong_ton_tai_vui_long_kiem_tra_lai', [$foreign_id]);
                            }
                            if($check_type == ARTICLE_DETAIL) {
                                $table_comment = TableRegistry::get('Articles');
                                $message = __d('admin', 'id_bai_viet_{0}_khong_ton_tai_vui_long_kiem_tra_lai', [$foreign_id]);
                            }

                            $post_info = $table_comment->find()->where(['id' => $foreign_id, 'status' => 1, 'deleted' => 0])->select('id')->first();
                            if (empty($post_info)) $this->responseJson([MESSAGE => $message]);

                            $data_comments[$key][$code] = $foreign_id;

                            break;
                        case 'images':
                            $images = !empty($val[$k]) ? trim($val[$k]) : null;

                            $list_image = null;
                            if(!empty($images)) {
                                $arr_images = explode(",", $images);
                                foreach ($arr_images as $image) {
                                    $list_image[] = str_replace(CDN_URL, "", $image);
                                }
                            }

                            $data_comments[$key][$code] = $list_image;

                            break;
                        default:
                            $value = !empty($val[$k]) ? trim($val[$k]) : null;

                            $data_comments[$key][$code] = $value;

                            break;
                    }
                }
            }            
        }

        if (empty($data_comments)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $result = [
            CODE => ERROR,
            MESSAGE => __d('admin', 'cap_nhat_thong_tin_binh_luan_khong_thanh_cong'),
            DATA => [],
        ];


        $dataExcel = $upload->_createTmpDataJson('excel', $data_comments, $this->limit_import);

        if(empty($dataExcel)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_tao_duoc_du_lieu')]);
        }

        exit(json_encode([
            CODE => SUCCESS,
            DATA => [
                'folder' => $dataExcel
            ]
        ]));
    }

    private function _createTmpDataJson($folder_name = 'example', $data = null, $limit = 10)
    {
        if(empty($data)) return null;

        $dir_folder = TMP . $folder_name . DS;
        $create_dir_folder = new Folder($dir_folder, true, 0755);

        $folder_child_name = Text::uuid();
        $dir_folder_child = $dir_folder . $folder_child_name . DS;


        $create_folder_child = new Folder($dir_folder_child, true, 0755);
        $files = $create_folder_child->find('.*\.json', true);

        $data = array_chunk($data, $limit);
        foreach ($data as $k_data => $v_data) {
            $file = new File($dir_folder_child . str_pad($k_data, 5, '0', STR_PAD_LEFT). '.json', true, 0755);
            $file->write(json_encode($v_data), 'w');
            $file->close();
        }

        return $folder_child_name;
    }

    public function processImportExcel()
    {   
        $this->viewBuilder()->enableAutoLayout(false);

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $data = $this->getRequest()->getData();

        $page = !empty($data['page']) ? $data['page'] : 0;
        $folder = !empty($data['folder']) ? $data['folder'] : null;

        if(empty($folder)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_ten_thu_muc_excel')]);
        }

        $table = TableRegistry::get('Comments');
        $utilities = $this->loadComponent('Utilities');

        $dir_folder = TMP . 'excel' . DS . $folder . DS;
        $create_dir_folder = new Folder($dir_folder);
        $files = $create_dir_folder->find('.*\.json', true);
        $file = new File($dir_folder . $files[$page]);

        $data = [];
        if($file->exists()){                        
            $content = $file->read();
            $file->close();
            $data = $utilities->isJson($content) ? json_decode($content, true) : [];
        }
        
        if(empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_co_du_lieu_excel')]);
        }

        $new_entities = [];
        foreach ($data as $key => $item_save) {
            $full_name = !empty($item_save['full_name']) ? $item_save['full_name'] : null;
            $phone = !empty($item_save['phone']) ? $item_save['phone'] : null;
            $email = !empty($item_save['email']) ? $item_save['email'] : null;
            $content = !empty($item_save['content']) ? $item_save['content'] : null;

            $item_save['images'] = !empty($item_save['images']) ? json_encode($item_save['images']) : null;
            $item_save['status'] = 1;
            $item_save['is_admin'] = null;
            $item_save['search_unicode'] = strtolower($utilities->formatSearchUnicode([$full_name, $email, $phone, $content]));
            $new_entities[] = $item_save;
        }

        if(!empty($new_entities)) {
            $comments = $table->newEntities($new_entities);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            // save data
            $save = $table->saveMany($comments);
            if (empty($save)){
                throw new Exception();
            }

            $conn->commit();

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }

        $percent = $total_comment = 0;
        $perpage = $this->limit_import;
        $total_page = count($files);

        if($total_page > ($page + 1) )
        {
            $percent = (($page + 1) * $perpage) / ((($total_page - 1) * $perpage) + count($data)) * 100;
            $this->responseJson([
                CODE => SUCCESS,
                DATA => [
                    'continue' => true,
                    'page' => $page + 1,
                    'product' => (($page + 1) * $perpage),
                    'folder' => $folder,
                    'percent' => $percent
                ]
            ]);
        }

        $total_comment = count($data);
        if($total_page > 1)
        {
            $total_comment = $page * $perpage + count($data);
        }

        $delete = @$create_dir_folder->delete();
        $percent = 100;
        $this->responseJson([
            CODE => SUCCESS,
            DATA => [
                'continue' => false,
                'product' => $total_comment,
                'percent' => $percent
            ]
        ]);
    }
}