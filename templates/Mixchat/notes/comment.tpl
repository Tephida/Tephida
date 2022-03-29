<div class="wallrecord note_wr_comm page_bg border_radius_5 margin_bottom_10" id="note_comment_{id}"
     style="padding:20px">
    <div class="ava_mini" style="float:width:60px"><a href="u{user-id}" onClick="Page.Go(this.href); return false"><img
                    src="{ava}" alt="" title=""/></a>{online}</div>
    <div style="float:left;width:760px">
        <div class="wallauthor"><a href="u{user-id}" onClick="Page.Go(this.href); return false">{author}</a></div>
        <div class="walltext">{comment}</div>
        <div class="infowalltext">{date} [owner]
            <a href="/" class="fl_r" onClick="notes.deletcomment({id}); return false" id="note_del_but_{id}">Удалить</a>[/owner]
        </div>
    </div>
    <div class="clear"></div>
</div>
<div class="clear"></div>
