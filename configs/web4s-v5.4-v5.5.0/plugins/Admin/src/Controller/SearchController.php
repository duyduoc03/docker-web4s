<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Filesystem\File;
use Cake\Datasource\ConnectionManager;

class SearchController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function index()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $keyword = !empty($data['keyword']) ? trim($data['keyword']) : null;
        if (!$this->getRequest()->is('post') || empty($keyword)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $result = [];        
        if(!empty($keyword)){
            $lang = TableRegistry::get('Languages')->getDefaultLanguage();

            // ============================ products
            $products_table = TableRegistry::get('Products');
            $products = TableRegistry::get('Products')->queryListProducts([
                'get_item' => true,
                'get_categories' => true,
                FILTER => [
                    'keyword' => $keyword
                ]
            ])->limit(3)->toList();

            if(!empty($products)){
                foreach($products as $product){
                    if(!isset($result['products'])) $result['products'] = [];
                    $result['products'][] = $products_table->formatDataProductDetail($product, $lang);
                }
            }            

            // ============================ articles
            $articles_table = TableRegistry::get('Articles');
            $articles = $articles_table->queryListArticles([
                'get_categories' => true,
                FILTER => [
                    'keyword' => $keyword
                ]
            ])->limit(3)->toList();

            if(!empty($articles)){
                foreach($articles as $article){
                    if(!isset($result['articles'])) $result['articles'] = [];
                    $result['articles'][] = $articles_table->formatDataArticleDetail($article, $lang);
                }
            }

            // ============================ orders
            $orders_table = TableRegistry::get('Orders');
            $orders = $orders_table->queryListOrders([
                FILTER => [
                    'keyword' => $keyword
                ]
            ])->limit(3)->toList();

            if(!empty($orders)){
                foreach($orders as $order){
                    if(!isset($result['orders'])) $result['orders'] = [];
                    $result['orders'][] = $orders_table->formatDataOrderDetail($order);
                }
            }

            // ============================ customers
            $customers_table = TableRegistry::get('Customers');
            $customers = $customers_table->queryListCustomers([
                FILTER => [
                    'keyword' => $keyword
                ]
            ])->limit(3)->toList();
            if(!empty($customers)){
                foreach($customers as $customer){
                    if(!isset($result['customers'])) $result['customers'] = [];
                    $result['customers'][] = $customers_table->formatDataCustomerDetail($customer);
                }
            }
        }
        
        $this->set('result', $result);
        $this->render('index');
    }

}