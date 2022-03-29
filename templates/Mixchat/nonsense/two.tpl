<div class="buttonsprofile albumsbuttonsprofile buttonsprofileSecond" style="height:22px">
 <a href="/loto" onClick="Page.Go(this.href); return false;">Все</a>
 <a href="/loto?act=one" onClick="Page.Go(this.href); return false;">Фортуна</a>
 <div class="buttonsprofileSec"><a href="/loto?act=two" onClick="Page.Go(this.href); return false;">Лото 6 из 45</a></div>
</div>
<div class="clear"></div>
<div class="page_bg border_radius_5">
<div class="nonsense_title" style="margin-bottom:30px">Следующая игра через</div>
<script language="JavaScript">
BID = 'cntdwn3';
TargetDate = "{next-game}";
TekDate = "{tek-date}";
CountActive = true;
CountStepper = -1;
LeadingZero = true;
DisplayFormat = "%%H%%:%%M%%:%%S%%";
FinishMessage = "<a href=\"/loto?act=two\" onClick=\"Page.Go(this.href); return false\">Обновить страницу</a>";
</script>
<div class="cntdwn"><span id='cntdwn3'></span>
</div>
<script type="text/javascript" src="{theme}/js/cd.js"></script>
<div id="ddtime"></div>
<div class="clear"></div>
[game]<div class="nonsense_title">Выигрышные числа</div>
<div class="nonsense_number">
 <a>{num1}</a>
 <a>{num2}</a>
 <a>{num3}</a>
 <a>{num4}</a>
 <a>{num5}</a>
 <a>{num6}</a>
</div>
<div class="nonsense_title">Результаты сыгранных билетов</div>
<div class="nonsense_info">
 {prizers-2}
 {prizers-3}
 {prizers-4}
 {prizers-5}
 {prizers-6}
 {none}
</div>
<div class="clear"></div>[/game]
<div class="nonsense_title" style="margin-top:30px">Время проведения</div>
<div class="nonsense_info">
 Розыгрыш происходит каждый день в <b>{nonsense_two_time}</b> по МСК.<br />
</div>
<div class="nonsense_title" style="margin-top:10px">Призовой фонд</div>
<div class="nonsense_info">
 2 из 45 = <b>{nonsense_two_prize_2} mix</b><br />
 3 из 45 = <b>{nonsense_two_prize_3} mix</b><br />
 4 из 45 = <b>{nonsense_two_prize_4} mix</b><br />
 5 из 45 = <b>{nonsense_two_prize_5} mix</b><br />
 6 из 45 = <b>{nonsense_two_prize_6}</b>
</div>
<div class="nonsense_title" style="margin-top:10px">Цена билета</div>
<div class="nonsense_info">
 Стоимость одного билета <b>{nonsense_two_cost} mix.</b>
</div>
<div class="nonsense_title" style="margin-top:10px">Билеты</div>
<div class="nonsense_info">
 Куплено уже билетов: <b>{users_num}</b>
</div>
<div class="nonsense_title" style="margin-top:10px">Ваши билеты</div>
<div class="nonsense_info">
 <div style="width:150px;margin:auto">
  <div class="button_div fl_l" style="line-height:15px;margin-top:10px"><button onClick="doLoad.data(3); nonsense.biletBox()" style="width:150px">Купить билет</button></div>
  <div class="clear"></div>
 </div>
 <div id="mybilets">{my-bilets}</div>
 <div class="clear"></div>
 [prev]<div class="rate_alluser cursor_pointer border_radius_5_bottom" style="margin:-30px;margin-top:10px" onClick="doLoad.data(3); nonsense.page()" id="loto_prev_ubut"><div id="load_loto_prev_ubut">Показать больше билетов</div></div>[/prev]
</div>
</div>