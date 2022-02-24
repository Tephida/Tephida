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

    public static function limit(string $act): int
    {
        if ($act == 'friends')
            return self::$max_friends;
        elseif ($act == 'messages')
            return self::$max_msg;
        elseif ($act == 'wall')
            return self::$max_wall;
        elseif ($act == 'identical')
            return self::$max_identical;
        elseif ($act == 'comments')
            return self::$max_comm;
        elseif ($act == 'groups')
            return self::$max_groups;
        else
            return 0;
    }

    public static function check(string $act, $text = false): void
    {
        $user_info = Registry::get('user_info');
        $db = Registry::get('db');
        if ($text)
            $text = md5($text);

        //спам дата
        $antiDate = date('Y-m-d', time());
        $antiDate = strtotime($antiDate);

        $action = self::$types[$act];
        $limit = self::limit($act);

        //Проверяем в таблице
        $check = $db->super_query("SELECT COUNT(*) AS cnt FROM `antispam` WHERE act = '{$action}' AND user_id = '{$user_info['user_id']}' AND date = '{$antiDate}' AND txt = '{$text}'");
        //Если кол-во, логов больше, то ставим блок
        if ($check['cnt'] >= $limit) {
            die('antispam_err');
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