<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Tephida - Панель управления</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <script type="text/javascript" src="/js/jquery.lib.js"></script>
    <script type="text/javascript" src="/js/en/lang.js"></script>
    <script type="text/javascript" src="/js/main.js"></script>
    <link href="/css/style.css" rel="stylesheet">
    <script>
        var Logged = {
            log_out: function () {
                $.post('/{admin_index}?act=logout', function () {
                    $.post('/{admin_index}', {ajax: 'yes'}, function (data) {
                        $('#page').html(data.content).css('min-height', '0px');
                    });

                });
            },
        }
    </script>
</head>
<body>
<style media="all">
    html, body{font-size:11px;background: linear-gradient(#0d789c, #c8eeb1,white, white) repeat-x;font-family:Tahoma;line-height:17px;} a{color:#4274a5;text-decoration:underline} a:hover{color:#4274a5;text-decoration:none} .box{margin:auto;width:{box_width}px;background:#fff;box-shadow:0px 1px 4px 1px #cfcfcf;-moz-box-shadow:0px 1px 4px 1px #cfcfcf;-webkit-box-shadow:0px 1px 4px 1px #cfcfcf;-khtml-box-shadow:0px 1px 4px 1px #cfcfcf;padding:10px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;margin-bottom:5px} .head{background: linear-gradient(#1993b0, #1993b0,#3db9c2) repeat-x;height:49px;border-top-left-radius:5px;-moz-top-left-border-radius:5px;-webkit-top-left-border-radius:5px;-khtml-top-left-border-radius:5px;margin:-10px;border-top-right-radius:5px;-moz-top-right-border-radius:5px;-webkit-top-right-border-radius:5px;-khtml-top-right-border-radius:5px;margin:-10px;margin-bottom:5px} .logo{background:url("/system/inc/images/logo.png") no-repeat;width:133px;height:48px;margin-left:5px} .h1{font-size:13px;font-weight:bold;color:#4274a5;margin-top:5px;margin-bottom:5px;padding-bottom:2px;border-bottom:1px solid #e5edf5;padding-left:2px} .clr{clear:both} .fl_l{float:left} .fl_r{float:right} .inp{border:0px;font-size:11px;padding:5px 10px 5px 10px;background:#fff;border:1px solid #ccc;color:#777;margin-top:10px;} .inpu{width:200px;box-shadow:inset 0px 1px 3px 0px #d2d2d2;border:1px solid #ccc;padding:4px;border-radius:3px;font-size:11px;font-family:tahoma;margin-bottom:5px;-moz-box-shadow:inset 0px 1px 3px 0px #d2d2d2;-webkit-box-shadow:inset 0px 1px 3px 0px #d2d2d2} textarea{width:300px;height:100px;} .fllogall{color:#555;margin-left:2px;float:left;width:280px;padding-top:2px} .oneb{float:left;width:300px;font-size:17px;font-weight:700;color:#777;margin-top:5px;padding-top:3px;height:70px} .oneb img{float:left;margin-right:7px} .oneb div{font-size:11px;font-weight:normal;line-height:14px;margin-left:68px;margin-top:5px} .tmenu{background:#f5f5f5;padding:5px;margin-top:-5px;margin-left:-10px;margin-right:-10px} .tmenu a{float:right;margin-left:10px} .foot{clear:both;text-align:center;padding:5px;color:#999;margin:-10px;width:600px;margin:auto} .foot a{color:#444} .foot a:hover{text-decoration:none} .mgcler{clear:both;border-bottom:1px dashed #ccc;margin-bottom:5px} .nav{text-align:center;clear:both;margin-top:5px;margin-bottom:5px} .nav a{padding:3px 5px 3px 5px;font-size:13px;border:1px solid #ddd;margin-right:3px;text-decoration:none} .nav a:hover{background:#f0f0f0} .nav span{padding:3px 5px 3px 5px;font-size:13px;border:1px solid #ddd;margin-right:3px;text-decoration:none;font-weight:bold} .tempdata{height:500px;width:200px;overflow:scroll;border:1px solid #ddd;padding:5px} .tefolfer{background:url("/system/inc/images/directory.png") no-repeat 3px 3px;padding:5px;height:15px;padding-left:24px;cursor:pointer;color:#444;padding-top:3px;font-family:Verdana;} .tefolfer:hover{background:#c8e5f5 url("/system/inc/images/directory.png") no-repeat 3px 3px;} .tetpl{background:url("/system/inc/images/html.png") no-repeat 3px 3px;padding:5px;height:15px;padding-left:24px;padding-top:3px;cursor:pointer;color:#444;font-family:Verdana;} .tetpl:hover{background:#c8e5f5 url("/system/inc/images/html.png") no-repeat 3px 3px;} .tecss{background:url("/system/inc/images/css.png") no-repeat 3px 3px;padding:5px;height:15px;padding-top:3px;padding-left:24px;cursor:pointer;color:#444;font-family:Verdana;} .tecss:hover{background:#c8e5f5 url("/system/inc/images/css.png") no-repeat 3px 3px;} .tejs{background:url("/system/inc/images/script.png") no-repeat 3px 3px;padding:5px;height:15px;padding-top:3px;padding-left:24px;cursor:pointer;color:#444;font-family:Verdana;} .tejs:hover{background:#c8e5f5 url("/system/inc/images/script.png") no-repeat 3px 3px;} .edittable{height:490px;width:655px;border:1px solid #ddd;padding:10px;margin-left:10px} .ftext{height:420px;width:645px;border:1px solid #ddd;line-height: 155%;margin-top:10px;padding:4px;font-family:verdana;font-size:12px;-moz-box-shadow:inset 0px 1px 3px 0px #d2d2d2;-webkit-box-shadow:inset 0px 1px 3px 0px #d2d2d2;box-shadow:inset 0px 1px 3px 0px #d2d2d2} #loading_text{color:#fff;position:relative;background: url("/system/inc/images/showb.png");width:250px;margin:auto;margin-top: 250px;padding:10px;font-size:11px;font-family:Verdana;border-radius:5px; -moz-border-radius:5px; -webkit-border-radius:5px;text-align:center;} #loading

    {z-index:100; position:fixed; padding:0; margin:0 auto; height:100%; min-height:100%; width:100%; overflow:hidden; display:none; left:0px; right:0px; bottom:0px; top:0px;background:url("../images/spacer.gif");}
</style>
<div class="box clr">
    <div class="head">
        <a href="{admin_link}" onclick="Page.Go(this.href); return false;">
            <div class="logo"></div>
        </a>
    </div>
    <div id="page">{content}</div>
    <div class="clr"></div>
</div>
<div class="clr"></div>
<div class="foot">
    <div style="margin-bottom:-10px">
        <a href="{admin_link}" style="margin-right:10px" onclick="Page.Go(this.href); return false;">главная</a>
        {stat_lnk}
        <a href="/" style="margin-right:10px" target="_blank">просмотр сайта</a>
        {exit_lnk}
    </div>
    <br/>Tephida<br/></div>
</body>
</html>