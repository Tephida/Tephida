<script>
    var page_cnt_rate = 1;
</script>
<div class="miniature_text clear">
    Выберите друзей которых хотите пригласить в сообщество.
    <div class="button_div no_display fl_r" style="margin-top:-2px;margin-left:10px;line-height:15px"
         id="buttomDiv">
        <button onClick="groups.inviteSend('{id}')" id="invSending">Отправить приглашения</button>
    </div>
    <div class="fl_r online no_display" id="usernum">Выбрано <b id="usernum2">0</b></div>
</div>

<div class="miniature_title fl_l apps_box_text">История повышения рейтинга</div>
<a class="cursor_pointer fl_r" style="font-size:12px" onClick="viiBox.clos('view_rating', 1)">Закрыть</a>
<div class="clear"></div>
<div id="rating_users">{users}</div>
<div class="clear"></div>
[prev]
<div class="rate_alluser cursor_pointer" onClick="rating.page()" id="rate_prev_ubut">
    <div id="load_rate_prev_ubut">Показать предыдущие повышения</div>
</div>
[/prev]
<div class="clear"></div>
