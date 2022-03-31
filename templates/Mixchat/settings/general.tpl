<script type="text/javascript">
    {*$(document).ready(function(){
        Xajax = new AjaxUpload('upload', {
            action: '/index.php?go=settings&act=upload_doc',
            name: 'uploadfile',
            onSubmit: function (file, ext) {
                if(!(ext && /^(jpg|png|jpeg|gif|jpe)$/.test(ext))){
                    addAllErr(lang_bad_format, 3300);
                    return false;
                }
                Page.Loading('start');
            },
            onComplete: function (file, response){
                Page.Loading('stop');
                if(response == 1) addAllErr(lang_bad_format, 3300);
                else if(response == 2) addAllErr('Максимальны размер 5 МБ.', 3300);
                else if(response == 3) addAllErr('Ошибка сервера. Попробуйте пожалуйста позже.', 3300);
                else $('#docver').html('<div class="texta">&nbsp;</div><img src="'+response+'" style="max-width:418px;margin-top:10px" id="docval" />');
            }
        });
        Xajax = new AjaxUpload('upload2', {
            action: '/index.php?go=settings&act=upload_doc_2',
            name: 'uploadfile',
            onSubmit: function (file, ext) {
                if(!(ext && /^(jpg|png|jpeg|gif|jpe)$/.test(ext))){
                    addAllErr(lang_bad_format, 3300);
                    return false;
                }
                Page.Loading('start');
            },
            onComplete: function (file, response){
                Page.Loading('stop');
                if(response == 1) addAllErr(lang_bad_format, 3300);
                else if(response == 2) addAllErr('Максимальны размер 5 МБ.', 3300);
                else if(response == 3) addAllErr('Ошибка сервера. Попробуйте пожалуйста позже.', 3300);
                else $('#docver2').html('<div class="texta">&nbsp;</div><img src="'+response+'" style="max-width:418px;margin-top:10px" id="docval2" />');
            }
        });
        myhtml.checked(['{b_friends}', '{b_friends_online}', '{b_people}', '{b_pages}', '{b_video}', '{b_audio}', '{b_notes}', '{b_albums}', '{b_gifts}', '{b_photo}', '{b_design}', '{b_wall}']);
    });*}
    function fastLOGIN(i) {
        $('#gr' + i).html('Отписаться').attr('onClick', 'fastEXIT(' + i + '); return false');
        $.post('/index.php?go=groups&act=login', {id: i});
    }

    function fastEXIT(i) {
        $('#gr' + i).html('Подписаться').attr('onClick', 'fastLOGIN(' + i + '); return false');
        $.post('/index.php?go=groups&act=exit', {id: i});
    }

    function savblock() {
        var b_friends = $('#b_friends').val();
        var b_friends_online = $('#b_friends_online').val();
        var b_people = $('#b_people').val();
        var b_pages = $('#b_pages').val();
        var b_video = $('#b_video').val();
        var b_audio = $('#b_audio').val();
        var b_notes = $('#b_notes').val();
        var b_albums = $('#b_albums').val();
        var b_gifts = $('#b_gifts').val();
        var b_photo = $('#b_photo').val();
        var b_design = $('#b_design').val();
        var b_wall = $('#b_wall').val();
        $.post('/index.php?go=settings&act=savblock', {b_friends: b_friends, b_friends_online: b_friends_online, b_people: b_people, b_pages: b_pages, b_video: b_video, b_audio: b_audio, b_notes: b_notes, b_albums: b_albums, b_gifts: b_gifts, b_photo: b_photo, b_design: b_design, b_wall: b_wall});
    }
</script>
<div class="buttonsprofile albumsbuttonsprofile buttonsprofileSecond" style="height:22px">
    <div class="buttonsprofileSec"><a href="/settings" onClick="Page.Go(this.href); return false;">Общее</a></div>
    <a href="/settings/privacy" onClick="Page.Go(this.href); return false;">Приватность</a>
    <a href="/settings/blacklist" onClick="Page.Go(this.href); return false;">Черный список</a>
    <a href="/settings/notify" onClick="Page.Go(this.href); return false;">Оповещения</a>
    <a href="/settings/deactive" onClick="Page.Go(this.href); return false;">Удалить страницу</a>
