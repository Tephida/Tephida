<script type="text/javascript">
$(document).ready(function(){
	vii_interval_im = setInterval("im.update('{for_user_id}')", 2000);
	music.jPlayerInc();
	$('.im_scroll').scroll(function(){
		if($('.im_scroll').scrollTop() <= ($('.im_scroll').height()/2)+250)
			im.page('{for_user_id}');
	});
});
func = function(val){
	document.getElementById('message_tab_frm').elements['msg_text'].focus();
	if(document.selection){
		document.getElementById('message_tab_frm').document.selection.createRange().text = document.getElementById('message_tab_frm').document.selection.createRange().text+val;
    } else if(document.getElementById('message_tab_frm').elements['msg_text'].selectionStart != undefined){
		var element = document.getElementById('message_tab_frm').elements['msg_text']; 
		var str = element.value; 
		var start = element.selectionStart; 
		var length = element.selectionEnd - element.selectionStart; 
		element.value = str.substr(0, start) + str.substr(start, length) + val + str.substr(start + length);
	} else {
		document.getElementById('message_tab_frm').elements['msg_text'].value += val; 
    }
}
</script>
<div id="jquery_jplayer"></div>
<input type="hidden" id="teck_id" value="" />
<input type="hidden" id="typePlay" value="standart" />
<input type="hidden" id="teck_prefix" value="" />
<div class="note_add_bg clear support_addform im_addform">
<div class="ava_mini im_ava_mini">
 <a href="/u{myuser-id}" onClick="Page.Go(this.href); return false"><img src="{my-ava}" alt="" /></a>
</div>
<form id="message_tab_frm">
<textarea 
	class="videos_input wysiwyg_inpt fl_l im_msg_texta" 
	id="msg_text" 
	style="height:38px"
	placeholder="Введите Ваше сообщение.."
	onKeyPress="
	 if(((event.keyCode == 13) || (event.keyCode == 10)) && (event.ctrlKey == false)) im.send('{for_user_id}', '{my-name}', '{my-ava}')
	 if(((event.keyCode == 13) || (event.keyCode == 10)) && (event.ctrlKey == true)) func('\r\n')
	"
	onKeyUp="im.typograf()"
></textarea>
</form>
<div class="clear"></div>
<div id="attach_files" class="no_display" style="margin-left:60px"></div>
<input id="vaLattach_files" type="hidden" />
<div class="clear"></div>
<div class="button_div fl_l" style="margin-left:60px"><button onClick="im.send('{for_user_id}', '{my-name}', '{my-ava}')" id="sending">Отправить</button></div>
[canInvite]<div class="button_div fl_l" style="margin-left:10px"><button onClick="imRoom.inviteBox(this, '{for_user_id}')">Пригласить друга</button></div>[/canInvite]
[canExit]<div class="button_div fl_l" style="margin-left:10px"><button onClick="imRoom.exit(this, '{for_user_id}')">Покинуть беседу</button></div>[/canExit]
[room]<div class="button_div fl_l" style="margin-left:10px"><button onClick="imRoom.viewRoom(this, '{for_user_id}')">Настройки беседы</button></div>[/room]
<div class="wall_attach fl_r" onClick="wall.attach_menu('open', this.id, 'wall_attach_menu')" onMouseOut="wall.attach_menu('close', this.id, 'wall_attach_menu')" id="wall_attach" style="margin-top:0px">Прикрепить</div>
 <div class="wall_attach_menu no_display" onMouseOver="wall.attach_menu('open', 'wall_attach', 'wall_attach_menu')" onMouseOut="wall.attach_menu('close', 'wall_attach', 'wall_attach_menu')" id="wall_attach_menu" style="margin-left:433px;margin-top:20px">
 <div class="wall_attach_icon_smile" id="wall_attach_link" onClick="wall.attach_addsmile()">Смайлик</div>
 <div class="wall_attach_icon_photo" id="wall_attach_link" onClick="wall.attach_addphoto()">Фотографию</div>
 <div class="wall_attach_icon_video" id="wall_attach_link" onClick="wall.attach_addvideo()">Видеозапись</div>
 <div class="wall_attach_icon_audio" id="wall_attach_link" onClick="wall.attach_addaudio()">Аудиозапись</div>
 <div class="wall_attach_icon_doc" id="wall_attach_link" onClick="wall.attach_addDoc()">Документ</div>
</div>
<div class="clear" style="margin-top:10px"></div>
<div class="clear"></div>
</div>
<input type="hidden" id="status_sending" value="1" />
<input type="hidden" id="for_user_id" value="{for_user_id}" />