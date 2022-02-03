[record]<div class="wallrecord comm_wr news_comm_wr" id="wall_record_{rec-id}" style="padding-bottom:4px;margin-top:5px">
<div style="float:left;width:60px"><div class="ava_mini"><a href="/{link}{author-id}" onClick="Page.Go(this.href); return false"><img src="{ava}" alt="" /></a><div style="position:absolute;margin-left:4px">{online}</div></div></div>
<div style="float:left;width:710px;margin-left:0px">
<div class="wallauthor" ><a href="/{link}{author-id}" onClick="Page.Go(this.href); return false">{author}</a> <span class="color777">{action-type-updates}</span></div>
[wall]<div class="wall_tell_all cursor_pointer" onMouseOver="myhtml.title('{rec-id}', 'Отправить в сообщество или другу', 'wall_tell_all_')" onClick="Repost.Box('{rec-id}'[groups], 1[/groups]); return false "id="wall_tell_all_{rec-id}" style="margin-top:-17px;margin-right:20px"></div><div class="wall_tell cursor_pointer wall_tell_fornews" onMouseOver="myhtml.title('{rec-id}', 'Рассказать друзьям', 'wall_tell_')" onClick="[wall-func]wall.tell[/wall-func][groups]groups.wall_tell[/groups]('{rec-id}'); return false" id="wall_tell_{rec-id}"></div>
 <div class="wall_tell_ok no_display wall_tell_fornews" id="wall_ok_tell_{rec-id}"></div>[/wall]
<div class="walltext" style="margin-top:3px">{comment}</div>
<div class="infowalltext_f clear">
<div class="fl_l">{action-type} {date} [comments-link]<span id="fast_comm_link_{rec-id}" class="fast_comm_link">&nbsp;|&nbsp; <a href="/" id="fast_link_{rec-id}" onClick="wall.open_fast_form('{rec-id}'); wall.fast_open_textarea('{rec-id}'); return false">Комментировать</a></span>[/comments-link]</div>
[wall]<div class="public_likes_user_block no_display" id="public_likes_user_block{rec-id}" onMouseOver="groups.wall_like_users_five('{rec-id}'[wall-func], 'uPages'[/wall-func])" onMouseOut="groups.wall_like_users_five_hide('{rec-id}')" style="margin-left:585px">
   <div onClick="[wall-func]wall.all_liked_users[/wall-func][groups]groups.wall_all_liked_users[/groups]('{rec-id}', '', '{likes}')">Понравилось {likes-text}</div>
   <div class="public_wall_likes_hidden">
    <div class="public_wall_likes_hidden2">
     <a href="/u{viewer-id}" id="like_user{viewer-id}_{rec-id}" class="no_display" onClick="Page.Go(this.href); return false"><img src="{viewer-ava}" width="32" /></a>
     <div id="likes_users{rec-id}"></div>
    </div>
   </div>
   <div class="public_like_strelka"></div>
  </div>
  <input type="hidden" id="update_like{rec-id}" value="0" />
  <div class="fl_r public_wall_like cursor_pointer" onClick="{like-js-function}" onMouseOver="groups.wall_like_users_five('{rec-id}'[wall-func], 'uPages'[/wall-func])" onMouseOut="groups.wall_like_users_five_hide('{rec-id}')" id="wall_like_link{rec-id}">
   <div class="fl_l" id="wall_like_active">Мне нравится</div>
   <div class="public_wall_like_no {yes-like}" id="wall_active_ic{rec-id}"></div>
   <b id="wall_like_cnt{rec-id}" class="{yes-like-color}">{likes}</b>
  </div>[/wall]
