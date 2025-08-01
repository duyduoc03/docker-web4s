<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Http\Response;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Hash;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ContactController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function list()
    {   
        $session = $this->request->getSession();
        $form_id = $session->read('form_id');

        $where['deleted'] = 0;
        if(!empty($form_id)) $where['id'] = $form_id;

        if($this->request->is('ajax')){
            $data = $this->getRequest()->getData();
            $form_id = !empty($data['form_id']) ? intval($data['form_id']) : null;
            $where['id'] = $form_id;
        }

        $contact_info = TableRegistry::get('ContactsForm')->find()->where($where)->select(['id', 'name', 'code', 'fields'])->first();
        $id = !empty($contact_info['id']) ? intval($contact_info['id']) : null;
        $name = !empty($contact_info['name']) ? $contact_info['name'] : null;
        $fields = !empty($contact_info['fields']) ? json_decode($contact_info['fields'], true) : [];

        // call ajax write session from_id khi thay đổi form đăng ký
        if($this->request->is('ajax')){
            if(empty($contact_info)) $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
            $session->write('form_id', $form_id);

            $this->responseJson([
                CODE => SUCCESS,
                MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong')
            ]);
        }

        $this->js_page = '/assets/js/pages/list_contact.js';

        $this->set('fields', json_encode($fields));
        $this->set('form_id', $id);
        $this->set('path_menu', 'contact');
        $this->set('title_for_layout', __d('admin', 'lien_he_cua_khach_hang') . ': ' . $name);
        
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Contacts');
        $utilities = $this->loadComponent('Utilities');

        $session = $this->request->getSession();
        $form_id = $session->read('form_id');

        $data = $params = [];

        $limit = PAGINATION_LIMIT_ADMIN;
        $page = 1;
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        // params query
        $params[QUERY] = !empty($data[QUERY]) ? $data[QUERY] : [];
        $params['get_form'] = true;

        // params filter
        $params[FILTER] = !empty($data[DATA_FILTER]) ? $data[DATA_FILTER] : [];
        $params[FILTER]['form_id'] = $form_id;
        if(!empty($params[QUERY])){
            $params[FILTER] = array_merge($params[FILTER], $params[QUERY]);
        }

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;
        
        // sort 
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        if(!empty($data['export']) && $data['export'] == 'all') {
            $limit = 1000;
            $count_eport_contact = $table->queryListContacts($params)->count();
            if(!empty($count_eport_contact) && $count_eport_contact > $limit){
                $page_number = ceil($count_eport_contact / $limit);
                
                if($page_number < 1)  $page_number = 1;
                $page_export = 0;
                $contact_export = [];
                for ($i = 0; $i < $page_number; $i++) {
                    $page_export ++;
                    $contact_export[] = $table->queryListContacts($params)->limit($limit)->page($page_export)->toArray();
                    if(empty($contact_export)) continue;
                }

                $result_export = [];
                if(!empty($contact_export)){
                    foreach ($contact_export as $export) {  
                        foreach ($export as $k => $contact) {
                            $result_export[] = $table->formatDataContactDetail($contact);
                        }
                    }
                }

                return $this->exportExcel(Hash::combine($result_export, '{n}.id', '{n}', '{n}.form_id'));
            }
        }

        try {
            $contacts = $this->paginate($table->queryListContacts($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        } catch (Exception $e) {
            $contacts = $this->paginate($table->queryListContacts($params), [
                'limit' => $limit,
                'page' => 1,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Contacts']) ? $this->request->getAttribute('paging')['Contacts'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);
        $result = [];
        if(!empty($contacts)){
            foreach ($contacts as $k => $contact) {
                $result[$k] = $table->formatDataContactDetail($contact);
                
                $list_fields = !empty($result[$k]['form']['fields']) ? $result[$k]['form']['fields'] : [];
                $list_fields = Hash::combine($list_fields, '{n}.code', '{n}.label');
                $content = [];
                if($list_fields) {
                    foreach($result[$k]['value'] as $label => $value){
                        if(!empty($list_fields[$label])){
                            $content[$list_fields[$label]] = strip_tags($value);
                        } else {
                            $content[$label] = strip_tags($value);
                        }
                    }
                }
                $result[$k]['content'] = $content;
            }
        }

        if(!empty($data['export'])) {
            return $this->exportExcel(Hash::combine($result, '{n}.id', '{n}', '{n}.form_id'));
        }
        
        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
            META => $meta_info
        ]);
    }

    public function changeStatus()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];
        $status = !empty($data['status']) ? intval($data['status']) : 0;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Contacts');

        $contacts = $table->find()->where([
            'id IN' => $ids,
            'deleted' => 0
        ])->select(['id', 'status'])->toArray();

        if(empty($contacts)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_lien_he')]);
        }

        $patch_data = [];
        foreach ($ids as $k => $contact_id) {
            $patch_data[] = [
                'id' => $contact_id,
                'status' => $status
            ];
        }
        
        $data_entities = $table->patchEntities($contacts, $patch_data);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $change_status = $table->saveMany($data_entities);            
            if (empty($change_status)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_trang_thai_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function detail($id = null)
    {
        $table = TableRegistry::get('Contacts');

        $contact = $table->find()->contain(['ContactsForm'])->where(['Contacts.id' => $id, 'Contacts.deleted' => 0])->first();
        if (empty($contact)) {
            $this->showErrorPage();
            return;
        }
        $contact_info = $table->formatDataContactDetail($contact);
        
        $fields = !empty($contact_info['form']['fields']) ? $contact_info['form']['fields'] : [];
        $fields = Hash::combine($fields, '{n}.code', '{n}.label');
        
        // update status
        $contact_entity = $table->patchEntity($contact, ['status' => 1]);
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();           
            
            $save = $table->save($contact_entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
        }catch (Exception $e) {
            $conn->rollback();
        }

        $this->set('contact', $contact_info);
        $this->set('fields', $fields);

        if($this->request->is('ajax')){
            $this->viewBuilder()->enableAutoLayout(false);
            $this->render('view_detail');
        }else{
            $this->css_page = ['/assets/css/pages/wizard/wizard-4.css'];
            $this->set('path_menu', 'contact');
            $this->set('title_for_layout', __d('admin', 'thong_tin_lien_he'));
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

        $table = TableRegistry::get('Contacts');

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach($ids as $id){

                // delete contact
                $contact = $table->get($id);
                if (empty($contact)) {
                    throw new Exception(__d('admin', 'khong_tim_thay_thong_tin_lien_he'));
                }

                $contact = $table->patchEntity($contact, ['id' => $id, 'deleted' => 1], ['validate' => false]);
                $delete = $table->save($contact);
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

    private function exportExcel($data)
    {
        if(empty($data)) return [];

        $spreadsheet = new Spreadsheet();

        foreach(range('A','M') as $columnID) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }

        $arr_alphabet = [];
        foreach(range('A','Z') as $alphabet){
            $arr_alphabet[] = $alphabet;
        }
        $k_export = 0;
        foreach ($data as $export) {
            $k_export ++;
            $sheet_name = "sheet_$k_export";

            if($k_export == 1){
                $$sheet_name = $spreadsheet->getActiveSheet();
                $spreadsheet->getActiveSheet()->setTitle(array_values($export)['0']['form']['name']);
            } else {
                $$sheet_name = $spreadsheet->createSheet();
                $spreadsheet->setActiveSheetIndex($k_export - 1);
                $spreadsheet->getActiveSheet()->setTitle(array_values($export)['0']['form']['name']);
                foreach(range('A','M') as $columnID) {
                    $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
                }
            }
            if(!empty(array_values($export)['0']['form']['fields'])) {
                foreach(array_values($export)['0']['form']['fields'] as $k_field => $field) {
                    $$sheet_name->setCellValue($arr_alphabet[$k_field].'1', $field['label']);              
                }
            }

            $count = 2;
            if(!empty($export)){
                foreach ($export as $cellValue) {
                    foreach (array_values($cellValue['value']) as $k_cell => $cell) {
                        $$sheet_name->setCellValue($arr_alphabet[$k_cell] . $count, $cell);
                    }
                    
                    $count ++;
                }
            }
            
        }
        
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
                'name' => 'thong_tin_lien_he_'. time()
            ]
        ]);

    }

}