<!DOCTYPE html>
<html lang="ru">
<head>
    {header}
    <link media="screen" href="{theme}/style/style.css" type="text/css" rel="stylesheet"/>
    {js}[not-logged]
    <script type="text/javascript" src="{theme}/js/reg.js"></script>
    [/not-logged]
</head>
<body onResize="onBodyResize()" class="no_display">
<div class="scroll_fix_bg no_display" onMouseDown="myhtml.scrollTop()">
    <div class="scroll_fix_page_top">Наверх</div>
</div>
<div id="doLoad"></div>
<div class="head">
    <div class="autowr">
        [logged]<a href="{my-page-link}" class="udinsMy" onClick="Page.Go(this.href); return false;"></a>[/logged]
        [not-logged]<a href="/" class="udins" onClick="Page.Go(this.href); return false;"></a>[/not-logged]
        <div class="headmenu">
            [logged]
            <a href="/messages" onClick="Page.Go(this.href); return false;">
                <div class="headm_posic">
                    <div id="new_msg">{msg}</div>
                    <img src="{theme}/images/spacer.gif" class="headm_ic_mess" alt="spacer"/><br/>
                    Сообщения
                </div>
            </a>
            <a href="/friends{requests-link}" onClick="Page.Go(this.href); return false;" id="requests_link">
                <div class="headm_posic">
                    <div id="new_requests">{demands}</div>
                    <img src="{theme}/images/spacer.gif" class="headm_ic_friend" alt="spacer"/><br/>
                    Друзья
                </div>
            </a>
            <a href="/albums/{my-id}" onClick="Page.Go(this.href); return false;" id="requests_link_new_photos">
                <div class="headm_posic">
                    <div id="new_photos">{new_photos}</div>
                    <img src="{theme}/images/spacer.gif" class="headm_ic_photo" alt="spacer"/><br/>
                    Фото
                </div>
            </a>
            <a href="/fave" onClick="Page.Go(this.href); return false;">
                <div class="headm_posic"><img src="{theme}/images/spacer.gif" class="headm_ic_fave"
                                              alt="spacer"/><br/>
                    Закладки
                </div>
            </a>
            <a href="/videos" onClick="Page.Go(this.href); return false;">
                <div class="headm_posic"><img src="{theme}/images/spacer.gif" class="headm_ic_videos"
                                              alt="spacer"/><br/>
                    Видео
                </div>
            </a>
            <a href="/audio" onClick="doLoad.js(0); player.open(); return false;">
                <div class="headm_posic"><img src="/templates/Default/images/spacer.gif" class="headm_ic_music"
                                              id="fplayer_pos" alt="spacer"/><br/>
                    Музыка
                </div>
            </a>
            <a href="{groups-link}" onClick="Page.Go(this.href); return false;" id="new_groups_lnk">
                <div class="headm_posic">
                    <div id="new_groups">{new_groups}</div>
                    <img src="{theme}/images/spacer.gif" class="headm_ic_groups" alt="spacer"/><br/>
                    Группы
                </div>
            </a>
            <a href="/news{news-link}" onClick="Page.Go(this.href); return false;" id="news_link">
                <div class="headm_posic">
                    <div id="new_news">{new-news}</div>
                    <img src="{theme}/images/spacer.gif" class="headm_ic_news" alt="spacer"/><br/>
                    Лента
                </div>
            </a>
            <a href="/notes" onClick="Page.Go(this.href); return false;">
                <div class="headm_posic"><img src="{theme}/images/spacer.gif" class="headm_ic_notes"
                                              alt="spacer"/><br/>
                    Заметки
                </div>
            </a>
            <a href="/" onClick="gSearch.open_tab(); return false;" id="se_link">
                <div class="headm_posic"><img src="{theme}/images/spacer.gif" class="headm_ic_se" alt="spacer"/><br/>
                    Поиск
                </div>
            </a>
            <a href="/settings" onClick="Page.Go(this.href); return false;">
                <div class="headm_posic"><img src="{theme}/images/spacer.gif" class="headm_ic_settings"
                                              alt="spacer"/><br/>
                    Настройки
                </div>
            </a>
            <a href="/support" onClick="Page.Go(this.href); return false;">
                <div class="headm_posic">
                    <div id="new_support">{new-support}</div>
                    <img src="{theme}/images/spacer.gif" class="headm_ic_support" alt="spacer"/><br/>
                    Помощь
                </div>
            </a>
            <a href="{ubm-link}" onClick="Page.Go(this.href); return false;" id="ubm_link">
                <div class="headm_posic">
                    <div id="new_ubm">{new-ubm}</div>
                    <img src="{theme}/images/spacer.gif" class="headm_ic_ubm" alt="spacer"/><br/>
                    Баланс
                </div>
            </a>
            <a href="/?act=logout">
                <div class="headm_posic">
                    <img src="{theme}/images/spacer.gif" class="headm_ic_logout" alt="spacer"/><br/>
                    Выйти
                </div>
            </a>
            [/logged]
            [not-logged]
            <a href="#" onclick="reg.box();">
                <div class="headm_posic btn_headmenu">
                    Войти
                </div>
            </a>
            [/not-logged]
        </div>
        <!--search-->
        <div class="search_tab no_display" id="search_tab">
            <input type="text" value="Поиск" class="fave_input search_input"
                   onBlur="if(this.value=='') this.value='Поиск';this.style.color = '#c1cad0';"
                   onFocus="if(this.value=='Поиск')this.value='';this.style.color = '#000'"
                   onKeyPress="if(event.keyCode == 13) gSearch.go();"
                   onKeyUp="FSE.Txt()"
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
            <div class="button_div fl_l margin_left">
                <button onClick="gSearch.go(); return false" id="se_but">Найти</button>
            </div>
        </div>
        <div class="fast_search_bg no_display">
            <a href="/" style="padding:12px;background:#eef3f5" onClick="gSearch.go(); return false"
               onMouseOver="FSE.ClrHovered(this.id)" id="all_fast_res_clr1">
                <text>Искать</text>
                <b id="fast_search_txt"></b>
                <div class="fl_r fast_search_ic"></div>
            </a>
            <span id="reFastSearch"></span>
        </div>
        <!--/search-->
    </div>
