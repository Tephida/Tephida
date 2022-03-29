<div class="vk_feed">
<div class="vk_poser"><img src="{poster}" width="50" height="50" /></div>
<div class="vk_author">{author}</div>
<div class="vk_text">{text}{addmsgpos}
<div class="vk_date clear">{date} [msg]&nbsp;|&nbsp; <a class="cursor_pointer" onClick="vk.msg_box('{vk_uid}', '{rand}'); return false"><span class="vk_msg_box_text" id="vk_msg_box_text{vk_uid}{rand}">Написать сообщение</span></a>[/msg] [comm]&nbsp;|&nbsp; <a class="cursor_pointer" onClick="vk.comm_box('{vk_hash}', '{vk_id}', '{rand}'); return false"><span class="vk_msg_box_text_2" id="vk_msg_box_text_2_{vk_uid}{rand}">Комментировать</span></a>[/comm]</div>
 <div class="vk_msg_box no_display" id="vk_msg_box{vk_uid}{rand}" style="float:left;margin-left:0px;margin-top:5px;box-shadow:0px 0px 5px #aaa">
  <div style="margin-bottom:5px"><b>Ваше сообщение</b></div>
  <textarea id="vk_msg_fast_text{vk_uid}{rand}" class="inpst" style="height:40px;width:200px"></textarea>
  <input type="hidden" id="vk_msg_hash{vk_uid}{rand}" />
  <div class="clear"></div>
  <div class="button_div fl_l margin_top_10"><button style="line-height:15px" onClick="vk.msg_fast_send('{vk_uid}', '{rand}'); return false" id="vk_msg_but_sending{vk_uid}{rand}">Отправить</button></div>
  <div class="button_div_gray fl_l margin_top_10 margin_left" style="line-height:15px"><button onClick="vk.msg_box_close('{vk_uid}{rand}'); return false">Отмена</button></div>
 </div>
 <div class="vk_msg_box no_display" id="vk_msg_box_2_{vk_id}{rand}" style="float:left;margin-left:0px;margin-top:5px;box-shadow:0px 0px 5px #aaa">
  <div style="margin-bottom:5px"><b>Ваш комментарий</b></div>
  <textarea id="vk_msg_fast_text_2_{vk_id}{rand}" class="inpst" style="height:40px;width:200px"></textarea>
  <div class="clear"></div>
  <div class="button_div fl_l margin_top_10"><button style="line-height:15px" onClick="vk.send_comm('{vk_hash}', '{vk_id}', '{rand}'); return false" id="vk_msg_but_sending_2_{vk_id}{rand}">Отправить</button></div>
  <div class="button_div_gray fl_l margin_top_10 margin_left" style="line-height:15px"><button onClick="vk.comm_box_close(); return false">Отмена</button></div>
 </div>
</div>
<div class="clear"></div>
{comments}
</div>