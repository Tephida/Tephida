<div class="miniature_box">
 <div class="miniature_pos" style="width:500px">
  <div class="payment_title">
   <img src="{ava}" width="50" height="50" />
   <div class="fl_l">
    Вы собираетесь пополнить Ваш счёт рублями <b>Нашего сайта</b>.<br />
    Ваш текущий баланс: <b>{rub} {text-rub}</b>
   </div>
   <div class="fl_r">
    <a class="cursor_pointer" onClick="viiBox.clos('payment', 1)">Закрыть</a>
   </div>
   <div class="clear"></div>
  </div>
  <div class="clear"></div>
  <div class="payment_h2">Выберите страну</div>
  <select id="payment_countr" onChange="payment.operator(this.value)" class="inpst payment_sel">
   <option value="0"></option>
   <option value="ru">Россия</option>
   <option value="ua">Украина</option>
   <option value="az">Азербайджан</option>
   <option value="ar">Армения</option>
   <option value="by">Белоруссия</option>
   <option value="ger">Германия</option>
   <option value="gru">Грузия</option>
   <option value="iz">Израиль</option>
   <option value="kz">Казахстан</option>
   <option value="kir">Киргизия</option>
   <option value="lat">Латвия</option>
   <option value="lit">Литва</option>
   <option value="tad">Таджикистан</option>
   <option value="est">Эстония</option>
  </select>
  <div class="payment_h2">Оператор</div>
  <select id="payment_oper" onChange="payment.cost(this.value)" class="inpst payment_sel" disabled="disabled">
   <option value="0"></option>
  </select>
  <div class="payment_h2">На сколько хотите пополнить</div>
  <select id="payment_cost" class="inpst payment_sel" onChange="payment.number(this.value)" disabled="disabled">
   <option value="0"></option>
  </select>
  <div class="payment_logo"><img src="{theme}/images/payment.png" width="256" height="256" /></div>
  <div class="no_display" id="smsblock">
  <div class="payment_block">
   Для получения <b>рублей</b> на Ваш счёт, отправьте платное SMS сообщение c текстом <b><span id="smspref"></span>{sms_number}{user-id}</b> на номер <b id="smsnumber">9797</b><br /><br />
   Стоимость сообщения равна сумме пополнения, то есть хотите пополнить на 100 руб. то SMS будет стоить 100 руб.
  </div>
  <div class="clear"></div>
  <div class="playment_but"><b>Обратите внимание</b>, если Вы не с России, то рубли будут переведены по курсу и зачислены к Вам на счёт.</div>
  </div>
 </div>
 <div class="clear" style="height:50px"></div>
</div>
<!-- Операторы -> Россия -->
<div class="operatos no_display" id="ru">
 <option value="0"></option>
 <option value="ru1">Билайн</option>
 <option value="ru2">МТС</option>
 <option value="ru3">МегаФон</option>
 <option value="ru4">НСС Саратов</option>
 <option value="ru5">Теле2</option>
 <option value="ru6">Ульяновск GSM</option>
 <option value="ru7">НСС</option>
 <option value="ru8">БайкалВестКом</option>
 <option value="ru9">Элайн GSM</option>
 <option value="ru10">Астрахань GSM</option>
 <option value="ru11">СМАРТС</option>
 <option value="ru12">Оренбург GSM</option>
 <option value="ru13">Стек GSM</option>
 <option value="ru14">ЕнисейТелеком</option>
 <option value="ru15">СибирьТелеком</option>
 <option value="ru16">АлтайСвязь</option>
 <option value="ru17">МОТИВ</option>
 <option value="ru18">Волгоград GSM</option>
 <option value="ru19">АКОС</option>
 <option value="ru20">УралСвязьИнформ</option>
 <option value="ru21">НТК</option>
</div>
<!-- /Операторы -> Россия -->
<!-- Операторы -> Украина -->
<div class="operatos no_display" id="ua">
 <option value="0"></option>
 <option value="ua1">MTC (UMC)</option>
 <option value="ua2">life:)</option>
 <option value="ua3">Kyivstar</option>
