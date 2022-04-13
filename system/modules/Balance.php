<?php
/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Mozg\modules;

use FluffyDollop\Http\Request;
use FluffyDollop\Support\Registry;
use Mozg\classes\FKWallet;
use Mozg\classes\Module;

class Balance extends Module
{
    protected array $ipFwkassa = ['168.119.157.136', '168.119.60.227', '138.201.88.124', '178.154.197.79'];

    public function main()
    {
        /** @var array $user_info */
        $user_info = $this->user_info;
        $user_id = $user_info['user_id'];
        $db = $this->db;
        /** @var array $owner */
        $owner = $db->super_query("SELECT user_balance, balance_rub FROM `users` WHERE user_id = '{$user_id}'");
        $config = settings_get();
        $params = [
            'title' => 'Balance',
            'ubm' => $owner['user_balance'],
            'rub' => $owner['balance_rub'],
            'cost' => $config['cost_balance'],
            'text_rub' => declOfNum($owner['balance_rub'], ['рубль', 'рубля', 'рублей']),
        ];

        view('balance.main', $params);
    }

    /**
     * @throws \JsonException|\ErrorException
     */
    public function payment_2()
    {
        /** @var array $user_info */
        $user_info = $this->user_info;
        $user_id = $user_info['user_id'];
        $db = $this->db;

        /** @var array $owner */
        $owner = $db->super_query("SELECT user_balance, balance_rub FROM `users` WHERE user_id = '{$user_id}'");

        if($user_info['user_photo']) {
            $user_ava = "/uploads/users/{$user_info['user_id']}/50_{$user_info['user_photo']}";
        }
        else {
            $user_ava = '/images/no_ava_50.png';
        }

        $config = settings_get();

        $params = [
            'title' => 'Balance',
            'balance' => $owner['user_balance'],
            'rub' => $owner['balance_rub'],
            'cost' => $config['cost_balance'],
            'ava' => $user_ava,
        ];

        view('balance.buymix', $params);

    }

    public function ok_payment()
    {
        $num = (new Request)->int('num');
        /** @var array $user_info */
        $user_info = $this->user_info;
        $user_id = $user_info['user_id'];
        $db = $this->db;
        $config = settings_get();
        $res_cost = $num * $config['cost_balance'];
        /** @var array $owner */
        $owner = $db->super_query("SELECT balance_rub FROM `users` WHERE user_id = '{$user_id}'");
        if($owner['balance_rub'] >= $res_cost){
            $db->query("UPDATE `users` SET user_balance = user_balance + '{$num}', balance_rub = balance_rub - '{$res_cost}' WHERE user_id = '{$user_id}'");
            // START -> Записываем в историю
//            $db->query("INSERT INTO `users_logs` SET user_id = '{$user_info['user_id']}', browser = '{$_BROWSER}',
//                             ip = '{$_IP}', act = '7', date = '{$server_time}',
//                             spent = '{$res_cost}', for_user_id = '{$user_id}'");
        } else {
            echo '1';//fixme
        }
    }

