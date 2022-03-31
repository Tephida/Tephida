<div class="buttonsprofile albumsbuttonsprofile buttonsprofileSecond" style="height:48px">
    <div class="buttonsprofileSec"><a href="/groups" onClick="Page.Go(this.href); return false;">Сообщества</a></div>
    <a href="/groups?act=admin" onClick="Page.Go(this.href); return false;">Управление сообществами</a>
    <a href="/groups" onClick="groups.createbox(); return false">Создать сообщество</a>
    <a href="/groups?act=invites" onClick="Page.Go(this.href); return false;">Приглашения</a>
</div>
<div class="msg_speedbar clear">[yes]Вы состоите в {num}[/yes][no]Вы не состоите ни в одном сообществе.[/no]</div>
<div class="margin_top_10"></div>
[no]
<div class="info_center border_radius_5"><br/><br/>
    Вы пока не состоите ни в одном сообществе.
    <br/><br/>
    Вы можете <a href="/groups" onClick="groups.createbox(); return false">создать сообщество</a> или воспользоваться <a
            href="/" onClick="gSearch.open_tab(); gSearch.select_type('4', 'по сообществам'); return false"
            id="se_link">поиском по сообществам</a>.<br/><br/><br/>
</div>[/no]