</div>
<!-- /Операторы -> Украина -->
<!-- Операторы -> Азербайджан -->
<div class="operatos no_display" id="az">
 <option value="0"></option>
 <option value="az1">Bakcell</option>
 <option value="az2">Nar Mobile (Azerfon)</option>
 <option value="az3">Azercell Telekom</option>
</div>
<!-- /Операторы -> Азербайджан -->
<!-- Операторы -> Армения -->
<div class="operatos no_display" id="ar">
 <option value="0"></option>
 <option value="ar1">Арментел</option>
 <option value="ar2">Виваселл</option>
</div>
<!-- /Операторы -> Армения -->
<!-- Операторы -> Белоруссия -->
<div class="operatos no_display" id="by">
 <option value="0"></option>
 <option value="by1">life :)</option>
 <option value="by2">МТС Беларусь</option>
 <option value="by3">Велком</option>
 <option value="by4">diallog</option>
</div>
<!-- /Операторы -> Белоруссия -->
<!-- Операторы -> Германия -->
<div class="operatos no_display" id="ger">
 <option value="0"></option>
 <option value="ger1">T-Mobile</option>
 <option value="ger2">Vodafone</option>
 <option value="ger3">ePlus</option>
 <option value="ger4">O2</option>
 <option value="ger5">Talkline</option>
 <option value="ger6">Debitel</option>
 <option value="ger7">Mobilcom</option>
</div>
<!-- /Операторы -> Германия -->
<!-- Операторы -> Германия -->
<div class="operatos no_display" id="gru">
 <option value="0"></option>
 <option value="gru1">Magticom</option>
 <option value="gru2">Geocell</option>
</div>
<!-- /Операторы -> Германия -->
<!-- Операторы -> Израиль -->
<div class="operatos no_display" id="iz">
 <option value="0"></option>
 <option value="iz1">Orange</option>
 <option value="iz2">Cellcom</option>
 <option value="iz3">Pelephone</option>
 <option value="iz4">MIRS</option>
</div>
<!-- /Операторы -> Израиль -->
<!-- Операторы -> Казахстан -->
<div class="operatos no_display" id="kz">
 <option value="0"></option>
 <option value="kz1">КаР-Тел</option>
 <option value="kz2">GSM Казахстан</option>
 <option value="kz3">Алтел</option>
 <option value="kz4">Мобайл Телеком-Сервис</option>
</div>
<!-- /Операторы -> Казахстан -->
<!-- Операторы -> Киргизия -->
<div class="operatos no_display" id="kir">
 <option value="0"></option>
 <option value="kir1">Fonex</option>
 <option value="kir2">Nexi (Sotel)</option>
 <option value="kir3">Katel</option>
 <option value="kir4">MegaCom</option>
 <option value="kir5">Beeline (Bitel. Mobi)</option>
</div>
<!-- /Операторы -> Киргизия -->
<!-- Операторы -> Латвия -->
<div class="operatos no_display" id="lat">
 <option value="0"></option>
 <option value="lat1">Теле2</option>
 <option value="lat2">LMT</option>
 <option value="lat3">Bite GSM</option>
</div>
<!-- /Операторы -> Латвия -->
<!-- Операторы -> Литва -->
<div class="operatos no_display" id="lit">
 <option value="0"></option>
 <option value="lit1">Omnitel</option>
 <option value="lit2">Bite GSM</option>
 <option value="lit3">Теле2</option>
</div>
<!-- /Операторы -> Литва -->
<!-- Операторы -> Таджикистан -->
<div class="operatos no_display" id="tad">
 <option value="0"></option>
 <option value="tad1">MLT</option>
 <option value="tad2">Indigo Somonkom</option>
 <option value="tad3">TT Mobile</option>
 <option value="tad4">MTEKO</option>
 <option value="tad5">Indigo Tajikistan</option>
</div>
<!-- /Операторы -> Таджикистан -->
<!-- Операторы -> Эстония -->
<div class="operatos no_display" id="est">
 <option value="0"></option>
 <option value="est1">EMT</option>
 <option value="est2">Radiolinija Eesti</option>
 <option value="est3">Теле2</option>
