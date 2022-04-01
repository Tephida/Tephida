<script type="text/javascript">
    $(document).ready(function () {
        myhtml.checked(['{n_friends}', '{n_wall}', '{n_comm}', '{n_comm_ph}', '{n_comm_note}', '{n_gifts}', '{n_rec}', '{n_im}']);
    });

    function save_notify() {
        var n_friends = $('#n_friends').val();
        var n_wall = $('#n_wall').val();
        var n_comm = $('#n_comm').val();
        var n_comm_ph = $('#n_comm_ph').val();
        var n_comm_note = $('#n_comm_note').val();
        var n_gifts = $('#n_gifts').val();
        var n_rec = $('#n_rec').val();
        var n_im = $('#n_im').val();
        $.post('/index.php?go=settings&act=save_notify', {n_friends: n_friends, n_wall: n_wall, n_comm: n_comm, n_comm_ph: n_comm_ph, n_comm_note: n_comm_note, n_gifts: n_gifts, n_rec: n_rec, n_im: n_im});
    }
</script>
<div class="buttonsprofile albumsbuttonsprofile buttonsprofileSecond" style="height:22px">
    <a href="/settings" onClick="Page.Go(this.href); return false;">Общее</a>
    <a href="/settings/privacy" onClick="Page.Go(this.href); return false;">Приватность</a>
    <a href="/settings/blacklist" onClick="Page.Go(this.href); return false;">Черный список</a>
    <div class="buttonsprofileSec"><a href="/settings/notify" onClick="Page.Go(this.href); return false;">Оповещения</a>
    </div>
</div>
<div class="margin_top_10"></div>
<div class="msg_speedbar clear">Оповещения по электронной почте</div>
<div class="page_bg border_radius_5 clear margin_top_10">
    <div class="html_checkbox" id="n_friends" onClick="myhtml.checkbox(this.id); save_notify()">Уведомление при новой
        заявки в друзья
    </div>
    <div class="clear" style="height:10px"></div>
    <div class="html_checkbox" id="n_wall" onClick="myhtml.checkbox(this.id); save_notify()">Уведомление при ответе на
        запись
    </div>
    <div class="clear" style="height:10px"></div>
    <div class="html_checkbox" id="n_comm" onClick="myhtml.checkbox(this.id); save_notify()">Уведомление при
        комментировании видео
    </div>
    <div class="clear" style="height:10px"></div>
    <div class="html_checkbox" id="n_comm_ph" onClick="myhtml.checkbox(this.id); save_notify()">Уведомление при
        комментировании фото
    </div>
    <div class="clear" style="height:10px"></div>
    <div class="html_checkbox" id="n_comm_note" onClick="myhtml.checkbox(this.id); save_notify()">Уведомление при
        комментировании заметки
    </div>
    <div class="clear" style="height:10px"></div>
    <div class="html_checkbox" id="n_gifts" onClick="myhtml.checkbox(this.id); save_notify()">Уведомление при новом
        подарке
    </div>
    <div class="clear" style="height:10px"></div>
    <div class="html_checkbox" id="n_rec" onClick="myhtml.checkbox(this.id); save_notify()">Уведомление при новой записи
        на стене
    </div>
    <div class="clear" style="height:10px"></div>
    <div class="html_checkbox" id="n_im" onClick="myhtml.checkbox(this.id); save_notify()">Уведомление при новом
        персональном сообщении
    </div>
    <div class="clear"></div>
</div>