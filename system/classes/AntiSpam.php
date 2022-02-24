<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
namespace Mozg\classes;

class AntiSpam
{
    //Лимиты на день
    private static int $max_friends = 40;
    private static int $max_msg = 40; #максимум сообщений не друзьям
    private static int $max_wall = 10; #максимум записей на стену
    private static int $max_identical = 10; #максимум одинаковых текстовых данных
    private static int $max_comm = 100; #максимум комментариев к записям на стенах людей и сообществ
    private static int $max_groups = 5; #максимум сообществ за день

    private static array $types = array(
        'friends' => 1,
        'messages' => 2,
        'wall' => 3,
        'identical' => 4,
        'comments' => 5,
        'groups' => 6,
    );

    public static function check(string $act, $text = false): void
    {
        $user_info = Registry::get('user_info');
        $db = Registry::get('db');
        if ($text)
            $text = md5($text);
        //спам дата
        $antiDate = date('Y-m-d', time());
        $antiDate = strtotime($antiDate);

        if ($act == 'friends') {
            //Проверяем в таблице
            $check = $db->super_query("SELECT COUNT(*) AS cnt FROM `antispam` WHERE act = '1' AND user_id = '{$user_info['user_id']}' AND date = '{$antiDate}'");
            //Если кол-во, логов больше, то ставим блок
            if ($check['cnt'] >= self::$max_friends) {
                die('antispam_err');
            }
        } elseif ($act == 'messages') {
            //Проверяем в таблице
            $check = $db->super_query("SELECT COUNT(*) AS cnt FROM `antispam` WHERE act = '2' AND user_id = '{$user_info['user_id']}' AND date = '{$antiDate}'");
            //Если кол-во, логов больше, то ставим блок
            if ($check['cnt'] >= self::$max_msg) {
                die('antispam_err');
            }
        } elseif ($act == 'wall') {
            //Проверяем в таблице
            $check = $db->super_query("SELECT COUNT(*) AS cnt FROM `antispam` WHERE act = '3' AND user_id = '{$user_info['user_id']}' AND date = '{$antiDate}'");
            //Если кол-во, логов больше, то ставим блок
            if ($check['cnt'] >= self::$max_wall) {
                die('antispam_err');
            }
        } elseif ($act == 'identical') {
            //Если спам на одинаковые тестовые данные
            //Проверяем в таблице
            $check = $db->super_query("SELECT COUNT(*) AS cnt FROM `antispam` WHERE act = '4' AND user_id = '{$user_info['user_id']}' AND date = '{$antiDate}' AND txt = '{$text}'");
            //Если кол-во, логов больше, то ставим блок
            if ($check['cnt'] >= self::$max_identical) {
                die('antispam_err');
            }
        } elseif ($act == 'comments') {
            //Проверяем в таблице
            $check = $db->super_query("SELECT COUNT(*) AS cnt FROM `antispam` WHERE act = '5' AND user_id = '{$user_info['user_id']}' AND date = '{$antiDate}'");
            //Если кол-во, логов больше, то ставим блок
            if ($check['cnt'] >= self::$max_comm) {
                die('antispam_err');
            }
        } //Если спам на проверку сообществ
        elseif ($act == 'groups') {
            //Проверяем в таблице
            $check = $db->super_query("SELECT COUNT(*) AS cnt FROM `antispam` WHERE act = '6' AND user_id = '{$user_info['user_id']}' AND date = '{$antiDate}'");
            //Если кол-во, логов больше, то ставим блок
            if ($check['cnt'] >= self::$max_groups) {
                die('antispam_err');
            }
        }
    }

    private static function getType($act): int
    {
        return self::$types[$act];
    }

    /**
     * @param string $act
     * @param bool $text
     * @return void
     */
    public static function logInsert(string $act, bool|string $text = false): void
    {
        $user_info = Registry::get('user_info');
        $db = Registry::get('db');
        $text = (is_string($text) and !empty($text)) ? md5($text) : '';
        $server_time = date('Y-m-d', time());
        $antiDate = strtotime($server_time);
        $act_num = self::getType($act);
        $db->query("INSERT INTO `antispam` SET act = '{$act_num}', user_id = '{$user_info['user_id']}', date = '{$antiDate}', txt = '{$text}'");
    }
}