</div>
<!-- /Операторы -> Эстония -->
<!-- Операторы -> Россия -> Билайн -->
<div class="cost no_display" id="cost_ru1">
 <option value="0"></option>
 <option value="9797">169.49 руб.</option>
 <option value="8510">254.24 руб.</option>
 <option value="8503">93.22 руб.</option>
 <option value="8355">94.07 руб.</option>
 <option value="8155">65.91 руб.</option>
</div>
<!-- /Операторы -> Россия -> Билайн -->
<!-- Операторы -> Россия -> МТС -->
<div class="cost no_display" id="cost_ru2">
 <option value="0"></option>
 <option value="9797">177.97 руб.</option>
 <option value="8510">258.3 руб.</option>
 <option value="8503">100.45 руб.</option>
 <option value="8355">94.71 руб.</option>
 <option value="8155">59.32 руб.</option>
</div>
<!-- /Операторы -> Россия -> МТС -->
<!-- Операторы -> Россия -> МегаФон -->
<div class="cost no_display" id="cost_ru3">
 <option value="0"></option>
 <option value="9797">170 руб.</option>
 <option value="8510">254.24 руб.</option>
 <option value="8503">100 руб.</option>
 <option value="8355">99 руб.</option>
 <option value="8155">66 руб.</option>
</div>
<!-- /Операторы -> Россия -> МегаФон -->
<!-- Операторы -> Россия -> НСС Саратов -->
<div class="cost no_display" id="cost_ru4">
 <option value="0"></option>
 <option value="9797">211.86 руб.</option>
 <option value="8510">254.24 руб.</option>
 <option value="8503">98.42 руб.</option>
 <option value="8355">84.36 руб.</option>
 <option value="8155">56.24 руб.</option>
</div>
<!-- /Операторы -> Россия -> НСС Саратов -->
<!-- Операторы -> Россия -> Теле2 -->
<div class="cost no_display" id="cost_ru5">
 <option value="0"></option>
 <option value="9797">177 руб.</option>
 <option value="8510">211.86 руб.</option>
 <option value="8503">99 руб.</option>
 <option value="8355">99 руб.</option>
 <option value="8155">66 руб.</option>
</div>
<!-- /Операторы -> Россия -> Теле2 -->
<!-- Операторы -> Россия -> Ульяновск GSM -->
<div class="cost no_display" id="cost_ru6">
 <option value="0"></option>
 <option value="9797">211.86 руб.</option>
 <option value="8510">254.24 руб.</option>
 <option value="8503">98.42 руб.</option>
 <option value="8355">84.36 руб.</option>
 <option value="8155">56.24 руб.</option>
</div>
<!-- /Операторы -> Россия -> Ульяновск GSM -->
<!-- Операторы -> Россия -> НСС -->
<div class="cost no_display" id="cost_ru7">
 <option value="0"></option>
 <option value="9797">211.86 руб.</option>
 <option value="8510">254.24 руб.</option>
 <option value="8503">98.42 руб.</option>
 <option value="8355">84.36 руб.</option>
 <option value="8155">56.24 руб.</option>
</div>
<!-- /Операторы -> Россия -> НСС -->
<!-- Операторы -> Россия -> БайкалВестКом -->
<div class="cost no_display" id="cost_ru8">
 <option value="0"></option>
 <option value="9797">169.49 руб.</option>
 <option value="8510">287 руб.</option>
 <option value="8503">99 руб.</option>
 <option value="8355">99 руб.</option>
 <option value="8155">66 руб.</option>
</div>
<!-- /Операторы -> Россия -> БайкалВестКом -->
<!-- Операторы -> Россия -> Элайн GSM -->
<div class="cost no_display" id="cost_ru9">
 <option value="0"></option>
 <option value="9797">177 руб.</option>
 <option value="8510">296.61 руб.</option>
 <option value="8503">99 руб.</option>
 <option value="8355">90 руб.</option>
 <option value="8155">60 руб.</option>
</div>
<!-- /Операторы -> Россия -> Элайн GSM -->
<!-- Операторы -> Россия -> Астрахань GSM -->
<div class="cost no_display" id="cost_ru10">
 <option value="0"></option>
 <option value="9797">211.86 руб.</option>
 <option value="8510">258.31 руб.</option>
 <option value="8503">99.15 руб.</option>
 <option value="8355">89.83 руб.</option>
 <option value="8155">60.17 руб.</option>
