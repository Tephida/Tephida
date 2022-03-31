<!DOCTYPE html>
<html lang="{lang}">
<head>
    {header}
    [logged]
    <script>var kj = {uid:'{my-id}'}</script>
    [/logged]
    <link href="/dist/output.css" rel="stylesheet">
    {js}
    [not-logged]
    <script type="text/javascript" src="/js/reg.js"></script>
    [/not-logged]
    <link rel="shortcut icon" href="/images/uic.png"/>
</head>
<body onResize="onBodyResize()" class="no_display">
<div class="scroll_fix_bg no_display" onMouseDown="myhtml.scrollTop()">
    <div class="scroll_fix_page_top">&#8593; Наверх</div>
</div>
<div id="doLoad"></div>
[not-logged]
<script>
    function boxlogin() {
        Box.Show('log', 300, 'Вход на сайт', '<form method="POST" action="" id="logform" style="padding:15px"><div class="flLg">Электронный адрес</div><input type="text" name="email" id="log_email" class="inplog" maxlength="50" style="width:257px" /><div class="flLg">Пароль</div><input type="password" name="password" id="log_password" class="inplog" maxlength="50" style="width:257px" /><div style="margin-top:7px" class="button_div"><button name="log_in" id="login_but" style="width:266px">Войти</button></div><div style="margin-top:7px" class="clear"><a href="/restore">Не можете войти?</a></div></form>', 'Отмена');
    }
</script>
[/not-logged]
[aviable=main][not-logged]<small style="color:#ccc;padding:3px;position:absolute">Tephida</small>[/not-logged][/aviable]
[not-aviable=main]
<div class="head">
    <div class="wr">
        <div class="headwr">
            [logged]<a href="/news" onClick="Page.Go(this.href); return false">[/logged]
                <div class="logo">Mixchat</div>
                [logged]</a>[/logged]
            <div class="headhr"></div>
            [logged]<!--search-->
            <div id="seNewB">
                <input type="text" value="Поиск" class="fave_input search_input"
                       onBlur="if(this.value == '' || this.value=='Поиск'){this.value='Поиск';this.style.color = '#c1cad0'}"
                       onFocus="if(this.value=='Поиск'){this.value='';this.style.color = '#000'}"
                       onKeyPress="if(event.keyCode == 13) gSearch.go();"
                       onKeyUp="FSE.Txt()"
                       onClick="if(this.value != 0) $('.fast_search_bg').show()"
                       id="query" maxlength="65"/>
                <div id="search_types">
                    <input type="hidden" value="1" id="se_type"/>
                    <div class="search_type" id="search_selected_text"
                         onClick="gSearch.open_types('#sel_types'); return false">по людям
                    </div>
                    <div class="search_alltype_sel no_display" id="sel_types">
                        <div id="1"
                             onClick="gSearch.select_type(this.id, 'по людям'); FSE.GoSe($('#query').val()); return false"
                             class="search_type_selected">по людям
                        </div>
                        <div id="2"
                             onClick="gSearch.select_type(this.id, 'по видеозаписям'); FSE.GoSe($('#query').val()); return false">
                            по видеозаписям
                        </div>
                        <div id="3"
                             onClick="gSearch.select_type(this.id, 'по заметкам');  FSE.GoSe($('#query').val()); return false">
                            по заметкам
                        </div>
                        <div id="4"
                             onClick="gSearch.select_type(this.id, 'по сообществам'); FSE.GoSe($('#query').val()); return false">
                            по сообществам
                        </div>
                        <div id="5"
                             onClick="gSearch.select_type(this.id, 'по аудиозаписям');  FSE.GoSe($('#query').val()); return false">
                            по аудиозаписям
                        </div>
                    </div>
                </div>
                <div class="fast_search_bg no_display" id="fast_search_bg">
                    <a href="/" style="padding:12px;background:#eef3f5" onClick="gSearch.go(); return false"
                       onMouseOver="FSE.ClrHovered(this.id)" id="all_fast_res_clr1">
                        <text>Искать</text>
                        <b id="fast_search_txt"></b>
                        <div class="fl_r fast_search_ic"></div>
                    </a>
                    <span id="reFastSearch"></span>
                </div>
            </div>
            <!--/search-->[/logged]
            <div class="headmenu fl_r">
                <div id="audioMP"></div>
                [not-logged]<a href="/">главная</a>
                <a href="/" onClick="boxlogin(); return false">войти</a>
                <a href="/" onClick="reg.rules(); return false">регистрация</a>
                [/not-logged]
                [logged]<a href="/index.php?go=search&online=1"
                           onClick="Page.Go(this.href); return false">{translate=main_tpl_people}</a>
                <a href="/index.php?go=search&type=4"
                   onClick="Page.Go(this.href); return false">{translate=main_tpl_lang_1}</a>
                <a href="/audio" onClick="Page.Go(this.href); return false" id="fplayer_pos">музыка</a>
                <a href="/support?act=new" onClick="Page.Go(this.href); return false">помощь</a>
                <a href="/?act=logout">выйти</a>[/logged]
            </div>
        </div>
    </div>
