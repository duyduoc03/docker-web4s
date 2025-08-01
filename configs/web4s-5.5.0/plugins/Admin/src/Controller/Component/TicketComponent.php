<?php

namespace Admin\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;

class TicketComponent extends AppComponent
{
    public $controller = null;
    public $components = ['System', 'Utilities', 'Auth', 'Web4s'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }

    public function saveTicket($data = [], $id = null)
    {
        if(empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Tickets');

        if (!empty($id)) {
            // lấy thông tin ticket
            $ticket = $table->getDetailTicket($id, ['get_parent' => false]);
            if(empty($ticket)){
                return $this->System->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }
        }

        // data
        $full_name = !empty($data['full_name']) ? strip_tags(trim($data['full_name'])) : null;
        $email = !empty($data['email']) ? strip_tags(trim($data['email'])) : null;
        $phone = !empty($data['phone']) ? strip_tags(trim($data['phone'])) : null;
        $title = !empty($data['title']) ? $data['title'] : null;
        $department = !empty($data['department']) ? $data['department'] : SALE;
        $priority = !empty($data['priority']) ? $data['priority'] : LOW;

        if (!empty($ticket)) {
            $title = !empty($ticket['title']) ? $ticket['title'] : null;
            $department = !empty($ticket['department']) ? $ticket['department'] : null;
            $priority = !empty($ticket['priority']) ? $ticket['priority'] : null;
        }

        // validate data
        if(empty($full_name)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'vui_long_nhap_thong_tin_ho_va_ten')]);
        }

        if(empty($email)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'vui_long_nhap_thong_tin_email')]);
        }

        if(empty($title)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'vui_long_nhap_thong_tin_tieu_de_ho_tro')]);
        }
        
        $data_save = [
            'parent_id' => !empty($id) ? intval($id) : null,
            'crm_parent_id' => !empty($ticket['crm_id']) ? intval($ticket['crm_id']) : null,
            'full_name' => $full_name,
            'email' => $email,
            'phone' => $phone,

            'title' => $title,
            'department' => $department,
            'priority' => $priority,

            'content' => !empty($data['content']) ? $data['content'] : null,
            'files' => !empty($data['files']) ? $data['files'] : null,
            'search_unicode' => strtolower($this->Utilities->formatSearchUnicode([$full_name, $email, $phone, $title])),

            'created_by' => $this->Auth->user('id')
        ];
        
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // gửi thông tin ticket sang crm
            $send_crm = $this->apiSendTicketToCrm($data_save);
            
            if(!empty($send_crm[CODE]) && $send_crm[CODE] != SUCCESS){
                $message = !empty($send_crm[MESSAGE]) ? $send_crm[MESSAGE] : __d('admin', 'gui_thong_tin_ticket_khong_thanh_cong');

                return $this->System->getResponse([MESSAGE => $message]);
            }

            // lưu lại thông tin mã code ticket mà crm khởi tạo và thông tin staff name
            $data_save['crm_id'] = !empty($send_crm[DATA]['crm_id']) ? intval($send_crm[DATA]['crm_id']) : null;
            $data_save['code'] = !empty($send_crm[DATA]['code']) ? $send_crm[DATA]['code'] : null;
            $data_save['staff_name'] = !empty($send_crm[DATA]['staff_name']) ? $send_crm[DATA]['staff_name'] : null;
            $ticket = $table->newEntity($data_save);

            // save data
            $save = $table->save($ticket);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            return $this->System->getResponse([CODE => SUCCESS, DATA => $save]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }

    public function apiSendTicketToCrm($data = [])
    {
        return $this->Web4s->sendTicketToCrm($data);
    }
}
