<div class="buttonsprofile albumsbuttonsprofile buttonsprofileSecond" style="height:22px">
 <a href="/loto" onClick="Page.Go(this.href); return false;">Все</a>
 <div class="buttonsprofileSec"><a href="/loto?act=one" onClick="Page.Go(this.href); return false;">Фортуна</a></div>
 <a href="/loto?act=two" onClick="Page.Go(this.href); return false;">Лото 6 из 45</a>
</div>
<div class="clear"></div>
<div class="page_bg border_radius_5">
<div class="nonsense_title" style="margin-bottom:30px">Следующая игра через</div>
<script language="JavaScript">
BID = 'cntdwn';
TargetDate = "{next-game}";
TekDate = "{tek-date}";
CountActive = true;
CountStepper = -1;
LeadingZero = true;
DisplayFormat = "%%H%%:%%M%%:%%S%%";
FinishMessage = "<a href=\"/loto?act=one\" onClick=\"Page.Go(this.href); return false\">Обновить страницу</a>";
</script>
<div class="cntdwn"><span id='cntdwn'></span>
</div>
<script type="text/javascript" src="{theme}/js/cd.js"></script>
<div id="ddtime"></div>
<div class="clear"></div>
[first-game]<div class="nonsense_title">Выигрышный номер</div>
<div class="nonsense_number"><a>{winner_number}</a></div>
<div class="nonsense_title">Победитель выиграл {prev_prize} mix</div>
<div class="nonsense_pob">
 <a href="/u{winner_uid}" onClick="Page.Go(this.href); return false"><img src="{ava}" /></a>
 <a href="/u{winner_uid}" onClick="Page.Go(this.href); return false"><b>{pob_name}</b></a>
 <div>{country-cuty}</div>
 <div>{age}</div>
 <div class="clear"></div>
</div>[/first-game]
<div class="nonsense_title" style="margin-top:30px">Розыгрыш</div>
<div class="nonsense_info">
 Розыгрыш происходит каждый день в <b>{nonsense_one_time}</b> по МСК.<br />
 Призовой фонд <b>{prize} mix.</b><br />
 Участие стоит <b>{nonsense_one_cost} mix.</b>
</div>
<div class="nonsense_title" style="margin-top:10px">Участники</div>
<div class="nonsense_info">
 Всего участников: <b>{users_num}</b>
 <div class="clear"></div>
 <div class="err_yellow [game]no_display[/game]" style="font-weight:normal;margin-top:25px;margin-bottom:0px;border:0px">Ваш выигрышный номер в системе <b>{mynumber}</b></div>
 <div class="err_red no_display" style="font-weight:normal;margin-top:25px;margin-bottom:0px;border:0px">У Вас <b>недостаточно</b> mix. <a href="/balance" onClick="Page.Go(this.href); return false">Пополнить баланс</a></div>
 [game]<div style="width:120px;margin:auto" id="nonsenseButLogin"><div class="button_div fl_l" style="line-height:15px;margin-top:10px"><button id="nonsenseLogin" onClick="doLoad.data(3); nonsense.login()">Принять участие</button></div></div>[/game]
</div>
<div class="clear"></div>
<div class="nonsense_title" style="margin-top:20px">Как это работает ?</div>
<div class="nonsense_info" style="font-size:11px;color:#777">
 Каждый день в назначенное время разыгрывается случайное число, которое генерирует система, от 1 до количества участников, и победитель получает весь призовой фонд себе на баланс, например участвует 7 человек, значит в указанное время будет сгенерировано случайное число от 1 до 7, ваше число выдается вам после того как вы нажали на кнопку "Принять участие". <br />На странице выводится результаты последнего розыгрыша.
</div>
</div>