</div>
<div class="wr">
    <div id="audioPlayer"></div>
    <div id="audioPad"></div>
    <div class="page">
        [logged]
        <div class="panelUser">
            <a onMouseOver="myhtml.title('1', 'Моя Страница', 'myprof')" onMouseOut="$('.js_titleRemove').remove();"
               href="{my-page-link}" onClick="Page.Go(this.href); return false;">
                <div class="ic_profile" id="myprof1" onMouseOut="$('.js_titleRemove').remove();"></div>
            </a>
            <a onMouseOut="$('.js_titleRemove').remove();" onMouseOver="myhtml.title('2', 'Сообщения', 'myprof')"
               href="/messages" onClick="Page.Go(this.href); return false;">
                <div class="ic_msg" id="myprof2" onMouseOut="$('.js_titleRemove').remove();">
                    <div id="new_msg">{msg}</div>
                </div>
            </a>
            <a onMouseOut="$('.js_titleRemove').remove();" onMouseOver="myhtml.title('3', 'Друзья', 'myprof')"
               href="/friends{requests-link}" onClick="Page.Go(this.href); return false;" id="requests_link">
                <div class="ic_friends" id="myprof3" onMouseOut="$('.js_titleRemove').remove();">
                    <div id="new_requests">{demands}</div>
                </div>
            </a>
            <a onMouseOut="$('.js_titleRemove').remove();" onMouseOver="myhtml.title('4', 'Фотографии', 'myprof')"
               href="/albums/{my-id}" onClick="Page.Go(this.href); return false;" id="requests_link_new_photos">
                <div class="ic_photo" id="myprof4" onMouseOut="$('.js_titleRemove').remove();">
                    <div id="new_photos">{new_photos}</div>
                </div>
            </a>
            <a onMouseOut="$('.js_titleRemove').remove();" onMouseOver="myhtml.title('5', 'Закладки', 'myprof')"
               href="/fave" onClick="Page.Go(this.href); return false;">
                <div id="myprof5" class="ic_fave" onMouseOut="$('.js_titleRemove').remove();"></div>
            </a>
            <a onMouseOut="$('.js_titleRemove').remove();" onMouseOver="myhtml.title('6', 'Видеозаписи', 'myprof')"
               href="/videos" onClick="Page.Go(this.href); return false;">
                <div onMouseOut="$('.js_titleRemove').remove();" id="myprof6" class="ic_video"></div>
            </a>
            <a onMouseOut="$('.js_titleRemove').remove();" onMouseOver="myhtml.title('8', 'Сообщества', 'myprof')"
               href="{groups-link}" onClick="Page.Go(this.href); return false;" id="new_groups_lnk">
                <div onMouseOut="$('.js_titleRemove').remove();" id="myprof8" class="ic_groups">
                    <div id="new_groups">{new_groups}</div>
                </div>
            </a>
            <a onMouseOut="$('.js_titleRemove').remove();" onMouseOver="myhtml.title('9', 'Новости', 'myprof')"
               href="/news{news-link}" onClick="Page.Go(this.href); return false;" id="news_link">
                <div onMouseOut="$('.js_titleRemove').remove();" id="myprof9" class="ic_news">
                    <div id="new_news">{new-news}</div>
                </div>
            </a>
            <a onMouseOut="$('.js_titleRemove').remove();" onMouseOver="myhtml.title('10', 'Заметки', 'myprof')"
               href="/notes" onClick="Page.Go(this.href); return false;">
                <div onMouseOut="$('.js_titleRemove').remove();" id="myprof10" class="ic_notes"></div>
            </a>
            <a onMouseOut="$('.js_titleRemove').remove();" onMouseOver="myhtml.title('11', 'Настройки', 'myprof')"
               href="/settings" onClick="Page.Go(this.href); return false;">
                <div onMouseOut="$('.js_titleRemove').remove();" id="myprof11" class="ic_settings"></div>
            </a>
            <a onMouseOut="$('.js_titleRemove').remove();" onMouseOver="myhtml.title('12', 'Помощь', 'myprof')"
               href="/support" onClick="Page.Go(this.href); return false;">
                <div onMouseOut="$('.js_titleRemove').remove();" id="myprof12" class="ic_support">
                    <div id="new_support">{new-support}</div>
                </div>
            </a>
            <a onMouseOut="$('.js_titleRemove').remove();" onMouseOver="myhtml.title('13', 'Баланс', 'myprof')"
               href="{ubm-link}" onClick="Page.Go(this.href); return false;" id="ubm_link">
                <div onMouseOut="$('.js_titleRemove').remove();" id="myprof13" class="ic_balance">
                    <div id="new_ubm">{new-ubm}</div>
                </div>
            </a>
        </div>
        [/logged]
        {*        <div id="audioPlayer"></div>*}
        <div id="page">
            {info}{content}
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<div class="footer">
    <a href="/support" onClick="Page.Go(this.href); return false">Помощь</a>
    <div>Mixchat &copy; 2022
        <a class="cursor_pointer" onClick="trsn.box();"
           onMouseOver="myhtml.title('1', 'Выбор используемого языка на сайте', 'langTitle', 1)"
           id="langTitle1">{translate=lang}</a>
    </div>
