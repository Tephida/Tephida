<div class="note_full_title border_radius_5" style="word-wrap:break-word">
    <span><a href="/notes/view/{note-id}" onClick="Page.Go(this.href); return false">{title}</a></span><br/>
    <div><a href="u{user-id}" onClick="Page.Go(this.href); return false">{name}</a></div>
</div>
<div class="page_bg border_radius_5">{full-text}</b></u></i>
    <div class="clear"></div>
</div>
<div class="note_inf_panel note_text_full">
    <span class="online">{date}</span>
    <div class="fl_r">{comm-num} &nbsp;|&nbsp; <a class="cursor_pointer" onClick="Report.Box('note', '{note-id}')">Пожаловаться
            на заметку</a>[owner]&nbsp;|&nbsp; <a href="/notes/edit/{note-id}"
                                                  onClick="Page.Go(this.href); return false">Редактировать</a> &nbsp;|&nbsp;
        <a href="/" onClick="notes.delet({note-id}, 1); return false">Удалить</a>[/owner]
    </div>
</div>
[all-comm]<a href="/" onClick="notes.allcomments({note-id}, {num}); return false" id="all_href_lnk_comm">
    <div class="photo_all_comm_bg note_all_com" id="all_lnk_comm">Показать {prev-text-comm}</div>
</a><span id="all_comments"></span>[/all-comm]
