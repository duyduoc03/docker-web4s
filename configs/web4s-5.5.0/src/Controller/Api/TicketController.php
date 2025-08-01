<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Exception\Exception;
use Cake\Utility\Hash;
use Firebase\JWT\JWT;
use UnexpectedValueException;

class TicketController extends AppController {

    public function initialize(): void
    {
        parent::initialize();

        $result = $this->loadComponent('Web4s')->validateAuthCrm();
        if ($result[CODE] != SUCCESS) {
            $this->responseErrorApi($result);
        }
    }

    public function reply()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->request->input('json_decode', true);
        if (empty($data)) {
            $this->responseApi([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Tickets');
        $utilities = $this->loadComponent('Utilities');

        $crm_id = !empty($data['crm_id']) ? intval($data['crm_id']) : null;
        $crm_parent_id = !empty($data['crm_parent_id']) ? intval($data['crm_parent_id']) : null;

        // check thông tin ticket có crm_id là $crm_parent_id có tồn tại hay không
        $ticket_info = $table->find()->where([
            'crm_id' => $crm_parent_id,
            'deleted' => 0
        ])->first();

        if (empty($ticket_info)) {
            $this->responseApi([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_ticket')]);
        }

        $full_name = !empty($data['full_name']) ? $data['full_name'] : null;
        $code = !empty($data['code']) ? $data['code'] : null;
        $title = !empty($data['title']) ? $data['title'] : null;
        $department = !empty($data['department']) ? $data['department'] : null;
        $priority = !empty($data['priority']) ? $data['priority'] : null;
        $content = !empty($data['content']) ? $data['content'] : null;
        $files = !empty($data['files']) ? $data['files'] : null;
        $status = !empty($data['status']) ? $data['status'] : null;

        if (empty($status)) {
            $full_name = !empty($ticket_info['full_name']) ? $ticket_info['full_name'] : null;
            $status = !empty($ticket_info['status']) ? $ticket_info['status'] : null;
        }
        
        $data_save = [
            'parent_id' => !empty($ticket_info['id']) ? intval($ticket_info['id']) : null,

            'full_name' => $full_name,
            'crm_id' => $crm_id,
            'code' => $code,
            'title' => $title,
            'department' => $department,
            'priority' => $priority,

            'content' => $content,
            'files' => $files,
            'status' => $status,
            'search_unicode' => strtolower($this->Utilities->formatSearchUnicode([$full_name, $title])),

            'crm_staff_name' => !empty($data['staff_name']) ? $data['staff_name'] : null
        ];

        $ticket = $table->patchEntity($ticket_info, ['status' => $status]);
        $new_ticket = $table->newEntity($data_save);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = $table->save($ticket);
            if (empty($save->id)){
                throw new Exception();
            }

            $save_new = $table->save($new_ticket);
            if (empty($save_new->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseApi([
                CODE => SUCCESS,
                MESSAGE => __d('template', 'cap_nhat_thanh_cong')
            ]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseApi([MESSAGE => $e->getMessage()]);  
        }
    }
}