</div>
<!-- /Операторы -> Россия -> Астрахань GSM -->
<!-- Операторы -> Россия -> СМАРТС -->
<div class="cost no_display" id="cost_ru11">
 <option value="0"></option>
 <option value="9797">177.97 руб.</option>
 <option value="8510">296.61 руб.</option>
 <option value="8503">99 руб.</option>
 <option value="8355">89.83 руб.</option>
 <option value="8155">60.17 руб.</option>
</div>
<!-- /Операторы -> Россия -> СМАРТС -->
<!-- Операторы -> Россия -> Оренбург GSM -->
<div class="cost no_display" id="cost_ru12">
 <option value="0"></option>
 <option value="9797">177 руб.</option>
 <option value="8510">254.24 руб.</option>
 <option value="8503">98.42 руб.</option>
 <option value="8355">84.36 руб.</option>
 <option value="8155">56.24 руб.</option>
</div>
<!-- /Операторы -> Россия -> Оренбург GSM -->
<!-- Операторы -> Россия -> Стек GSM -->
<div class="cost no_display" id="cost_ru13">
 <option value="0"></option>
 <option value="9797">177 руб.</option>
 <option value="8355">99 руб.</option>
 <option value="8155">66 руб.</option>
</div>
<!-- /Операторы -> Россия -> Стек GSM -->
<!-- Операторы -> Россия -> ЕнисейТелеком -->
<div class="cost no_display" id="cost_ru14">
 <option value="0"></option>
 <option value="9797">177 руб.</option>
 <option value="8510">296.61 руб.</option>
 <option value="8503">99 руб.</option>
 <option value="8355">99 руб.</option>
 <option value="8155">66 руб.</option>
</div>
<!-- /Операторы -> Россия -> ЕнисейТелеком -->
<!-- Операторы -> Россия -> СибирьТелеком -->
<div class="cost no_display" id="cost_ru15">
 <option value="0"></option>
 <option value="9797">177 руб.</option>
 <option value="8510">296.61 руб.</option>
 <option value="8503">99 руб.</option>
 <option value="8355">90 руб.</option>
 <option value="8155">60 руб.</option>
</div>
<!-- /Операторы -> Россия -> СибирьТелеком -->
<!-- Операторы -> Россия -> АлтайСвязь -->
<div class="cost no_display" id="cost_ru16">
 <option value="0"></option>
 <option value="9797">177 руб.</option>
 <option value="8355">99 руб.</option>
 <option value="8155">66 руб.</option>
</div>
<!-- /Операторы -> Россия -> АлтайСвязь -->
<!-- Операторы -> Россия -> МОТИВ -->
<div class="cost no_display" id="cost_ru17">
 <option value="0"></option>
 <option value="9797">177 руб.</option>
 <option value="8510">270 руб.</option>
 <option value="8503">99 руб.</option>
 <option value="8355">99 руб.</option>
 <option value="8155">66 руб.</option>
</div>
<!-- /Операторы -> Россия -> МОТИВ -->
<!-- Операторы -> Россия -> Волгоград GSM -->
<div class="cost no_display" id="cost_ru18">
 <option value="0"></option>
 <option value="9797">177 руб.</option>
 <option value="8510">296.61 руб.</option>
 <option value="8503">99 руб.</option>
 <option value="8355">99 руб.</option>
 <option value="8155">66 руб.</option>
</div>
<!-- /Операторы -> Россия -> Волгоград GSM -->
<!-- Операторы -> Россия -> АКОС -->
<div class="cost no_display" id="cost_ru19">
 <option value="0"></option>
 <option value="9797">177 руб.</option>
 <option value="8510">296.61 руб.</option>
 <option value="8503">99 руб.</option>
 <option value="8355">99 руб.</option>
 <option value="8155">66 руб.</option>
