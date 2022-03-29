<?php
declare(strict_types=1);

namespace Mozg\modules;

use FluffyDollop\Support\Registry;

/**
 *
 */
class Bugs
{

    public static array $status = [
        0 => [
            'text' => 'Открыто',
            'color_class' => 'text-danger',
            'tooltip' => 'Мы проверяем ваше сообщение об ошибке.',
        ],
        1 => [
            'text' => 'Открыто',
            'color_class' => 'text-danger',
            'tooltip' => 'Мы проверяем ваше сообщение об ошибке.',
        ],
        9 => [
            'text' => 'В работе',
            'color_class' => 'text-muted',
            'tooltip' => 'Нам удалось воспроизвести эту ошибку. Она передана специалистам соответствующего продукта для дальнейшего изучения.',
        ],
        2 => [
            'text' => 'Исправлено',
            'color_class' => 'text-success',
            'tooltip' => 'ttt',
        ],
        3 => [
            'text' => 'Отклонено',
            'color_class' => 'text-muted',
            'tooltip' => 'ttt',
        ],
        4 => [
            'text' => 'На рассмотрении',
            'color_class' => 'text-success',
            'tooltip' => 'Мы рассматриваем ваше сообщение с учетом предоставленной информации.',
        ],
        6 => [
            'text' => 'Решено',
            'color_class' => 'text-muted',
            'tooltip' => 'Ошибка закрыта.',
        ],
        8 => [
            'text' => 'Заблокировано',
            'color_class' => 'text-muted',
            'tooltip' => 'ttt',
        ],
        5 => [
            'text' => 'Переоткрыто',
            'color_class' => 'text-warning',
            'tooltip' => 'ttt',
        ],
        10 => [
            'text' => 'Не воспроизводится',
            'color_class' => 'text-warning',
            'tooltip' => 'Нам не удалось воспроизвести эту ошибку.',
        ],
        7 => [
            'text' => 'Отложено',
            'color_class' => 'text-warning',
            'tooltip' => 'Отложено',
        ],
        11 => [
            'text' => 'Требует корректировки',
            'color_class' => 'text-warning',
            'tooltip' => 'Нам нужна дополнительная информация от вас, чтобы воспроизвести ошибку, о которой вы сообщили.',
        ],
    ];

    /**
     * @return int
     * @throws JsonException
     */
    public function add_box(): int
    {
//        $tpl = $params['tpl'];
        $db = $this->db();
        $user_info = $row = $this->user_info();
        $user_id = $user_info['user_id'];

//        Tools::NoAjaxQuery();
//        $tpl->load_template('bugs/add.tpl');
        $row = $db->super_query("SELECT user_id, user_photo FROM `users` WHERE user_id = '{$user_id}'");
        if ($row['user_photo']) {
//            $tpl->set('{photo}', '/uploads/users/' . $row['user_id'] . '/' . $row['user_photo']);
            $params['photo'] = '/uploads/users/' . $row['user_id'] . '/' . $row['user_photo'];
        } else {
//            $tpl->set('{photo}', '/images/no_ava.gif');
            $params['photo'] = '/images/no_ava.gif';
        }
//        $tpl->compile('content');
//        Tools::AjaxTpl($tpl);
//        return view('bugs.add', $params);
        return _e_json(array(
            'status' => 1,
            'row' => view_data('bugs.add', $params)
        ));
    }

    /**
     *
     * @throws \Exception
     * @return int
     */
    public function create(): int
    {
        $db = $this->db();
        $logged = $this->logged();
        $user_info = $row = $this->user_info();
        $user_id = $user_info['user_id'];

        $request = (Request::getRequest()->getGlobal());
        if ($logged) {
            Antispam::Check(9, $user_id);
            $title = Validation::textFilter($request['title']);
            $text = Validation::textFilter($request['text']);
            $file = Validation::textFilter($request['file']);

            if (!$file) {
                $file = '';
            }

            $user_info = $this->user_info();
            $user_id = $user_info['user_id'];

            $server_time = Date::time();
            $date = Date::date_convert($server_time, 'Y-m-d H:i:s');

            $row = $db->query("INSERT INTO `bugs` (uids, title, text, date, add_date, images) VALUES ('{$user_id}', '{$title}', '{$text}', '{$date}','{$date}', '{$file}')");
            Antispam::LogInsert(9, $user_id);
            $id = $db->insert_id();
            $status = Status::OK;
        } else {
            $status = Status::BAD_LOGGED;

        }
        return _e_json(array(
            'status' => $status,
        ));
    }

