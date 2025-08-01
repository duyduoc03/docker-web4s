<?php

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Hash;
use Cake\Core\Exception\Exception;
use Cake\Cache\Cache;

class WheelController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
    }

    public function spinning($wheel_id = null) 
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if (!$this->getRequest()->is('post') || empty($wheel_id)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $full_name = !empty($data['full_name']) ? trim($data['full_name']) : null;
        $phone = !empty($data['phone']) ? trim($data['phone']) : null;
        $email = !empty($data['email']) ? trim($data['email']) : null;
        

        $table = TableRegistry::get('WheelFortune');
        $table_option = TableRegistry::get('WheelOptions');
        $table_log = TableRegistry::get('WheelFortuneLog');

        $wheel_info = $table->getDetailWheelFortune($wheel_id, LANGUAGE);
        $wheel_info = $table->formatDataWheelFortune($wheel_info, LANGUAGE);
        if(empty($wheel_info['status'])) $this->responseJson([MESSAGE => __d('template', 'vong_quay_da_ket_thuc')]);

        $winning_chance = !empty($wheel_info['winning_chance']) ? intval($wheel_info['winning_chance']) : 0;
        $check_limit = !empty($wheel_info['check_limit']) ? 1 : 0;
        $start_time = !empty($wheel_info['start_time']) ? intval($wheel_info['start_time']) : null;
        $end_time = !empty($wheel_info['end_time']) ? intval($wheel_info['end_time']) : null;
        $options = !empty($wheel_info['options']) ? $wheel_info['options'] : [];
        $check_ip = !empty($wheel_info['check_ip']) ? 1 : 0;

        $receive = !empty($wheel_info['config_email']['receive']) ? 1 : 0;
        $title = !empty($wheel_info['config_email']['title']) ? $wheel_info['config_email']['title'] : null;
        $email_config = !empty($wheel_info['config_email']['email']) ? $wheel_info['config_email']['email'] : null;

        $show_contact = !empty($wheel_info['config_behavior']['show_contact']) ? intval($wheel_info['config_behavior']['show_contact']) : 0;
        $play_limit = !empty($wheel_info['config_behavior']['play_limit']) ? intval($wheel_info['config_behavior']['play_limit']) : 0;
        $retries = !empty($wheel_info['config_behavior']['retries']) ? 1 : 0;
        $occurance_after = !empty($wheel_info['config_behavior']['occurance_after']) ? 1 : 0;
        $occurance_after_delay = !empty($wheel_info['config_behavior']['occurance_after_delay']) ? intval($wheel_info['config_behavior']['occurance_after_delay']) : 0;

        if(!empty($end_time) && $end_time < time()) $this->responseJson([MESSAGE => __d('template', 'vong_quay_da_ket_thuc')]);
        if(empty($options)) $this->responseJson([MESSAGE => __d('template', 'vong_quay_chua_co_giai_thuong')]);

        $my_ip = $this->request->clientIp();

        // kiem tra cau hinh cho phep choi lan tiep theo 
        if($retries && $occurance_after) {
            $count_play = $table_log->find()->where([
                'wheel_id' => $wheel_id,
                'ip' => $my_ip,
                'created >=' => strtotime(date("d-m-Y")),
                'lang' => LANGUAGE
            ])->select(['id'])->count();

            if($count_play > 0) $this->responseJson([MESSAGE => __d('template', 'ban_da_het_luot_choi_ngay_hom_nay_vui_long_thu_lai_sau_{0}_ngay_nua', [$occurance_after_delay])]);
        }

        if($show_contact) {
            if(empty($full_name)) $this->responseJson([MESSAGE => __d('template', 'vui_long_cap_nhat_ho_va_ten')]);
            if(empty($phone)) $this->responseJson([MESSAGE => __d('template', 'vui_long_cap_nhat_so_dien_thoai')]);
            if(empty($email)) $this->responseJson([MESSAGE => __d('template', 'vui_long_cap_nhat_email')]);

            // kiem tra email KH tham gia quay thuong
            $count_email = $table_log->find()->where(['email' => $email, 'lang' => LANGUAGE])->count();
            if($count_email >= $play_limit && $play_limit > 0) $this->responseJson([MESSAGE => __d('template', 'ban_da_het_luot_choi')]);

            $count_phone = $table_log->find()->where(['phone' => $phone, 'lang' => LANGUAGE])->count();
            if($count_phone >= $play_limit && $play_limit > 0) $this->responseJson([MESSAGE => __d('template', 'ban_da_het_luot_choi')]);

        }elseif(!$show_contact && !empty($check_ip)) {

            $count_ip = $table_log->find()->where([
                'wheel_id' => $wheel_id,
                'ip' => $my_ip,
                'created >=' => strtotime(date("d-m-Y")),
                'lang' => LANGUAGE
             ])->select(['id'])->count();

            if($count_ip > $play_limit && $play_limit > 0) $this->responseJson([MESSAGE => __d('template', 'ban_da_het_luot_choi')]);
        }

        // random giải thưởng
        $random_prize = $this->randomPrize($winning_chance, $check_limit, $options);

        $winning = !empty($random_prize['winning']) ? intval($random_prize['winning']) : 0;
        $index_prize = !empty($random_prize['index_prize']) ? intval($random_prize['index_prize']) : null;
        $total_deg = !empty($random_prize['total_deg']) ? intval($random_prize['total_deg']) : null;

        $option_id = !empty($options[$index_prize]['id']) ? intval($options[$index_prize]['id']) : null;
        $option_info = $table_option->find()->where(['id' => $option_id, 'wheel_id' => $wheel_id])->first();
        if(empty($option_info)) $this->responseJson([MESSAGE => __d('template', 'khong_tim_thay_thong_tin_giai_thuong')]);

        // lưu số lần quay mỗi giải 
        $winning_option = !empty($option_info['winning']) ? intval($option_info['winning']) + 1 : 1;
        $data_save_option = [
            'winning' => $winning_option
        ];  

        // TH chưa nhập thông tin trả về giải thưởng trước
        if(!$show_contact) {
            // luu cache index giai thuong
            Cache::write('prize', ['index_prize' => $index_prize, 'winning' => $winning]);
            $this->responseJson([
                CODE => SUCCESS,
                DATA => [
                    'total_deg' => $total_deg,
                    'prize' => $options[$index_prize],
                    'index_prize' => $index_prize,
                    'show_contact' => 1
                ]
            ]);
        }

        $data_save = [
            'wheel_id' => $wheel_id,
            'full_name' => $full_name,
            'phone' => $phone,
            'email' => $email,
            'ip' => $my_ip,
            'winning' => $winning,
            'prize_name' => !empty($options[$index_prize]['prize_name']) ? $options[$index_prize]['prize_name'] : null,
            'prize_value' => !empty($options[$index_prize]['prize_value']) ? $options[$index_prize]['prize_value'] : null,
            'type' => $show_contact,
            'created' => time(),
            'lang' => LANGUAGE
        ];

        $entity = $table_log->newEntity($data_save);
        $entity_option = $table_option->patchEntity($option_info, $data_save_option);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
                        
            $save = $table_log->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $save_option = $table_option->save($entity_option);
            if (empty($save_option->id)){
                throw new Exception();
            }
            
            $conn->commit();

            // xoa cache quay thuong
            Cache::delete('prize');

            if($receive) {
                $params_email = [
                    'to_email' => $email_config,
                    'code' => 'CONTACT_LUCKY_SPIN',
                    'id_record' => $save->id
                ];
                $result = $this->loadComponent('Email')->send($params_email);
            }

            $this->responseJson([
                CODE => SUCCESS,
                DATA => [
                    'total_deg' => $total_deg,
                    'prize' => $save
                ]
            ]);
        }catch (Exception $e) {
            $conn->rollback();

            $message = !empty($e->getMessage()) ? $e->getMessage() : __d('template', 'loi_xu_ly_du_lieu');
            $this->responseJson([MESSAGE => $message]);
        }
    }

    public function saveContact($wheel_id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if (!$this->getRequest()->is('post') || empty($wheel_id) || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $full_name = !empty($data['full_name']) ? trim($data['full_name']) : null;
        $phone = !empty($data['phone']) ? trim($data['phone']) : null;
        $email = !empty($data['email']) ? trim($data['email']) : null;
        $prize = !empty($data['prize']) ? intval($data['prize']) : 'no_prize';

        if(empty($full_name)) $this->responseJson([MESSAGE => __d('template', 'vui_long_cap_nhat_ho_va_ten')]);
        if(empty($phone)) $this->responseJson([MESSAGE => __d('template', 'vui_long_cap_nhat_so_dien_thoai')]);
        if(empty($email)) $this->responseJson([MESSAGE => __d('template', 'vui_long_cap_nhat_email')]);
        if($prize == 'no_prize') $this->responseJson([MESSAGE => __d('template', 'khong_tim_thay_thong_tin_giai_thuong')]);
        

        $table = TableRegistry::get('WheelFortune');
        $table_option = TableRegistry::get('WheelOptions');
        $table_log = TableRegistry::get('WheelFortuneLog');

        $wheel_info = $table->getDetailWheelFortune($wheel_id, LANGUAGE);
        $wheel_info = $table->formatDataWheelFortune($wheel_info, LANGUAGE);
        if(empty($wheel_info['status'])) $this->responseJson([MESSAGE => __d('template', 'vong_quay_da_ket_thuc')]);

        $start_time = !empty($wheel_info['start_time']) ? intval($wheel_info['start_time']) : null;
        $end_time = !empty($wheel_info['end_time']) ? intval($wheel_info['end_time']) : null;
        $options = !empty($wheel_info['options']) ? $wheel_info['options'] : [];

        $receive = !empty($wheel_info['config_email']['receive']) ? 1 : 0;
        $title = !empty($wheel_info['config_email']['title']) ? $wheel_info['config_email']['title'] : null;
        $email_config = !empty($wheel_info['config_email']['email']) ? $wheel_info['config_email']['email'] : null;

        if(!empty($end_time) && $end_time < time()) $this->responseJson([MESSAGE => __d('template', 'vong_quay_da_ket_thuc')]);
        if(empty($options)) $this->responseJson([MESSAGE => __d('template', 'vong_quay_chua_co_giai_thuong')]);

        $total_prize = count($options);

        $my_ip = $this->request->clientIp();

        $cache_prize = Cache::read('prize');
        $index_prize = !empty($cache_prize['index_prize']) ? intval($cache_prize['index_prize']) : null;
        $winning = !empty($cache_prize['winning']) ? intval($cache_prize['winning']) : 1;

        $option_id = !empty($options[$index_prize]['id']) ? intval($options[$index_prize]['id']) : null;
        $option_info = $table_option->find()->where(['id' => $option_id, 'wheel_id' => $wheel_id])->first();
        if(empty($option_info)) $this->responseJson([MESSAGE => __d('template', 'khong_tim_thay_thong_tin_giai_thuong')]);

        if($index_prize > $total_prize || $index_prize !== $prize) {
            $this->responseJson([MESSAGE => __d('template', 'khong_tim_thay_thong_tin_giai_thuong')]);
        }

        // lưu số lần quay mỗi giải 
        $winning_option = !empty($option_info['winning']) ? intval($option_info['winning']) + 1 : 1;
        $data_save_option = [
            'winning' => $winning_option
        ];

        $data_save = [
            'wheel_id' => $wheel_id,
            'full_name' => $full_name,
            'phone' => $phone,
            'email' => $email,
            'ip' => $my_ip,
            'winning' => $winning,
            'prize_name' => !empty($options[$index_prize]['prize_name']) ? $options[$index_prize]['prize_name'] : null,
            'prize_value' => !empty($options[$index_prize]['prize_value']) ? $options[$index_prize]['prize_value'] : null,
            'type' => 1,
            'created' => time(),
            'lang' => LANGUAGE
        ];

        $entity = $table_log->newEntity($data_save);
        $entity_option = $table_option->patchEntity($option_info, $data_save_option);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
                        
            $save = $table_log->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $save_option = $table_option->save($entity_option);
            if (empty($save_option->id)){
                throw new Exception();
            }
            
            $conn->commit();

            // xoa cache quay thuong
            Cache::delete('prize');

            if($receive) {
                $params_email = [
                    'to_email' => $email_config,
                    'code' => 'CONTACT_LUCKY_SPIN',
                    'id_record' => $save->id
                ];
                $result = $this->loadComponent('Email')->send($params_email);
            }

            $this->responseJson([
                CODE => SUCCESS,
                MESSAGE => __d('template', 'gui_thong_tin_lien_he_thanh_cong')
            ]);
        }catch (Exception $e) {
            $conn->rollback();

            $message = !empty($e->getMessage()) ? $e->getMessage() : __d('template', 'loi_xu_ly_du_lieu');
            $this->responseJson([MESSAGE => $message]);
        }
    }

    private function randomPrize($winning_chance = 0, $check_limit = 0, $options = []) 
    {
        if(empty($options)) return [];

        $total_prize = count($options);
        $deg_angle = 360 / $total_prize;
        $percent_winning = 100 / $total_prize;
        $index_prize = null;

        foreach ($options as $key => $option) {
            $type_award = !empty($option['type_award']) ? $option['type_award'] : null;
            $winning = !empty($option['winning']) ? intval($option['winning']) : 0;
            $limit_prize = !empty($option['limit_prize']) ? intval($option['limit_prize']) : 0;

            if($type_award === 'nothing') $index_prize = $key;
            if(!empty($option['percent_winning'])) $percent_winning = $option['percent_winning'];
            else $percent_winning = 0;

            // kiem tra gioi han so luong qua tang => set lai ti le chien thang
            if($winning >= $limit_prize && $limit_prize > 0 && $check_limit) {
                $options[$key]['percent_winning'] = 0;
            }else{
                $options[$key]['percent_winning'] = $percent_winning;
            }
        }

        // Check cơ hội chiến thắng
        if($winning_chance === 0) $winning = false;
        else $winning = rand(0,100) <= $winning_chance;

        // Random số vòng quay ngẫu nhiên
        $number_random = mt_rand() / mt_getrandmax();
        $number_spin = floor($number_random * 4) + 2;

        // TH có cơ hội trúng thưởng
        if($winning) {

            // Tạo danh sách các chỉ số dựa trên xác suất
            $weighted_list = [];
            for ($x = 0; $x < $total_prize; $x++) {
                $percent = intval($options[$x]['percent_winning']);

                if(empty($percent) || $percent == 0) continue;

                for ($i = 0; $i < $percent; $i++) {
                    $weighted_list[] = $x;
                }
            }

            // Random giải thưởng
            $index_win = floor($number_random * count($weighted_list));
            $index_prize = $weighted_list[$index_win];
        }

        // Tính góc quay giai thuong
        $deg_prize = 360 - $index_prize * $deg_angle;
        $total_deg = floor($number_spin * 360 + $deg_prize);

        return [
            'winning' => !empty($winning) ? 1 : 0,
            'index_prize' => $index_prize,
            'total_deg' => $total_deg
        ];
    }

}