    /**
     * Проверка заказа
     * @return void
     */
    public function checkFWKassa()
    {
        $wallet = new FKWallet();
        $config_payment = $wallet->config();
        $valid_ip = false;
        $array_IP =  $this->ipFwkassa;
        if (isset($_SERVER["HTTP_X_REAL_IP"])) {
            $cur_ip = $_SERVER["HTTP_X_REAL_IP"];
        } else {
            $cur_ip = $_SERVER["REMOTE_ADDR"];
        }
        if (in_array($cur_ip, $array_IP, true)) {
            $valid_ip = true;
        }

        $status = $_REQUEST['STATUS'];
        $order_id = $_REQUEST['MERCHANT_ORDER_ID'];

        if (!$valid_ip) {
            echo  'Error: ip_checked';//todo update
        }elseif (isset($_REQUEST['STATUS']) && $status === 'success'){
            $db = $this->db;

            $db->query("UPDATE `pay` SET status = '1' WHERE order_id = '{$order_id}'");
            /** @var array $row */
            $row = $db->super_query("SELECT id, amount, user_id FROM `pay` WHERE order_id = '{$order_id}'");
            $server_time = time();
            $db->query("UPDATE LOW_PRIORITY `users` SET 
                                balance_rub = balance_rub +  '{$row['amount']}', user_lastupdate = '{$server_time}' 
                            WHERE user_id = '{$row['user_id']}'");
            echo '';
//            header('Location: /shop/?order_id=' . $order_id . '&payment=' . $status);
        }elseif (isset($_REQUEST['STATUS']) && $status === 'fail'){
            echo '';
        }
    }

    /**
     * Menu
     * @return void
     * @throws \ErrorException
     * @throws \JsonException
     */
    public function createOrderBox()
    {
        $kassa = 'test';
        $amount = 100;
        $order = 1;
        $status = 0;
        $product = 1;
        /** @var array $user_info */
        $user_info = Registry::get('user_info');
        $user_id = $user_info['user_id'];
        $params = [
            'title' => 'Payment',//todo
            'kassa' => $kassa,
            'amount' => $amount,
            'order' => $order,
            'user_id' => $user_id,
            'product' => $product,
            'status' => $status,
        ];
        view('balance.create_test', $params);
    }

    /**
     * Step 2
     * редирект на удаленный сервер
     * @return void
     */
    public function payCreateTest(): void
    {
        $kassa = (new Request)->filter('kassa');
        $product = (new Request)->filter('product');
        $amount = 0;
        /** @var array $user_info */
        $user_info = Registry::get('user_info');
        $user_id = $user_info['user_id'];
        $order = 0;
        $status = 0;

        $db = $this->db;
        $db->query("INSERT INTO `pay` SET 
                      kassa = '{$kassa}', 
                      amount = '{$amount}', 
                      order_id = '{$order}', 
                      user_id = '{$user_id}', 
                      status = '{$status}', 
                      product = '{$product}'
                      ");
        $invoice = $db->insert_id();
        header('Location: /pay/test/?amount'.$amount.'&invoice='.$invoice);
    }

    /**
     * #FREEKASSA create order
     * step 2-3
     * @return void
     */
    public function payCreateFkw(): void
    {
//        $kassa = requestFilter('kassa');
        $kassa = 'fkwallet';
        $product = (new Request)->filter('product');
        $amount = 100;
        /** @var array $user_info */
        $user_info = Registry::get('user_info');
        $user_id = $user_info['user_id'];
        $user_email = $user_info['user_email'];
        $order = 0;
        $status = 0;

        $db = $this->db;
        $db->query("INSERT INTO `pay` SET 
                      kassa = '{$kassa}', 
                      amount = '{$amount}', 
                      order_id = '{$order}', 
                      user_id = '{$user_id}', 
                      status = '{$status}', 
                      product = '{$product}'
                      ");
        $invoice = $db->insert_id();

        $wallet = new FKWallet();
        $fkw_config = $wallet->config();
        $wallet_data = [
            'shopId'=>$fkw_config['merchant_id'],
            'nonce'=>time(),
            'paymentId' => $invoice,
            'i' => 1,
            'email' => $user_email,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'amount' => 100,
            'currency' => 'RUB'
        ];
        $response = (new FKWallet())->createOrder($wallet_data);

        $db->query("UPDATE `pay` SET order_id = '{$response['orderId']}' WHERE id = '{$invoice}'");
        if($response['type'] === 'error'){
            $db->query("DELETE FROM `pay` WHERE id = '{$invoice}'");
            header('Location: https://mixchat.ru/balance');
            exit();
        }
        header('Location: '.$response['location']);
        exit();
    }

    /**
     * Редирект на сайт
     * #3
     * @return void
     */
    public function payMain(): void
    {
        $invoice = (new Request)->filter('invoice');
        header('Location: /pay/test/success/?invoice='.$invoice);
    }


    /**
     * #4
     * @return void
     * @throws \ErrorException
     * @throws \JsonException
     */
    public function payTestSuccess()
    {
        $invoice = (new Request)->filter('invoice');
        $db = $this->db;
        /** @var array $user_info */
        $user_info = Registry::get('user_info');
        $user_id = $user_info['user_id'];
        $db->query("UPDATE `pay` SET status = 1 WHERE id = '{$invoice}'");
        /** @var array $row */
        $row = $db->super_query("SELECT * FROM `pay` WHERE id = '{$invoice}'");//todo update columns
        $server_time = time();
        $db->query("UPDATE LOW_PRIORITY `users` SET 
                                balance_rub = balance_rub +  '{$row['amount']}', user_lastupdate = '{$server_time}' 
                            WHERE user_id = '{$user_id}'");
        $params = [
            'title' => 'Payment',//todo
//            'kassa' => $row['kassa'],
//            'amount' => $row['amount'],
//            'order' => $row['order_id'],
//            'product' => $row['product'],
//            'status' => $row['status'],
        ];
        view('balance.test_success', $params);
    }
}