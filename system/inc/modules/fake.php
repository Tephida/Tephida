<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use FluffyDollop\Support\Registry;
use Mozg\classes\TplCp;

$config = settings_get();

$act = (new \FluffyDollop\Http\Request)->filter('act');

switch ($act) {
    case "people":
        if (isset($_POST['saveconf'])) {
            $db = Registry::get('db');
            //                $saves = $_POST['save'];

            try {
                $saves = json_decode(stripslashes($_POST['save']), false, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                $saves = [];
                throw new Error('error decode data');
            }

            if ($saves->num_fake > 0){
                $people = 0;
                for ($i = 0;$i < $saves->num_fake;  ++$i ){
                    try {
                        $faker = Faker\Factory::create('ru_RU');
//                        $name = $faker->name('female');
//                        [$first_name,   $name, $lastname, ] = explode(' ', $name);
                        $first_name = $faker->firstName('female');
                        $lastname = $faker->lastName();
                        $lastname = str_replace('aa', '', $lastname);
                        $lastname .= 'a';
                        $email = $faker->email();
                        $pass = random_bytes(8);
                        $hid = random_bytes(8);
                        $server_time = time();
                        $table_Chema[] = "INSERT INTO `users` 
                            SET user_name = '{$first_name}', 
                                user_lastname = '{$lastname}', 
                                user_email = '{$email}', 
                                user_password = '{$pass}', 
                                user_group = 5, 
                                user_search_pref = '{$first_name} {$lastname}', 
                                user_privacy = 'val_msg|1||val_wall1|1||val_wall2|1||val_wall3|1||val_info|1||', 
                                user_hid = '{$hid}',     
                                user_birthday = '0-0-0', 
                                user_day = '0', 
                                user_month = '0', 
                                user_year = '0', 
                                user_country = '0', 
                                user_city = '0', 
                                user_lastdate = '{$server_time}', 
                                user_lastupdate = '{$server_time}',   
                                user_reg_date = '{$server_time}'";
                        foreach ($table_Chema as $query) {
                            $db->query($query);
                        }
                        ++$people;
                    }catch (Error){

                    }

                }
                $response = array(
                    'info' => "Успешно сохранены {$people} из {$saves->num_fake} человек!",
                );
            }else{
                $response = array(
                    'info' => '0 человек добавлено'
                );
            }
        } else {
            $response = array(
                'info' => 'Ошибка сохранения'
            );
        }

        (new \FluffyDollop\Http\Response)->_e_json($response);
        break;

    default:

        $tpl = new TplCp(ADMIN_DIR . '/tpl/');
        $tpl->load_template('fake/main.tpl');
        $tpl->set('{config_cost_balance}', '');
        $tpl->compile('content');
        $tpl->render();
}
