<div id="photo_view_{id}" class="photo_view" onClick="Photo.setEvent(event, '{close-link}')">
 <div class="photo_com_title">[all]Фотография {jid} из {photo-num}[/all][wall]Просмотр фотографии[/wall]</div>
 <div style="background:#f5f5f5;margin-left:-12px;margin-right:-12px;margin-bottom:10px"><center><a href="/photo{uid}_{next-id}{section}" onClick="Photo.Show(this.href); return false" id="photo_href"><img id="ladybug_ant{id}" src="{photo}" /></a></center></div>
 <div class="clr"></div>
 <div id="pinfo_{id}" class="pinfo">
  <div class="photo_descr clr">
  <div>{descr}</div>
  <div class="color777">Альбом: <a href="/albums/view/{aid}">{album-name}</a></div>
  <div class="color777">Отправитель: <a href="/u{uid}">{author}</a></div>
  <div class="color777">Добавлена {date}</div>
  </div>
  <div class="profbloctitl" style="border-bottom:0px">Комментарии</div>
  [all-comm]<a href="/" onClick="comments.all({id}, {num}); return false" id="all_href_lnk_comm_{id}"><div class="photo_all_comm_bg" id="all_lnk_comm_{id}" style="border:0px;margin-bottom:0px;background:#f5f5f5;color:#555;border-top:1px solid #DBE0EA;">Показать предыдущие {comm_num}</div></a><span id="all_comments_{id}"></span>[/all-comm]
  <span id="comments_{id}">{comments}</span>
  [add-comm]<div class="wallformbg clr" style="margin-bottom:-12px;border-top:1px solid #DBE0EA;">
  <textarea id="textcom_{id}" class="inp" placeholder="Ваш комментарий.."></textarea>
  <button class="button" id="add_comm" onClick="comments.add({id}); return false">Отправить</button>
  </div>[/add-comm]
 </div>
</div>