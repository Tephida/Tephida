<?php
declare(strict_types=1);

/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use JetBrains\PhpStorm\NoReturn;

if (!defined('MOZG')) die('Hacking attempt!');

class db
{
    public false|mysqli|null $db_id = false;
    public int $query_num = 0;
    public array $query_list = array();
    public array $query_errors_list = array();
    public string $mysql_error = '';
    public int $mysql_error_num = 0;
    public bool|mysqli_result $query_id = false;


    public function connect(?string $db_user, ?string $db_pass, ?string $db_name, ?string $db_location = 'localhost', $show_error = 1): bool
    {
        $db_location = explode(":", $db_location);

        mysqli_report(MYSQLI_REPORT_OFF);

        if (isset($db_location[1])) {
            $this->db_id = mysqli_connect($db_location[0], $db_user, $db_pass, $db_name, $db_location[1]);
        } else {
            $this->db_id = mysqli_connect($db_location[0], $db_user, $db_pass, $db_name);
        }

        $this->query_list[] = array('query' => 'Connection with MySQL Server',
            'num' => 0);

        if (!$this->db_id) {
            if ($show_error == 1) {
                $this->display_error(mysqli_connect_error(), '1');
            } else {
                $this->query_errors_list[] = array('error' => mysqli_connect_error());
                return false;
            }
        }

        mysqli_set_charset($this->db_id, COLLATE);

        mysqli_query($this->db_id, "SET NAMES '" . COLLATE . "'", 0);

        $this->sql_mode();

        return true;
    }

    public function query(string $query, bool $show_error = true,): mysqli_result|bool
    {
        if (!$this->db_id)
            $this->connect(DBUSER, DBPASS, DBNAME, DBHOST);

        if (!($this->query_id = mysqli_query($this->db_id, $query))) {

            $this->mysql_error = mysqli_error($this->db_id);
            $this->mysql_error_num = mysqli_errno($this->db_id);

            if ($show_error) {

                $this->display_error($this->mysql_error, $this->mysql_error_num, $query);

            } else {

                $this->query_errors_list[] = array('query' => $query, 'error' => $this->mysql_error);

            }
        }
        $this->query_num++;
        return $this->query_id;
    }

    /**
     * Unused
     * @param string $query
     * @param bool $show_error
     * @return void
     */
    public function multi_query(string $query, bool $show_error = true): void
    {

        if (!$this->db_id) $this->connect(DBUSER, DBPASS, DBNAME, DBHOST);

        if (mysqli_multi_query($this->db_id, $query)) {
            while (mysqli_more_results($this->db_id) && mysqli_next_result($this->db_id)) {
                ;
            }
        }

        if (mysqli_error($this->db_id)) {

            $this->mysql_error = mysqli_error($this->db_id);
            $this->mysql_error_num = mysqli_errno($this->db_id);

            if ($show_error) {

                $this->display_error($this->mysql_error, $this->mysql_error_num, $query);

            } else {

                $this->query_errors_list[] = array('query' => $query, 'error' => $this->mysql_error);

            }
        }
        $this->query_num++;
    }

    /** 1 used */
    public function get_row(mysqli_result|string $query_id = ''): array|bool|null|string
    {
        if ($query_id == '')
            $query_id = $this->query_id;

        return mysqli_fetch_assoc($query_id);
    }

    /**
     * @return int|string
     * @deprecated
     */
    public function get_affected_rows(): int|string
    {
        return mysqli_affected_rows($this->db_id);
    }

    /** 2 used */
    function get_array(mysqli_result|string $query_id = ''): bool|array|null
    {
        if ($query_id == '')
            $query_id = $this->query_id;

        return mysqli_fetch_array($query_id);
    }

    public function super_query(string $query, bool $multi = false, $show_error = true): array|bool|null
    {
        $this->query_num++;
        $this->query($query, $show_error);
        if (!$multi) {

            $data = $this->get_row();
            $this->free();

            return $data;

        } else {

            $rows = array();

            while ($row = $this->get_row()) {
                $rows[] = $row;
            }

            $this->free();

            return $rows;
        }
    }

    /** 1 used */
    function num_rows(mysqli_result|string $query_id = ''): int|string
    {
        if ($query_id == '')
            $query_id = $this->query_id;

        return mysqli_num_rows($query_id);
    }

    public function insert_id(): int|string
    {
        return mysqli_insert_id($this->db_id);
    }

    /**
     * @param $query_id
     * @return array
     * @deprecated
     */
    function get_result_fields(mysqli_result|string $query_id = ''): array
    {
        if ($query_id == '')
            $query_id = $this->query_id;

        while ($field = mysqli_fetch_field($query_id)) {
            $fields[] = $field;
        }

        return $fields ?? array();
    }

    public function free(mysqli_result|string $query_id = ''): void
    {

        if ($query_id == '')
            $query_id = $this->query_id;

        if ($query_id) {
            mysqli_free_result($query_id);
            $this->query_id = false;
        }
    }

    public function close(): void
    {
        if ($this->db_id)
            mysqli_close($this->db_id);
        $this->db_id = false;
    }

    private function sql_mode(): void
    {
        $remove_modes = array('STRICT_TRANS_TABLES', 'STRICT_ALL_TABLES', 'ONLY_FULL_GROUP_BY', 'NO_ZERO_DATE', 'NO_ZERO_IN_DATE', 'TRADITIONAL');

        $this->query("SELECT @@SESSION.sql_mode", false, false);

        $row = $this->get_array();

        if (!$row[0]) {
            return;
        }

        $modes_array = explode(',', $row[0]);
        $modes_array = array_change_key_case($modes_array, CASE_UPPER);

        foreach ($modes_array as $key => $value) {
            if (in_array($value, $remove_modes)) {
                unset($modes_array[$key]);
            }
        }

        $mode_list = implode(',', $modes_array);

        if ($row[0] != $mode_list) {
            $this->query("SET SESSION sql_mode='{$mode_list}'", false, false);
        }

    }

    function __destruct()
    {

        if ($this->db_id) mysqli_close($this->db_id);

        $this->db_id = false;
    }

    #[NoReturn] private function display_error(string $error, int $error_num, string $query = ''): void
    {

        $query = htmlspecialchars($query, ENT_QUOTES, 'utf-8');
        $error = htmlspecialchars($error, ENT_QUOTES, 'utf-8');

        $trace = debug_backtrace();

        $level = 0;
        if (isset($trace[1]['function']) and $trace[1]['function'] == "query")
            $level = 1;
        if (isset($trace[1]['function']) and $trace[2]['function'] == "super_query")
            $level = 2;

        $trace[$level]['file'] = str_replace(ROOT_DIR, "", $trace[$level]['file']);

        echo <<<HTML
<!DOCTYPE html>
<html>
<head>
<title>MySQL Fatal Error</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
	<div style="width: 700px;margin: 20px; border: 1px solid #D9D9D9; background-color: #F1EFEF; -moz-border-radius: 5px; -webkit-border-radius: 5px; border-radius: 5px; -moz-box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3); -webkit-box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3); box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3);" >
		<div class="top" >MySQL Error!</div>
		<div class="box" ><b>MySQL error</b> in file: <b>{$trace[$level]['file']}</b> at line <b>{$trace[$level]['line']}</b></div>
		<div class="box" >Error Number: <b>{$error_num}</b></div>
		<div class="box" >The Error returned was:<br /> <b>{$error}</b></div>
		<div class="box" ><b>SQL query:</b><br /><br />{$query}</div>
		</div>		
</body>
</html>
HTML;
        die();
    }
}