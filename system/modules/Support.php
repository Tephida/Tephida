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
use Mozg\classes\DB;
use Mozg\classes\Flood;
use Mozg\classes\Module;

class Support extends Module
{
    public function pageNew()
    {
        $params['title'] = 'Новый вопрос';
        $config = settings_get();
        $tpl_dir_name = ROOT_DIR . '/templates/' . $config['temp'];
//        $tpl = new TpLSite($tpl_dir_name, $meta_tags);

        $mobile_speedbar = 'Новый вопрос';
        $tpl->load_template('support/new.tpl');
        $tpl->set('{uid}', $user_id);
        $tpl->compile('content');

        $tpl->render();
    }

    public function create()
    {
        NoAjaxQuery();
        if (Flood::check('support')) {
            echo 'limit';
        } else {
            $title = (new Request)->filter('title', 25000, true);
            $question = (new Request)->filter('question');
            $limitTime = time() - 3600;
            $rowLast = DB::getDB()->super_query("SELECT COUNT(*) AS cnt FROM `support` WHERE сdate > '{$limitTime}'");
            if (!$rowLast['cnt'] and !empty($title) and !empty($question) and $user_info['user_group'] != 4) {
                Flood::LogInsert('support');
                $question = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<!--link:$1--><a href="$1" target="_blank">$1</a><!--/link-->', $question);
                DB::getDB()->query("INSERT INTO `support` SET title = '{$title}', question = '{$question}', suser_id = '{$user_id}', sfor_user_id = '{$user_id}', sdate = '{$server_time}', сdate = '{$server_time}'");
                $dbid = DB::getDB()->insert_id();
                $row = DB::getDB()->super_query("SELECT user_search_pref, user_photo FROM `users` WHERE user_id = '{$user_id}'");

                $meta_tags['title'] = 'Новый вопрос';
                $config = settings_get();
                $tpl_dir_name = ROOT_DIR . '/templates/' . $config['temp'];
                $tpl = new TpLSite($tpl_dir_name, $meta_tags);

                $tpl->load_template('support/show.tpl');
                $tpl->set('{title}', stripslashes($title));
                $tpl->set('{question}', stripslashes($question));
                $tpl->set('{qid}', $dbid);
                $date_str = megaDate($server_time);
                $tpl->set('{date}', $date_str);
                $tpl->set('{status}', 'Вопрос ожидает обработки.');
                $tpl->set('{name}', $row['user_search_pref']);
                $tpl->set('{uid}', $user_id);
                if ($row['user_photo']) {
                    $tpl->set('{ava}', '/uploads/users/' . $user_id . '/50_' . $row['user_photo']);
                } else {
                    $tpl->set('{ava}', '/images/no_ava_50.png');
                }
                $tpl->set('{answers}', '');
                $tpl->compile('content');
//                AjaxTpl($tpl);
                echo 'r|x' . $dbid;
            } else {
                echo 'limit';
            }
        }
    }

    public function delete()
    {
        NoAjaxQuery();
        $qid = (new Request)->int('qid');
        $row = DB::getDB()->super_query("SELECT suser_id FROM `support` WHERE id = '{$qid}'");
        if ($row['suser_id'] == $user_id || $user_info['user_group'] == 4) {
            DB::getDB()->query("DELETE FROM `support` WHERE id = '{$qid}'");
            DB::getDB()->query("DELETE FROM `support_answers` WHERE qid = '{$qid}'");
        }
    }

    public function deleteAnswer()
    {
        NoAjaxQuery();
        $id = (new Request)->int('id');
        $row = DB::getDB()->super_query("SELECT auser_id FROM `support_answers` WHERE id = '{$id}'");
        if ($row['auser_id'] == $user_id || $user_info['user_group'] == 4) {
            DB::getDB()->query("DELETE FROM `support_answers` WHERE id = '{$id}'");
        }
    }

    public function close()
    {
        NoAjaxQuery();
        $qid = (new Request)->int('qid');
        if ($user_info['user_group'] == 4) {
            $row = DB::getDB()->super_query("SELECT COUNT(*) AS cnt FROM `support` WHERE id = '{$qid}'");
            if ($row['cnt']) {
                DB::getDB()->query("UPDATE `support` SET sfor_user_id = 0 WHERE id = '{$qid}'");
            }
        }
    }