</div>
<!-- /Операторы -> Россия -> АКОС -->
<!-- Операторы -> Россия -> УралСвязьИнформ -->
<div class="cost no_display" id="cost_ru20">
 <option value="0"></option>
 <option value="9797">152.54 руб.</option>
 <option value="8510">260.17 руб.</option>
 <option value="8503">90.68 руб.</option>
 <option value="8355">90.68 руб.</option>
 <option value="8155">60.17 руб.</option>
</div>
<!-- /Операторы -> Россия -> УралСвязьИнформ -->
<!-- Операторы -> Россия -> НТК -->
<div class="cost no_display" id="cost_ru21">
 <option value="0"></option>
 <option value="9797">169 руб.</option>
 <option value="8510">260 руб.</option>
 <option value="8503">101.5 руб.</option>
 <option value="8355">86.71 руб.</option>
 <option value="8155">58 руб.</option>
</div>
<!-- /Операторы -> Россия -> НТК -->
<!-- Операторы -> Украина -> MTC (UMC) -->
<div class="cost no_display" id="cost_ua1">
 <option value="0"></option>
 <option value="2855">25 грн.</option>
 <option value="3855">50 грн.</option>
 <option value="7204">12.4 грн.</option>
 <option value="7212">8 грн.</option>
</div>
<!-- /Операторы -> Украина -> MTC (UMC) -->
<!-- Операторы -> Украина -> life:) -->
<div class="cost no_display" id="cost_ua2">
 <option value="0"></option>
 <option value="2855">25 грн.</option>
 <option value="3855">50 грн.</option>
 <option value="7204">12.4 грн.</option>
 <option value="7212">8 грн.</option>
</div>
<!-- /Операторы -> Украина -> life:) -->
<!-- Операторы -> Украина -> Kyivstar -->
<div class="cost no_display" id="cost_ua3">
 <option value="0"></option>
 <option value="2855">25 грн.</option>
 <option value="3855">50 грн.</option>
 <option value="7204">12.4 грн.</option>
 <option value="7212">8 грн.</option>
</div>
<!-- /Операторы -> Украина -> Kyivstar -->
<!-- Операторы -> Азербайджан -> Bakcell -->
<div class="cost no_display" id="cost_az1">
 <option value="0"></option>
 <option value="3301">0.9 AZN</option>
 <option value="3302">2 AZN</option>
 <option value="3303">3 AZN</option>
 <option value="3304">5 AZN</option>
</div>
<!-- /Операторы -> Азербайджан -> Bakcell -->
<!-- Операторы -> Азербайджан -> Nar Mobile (Azerfon) -->
<div class="cost no_display" id="cost_az2">
 <option value="0"></option>
 <option value="3301">0.75 AZN</option>
 <option value="3302">1.99 AZN</option>
 <option value="3303">2.99 AZN</option>
 <option value="3304">4.99 AZN</option>
</div>
<!-- /Операторы -> Азербайджан -> Nar Mobile (Azerfon) -->
<!-- Операторы -> Азербайджан -> Azercell Telekom -->
<div class="cost no_display" id="cost_az3">
 <option value="0"></option>
 <option value="4448">0.8 AZN</option>
 <option value="8171">4 AZN</option>
</div>
<!-- /Операторы -> Азербайджан -> Azercell Telekom -->
<!-- Операторы -> Армения -> Арментел -->
<div class="cost no_display" id="cost_ar1">
 <option value="0"></option>
 <option value="7122">1666.67 AMD</option>
 <option value="7132">833.33 AMD</option>
 <option value="8161">1416.67 AMD</option>
</div>
<!-- /Операторы -> Армения -> Арментел -->
<!-- Операторы -> Армения -> Виваселл -->
<div class="cost no_display" id="cost_ar2">
 <option value="0"></option>
 <option value="4446|dx">333.33 AMD</option>
 <option value="4449|dx">1000 AMD</option>
 <option value="7122">1666.67 AMD</option>
 <option value="7132">833.33 AMD</option>
 <option value="8161|dx">1416.67 AMD</option>
</div>
<!-- /Операторы -> Армения -> Виваселл -->
<!-- Операторы -> Белоруссия -> life :) -->
<div class="cost no_display" id="cost_by1">
 <option value="0"></option>
 <option value="3336">15900 byr</option>
 <option value="3337">9900 byr</option>
 <option value="3338">6900 byr</option>
 <option value="3339">29900 byr</option>