    /**
     *
     * @throws \Exception
     * @return int
     */
    public function create_comment(): int
    {
        $db = $this->db();
        $logged = $this->logged();
        $user_info = $row = $this->user_info();
        $user_id = $user_info['user_id'];

        $request = (Request::getRequest()->getGlobal());

        if ($logged) {
            if ($user_id == $request['user_id']){
//            Antispam::Check(9, $user_id);
//                $title = $request['title'];
                $text = $request['text'];
                $status = $request['status'];
                $bug_id = $request['id'];
//            $file = Validation::textFilter($request['file']);

//            if (!$file) {
////            die();//////}
//                $file = '';
//            }

//                $user_info = $this->user_info();
//                $user_id = $user_info['user_id'];

                $server_time = Date::time();
                $date = Date::date_convert($server_time, 'Y-m-d H:i:s');

                $row = $db->query("INSERT INTO `bugs_comments` (author_user_id, text, add_date, status, bug_id) VALUES ('{$user_id}', '{$text}', '{$date}', '{$status}', '{$bug_id}')");
                $db->query("UPDATE `bugs` SET status = '{$status}', date = '{$date}' WHERE id = '{$bug_id}'");
//            Antispam::LogInsert(9, $user_id);
                $id = $db->insert_id();
                $status = Status::OK;
            } else {
                $status = Status::NOT_DATA;
            }
        } else {
            $status = Status::BAD_LOGGED;
        }
        return _e_json(array(
            'status' => $status,
        ));
    }

    /**
     * @return int
     * @throws JsonException
     */
    public function load_img(): int
    {


        $image_tmp = $_FILES['uploadfile']['tmp_name'];
        $image_name = Gramatic::totranslit($_FILES['uploadfile']['name']);
        $server_time = Date::time();
        $image_rename = substr(md5($server_time + random_int(1, 100000)), 0, 20);
        $image_size = $_FILES['uploadfile']['size'];
        $exp = explode(".", $image_name);
        $type = end($exp); // формат файла

        $max_size = 1024 * 5000;

        if ($image_size <= $max_size) {
            $allowed_files = explode(', ', 'jpg, jpeg, jpe, png, gif');
            if (in_array(strtolower($type), $allowed_files)) {
                $res_type = strtolower('.' . $type);
                $user_info = $this->user_info();
                $user_id = $user_info['user_id'];
                $upload_dir = __DIR__ . '/../../public/uploads/bugs/' . $user_id . '/';
                FileSystem::createDir($upload_dir);
                if (!is_dir($upload_dir)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $upload_dir));
                }

//                $rImg = $upload_dir.$image_rename.$res_type;

                if (move_uploaded_file($image_tmp, $upload_dir . $image_rename . $res_type)) {

                    //Создание оригинала
                    $manager = new ImageManager(array('driver' => 'gd'));
                    $image = $manager->make($upload_dir . $image_rename . $res_type)->resize(600, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $image->save($upload_dir . $image_rename . '.webp', 85);

                    //Создание маленькой копии
                    $manager = new ImageManager(array('driver' => 'gd'));
                    $image = $manager->make($upload_dir . $image_rename . $res_type)->resize(200, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $image->save($upload_dir . 'c_' . $image_rename . '.webp', 90);

                    FileSystem::delete($upload_dir . $image_rename . $res_type);
                    $res_type = '.webp';

                    $img = ($user_id . '|' . $image_rename . $res_type);
                    $status = Status::OK;
                } else {
                    $img = '';
                    $status = Status::BAD_MOVE;
                }
            } else {
                $img = '';
                $status = Status::BAD_FORMAT;
            }
        } else {
            $img = '';
            $status = Status::BIG_SIZE;
        }
        return _e_json(array(
            'img' => $img,
            'status' => $status,
        ));
    }

