[new]
<script type="text/javascript">
    document.getElementById('new_ubm').innerHTML = '';
    document.getElementById('ubm_link').setAttribute('href', '/balance');
</script>[/new]
<div class="buttonsprofile">
    [no-new]
    <div class="buttonsprofileSec">[/no-new]<a href="/gifts{uid}" onClick="Page.Go(this.href); return false;">[not-owner]Подарки {name}
            [/not-owner][owner]Мои Подарки[/owner]</a>[no-new]
    </div>
    [/no-new]
    [new]
    <div class="buttonsprofileSec"><a href="/gifts{uid}?new=1" onClick="Page.Go(this.href); return false;">Новые
            подарки</a></div>
    [/new]
    <a href="/u{uid}" onClick="Page.Go(this.href); return false;">[not-owner]К странице {name}[/not-owner][owner]К моей
        странице[/owner]</a>
    [not-owner]<a href="/" onClick="gifts.box('{uid}'); return false;">Отправить подарок для {name}</a>[/not-owner]
</div>
<div class="msg_speedbar clear margin_bottom_10" [yes]style="border-bottom:0px"
     [/yes]>[no-new][yes]У [not-owner]{name}[/not-owner][owner]Вас[/owner] {gifts-num}[/yes][no]Нет ни одного подарка[/no][/no-new][new]Непросмотренные подарки[/new]</div>
[no]
<div class="info_center margin_top_10"><br/><br/>[not-owner]У {name} еще нет подарков.<br/>Вы можете стать первым, кто
    отправит подарок. Для этого нажмите <a href="/" onClick="gifts.box('{uid}'); return false;">здесь</a>.[/not-owner][owner]У
    Вас еще нет подарков.[/owner]<br/><br/><br/></div>[/no]