</div>
<!-- /Операторы -> Белоруссия -> life :) -->
<!-- Операторы -> Белоруссия -> МТС Беларусь -->
<div class="cost no_display" id="cost_by2">
 <option value="0"></option>
 <option value="3336">15900 byr</option>
 <option value="3337">9900 byr</option>
 <option value="3338">6900 byr</option>
 <option value="3339">29900 byr</option>
</div>
<!-- /Операторы -> Белоруссия -> МТС Беларусь -->
<!-- Операторы -> Белоруссия -> Велком -->
<div class="cost no_display" id="cost_by3">
 <option value="0"></option>
 <option value="3336">15900 byr</option>
 <option value="3337">9900 byr</option>
 <option value="3338">6900 byr</option>
 <option value="3339">29900 byr</option>
</div>
<!-- /Операторы -> Белоруссия -> Велком -->
<!-- Операторы -> Белоруссия -> diallog -->
<div class="cost no_display" id="cost_by4">
 <option value="0"></option>
 <option value="3336">9900 byr</option>
 <option value="3337">6900 byr</option>
 <option value="3338">2900 byr</option>
</div>
<!-- /Операторы -> Белоруссия -> diallog -->
<!-- Операторы -> Германия -> T-Mobile -->
<div class="cost no_display" id="cost_ger1">
 <option value="0"></option>
 <option value="88088|WWWDX">8.39 EUR</option>
</div>
<!-- /Операторы -> Германия -> T-Mobile -->
<!-- Операторы -> Германия -> Vodafone -->
<div class="cost no_display" id="cost_ger2">
 <option value="0"></option>
 <option value="88088|WWWDX">8.39 EUR</option>
</div>
<!-- /Операторы -> Германия -> Vodafone -->
<!-- Операторы -> Германия -> ePlus -->
<div class="cost no_display" id="cost_ger3">
 <option value="0"></option>
 <option value="88088|WWWDX">8.39 EUR</option>
</div>
<!-- /Операторы -> Германия -> ePlus -->
<!-- Операторы -> Германия -> O2 -->
<div class="cost no_display" id="cost_ger4">
 <option value="0"></option>
 <option value="88088|WWWDX">8.39 EUR</option>
</div>
<!-- /Операторы -> Германия -> O2 -->
<!-- Операторы -> Германия -> Talkline -->
<div class="cost no_display" id="cost_ger5">
 <option value="0"></option>
 <option value="88088|WWWDX">8.39 EUR</option>
</div>
<!-- /Операторы -> Германия -> Talkline -->
<!-- Операторы -> Германия -> Debitel -->
<div class="cost no_display" id="cost_ger6">
 <option value="0"></option>
 <option value="88088|WWWDX">8.39 EUR</option>
</div>
<!-- /Операторы -> Германия -> Debitel -->
<!-- Операторы -> Германия -> Mobilcom -->
<div class="cost no_display" id="cost_ger7">
 <option value="0"></option>
 <option value="88088|WWWDX">8.39 EUR</option>
</div>
<!-- /Операторы -> Германия -> Mobilcom -->
<!-- Операторы -> Грузия -> Magticom -->
<div class="cost no_display" id="cost_gru1">
 <option value="0"></option>
 <option value="4107|dx">4.2 GEL</option>
 <option value="4161|dx">7.46 GEL</option>
 <option value="4445|dx">1.02 GEL</option>
</div>
<!-- /Операторы -> Грузия -> Magticom -->
<!-- Операторы -> Грузия -> Geocell -->
<div class="cost no_display" id="cost_gru2">
 <option value="0"></option>
 <option value="4445|dx">1.02 GEL</option>
</div>
<!-- /Операторы -> Грузия -> Geocell -->
<!-- Операторы -> Израиль -> Orange -->
<div class="cost no_display" id="cost_iz1">
 <option value="0"></option>
 <option value="5550|dx">17.35 ILS</option>
</div>
<!-- /Операторы -> Израиль -> Orange -->
<!-- Операторы -> Израиль -> Cellcom -->
<div class="cost no_display" id="cost_iz2">
 <option value="0"></option>
 <option value="5550|dx">17.35 ILS</option>
