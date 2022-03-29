<div class="msg_speedbar">Восстановление доступа к странице</div>
<div class="note_add_bg support_bg border_radius_5 margin_top_10" id="step1">
    <div style="width:310px"><b>{name}</b>, в целях безопасности вашего аккаунта администрация <b>Mixchat</b>
        рекомендует вам регулярно менять пароль. Эта процедура не займет у вас много времени, но значительно уменьшит
        риски доступа третьих лиц к вашей учетной записи. Для изменения пароля к аккаунту введите новый пароль в
        специальное поле под данным сообщением.
    </div>
    <div class="err_red no_display name_errors" id="err"
         style="font-weight:normal;width:292px;margin-top:10px;margin-bottom:0px"></div>
    <input type="password"
           class="videos_input fl_l"
           style="width:300px;margin-top:10px"
           maxlength="65"
           id="new_pass"
           placeholder="Новый пароль"
    />
    <div class="clear"></div>
    <div class="input_hr" style="width:315px"></div>
    <input type="password"
           class="videos_input fl_l"
           style="width:300px"
           maxlength="65"
           id="new_pass2"
           placeholder="Повторите еще раз новый пароль"
    />
    <input type="hidden" id="hash" value="{hash}"/>
    <div class="clear"></div>
    <div class="input_hr" style="width:315px"></div>
    <div class="button_div fl_l">
        <button onClick="restore.finish(); return false" id="send">Сменить</button>
    </div>
    <div class="clear"></div>
</div>
<div class="note_add_bg support_bg no_display margin_top_10 border_radius_5" id="step2"><b>{name}</b>, Ваш пароль был
    успешно изменён на новый, теперь Вы можете зайти на сайт используя новый пароль.
</div>