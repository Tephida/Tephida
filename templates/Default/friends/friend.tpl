<div class="friends_onefriend width_100" id="friend_{user-id}">
 <a href="/u{user-id}" onClick="Page.Go(this.href); return false"><div class="friends_ava"><img src="{ava}" alt="" id="ava_{user-id}" /></div></a>
 <div class="fl_l" style="width:500px">
  <a href="/u{user-id}" onClick="Page.Go(this.href); return false"><b>{name}</b></a><div class="friends_clr"></div>
  {country}{city}<div class="friends_clr"></div>
  {age}<div class="friends_clr"></div>
  <span class="online">{online}</span><div class="friends_clr"></div>
 </div>
 <div class="menuleft fl_r friends_m">
  [viewer]<a href="/" onClick="messages.new_({user-id}); return false"><div>Написать сообщение</div></a>[/viewer]
  [owner]<a onMouseDown="friends.delet({user-id}, 0); return false"><div>Убрать из друзей</div></a>[/owner]
  <a href="/albums/{user-id}" onClick="Page.Go(this.href); return false"><div>Альбомы</div></a>
 </div>
</div>