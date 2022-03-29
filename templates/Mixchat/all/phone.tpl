<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
<title>Привязка номера телефона</title>
<meta name="generator" content="Vii Engine" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<noscript><meta http-equiv="refresh" content="0; URL=/badbrowser.php"></noscript>
<script type="text/javascript" src="{theme}/js/jquery.lib.js?734"></script>
</head>
<body>
<style type="text/css" media="all">
html,body{background:#f5f5f5}
.pg1{width:500px;border:1px solid #eee;margin:auto;padding:20px;background:#fff;box-shadow:0px 2px 8px 1px #ccc;-moz-box-shadow:0px 2px 8px 1px #ccc;-webkit-box-shadow:0px 2px 8px 1px #ccc;line-height:18px;text-align:center;margin-top:100px;font-family:Tahoma;font-size:12px;color:#000}
.title{color:#21578b;font-weight:bold;border-bottom:1px solid #F1F4F7;padding-bottom:5px;text-align:left;margin-bottom:10px}
.pg1 span{color:#21578b;}
.inp{font-size:11px;font-family:Tahoma;padding:5px;border:1px solid #ddd;width:150px}
.button{border-radius:3px;-moz-border-radius:3px;-webkit-border-radius:3px;font-size:11px;color:#fff;width:150px;text-align:center;background:#4b80b5;font-family:Tahoma;border:1px solid #2a5f94;text-shadow:0px 1px 0px #2a5f94;margin-top:15px;padding-top:3px;padding-bottom:3px;cursor:pointer}
.err_yellow{padding:10px;background:#f4f7fa;border:1px solid #bfd2e4;margin-bottom:10px;text-align:left;font-size:11px;display:none}
.err_red{padding:10px;background:#faebeb;border:1px solid #d68383;margin-bottom:10px;line-height:17px;text-align:left;font-size:11px;display:none}
</style>
<script type="text/javascript">
$(document).ready(function(){
	$('#phone').val('+7').focus();
});
function sendCode(){
	var number = $('#phone').val();
	$('#send').hide();
	$('#loading').show();
	$('#country, #phone').attr('disabled', 'disabled');
	$.post('/antibot/sendsms.php', {number: number}, function(d){
		if(d == 1){
			$('#okcode, .err_yellow').show();
			$('#send, .err_red').hide();
			$('#code').focus();
		} else if(d == 2) {
			$('.err_red').html('<b>Мобильный номер занят.</b><br />Номер <b>'+number+'</b> уже используется другим пользователем сайта.').show();
			$('#send').show();
			$('#country, #phone').attr('disabled', '');
		} else if(d == 5) {
			$('.err_red').html('<b>Лимит.</b><br />У Вас исчерпан лимит на получение подтверждающего кода.').show();
			$('#send').show();
			$('#country, #phone').attr('disabled', '');
		} else {
			$('.err_red').html('<b>Некорректный мобильный номер.</b><br />Необходимо корректно ввести номер в международном формате, например +79210000000.').show();
			$('#send').show();
			$('#country, #phone').attr('disabled', '');
		}
		$('#loading').hide();
	});
}
function sendCodeOk(){
	$('#send_code').hide();
	$('#loading').show();
	$.post('/antibot/sendsms.php?act=check', {code: $('#code').val()}, function(d){
		if(d != 0){
			window.location.href = d;
		} else {
			$('#send_code').show();
			$('#loading').hide();
			$('.err_red').html('<b>Неверный код.</b><br />Проверьте правильность ввода кода.').show();
		}
	});
}
</script>
<div class="pg1">
 <div class="title">Привязка номера телефона</div>
 <div class="err_red"></div>
 <div class="err_yellow"><b>Код отправлен.</b><br />На Ваш телефон в течение нескольких секунд придет <b>7</b>-значный код.<br />Пример кода: <b>7895846</b><br /><br />У Вас осталась <b>1</b> попытка.</div>
 Мы просим всех пользователей привязать к странице свой номер <br /><b>мобильного телефона</b>. Это защитит Вашу страницу от угроз и избавит от необходимости постоянно вводить коды.<br /><br /><br />
 <div style="margin-left:-103px"><span><b>Страна</b></span></div>
 <div style="margin-top:10px">
  <select class="inp" id="country" onChange="$('#phone').val(this.value).focus()">
   <option value="+994">Азербайджан</option>
   <option value="+375">Беларусь</option>
   <option value="+77">Казахстан</option>
   <option value="+371">Латвия</option>
   <option value="+370">Литва</option>
   <option value="+373">Молдова</option>
   <option value="+7" selected>Россия</option>
   <option value="+1">США</option>
   <option value="+380">Украина</option>
   <option value="+372">Эстония</option>
   <option value="+972">Израиль</option>
  </select>
 </div>
 <div style="margin-left:-13px;margin-top:15px"><span><b>Мобильный телефон</b></span></div>
 <div style="margin-top:10px">
  <input type="text" class="inp" id="phone" style="width:135px" value="" />
 </div>
 <span id="okcode" style="display:none">
 <div style="margin-left:-13px;margin-top:15px"><span><b>Код подтверждения</b></span></div>
 <div style="margin-top:10px">
  <input type="text" class="inp" id="code" style="width:135px" value="" />
 </div>
 <button class="button" onClick="sendCodeOk()" id="send_code">Отправить код</button>
 </span>
 <button class="button" onClick="sendCode()" id="send">Получить код</button>
 <img src="{theme}/images/load3.gif" id="loading" style="display:none;margin-top:18px" />
</div>
</body>
</html>