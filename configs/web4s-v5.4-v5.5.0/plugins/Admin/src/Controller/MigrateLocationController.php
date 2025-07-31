<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Database\Schema\TableSchema;
use Cake\Database\Exception\DatabaseException;
use Cake\Http\Client;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Options;
use PHPHtmlParser\Exceptions\ContentLengthException;
use Zend\Diactoros\Stream;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Utility\Text;
use Cake\Log\Log;
use Cake\ORM\Locator\LocatorInterface;

class MigrateLocationController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function index()
    {
        // check exist table locations_changed
        $connection = ConnectionManager::get('default');
        $check = $connection->execute("SHOW TABLES LIKE 'locations_changed'")->fetchAll('assoc');
        if(empty($check)) die('Chưa khởi tạo bảng locations_changed');

        $check = $connection->execute("SHOW TABLES LIKE 'cities_migrate'")->fetchAll('assoc');
        if(empty($check)) die('Chưa khởi tạo bảng cities_migrate');

        $check = $connection->execute("SHOW TABLES LIKE 'districts_migrate'")->fetchAll('assoc');
        if(empty($check)) die('Chưa khởi tạo bảng districts_migrate');

        $check = $connection->execute("SHOW TABLES LIKE 'wards_migrate'")->fetchAll('assoc');
        if(empty($check)) die('Chưa khởi tạo bảng wards_migrate');

        $this->js_page = [
            '/assets/plugins/diff-match-patch/diff-match-patch.js',
            '/assets/js/pages/migrate_location.js?v=' . time()
        ];

        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', 'Đồng bộ dữ liệu tỉnh thành');
    }

    public function process()
    {   
        $this->js_page = [
            '/assets/js/pages/migrate_location_process.js?v=' . time()
        ];

        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', 'Chuyển đổi dữ liệu');
    }

    public function importData()
    {
        ini_set('memory_limit', '4048M');
        set_time_limit(0);

        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $excel_file = !empty($data['excel_file']) ? $data['excel_file'] : null;
        if (!$this->getRequest()->is('post') || empty($excel_file)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        if(!empty($excel_file->getError())){
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $file_tmp = $excel_file->getStream()->getMetadata('uri');
        $file_type = $excel_file->getClientMediaType();

        if(!empty($file_error) || empty($file_tmp) || empty($file_type)){
            $this->responseJson([MESSAGE => __d('template', 'tep_khoi_tao_du_lieu_ban_dau_khong_hop_le')]);
        }

        $file = \PhpOffice\PhpSpreadsheet\IOFactory::identify($file_tmp);
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($file);
        $spreadsheet = $reader->load($file_tmp);
 
        $data_excel = $spreadsheet->getActiveSheet()->toArray();

        if (empty($data_excel) || count($data_excel) < 2) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $data_excel = array_filter(array_map('array_filter', $data_excel));
        
        $fields = [
            'Tỉnh Thành Phố' => 'city_name',
            'Mã TP' => 'city_code',
            'Quận Huyện' => 'district_name',
            'Mã QH' => 'district_code',
            'Phường Xã' => 'ward_name',
            'Mã PX' => 'ward_code'
        ];

        if(empty($fields)) $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);

        $headers = !empty($data_excel[0]) ? $data_excel[0] : [];
        if(empty($headers)) $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);

        $columns = [];
        foreach($headers as $key => $title){
            if(empty($fields[$title]) || in_array($fields[$title], $columns)) continue;

            $columns[$key] = $fields[$title];
        }

        if(empty($columns)) $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);

        // xóa header
        unset($data_excel[0]);

        $cities_table = TableRegistry::get('CitiesMigrate');
        $districts_table = TableRegistry::get('DistrictsMigrate');
        $wards_table = TableRegistry::get('WardsMigrate');

        $data_result = [];
        foreach($data_excel as $row) {
            if(empty($row)) continue;

            $item = [];
            foreach($row as $key => $val){
                if(empty($columns[$key])) continue;
                $item[$columns[$key]] = $val;
            }

            if(empty($item)) continue;

            $city_name = !empty($item['city_name']) ? $item['city_name'] : '';
            $city_code = !empty($item['city_code']) ? $item['city_code'] : '';

            $district_name = !empty($item['district_name']) ? $item['district_name'] : '';
            $district_code = !empty($item['district_code']) ? $item['district_code'] : '';

            $ward_name = !empty($item['ward_name']) ? $item['ward_name'] : '';
            $ward_code = !empty($item['ward_code']) ? $item['ward_code'] : '';

            if(
                empty($city_name) || empty($city_code) || 
                empty($district_name) || empty($district_code) ||
                empty($ward_name) || empty($ward_code)
            ) continue;


            // thêm tỉnh thành
            $city_info = $cities_table->find()->where(['code' => $city_code])->select(['id'])->first();
            if(empty($city_info)){
                $city_type = 1; // Tỉnh
                if(strpos($city_name, 'Tỉnh') !== false) $city_type = 1;
                if(strpos($city_name, 'Thành phố') !== false) $city_type = 2;

                $city_name = trim(str_replace(['Thành phố', 'Tỉnh'], '', $city_name));

                $entity = $cities_table->newEntity([
                    'name' => $city_name,
                    'code' => $city_code,
                    'type' => $city_type
                ]);

                $city_created = $cities_table->save($entity);
            }

            // thêm tỉnh thành
            $district_info = $districts_table->find()->where(['code' => $district_code])->select(['id'])->first();
            if(empty($district_info)){
                $district_type = '';
                if(strpos($district_name, 'Quận') !== false) $district_type = 1;
                if(strpos($district_name, 'Huyện') !== false) $district_type = 2;
                if(strpos($district_name, 'Thành phố') !== false) $district_type = 3;
                if(strpos($district_name, 'Thành Phố') !== false) $district_type = 3;
                if(strpos($district_name, 'Thị xã') !== false) $district_type = 4;
                if(strpos($district_name, 'Thị Xã') !== false) $district_type = 4;

                $entity = $districts_table->newEntity([
                    'name' => $district_name,
                    'code' => $district_code,
                    'type' => $district_type,
                    'city_code' => $city_code
                ]);

                $district_created = $districts_table->save($entity);
            }

            // thêm phường xã
            $ward_info = $wards_table->find()->where(['code' => $ward_code])->select(['id'])->first();
            if(empty($ward_info)){
                $ward_type = '';
                if(strpos($ward_name, 'Phường') !== false) $ward_type = 1;
                if(strpos($ward_name, 'Xã') !== false) $ward_type = 2;
                if(strpos($ward_name, 'Thị trấn') !== false) $ward_type = 3;
                if(strpos($ward_name, 'Thị Trấn') !== false) $ward_type = 3;

                $entity = $wards_table->newEntity([
                    'name' => $ward_name,
                    'code' => $ward_code,
                    'type' => $ward_type,
                    'district_code' => $district_code
                ]);

                $ward_created = $wards_table->save($entity);
            }
        }

        exit(json_encode([
            CODE => SUCCESS
        ]));  
    }

    public function loadCities()
    {
        $this->viewBuilder()->enableAutoLayout(false);        

        // tỉnh thành hệ thống       
        $system_cities = TableRegistry::get('Cities')->find()->where([
            'status' => 1,
            'deleted' => 0,
            'country_id' => 1
        ])->select([
            'Cities.id', 
            'Cities.name',
            'Cities.type',
        ])->order('Cities.id ASC')->toArray();
        if(!empty($system_cities)){
            foreach($system_cities as $k => $city_info){
                $name = !empty($city_info['name']) ? $city_info['name'] : '';
                $type = isset($city_info['type']) ? $city_info['type'] : '';
                if(empty($name)) continue;

                $extend = '';
                if($type == 1) $extend = 'Tỉnh';
                if($type == 2) $extend = 'Thành phố';
                
                $city_info['type'] = $type;
                $city_info['extend'] = $extend;
                $city_info['name'] = trim(str_replace(['Tỉnh', 'Thành phố', 'Thành Phố'], '', $name));

                unset($city_info['CitiesChanged']);
                $system_cities[$k] = $city_info->toArray();
            }
        }
        
        if(empty($system_cities)){
            die('Không lấy được danh sách tỉnh thành');
        }

        // tỉnh thành migrate
        $migrate_cities = TableRegistry::get('CitiesMigrate')->find()->select([
            'id', 
            'name',
            'code',
            'type'
        ])->toArray();
        if(!empty($migrate_cities)){
            foreach($migrate_cities as $k => $city_info){
                $name = !empty($city_info['name']) ? $city_info['name'] : '';
                $type = !empty($city_info['type']) ? $city_info['type'] : '';
                if(empty($name)) continue;

                $extend = '';
                if($type == 1) $extend = 'Tỉnh';
                if($type == 2) $extend = 'Thành phố';
                
                $city_info['type'] = $type;
                $city_info['extend'] = $extend;
                $city_info['name'] = trim(str_replace(['Tỉnh', 'Thành phố', 'Thành Phố'], '', $name));

                $migrate_cities[$k] = $city_info->toArray();
            }
        }
        $migrate_cities = !empty($migrate_cities) ? Hash::combine($migrate_cities, '{n}.name', '{n}') : [];

        // so sánh dữ liệu
        $cities = [];
        foreach($system_cities as $city_info){
            $name = !empty($city_info['name']) ? $city_info['name'] : '';
            if(empty($name)) continue;

            $migrate_city = !empty($migrate_cities[$name]) ? $migrate_cities[$name] : [];

            $city_info['migrate_id'] = !empty($migrate_city['id']) ? $migrate_city['id'] : '';
            $city_info['migrate_code'] = !empty($migrate_city['code']) ? $migrate_city['code'] : '';
            $city_info['migrate_name'] = !empty($migrate_city['name']) ? $migrate_city['name'] : '';
            $city_info['migrate_type'] = !empty($migrate_city['type']) ? $migrate_city['type'] : '';
            $city_info['migrate_extend'] = !empty($migrate_city['extend']) ? $migrate_city['extend'] : '';            

            $cities[] = $city_info;

            // xóa item $migrate_cities
            unset($migrate_cities[$name]);
        }

        //nếu còn item $migrate_cities thì thêm vào
        if(!empty($migrate_cities)){
            foreach($migrate_cities as $migrate_city){

                $cities[] = [
                    'id' => '',
                    'name' => '',
                    'position' => '',
                    'type' => '',
                    'migrate_id' => !empty($migrate_city['id']) ? $migrate_city['id'] : '',
                    'migrate_code' => !empty($migrate_city['code']) ? $migrate_city['code'] : '',
                    'migrate_name' => !empty($migrate_city['name']) ? $migrate_city['name'] : '',
                    'migrate_type'=> !empty($migrate_city['type']) ? $migrate_city['type'] : '',
                    'migrate_extend' => !empty($migrate_city['extend']) ? $migrate_city['extend'] : ''
                ];
            }
        }
        
        $this->set('cities', $cities);
        $this->render('list_cities');
    }

    public function loadDistricts()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        $data = $this->request->getData();

        $city_id = !empty($data['city_id']) ? intval($data['city_id']) : null;
        $migrate_city_id = !empty($data['migrate_city_id']) ? $data['migrate_city_id'] : null;
        if(empty($city_id) && empty($migrate_city_id)){
            die('Tham số không hợp lệ');
        }

        // quận huyện hệ thống
        $system_districts = [];
        if(!empty($city_id)){
            $system_districts = TableRegistry::get('Districts')->find()->where([
                'status' => 1,
                'deleted' => 0,
                'city_id' => $city_id
            ])->select([
                'Districts.id', 
                'Districts.name',
                'Districts.type',
                'Districts.position',
            ])->order('Districts.id ASC')->toArray();
        }
        
        if(!empty($system_districts)){
            foreach($system_districts as $k => $district_info){
                $name = !empty($district_info['name']) ? $district_info['name'] : '';
                if(empty($name)) continue;

                $type = !empty($district_info['type']) ? $district_info['type'] : null;
                if(strpos($name, 'Quận') !== false) $type = 1;
                if(strpos($name, 'Huyện') !== false) $type = 2;
                if(strpos($name, 'Thành phố') !== false) $type = 3;
                if(strpos($name, 'Thành Phố') !== false) $type = 3;
                if(strpos($name, 'Thị xã') !== false) $type = 4;
                if(strpos($name, 'Thị Xã') !== false) $type = 4;

                $extend = '';
                if($type == 1) $extend = 'Quận';
                if($type == 2) $extend = 'Huyện';
                if($type == 3) $extend = 'Thành phố';
                if($type == 4) $extend = 'Thị xã';

                $district_info['type'] = $type;
                $district_info['extend'] = $extend;
                $district_info['name'] = trim(str_replace(['Quận', 'Huyện', 'Thành phố', 'Thành Phố', 'Thị xã', 'Thị Xã'], '', $name));

                $system_districts[$k] = $district_info->toArray();
            }
        }

        // if(empty($system_districts)) die('Không lấy được danh sách quận huyện');


        // quận huyện migrate 
        $migrate_districts = [];
        if(!empty($migrate_city_id)){
            $migrate_city_info = TableRegistry::get('CitiesMigrate')->find()->where([
                'id' => $migrate_city_id]
            )->select(['id', 'code'])->first();

            $migrate_city_code = !empty($migrate_city_info['code']) ? $migrate_city_info['code'] : '';

            if(!empty($migrate_city_code)){
                $migrate_districts = TableRegistry::get('DistrictsMigrate')->find()->where([
                    'city_code' => $migrate_city_code
                ])->select([
                    'id', 
                    'name',
                    'code',
                    'type',
                    'city_code'
                ])->toArray();
            }
        }

        if(!empty($migrate_districts)){
            foreach($migrate_districts as $k => $district_info){
                $name = !empty($district_info['name']) ? $district_info['name'] : '';
                if(empty($name)) continue;

                $type = !empty($district_info['type']) ? intval($district_info['type']) : null;
                if(strpos($name, 'Quận') !== false) $type = 1;
                if(strpos($name, 'Huyện') !== false) $type = 2;
                if(strpos($name, 'Thành phố') !== false) $type = 3;
                if(strpos($name, 'Thành Phố') !== false) $type = 3;
                if(strpos($name, 'Thị xã') !== false) $type = 4;
                if(strpos($name, 'Thị Xã') !== false) $type = 4;

                $extend = '';
                if($type == 1) $extend = 'Quận';
                if($type == 2) $extend = 'Huyện';
                if($type == 3) $extend = 'Thành phố';
                if($type == 4) $extend = 'Thị xã';

                $district_info['type'] = $type;
                $district_info['extend'] = $extend;
                $district_info['name'] = trim(str_replace(['Quận', 'Huyện', 'Thành phố', 'Thành Phố', 'Thị xã', 'Thị Xã'], '', $name));

                $migrate_districts[$k] = $district_info->toArray();
            }

            $migrate_districts = !empty($migrate_districts) ? Hash::combine($migrate_districts, '{n}.name', '{n}') : [];
        }
 
        // so sánh dữ liệu
        $districts = [];
        foreach($system_districts as $district_info){
            $name = !empty($district_info['name']) ? $district_info['name'] : '';
            if(empty($name)) continue;

            $migrate_district = !empty($migrate_districts[$name]) ? $migrate_districts[$name] : [];

            $district_info['migrate_id'] = !empty($migrate_district['id']) ? $migrate_district['id'] : '';
            $district_info['migrate_code'] = !empty($migrate_district['code']) ? $migrate_district['code'] : '';
            $district_info['migrate_name'] = !empty($migrate_district['name']) ? $migrate_district['name'] : '';
            $district_info['migrate_type'] = !empty($migrate_district['type']) ? $migrate_district['type'] : '';
            $district_info['migrate_extend'] = !empty($migrate_district['extend']) ? $migrate_district['extend'] : '';

            $districts[] = $district_info;

            // xóa item $migrate_districts 
            unset($migrate_districts[$name]);
        }

        //nếu còn item $migrate_districts thì thêm vào
        if(!empty($migrate_districts)){
            foreach($migrate_districts as $migrate_district){

                $districts[] = [
                    'id' => '',
                    'name' => '',
                    'position' => '',
                    'type' => '',
                    'migrate_id' => !empty($migrate_district['id']) ? $migrate_district['id'] : '',
                    'migrate_code' => !empty($migrate_district['code']) ? $migrate_district['code'] : '',
                    'migrate_name' => !empty($migrate_district['name']) ? $migrate_district['name'] : '',
                    'migrate_type'=> !empty($migrate_district['type']) ? $migrate_district['type'] : '',
                    'migrate_extend' => !empty($migrate_district['extend']) ? $migrate_district['extend'] : ''
                ];
            }
        }
        
        $this->set('city_id', $city_id);
        $this->set('migrate_city_id', $migrate_city_id);
        $this->set('districts', $districts);        
        $this->render('list_districts');
    }

    public function loadWards()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        $data = $this->request->getData();

        $district_id = !empty($data['district_id']) ? intval($data['district_id']) : null;
        $migrate_district_id = !empty($data['migrate_district_id']) ? $data['migrate_district_id'] : null;
        if(empty($district_id) && empty($migrate_district_id)){
            die('Tham số không hợp lệ');
        }

        // phường xã hệ thống
        if(!empty($district_id)){
            $system_wards = TableRegistry::get('Wards')->find()->where([
                'status' => 1,
                'deleted' => 0,
                'district_id' => $district_id
            ])->select([
                'Wards.id', 
                'Wards.name',
                'Wards.type',
                'Wards.position',
            ])->order('Wards.id ASC')->toArray();
        }
        
        if(!empty($system_wards)){
            foreach($system_wards as $k => $ward_info){
                $name = !empty($ward_info['name']) ? $ward_info['name'] : '';
                if(empty($name)) continue;

                $type = !empty($ward_info['type']) ? $ward_info['type'] : null;

                if(strpos($name, 'Phường') !== false) $type = 1;
                if(strpos($name, 'Xã') !== false) $type = 2;
                if(strpos($name, 'Thị trấn') !== false) $type = 3;
                if(strpos($name, 'Thị Trấn') !== false) $type = 3;

                $extend = '';
                if($type == 1) $extend = 'Phường';
                if($type == 2) $extend = 'Xã';
                if($type == 3) $extend = 'Thị trấn';

                $ward_info['type'] = $type;
                $ward_info['extend'] = $extend;
                $ward_info['name'] = trim(str_replace(['Phường', 'Xã', 'Thị trấn', 'Thị Trấn'], '', $name));

                $system_wards[$k] = $ward_info->toArray();
            }
        }

        // if(empty($system_wards)) die('Không lấy được danh sách phường xã');

        // quận huyện migrate
        if(!empty($migrate_district_id)){
            $migrate_district_info = TableRegistry::get('DistrictsMigrate')->find()->where([
                'id' => $migrate_district_id]
            )->select(['id', 'code'])->first();
            $migrate_district_code = !empty($migrate_district_info['code']) ? $migrate_district_info['code'] : '';
        }        

        if(!empty($migrate_district_code)){
            $migrate_wards = TableRegistry::get('WardsMigrate')->find()->where([
                'district_code' => $migrate_district_code
            ])->select([
                'id', 
                'name',
                'code',
                'type',
                'district_code'
            ])->toArray();
        }
        
        
        if(!empty($migrate_wards)){
            foreach($migrate_wards as $k => $ward_info){
                $name = !empty($ward_info['name']) ? $ward_info['name'] : '';
                if(empty($name)) continue;

                $type = !empty($ward_info['type']) ? $ward_info['type'] : null;
                if(strpos($name, 'Phường') !== false) $type = 1;
                if(strpos($name, 'Xã') !== false) $type = 2;
                if(strpos($name, 'Thị trấn') !== false) $type = 3;
                if(strpos($name, 'Thị Trấn') !== false) $type = 3;

                $extend = '';
                if($type == 1) $extend = 'Phường';
                if($type == 2) $extend = 'Xã';
                if($type == 3) $extend = 'Thị trấn';

                $ward_info['type'] = $type;
                $ward_info['extend'] = $extend;
                $ward_info['name'] = trim(str_replace(['Phường', 'Xã', 'Thị trấn', 'Thị Trấn'], '', $name));

                $migrate_wards[$k] = $ward_info->toArray();
            }
        }

        $migrate_wards = !empty($migrate_wards) ? Hash::combine($migrate_wards, '{n}.name', '{n}') : [];
 
        // so sánh dữ liệu
        $wards = [];
        if(!empty($system_wards)){
            foreach($system_wards as $ward_info){
                $name = !empty($ward_info['name']) ? $ward_info['name'] : '';
                if(empty($name)) continue;
                
                $migrate_ward = !empty($migrate_wards[$name]) ? $migrate_wards[$name] : [];

                $ward_info['migrate_id'] = !empty($migrate_ward['id']) ? $migrate_ward['id'] : '';
                $ward_info['migrate_code'] = !empty($migrate_ward['code']) ? $migrate_ward['code'] : '';
                $ward_info['migrate_name'] = !empty($migrate_ward['name']) ? $migrate_ward['name'] : '';
                $ward_info['migrate_type'] = !empty($migrate_ward['type']) ? $migrate_ward['type'] : '';
                $ward_info['migrate_extend'] = !empty($migrate_ward['extend']) ? $migrate_ward['extend'] : '';

                $wards[] = $ward_info;

                // xóa item $migrate_wards 
                unset($migrate_wards[$name]);
            }
        }        

        //nếu còn item $migrate_districts thì thêm vào
        if(!empty($migrate_wards)){
            foreach($migrate_wards as $migrate_ward){
                $wards[] = [
                    'id' => '',
                    'name' => '',
                    'position' => '',
                    'type' => '',
                    'migrate_id' => !empty($migrate_ward['id']) ? $migrate_ward['id'] : '',
                    'migrate_code' => !empty($migrate_ward['code']) ? $migrate_ward['code'] : '',
                    'migrate_name' => !empty($migrate_ward['name']) ? $migrate_ward['name'] : '',
                    'migrate_type'=> !empty($migrate_ward['type']) ? $migrate_ward['type'] : '',
                    'migrate_extend' => !empty($migrate_ward['extend']) ? $migrate_ward['extend'] : ''
                ];
            }
        }
        
        $this->set('district_id', $district_id);
        $this->set('migrate_district_id', $migrate_district_id);
        $this->set('wards', $wards);
        $this->render('list_wards');
    }

    public function loadContentMigrateModal()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        $data = $this->request->getData();

        $object = !empty($data['object']) ? $data['object'] : '';
        $record_id = !empty($data['record_id']) ? intval($data['record_id']) : null;
        $migrate_id = !empty($data['migrate_id']) ? intval($data['migrate_id']) : '';
        $parent_id = !empty($data['parent_id']) ? intval($data['parent_id']) : '';
        $migrate_parent_id = !empty($data['migrate_parent_id']) ? intval($data['migrate_parent_id']) : '';

        if(empty($object) || !in_array($object, ['city', 'district', 'ward'])){
            die('Tham số không hợp lệ: Type');
        }

        $type_suggest = 'info';
        if(empty($migrate_id)) $type_suggest = 'merge';
        if(empty($record_id)) $type_suggest = 'create';

        $cities = $districts = $wards = $records_migrate = [];
        $record_name = $record_extend = '';
        $city_id = $district_id = $ward_id = '';
        if($object == 'city'){
            if(!empty($record_id)){
                $record_info = TableRegistry::get('Cities')->find()->where([
                    'id' => $record_id
                ])->first();
            }

            $record_name = !empty($record_info['name']) ? $record_info['name'] : '';
            $type = isset($record_info['type']) ? $record_info['type'] : '';
            
            if($type == 1) $record_extend = 'Tỉnh';
            if($type == 2) $record_extend = 'Thành phố';

            $cities_migrate = TableRegistry::get('CitiesMigrate')->find()->toList();
            $records_migrate = !empty($cities_migrate) ? Hash::combine($cities_migrate, '{n}.id', '{n}.name') : [];

            $cities = TableRegistry::get('Cities')->find()->where([
                'deleted' => 0,
                'status' => 1
            ])->toList();
            $cities = !empty($cities) ? Hash::combine($cities, '{n}.id', '{n}.name') : [];
        }

        if($object == 'district'){
            if(!empty($record_id)){
                $district_info = TableRegistry::get('Districts')->find()->where([
                    'id' => $record_id
                ])->first();
            }
            $record_name = !empty($district_info['name']) ? $district_info['name'] : '';

            $type = '';
            if(strpos($record_name, 'Quận') !== false) $type = 1;
            if(strpos($record_name, 'Huyện') !== false) $type = 2;
            if(strpos($record_name, 'Thành phố') !== false) $type = 3;
            if(strpos($record_name, 'Thành Phố') !== false) $type = 3;
            if(strpos($record_name, 'Thị xã') !== false) $type = 4;
            if(strpos($record_name, 'Thị Xã') !== false) $type = 4;

            if($type == 1) $record_extend = 'Quận';
            if($type == 2) $record_extend = 'Huyện';
            if($type == 3) $record_extend = 'Thành phố';
            if($type == 4) $record_extend = 'Thị xã';

            $record_name = trim(str_replace(['Quận', 'Huyện', 'Thành phố', 'Thành Phố', 'Thị xã', 'Thị Xã'], '', $record_name));

            // districts
            $cities = TableRegistry::get('Cities')->find()->where([
                'deleted' => 0,
                'status' => 1
            ])->toList();
            $cities = !empty($cities) ? Hash::combine($cities, '{n}.id', '{n}.name') : [];

            if(!empty($parent_id)){
                $districts = TableRegistry::get('Districts')->find()->where([
                    'city_id' => $parent_id
                ])->toList();
                $districts = !empty($districts) ? Hash::combine($districts, '{n}.id', '{n}.name') : [];
            }

            // districts migrate
            if(!empty($migrate_parent_id)){
                $city_migrate_info = TableRegistry::get('CitiesMigrate')->find()->where([
                    'id' => $migrate_parent_id
                ])->first();

                $city_code = !empty($city_migrate_info['code']) ? $city_migrate_info['code'] : '';
                if(!empty($city_code)){
                    $districts_migrate = TableRegistry::get('DistrictsMigrate')->find()->where([
                        'city_code' => $city_code
                    ])->toList();
                    $records_migrate = !empty($districts_migrate) ? Hash::combine($districts_migrate, '{n}.id', '{n}.name') : [];
                }
            }

            $city_id = $parent_id;
            // $district_id = $record_id;
        }

        if($object == 'ward'){
            if(!empty($record_id)){
                $ward_info = TableRegistry::get('Wards')->find()->where([
                    'id' => $record_id
                ])->first();
            }
            $record_name = !empty($ward_info['name']) ? $ward_info['name'] : '';

            $type = '';
            if(strpos($record_name, 'Phường') !== false) $type = 1;
            if(strpos($record_name, 'Xã') !== false) $type = 2;
            if(strpos($record_name, 'Thị trấn') !== false) $type = 3;
            if(strpos($record_name, 'Thị Trấn') !== false) $type = 3;

            $record_extend = '';
            if($type == 1) $record_extend = 'Phường';
            if($type == 2) $record_extend = 'Xã';
            if($type == 3) $record_extend = 'Thị trấn';

            $record_name = trim(str_replace(['Phường', 'Xã', 'Thị trấn', 'Thị Trấn'], '', $record_name));

            if(!empty($parent_id)){
                $district_info = TableRegistry::get('Districts')->find()->where([
                    'id' => $parent_id
                ])->first();
                $city_id = !empty($district_info['city_id']) ? intval($district_info['city_id']) : '';

                if(!empty($city_id)){
                    $districts = TableRegistry::get('Districts')->find()->where([
                        'city_id' => $city_id
                    ])->toList();
                    $districts = !empty($districts) ? Hash::combine($districts, '{n}.id', '{n}.name') : [];
                }

                $wards = TableRegistry::get('Wards')->find()->where([
                    'district_id' => $parent_id
                ])->toList();
                $wards = !empty($wards) ? Hash::combine($wards, '{n}.id', '{n}.name') : [];
            }

            if(!empty($migrate_parent_id)){
                $district_migrate_info = TableRegistry::get('DistrictsMigrate')->find()->where([
                    'id' => $migrate_parent_id
                ])->first();

                $district_code = !empty($district_migrate_info['code']) ? $district_migrate_info['code'] : '';
                if(!empty($district_code)){
                    $wards_migrate = TableRegistry::get('WardsMigrate')->find()->where([
                        'district_code' => $district_code
                    ])->toList();
                    $records_migrate = !empty($wards_migrate) ? Hash::combine($wards_migrate, '{n}.id', '{n}.name') : [];
                }
            }

            $district_id = $parent_id;
            // $ward_id = $record_id;            
        }
     
        $this->set('object', $object);
        $this->set('record_name', $record_name);
        $this->set('record_extend', $record_extend);

        $this->set('record_id', $record_id);
        $this->set('parent_id', $parent_id);
        $this->set('migrate_id', $migrate_id);
        $this->set('migrate_parent_id', $migrate_parent_id);

        $this->set('city_id', $city_id);
        $this->set('district_id', $district_id);
        $this->set('ward_id', $ward_id);

        $this->set('wards', $wards);
        $this->set('districts', $districts);
        $this->set('cities', $cities);
        
        $this->set('type_suggest', $type_suggest);

        $this->set('records_migrate', $records_migrate);
    }

    public function mergeData()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $result = $this->_saveMergeData($data);

        $this->responseJson($result);
    }

    private function _saveMergeData($data = [])
    {
        $utilities = TableRegistry::get('Utilities');

        $type = !empty($data['type']) ? $data['type'] : '';
        $object = !empty($data['object']) ? $data['object'] : '';
        $merge_record_id = !empty($data['merge_record_id']) ? intval($data['merge_record_id']) : '';
        $record_id = !empty($data['record_id']) ? intval($data['record_id']) : '';

        $city_id = !empty($data['city_id']) ? intval($data['city_id']) : '';
        $district_id = !empty($data['district_id']) ? intval($data['district_id']) : '';
        $ward_id = !empty($data['ward_id']) ? intval($data['ward_id']) : '';

        if(empty($object) || !in_array($object, ['city', 'district', 'ward'])){
            return $utilities->getResponse([MESSAGE => 'Trường không hợp lệ: OBJECT']);
        }

        if(empty($type) || !in_array($type, ['info', 'merge', 'create'])){
            return $utilities->getResponse([MESSAGE => 'Trường không hợp lệ: OBJECT']);
        }

        if($type == 'info'){
            if(empty($record_id) || empty($merge_record_id)){
                return $utilities->getResponse([MESSAGE => 'Tham số không hợp lệ']);
            }
        }

        if($type == 'merge' && empty($record_id)){
            return $utilities->getResponse([MESSAGE => 'Tham số không hợp lệ: record_id']);
        }

        if($type == 'create' && empty($merge_record_id)){
            return $utilities->getResponse([MESSAGE => 'Tham số không hợp lệ: merge_record_id']);
        }

        $data_changed = [
            'type' => $type,
            'object' => $object,
            'record_id' => $record_id
        ];

        switch($object){
            case 'city':
                $table = TableRegistry::get('Cities');
                $table_migrate = TableRegistry::get('CitiesMigrate');
            break;

            case 'district':
                $table = TableRegistry::get('Districts');
                $table_migrate = TableRegistry::get('DistrictsMigrate');
            break;

            case 'ward':
                $table = TableRegistry::get('Wards');
                $table_migrate = TableRegistry::get('WardsMigrate');
            break;
        }        
        
        // kiểm tra thông tin
        if(!empty($record_id)){
            $record_info = $table->find()->where([
                'id' => $record_id
            ])->first();
            if(empty($record_info)){
                return $utilities->getResponse([MESSAGE => 'Không lấy được thông tin bản ghi']);
            }
        }
        
        if(!empty($merge_record_id)){
            $merge_info = $table_migrate->find()->where([
                'id' => $merge_record_id
            ])->first();

            if(empty($merge_info)){
                return $utilities->getResponse([MESSAGE => 'Không lấy được thông tin bản ghi']);
            }
        }

        // cập nhật thông tin
        if(
            $type == 'info' &&
            $record_info['name'] == $merge_info['name'] && 
            $record_info['type'] == $merge_info['type']
        ) return $utilities->getResponse([CODE => SUCCESS]);

        $conn = ConnectionManager::get('default');

        try{
            $conn->begin();

            // sáp nhập vào quận huyện khác
            if($type == 'merge'){
                // xóa quận huyện hiện tại
                $delete = $table->delete($record_info);
                if (empty($delete)){
                    return $utilities->getResponse([MESSAGE => 'Cập nhật thông tin không thành công']);
                }

                // cập nhật lại tất cả phường xã cũ sang district_id mới
                if($object == 'city' && !empty($city_id)){
                    $update_child = TableRegistry::get('Districts')->updateAll(
                        [  
                            'city_id' => $city_id,
                        ],
                        [  
                            'city_id' => $record_id
                        ]
                    );

                    $data_changed['merge_id'] = $city_id;
                }

                if($object == 'district' && !empty($district_id)){
                    $update_child = TableRegistry::get('Wards')->updateAll(
                        [  
                            'district_id' => $district_id,
                        ],
                        [  
                            'district_id' => $record_id
                        ]
                    );

                    $data_changed['merge_id'] = $district_id;
                }

                if($object == 'ward' && !empty($ward_id)){
                    $data_changed['merge_id'] = $ward_id;
                }

            }

            if($type == 'info'){
                $entity = $table->patchEntity($record_info, [
                    'name' => $merge_info['name'],
                    'type' => $merge_info['type']
                ]);

                $save_record = $table->save($entity);

                if (empty($save_record->id)){
                    return $utilities->getResponse([MESSAGE => 'Lưu thông tin không thành công']);
                }

            }

            if($type == 'create') {
                $data_new = [
                    'name' => $merge_info['name'],
                    'type' => $merge_info['type'],
                    'status' => 1,
                    'deleted' => 0
                ];

                if($object == 'district'){
                    $city_code = !empty($merge_info['city_code']) ? $merge_info['city_code'] : '';

                    if(empty($city_code)) {
                        return $utilities->getResponse([MESSAGE => 'Không lấy được thông tin bản ghi']);
                    }

                    $city_merge_info = TableRegistry::get('CitiesMigrate')->find()->where(['code' => $city_code])->first();
                    $city_name = !empty($city_merge_info['name']) ? $city_merge_info['name'] : '';
                    if(empty($city_name)) {
                        return $utilities->getResponse([MESSAGE => 'Không lấy được thông tin bản ghi']);
                    }

                    $city_info = TableRegistry::get('Cities')->find()->where(['name' => $city_name])->first();
                    $city_id = !empty($city_info['id']) ? intval($city_info['id']) : '';
                    if(empty($city_id)) {
                        return $utilities->getResponse([MESSAGE => 'Không lấy được thông tin bản ghi']);
                    }

                    $data_new['city_id'] = $city_id;
                }

                if($object == 'ward'){
                    $district_code = !empty($merge_info['district_code']) ? $merge_info['district_code'] : '';
                    if(empty($district_code)) {
                        return $utilities->getResponse([MESSAGE => 'Không lấy được thông tin bản ghi']);
                    }

                    $district_merge_info = TableRegistry::get('DistrictsMigrate')->find()->where([
                        'code' => $district_code
                    ])->first();
                    $district_name = !empty($district_merge_info['name']) ? $district_merge_info['name'] : '';                    
                    if(empty($district_name)) {
                        return $utilities->getResponse([MESSAGE => 'Không lấy được thông tin bản ghi']);
                    }

                    $district_info = TableRegistry::get('Districts')->find()->where(['name' => $district_name])->first();
                    $district_id = !empty($district_info['id']) ? intval($district_info['id']) : '';
                    if(empty($district_id)) {
                        return $utilities->getResponse([MESSAGE => 'Không lấy được thông tin quận huyện']);
                    }

                    $data_new['district_id'] = $district_id;
                }
                
                $entity = $table->newEntity($data_new);
                $save_record = $table->save($entity);
                if (empty($save_record->id)){
                    return $utilities->getResponse([MESSAGE => 'Lưu thông tin không thành công']);
                }

                $data_changed['record_id'] = $save_record->id;
            }
            
            // create log changed
            $entity_changed = TableRegistry::get('LocationsChanged')->newEntity($data_changed);
            $save_changed = TableRegistry::get('LocationsChanged')->save($entity_changed);
            if (empty($save_changed->id)){
                return $utilities->getResponse([MESSAGE => 'Lưu thông tin không thành công']);
            }


            $conn->commit();
        }catch (Exception $e) {
            $conn->rollback();
        }

        return $utilities->getResponse([CODE => SUCCESS]);
    }

    public function loadTables()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        $data = $this->request->getData();

        $db = ConnectionManager::get('default');
        $collection = $db->getSchemaCollection();

        $tables = $collection->listTables();

        $this->set('tables', $tables);
        $this->render('list_tables');
    }
    
    public function loadFields()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        $data = $this->request->getData();

        $table_name = !empty($data['table']) ? $data['table'] : '';

        $fields = [
            'city_id' => 0,
            'district_id' => 0,
            'ward_id' => 0,
            'city_name' => 0,
            'district_name' => 0,
            'ward_name' => 0,
            'address' => 0,
            'full_address' => 0,
            'synced' => 0
        ];

        $columns = [];
        if(!empty($table_name)){
            $db = ConnectionManager::get('default');

            $collection = $db->getSchemaCollection();
            $schema = $collection->describe($table_name);
            $columns = $schema->columns();
        }

        if(!empty($columns)){
            foreach($fields as $field => $value){
                if(in_array($field, $columns)) {
                    $fields[$field] = 1;
                }
            }
        }
        
        $this->set('fields', $fields);

        $this->render('list_fields');
    }

    public function processFormReload()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        $data = $this->request->getData();

        $table_name = !empty($data['table']) ? $data['table'] : '';
        if(empty($table_name)) die;        

        $this->set('table_name', $table_name);
        $this->render('process_form');
    }

    public function checkStatus()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $table_name = !empty($data['table']) ? $data['table'] : '';
        if(empty($table_name)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $field_synced = false;
        $total_record = $synced_record = 0;

        try{
            // check field synced exist
            $db = ConnectionManager::get('default');
            $collection = $db->getSchemaCollection();
            $schema = $collection->describe($table_name);
            $columns = $schema->columns();
            if(!empty($columns) && in_array('synced', $columns)) $field_synced = true;

            // count total record
            $total_record = $record = TableRegistry::get($table_name)->find()->count();

            // count number record synced
            if(!empty($field_synced)){
                $synced_record = TableRegistry::get($table_name)->find()->where([
                    'synced' => 1
                ])->count();
            }

            // fields
            $fields = [
                'city_id',
                'district_id',
                'ward_id',
                'city_name',
                'district_name',
                'ward_name',
                'address',
                'full_address'
            ];

            if(!empty($columns)){
                foreach($fields as $k => $field){
                    if(!in_array($field, $columns)) unset($fields[$k]);
                }

                $fields = array_values($fields);
            }

        }catch(DatabaseException $ex){
           
        }
            
        
        $this->responseJson([CODE => SUCCESS, 
            DATA => [
                'field_synced' => $field_synced,
                'total_record' => $total_record,
                'synced_record' => $synced_record,
                'fields' => $fields
            ]
        ]);
    }

    public function processing()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $table_name = !empty($data['table']) ? $data['table'] : '';
        $fields = !empty($data['fields']) ? json_decode($data['fields']) : [];
        if(empty($table_name) || empty($fields)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $number_record = 0;

        $fields[] = 'id';
        $records = $record = TableRegistry::get($table_name)->find()->where([
            'synced' => 0
        ])->select($fields)->limit(20)->toList();
        if(!empty($records)){
            foreach($records as $record){
                $this->_processAndUpdate($table_name, $fields, $record);
                $number_record ++;
            }
        }

        $this->responseJson([
            CODE => SUCCESS,
            DATA => [
                'number_record' => $number_record
            ]
        ]);
    }

    private function _processAndUpdate($table_name = '', $fields = [], $record = [])
    {
        if(empty($table_name) || empty($fields) || empty($record)) return true;

        $city_id = !empty($record['city_id']) ? intval($record['city_id']) : null;
        $district_id = !empty($record['city_id']) ? intval($record['city_id']) : null;
        $ward_id = !empty($record['ward_id']) ? intval($record['ward_id']) : null;
        $address = !empty($record['address']) ? trim($record['address']) : '';

        // kiểm tra có bản ghi trong logs_changed không
        $city_id_new = $district_id_new = $ward_id_new = null;
        $city_name_new = $district_name_new = $ward_name_new = null;
        $full_address = [];
        if(!empty($address)) $full_address[] = $address;

        if(!empty($ward_id)){
            $change_info = TableRegistry::get('locations_changed')->find()->where([
                'object' => 'ward',
                'record_id' => $ward_id
            ])->select(['id', 'type', 'merge_id'])->first();

            $type = !empty($change_info['type']) ? $change_info['type'] : '';
            $merge_id = !empty($change_info['merge_id']) ? intval($change_info['merge_id']) : null;

            if(empty($ward_id_new) || $type == 'info') $ward_id_new = $ward_id;
            if($type == 'merge' && !empty($merge_id)) $ward_id_new = $merge_id;
            
            if(!empty($ward_id_new)){
                $ward_info = TableRegistry::get('Wards')->find()->where([
                    'id' => $ward_id_new
                ])->select(['id', 'name', 'district_id'])->first();
                $ward_name_new = !empty($ward_info['name']) ? $ward_info['name'] : '';

                if(!empty($ward_name_new)) $full_address[] = $ward_name_new;
                if(!empty($ward_info['district_id'])) $district_id = intval($ward_info['district_id']);
            }
        }

        if(!empty($district_id)){
            $change_info = TableRegistry::get('locations_changed')->find()->where([
                'object' => 'district',
                'record_id' => $district_id
            ])->select(['id', 'type', 'merge_id'])->first();            
            $type = !empty($change_info['type']) ? $change_info['type'] : '';
            $merge_id = !empty($change_info['merge_id']) ? intval($change_info['merge_id']) : null;

            if(empty($district_id_new) || $type == 'info') $district_id_new = $district_id;
            if($type == 'merge' && !empty($merge_id)) $district_id_new = $merge_id;

            if(!empty($district_id_new)){
                $district_info = TableRegistry::get('Districts')->find()->where([
                    'id' => $district_id_new
                ])->select(['id', 'name', 'city_id'])->first();                
                $district_name_new = !empty($district_info['name']) ? $district_info['name'] : '';
                if(!empty($district_name_new)) $full_address[] = $district_name_new;
                if(!empty($district_info['city_id'])) $city_id = intval($district_info['city_id']);
            }
        }    

        if(!empty($city_id)){
            $change_info = TableRegistry::get('locations_changed')->find()->where([
                'object' => 'city',
                'record_id' => $city_id
            ])->select(['id', 'type', 'merge_id'])->first();

            $type = !empty($change_info['type']) ? $change_info['type'] : '';
            $merge_id = !empty($change_info['merge_id']) ? intval($change_info['merge_id']) : null;

            if(empty($city_id_new) || $type == 'info') $city_id_new = $city_id;
            if($type == 'merge' && !empty($merge_id)) $city_id_new = $merge_id;

            if(!empty($city_id_new)){
                $city_info = TableRegistry::get('Cities')->find()->where([
                    'id' => $city_id_new
                ])->select(['id', 'name'])->first();
                $city_name_new = !empty($city_info['name']) ? $city_info['name'] : '';
                if(!empty($city_name_new)) $full_address[] = $city_name_new;
            }
        }            

        // lấy full_address
        $full_address = !empty($full_address) ? implode(', ', $full_address) : '';

        // cập nhật thông tin bản ghi
        $data_save = [
            'city_id' => $city_id_new,
            'district_id' => $district_id_new,
            'ward_id' => $ward_id_new,
            'city_name' => $city_name_new,
            'district_name' => $district_name_new,
            'ward_name' => $ward_name_new,
            'full_address' => $full_address,
            'synced' => 1
        ];

        $entity = TableRegistry::get($table_name)->patchEntity($record, $data_save);

        $update =TableRegistry::get($table_name)->save($entity);
        if (empty($update->id)) return false;

        return true;
    }


}