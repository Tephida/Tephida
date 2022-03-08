<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

if (!defined('MOZG'))
    die('Hacking attempt!');

NoAjaxQuery();

if (Registry::get('logged')) {
    $act = requestFilter('act');
    $user_info = $user_info ?? Registry::get('user_info');
    $user_id = $user_info['user_id'];
    $metatags['title'] = $lang['balance'];
    $mobile_speedbar = $lang['balance'];
    $server_time = Registry::get('server_time');
    $db = Registry::get('db');

    switch ($act) {

        //################### Страница приглашения дург ###################//
        case "invite":
            $tpl->load_template('balance/invite.tpl');
            $tpl->set('{uid}', $user_id);
            $tpl->set('{site}', $_SERVER['HTTP_HOST']);
            $tpl->compile('content');

            compile($tpl);
            break;

        //################### Страница приглашённых друзей ###################//
        case "invited":
            $tpl->load_template('balance/invited.tpl');
            $tpl->compile('info');
            /** fixme limit */
            $sql_ = $db->super_query("SELECT tb1.ruid, tb2.user_name, user_search_pref, user_birthday, user_last_visit, user_photo, user_logged_mobile FROM `invites` tb1, `users` tb2 WHERE tb1.uid = '{$user_id}' AND tb1.ruid = tb2.user_id", true);
            if ($sql_) {
                $tpl->load_template('balance/invitedUser.tpl');
                foreach ($sql_ as $row) {
                    $user_country_city_name = explode('|', $row['user_country_city_name']);
                    $tpl->set('{country}', $user_country_city_name[0]);

                    if ($user_country_city_name[1])
                        $tpl->set('{city}', ', ' . $user_country_city_name[1]);
                    else
                        $tpl->set('{city}', '');

                    $tpl->set('{user-id}', $row['ruid']);
                    $tpl->set('{name}', $row['user_search_pref']);

                    if ($row['user_photo'])
                        $tpl->set('{ava}', '/uploads/users/' . $row['ruid'] . '/100_' . $row['user_photo']);
                    else
                        $tpl->set('{ava}', '{theme}/images/100_no_ava.png');

                    //Возраст юзера
                    $user_birthday = explode('-', $row['user_birthday']);
                    $tpl->set('{age}', user_age($user_birthday[0], $user_birthday[1], $user_birthday[2]));

                    OnlineTpl($row['user_last_visit'], $row['user_logged_mobile']);
                    $tpl->compile('content');
                }
            } else
                msgbox('', '<br /><br />Вы еще никого не приглашали.<br /><br /><br />', 'info_2');

            compile($tpl);
            break;

        //################### Страница оплаты ###################//
        case "payment":

            NoAjaxQuery();

            $owner = $db->super_query("SELECT balance_rub FROM `users` WHERE user_id = '{$user_id}'");

            $tpl->load_template('balance/payment.tpl');

            if ($user_info['user_photo']) $tpl->set('{ava}', "/uploads/users/{$user_info['user_id']}/50_{$user_info['user_photo']}");
            else $tpl->set('{ava}', "{theme}/images/no_ava_50.png");

            $tpl->set('{rub}', $owner['balance_rub']);
            $tpl->set('{text-rub}', declOfNum($owner['balance_rub'], array('рубль', 'рубля', 'рублей')));
            $tpl->set('{user-id}', $user_info['user_id']);
            $config = settings_get();
            $tpl->set('{sms_number}', $config['sms_number']);

            $tpl->compile('content');

            AjaxTpl($tpl);
            break;

        //################### Страница покупки голосов ###################//
        case "payment_2":

            NoAjaxQuery();

            $owner = $db->super_query("SELECT user_balance, balance_rub FROM `users` WHERE user_id = '{$user_id}'");

            $tpl->load_template('balance/payment_2.tpl');

            if ($user_info['user_photo']) $tpl->set('{ava}', "/uploads/users/{$user_info['user_id']}/50_{$user_info['user_photo']}");
            else $tpl->set('{ava}', "{theme}/images/no_ava_50.png");

            $tpl->set('{balance}', $owner['user_balance']);
            $tpl->set('{rub}', $owner['balance_rub']);
            $config = settings_get();
            $tpl->set('{cost}', $config['cost_balance']);

            $tpl->compile('content');

            AjaxTpl($tpl);
            break;

        //################### Завершение покупки голосов ###################//
        case "ok_payment":

            NoAjaxQuery();

            $num = intFilter('num');
            if ($num <= 0) $num = 0;
            $config = settings_get();
            $resCost = $num * $config['cost_balance'];

            //Выводим тек. баланс юзера (руб.)
            $owner = $db->super_query("SELECT balance_rub FROM `users` WHERE user_id = '{$user_id}'");

            if ($owner['balance_rub'] >= $resCost) {

                $db->query("UPDATE `users` SET user_balance = user_balance + '{$num}', balance_rub = balance_rub - '{$resCost}' WHERE user_id = '{$user_id}'");

            } else
                echo '1';
            break;

        default:

            //################### Вывод текущего счета ###################//

            $owner = $db->super_query("SELECT user_balance, balance_rub FROM `users` WHERE user_id = '{$user_id}'");

            $tpl->load_template('balance/main.tpl');

            $tpl->set('{ubm}', $owner['user_balance']);
            $tpl->set('{rub}', $owner['balance_rub']);
            $tpl->set('{text-rub}', declOfNum($owner['balance_rub'], array('рубль', 'рубля', 'рублей')));

            $tpl->compile('content');

            compile($tpl);
    }
//    $tpl->clear();
//    $db->free();
} else {
    $user_speedbar = $lang['no_infooo'];
    msgbox('', $lang['not_logged'], 'info');
    compile($tpl);
}