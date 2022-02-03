[record]<div class="onewallrec" id="wall_record_{rec-id}">
<div class="ewllfs"><a href="/u{author-id}"><img src="{ava}" width="40" height="40" align="left" /></a></div>
<div class="wallwTEXT">
<a href="/u{author-id}"><b>{author}</b></a> {online}<span class="color777">{action-type-updates}</span><br />
<div>{comment}</div>
<div class="clr"></div>
<span class="color777">{action-type} {date} [wall]<img src="{theme}/images/index2.png" style="margin-left:5px;margin-right:5px" /><b id="wall_like_cnt{rec-id}" class="wall_like_cnt">{likes}</b></span>[/wall]
<div class="panelsuvd">[wall]<a href="/" id="wall_like_link{rec-id}" onClick="{like-js-function}; return false" class="{yes-like-color}">Мне нравится</a>[/wall]</div>
[comments-link]<div id="fast_form_{rec-id}"><textarea class="wall_fast_text inp" style="height:33px;color:#000;margin:0px;margin-top:10px;width:98%;margin-bottom:10px" id="fast_text_{rec-id}" placeholder="Комментировать.."></textarea>
<div class="clr"></div>
<button class="button" onClick="[wall-func]wall.fast_send[/wall-func][groups]groups.wall_send_comm[/groups]('{rec-id}', '{author-id}', 1); return false">Отправить</button></div>[/comments-link]
</div>
<div class="clr"></div>
</div>[/record]
[all-comm]<div class="cursor_pointer" onClick="[wall-func]wall.all_comments('{rec-id}', '{author-id}', 1); return false[/wall-func][groups]groups.wall_all_comments('{rec-id}', '{author-id}'); return false[/groups]" id="wall_all_but_link_{rec-id}"><div class="public_wall_all_comm" id="wall_all_comm_but_{rec-id}">Показать {gram-record-all-comm}</div></div>[/all-comm]
[comment]<div class="wall_fast_block" id="wall_fast_comment_{comm-id}">
<div class="wall_fast_ava"><a href="/u{user-id}" onClick="Page.Go(this.href); return false"><img src="{ava}" alt="" width="25" height="25" /></a></div>
<div class="wall_fast_pad">
<div><a href="/u{user-id}">{name}</a></div>
<div class="wall_fast_comment_text">{text}</div>
<div class="color777" style="font-size:11px">{date} [owner]- <a href="/" class="size10 fl_r no_display" id="fast_del_{comm-id}" onClick="[wall-func]wall.fast_comm_del('{comm-id}')[/wall-func][groups]groups.comm_wall_delet('{comm-id}', '{public-id}')[/groups]; return false" style="font-weight:normal;font-size:11px">Удалить</a>[/owner]</div>
<div class="clear"></div>
</div>
</div>[/comment]
[comment-form]<div style="margin-left:50px;margin-bottom:10px" id="fast_form_{rec-id}"><textarea class="wall_fast_text inp" style="height:33px;color:#000;margin:0px;margin-top:5px;width:99%;margin-bottom:10px" id="fast_text_{rec-id}" placeholder="Комментировать.."></textarea>
<div class="clr"></div>
<button class="button" onClick="[wall-func]wall.fast_send[/wall-func][groups]groups.wall_send_comm[/groups]('{rec-id}', '{author-id}', 1); return false">Отправить</button></div>[/comment-form]