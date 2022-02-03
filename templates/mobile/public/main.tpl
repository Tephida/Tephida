<a href="/{adres}"><img src="{photo}" align="left" width="50" height="50" style="margin-right:10px" />
<b>{title}</b></a><br />
<div style="font-size:11px">{num}</div>
<div class="clr"></div>
<div class="clr" style="margin-top:10px"></div>
<div id="yes" class="{yes}"><button class="button" onClick="groups.login('{id}'); return false" style="width:100%">Подписаться</button></div>
<div id="no" class="{no}"><button class="button" onClick="groups.exit2('{id}', '{viewer-id}'); return false" style="width:100%">Отписаться</button></div>
<div class="profbloctitl">Информация</div>
<div style="padding-top:10px;font-size:12px;line-height:18px">
<div class="{descr-css}">Описание:<br />
<div style="color:#000;margin-top:10px">{descr}</div></div>
Дата создания: <span style="color:#000">{date}</span>
</div>
<div class="profbloctitl">Другое</div>
<div class="profii2">
 <a href="/{adres}"  onClick="groups.all_people('{id}'); return false">{num}</a>
</div>
<div class="profbloctitl" style="margin-top:-1px;margin-bottom:-1px">{rec-num}</div>
[admin]<div class="wallformbg" style="border-bottom:1px solid #EFEFEF;margin-bottom:-1px;margin-top:1px">
<textarea id="wall_text" class="wall_fast_text inp" placeholder="Что у Вас нового?"></textarea>
<button class="button" onClick="groups.wall_send('{id}'); return false">Отправить</button>
</div>[/admin]
<div id="public_wall_records">{records}</div>
<div class="clr"></div>
<div class="cursor_pointer {wall-page-display}" onClick="groups.wall_page('{id}'); return false" id="wall_all_records"><div class="photo_all_comm_bg" id="load_wall_all_records">к предыдущим записям</div></div>
<input type="hidden" id="page_cnt" value="1" />
<input type="hidden" id="public_id" value="{id}" />