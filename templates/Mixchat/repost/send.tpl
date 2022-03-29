<div class="videos_pad" style="padding:20px">
 <div class="videos_text">Ваш комментарий</div>
 <textarea type="text" class="videos_input" id="comment_repost" style="margin-bottom:10px;width:375px;height:30px"></textarea>
 <div class="videos_text">Выберите аудиторию</div>
 <div class="cursor_pointer"><input type="radio" checked id="friends" name="type" onClick="$('#type_repost').val(1)" /> <label for="friends">Друзья и подписчики</label></div>
 <div class="color777" style="padding-top:7px;padding-left:24px">Поделиться записью с Вашими друзьями и подписчиками</div>
 <div style="margin-top:10px" class="cursor_pointer"><input type="radio" id="groups" name="type" onClick="$('#type_repost').val(2)" {groups-friends} /> <label for="groups">Подписчики сообщества</label></div>
 <div class="color777" style="padding-top:7px;padding-left:24px">
 <select class="inpst" style="width:300px" id="sel_group" {groups-friends}>
  {groups-list}
 </select>
 </div>
 <div style="margin-top:10px" class="cursor_pointer"><input type="radio" id="users" name="type" onClick="$('#type_repost').val(3)" {disabled-friends} /> <label for="users">Отправить личным сообщением</label></div>
 <div class="color777" style="padding-top:7px;padding-left:24px">
 <select class="inpst" style="width:300px" id="for_user_id" {disabled-friends}>
  {friends-list}
 </select>
 </div>
 <input type="hidden" id="type_repost" value="1" />
</div>
<div class="clear"></div>