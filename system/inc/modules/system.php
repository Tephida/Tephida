<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

$config = settings_load();

$act = requestFilter('act');

switch ($act) {
    case "save":
        if (isset($_POST['saveconf'])) {
            if (file_exists(ENGINE_DIR . '/data/config.php')) {
//                $saves = $_POST['save'];

                if (isset($_POST['save'])) {
                    $saves = json_decode(stripslashes($_POST['save']));

                    $find[] = "'\r'";
                    $replace[] = "";
                    $find[] = "'\n'";
                    $replace[] = "";

                    $handler = fopen(ENGINE_DIR . '/data/config.php', "w");
                    fwrite($handler, "<?php \n\n//System Configurations\n\nreturn array (\n\n");

                    foreach ($saves as $name => $value) {

                        if ($name != "offline_msg" and $name != "lang_list") {
                            $value = trim(stripslashes($value));
                            $value = htmlspecialchars($value, ENT_QUOTES);
                            $value = preg_replace($find, $replace, $value);

                            $name = trim(stripslashes($name));
                            $name = htmlspecialchars($name, ENT_QUOTES);
                            $name = preg_replace($find, $replace, $name);
                        }

                        $value = str_replace("$", "&#036;", $value);
                        $value = str_replace("{", "&#123;", $value);
                        $value = str_replace("}", "&#125;", $value);

                        $name = str_replace("$", "&#036;", $name);
                        $name = str_replace("{", "&#123;", $name);
                        $name = str_replace("}", "&#125;", $name);

                        fwrite($handler, "'{$name}' => \"{$value}\",\n\n");
                    }
                    fwrite($handler, ");\n\n?>");
                    fclose($handler);
                    $response = array(
                        'info' => 'Настройки системы были успешно сохранены!'
                    );
                } else {
                    $response = array(
                        'info' => 'Ошибка сохранения',
                    );
                }
            } else {
                $response = array(
                    'info' => 'Ошибка сохранения',
                );
            }
        } else {
            $response = array(
                'info' => 'Ошибка сохранения'
            );
        }

        _e_json($response);
        break;

    default:
        $tpl = initAdminTpl();

        $tpl->load_template('settings/main.tpl');

        $tpl->set('{config_home}', $config['home']);
        $tpl->set('{config_charset}', $config['charset']);
        $tpl->set('{config_home_url}', $config['home_url']);
        $tpl->set('{config_admin_index}', $config['admin_index']);

        //Чтение всех шаблон в папке "templates"
        $root = './templates/';
        $root_dir = scandir($root);
        $for_select = '';
        foreach ($root_dir as $templates) {
            if ($templates != '.' and $templates != '..' and $templates != '.htaccess')
                $for_select .= str_replace('value="' . $config['temp'] . '"', 'value="' . $config['temp'] . '" selected', '<option value="' . $templates . '">' . $templates . '</option>');
        }

        $tpl->set('{for_select}', $for_select);

        $tpl->set('{config_online_time}', $config['online_time']);

        //Чтение всех языков
        $root_dir2 = scandir('./lang/');
        $for_select_lang = '';
        foreach ($root_dir2 as $lang) {
            if ($lang != '.' and $lang != '..' and $lang != '.htaccess')
                $for_select_lang .= str_replace('value="' . $config['lang'] . '"', 'value="' . $config['lang'] . '" selected', '<option value="' . $lang . '">' . $lang . '</option>');
        }

        $tpl->set('{for_select_lang}', $for_select_lang);

        //GZIP
        $for_select_gzip = installationSelected($config['gzip'], '<option value="yes">Да</option><option value="no">Нет</option>');

        $tpl->set('{for_select_gzip}', $for_select_gzip);

        //GZIP JS
        $for_select_gzip_js = installationSelected($config['gzip_js'], '<option value="yes">Да</option><option value="no">Нет</option>');

        $tpl->set('{for_select_gzip_js}', $for_select_gzip_js);

        //Offline
        $for_select_offline = installationSelected($config['offline'], '<option value="yes">Да</option><option value="no">Нет</option>');

        $tpl->set('{for_select_offline}', $for_select_offline);

        $config['offline_msg'] = stripslashes($config['offline_msg']);

        $tpl->set('{config_offline_msg}', $config['offline_msg'] ?? '');

        $tpl->set('{config_lang_list}', $config['lang_list']);
        $tpl->set('{config_bonus_rate}', $config['bonus_rate']);
        $tpl->set('{config_cost_balance}', $config['cost_balance']);


        //Video mod
//	echohtmlstart('<a name="video"></a>Настройки видео');

        $for_select_video_mod = installationSelected($config['video_mod'], '<option value="yes">Да</option><option value="no">Нет</option>');
        $for_select_video_mod_comm = installationSelected($config['video_mod_comm'], '<option value="yes">Да</option><option value="no">Нет</option>');
        $for_select_video_mod_add = installationSelected($config['video_mod_add'], '<option value="yes">Да</option><option value="no">Нет</option>');
        $for_select_video_mod_add_my = installationSelected($config['video_mod_add_my'], '<option value="yes">Да</option><option value="no">Нет</option>');
        $for_select_video_mod_privat = installationSelected($config['video_mod_privat'], '<option value="yes">Да</option><option value="no">Нет</option>');
        $for_select_video_mod_del = installationSelected($config['video_mod_del'], '<option value="yes">Да</option><option value="no">Нет</option>');
        $for_select_video_mod_search = installationSelected($config['video_mod_search'], '<option value="yes">Да</option><option value="no">Нет</option>');

        $tpl->set('{for_select_video_mod}', $for_select_video_mod);
        $tpl->set('{for_select_video_mod_comm}', $for_select_video_mod_comm);
        $tpl->set('{for_select_video_mod_add}', $for_select_video_mod_add);
        $tpl->set('{for_select_video_mod_add_my}', $for_select_video_mod_add_my);
        $tpl->set('{for_select_video_mod_privat}', $for_select_video_mod_privat);
        $tpl->set('{for_select_video_mod_del}', $for_select_video_mod_del);
        $tpl->set('{for_select_video_mod_search}', $for_select_video_mod_search);

        //Audio mod
        $for_select_audio_mod = installationSelected($config['audio_mod'], '<option value="yes">Да</option><option value="no">Нет</option>');
        $for_select_audio_mod_add = installationSelected($config['audio_mod_add'], '<option value="yes">Да</option><option value="no">Нет</option>');
        $for_select_audio_mod_add_my = installationSelected($config['audio_mod_add_my'], '<option value="yes">Да</option><option value="no">Нет</option>');
        $for_select_audio_mod_search = installationSelected($config['audio_mod_search'], '<option value="yes">Да</option><option value="no">Нет</option>');

        $tpl->set('{for_select_audio_mod}', $for_select_audio_mod);
        $tpl->set('{for_select_audio_mod_add}', $for_select_audio_mod_add);
        $tpl->set('{for_select_audio_mod_add_my}', $for_select_audio_mod_add_my);
        $tpl->set('{for_select_audio_mod_search}', $for_select_audio_mod_search);

        //Photo mod
        $for_select_album_mod = installationSelected($config['album_mod'], '<option value="yes">Да</option><option value="no">Нет</option>');
        $for_select_albums_drag = installationSelected($config['albums_drag'], '<option value="yes">Да</option><option value="no">Нет</option>');
        $for_select_photos_drag = installationSelected($config['photos_drag'], '<option value="yes">Да</option><option value="no">Нет</option>');
        $for_select_photos_comm = installationSelected($config['photos_comm'], '<option value="yes">Да</option><option value="no">Нет</option>');
        $for_select_photos_load = installationSelected($config['photos_load'], '<option value="yes">Да</option><option value="no">Нет</option>');

        $tpl->set('{config_max_albums}', $config['max_albums']);
        $tpl->set('{config_max_album_photos}', $config['max_album_photos']);
        $tpl->set('{config_max_photo_size}', $config['max_photo_size']);
        $tpl->set('{config_photo_format}', $config['photo_format']);

        $tpl->set('{config_rate_price}', $config['rate_price']);
        $tpl->set('{for_select_album_mod}', $for_select_album_mod);
        $tpl->set('{for_select_albums_drag}', $for_select_albums_drag);
        $tpl->set('{for_select_photos_drag}', $for_select_photos_drag);
        $tpl->set('{for_select_photos_comm}', $for_select_photos_comm);
        $tpl->set('{for_select_photos_load}', $for_select_photos_load);
        $tpl->set('{config_rate_price}', $config['rate_price']);

        //E-mail
        $for_select_mail_metod = installationSelected($config['mail_metod'], '<option value="php">PHP Mail()</option><option value="smtp">SMTP</option>');

        $tpl->set('{for_select_mail_metod}', $for_select_mail_metod);

        $tpl->set('{config_admin_mail}', $config['admin_mail']);
        $tpl->set('{config_smtp_host}', $config['smtp_host']);
        $tpl->set('{config_smtp_port}', $config['smtp_port']);
        $tpl->set('{config_smtp_user}', $config['smtp_user']);
        $tpl->set('{config_smtp_pass}', $config['smtp_pass']);

        //Настройки E-mail оповещаний
        $for_select_news_mail_1 = installationSelected($config['news_mail_1'], '<option value="yes">Да</option><option value="no">Нет</option>');
        $for_select_news_mail_2 = installationSelected($config['news_mail_2'], '<option value="yes">Да</option><option value="no">Нет</option>');
        $for_select_news_mail_3 = installationSelected($config['news_mail_3'], '<option value="yes">Да</option><option value="no">Нет</option>');
        $for_select_news_mail_4 = installationSelected($config['news_mail_4'], '<option value="yes">Да</option><option value="no">Нет</option>');
        $for_select_news_mail_5 = installationSelected($config['news_mail_5'], '<option value="yes">Да</option><option value="no">Нет</option>');
        $for_select_news_mail_6 = installationSelected($config['news_mail_6'], '<option value="yes">Да</option><option value="no">Нет</option>');
        $for_select_news_mail_7 = installationSelected($config['news_mail_7'], '<option value="yes">Да</option><option value="no">Нет</option>');
        $for_select_news_mail_8 = installationSelected($config['news_mail_8'], '<option value="yes">Да</option><option value="no">Нет</option>');

        $tpl->set('{for_select_news_mail_1}', $for_select_news_mail_1);
        $tpl->set('{for_select_news_mail_2}', $for_select_news_mail_2);
        $tpl->set('{for_select_news_mail_3}', $for_select_news_mail_3);
        $tpl->set('{for_select_news_mail_4}', $for_select_news_mail_4);
        $tpl->set('{for_select_news_mail_5}', $for_select_news_mail_5);
        $tpl->set('{for_select_news_mail_6}', $for_select_news_mail_6);
        $tpl->set('{for_select_news_mail_7}', $for_select_news_mail_7);
        $tpl->set('{for_select_news_mail_8}', $for_select_news_mail_8);

        $tpl->compile('content');
        compileAdmin($tpl);


}

