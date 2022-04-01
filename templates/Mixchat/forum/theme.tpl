<div class="forum_bg2 page_bg border_radius_5 margin_bottom_10" id="theme{fid}" style="padding-top:10px">
    <div class="forum_title cursor_pointer" onClick="Page.Go('/forum{pid}?act=view&id={fid}'); return false">{title}
        &nbsp;&nbsp;<span class="color777" style="font-weight:normal">{status}</span></div>
    <div class="forum_bottom" style="font-size:11px;color:#000">{msg-num}. Последнее от <a href="/u{user-id}"
                                                                                           onClick="Page.Go(this.href); return false">{name}</a>, {date}
    </div>
</div>
<div class="clear"></div>