    public function answer()
    {
        NoAjaxQuery();
        $qid = (new Request)->int('qid');
        $answer = (new Request)->filter('answer');
        $check = DB::getDB()->super_query("SELECT suser_id FROM `support` WHERE id = '{$qid}'");
        if ($check['suser_id'] == $user_id or $user_info['user_group'] == 4 and isset($answer) and !empty($answer)) {
            if ($user_info['user_group'] == 4) {
                $auser_id = 0;
                DB::getDB()->query("UPDATE `users` SET user_support = user_support+1 WHERE user_id = '{$check['suser_id']}'");
            } else {
                $auser_id = $user_id;
            }

            $answer = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<!--link:$1--><a href="$1" target="_blank">$1</a><!--/link-->', $answer);

            DB::getDB()->query("INSERT INTO `support_answers` SET qid = '{$qid}', auser_id = '{$auser_id}', adate = '{$server_time}', answer = '{$answer}'");
            DB::getDB()->query("UPDATE `support` SET sfor_user_id = '{$auser_id}', sdate = '{$server_time}' WHERE id = '{$qid}'");

            $row = DB::getDB()->super_query("SELECT user_search_pref, user_photo FROM `users` WHERE user_id = '{$user_id}'");

            $meta_tags['title'] = 'Новый вопрос';
            $config = settings_get();
            $tpl_dir_name = ROOT_DIR . '/templates/' . $config['temp'];
//            $tpl = new TpLSite($tpl_dir_name, $meta_tags);

            $tpl->load_template('support/answer.tpl');
            if (!$auser_id) {
                $tpl->set('{name}', 'Агент поддержки');
                $tpl->set('{ava}', '/images/support.png');
                $tpl->set_block("'\\[no-agent\\](.*?)\\[/no-agent\\]'si", "");
            } else {
                $tpl->set('{name}', $row['user_search_pref']);
                if ($row['user_photo']) {
                    $tpl->set('{ava}', '/uploads/users/' . $user_id . '/50_' . $row['user_photo']);
                } else {
                    $tpl->set('{ava}', '/images/no_ava_50.png');
                }

                $tpl->set('[no-agent]', '');
                $tpl->set('[/no-agent]', '');
            }

            if ($auser_id == $user_id || $user_info['user_group'] == 4) {
                $tpl->set('[owner]', '');
                $tpl->set('[/owner]', '');
            } else {
                $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
            }

            $tpl->set('{uid}', $user_id);
            $tpl->set('{answer}', stripslashes($answer));
            $date_str = megaDate($server_time);
            $tpl->set('{date}', $date_str);
            $tpl->compile('content');
//            AjaxTpl($tpl);
        }
    }

    public function show()
    {
        $qid = (new Request)->int('qid');

        $mobile_speedbar = 'Просмотр вопроса';

        $meta_tags['title'] = 'Просмотр вопроса';
        $config = settings_get();
        $tpl_dir_name = ROOT_DIR . '/templates/' . $config['temp'];
//        $tpl = new TpLSite($tpl_dir_name, $meta_tags);

        if ($user_info['user_group'] == 4) {
            $sql_where = "";
        } else {
            $sql_where = "AND tb1.suser_id = '{$user_id}'";
        }

        $row = DB::getDB()->super_query("SELECT tb1.id, title, question, sdate, sfor_user_id, suser_id, tb2.user_search_pref, user_photo FROM `support` tb1, `users` tb2 WHERE tb1.id = '{$qid}' AND tb1.suser_id = tb2.user_id {$sql_where}");
        if ($row) {
            //Выводим ответы
            $sql_answer = DB::getDB()->super_query("SELECT id, adate, answer, auser_id FROM `support_answers` WHERE qid = '{$qid}' ORDER by `adate` ASC LIMIT 0, 100", true);

            $tpl->load_template('support/answer.tpl');
            foreach ($sql_answer as $row_answer) {
                if (!$row_answer['auser_id']) {
                    $tpl->set('{name}', 'Агент поддержки');
                    $tpl->set('{ava}', '/images/support.png');
                    $tpl->set_block("'\\[no-agent\\](.*?)\\[/no-agent\\]'si", "");
                } else {
                    $tpl->set('{name}', $row['user_search_pref']);
                    if ($row['user_photo']) {
                        $tpl->set('{ava}', '/uploads/users/' . $row['suser_id'] . '/50_' . $row['user_photo']);
                    } else {
                        $tpl->set('{ava}', '/images/no_ava_50.png');
                    }

                    $tpl->set('[no-agent]', '');
                    $tpl->set('[/no-agent]', '');
                }

                if ($row_answer['auser_id'] == $user_id || $user_info['user_group'] == 4) {
                    $tpl->set('[owner]', '');
                    $tpl->set('[/owner]', '');
                } else {
                    $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                }

                $tpl->set('{id}', $row_answer['id']);
                $tpl->set('{uid}', $user_id);
                $tpl->set('{answer}', stripslashes($row_answer['answer']));
                $date_str = megaDate($row_answer['adate']);
                $tpl->set('{date}', $date_str);
                $tpl->compile('answers');
            }

            $tpl->load_template('support/show.tpl');
            $tpl->set('{title}', stripslashes($row['title']));
            $tpl->set('{question}', stripslashes($row['question']));
            $tpl->set('{qid}', $qid);
            $date_str = megaDate($row['sdate']);
            $tpl->set('{date}', $date_str);

            if ($row['sfor_user_id'] == $row['suser_id']) {
                $tpl->set('{status}', 'Вопрос ожидает обработки.');
            } else {
                $tpl->set('{status}', 'Есть ответ.');
            }

            $tpl->set('{name}', $row['user_search_pref']);

            if ($user_info['user_group'] == 4) {
                $tpl->set('{uid}', $row['suser_id']);
            } else {
                $tpl->set('{uid}', $user_id);
            }

            if ($row['user_photo']) {
                $tpl->set('{ava}', '/uploads/users/' . $row['suser_id'] . '/50_' . $row['user_photo']);
            } else {
                $tpl->set('{ava}', '/images/no_ava_50.png');
            }

            $tpl->set('{answers}', $tpl->result['answers'] ?? '');
            $tpl->compile('content');
        } else {
            $speedbar = $lang['error'];
            msgbox('', $lang['support_no_quest'], 'info');
        }
        compile($tpl);
    }

