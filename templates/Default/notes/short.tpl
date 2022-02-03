<div id="note_{note-id}">
<div class="notes_ava"><a href="/u{user-id}" onClick="Page.Go(this.href); return false"><img src="{ava}" alt="" /></a></div>
<div class="one_note">
 <span><a href="/notes/view/{note-id}" onClick="Page.Go(this.href); return false">{title}</a></span><br />
 <div><a href="/u{user-id}" onClick="Page.Go(this.href); return false">{name}</a> {date}</div>
</div>
<div class="note_text clear"><div class="note_pos">{short-text}</div> <a href="/notes/view/{note-id}" onClick="Page.Go(this.href); return false"><b>Читать далее..</b></a></b></u></i><div class="clear"></div></div>
<div class="note_inf_panel">
 <a href="/notes/view/{note-id}" onClick="Page.Go(this.href); return false">{comm-num}</a> [owner]&nbsp;|&nbsp; <a href="/notes/edit/{note-id}" onClick="Page.Go(this.href); return false">Редактировать</a> &nbsp;|&nbsp; <a href="/" onClick="notes.delet({note-id}, false, {user-id}); return false">Удалить</a>[/owner]
</div>
<div class="clear"></div>
</div>