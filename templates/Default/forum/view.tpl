<script type="text/javascript">
var page = 1;
$(document).ready(function(){
	$('#fast_text_1').focus();
	langNumric('langMsg', '{msg-num}', 'сообщение', 'сообщения', 'сообщений', 'сообщение', 'сообщение');
	music.jPlayerInc();
});
</script>
<div id="jquery_jplayer"></div>
<input type="hidden" id="teck_id" value="" />
<input type="hidden" id="teck_prefix" value="" />
<input type="hidden" id="typePlay" value="standart" />
<div class="search_form_tab" style="margin-top:-9px;background:#fff">
 <div class="buttonsprofile albumsbuttonsprofile buttonsprofileSecond" style="height:22px">
  <a href="/public{id}" onClick="Page.Go(this.href); return false;"><div><b>К сообществу</b></div></a>
  <a href="/forum{id}" onClick="Page.Go(this.href); return false;"><div><b>Обсуждения</b></div></a>
  <div class="buttonsprofileSec"><a href="/forum{id}?act=view&id={fid}" onClick="Page.Go(this.href); return false;"><div><b>Просмотр темы</b></div></a></div>
  [admin-2]<div class="fl_r">
   <div class="sett_privacy" style="margin-bottom:0px" onClick="settings.privacyOpen('msg')" id="privacy_lnk_msg">Редактировать</div>
   <div class="sett_openmenu no_display" id="privacyMenu_msg" style="margin-left:-3px;margin-top:-1px;width:115px">
   <div id="selected_p_privacy_lnk_msg" class="sett_selected" onClick="settings.privacyClose('msg')">Редактировать</div>
    <div class="sett_hover" onClick="Forum.EditTitle()">Изменить название</div>
    <span id="votelnk">{vote-link}</span>
    [admin]<div class="sett_hover" onClick="Forum.Fix('{fid}')" id="fix_text">{fix-text}</div>
    <div class="sett_hover" onClick="Forum.Status('{fid}')" id="status_text">{status-text}</div>[/admin]
    <div class="sett_hover" onClick="Forum.DelBox('{fid}', '{id}')">Удалить тему</div>
   </div>
   <div class="mgclr"></div>
  </div>[/admin-2]
 </div>
</div>
<div class="clear"></div>
<div class="note_full_title" style="margin-top:8px">
 <span id="titleTeck"><a href="/forum{id}?act=view&id={fid}" onClick="Page.Go(this.href); return false" id="editTitleSaved">{title}</a><br /></span>
 <div id="editTitle" class="no_display">
 <input type="text" class="videos_input" value="{title}" id="title" maxlength="65" size="65" />
 <div class="clear" style="margin-top:-5px;margin-bottom:35px;line-height:14px">
   <div class="button_div fl_l"><button onClick="Forum.SaveEditTitle('{fid}')">Сохранить</button></div>
   <div class="button_div_gray fl_l margin_left" id="editClose"><button onClick="Forum.CloseEditTitle()">Отмена</button></div>
  </div>
  <div class="clear"></div>
 </div>
 <div><a href="/u{user-id}" onClick="Page.Go(this.href); return false">{name}</a></div>