</div>
<div class="err_yellow name_errors border_radius_5 {code-1}" style="font-weight:normal;margin-top:25px">Код активации из
    письма с текущего почтового ящика принят. Осталось подтвердить код активации в письме, отправленном на новый
    почтовый ящик.
</div>
<div class="err_yellow name_errors border_radius_5 {code-2}" style="font-weight:normal;margin-top:25px">Код активации из
    письма с нового почтового ящика принят. Осталось подтвердить код активации в письме, отправленном на текущий
    почтовый ящик.
</div>
<div class="err_yellow name_errors border_radius_5 {code-3}" style="font-weight:normal;margin-top:25px">Адрес Вашей
    электронной почты был успешно изменен на новый.
</div>
<div class="margin_top_10"></div>
<div class="msg_speedbar clear">Изменить пароль</div>
<div class="page_bg border_radius_5 margin_top_10">
    <div class="err_red no_display pass_errors" id="err_pass_1" style="font-weight:normal;">Пароль не изменён, так как
        прежний пароль введён неправильно.
    </div>
    <div class="err_red no_display pass_errors" id="err_pass_2" style="font-weight:normal;">Пароль не изменён, так как
        новый пароль повторен неправильно.
    </div>
    <div class="err_yellow no_display pass_errors" id="ok_pass" style="font-weight:normal;">Пароль успешно изменён.
    </div>
    <div class="texta">Старый пароль:</div>
    <input type="password" id="old_pass" class="inpst" maxlength="100" style="width:150px;"/>
    <span id="validOldpass"></span>
    <div class="mgclr"></div>
    <div class="texta">Новый пароль:</div>
    <input type="password" id="new_pass" class="inpst" maxlength="100" style="width:150px;"
           onMouseOver="myhtml.title('', 'Пароль должен быть не менее 6 символов в длину', 'new_pass')"/>
    <span id="validNewpass"></span>
    <div class="mgclr"></div>
    <div class="texta">Повторите пароль:</div>
    <input type="password" id="new_pass2" class="inpst" maxlength="100" style="width:150px;"
           onMouseOver="myhtml.title('', 'Введите еще раз новый пароль', 'new_pass2')"/>
    <span id="validNewpass2"></span>
    <div class="mgclr"></div>
    <div class="texta">&nbsp;</div>
    <div class="button_div fl_l">
        <button onClick="settings.saveNewPwd(); return false" id="saveNewPwd">Изменить пароль</button>
    </div>
    <div class="mgclr"></div>
</div>

<div class="margin_top_10"></div>
<div class="msg_speedbar clear">Изменить имя</div>
<div class="page_bg border_radius_5 margin_top_10">
    <div class="err_red no_display name_errors" id="err_name_1" style="font-weight:normal;">Специальные символы и
        пробелы запрещены.
    </div>
    <div class="err_yellow no_display name_errors" id="ok_name" style="font-weight:normal;">Изменения успешно
        сохранены.
    </div>
    <div class="texta">Ваше имя:</div>
    <label for="name"></label>
    <input type="text" id="name" class="inpst" maxlength="100" style="width:150px;" value="{name}"/>
    <span id="validName"></span>
    <div class="mgclr"></div>
    <div class="texta">Ваша фамилия:</div>
    <label for="lastname"></label>
    <input type="text" id="lastname" class="inpst" maxlength="100" style="width:150px;" value="{lastname}"/>
    <span id="validLastname"></span>
    <div class="mgclr"></div>
    <div class="texta">&nbsp;</div>
    <div class="button_div fl_l">
        <button onClick="settings.saveNewName(); return false" id="saveNewName">Изменить имя</button>
    </div>
    <div class="mgclr"></div>
</div>

