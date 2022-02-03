<div class="friends_onefriend width_100" style="border-top:1px solid #e0eaef">
 <a href="/{adres}" onClick="Page.Go(this.href); return false"><div class="friends_ava"><img src="{photo}" /></div></a>
 <div class="grInviteInviterName">
  <div>приглашает <a href="/u{inviter-id}" onClick="Page.Go(this.href); return false"><b>{inviter-name}</b></a></div>
  <a href="/u{inviter-id}" onClick="Page.Go(this.href); return false"><img src="{inviter-ava}" width="30" height="30" /></a>
 </div>
 <a href="/{adres}" onClick="Page.Go(this.href); return false"><b>{name}</b></a><div class="friends_clr"></div>
 <span class="color777">{traf}</span><div class="friends_clr"></div>
 <div id="action_{id}" style="margin-top:7px">
  <div class="button_div fl_l"><button onClick="groups.InviteOk('{id}')">Вступить в сообщество</button></div>
  <div class="button_div_gray fl_l margin_left"><button onClick="groups.InviteNo('{id}')">Отклонить приглашение</button></div>
 </div>
</div>