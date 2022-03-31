<div class="im_text support_answer im_msg {new}" id="imMsg{msg-id}" {read-js-func}>
    <div style="float:left;width:55px">
        <div class="ava_mini im_msg_ava im_ava_mini">
            <a href="/u{user-id}" onClick="Page.Go(this.href); return false">
                <img src="{ava}" width="45"/></a>
        </div>
    </div>
    <div class="wallauthor support_anser_nam im_msg_name" style="padding-left:0px">
        <a href="/u{user-id}" onClick="Page.Go(this.href); return false">{name}</a>
        <div class="fl_r im_msg_date">
            <div class="fl_l">{date}</div>
            <img src="/images/close_a_wall.png"
                 onMouseOver="myhtml.title('{msg-id}', 'Удалить сообщение', 'del_text_', -3)"
                 onClick="im.delet('{msg-id}', '{folder}'); return false" id="del_text_{msg-id}"
                 class="msg_histry_del cursor_pointer im_msg_delf fl_r"/></div>
    </div>
    <div style="float:left;width:550px;overflow:hidden">
        <div class="walltext im_msg_mag" style="margin-left:0px">{text}</div>
        <div class="clear"></div>
        <div class="border_radius_5 no_display" id="translate_{msg-id}"
             style="background:#f9f9f9;padding:10px;margin-top:5px;margin-bottom:5px">cs
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>