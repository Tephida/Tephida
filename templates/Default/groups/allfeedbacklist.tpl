<div class="public_obefeed" id="f{user-id}" onMouseOver="ge('public_minilink{user-id}').style.opacity = '1'" onMouseOut="ge('public_minilink{user-id}').style.opacity = '0.4'">
 <a href="/u{user-id}" onClick="Page.Go(this.href); return false"><img src="{ava}" align="left" /></a>
 <a href="/u{user-id}" onClick="Page.Go(this.href); return false">{name}</a><div class="fl_r public_minilink" id="public_minilink{user-id}"><a href="/" onClick="messages.new_('{user-id}'); return false">Сообщение</a>[admin] &nbsp;-&nbsp; <a href="/public{id}" onClick="groups.editfeedback('{user-id}'); return false">Редактировать</a> &nbsp;-&nbsp; <a href="/" onClick="groups.delfeedback('{id}', '{user-id}'); return false">Удалить</a>[/admin]</div><br />
 <div id="close_editf{user-id}"><span id="okoffice{user-id}">{office}</span><div class="color777"><span id="okphone{user-id}">{phone}</span><span id="okemail{user-id}">{email}</span></div></div>
 [admin]<div id="editf{user-id}" class="no_display">
  <input type="text" id="office{user-id}" value="{office}" class="inpst" style="width:120px;margin-bottom:5px;padding:2px" /><br />
  <input type="text" id="phone{user-id}" value="{phone}" class="inpst" style="width:120px;padding:2px" />
  <input type="text" id="email{user-id}" value="{email}" class="inpst" style="width:120px;padding:2px" />
  <a href="/public{id}" style="margin-left:10px;font-size:11px;font-weight:normal" onClick="groups.editfeeddave('{id}', '{user-id}'); return false">Сохранить</a>
 </div>[/admin]
 <div class="clear"></div>
</div>