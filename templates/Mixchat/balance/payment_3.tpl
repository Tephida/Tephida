<script type="text/javascript">
$(document).ready(function(){
  payment.update();
});
</script>
<div class="miniature_box">
 <div class="miniature_pos" style="width:500px">
  <div class="payment_title">
   <img src="{ava}" width="50" height="50" />
   <div class="fl_l">
    Вы собираетесь пополнить Ваш счёт рублей <b>MixNet</b>.<br />
    Ваш текущий баланс: <b>{rub} руб.</b>
   </div>
   <div class="fl_r">
    <a class="cursor_pointer" onClick="viiBox.clos('payment_3', 1)">Закрыть</a>
   </div>
   <div class="clear"></div>
  </div>
  <div class="clear"></div>
  <div class="payment_h2" style="text-align:center">Введите желаемое количество рублей:</div>
  <center>
   <input type="text" class="inpst payment_inp" maxlength="4" id="cost_balance" onKeyUp="payment.update()" />
   <div class="rating_text_balance">У Вас <span id="rt">останется</span> <b id="num">{balance}</b> mix</div>
   <input type="hidden" id="balance" value="{balance}" />
   <input type="hidden" id="cost" value="{cost}" />
  </center>
  <div class="button_div fl_l" style="margin-left:210px;margin-top:15px"><button onClick="payment.send_three()" id="saverate">Обменять</button></div>
  <div class="clear"></div>
 </div>
 <div class="clear" style="height:50px"></div>
</div>