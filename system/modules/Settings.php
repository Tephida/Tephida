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
use Mozg\classes\Module;

class Settings extends Module
{
    final public function main(): bool
    {
        $lang = $this->lang;
        $db = $this->db;
        $user_info = $this->user_info;
        $logged = $this->logged;
        $user_name = explode(' ', $user_info['user_search_pref']);
        $params['user']['user_info'] = $user_info;
        $params['user']['user_info']['user_name'] = $user_name[0];
        $params['user']['user_info']['user_lastname'] = $user_name[1];
        if ($logged) {
//            $database = self::getDB();
            $params['title'] = $lang['settings'];

//            $request = (Request::getRequest()->getGlobal());

            //Завершении смены E-mail
            $params['code_1'] = 'no_display';
            $params['code_2'] = 'no_display';
            $params['code_3'] = 'no_display';

            if (isset($request['code1'])) {
                $code1 = (new Request)->textFilter('code1');
                $code2 = (new Request)->textFilter('code2');

                if (strlen($code1) == 32) {
//                    $_IP = Request::getRequest()->getClientIP();
                    $_IP = '';//fixme
                    $code2 = '';
                    $check_code1 = $db->super_query("SELECT email FROM `restore` WHERE hash = '{$code1}' AND ip = '{$_IP}'");
                    if ($check_code1['email']) {
                        $check_code2 = $db->super_query("SELECT COUNT(*) AS cnt FROM `restore` WHERE hash != '{$code1}' AND email = '{$check_code1['email']}' AND ip = '{$_IP}'");
                        if ($check_code2['cnt']) {
                            $params['code_1'] = '';
                        } else {
                            $params['code_1'] = 'no_display';
                            $params['code_3'] = '';
                            //Меняем
                            $db->query("UPDATE `users` SET user_email = '{$check_code1['email']}' WHERE user_id = '{$params['user']['user_id']}'");
                            $params['user']['user_email'] = $check_code1['email'];
                        }
                        $db->query("DELETE FROM `restore` WHERE hash = '{$code1}' AND ip = '{$_IP}'");
                    }
                }

                if (strlen($code2) == 32) {
                    $check_code2 = $db->super_query("SELECT email FROM `restore` WHERE hash = '{$code2}' AND ip = '{$_IP}'");
                    if ($check_code2['email']) {
                        $check_code1 = $db->super_query("SELECT COUNT(*) AS cnt FROM `restore` WHERE hash != '{$code2}' AND email = '{$check_code2['email']}' AND ip = '{$_IP}'");
                        if ($check_code1['cnt']) {
                            $params['code_2'] = '';
                        } else {
                            $params['code_2'] = 'no_display';
                            $params['code_3'] = '';

                            //Меняем
                            $db->query("UPDATE `users` SET user_email = '{$check_code2['email']}'  WHERE user_id = '{$params['user']['user_id']}'");
                            $params['user']['user_email'] = $check_code2['email'];
                        }
                        $db->query("DELETE FROM `restore` WHERE hash = '{$code2}' AND ip = '{$_IP}'");
                    }
                }
            }

            //Email
            $substre = substr($user_info['user_email'], 0, 1);
            $epx1 = explode('@', $user_info['user_email']);
            $params['email'] = $substre . '*******@' . $epx1[1];

//            $time_list = Zone::list();

            $params['date_today'] = date("d.m.y H:i:s");

//            $params['timezs'] = Tools::installationSelected($user_info['time_zone'], $time_list);

//            $params['menu'] = Menu::settings();

            return view('settings.settings', $params);
        }

        $params['title'] = $lang['no_infooo'];
        $params['info'] = $lang['not_logged'];
        return view('info.info', $params);
    }
}