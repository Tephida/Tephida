<script type="text/javascript">
    var startResizeCss = false;
    $(document).ready(function () {
        {*[admin]Xajax = new AjaxUpload('upload_3', {
            action: '/index.php?go=groups&act=upload&public_id={id}',
            name: 'uploadfile',
            onSubmit: function (file, ext) {
                if(!(ext && /^(doc|docx|xls|xlsx|ppt|pptx|rtf|pdf|png|jpg|gif|psd|mp3|djvu|fb2|ps|jpeg|txt)$/.test(ext))) {
                    addAllErr('Неверный формат файла', 3300);
                    return false;
                }
                Page.Loading('start');
            },
            onComplete: function (file, row){
                if(row == 1)
                    addAllErr('Превышен максимальный размер файла 5 МБ', 3300);
                else {
                    window.location.href = window.location.href;
                }
                Page.Loading('stop');
            }
        });
        $('#wall_text, .fast_form_width').autoResize();
        myhtml.checked(['{settings-comments}', '{settings-discussion}', '{background_repeat}']);[/admin]*}
        $('#wall_text, .fast_form_width').autoResize();
        myhtml.checked(['{settings-comments}', '{settings-discussion}']);
        $(window).scroll(function () {
            if ($('#type_page').val() == 'public') {
                if ($(document).height() - $(window).height() <= $(window).scrollTop() + ($(document).height() / 2 - 250)) {
                    groups.wall_page();
                }
                if ($(window).scrollTop() < $('#fortoAutoSizeStyle').offset().top) {
                    startResizeCss = false;
                    $('#addStyleClass').remove();
                }
                if ($(window).scrollTop() > $('#fortoAutoSizeStyle').offset().top && !startResizeCss) {
                    startResizeCss = true;
                    $('body').append('<div id="addStyleClass"><style type="text/css" media="all">.wallrecord{width:822px;margin-left:-210px}.infowalltext_f{font-size:11px}.wall_inpst{width:738px}.public_likes_user_block{margin-left:610px}.wall_fast_opened_form{width:788px;margin-left:-150px}.wall_fast_block{width:780px;margin-left:-150px}.public_wall_all_comm{width:840px;margin-left:-210px}.player_mini_mbar_wall{width:710px;margin-bottom:0px}#audioForSize{min-width:700px}.wall_rec_autoresize{width:760px}.wall_fast_ava img{width:50px}.wall_fast_ava{width:60px}.wall_fast_comment_text{margin-left:57px}.wall_fast_date{margin-left:57px;font-size:11px}.size10{font-size:11px}.fast_form_width2{width:778px}.audio_onetrack, .player_mini_mbar{width:760px}</style></div>');
                }
            }
        });
        Page.langNumric('langForum', '{forum-num}', 'обсуждение', 'обсуждения', 'обсуждений', 'обсуждение', 'Нет обсуждений');
        Page.langNumric('langNumricAll', '{audio-num}', 'аудиозапись', 'аудиозаписи', 'аудиозаписей', 'аудиозапись', 'аудиозаписей');
    });
    $(document).click(function (event) {
        wall.event(event);
    });