</div>
<!-- /Операторы -> Израиль -> Cellcom -->
<!-- Операторы -> Израиль -> Pelephone -->
<div class="cost no_display" id="cost_iz3">
 <option value="0"></option>
 <option value="5550|dx">17.35 ILS</option>
</div>
<!-- /Операторы -> Израиль -> Pelephone -->
<!-- Операторы -> Израиль -> MIRS -->
<div class="cost no_display" id="cost_iz4">
 <option value="0"></option>
 <option value="5550|dx">17.35 ILS</option>
</div>
<!-- /Операторы -> Израиль -> MIRS -->
<!-- Операторы -> Казахстан -> КаР-Тел -->
<div class="cost no_display" id="cost_kz1">
 <option value="0"></option>
 <option value="7122">530.36 kzt</option>
 <option value="7132">265.18 kzt</option>
</div>
<!-- /Операторы -> Казахстан -> КаР-Тел -->
<!-- Операторы -> Казахстан -> GSM Казахстан -->
<div class="cost no_display" id="cost_kz2">
 <option value="0"></option>
 <option value="7122">530.36 kzt</option>
 <option value="7132">265.18 kzt</option>
</div>
<!-- /Операторы -> Казахстан -> GSM Казахстан -->
<!-- Операторы -> Казахстан -> Алтел -->
<div class="cost no_display" id="cost_kz3">
 <option value="0"></option>
 <option value="7122">530.36 kzt</option>
 <option value="7132">265.18 kzt</option>
</div>
<!-- /Операторы -> Казахстан -> Алтел -->
<!-- Операторы -> Казахстан -> Мобайл Телеком-Сервис -->
<div class="cost no_display" id="cost_kz4">
 <option value="0"></option>
 <option value="7122">530.36 kzt</option>
 <option value="7132">265.18 kzt</option>
</div>
<!-- /Операторы -> Казахстан -> Мобайл Телеком-Сервис -->
<!-- Операторы -> Киргизия -> Fonex -->
<div class="cost no_display" id="cost_kir1">
 <option value="0"></option>
 <option value="7122">183.41 kgs</option>
 <option value="7132">91.7 kgs</option>
</div>
<!-- /Операторы -> Киргизия -> Fonex -->
<!-- Операторы -> Киргизия -> Nexi (Sotel) -->
<div class="cost no_display" id="cost_kir2">
 <option value="0"></option>
 <option value="7122">183.41 kgs</option>
 <option value="7132">91.7 kgs</option>
</div>
<!-- /Операторы -> Киргизия -> Nexi (Sotel) -->
<!-- Операторы -> Киргизия -> Katel -->
<div class="cost no_display" id="cost_kir3">
 <option value="0"></option>
 <option value="7122">183.41 kgs</option>
 <option value="7132">91.7 kgs</option>
</div>
<!-- /Операторы -> Киргизия -> Katel -->
<!-- Операторы -> Киргизия -> MegaCom -->
<div class="cost no_display" id="cost_kir4">
 <option value="0"></option>
 <option value="7122">183.41 kgs</option>
 <option value="7132">91.7 kgs</option>
</div>
<!-- /Операторы -> Киргизия -> MegaCom -->
<!-- Операторы -> Киргизия -> Beeline (Bitel. Mobi) -->
<div class="cost no_display" id="cost_kir5">
 <option value="0"></option>
 <option value="7122">2.62 usd</option>
 <option value="7132">2.18 usd</option>
</div>
<!-- /Операторы -> Киргизия -> Beeline (Bitel. Mobi) -->
<!-- Операторы -> Латвия -> Теле2 -->
<div class="cost no_display" id="cost_lat1">
 <option value="0"></option>
 <option value="8385|XXXDX">4.1 LVL</option>
</div>
<!-- /Операторы -> Латвия -> Теле2 -->
<!-- Операторы -> Латвия -> LMT -->
<div class="cost no_display" id="cost_lat2">
 <option value="0"></option>
 <option value="8385|XXXDX">4.1 LVL</option>