</div>
[/not-aviable]
[aviable=main]
<style media="all">html, body{background:#000 url("/images/bg1.jpg") no-repeat 50% 0;height:100%;width:100%}</style>
<div class="autowr">
    <a href="/" class="new_logo" style="margin-top:-10px">Mixchat</a>
    <div class="new_descr">Универсальное средство для общения и поиска людей.<br/>
    </div>
    <div class="new_but fl_r cursor_pointer" onClick="boxlogin();" style="margin-top:-32px">Войти</div>
</div>
<div class="clear"></div>
<div class="new_gr"></div>
<div class="autowr">
    <div style="position:absolute;margin-top:50px;width:800px">{info}</div>
    <div style="background:url('/images/i.png');width:1156px;height:609px;margin-left:-120px;margin-top:50px"></div>
    {content}
</div>
[/aviable]
[logged]
<script type="text/javascript">
    function upClose(xnid) {
        $('#event' + xnid).remove();
        $('#updates').css('height', $('.update_box').size() * 123 + 'px');
    }

    function GoPage(event, p) {
        var oi = (event.target) ? event.target.id : ((event.srcElement) ? event.srcElement.id : null);
        if (oi == 'no_ev' || oi == 'update_close' || oi == 'update_close2') return false;
        else {
            pattern = new RegExp(/photo[0-9]/i);
            pattern2 = new RegExp(/video[0-9]/i);
            if (pattern.test(p))
                Photo.Show(p);
            else if (pattern2.test(p)) {
                vid = p.replace('/video', '');
                vid = vid.split('_');
                videos.show(vid[1], p, location.href);
            } else
                Page.Go(p);
        }
    }

    $(document).ready(function () {
        setInterval(function () {
            $.post('/index.php?go=updates', function (d) {
                row = d.split('|');
                if (d && row[1]) {
                    if (row[0] == 1) uTitle = 'Новый ответ на стене';
                    else if (row[0] == 2) uTitle = 'Новый комментарий к фотографии';
                    else if (row[0] == 3) uTitle = 'Новый комментарий к видеозаписи';
                    else if (row[0] == 4) uTitle = 'Новый комментарий к заметке';
                    else if (row[0] == 5) uTitle = 'Новый ответ на Ваш комментарий';
                    else if (row[0] == 6) uTitle = 'Новый ответ в теме';
                    else if (row[0] == 7) uTitle = 'Новый подарок';
                    else if (row[0] == 8) uTitle = 'Новое сообщение';
                    else if (row[0] == 9) uTitle = 'Новая оценка';
                    else if (row[0] == 10) uTitle = 'Ваша запись понравилась';
                    else if (row[0] == 11) uTitle = 'Новая заявка';
                    else if (row[0] == 12) uTitle = 'Заявка принята';
                    else if (row[0] == 13) uTitle = 'Подписки';
                    else uTitle = 'Событие';
                    if (row[0] == 8) {
                        sli = row[6].split('/');
                        tURL = (location.href).replace('https://' + location.host, '').replace('/', '').split('#');
                        if (!sli[2] && tURL[0] == 'messages') return false;
                        if ($('#new_msg').text()) msg_num = parseInt($('#new_msg').text().replace(')', '').replace('(', '')) + 1;
                        else msg_num = 1;
                        $('#new_msg').html("<div class=\"ic_newAct\">" + msg_num + "</div>");
                    }
                    setTimeout('upClose(' + row[4] + ');', 10000);
                    temp = '<div class="update_box cursor_pointer" id="event' + row[4] + '" onClick="GoPage(event, \'' + row[6] + '\'); upClose(' + row[4] + ')"><div class="update_box_margin"><div style="height:19px"><span>' + uTitle + '</span><div class="update_close fl_r no_display" id="update_close" onMouseDown="upClose(' + row[4] + ')"><div class="update_close_ic" id="update_close2"></div></div></div><div class="clear"></div><div class="update_inpad"><a href="/u' + row[2] + '" onClick="Page.Go(this.href); return false"><div class="update_box_marginimg"><img src="' + row[5] + '" id="no_ev" /></div></a><div class="update_data"><a id="no_ev" href="/u' + row[2] + '" onClick="Page.Go(this.href); return false">' + row[1] + '</a>&nbsp;&nbsp;' + row[3] + '</div></div><div class="clear"></div></div></div>';
                    $('#updates').html($('#updates').html() + temp);
                    var beepThree = $("#beep-three")[0];
                    document.getElementById("beep-three").volume = 0.7;
                    beepThree.play();
                    if ($('.update_box').size() <= 5) $('#updates').animate({'height': (123*$('.update_box').size())+'px'});
                    if ($('.update_box').size() > 5) {
                        evFirst = $('.update_box:first').attr('id');
                        $('#' + evFirst).animate({'margin-top': '-123px'}, 400, function () {
                            $('#' + evFirst).fadeOut('fast', function () {
                                $('#' + evFirst).remove();
                            });
                        });
                    }
                }
            });
        }, 3500);
    });
</script>
<div class="no_display hidden">
    <audio id="beep-three" controls preload="auto">
        <source src="/images/soundact.ogg"></source>
    </audio>
</div>
[/logged]
<div id="audioPlayList"></div>
<audio id="audioplayer" preload="auto"></audio>
<div id="updates"></div>
<div class="clear"></div>
</body>
</html>