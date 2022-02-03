<script type="text/javascript">
$(document).ready(function(){
  $('#rate_num').focus().val('1');
  rating.update();
});
</script>
<div class="miniature_box">
 <div class="miniature_pos" style="width:400px">
  <div class="miniature_title fl_l apps_box_text">Повышения рейтинга</div><a class="cursor_pointer fl_r" style="font-size:12px" onClick="viiBox.clos('rate', 1)">Закрыть</a>
  <div class="clear"></div>
   <div class="rating_text">Введите ниже, на сколько Вы хотите повысить рейтинг.<br />
   Обратите внимание, что услуга считается оказанной в момент зачисления рейтинга, возврат невозможен.</div>
   <div class="rating_iny">
    + <input type="text" class="inpst" maxlength="3" id="rate_num" onKeyUp="rating.update()" />
	<div class="rating_text_balance">У Вас <span id="rt">останется</span> <b id="num">{num}</b> убм</div>
	<input type="hidden" id="balance" value="{balance}" />
   </div>
   <div class="button_div fl_l" style="margin-left:150px"><button onClick="rating.save('{user-id}')" id="saverate">Повысить рейтинг</button></div>
  <div class="clear"></div>
 </div>
 <div class="clear" style="height:20px"></div>
</div>