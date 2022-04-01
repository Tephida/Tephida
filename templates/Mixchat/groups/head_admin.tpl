<div class="buttonsprofile albumsbuttonsprofile buttonsprofileSecond" style="height:48px">
    <a href="/groups" onClick="Page.Go(this.href); return false;">Сообщества</a>
    <div class="buttonsprofileSec"><a href="/groups?act=admin" onClick="Page.Go(this.href); return false;">Управление
            сообществами</a></div>
    <a href="/groups" onClick="groups.createbox(); return false">Создать сообщество</a>
    <a href="/groups?act=invites" onClick="Page.Go(this.href); return false;">Приглашения</a>
</div>
<div class="msg_speedbar clear" [yes]style="margin-bottom:0px;border-bottom:0px"
     [/yes]>[yes]Вы руководитель в {num}[/yes][no]Вы не управляете ни одним сообществом[/no]</div>
<div class="margin_top_10"></div>
[no]
<div class="info_center"><br/><br/>
    Вы не управляете ни одним сообществом.
    <br/><br/>
    Вы можете <a href="/groups" onClick="groups.createbox(); return false">создать сообщество</a> или воспользоваться <a
            href="/" onClick="gSearch.open_tab(); gSearch.select_type('4', 'по сообществам'); return false"
            id="se_link">поиском по сообществам</a>.<br/><br/><br/>
</div>[/no]