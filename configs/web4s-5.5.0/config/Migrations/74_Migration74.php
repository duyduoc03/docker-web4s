<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration74 extends AbstractMigration
{

    /**
     * 
     * - bảng `payments_gateway`
     * - bảng `payments_gateway_content`
     *   thêm row 'nowpayment' và 'stripe'
     * 
     */

    public function up()
    {   

        // ============ thêm cổng thanh toán 'nowpayment' 
        $record = $this->fetchRow('SELECT * FROM `payments_gateway` WHERE `code` = "nowpayment"');
        if (empty($record)) {
            $this->table('payments_gateway')->insert([
                'code'  => 'nowpayment',
                'config' => null,
                'is_installment' => 0,
                'status' => 0
            ])->save();
        }
                
        $record = $this->fetchRow('SELECT * FROM `payments_gateway_content` WHERE `payment_code` LIKE "nowpayment"');
        if (empty($record)) {

            $this->table('payments_gateway_content')->insert([
                [
                    'payment_code'  => 'nowpayment',
                    'name' => 'NowPayment',
                    'content' => null,
                    'lang' => 'vi'
                ],
                [
                    'payment_code'  => 'nowpayment',
                    'name' => 'NowPayment',
                    'content' => null,
                    'lang' => 'en'
                ]
            ])->save();
        }

        // ============ thêm cổng thanh toán 'stripe' 

        $record = $this->fetchRow('SELECT * FROM `payments_gateway` WHERE `code` = "stripe"');
        if (empty($record)) {
            $this->table('payments_gateway')->insert([
                'code'  => 'stripe',
                'config' => null,
                'is_installment' => 0,
                'status' => 0
            ])->save();
        }
                
        $record = $this->fetchRow('SELECT * FROM `payments_gateway_content` WHERE `payment_code` LIKE "stripe"');
        if (empty($record)) {

            $this->table('payments_gateway_content')->insert([
                [
                    'payment_code'  => 'stripe',
                    'name' => 'Stripe',
                    'content' => null,
                    'lang' => 'vi'
                ],
                [
                    'payment_code'  => 'stripe',
                    'name' => 'Stripe',
                    'content' => null,
                    'lang' => 'en'
                ]
            ])->save();
        }

    }

    public function down()
    {

    }
}