    /**
     * @throws \JsonException
     * @throws \ErrorException
     */
    public function main()
    {
        $lang = $this->lang;
        $logged = $this->logged;
        $db = $this->db;
        $user_info = $this->user_info;
        $user_id = $user_info['user_id'];
        $page = (new Request)->int('page', 1);
        $gcount = 20;
        $limit_page = ($page - 1) * $gcount;

        $meta_tags['title'] = 'Помощь';
        $config = settings_get();

        if ($logged) {
            $user_id = $user_info['user_id'];
//            $params['title'] = $lang['help'];

            $path = explode('/', $_SERVER['REQUEST_URI']);
            $page = !empty($path['2']) ? (int)$path['2'] : 1;
            $g_count = 20;
            $limit_page = ($page - 1) * $g_count;
            if ($user_info['user_support'] && $user_info['user_group'] !== 4) {
                DB::getDB()->update('users', [
                    'user_support' => '0',
                ], [
                    'user_id' => $user_id,
                ]);
            }

            $params['group'] = $user_info['user_group'];
            if ($user_info['user_group'] <= 4) {
                $sql_where = "ORDER by `sdate` DESC";
                $sql_where_cnt = "";
                $params['support_title'] = 'Вопросы от пользователей';
            } else {
                $sql_where = "AND tb1.suser_id = '{$user_id}' ORDER by `sdate` DESC";
                $sql_where_cnt = "WHERE suser_id = '{$user_id}'";
                $params['support_title'] = 'Мои вопросы';
            }

            $sql_ = DB::getDB()->run(
                "SELECT tb1.id, title, suser_id, sfor_user_id, sdate, tb2.user_photo, user_search_pref 
                FROM `support` tb1, `users` tb2 WHERE tb1.suser_id = tb2.user_id AND tb1.suser_id = ? 
                ORDER by `sdate` DESC LIMIT ?, ?", $user_id, $limit_page, $g_count);

            if ($sql_) {
                $count = DB::getDB()->single('SELECT COUNT(*) FROM support WHERE suser_id = ?', $user_id);
            } else {
                $count = 0;
            }
            $params['cnt'] = $count;
            $params['alert_info'] = '';

            if (isset($sql_) && $sql_) {
                if ($user_info['user_group'] <= 4) {
                    $params['cnt'] = $count . ' ' . declWord($count, 'questions');
                } else {
                    $params['cnt'] = 'Вы задали ' . $count . ' ' . declWord($count, 'questions');
                }
                foreach ($sql_ as $key => $row) {
                    $sql_[$key]['title'] = stripslashes($row['title']);
                    $sql_[$key]['date'] = $row['sdate'];
                    if ($row['sfor_user_id'] == $row['suser_id'] or $user_info['user_group'] == 4) {
                        if ($row['sfor_user_id'] == $row['suser_id']) {
                            $sql_[$key]['status'] = 'Вопрос ожидает обработки.';
                        } else {
                            $sql_[$key]['status'] = 'Есть ответ.';
                        }
                        $sql_[$key]['name'] = $row['user_search_pref'];
                        $sql_[$key]['answer'] = '';
                        if ($row['user_photo']) {
                            $sql_[$key]['ava'] = '/uploads/users/' . $row['suser_id'] . '/50_' . $row['user_photo'];
                        } else {
                            $sql_[$key]['ava'] = '/images/no_ava_50.png';
                        }
                    } else {
                        $sql_[$key]['name'] = 'Агент поддержки';
                        $sql_[$key]['status'] = 'Есть ответ.';
                        $sql_[$key]['ava'] = '/images/support.png';
                        $sql_[$key]['answer'] = 'ответил';
                    }
                    $sql_[$key]['qid'] = $row['id'];
                }
                $params['questions'] = $sql_;
                $params['navigation'] = '';
            } else {
                $params['alert_info'] = '';
            }
            return view('support.support', $params);
        }
        $params['title'] = $lang['no_infooo'];
        $params['info'] = $lang['not_logged'];
        return view('info.info', $params);
    }
}