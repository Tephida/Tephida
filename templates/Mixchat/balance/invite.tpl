<div class="buttonsprofile">
    <a href="/balance" onClick="Page.Go(this.href); return false;">Личный счёт</a>
    <div class="buttonsprofileSec"><a href="/balance?act=invite" onClick="Page.Go(this.href); return false;">Пригласить
            друга</a></div>
    <a href="/balance?act=invited" onClick="Page.Go(this.href); return false;">Приглашённые друзья</a>
    <a href="/balance?act=business" onClick="Page.Go(this.href); return false;">Мои подарки</a>
</div>
<div class="msg_speedbar clear">Инструкция по приглашению друга</div>
<div class="ubm_descr border_radius_5">
    <center>
        Для приглашения друга отправьте ему ссылку на регистрацию, которая указана ниже.<br/><br/>
        <span class="color777">Ваша ссылка для приглашения:</span>&nbsp;&nbsp;
        <input type="text"
               class="videos_input"
               style="width:200px"
               onClick="this.select()"
               value="https://mixchat.ru/reg{uid}"
        />
    </center>
</div>