{*<div class="margin_top_10"></div><div class="msg_speedbar clear">Адрес профиля</div>
<div class="page_bg border_radius_5 margin_top_10">
<div class="err_yellow no_display name_errors" id="ok_sl" style="font-weight:normal;">Изменения успешно сохранены.</div>
<div class="err_red no_display name_errors" id="err_sl_1" style="font-weight:normal;">Короткая ссылка может состоять только из латинских символов, цифр и знака подчеркивания.</div>
<div class="err_red no_display name_errors" id="err_sl_2" style="font-weight:normal;">Эта короткая ссылка уже занята.</div>
<div class="err_red no_display name_errors" id="err_sl_3" style="font-weight:normal;">Недопустимая короткая ссылка.</div>
<div class="texta">Номер страницы:</div><div style="color:#555;margin-top:2px;margin-bottom:10px">{id}</div><div class="mgclr"></div>
<div class="texta">Короткая ссылка:</div>
 <input type="text" id="short_link" class="inpst" maxlength="100"  style="width:150px;" value="{short-link}" />
 <span id="validShortLink"></span>
 <div class="mgclr"></div>
<div class="texta">&nbsp;</div><div class="button_div fl_l">
  <button onClick="settings.set_short_link($('#short_link').val()); return false" id="saveShortLink">Изменить короткую ссылку</button>
 </div>
 <div class="mgclr"></div>
</div>*}

{*<div class="margin_top_10"></div><div class="msg_speedbar clear">Пересчет</div>
<div class="page_bg border_radius_5 margin_top_10">
<div class="err_yellow no_display name_errors" id="ok_conversion" style="font-weight:normal;">Пересчет показателей был успешно выполнен.</div>
<div class="texta">&nbsp;</div><div class="button_div fl_l">
  <button onClick="settings.conversion(); return false" id="conversion">Пересчитать показатели новых событий</button>
 </div>
 <div class="mgclr"></div>
</div>*}

{*<div class="margin_top_10"></div><div class="msg_speedbar clear">Верификация</div>
<div class="page_bg border_radius_5 margin_top_10">
<div class="err_yellow {block_verification} name_errors" id="ok_verification" style="font-weight:normal;">Заявка на верификацию отправлена. <a class="cursor_pointer" onClick="settings.verification_cancel()">Отмена</a></div>
<div class="err_yellow {block_verification_3} name_errors" style="font-weight:normal;">Ваша страница верифицирована.</div>
<div id="block_verification" class="{block_verification_2}">
<div id="bverb"><div class="texta">&nbsp;</div><div class="button_div fl_l">
  <button onClick="$('#bver').show();$('#bverb').hide();$('#skype').focus()">Отправить заявку на верификацию</button>
 </div><div class="mgclr"></div>
</div>
<div id="bver" class="no_display">
 <div class="texta">Skype:</div>
 <input type="text" id="skype" class="inpst" maxlength="100"  style="width:150px;" />
 <div class="mgclr"></div>
 <div class="texta">Подтверждающий документ #1:</div><div class="button_div_gray fl_l" style="margin-top:5px">
  <button id="upload">Выбрать документ</button></div><div class="mgclr"></div>
 <div id="docver"><div class="texta">&nbsp;</div><img src="/images/1.jpg" style="max-width:418px;margin-top:10px" id="docval" /></div>
 <div class="texta">&nbsp;</div>Вам необходимо сделать фотографию напротив экрана монитора держав в руках паспорт или свидетельство о рождении.<div class="mgclr"></div>
 <div class="texta">Подтверждающий документ #2:</div><div class="button_div_gray fl_l" style="margin-top:5px">
  <button id="upload2">Выбрать документ</button></div><div class="mgclr"></div>
 <div id="docver2">
  <div class="texta">&nbsp;</div>
  <img src="/images/2.jpg" style="max-width:418px;margin-top:10px" id="docval2" />
 </div>
 <div class="texta">&nbsp;</div>Необходимо сделать копию или фотографию самого паспорта или свидетельство о рождении.<div class="mgclr"></div>
 <div class="texta">&nbsp;</div>
 <div class="html_checkbox" id="pravila" onClick="myhtml.checkbox(this.id);" style="margin-top:10px">Я даю согласие на обработку и хранение моих персональных данных</div><div class="mgclr"></div>
 <div class="texta">&nbsp;</div>
 <div class="button_div fl_l" style="margin-top:10px">
  <button onClick="settings.verification()" id="sendverification">Отправить</button>
 </div><div class="mgclr"></div>
</div>
</div>
</div>*}

