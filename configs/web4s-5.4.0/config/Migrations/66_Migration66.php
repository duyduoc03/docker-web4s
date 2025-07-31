<?php
declare(strict_types=1);

use Migrations\AbstractMigration;
use Cake\ORM\TableRegistry;

class Migration66 extends AbstractMigration
{
    /**
     * - thêm `customer_id` vào table orders
     * 
     */

    public function up()
    {
        $orders_table = $this->table('orders');
        if (!$orders_table->hasColumn('customer_id')) {
            $orders_table->addColumn('customer_id', 'integer', [
                'limit' => 11,
                'after' => 'note',
                'null' => true
            ])->update();
        }
        

        $table = TableRegistry::get('Orders');
        $loop = 1000;
        for ($i = 1; $i <= $loop; $i++) { 
            $orders = $table->find()->contain(['OrdersContact'])->select([
                'Orders.id', 
                'Orders.customer_id', 
                'OrdersContact.customer_id'
            ])->limit(100)->page($i)->toList();
            if(empty($orders)) break;

            $data_save = [];
            foreach($orders as $order){
                $data_save[] = [
                    'id' => !empty($order['id']) ? intval($order['id']) : null,
                    'customer_id' => !empty($order['OrdersContact']['customer_id']) ? $order['OrdersContact']['customer_id'] : null,
                ];
            }

            $entities = $table->patchEntities($orders, $data_save, ['validate' => false]);
            try{
                $table->saveMany($entities);
            }catch (Exception $e) {
                break;
            }
        }
    }

    public function down()
    {
   
    }
}