<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;

class ShopController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function getList()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        $data = $this->getRequest()->getData();

        $table = TableRegistry::get('Shops');
        $city_id = !empty($data['city_id']) ? intval($data['city_id']) : null;
        $district_id = !empty($data['district_id']) ? intval($data['district_id']) : null;

        $params = [];
        $params[FILTER] = [
            STATUS => 1
        ];

        if (!empty($city_id)) {
            $params[FILTER]['city_id'] = $city_id;
        }

        if (!empty($district_id)) {
            $params[FILTER]['district_id'] = $district_id;
        }

        $shops = $table->queryListShops($params)->toArray();

        $result = [];
        if (!empty($shops)) {
            foreach ($shops as $key => $shop) {
                $result[] = $table->formatDataShopDetail($shop, LANGUAGE);
            }
        }

        $this->set('shops', $result);
        $this->render('item_list');
    }
}