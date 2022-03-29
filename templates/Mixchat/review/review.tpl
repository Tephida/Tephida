<div id="{id}" style="margin-top:5px;padding:10px 10px 0px 0px;">
<div><img class="cursor_pointer" onClick="Page.Go('/u{aid}'); return false" src="{a_ava}" alt="" width="80" height="80"/></div>
<div class="mn_boxes cursor_pointer" onClick="Page.Go('/u{aid}'); return false" style="margin-top:-82px;margin-left:80px;width:675px">{aname} <span>{online}</span></div>
<div id="mn_show_count" style="padding1: 5px 5px;margin-left:79px;width:674px;height1:37px;">
<div class="color777" style="width:679px">
<div id="edit_review_{id}">{text}</div>
<div id="edit_review_cont_{id}" class="no_display">
<textarea id="texts_{id}" name="texts" class="inpst" style="width:665px;height:30px">{text}</textarea>
<div class="button_div fl_l" style="margin-top:2px;margin-left:220px"><button style="" onClick="review.save({id}); return false">Сохранить изменения</button></div>
<div class="button_div_gray fl_l margin_left" style="margin-top:2px;"><button onClick="review.edit_close('{id}'); return false">Отмена</button></div>
<div style="padding:12px"></div></div>
</div></div>
<div class="mn_boxes_about"style="margin-left:80px;width:675px"><span>{rsex} {date}</span>[a_user]<a href="/" onClick="review.del('{id}'); return false" id="del_rev" class="cursor_pointer fl_r" style="margin-left:10px">удалить</a><a href="/" onClick="review.edit_form({id}); return false" class="cursor_pointer fl_r">изменить</a>[/a_user]</div>
</div>