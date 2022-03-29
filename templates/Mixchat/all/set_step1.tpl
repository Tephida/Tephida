<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
<title>Завершение регистрации</title>
<meta name="generator" content="Vii Engine" />
<meta http-equiv="content-type" content="text/html; charset=windows-1251" />
<noscript><meta http-equiv="refresh" content="0; URL=/badbrowser.php"></noscript>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
</head>
<body>
<style type="text/css" media="all">
html,body{background:#f5f5f5}
.pg1{width:500px;border:1px solid #eee;margin:auto;padding:20px;background:#fff;box-shadow:0px 2px 8px 1px #ccc;-moz-box-shadow:0px 2px 8px 1px #ccc;-webkit-box-shadow:0px 2px 8px 1px #ccc;line-height:18px;text-align:center;margin-top:100px;font-family:Tahoma;font-size:12px;color:#000}
.title{color:#21578b;font-weight:bold;border-bottom:1px solid #F1F4F7;padding-bottom:5px;text-align:left;margin-bottom:10px}
.pg1 span{color:#21578b;}
.inp, .inpst{font-size:11px;font-family:Tahoma;padding:5px;border:1px solid #ddd;width:150px}
.button{border-radius:3px;-moz-border-radius:3px;-webkit-border-radius:3px;font-size:11px;color:#fff;width:150px;text-align:center;background:#4b80b5;font-family:Tahoma;border:1px solid #2a5f94;text-shadow:0px 1px 0px #2a5f94;margin-top:15px;padding-top:3px;padding-bottom:3px;cursor:pointer}
.err_yellow{padding:10px;background:#f4f7fa;border:1px solid #bfd2e4;margin-bottom:10px;text-align:left;font-size:11px;display:none}
.err_red{padding:10px;background:#faebeb;border:1px solid #d68383;margin-bottom:10px;line-height:17px;text-align:left;font-size:11px;display:none}
.no_display{display:none}
.clear{clear:both}
</style>
<div class="pg1">
 <div class="title">Загрузите фон</div>
 <div class="err_red"></div>
 Вы можете загрузить фон своей страницы, чтоб она была запоминающейся для других пользователей.<br /><br /><br />
 <form method="POST" action="">
 <div id="step1"><span><b>Фон</b></span>
 <div style="margin-top:10px">
  <div style="float:left;width:125px;margin-right:5px;margin-left:-7px"><img src="/uploads/bg/1_1.jpg" /><br /><input type="radio" value="1" name="bg" /></div>
  <div style="float:left;width:125px;margin-right:5px"><img src="/uploads/bg/2_2.jpg" /><br /><input type="radio" value="2" name="bg" /></div>
  <div style="float:left;width:125px;margin-right:5px;"><img src="/uploads/bg/3_3.jpg" /><br /><input type="radio" value="3" name="bg" /></div>
  <div style="float:left;width:125px;margin-right:-10px"><img src="/uploads/bg/4_4.jpg" /><br /><input type="radio" value="4" name="bg" /></div>
 </div></div>
 <div class="clear"></div>
  <div id="step2" class="no_display"><div style="margin-left:-75px"><span><b>Цвет шапки</b></span></div>
  <select class="inpst" name="color_head" style="width:150px;margin-top:10px">
  <option value="1">Красный</option>
  <option value="2">Оранжевый</option>
  <option value="3">Желтый</option>
  <option value="4">Зеленый</option>
  <option value="5">Голубой</option>
  <option value="6" selected>Синий</option>
  <option value="7">Фиолетовый</option>
  </select>
 <div class="clear"></div></div>
 <button class="button no_display" name="send2" id="send2">Завершить</button>
 </form>
 <button class="button" onClick="$('#step1, #send1').hide();$('#step2, #send2').show()" id="send1">Далее &raquo;</button>
 <img src="{theme}/images/load3.gif" id="loading" style="display:none;margin-top:18px" />
</div>
</body>
</html>