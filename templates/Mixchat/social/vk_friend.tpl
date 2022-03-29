<div class="friends_onefriend width_100">
 <a><div class="friends_ava"><img src="{ava}" alt="" /></div></a>
 <div class="fl_l" style="width:500px">
  <a><b>{name}</b></a><div class="friends_clr"></div>
  {mininfo}<div class="friends_clr"></div>
  <span class="online">{online}</span><div class="friends_clr"></div>
 </div>
 <div class="menuleft fl_r friends_m">
  <a class="cursor_pointer" onClick="vk.msg_box('{vk_uid}'); return false"><div class="vk_msg_box_text" id="vk_msg_box_text{vk_uid}">Написать сообщение</div></a>
 </div>
 <div class="vk_msg_box no_display" id="vk_msg_box{vk_uid}">
  <textarea id="vk_msg_fast_text{vk_uid}" class="inpst" style="height:40px;width:200px"></textarea>
  <input type="hidden" id="vk_msg_hash{vk_uid}" />
  <div class="clear"></div>
  <div class="button_div fl_l margin_top_10"><button onClick="vk.msg_fast_send('{vk_uid}'); return false" id="vk_msg_but_sending{vk_uid}">Отправить</button></div>
  <div class="button_div_gray fl_l margin_top_10 margin_left"><button onClick="vk.msg_box_close('{vk_uid}'); return false">Отмена</button></div>
 </div>
</div>