</div>
<!-- /Операторы -> Латвия -> LMT -->
<!-- Операторы -> Латвия -> Bite GSM -->
<div class="cost no_display" id="cost_lat3">
 <option value="0"></option>
 <option value="8385|XXXDX">4.1 LVL</option>
</div>
<!-- /Операторы -> Латвия -> Bite GSM -->
<!-- Операторы -> Литвия -> Omnitel -->
<div class="cost no_display" id="cost_lit1">
 <option value="0"></option>
 <option value="1896|MMMDX">9.92 ltl</option>
</div>
<!-- /Операторы -> Литвия -> Omnitel -->
<!-- Операторы -> Литвия -> Bite GSM -->
<div class="cost no_display" id="cost_lit2">
 <option value="0"></option>
 <option value="1896|MMMDX">9.92 ltl</option>
</div>
<!-- /Операторы -> Литвия -> Bite GSM -->
<!-- Операторы -> Литвия -> Теле2 -->
<div class="cost no_display" id="cost_lit3">
 <option value="0"></option>
 <option value="1896|MMMDX">9.92 ltl</option>
</div>
<!-- /Операторы -> Литвия -> Теле2 -->
<!-- Операторы -> Таджикистан -> MLT -->
<div class="cost no_display" id="cost_tad1">
 <option value="0"></option>
 <option value="4161|dx">5 USD</option>
 <option value="4446|dx">2 USD</option>
 <option value="4449|dx">3 USD</option>
</div>
<!-- /Операторы -> Таджикистан -> MLT -->
<!-- Операторы -> Таджикистан -> Indigo Somonkom -->
<div class="cost no_display" id="cost_tad2">
 <option value="0"></option>
 <option value="4161|dx">5 USD</option>
 <option value="4446|dx">2 USD</option>
 <option value="4449|dx">3 USD</option>
</div>
<!-- /Операторы -> Таджикистан -> Indigo Somonkom -->
<!-- Операторы -> Таджикистан -> TT Mobile -->
<div class="cost no_display" id="cost_tad3">
 <option value="0"></option>
 <option value="4161|dx">5 USD</option>
 <option value="4446|dx">2 USD</option>
 <option value="4449|dx">3 USD</option>
</div>
<!-- /Операторы -> Таджикистан -> TT Mobile -->
<!-- Операторы -> Таджикистан -> MTEKO -->
<div class="cost no_display" id="cost_tad4">
 <option value="0"></option>
 <option value="4161|dx">5 USD</option>
 <option value="4446|dx">2 USD</option>
 <option value="4449|dx">3 USD</option>
</div>
<!-- /Операторы -> Таджикистан -> MTEKO -->
<!-- Операторы -> Таджикистан -> Indigo Tajikistan -->
<div class="cost no_display" id="cost_tad5">
 <option value="0"></option>
 <option value="4161|dx">5 USD</option>
 <option value="4446|dx">2 USD</option>
 <option value="4449|dx">3 USD</option>
</div>
<!-- /Операторы -> Таджикистан -> Indigo Tajikistan -->
<!-- Операторы -> Эстония -> EMT -->
<div class="cost no_display" id="cost_est1">
 <option value="0"></option>
 <option value="13015|dx">2.08 EUR</option>
 <option value="13017|dx">2.67 EUR</option>
 <option value="15330|dx">0.8 EUR</option>
</div>
<!-- /Операторы -> Эстония -> EMT -->
<!-- Операторы -> Эстония -> Radiolinija Eesti -->
<div class="cost no_display" id="cost_est2">
 <option value="0"></option>
 <option value="13015|dx">2.08 EUR</option>
 <option value="13017|dx">2.67 EUR</option>
 <option value="15330|dx">0.8 EUR</option>
</div>
<!-- /Операторы -> Эстония -> Radiolinija Eesti -->
<!-- Операторы -> Эстония -> Теле2 -->
<div class="cost no_display" id="cost_est3">
 <option value="0"></option>
 <option value="13015|dx">2.08 EUR</option>
 <option value="13017|dx">2.67 EUR</option>
 <option value="15330|dx">0.8 EUR</option>
</div>
<!-- /Операторы -> Эстония -> Теле2 -->