</div>
<div class="clear"></div>
<div style="margin-top:44px;"></div>
<div class="autowr">
    <div class="content" style="width:800px;">
        <div class="shadow">
            <div class="speedbar no_display" id="">{speedbar}</div>
        <div class="padcont">
            <div id="audioPlayer"></div>
            <div id="page">{info}{content}</div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="footer">
        Vii Engine &copy; 2022 <a class="cursor_pointer" onClick="trsn.box()"
                                  onMouseOver="myhtml.title('1', 'Выбор используемого языка на сайте', 'langTitle', 1)"
                                  id="langTitle1">{lang}</a>
        <div class="fl_r">
            <a href="/index.php?act=change_mobile">мобильная версия</a>
            <a href="/index.php?go=search&online=1" onClick="Page.Go(this.href); return false">люди</a>
            <a href="/index.php?go=search&type=2" onClick="Page.Go(this.href); return false">видео</a>
            <a href="/index.php?go=search&type=5" onClick="Page.Go(this.href); return false">музыка</a>
            <a href="/support?act=new" onClick="Page.Go(this.href); return false">помощь</a>
            <a href="/reviews" onClick="Page.Go(this.href); return false">отзывы</a>
            <a href="/blog" onClick="Page.Go(this.href); return false">блог</a>
        </div>
    </div>
    </div>
</div>
[logged]
<script type="text/javascript" src="{theme}/js/push.js"></script>
<div class="no_display">
    <audio id="beep-three" controls preload="auto">
        <source src="{theme}/images/soundact.ogg">
    </audio>
</div>
<div id="updates"></div>
[/logged]
<div class="clear"></div>
</body>
</html>