{url_img}
<script type="text/javascript">
    var startResizeCss = false;
    var user_id = '{user-id}';
    $(document).ready(function () {
        $(window).scroll(function () {
            if ($('#type_page').val() == 'profile') {
                if ($(document).height() - $(window).height() <= $(window).scrollTop() + ($(document).height() / 2 - 250)) {
                    wall.page(user_id);
                }
                if ($(window).scrollTop() < $('#fortoAutoSizeStyleProfile').offset().top) {
                    startResizeCss = false;
                    $('#addStyleClass').remove();
                }
                if ($(window).scrollTop() > $('#fortoAutoSizeStyleProfile').offset().top && !startResizeCss) {
                    startResizeCss = true;
                    $('body').append('<div id="addStyleClass"><style type="text/css" media="all">.wallrecord{width:822px;margin-left:-210px}.infowalltext_f{font-size:11px}.wall_inpst{width:738px}.public_likes_user_block{margin-left:610px}.wall_fast_opened_form{width:788px;margin-left:-150px}.wall_fast_block{width:780px;margin-left:-150px}.public_wall_all_comm{width:780px;margin-left:-150px}.player_mini_mbar_wall{width:710px;margin-bottom:0px}#audioForSize{min-width:700px}.wall_rec_autoresize{width:760px}.wall_fast_ava img{width:50px}.wall_fast_ava{width:60px}.wall_fast_comment_text{margin-left:57px}.wall_fast_date{margin-left:57px;font-size:11px}.size10{font-size:11px}.fast_form_width2{width:778px}.audio_onetrack, .player_mini_mbar{width:760px}</style></div>');
                }
            }
        });
        $('#wall_text, .fast_form_width').autoResize();
        {*        [owner]
        if ($('.profile_onefriend_happy').size() > 4) $('#happyAllLnk').show();
        [/owner]*}
    });
    $(document).click(function (event) {
        wall.event(event);
    });

    function nshb() {
        $('#nshb').text('Скрыть блоки').attr('onClick', 'nshbb(); return false');
        $('.b_friends, .b_friends_online, .b_people, .b_pages, .b_video, .b_audio, .b_notes, .b_albums, .b_gifts, .b_photo, .b_wall').show();
    }

    function nshbb() {
        $('#nshb').text('Показать скрытые блоки').attr('onClick', 'nshb(); return false');
        $('.b_friends, .b_friends_online, .b_people, .b_pages, .b_video, .b_audio, .b_notes, .b_albums, .b_wall, .b_gifts[owner], .b_photo[/owner]').hide();
    }
