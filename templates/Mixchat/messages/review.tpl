<script type="text/javascript">
    [new]
    var msg_num = parseInt($('#new_msg').text().replace(')', '').replace('(', '')) - 1;
    if (msg_num > 0)
        $('#new_msg').html("<div class=\"ic_newAct\" style=\"margin-left:37px\">" + msg_num + "</div>");
    else
        $('#new_msg').html('');
    [/new]

        $(document).ready(function () {
            $('#msg_value').autoResize();
            $('#msg_value').focus();
        });
</script>
<div id="jquery_jplayer"></div>
<input type="hidden" id="teck_id" value=""/>
<input type="hidden" id="teck_prefix" value=""/>
<input type="hidden" id="typePlay" value="standart"/>

<div id="bmsg_{mid}">
    <div class="msg_div">
        <div class="msg_ava fl_l"><a href="u{user-id}" onClick="Page.Go(this.href); return false"><img src="{ava}"
                                                                                                       alt=""/></a>
        </div>
        <div class="msg_name"><a href="u{user-id}" onClick="Page.Go(this.href); return false">[outbox]Сообщение для
                [/outbox]{name}</a>&nbsp;&nbsp;<font>{online}</font><span>{date}</span></div>
        <div class="msg_text" style="margin-left:115px">
            <div class="delicious"></div>
            {text}<br/>

            <div class="msg_answer_form">
                <textarea class="inpst" style="height:65px;width:765px;margin-bottom:10px" id="msg_value"></textarea>
            </div>


        </div>
    </div>
    <div class="clear"></div>
</div>

<!--<div class="msg_review_ava">
 <a href="/u{user-id}" onClick="Page.Go(this.href); return false"><img src="{ava}" alt="" /></a>
 <div>{online}</div>
</div>
<div class="msg_review_right_col">
 <div class="msg_review_name">[outbox]Сообщение для [/outbox]<a href="/u{user-id}" onClick="Page.Go(this.href); return false">{name}</a></div>
 <div class="msg_review_date">{date}</div>
 <div class="clear"></div>
 <div class="msg_review_text">{text}</div>
</div>
<div class="msg_answer_form">
<textarea class="inpst" style="height:65px;width:765px;margin-bottom:10px" id="msg_value"></textarea>
<div id="attach_files" class="no_display"></div>
<input id="vaLattach_files" type="hidden" />
<div class="clear"></div>
<div class="button_div fl_l"><button onClick="messages.reply({user-id}, '[inbox]reply[/inbox][outbox]new[/outbox]'); return false" id="msg_sending">[inbox]Ответить[/inbox][outbox]Отправить[/outbox]</button></div>
<div class="wall_attach fl_r" onClick="wall.attach_menu('open', this.id, 'wall_attach_menu')" onMouseOut="wall.attach_menu('close', this.id, 'wall_attach_menu')" id="wall_attach" style="margin-top:0px">Прикрепить</div>
 <div class="wall_attach_menu no_display" onMouseOver="wall.attach_menu('open', 'wall_attach', 'wall_attach_menu')" onMouseOut="wall.attach_menu('close', 'wall_attach', 'wall_attach_menu')" id="wall_attach_menu" style="margin-left:685px;margin-top:20px">
 <div class="wall_attach_icon_smile" id="wall_attach_link" onClick="wall.attach_addsmile()">Смайлик</div>
 <div class="wall_attach_icon_photo" id="wall_attach_link" onClick="wall.attach_addphoto()">Фотографию</div>
 <div class="wall_attach_icon_video" id="wall_attach_link" onClick="wall.attach_addvideo()">Видеозапись</div>
 <div class="wall_attach_icon_audio" id="wall_attach_link" onClick="wall.attach_addaudio()">Аудиозапись</div>
 <div class="wall_attach_icon_doc" id="wall_attach_link" onClick="wall.attach_addDoc()">Документ</div>
</div>
<div class="clear" style="margin-top:10px"></div>
</div>
<div class="msg_view_histroy" id="history_lnk" onClick="messages.history({user-id}); return false">Показать историю сообщений</div>
<span class="no_display"><input type="hidden" id="theme_value" value="{subj}" /></span>
<div class="msg_view_history_title no_display">История сообщений</div>
<div id="msg_historyies"></div>-->