[comments-link]<div class="wall_fast_form no_display" id="fast_form_{rec-id}" style="margin-top:22px">
<div class="no_display wall_fast_texatrea" id="fast_textarea_{rec-id}">
 <textarea class="wall_inpst fast_form_width wall_fast_text" style="height:33px;color:#000;margin:0px;;width:688px" id="fast_text_{rec-id}"
	onKeyPress="if(event.keyCode == 10 || (event.ctrlKey && event.keyCode == 13))[wall-func]wall.fast_send[/wall-func][groups]groups.wall_send_comm[/groups]('{rec-id}', '{author-id}', 1)"
 ></textarea>
  <div class="button_div fl_l margin_top_5">
   <button id="fast_buts_{rec-id}"
		onClick="[wall-func]wall.fast_send[/wall-func][groups]groups.wall_send_comm[/groups]('{rec-id}', '{author-id}', 1); return false"
   >Отправить</button>
  </div>
</div>
<div class="clear"></div>
</div>[/comments-link]
</div>
<div class="clear"></div>
</div>
<div class="clear"></div>
</div>[/record]
[all-comm]<div class="cursor_pointer" onClick="[wall-func]wall.all_comments('{rec-id}', '{author-id}', 1); return false[/wall-func][groups]groups.wall_all_comments('{rec-id}', '{author-id}'); return false[/groups]" id="wall_all_but_link_{rec-id}"><div class="public_wall_all_comm" id="wall_all_comm_but_{rec-id}">Показать {gram-record-all-comm}</div></div>[/all-comm]
[comment]<div class="wall_fast_block" id="wall_fast_comment_{comm-id}" onMouseOver="ge('fast_del_{comm-id}').style.display = 'block'" onMouseOut="ge('fast_del_{comm-id}').style.display = 'none'">
<div class="wall_fast_ava"><a href="/u{user-id}" onClick="Page.Go(this.href); return false"><img src="{ava}" alt="" /></a></div>
<div><a href="/u{user-id}" onClick="Page.Go(this.href); return false">{name}</a></div>
<div class="wall_fast_comment_text">{text}</div>
<div class="wall_fast_date fl_l">{date} &nbsp;-&nbsp; <a href="#" onClick="wall.Answer('{rec-id}', '{comm-id}', '{name}'); return false" id="answer_lnk">Ответить</a></div>[owner]<a href="/" class="size10 fl_r no_display" id="fast_del_{comm-id}" onClick="[wall-func]wall.fast_comm_del('{comm-id}')[/wall-func][groups]groups.comm_wall_delet('{comm-id}', '{public-id}')[/groups]; return false">Удалить</a>[/owner]
<div class="clear"></div>
</div>[/comment]
[comment-form]<div class="wall_fast_opened_form" id="fast_form">
 <input type="text" class="wall_inpst fast_form_width wall_fast_input" value="Комментировать..." id="fast_inpt_{rec-id}" onMouseDown="wall.fast_open_textarea('{rec-id}', 2); return false" style="margin:0px;width:688px" />
 <div class="no_display wall_fast_texatrea" id="fast_textarea_{rec-id}">
 <textarea class="wall_inpst fast_form_width wall_fast_text" style="height:33px;color:#000;margin:0px;;width:688px" id="fast_text_{rec-id}"
	onKeyPress="if(event.keyCode == 10 || (event.ctrlKey && event.keyCode == 13))[wall-func]wall.fast_send[/wall-func][groups]groups.wall_send_comm[/groups]('{rec-id}', '{author-id}', 1)"
 ></textarea>
  <div class="button_div fl_l margin_top_5">
   <button id="fast_buts_{rec-id}"
		onClick="[wall-func]wall.fast_send[/wall-func][groups]groups.wall_send_comm[/groups]('{rec-id}', '{author-id}', 1); return false"
   >Отправить</button>
  </div>
  <div class="wall_answer_for_comm fl_l">
   <a class="cursor_pointer answer_comm_for" id="answer_comm_for_{rec-id}"></a>
   <input type="hidden" class="answer_comm_id" id="answer_comm_id{rec-id}" />
  </div>
 </div>
 <div class="clear"></div>
</div>[/comment-form]