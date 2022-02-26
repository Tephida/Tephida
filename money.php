<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
echo 'error 500';
exit();
@session_start();
@ob_start();
@ob_implicit_flush(0);

@error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);

define('ROOT_DIR', dirname(__FILE__));
define('ENGINE_DIR', ROOT_DIR . '/system');

header('Content-type: text/html; charset=utf-8');

include_once ENGINE_DIR . '/data/config.php';
include_once ENGINE_DIR . '/classes/mysql.php';
include_once ENGINE_DIR . '/data/db.php';

//Кодовое слово
$code_word = $config['code_word'];

//Входные данные
$from = $_GET['from']; //Номер абонента, который отправил SMS
$date = $_GET['date']; //Дата отправки SMS в формате ГГГГ-ММ-ДД чч:мм:сс (пример: 2008-08-17 18:00:24)
$msg = $_GET['msg']; //Сообщение, отправленное абонентом
$cost = $_GET['cost']; //Ваша прибыль в RUR
$operator_id = $_GET['operator_id']; //идентификатор оператора (расшифровка в таблицах выше)
$country = $_GET['country']; //идентификатор страны (расшифровка в таблицах выше)
$short_number = $_GET['short_number']; //короткий номер
$sms_id = $_GET['sms_id']; //уникальный номер сообщения в нашей системе (ВНИМАНИЕ: номер не целочисленный, а строковый, т.е. могут быть латинские буквы, длина от 15 символов до 20). При проверке обработчика всегда равен значению "1debug" (без кавычек)
$abonent_cost = $_GET['abonent_cost']; //стоимость для оператора в RUR
$clear_msg = $_GET['clear_msg']; //сообщение без префиксов и сабпрефиксов (если оно есть)
$sign = $_GET['sign']; //параметр, защищающий от мошенничества. Принимает значение md5($_GET['sms_id'].'ваш_секретный_код'). Если вы не заполняли поле "секретный код", этот параметр будет пустым. Пример по использованию этого параметра приведен ниже

//если скрипт был вызван с неправильным параметром безопасности, завершить выполнение.
if ($sign != md5($sms_id . $code_word)) {

    die('hacking attempt');

}

$user_id = str_replace($config['sms_number'], '', $msg);
$user_id = str_replace('dx', '', $user_id);
$user_id = str_replace('MMMDX', '', $user_id);
$user_id = str_replace('XXXDX', '', $user_id);
$user_id = intval($user_id);

//Проверка на существование юзера
$row = $db->super_query("SELECT COUNT(*) AS cnt FROM `users` WHERE user_id = '{$user_id}'");

if ($row['cnt'] and $cost and $abonent_cost) {

    $abonent_cost = $db->safesql($abonent_cost);
    $from = $db->safesql($from);
    $msg = $db->safesql($msg);
    $short_number = $db->safesql($short_number);
    $date = $db->safesql($date);
    $operator_id = intval($operator_id);
    $country = intval($country);

    //Начисляем
    $db->query("UPDATE `users` SET balance_rub = balance_rub + '{$abonent_cost}' WHERE user_id = '{$user_id}'");

    //Вставляем в лог смс
    $db->query("INSERT INTO `sms_log` SET user_id = '{$user_id}', from_u = '{$from}', msg = '{$msg}', operator_id = '{$operator_id}', country = '{$country}', short_number = '{$short_number}', abonent_cost = '{$abonent_cost}', date = '{$date}'");

    //Ответ
    echo "ok\n";
    echo "Ваш баланс пополнен на: {$abonent_cost} руб.";

}
?>