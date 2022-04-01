<script type="text/javascript">
    $(document).ready(function () {
        $('#title_n').focus();
    });
</script>
<div class="buttonsprofile">
    <a href="/public{id}" onClick="Page.Go(this.href); return false;">К сообществу</a>
    <a href="/forum{id}" onClick="Page.Go(this.href); return false;">Обсуждения</a>
    <div class="buttonsprofileSec"><a href="/forum{id}?act=new" onClick="Page.Go(this.href); return false;">Новая
            тема</a></div>
</div>
<div class="clear"></div>
<div class="note_add_bg border_radius_5">
    <div class="videos_text">Заголовок</div>
    <input type="text" class="videos_input" style="width:767px" maxlength="65" id="title_n"/>
    <div class="input_hr"></div>
    <div class="videos_text">Текст</div>
    <textarea class="videos_input wysiwyg_inpt" id="text" style="height:200px;width:767px"></textarea>
    <div class="clear"></div>
    <div id="attach_files" class="no_display"></div>
    <input id="vaLattach_files" type="hidden"/>
    <div class="clear"></div>
    <div class="button_div fl_l margin_top_10">
        <button onClick="Forum.New('{id}'); return false" id="forum_sending">Создать тему</button>
    </div>
    <div class="wall_attach fl_r" onMouseOver="wall.attach_menu('open', this.id, 'wall_attach_menu')"
         onMouseOut="wall.attach_menu('close', this.id, 'wall_attach_menu')" id="wall_attach" style="margin-top:10px">
        Прикрепить
    </div>
    <div class="wall_attach_menu no_display" onMouseOver="wall.attach_menu('open', 'wall_attach', 'wall_attach_menu')"
         onMouseOut="wall.attach_menu('close', 'wall_attach', 'wall_attach_menu')" id="wall_attach_menu"
         style="margin-left:670px;margin-top:30px">
        <div class="wall_attach_icon_smile" id="wall_attach_link" onClick="wall.attach_addsmile()">Смайлик</div>
        <div class="wall_attach_icon_photo" id="wall_attach_link" onClick="wall.attach_addphoto()">Фотографию</div>
        <div class="wall_attach_icon_video" id="wall_attach_link" onClick="wall.attach_addvideo()">Видеозапись</div>
        <div class="wall_attach_icon_audio" id="wall_attach_link" onClick="wall.attach_addaudio()">Аудиозапись</div>
        <div class="wall_attach_icon_doc" id="wall_attach_link" onClick="wall.attach_addDoc()">Документ</div>
    </div>
    <div class="clear"></div>
</div>