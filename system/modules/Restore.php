<?php
/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Mozg\modules;

use ErrorException;
use JsonException;
use Mozg\classes\{Email, Module, TpLSite, ViewEmail};
use FluffyDollop\Support\{Registry, ViiMail, Status};

class Restore extends Module
{
    /**
     * @throws JsonException
     */
    public function next()
    {
        NoAjaxQuery();
        $db = $this->db;
        $email = requestFilter('email');
        if (!empty($email)) {
            $check = $db->super_query("SELECT user_id, user_search_pref, user_photo FROM `users` WHERE user_email = '{$email}'");
            if ($check) {
                $config = settings_get();
                $theme = '/templates/' . $config['temp'];
                if ($check['user_photo']) {
                    $check['user_photo'] = "/uploads/users/{$check['user_id']}/50_{$check['user_photo']}";
                } else {
                    $check['user_photo'] = "{$theme}/images/no_ava_50.png";
                }

                $response = array(
                    'status' => Status::OK,
                    'user_name' => $check['user_search_pref'],
                    'user_photo' => $check['user_photo'],
                );

            } else {
                $response = array(
                    'status' => Status::NOT_USER,
                );
            }
        } else {
            $response = array(
                'status' => Status::BAD,
            );

        }
        _e_json($response);

    }

    /**
     * @throws \Exception
     */
    public function send(): void
    {
        NoAjaxQuery();
        $db = $this->db;
        $server_time = time();
        $email = requestFilter('email');
        /** @var array $check */
        $check = $db->super_query("SELECT user_name FROM `users` WHERE user_email = '{$email}'");
        if ($check) {
            //Удаляем все предыдущие запросы на восстановление
            $db->query("DELETE FROM `restore` WHERE email = '{$email}'");

            $salt = 'abchefghjkmnpqrstuvwxyz0123456789';
            $rand_lost = '';
            for ($i = 0; $i < 15; $i++) {
                $rand_lost .= $salt[random_int(0, 33)];
            }
            $hash = md5($server_time . $email . random_int(0, 100000) . $rand_lost . $check['user_name']);

            //Вставляем в базу
            $_IP = '';//FIXME
            $db->query("INSERT INTO `restore` SET email = '{$email}', hash = '{$hash}', ip = '{$_IP}'");

            //Отправляем письмо на почту для восстановления
            $config = settings_load();

            /** @var array $lang */
            $dictionary = $this->lang;
            $variables = [
                'user_name' => $check['user_name'],
                'home_url' => $config['home_url'],
                'hash' => $hash,
            ];
            $message = (new ViewEmail('restore.email', $variables))->run();
            /** @var ?string $dictionary['lost_subj'] */
            Email::send($email, $dictionary['lost_subj'], $message);
        }
    }

    /**
     * @throws JsonException
     * @throws ErrorException
     * @throws \Exception
     */
    public function preFinish()
    {
        $tpl = $this->tpl;
        $db = $this->db;
        $hash = strip_data(requestFilter('h'));
        $_IP = '';//FIXME
        if (!empty($hash)) {
            $row = $db->super_query("SELECT email FROM `restore` WHERE hash = '{$hash}' AND ip = '{$_IP}'");
            if ($row) {
                $info = $db->super_query("SELECT user_name FROM `users` WHERE user_email = '{$row['email']}'");
                $tpl->load_template('restore/prefinish.tpl');
                $tpl->set('{name}', $info['user_name']);

                $salt = "abchefghjkmnpqrstuvwxyz0123456789";
                $rand_lost = '';
                for ($i = 0; $i < 15; $i++) {
                    $rand_lost .= $salt[random_int(0, 33)];
                }
                $newhash = md5(time() . $row['email'] . random_int(0, 100000) . $rand_lost);
                $tpl->set('{hash}', $newhash);
                $db->query("UPDATE `restore` SET hash = '{$newhash}' WHERE email = '{$row['email']}'");

                $tpl->compile('content');
            } else {
                $lang = $this->lang;

                $tpl->load_template('info.tpl');
                $tpl->set('{error}', $lang['restore_badlink']);
                $tpl->set('{title}', $lang['restore_badlink']);
                $tpl->compile('info');
            }
        } else {
            $lang = $this->lang;
            $tpl->load_template('info.tpl');
            $tpl->set('{error}', $lang['restore_badlink']);
            $tpl->set('{title}', $lang['restore_badlink']);
            $tpl->compile('info');
        }
        compile($tpl);
    }

    public function finish()
    {
        NoAjaxQuery();
        $hash = strip_data(requestFilter('hash'));
        $row = $db->super_query("SELECT email FROM `restore` WHERE hash = '{$hash}' AND ip = '{$_IP}'");
        if ($row) {
            $new_pass = md5(md5(requestFilter('new_pass')));
            $new_pass2 = md5(md5(requestFilter('new_pass2')));
            if (strlen($new_pass) >= 6 and $new_pass == $new_pass2) {
                $db->query("UPDATE `users` SET user_password = '{$new_pass}' WHERE user_email = '{$row['email']}'");
                $db->query("DELETE FROM `restore` WHERE email = '{$row['email']}'");
            }
        }
    }

    /**
     * @throws JsonException | ErrorException
     */
    public function main()
    {
        $meta_tags['title'] = 'Восстановление';
        $tpl = new TpLSite($this->tpl_dir_name, $meta_tags);
        if (!Registry::get('logged')) {
            $tpl->load_template('restore/main.tpl');
            $tpl->compile('content');
            $tpl->render();
        } else {
            $lang = $this->lang;
            msgBoxNew($tpl, 'Восстановление', $lang['no_str_bar'], 'info.tpl');

        }

    }
}