</script>
<input type="hidden" id="type_page" value="public"/>
<style>
    .newcolor000{color:#000} .audio_onetrack, .player_mini_mbar{width:560px} .wall_none

    {margin-top:10px}
</style>
<div id="jquery_jplayer"></div>
<div id="addStyleClass"></div>
<input type="hidden" id="teck_id" value=""/>
<input type="hidden" id="teck_prefix" value=""/>
<input type="hidden" id="typePlay" value="standart"/>
<input type="hidden" id="public_id" value="{id}"/>
<div class="ava fl_r" style="margin-right:0px" onMouseOver="groups.wall_like_users_five_hide()">
    <img src="{photo}" id="ava"/>
    <div class="menuleft" style="margin-top:-2px">
        [admin]<a href="/" onClick="groups.loadphoto('{id}'); return false">
            <div>Изменить фотографию</div>
        </a>
        <span id="del_pho_but" class="{display-ava}">
   <a href="/" onClick="groups.delphoto('{id}'); return false;"><div>Удалить фотографию</div></a>
  </span>
        <a href="/" onClick="groups.editform(); return false">
            <div>Управление страницей</div>
        </a>
        [/admin]
        <a href="/" onClick="groups.inviteBox('{id}'); return false">
            <div>Пригласить друзей</div>
        </a>
    </div>
    <div class="publick_subscblock">
        <div id="yes" class="{yes}">
            <div class="button_div fl_l" style="margin-bottom:15px;line-height:15px">
                <button onClick="groups.login('{id}'); return false" style="width:174px">Подписаться</button>
            </div>
            <div id="num2">{num-2}</div>
        </div>
        <div id="no" class="{no}" style="text-align:left">
            Вы подписаны на новости этого сообщества.<br/>
            <div style="margin-top:7px"></div>
            <a href="/public{id}" onClick="groups.exit2('{id}', '{viewer-id}'); return false">Отписаться</a>
        </div>
        <div class="clear"></div>
    </div>
    <div style="margin-top:7px">
        <div class="{no-users}" id="users_block">
            <div class="albtitle cursor_pointer" onClick="groups.all_people('{id}')">Подписчики</div>
            <div class="public_bg">
                <div class="color777 public_margbut">{num}</div>
                <div class="public_usersblockhidden" style="margin-left:-5px">{users}</div>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    [feedback]
    <div class="albtitle cursor_pointer" onClick="groups.allfeedbacklist('{id}')">Контакты [yes][admin]<a
                href="/public{id}" class="fl_r" onClick="groups.allfeedbacklist('{id}'); return false">ред.</a>[/admin][/yes]
    </div>
    <div class="public_bg" id="feddbackusers">
        [yes]
        <div class="color777 public_margbut">{num-feedback}</div>
        [/yes]
        {feedback-users}
        [no]
        <div class="line_height color777" align="center">Страницы представителей, номера телефонов, e-mail<br/>
            <a href="/public{id}" onClick="groups.addcontact('{id}'); return false">Добавить контакты</a></div>
        [/no]
    </div>
    [/feedback]
    [audios]
    <div class="albtitle cursor_pointer" onClick="Page.Go('/public/audio{id}'); return false">Аудиозаписи</div>
    <div class="public_bg">
        [yesaudio]
        <div class="color777 public_margbut">{audio-num} <span id="langNumricAll"></span></div>
        [/yesaudio]
        {audios}
        [noaudio]
        <div class="line_height color777" align="center">Композиции или другие аудиоматериалы<br/>
            <a href="/public/audio{id}" onClick="Page.Go(this.href); return false">Добавить аудиозапись</a></div>
        [/noaudio]
    </div>
    [/audios]
    <div id="fortoAutoSizeStyle"></div>
</div>
<div class="profiewr profiewr2">
    <div id="public_editbg_container">
        <div class="public_editbg_container">
            <div class="fl_l" style="width:650px">
                [admin]
                <div class="set_status_bg no_display" id="set_status_bg">
                    <input type="text" id="status_text" class="status_inp" value="{status-text}" style="width:645px;"
                           maxlength="255" onKeyPress="if(event.keyCode == 13)gStatus.set('', 1)"/>
                    <div class="fl_l status_text"><span class="no_status_text [status]no_display[/status]">Введите здесь текст статуса.</span><a
                                href="/" class="yes_status_text [no-status]no_display[/no-status]"
                                onClick="gStatus.set(1, 1); return false">Удалить статус</a></div>
                    [status]
                    <div class="button_div_gray fl_r status_but margin_left">
                        <button>Отмена</button>
                    </div>
                    [/status]
                    <div class="button_div fl_r status_but">
                        <button id="status_but" onClick="gStatus.set('', 1)">Сохранить</button>
                    </div>
                </div>
                [/admin]
                <div class="titleu" id="e_public_title">{title}</div>
                [not-admin][status]
                <div class="status border_radius_5">[/status][/not-admin]
                    [admin]
                    <div class="status border_radius_5">[/admin]
                        <div>[admin]<a href="/" id="new_status"
                                       onClick="gStatus.open(); return false">[/admin]{status-text}[admin]</a>[/admin]
                        </div>
                        [admin]<span id="tellBlockPos"></span>
                        <div class="status_tell_friends no_display" style="width:215px;z-index:10000">
                            <div class="status_str"></div>
                            <div class="html_checkbox" id="tell_friends"
                                 onClick="myhtml.checkbox(this.id); gStatus.startTellPublic('{id}')">Рассказать
                                подписчикам сообщества
                            </div>
                        </div>
                        [/admin]
                        [admin]<a href="#" onClick="gStatus.open(); return false" id="status_link"
                                  [status]class="no_display" [/status]>установить статус</a>[/admin]
                        [admin]
                    </div>
                    [/admin]
                    [not-admin][status]
                </div>
                [/status][/not-admin]
                <div class="page_bg border_radius_5 margin_top_10">
                    <div class="{descr-css}" id="descr_display">
                        <div class="flpodtext">Описание:</div>
                        <div class="flpodinfo" id="e_descr">{descr}</div>
                    </div>
                    <div class="flpodtext">Дата создания:</div>
                    <div class="flpodinfo">{date}</div>
                    [web]
                    <div class="flpodtext">Веб-сайт:</div>
                    <div class="flpodinfo"><a href="{web}" target="_blank">{web}</a></div>
                    [/web]
                </div>
            </div>
            [admin]
            <div class="public_editbg border_radius_5 fl_l no_display" id="edittab1">
                <div class="public_title">Редактирование страницы</div>
                <div class="clear margin_top_10"></div>
                <div class="texta">Название:</div>
                <input type="text" id="title" class="inpst" maxlength="100" style="width:260px;" value="{title}"/>
                <div class="mgclr"></div>
                <div class="texta">Описание:</div>
                <textarea id="descr" class="inpst" style="width:260px;height:80px">{edit-descr}</textarea>
                <div class="mgclr"></div>
                <div class="texta">Адрес страницы:</div>
                <input type="hidden" id="prev_adres_page" class="inpst" maxlength="100" style="width:260px;"
                       value="{adres}"/>
                <input type="text" id="adres_page" class="inpst" maxlength="100" style="width:260px;" value="{adres}"/>
                <div class="mgclr"></div>
                <div class="texta">Веб-сайт:</div>
                <input type="text" id="web" class="inpst" maxlength="100" style="width:260px;" value="{web}"/>
                <div class="mgclr"></div>
                <!--<div class="texta">Фон страницы:</div>
                 <div class="button_div_gray fl_l"><button id="upload_3">Загрузить</button></div>
                 <div class="clear"></div>
                 <small style="margin-left:150px">Файл не должен превышать 5 Mб.</small>
                <div class="mgclr clear"></div>-->
                <div class="texta">&nbsp;</div>
                <div class="html_checkbox" id="comments" onClick="myhtml.checkbox(this.id)" style="margin-bottom:8px">
                    Комментарии включены
                </div>
                <div class="mgclr clear"></div>
                <div class="texta">&nbsp;</div>
                <div class="html_checkbox" id="discussion" onClick="myhtml.checkbox(this.id)" style="margin-bottom:8px">
                    Обсуждения включены
                </div>
                <div class="mgclr clear"></div>
                <!--<div class="texta">&nbsp;</div>
                 <div class="html_checkbox" id="background_repeat" onClick="myhtml.checkbox(this.id)" style="margin-bottom:8px">Растянуть фон на весь экран</div>-->
                <div class="mgclr clear"></div>
                <div class="texta">&nbsp;</div>
                <a href="/public{id}" onClick="groups.edittab_admin(); return false">Назначить администраторов
                    &raquo;</a>
                <div class="mgclr"></div>
                <div class="texta">&nbsp;</div>
                <div class="button_div fl_l">
                    <button onClick="groups.saveinfo('{id}'); return false" id="pubInfoSave">Сохранить</button>
                </div>
                <div class="button_div_gray fl_l margin_left">
                    <button onClick="groups.editformClose(); return false">Отмена</button>
                </div>
                <div class="mgclr"></div>
            </div>
            <div class="public_editbg border_radius_5 fl_l no_display" id="edittab2">
                <div class="public_title">Руководители страницы</div>
                <div class="clear margin_top_10"></div>
                <input
                        type="text"
                        placeholder="Введите ссылку на страницу или введите ID страницы пользователя и нажмите Enter"
                        class="videos_input"
                        style="width:615px"
                        onKeyPress="if(event.keyCode == 13)groups.addadmin('{id}')"
                        id="new_admin_id"
                />
                <div class="clear"></div>
                <div style="width:600px" id="admins_tab">{admins}</div>
                <div class="clear"></div>
                <div class="button_div fl_l">
                    <button onClick="groups.editform(); return false">Назад</button>
                </div>
            </div>
            [/admin]
        </div>
    </div>
    [discussion]
    <div class="page_bg border_radius_5 margin_top_10" style="padding-bottom:0px">
        <a href="/forum{id}" onClick="Page.Go(this.href); return false" class="fl_l"
           style="text-decoration:none;height:10px">
            <div class="albtitle albtitle2">{forum-num} <b id="langForum">Нет обсуждений</b></div>
        </a>
        <a href="/forum{id}?act=new" onClick="Page.Go(this.href); return false" class="fl_r {no}"
           style="text-decoration:none">
            <div class="albtitle albtitle2">Новая тема</div>
        </a>
        <div class="albtitle albtitle2">&nbsp;</div>
        <div class="clear"></div>
        <div style="margin-top:-11px">{thems}</div>
        <div class="clear"></div>
    </div>
    [/discussion]

    <div class="page_bg border_radius_5 margin_top_10 page_bg_wall"
         style="padding-bottom:0px[admin];padding-bottom:20px[/admin]">

        <div class="albtitle albtitle2" style="border-bottom:0px">{rec-num}</div>
        [admin]
        <div class="newmes" id="wall_tab"
             style="border-bottom:0px;margin-left:-13px;margin-top:-15px;margin-bottom:-10px">
            <input type="hidden" value="Что у Вас нового?" id="wall_input_text"/>
            <input type="text" class="msg_se_inp" value="Что у Вас нового?" onMouseDown="wall.form_open(); return false"
                   id="wall_input" style="margin:0px;width:600px"/>
            <div class="no_display" id="wall_textarea">
   <textarea id="wall_text" class="wall_inpst wall_fast_opened_texta" style="width:612px"
             onKeyUp="wall.CheckLinkText(this.value)"
             onBlur="wall.CheckLinkText(this.value, 1)"
             onKeyPress="if(event.keyCode == 10 || (event.ctrlKey && event.keyCode == 13)) groups.wall_send('{id}')"
   >
   </textarea>
                <div id="attach_files" class="margin_top_10 no_display"></div>
                <div id="attach_block_lnk" class="no_display clear">
                    <div class="attach_link_bg">
                        <div align="center" id="loading_att_lnk"><img src="/images/loading_mini.gif"
                                                                      style="margin-bottom:-2px"/></div>
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
                             src="/images/close_a.png" onMouseOver="myhtml.title('1', 'Не прикреплять', 'attach_lnk_')"
                             id="attach_lnk_1" onClick="wall.RemoveAttachLnk()"/></div>
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
                        <div class="texta">Варианты ответа:<br/><small><span id="addNewAnswer"><a class="cursor_pointer"
                                                                                                  onClick="Votes.AddInp()">добавить</a></span>
                                | <span id="addDelAnswer">удалить</span></small></div>
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
                                                    style="text-decoration:none;cursor:default"></a></div>
                        <img class="fl_l cursor_pointer" style="margin-top:2px;margin-left:5px"
                             src="/images/close_a.png" onMouseOver="myhtml.title('1', 'Не прикреплять', 'attach_vote_')"
                             id="attach_vote_1" onClick="Votes.RemoveForAttach()"/></div>
                    <input type="hidden" id="answerNum" value="2"/>
                </div>
                <div class="clear"></div>
                <input id="vaLattach_files" type="hidden"/>
                <div class="clear"></div>
                <div class="button_div fl_l margin_top_10">
                    <button onClick="groups.wall_send('{id}'); return false" id="wall_send">Отправить</button>
                </div>
                <div class="wall_attach fl_r" style="margin-right:-14px"
                     onMouseOver="wall.attach_menu('open', this.id, 'wall_attach_menu')"
                     onMouseOut="wall.attach_menu('close', this.id, 'wall_attach_menu')" id="wall_attach">Прикрепить
                </div>
                <div class="wall_attach_menu no_display"
                     onMouseOver="wall.attach_menu('open', 'wall_attach', 'wall_attach_menu')"
                     onMouseOut="wall.attach_menu('close', 'wall_attach', 'wall_attach_menu')" id="wall_attach_menu">
                    <div class="wall_attach_icon_smile" id="wall_attach_link" onClick="wall.attach_addsmile()">Смайлик
                    </div>
                    <div class="wall_attach_icon_photo" id="wall_attach_link"
                         onClick="groups.wall_attach_addphoto(0, 0, '{id}')">Фотографию
                    </div>
                    <div class="wall_attach_icon_video" id="wall_attach_link" onClick="groups.wall_video_add_box()">
                        Видеозапись
                    </div>
                    <div class="wall_attach_icon_audio" id="wall_attach_link" onClick="wall.attach_addaudio()">
                        Аудиозапись
                    </div>
                    <div class="wall_attach_icon_doc" id="wall_attach_link" onClick="wall.attach_addDoc()">Документ
                    </div>
                    <div class="wall_attach_icon_vote" id="wall_attach_link"
                         onClick="$('#attach_block_vote').slideDown('fast');wall.attach_menu('close', 'wall_attach', 'wall_attach_menu');$('#vote_title').focus();$('#vaLattach_files').val($('#vaLattach_files').val()+'vote|start||')">
                        Опрос
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        [/admin]
    </div>
    <div id="public_wall_records">{records}</div>
    <div class="cursor_pointer {wall-page-display}" onClick="groups.wall_page('{id}'); return false"
         id="wall_all_records">
        <div class="border_radius_5 public_wall_all_comm" id="load_wall_all_records">к предыдущим записям</div>
    </div>
    <input type="hidden" id="page_cnt" value="1"/>
</div>
<div class="clear"></div>