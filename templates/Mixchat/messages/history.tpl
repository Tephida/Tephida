<div class="msg_onehistory [new]msg_new[/new]" id="bmsg_{msg-id}">
    <div class="msg_history_name [owner]msg_history_owner_color[/owner]">
        <a href="u{user-id}" onClick="Page.Go(this.href); return false"><b>{name}</b></a>
    </div>
    <div class="msg_hist_text fl_l">{text}&nbsp;</div>
    <div class="msg_hist_date">{date}</div>
    <img src="/images/close_a_wall.png" onMouseOver="myhtml.title('{msg-id}', 'Удалить сообщение', 'del_text_')"
         onClick="messages.delet('{msg-id}', '{folder}'); return false" id="del_text_{msg-id}"
         class="msg_histry_del cursor_pointer"/>
    <img src="/images/loading_mini.gif" id="del_load_{msg-id}" class="msg_histry_del no_display"/>
    <div class="clear"></div>
</div>