    /**
     * @throws JsonException
     * @return int
     */
    public function delete(): int
    {
        $logged = $this->logged();

        if ($logged) {
            $db = $this->db();

            $request = (Request::getRequest()->getGlobal());

            $id = (int)$request['id'];

            $row = $db->super_query("SELECT uids, images FROM `bugs` WHERE id = '{$id}'");
            if ($row['uids']){
                $user_info = $this->user_info();
                $user_id = $user_info['user_id'];

                $url_1 = __DIR__ . '/../../public/uploads/bugs/' . $row['uids'] . '/o_' . $row['images'];
                $url_2 = __DIR__ . '/../../public/uploads/bugs/' . $row['uids'] . '/' . $row['images'];

                FileSystem::delete($url_1);
                FileSystem::delete($url_2);

                $db->query("DELETE FROM `bugs` WHERE id = '{$id}'");
                $status = Status::OK;
            } else {
                $status = Status::NOT_FOUND;
            }
        } else {
            $status = Status::BAD_LOGGED;
        }
        return _e_json(array(
            'status' => $status,
        ));
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function open(): int
    {
        $params = array();
//        $tpl = $params['tpl'];
        $db = $this->db();

        $request = (Request::getRequest()->getGlobal());

        $limit_num = 10;
        if ($request['page_cnt'] > 0) {
            $page_cnt = (int)$request['page_cnt'] * $limit_num;
        } else {
            $page_cnt = 0;
        }

        $where = "AND status = '1'";

        $sql_ = $db->super_query("SELECT tb1.*, tb2.user_id, user_search_pref, user_photo, user_sex FROM `bugs` tb1, `users` tb2 WHERE tb1.uids = tb2.user_id  {$where} ORDER by `date` DESC LIMIT {$page_cnt}, {$limit_num}", 1);

        if ($sql_) {
            $params['bugs'] = (new Bugs)->getData($sql_);
        }
        $params['menu'] = Menu::bugs();
//        $tpl->load_template('bugs/head.tpl');
//        $tpl->set('{load}', $tpl->result['bugs']);
//        Tools::navigation($page_cnt, $limit_num, '/index.php'.$query.'&page_cnt=');
//        $tpl->compile('content');

        return view('bugs.main', $params);
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function complete(): int
    {
//        $tpl = $params['tpl'];
        $db = $this->db();

        $request = (Request::getRequest()->getGlobal());

        $limit_num = 10;
        if ($request['page_cnt'] > 0) {
            $page_cnt = (int)$request['page_cnt'] * $limit_num;
        } else {
            $page_cnt = 0;
        }

        $where = "AND status = '2'";

        $sql_ = $db->super_query("SELECT tb1.*, tb2.user_id, user_search_pref, user_photo, user_sex FROM `bugs` tb1, `users` tb2 WHERE tb1.uids = tb2.user_id  {$where} ORDER by `date` DESC LIMIT {$page_cnt}, {$limit_num}", 1);

        if ($sql_) {
            $params['bugs'] = (new Bugs)->getData($sql_);
        }
        $params['menu'] = Menu::bugs();
//        $tpl->load_template('bugs/head.tpl');
//        $tpl->set('{load}', $tpl->result['bugs']);
//        Tools::navigation($page_cnt, $limit_num, '/index.php'.$query.'&page_cnt=');
//        $tpl->compile('content');

        return view('bugs.main', $params);
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function close(): int
    {
        $db = $this->db();

        $request = (Request::getRequest()->getGlobal());

        $limit_num = 10;
        if ($request['page_cnt'] > 0) {
            $page_cnt = (int)$request['page_cnt'] * $limit_num;
        } else {
            $page_cnt = 0;
        }

        $where = "AND status = '3'";

        $sql_ = $db->super_query("SELECT tb1.*, tb2.user_id, user_search_pref, user_photo, user_sex FROM `bugs` tb1, `users` tb2 WHERE tb1.uids = tb2.user_id  {$where} ORDER by `date` DESC LIMIT {$page_cnt}, {$limit_num}", 1);

        if ($sql_) {
            $params['bugs'] = (new Bugs)->getData($sql_);
        }
        $params['menu'] = Menu::bugs();
//        $tpl->load_template('bugs/head.tpl');
//        $tpl->set('{load}', $tpl->result['bugs']);
//        Tools::navigation($page_cnt, $limit_num, '/index.php'.$query.'&page_cnt=');
//        $tpl->compile('content');

        return view('bugs.main', $params);
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function my(): int
    {
//        $tpl = $params['tpl'];
        $db = $this->db();

        $request = (Request::getRequest()->getGlobal());
        $path = explode('/', $_SERVER['REQUEST_URI']);

        $limit_num = 10;
        if ($request['page_cnt'] > 0) {
            $page_cnt = (int)$request['page_cnt'] * $limit_num;
        } else {
            $page_cnt = 0;
        }

        $user_info = $this->user_info();
        $user_id = $user_info['user_id'];

        $where = "AND uids = '{$user_id}'";

        $sql_ = $db->super_query("SELECT tb1.*, tb2.user_id, user_search_pref, user_photo, user_sex FROM `bugs` tb1, `users` tb2 WHERE tb1.uids = tb2.user_id  {$where} ORDER by `date` DESC LIMIT {$page_cnt}, {$limit_num}", 1);

        if ($sql_) {
            $params['bugs'] = (new Bugs)->getData($sql_);
        }
//        $tpl->load_template('bugs/head.tpl');
//        $tpl->set('{load}', $tpl->result['bugs']);
//        Tools::navigation($page_cnt, $limit_num, '/index.php'.$query.'&page_cnt=');
//        $tpl->compile('content');

        $params['menu'] = Menu::bugs();
        return view('bugs.main', $params);
    }

    /**
     * @return int
     * @throws JsonException
     * @throws \Exception
     */
    public function view(): int
    {
        $params = array();
//        $tpl = $params['tpl'];
        $db = $this->db();

        $request = (Request::getRequest()->getGlobal());

        $id = (int)$request['id'];

        $sql_ = $db->super_query("SELECT tb1.*, tb2.user_id, user_search_pref, user_photo, user_sex FROM `bugs` tb1, `users` tb2 WHERE tb1.id = '{$id}' AND tb1.uids = tb2.user_id", true);
//        $bugs = $db->super_query("SELECT admin_id, admin_text FROM `bugs` WHERE admin_id = '{$sql_['user_id']}'");

        if ($sql_) {
            $params['bugs'] = (new Bugs)->getData($sql_);
            $status = Status::OK;
        }else{
            $status = Status::NOT_FOUND;
        }

        //Admin
//        $tpl->set('{admin_text}', stripslashes($row['admin_text']));
//        $tpl->set('{admin_id}', stripslashes($row['admin_id']));

//        $user_info = $this->user_info();
//        $user_id = $user_info['user_id'];

        //user
//        if ($user_id == $row['uids']) {
////            $tpl->set('{delete}', '<a href="/" onClick="bugs.Delete(' . $row['id'] . '); return false;" style="color: #000000">Удалить</a>');
//        }
//        else {
////            $tpl->set('{delete}', '');
//        }

//        $tpl->set('{uid}', $row['user_id']);
//        if ($row['user_photo']) {
////            $tpl->set('{ava}', '/uploads/users/' . $row['user_id'] . '/50_' . $row['user_photo']);
//        }
//        else {
////            $tpl->set('{ava}', '/templates/Default/images/no_ava_50.png');
//        }
//        $tpl->set('{name}', $row['user_search_pref']);
//        $tpl->compile('content');

        $row = view_data('bugs.view', $params);

        return _e_json(array(
            'status' => $status,
            'row' => $row
        ));
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function view_page(): int
    {
        $params = array();
        $db = $this->db();
        $path = explode('/', $_SERVER['REQUEST_URI']);

        $id = (int)$path['2'];

        $sql_ = $db->super_query("SELECT tb1.*, tb2.user_id, user_search_pref, user_photo, user_sex FROM `bugs` tb1, `users` tb2 WHERE tb1.id = '{$id}' AND tb1.uids = tb2.user_id", true);
//        $bugs = $db->super_query("SELECT admin_id, admin_text FROM `bugs` WHERE admin_id = '{$sql_['user_id']}'");
        if ($sql_) {
            $params['bugs'] = (new Bugs)->getData($sql_);
        }
        $params['menu'] = Menu::bugs();
        return view('bugs.view_page', $params);
    }

    /**
     * @return bool
     * @ajax
     * @throws \Exception
     */
    public function index(): bool
    {
        $db = Registry::get('db');

        $path = explode('/', $_SERVER['REQUEST_URI']);

        $limit_num = 10;
        if (isset($path['2']) AND $path['2'] > 0) {
            $page_cnt = (int)$path['2'] * $limit_num;
        } else {
            $page_cnt = 0;
        }
        $params = array();
        $where_sql = '';
        $where_cat = '';

        $sql_ = $db->super_query("SELECT tb1.*, tb2.user_id, user_search_pref, user_photo, user_sex FROM `bugs` tb1, `users` tb2 WHERE tb1.uids = tb2.user_id {$where_sql} {$where_cat} ORDER by `date` DESC LIMIT {$page_cnt}, {$limit_num}", true);

        if ($sql_) {
            $params['bugs'] = $this->getData($sql_);
        }
//        $query = Validation::strip_data(urldecode($request['query']));
//        Tools::navigation($page_cnt, $limit_num, '/index.php'.$query.'&page_cnt=');
//        $params['menu'] = Menu::bugs();
//        $params['navigation'] = \Sura\Libs\Tools::navigation($page_cnt, $limit_num, '/bugs/');
//        $tpl->compile('content');

        return view('bugs.main', $params);
    }

    public function getData(array $sql_): array
    {
        $user_info = Registry::get('user_info');
        $user_id = $user_info['user_id'];

        if ($user_info['user_group'] < 5) {
            $moderator = true;
        } else {
            $moderator = false;
        }

        foreach ($sql_ as $key => $row) {

            $sql_[$key]['title'] = stripslashes($row['title']);
            $sql_[$key]['text'] = stripslashes($row['text']);
            $sql_[$key]['date'] = $row['date'];
            $sql_[$key]['add_date'] = $row['add_date'];
            $sql_[$key]['datetime'] = $row['add_date'];
            $sql_[$key]['id'] = $row['id'];
            $sql_[$key]['uid'] = $row['user_id'];
            $sql_[$key]['user_search_pref'] = $row['user_search_pref'];
            $sql_[$key]['user_id'] = $user_id;
            $sql_[$key]['moderator'] = $moderator;

//            if ($moderator == true || $row['user_id'] == $user_id){
//                $sql_[$key]['delete'] = '<a href="/" onClick="bugs.Delete(\' '.$row['id'].' \'); return false;" style="color: #000000">Удалить</a>';
//            }
            $sql_[$key]['status'] = '<span class="' . self::$status[$row['status']]['color_class'] . '">' . self::$status[$row['status']]['text'] . '</span>';
            $sql_[$key]['status_bug'] = self::getStatusData((int)$row['status']);
            $sql_[$key]['name'] = $row['user_search_pref'];

            if ($row['user_sex'] == 1) {
                $sql_[$key]['sex'] = 'добавил';
            } else {
                $sql_[$key]['sex'] = 'добавила';
            }

            if ($row['user_photo']) {
                $sql_[$key]['ava'] = '/uploads/users/' . $row['uids'] . '/50_' . $row['user_photo'];
            } else {
                $sql_[$key]['ava'] = '/images/no_ava_50.png';
            }
            $db = Registry::get('db');
            $comments = $db->super_query("SELECT id, author_user_id, bug_id, text, add_date, status FROM `bugs_comments` WHERE bug_id = {$row['id']}  ORDER by `add_date`", true);
            foreach ($comments as $key2 => $comment) {
                if ($comment['status'] > 0) {
                    $comments[$key2]['status_info'] = 'Статус изменен на ' . self::$status[$comment['status']]['text'];
                } else {
                    $comments[$key2]['status_info'] = '';
                }
            }
            $sql_[$key]['comments'] = $comments;

        }
        return $sql_;
    }

    /**
     * @param $status
     * @return string
     */
    public static function getStatusData(int $status): string
    {
        if ($status == 0 || $status == 1) {
            $response = '            
            <div class="_3t4q _3t4s">
                <div class="_3t4u" style="left: 25%; width: 50%;"></div>
                <div class="_3t4u _3t4v" style="left: 25%; width: 0%;"></div>
                <ul class="_3t51">
                    <li class="_3t4j active _71_e"
                     onmouseover="myhtml.title(\'0\', \'Мы проверяем ваше сообщение об ошибке.\', \'step\', 5)"
                     data-tooltip-content="Мы проверяем ваше сообщение об ошибке."
                        data-hover="tooltip" data-tooltip-position="above" data-tooltip-alignh="center" id="step0"
                        style="width: 50%;">
                        <span class="_3t4l"></span>
                        <label class="_3t4m">Открыто</label>
                    </li>
                    <li class="_3t4j _71_e" 
                    onmouseover="myhtml.title(\'1\', \'Ошибка закрыта.\', \'step\', 5)"
                    data-tooltip-content="Ошибка закрыта." data-hover="tooltip"
                        data-tooltip-position="above" data-tooltip-alignh="center" id="step1" style="width: 50%;">
                        <span class="_3t4l"></span>
                        <label class="_3t4m">Решено</label>
                    </li>
                </ul>
            </div>';
        } elseif ($status == 8 || $status == 6 || $status == 3) {
            $response = '
            <div class="_3t4q _3t4s">
                <div class="_3t4u" style="left: 25%; width: 50%;"></div>
                <div class="_3t4u _3t4v" style="left: 25%; width: 50%;"></div>
                <ul class="_3t51">
                    <li class="_3t4j active _71_e"
                     onmouseover="myhtml.title(\'0\', \'Мы проверяем ваше сообщение об ошибке.\', \'step\', 5)"
                     data-tooltip-content="Мы проверяем ваше сообщение об ошибке."
                        data-hover="tooltip" data-tooltip-position="above" data-tooltip-alignh="center" id="step0"
                        style="width: 50%;">
                        <span class="_3t4l"></span>
                        <label class="_3t4m">Открыто</label>
                    </li>
                    <li class="_3t4j active _71_e" 
                    onmouseover="myhtml.title(\'1\', \'' . self::$status[$status]['tooltip'] . '.\', \'step\', 5)"
                    data-tooltip-content="Ошибка закрыта." data-hover="tooltip"
                        data-tooltip-position="above" data-tooltip-alignh="center" id="step1" style="width: 50%;">
                        <span class="_3t4l"></span>
                        <label class="_3t4m">' . self::$status[$status]['text'] . '</label>
                    </li>
                </ul>
            </div>';
        } elseif ($status == 2 || $status == 4 || $status == 5 || $status == 7 || $status == 9 || $status == 10 || $status == 11) {
            $response = '
            <div class="_3t4q _3t4s">
                <div class="_3t4u" style="left: 16.6667%; width: 66.6667%;"></div>
                <div class="_3t4u _3t4v" style="left: 16.6667%; width: 33.3333%;"></div>
                <ul class="_3t51">
                    <li class="_3t4j active _71_e" 
                    onmouseover="myhtml.title(\'0\', \'Мы проверяем ваше сообщение об ошибке.\', \'step\', 5)"
                    data-tooltip-content="Мы проверяем ваше сообщение об ошибке."
                        data-hover="tooltip" data-tooltip-position="above" data-tooltip-alignh="center" id="step0"
                        style="width: 33.3333%;">
                        <span class="_3t4l"></span>
                        <label class="_3t4m">Открыто</label>
                    </li>
                    <li class="_3t4j active _71_e"
                    onmouseover="myhtml.title(\'1\', \'' . self::$status[$status]['tooltip'] . '\', \'step\', 5)"
                        data-tooltip-content="Нам удалось воспроизвести эту ошибку. Она передана специалистам соответствующего продукта для дальнейшего изучения."
                        data-hover="tooltip" data-tooltip-position="above" data-tooltip-alignh="center" id="step1"
                        style="width: 33.3333%;">
                        <span class="_3t4l"></span>
                        <label class="_3t4m">' . self::$status[$status]['text'] . '</label>
                    </li>
                    <li class="_3t4j active _71_e"
                     onmouseover="myhtml.title(\'2\', \'Ошибка закрыта.\', \'step\', 5)"
                     data-tooltip-content="Ошибка закрыта." data-hover="tooltip"
                        data-tooltip-position="above" data-tooltip-alignh="center" id="step2" style="width: 33.3333%;">
                        <span class="_3t4l"></span>
                        <label class="_3t4m">Решено</label>
                    </li>
                </ul>
            </div>';
        } else {
            $response = '            
            <div class="_3t4q _3t4s">
                <div class="_3t4u" style="left: 25%; width: 50%;"></div>
                <div class="_3t4u _3t4v" style="left: 25%; width: 50%;"></div>
                <ul class="_3t51">
                    <li class="_3t4j active _71_e" 
                    onmouseover="myhtml.title(\'0\', \'Мы проверяем ваше сообщение об ошибке.\', \'step\', 5)"
                    data-tooltip-content="Мы проверяем ваше сообщение об ошибке."
                        data-hover="tooltip" data-tooltip-position="above" data-tooltip-alignh="center" id="step0"
                        style="width: 50%;">
                        <span class="_3t4l"></span>
                        <label class="_3t4m">Открыто</label>
                    </li>
                    <li class="_3t4j active _71_e" 
                    onmouseover="myhtml.title(\'1\', \'Ошибка закрыта.\', \'step\', 5)"
                    data-tooltip-content="Ошибка закрыта." data-hover="tooltip"
                        data-tooltip-position="above" data-tooltip-alignh="center" id="step1" style="width: 50%;">
                        <span class="_3t4l"></span>
                        <label class="_3t4m">Решено</label>
                    </li>
                </ul>
            </div>';
        }
        return $response;
    }
}