<div class="margin_top_10"></div>
<div class="msg_speedbar clear">Адрес Вашей электронной почты</div>
<div class="page_bg border_radius_5 margin_top_10">
    <div class="err_yellow name_errors no_display" id="ok_email" style="font-weight:normal;">На <b>оба</b> почтовых
        ящика придут письма с подтверждением.
    </div>
    <div class="err_red no_display name_errors" id="err_email" style="font-weight:normal;">Неправильный email адрес
    </div>
    <div class="texta">Текущий адрес:</div>
    <div style="color:#555;margin-top:2px;margin-bottom:10px">{email}</div>
    <div class="mgclr"></div>
    <div class="texta">Новый адрес:</div>
    <input type="text" id="email" class="inpst" maxlength="100" style="width:150px;"/>
    <span id="validName"></span>
    <div class="mgclr"></div>
    <div class="texta">&nbsp;</div>
    <div class="button_div fl_l">
        <button onClick="settings.savenewmail(); return false" id="saveNewEmail">Сохранить адрес</button>
    </div>
    <div class="mgclr"></div>
</div>

<div class="margin_top_10"></div>
<div class="msg_speedbar clear">Настройки блоков</div>
<div class="page_bg border_radius_5 margin_top_10">
    <div class="html_checkbox" id="b_design" onClick="myhtml.checkbox(this.id); savblock()">На всех страницах применять
        мои настройки дизайна
    </div>
    <div class="clear" style="height:10px"></div>
    <div class="html_checkbox" id="b_photo" onClick="myhtml.checkbox(this.id); savblock()">Главная фотография</div>
    <div class="clear" style="height:10px"></div>
    <div class="html_checkbox" id="b_friends" onClick="myhtml.checkbox(this.id); savblock()">Друзья</div>
    <div class="clear" style="height:10px"></div>
    <div class="html_checkbox" id="b_friends_online" onClick="myhtml.checkbox(this.id); savblock()">Друзья на сайте
    </div>
    <div class="clear" style="height:10px"></div>
    <div class="html_checkbox" id="b_people" onClick="myhtml.checkbox(this.id); savblock()">Интересные люди</div>
    <div class="clear" style="height:10px"></div>
    <div class="html_checkbox" id="b_pages" onClick="myhtml.checkbox(this.id); savblock()">Интересные страницы</div>
    <div class="clear" style="height:10px"></div>
    <div class="html_checkbox" id="b_video" onClick="myhtml.checkbox(this.id); savblock()">Видеозаписи</div>
    <div class="clear" style="height:10px"></div>
    <div class="html_checkbox" id="b_audio" onClick="myhtml.checkbox(this.id); savblock()">Аудиозаписи</div>
    <div class="clear" style="height:10px"></div>
    <div class="html_checkbox" id="b_notes" onClick="myhtml.checkbox(this.id); savblock()">Заметки</div>
    <div class="clear" style="height:10px"></div>
    <div class="html_checkbox" id="b_albums" onClick="myhtml.checkbox(this.id); savblock()">Альбомы</div>
    <div class="clear" style="height:10px"></div>
    <div class="html_checkbox" id="b_gifts" onClick="myhtml.checkbox(this.id); savblock()">Подарки</div>
    <div class="clear" style="height:10px"></div>
    <div class="html_checkbox" id="b_wall" onClick="myhtml.checkbox(this.id); savblock()">Стена</div>
    <div class="clear" style="height:10px"></div>
</div>
