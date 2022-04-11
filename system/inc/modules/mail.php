<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

$act = $_GET['act'];

switch ($act) {

    //################### Начало рассылки ###################//
    case "send":
        $limit = (int)$_POST['limit'];
        $lastid = (int)$_POST['lastid'];
        $title = (new \FluffyDollop\Http\Request)->filter('title', 25000, true);
//		$_POST['text'] = $_POST['text'];

        $sql_ = $db->super_query("SELECT user_search_pref, user_email FROM `users` ORDER by `user_id` ASC LIMIT " . $lastid . ", " . $limit, true);

        if ($sql_) {
//            include_once ENGINE_DIR . '/classes/mail.php';
//            $mail = new vii_mail($config, true);

            foreach ($sql_ as $row) {
                $message_send = (new \FluffyDollop\Http\Request)->filter('text');
                $message_send = str_replace("{%user-name%}", $row['user_search_pref'], $message_send);

                $mail->send($row['user_email'], $title, $message_send);

                echo 'ok';
            }
        }

        die();

    default:
        $users = $db->super_query("SELECT COUNT(*) AS cnt FROM `users`");
        if ($users['cnt'] < 20)
            $max_users = $users['cnt'];
        else
            $max_users = 20;

        echoheader();

        echo '<div id="form">';
        echohtmlstart('Подготовка к отправке сообщений');

        echo <<<HTML
<style type="text/css" media="all">
.inpu{width:305px;}
textarea{width:600px;height:300px;}
</style>
<script type="text/javascript" src="/system/inc/js/jquery.js"></script>
<script type="text/javascript">
function mailSend(){
	var limit = $('#limit').val();
	var title = $('#title').val();
	var text = $('#text').val();
	var interval = parseInt($('#interval').val())*1000;
	var lastid = $('#lastlimit').val();
	if(lastid != 'finish'){
		if(title != 0){
			if(text != 0){
				$('#form').hide();
				document.getElementById('limit').disabled = true;
				document.getElementById('interval').disabled = true;
				document.getElementById('text').disabled = true;
				document.getElementById('button').disabled = true;
				document.getElementById('title').disabled = true;
				$('#sendingbox').show();
				$.post('/controlpanel.php?mod=mail&act=send', {limit: limit, title: title, text: text, lastid: lastid}, function(data){
					if(data){
						setTimeout('mailSend()', interval);
						$('#lastlimit').val(parseInt(lastid)+parseInt(limit));
						$('#ok_users').text(parseInt(lastid)+parseInt(limit));
						if($('#ok_users').text() == $('#user_cnt').text())
							$('#status').html('<font color="green">отправка завершена</font>');
					} else {
						$('#status').html('<font color="green">отправка завершена</font>');
						$('#lastlimit').val('finish');
						$('#ok_users').text($('#user_cnt').text());
					}
				});
			} else
				alert('Введите текст сообщения');
		} else
			alert('Введите заголовок сообщения');
	} else
		alert('Перезагрузите страницу, для новой рассылки');
}
</script>
<form method="POST" action="">

<div class="fllogall">Количество писем за один проход:</div><input type="text" id="limit" class="inpu" style="width:50px" value="{$max_users}" /><div class="mgcler"></div>

<div class="fllogall">Интервал между отправкой писем:</div><input type="text" id="interval" class="inpu" style="width:50px" value="1" /> сек.<div class="mgcler"></div>

<div class="fllogall">Заголовок:</div><input type="text" id="title" class="inpu" /><div class="mgcler"></div>

<div class="fllogall">Текст сообщения:</div><textarea class="inpu" id="text"></textarea><div class="mgcler"></div>

<div class="fllogall">&nbsp;</div><div style="margin-bottom:7px">В своем сообщении вы можете использовать тег <br /><b>{%user-name%}</b>, который означает имя получателя.</div><div class="mgcler"></div>

<div class="fllogall">&nbsp;</div><div class="button_div fl_l"><button onClick="mailSend(); return false" id="button" class="inp" style="margin-top:5px">Начать отправку</button></div>
<input type="hidden" id="lastlimit" class="inpu" value="0" />
</form>
HTML;

        echo '</div><div id="sendingbox" style="display:none">';
        echohtmlstart('Отправка сообщений');
        echo <<<HTML
<div id="result"></div>
Отправлено сообщений: <span style="color:red;" id="ok_users">0</span> из <span style="color:blue;" id="user_cnt">{$users['cnt']}</span> Статус: <span id="status">отправка...</span><br /><br />
<span style="color:#777">Внимание идет отсылка сообщений пользователям, не закрывайте данное окно до тех пор, пока не будут отосланы все письма</span>
</div>
HTML;
        echohtmlend();
}