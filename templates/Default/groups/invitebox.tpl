<script type="text/javascript">
var page_cnt_invite = 1;
</script>
<div class="miniature_box">
 <div class="miniature_pos">
  <div class="miniature_title fl_l">Приглашение в сообщество</div><a class="cursor_pointer fl_r" onClick="viiBox.clos('inviteBox', 1)">Закрыть</a>
  <div class="miniature_text clear">
   Выберите друзей которых хотите пригласить в сообщество.
   <div class="button_div no_display fl_r" style="margin-top:-2px;margin-left:10px;line-height:15px" id="buttomDiv"><button onClick="groups.inviteSend('{id}')" id="invSending">Отправить приглашения</button></div>
   <div class="fl_r online no_display" id="usernum">Выбрано <b id="usernum2">0</b></div>
  </div>
  <div style="margin-right:-20px" id="inviteUsers">{friends}</div>
  <div class="clear"></div>
  <input type="hidden" id="userInviteList" />
  [but]<div class="rate_alluser cursor_pointer" style="margin-top:0px" onClick="groups.inviteFriendsPage('{id}')" id="invite_prev_ubut"><div id="load_invite_prev_ubut">Показать больше друзей</div></div>[/but]
  <div class="clear"></div>
 </div>
</div>