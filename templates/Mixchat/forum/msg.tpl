<div class="forum_msg_border2 page_bg border_radius_5 margin_top_10"
     style="margin-left:0px;margin-right:0px;padding:20px" id="{mid}">
    <div class="forum_msg_ava">
        <a href="/u{user-id}" onClick="Page.Go(this.href); return false"><img src="{ava}" width="50"
                                                                              height="50"/></a><br/>
        {online}
    </div>
    <div class="forum_text">
        <a href="/u{user-id}" onClick="Page.Go(this.href); return false"><b>{name}</b></a><br/>
        {text}<br/>
        <span class="color777">{date} [admin-2]<a href="/" class="fl_r" onClick="Forum.DelMsg('{mid}'); return false">Удалить</a>[/admin-2] [not-owner]&nbsp;|&nbsp; <a
                    href="/" onClick="wall.Answer('1', '{mid}', '{name}'); $(window).scrollTop(9999); return false">Ответить</a>[/not-owner]</span>
    </div>
    <div class="clear"></div>
</div>