<div class="wallformbg" style="margin-top:-10px;margin-bottom:10px;border-bottom:1px solid #EFEFEF">
<textarea id="msg_text" class="wall_fast_text inp"></textarea>
<button class="button" onClick="im.send('{for_user_id}', '{my-name}', '{my-ava}')">Отправить</button>
</div>
<a href="/messages" onClick="im.open('{for_user_id}'); return false"><img src="{theme}/images/rfre2.png" align="left" style="margin-right:5px" /></a><a href="/messages" onClick="im.open('{for_user_id}'); return false">Обновить переписку</a>
<input type="hidden" id="for_user_id" value="{for_user_id}" />