</script>
<input type="hidden" id="type_page" value="profile"/>
<style>
    .newcolor000{color:#000} .audio_onetrack, .player_mini_mbar{width:560px} .wall_none

    {margin-top:10px}
</style>
<div id="jquery_jplayer"></div>
<input type="hidden" id="teck_id" value=""/>
<input type="hidden" id="teck_prefix" value=""/>
<input type="hidden" id="typePlay" value="standart"/>
<div class="ava">
    <div class="b_photo {b_photo}">
        <div id="ava">
            <img src="{ava}" alt="page_enlarge" id="ava_{user-id}" />
        </div>
    </div>
    <div class="menuleft2" style="margin-top:-3px">
        [owner]<a href="/docs" onClick="Page.Go(this.href); return false;">
            <div class="fl_l pr_ic_docs"></div>
            <div>Мои документы</div>
        </a>
        <a href="/editmypage" onClick="Page.Go(this.href); return false;">
            <div class="fl_l pr_ic_edit"></div>
            <div>Редактировать страницу</div>
        </a>
        <a href="/" onClick="Profile.LoadPhoto(); return false;">
            <div class="fl_l pr_ic_ph"></div>
            <div>Изменить фотографию</div>
        </a>
        <a href="/" onClick="Profile.miniature(); return false;">
            <div class="fl_l pr_ic_mini"></div>
            <div>Изменить миниатюру</div>
        </a>
        <a href="/" onClick="Profile.DelPhoto(); return false;" id="del_pho_but" {display-ava}>
            <div class="fl_l pr_ic_del"></div>
            <div>Удалить фотографию</div>
        </a>[/owner]
        [not-owner][blacklist][privacy-msg]<a href="/" onClick="messages.new_({user-id}); return false">
            <div>Отправить сообщение</div>
        </a>[/privacy-msg][/blacklist]
        [no-friends][blacklist]<a href="/" onClick="friends.add({user-id}); return false">
            <div>Добавить в друзья</div>
        </a>[/blacklist][/no-friends]
        [yes-friends]<a href="/" onClick="friends.delet({user-id}, 1); return false">
            <div>Убрать из друзей</div>
        </a>[/yes-friends]
{*        [yes-demand]<a href="/" onClick="friends.add({user-id}); return false">*}
{*            <div>Принять дружбу</div>*}
{*        </a>[/yes-demand]*}
        [blacklist][no-subscription]<a href="/" onClick="subscriptions.add({user-id}); return false"
                                       id="lnk_unsubscription">
            <div><span id="text_add_subscription">Подписаться на обновления</span> <img
                        src="/images/loading_mini.gif" alt="" id="addsubscription_load"
                        class="no_display" style="margin-right:-13px"/></div>
        </a>[/no-subscription][/blacklist]
        [yes-subscription]<a href="/" onClick="subscriptions.del({user-id}); return false" id="lnk_unsubscription">
            <div><span id="text_add_subscription">Отписаться от обновлений</span> <img
                        src="/images/loading_mini.gif" alt="" id="addsubscription_load"
                        class="no_display" style="margin-right:-13px"/></div>
        </a>[/yes-subscription]
        <a href="/" onClick="gifts.box('{user-id}'); return false">
            <div>Отправить подарок</div>
        </a>
        [no-fave]<a href="/" onClick="fave.add({user-id}); return false" id="addfave_but">
            <div><span id="text_add_fave">Добавить в закладки</span> <img
                        src="/images/loading_mini.gif" alt="" id="addfave_load" class="no_display"/>
            </div>
        </a>[/no-fave]
        [yes-fave]<a href="/" onClick="fave.delet({user-id}); return false" id="addfave_but">
            <div><span id="text_add_fave">Удалить из закладок</span> <img
                        src="/images/loading_mini.gif" alt="" id="addfave_load" class="no_display"/>
            </div>
        </a>[/yes-fave]
        [no-blacklist]<a href="/" onClick="settings.addblacklist({user-id}); return false" id="addblacklist_but">
            <div><span id="text_add_blacklist">Заблокировать</span> <img
                        src="/images/loading_mini.gif" alt="" id="addblacklist_load"
                        class="no_display"/></div>
        </a>[/no-blacklist]
        [yes-blacklist]<a href="/" onClick="settings.delblacklist({user-id}, 1); return false" id="addblacklist_but">
            <div><span id="text_add_blacklist">Разблокировать</span> <img
                        src="/images/loading_mini.gif" alt="" id="addblacklist_load"
                        class="no_display"/></div>
        </a>[/yes-blacklist]
{*        <a class="cursor_pointer" onClick="transmit.box('{user-id}')">*}
{*            <div>Передать mix</div>*}
{*        </a>*}
        [/not-owner]
        <a href="/" id="nshb" onClick="nshb(); return false" style="margin-bottom:1px">
            <div><span id="text_add_blacklist">Показать скрытые блоки</span></div>
        </a>
    </div>
    [blacklist]
    <div class="leftcbor">
        [owner][happy-friends]
        <div id="happyBLockSess">
            <div class="albtitle">Дни рожденья друзей <span>{happy-friends-num}</span>
                <div class="profile_happy_hide">
                    <img src="/images/hide_lef.gif"
                         onMouseOver="myhtml.title('1', 'Скрыть', 'happy_block_')"
                         id="happy_block_1" onClick="HappyFr.HideSess(); return false"/>
                </div>
            </div>
            <div class="newmesnobg profile_block_happy_friends"
                 style="padding:0px;padding-left:4px;padding-top:5px;">{happy-friends}
                <div class="clear"></div>
            </div>
            <div class="cursor_pointer no_display" onMouseDown="HappyFr.Show(); return false" id="happyAllLnk">
                <div class="public_wall_all_comm profile_block_happy_friends_lnk">Показать все</div>
            </div>
        </div>
        [/happy-friends][/owner]

        [common-friends]<a href="/friends/common/{user-id}" style="text-decoration:none"
                           onClick="Page.Go(this.href); return false">
            <div class="albtitle">
                <div class="profile_ic_frieds fl_l"></div>
                Общие друзья <span>{mutual-num}</span></div>
        </a>
        <div class="newmesnobg" style="padding:0px;padding-top:10px;">{mutual_friends}
            <div class="clear"></div>
        </div>
        [/common-friends]
        [friends]
        <div class="b_friends {b_friends}">
            <a href="/friends/{user-id}" onClick="Page.Go(this.href); return false" style="text-decoration:none">
                <div class="albtitle">
                    <div class="profile_ic_frieds fl_l"></div>
                    Друзья <span>{friends-num}</span>
                </div>
            </a>
            <div class="newmesnobg" style="padding:0px;padding-top:10px;">{friends}
                <div class="clear"></div>
            </div>
        </div>
        [/friends]
        [online-friends]
        <div class="b_friends_online {b_friends_online}">
            <a href="/friends/online/{user-id}" onClick="Page.Go(this.href); return false" style="text-decoration:none">
                <div class="albtitle">
                    <div class="profile_ic_frieds fl_l"></div>
                    Друзья на сайте <span>{online-friends-num}</span>
                </div>
            </a>
            <div class="newmesnobg" style="padding:0px;padding-top:10px;">{online-friends}
                <div class="clear"></div>
            </div>
        </div>
        [/online-friends]
        [subscriptions]
        <div class="b_people {b_people}">
            <a href="/" onClick="subscriptions.all({user-id}, '', {subscriptions-num}); return false"
               style="text-decoration:none">
                <div class="albtitle">
                    <div class="profile_ic_frieds fl_l"></div>
                    Интересные люди <span>{subscriptions-num}</span>
                </div>
            </a>
            <div class="newmesnobg" style="padding-right:0px;padding-bottom:0px;">{subscriptions}
                <div class="clear"></div>
            </div>
        </div>
        [/subscriptions]
        [groups]
        <div class="b_pages {b_pages}">
            <div class="albtitle cursor_pointer" onClick="groups.all_groups_user('{user-id}')">
                <div class="profile_ic_pages fl_l"></div>
                Подписки <span id="groups_num">{groups-num}</span>
            </div>
            <div class="newmesnobg" style="padding-right:0px;padding-bottom:0px;">{groups}
                <div class="clear"></div>
            </div>
        </div>
        [/groups]
        [videos]
        <div class="b_video {b_video}">
            <a href="/videos/{user-id}" onClick="Page.Go(this.href); return false" style="text-decoration:none">
                <div class="albtitle">
                    <div class="profile_ic_videos fl_l"></div>
                    Видеозаписи <span>{videos-num}</span>
                </div>
            </a>
            <div class="newmesnobg" style="padding-right:0px;padding-bottom:0px;">{videos}
                <div class="clear"></div>
            </div>
        </div>
        [/videos]
        [notes]
        <div class="{b_notes}">
            <a href="/notes/{user-id}" onClick="Page.Go(this.href); return false" style="text-decoration:none">
                <div class="albtitle">
                    <div class="profile_ic_notes fl_l"></div>
                    Заметки <span>{notes-num}</span>
                </div>
            </a>
            <div class="newmesnobg" style="padding-right:0px;padding-left:5px">{notes}
                <div class="clear"></div>
            </div>
        </div>
        [/notes]
        <div class="clear"></div>
        <span id="fortoAutoSizeStyleProfile"></span>
    </div>
    [/blacklist]
</div>
<div class="profiewr">
    [owner]
    <div class="set_status_bg no_display" id="set_status_bg">
        <input type="text" id="status_text" class="status_inp" value="{status-text}" style="width:645px"
               maxlength="255" onKeyPress="if(event.keyCode == 13)gStatus.set()"/>
        <div class="fl_l status_text">
            <span class="no_status_text [status]no_display[/status]">Введите здесь текст Вашего статуса.</span>
            <a href="/" class="yes_status_text [no-status]no_display[/no-status]"
               onClick="gStatus.set(1); return false">Удалить статус</a>
        </div>
        [status]
        <div class="button_div_gray fl_r status_but margin_left">
            <button>Отмена</button>
        </div>
        [/status]
        <div class="button_div fl_r status_but">
            <button id="status_but" onClick="gStatus.set()">Сохранить</button>
        </div>
    </div>
    [/owner]

    <div class="titleu">{name} {lastname}</div>

    <div class="profile_rate_pos">
        <div class="profile_rate_text">&nbsp;</div>
        [owner]<a class="cursor_pointer" onClick="rating.view()">[/owner]
            <div class="profile_rate_100_left {rating-class-left}"></div>
            [owner]</a>[/owner]
        <div class="profile_rate_add" onClick="rating.addbox('{user-id}')"
             onMouseOver="myhtml.title('1', 'Повысить рейтинг', 'rate', 1)" id="rate1">
            <img src="/images/icons/rate_ic.png"/></div>
        [owner]<a class="cursor_pointer" onClick="rating.view()" style="text-decoration:none">[/owner]
            <div class="profile_rate_100_right {rating-class-right}"></div>
            <div class="profile_rate_100_head {rating-class-head}" id="profile_rate_num">{rating}</div>
            [owner]</a>[/owner]
    </div>
    [not-owner][status]
    <div class="status_tri"></div>
    <div class="status border_radius_5">[status][/not-owner]
        [owner]
        <div class="status_tri"></div>
        <div class="status border_radius_5">[/owner]
            <div>
                [owner]<a href="/" id="new_status" onClick="gStatus.open(); return false">[/owner]
                    [blacklist]{status-text}[/blacklist]
                    [owner]</a>[/owner]

            </div>
            [owner]<span id="tellBlockPos"></span>
            <div class="status_tell_friends no_display" style="z-index:200">
                <div class="status_str"></div>
                <div class="html_checkbox" id="tell_friends" onClick="myhtml.checkbox(this.id); gStatus.startTell()">
                    Рассказать друзьям
                </div>
            </div>
            [/owner]
            [owner]
            <a href="#" onClick="gStatus.open(); return false"
               id="status_link" [status]class="no_display" [/status]>установить
            статус</a>[/owner]
            [not-owner][status]
        </div>
        [/status][/not-owner]
        [owner]
    </div>
    [/owner]
    <div class="clear"></div>
    <div class="page_bg border_radius_5 margin_top_10">
        <div class="fl_r online"><b>{online}</b></div>
        [not-all-country]
        <div class="flpodtext">Страна:</div>
        <div class="flpodinfo">
            <a href="/?go=search&country={country-id}" onClick="Page.Go(this.href); return false">{country}</a>
        </div>
        [/not-all-country]
        [not-all-city]
        <div class="flpodtext">Город:</div>
        <div class="flpodinfo">
            <a href="/?go=search&country={country-id}&city={city-id}"
               onClick="Page.Go(this.href); return false">{city}</a>
        </div>
        [/not-all-city]
        [blacklist][not-all-birthday]
        <div class="flpodtext">День рождения:</div>
        <div class="flpodinfo">{birth-day}</div>
        [/not-all-birthday]
        [privacy-info][sp]
        <div class="flpodtext">Семейное положение:</div>
        <div class="flpodinfo">{sp}</div>
        [/sp][/privacy-info]
        <div class="cursor_pointer" onClick="Profile.MoreInfo(); return false" id="moreInfoLnk">
            <div class="profile_hide_opne" id="moreInfoText">Показать подробную информацию</div>
        </div>
        <div id="moreInfo" class="no_display">
            [privacy-info][not-block-contact]
            <div class="fieldset">
                <div class="w2_a">Контактная информация
                    [owner]
                    <span>
                        <a href="/editmypage/contact" onClick="Page.Go(this.href); return false;">редактировать</a>
                    </span>
                    [/owner]
                </div>
            </div>
            [not-contact-phone]
            <div class="flpodtext">Моб. телефон:</div>
            <div class="flpodinfo">{phone}</div>
            [/not-contact-phone]
            [not-contact-vk]
            <div class="flpodtext">В контакте:</div>
            <div class="flpodinfo">{vk}</div>
            [/not-contact-vk]
            [not-contact-od]
            <div class="flpodtext">Одноклассники:</div>
            <div class="flpodinfo">{od}</div>
            [/not-contact-od]
            [not-contact-fb]
            <div class="flpodtext">FaceBook:</div>
            <div class="flpodinfo">{fb}</div>
            [/not-contact-fb]
            [not-contact-skype]
            <div class="flpodtext">Skype:</div>
            <div class="flpodinfo"><a href="skype:{skype}">{skype}</a></div>
            [/not-contact-skype]
            [not-contact-icq]
            <div class="flpodtext">ICQ:</div>
            <div class="flpodinfo">{icq}</div>
            [/not-contact-icq]
            [not-contact-site]
            <div class="flpodtext">Веб-сайт:</div>
            <div class="flpodinfo"><a href="/away.php?url={site}" title="Перейти на сайт" target="_blank">{site}</a>
            </div>
            [/not-contact-site][/not-block-contact]
            <div class="fieldset">
                <div class="w2_b">Личная информация
                    [owner]
                    <span>
                        <a href="/editmypage/interests" onClick="Page.Go(this.href); return false;">редактировать</a>
                    </span>
                    [/owner]
            </div>
        </div>{not-block-info}
        [not-info-activity]
        <div class="flpodtext">Деятельность:</div>
        <div class="flpodinfo">{activity}</div>
        [/not-info-activity]
        [not-info-interests]
        <div class="flpodtext">Интересы:</div>
        <div class="flpodinfo">{interests}</div>
        [/not-info-interests]
        [not-info-music]
        <div class="flpodtext">Любимая музыка:</div>
        <div class="flpodinfo">{music}</div>
        [/not-info-music]
        [not-info-kino]
        <div class="flpodtext">Любимые фильмы:</div>
        <div class="flpodinfo">{kino}</div>
        [/not-info-kino]
        [not-info-books]
        <div class="flpodtext">Любимые книги:</div>
        <div class="flpodinfo">{books}</div>
        [/not-info-books]
        [not-info-games]
        <div class="flpodtext">Любимые игры:</div>
        <div class="flpodinfo">{games}</div>
        [/not-info-games]
        [not-info-quote]
        <div class="flpodtext">Любимые цитаты:</div>
        <div class="flpodinfo">{quote}</div>
        [/not-info-quote]
        [not-info-myinfo]
        <div class="flpodtext">О себе:</div>
        <div class="flpodinfo">{myinfo}</div>
        [/not-info-myinfo][/privacy-info]
    </div>

</div>

[albums]
<div class="b_albums {b_albums}">
    <div class="page_bg border_radius_5 margin_top_10">
        <a href="/albums/{user-id}" onClick="Page.Go(this.href); return false" style="text-decoration:none">
            <div class="albtitle albtitle2">
                <div class="profile_ic_albums fl_l"></div>
                Альбомы <span>{albums-num}</span></div>
        </a>{albums}
        <div class="clear"></div>
    </div>
</div>[/albums]
[audios]
<div class="b_audio {b_audio}">
    <div class="page_bg border_radius_5 margin_top_10" style="padding-bottom:10px">
        <div id="jquery_jplayer"></div>
        <input type="hidden" id="teck_id" value="1"/>
        <a href="/audio{user-id}" onClick="Page.Go(this.href); return false" style="text-decoration:none">
            <div class="albtitle albtitle2">
                <div class="profile_ic_audios fl_l">
                </div>{audios-num}</div>
        </a>{audios}
        <div class="clear"></div>
    </div>
</div>[/audios]
[gifts]
<div class="b_gifts {b_gifts}">
    <div class="page_bg border_radius_5 margin_top_10">
        <a href="/gifts{user-id}" onClick="Page.Go(this.href); return false" style="text-decoration:none">
            <div class="albtitle  albtitle2">
                <div class="profile_ic_gifts fl_l"></div>{gifts-text}</div>
            <center>{gifts}</center>
            <div class="clear"></div>
        </a></div>
</div>[/gifts]

<div class="b_wall {b_wall}">
    <div class="page_bg border_radius_5 margin_top_10 page_bg_wall"
         style="padding-bottom:15px">
        <a href="/wall{user-id}" onClick="Page.Go(this.href); return false" style="text-decoration:none">
            <div class="albtitle albtitle2" style="border-bottom:0">Стена <span
                        id="wall_rec_num">{wall-rec-num}</span></div>
        </a>
        [privacy_wall]
        <div class="newmes" id="wall_tab"
             style="border-bottom:0px;margin-left:-13px;margin-top:-15px;margin-bottom:-10px">
            <input type="hidden" value="[owner]Что у Вас нового?[/owner][not-owner]Написать сообщение...[/not-owner]"
                   id="wall_input_text"/>
            <input type="text" class="msg_se_inp"
                   value="[owner]Что у Вас нового?[/owner][not-owner]Написать сообщение...[/not-owner]"
                   onMouseDown="wall.form_open(); return false" id="wall_input" style="width:600px"/>
            <div class="no_display" id="wall_textarea">
   <textarea id="wall_text" class="wall_inpst wall_fast_opened_texta"
             style="width:612px"
             onKeyUp="wall.CheckLinkText(this.value)"
             onBlur="wall.CheckLinkText(this.value, 1)"
   >
   </textarea>
                <div id="attach_files" class="margin_top_10 no_display"></div>
                <div id="attach_block_lnk" class="no_display clear">
                    <div class="attach_link_bg">
                        <div align="center" id="loading_att_lnk">
                            <img src="/images/loading_mini.gif" style="margin-bottom:-2px"/>
                        </div>
                        <img src="" align="left" id="attatch_link_img" class="no_display cursor_pointer"
                             onClick="wall.UrlNextImg()"/>
                        <div id="attatch_link_title"></div>
                        <div id="attatch_link_descr"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="attach_toolip_but"></div>
                    <div class="attach_link_block_ic fl_l"></div>
                    <div class="attach_link_block_te">
                        <div class="fl_l">Ссылка: <a href="/" id="attatch_link_url" target="_blank"></a></div>
                        <img class="fl_l cursor_pointer" style="margin-top:2px;margin-left:5px"
                             src="/images/close_a.png"
                             onMouseOver="myhtml.title('1', 'Не прикреплять', 'attach_lnk_')" id="attach_lnk_1"
                             onClick="wall.RemoveAttachLnk()"/></div>
                    <input type="hidden" id="attach_lnk_stared"/>
                    <input type="hidden" id="teck_link_attach"/>
                    <span id="urlParseImgs" class="no_display"></span>
                </div>
                <div class="clear"></div>
                <div id="attach_block_vote" class="no_display">
                    <div class="attach_link_bg">
                        <div class="texta">Тема опроса:</div>
                        <input type="text" id="vote_title" class="inpst" maxlength="80" value=""
                               style="width:355px;margin-left:5px"
                               onKeyUp="$('#attatch_vote_title').text(this.value)"
                        />
                        <div class="mgclr"></div>
                        <div class="texta">Варианты ответа:<br/>
                            <small>
                                <span id="addNewAnswer">
                                    <a class="cursor_pointer" onClick="Votes.AddInp()">добавить</a>
                                </span>
                                | <span id="addDelAnswer">удалить</span>
                            </small>
                        </div>
                        <input type="text" id="vote_answer_1" class="inpst" maxlength="80" value=""
                               style="width:355px;margin-left:5px"/>
                        <div class="mgclr"></div>
                        <div class="texta">&nbsp;</div>
                        <input type="text" id="vote_answer_2" class="inpst" maxlength="80" value=""
                               style="width:355px;margin-left:5px"/>
                        <div class="mgclr"></div>
                        <div id="addAnswerInp"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="attach_toolip_but"></div>
                    <div class="attach_link_block_ic fl_l"></div>
                    <div class="attach_link_block_te">
                        <div class="fl_l">Опрос: <a id="attatch_vote_title"
                                                    style="text-decoration:none;cursor:default"></a>
                        </div>
                        <img class="fl_l cursor_pointer" style="margin-top:2px;margin-left:5px"
                             src="/images/close_a.png"
                             onMouseOver="myhtml.title('1', 'Не прикреплять', 'attach_vote_')" id="attach_vote_1"
                             onClick="Votes.RemoveForAttach()"/>
                    </div>
                    <input type="hidden" id="answerNum" value="2"/>
                </div>
                <div class="clear"></div>
                <input id="vaLattach_files" type="hidden"/>
                <div class="clear"></div>
                <div class="button_div fl_l margin_top_10">
                    <button onClick="wall.send(); return false" id="wall_send">Отправить</button>
                </div>
                <div class="wall_attach fl_r" style="margin-right:-14px"
                     onMouseOver="wall.attach_menu('open', this.id, 'wall_attach_menu')"
                     onMouseOut="wall.attach_menu('close', this.id, 'wall_attach_menu')" id="wall_attach">Прикрепить
                </div>
                <div class="wall_attach_menu no_display"
                     onMouseOver="wall.attach_menu('open', 'wall_attach', 'wall_attach_menu')"
                     onMouseOut="wall.attach_menu('close', 'wall_attach', 'wall_attach_menu')" id="wall_attach_menu">
                    <div class="wall_attach_icon_smile border_radius_5" id="wall_attach_link"
                         onClick="wall.attach_addsmile()">Смайлик
                    </div>
                    <div class="wall_attach_icon_photo border_radius_5" id="wall_attach_link"
                         onClick="wall.attach_addphoto()">Фотографию
                    </div>
                    <div class="wall_attach_icon_video border_radius_5" id="wall_attach_link"
                         onClick="wall.attach_addvideo()">Видеозапись
                    </div>
                    <div class="wall_attach_icon_audio border_radius_5" id="wall_attach_link"
                         onClick="wall.attach_addaudio()">Аудиозапись
                    </div>
                    <div class="wall_attach_icon_doc border_radius_5" id="wall_attach_link"
                         onClick="wall.attach_addDoc()">Документ
                    </div>
                    <div class="wall_attach_icon_vote border_radius_5" id="wall_attach_link"
                         onClick="$('#attach_block_vote').slideDown('fast');wall.attach_menu('close', 'wall_attach', 'wall_attach_menu');$('#vote_title').focus();$('#vaLattach_files').val($('#vaLattach_files').val()+'vote|start||')">
                        Опрос
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        [/privacy_wall]
    </div>

    <div id="wall_records">{records}[no-records]
        <div class="wall_none">На стене пока нет ни одной записи.
    </div>
    [/no-records]
</div>
[wall-link]<span id="wall_all_record"></span>
<div onClick="wall.page('{user-id}'); return false" id="wall_l_href" class="cursor_pointer">
    <div class="doc_all_but margin_top_10 border_radius_5" id="wall_link">к предыдущим записям</div>
</div>[/wall-link][/blacklist]
</div>

[not-blacklist]
<div class="err_yellow" style="font-weight:normal;margin-top:5px">{name} ограничил доступ к своей странице.
</div>[/not-blacklist]
</div>
<div class="clear"></div>