</div>
<div class="err_yellow no_display forum_infos_div"></div>
<div id="attach_block_vote" class="no_display" style="margin:auto;width:550px;margin-top:10px">
 <div class="attach_link_bg">
  <div class="texta">Тема опроса:</div><input type="text" id="vote_title" class="inpst" maxlength="80" value="" style="width:355px;margin-left:5px" 
		onKeyUp="$('#attatch_vote_title').text(this.value)"
  /><div class="mgclr"></div>
  <div class="texta">Варианты ответа:<br /><small><span id="addNewAnswer"><a class="cursor_pointer" onClick="Votes.AddInp()">добавить</a></span> | <span id="addDelAnswer">удалить</span></small></div><input type="text" id="vote_answer_1" class="inpst" maxlength="80" value="" style="width:355px;margin-left:5px" /><div class="mgclr"></div>
  <div class="texta">&nbsp;</div><input type="text" id="vote_answer_2" class="inpst" maxlength="80" value="" style="width:355px;margin-left:5px" /><div class="mgclr"></div>
   <div id="addAnswerInp"></div>
  <div class="clear"></div>
  </div>
  <div class="attach_toolip_but"></div>
  <div class="attach_link_block_ic fl_l"></div><div class="attach_link_block_te"><div class="fl_l">Опрос: <a id="attatch_vote_title" style="text-decoration:none;cursor:default"></a></div></div>
  <input type="hidden" id="answerNum" value="2" />
  <div class="clear" style="margin-top:25px;margin-bottom:40px;line-height:14px">
   <div class="button_div fl_l"><button onClick="Forum.CreateVote('{fid}')" id="savevote">Создать опрос</button></div>
   <div class="button_div_gray fl_l margin_left" id="editClose"><button onClick="$('#attach_block_vote').slideUp(100); Forum.RemoveForAttach()">Отмена</button></div>
  </div>
 <div class="clear"></div>
</div>
<div style="line-height:17px" id="voteblockk">{vote}</div>
<div class="allbar_title forum_view_title">В теме <span id="msgNumJS">{msg-num}</span> <span id="langMsg">сообщение</span></div>
<div class="forum_msg_border">
<div class="forum_msg_ava">
 <a href="/u{user-id}" onClick="Page.Go(this.href); return false"><img src="{ava}" width="50" height="50" /></a><br />
 {online}
</div>
<div class="forum_text">
 <a href="/u{user-id}" onClick="Page.Go(this.href); return false"><b>{name}</b></a><br />
 <span id="teckText">{text}</span>
 <div class="clear"></div>
 <div class="no_display" id="editTextTab">
  <textarea class="inpst" style="width:680px;height:120px" id="editText">{edit-text}</textarea>
  <div class="clear" style="margin-top:10px;margin-bottom:40px;line-height:14px">
   <div class="button_div fl_l"><button onClick="Forum.SaveEdit('{fid}')" id="saveedit">Сохранить</button></div>
   <div class="button_div_gray fl_l margin_left" id="editClose"><button onClick="Forum.CloseEdit()">Отмена</button></div>
  </div>
  <div class="clear"></div>
 </div>
 <span class="color777">{date} [admin-2]<span id="editLnk">&nbsp;|&nbsp; <a href="/" onClick="Forum.EditText(); return false">Редактировать</a></span>[/admin-2]</span>
</div>
<div class="clear"></div>
</div>
[msg]<div class="cursor_pointer" onClick="Forum.MsgPage('{fid}'); return false" id="forum_msg_lnk" style="margin-top:-1px;margin-left:8px;margin-right:7px"><div class="public_wall_all_comm" id="load_forum_msg_lnk" style="margin-left:0px">Показать предыдущие сообщения</div></div>[/msg]
<span id="msgPrev"></span>
<span id="msg">{msg}</span>
<div class="note_add_bg clear support_addform forum_addmsgbg [/add-form]no_display[/add-form]">
<div class="ava_mini">
 <a href="/u{my-uid}" onClick="Page.Go(this.href); return false"><img src="{my-ava}" alt="" width="50" height="50" /></a>
</div>
<textarea 
	class="videos_input wysiwyg_inpt fl_l" 
	id="fast_text_1" 
	style="width:682px;height:37px;color:#c1cad0;margin-left:3px;color:#000"
	placeholder="Комментировать.."
	onKeyPress="if(event.keyCode == 10 || (event.ctrlKey && event.keyCode == 13)) Forum.SendMsg('{fid}')"
></textarea>
<div class="clear"></div>
<div class="button_div fl_l" style="margin-left:64px"><button onClick="Forum.SendMsg('{fid}'); return false" id="msg_send">Отправить</button></div>
<div class="wall_answer_for_comm fl_l" style="margin-top:5px">
 <a class="cursor_pointer answer_comm_for" id="answer_comm_for_1"></a>
 <input type="hidden" class="answer_comm_id" id="answer_comm_id1" />
</div>
<div class="clear"></div>
</div>