<div class="search_form_tab" style="margin-top:-10px">
 <div class="buttonsprofile albumsbuttonsprofile buttonsprofileSecond" style="height:22px">
  <a href="/about" onClick="Page.Go(this.href); return false;"><div><b>О Сайте</b></div></a>
  <a href="/terms" onClick="Page.Go(this.href); return false;"><div><b>Правила сайта</b></div></a>
  <div class="buttonsprofileSec"><a href="/review" onClick="Page.Go(this.href); return false;"><div><b>Отзывы</b></div></a></div>
 </div>
</div>
<div class="mn_boxes" style="margin-top:20px">Правила размещения отзывов</div>
<div id="mn_show_count" style="padding: 10px 10px;">
<center><div class="color777" style="padding: 30px 40px;"><b>Ещё не придумал правила :(</b></div>
</div>
<div class="mn_boxes" style="margin-top:10px">Отзывы о сайте<span>{r_cnt}</span></div>
<div class="note_add_bg clear support_addform forum_addmsgbg" style="margin-top:0px;margin-left:0px;width: 740px;">
<div class="ava_mini">
 <a href="/u{my-uid}" onClick="Page.Go(this.href); return false"><img src="{my-ava}" alt="" width="50" height="50" /></a>
</div>
<textarea 
	class="videos_input wysiwyg_inpt fl_l" 
	id="text" 
	name="text"
	style="width:577px;height:37px;color:#c1cad0;margin-left:3px;color:#000"
	placeholder="Написать отзыв.."
	onKeyPress="if(event.keyCode == 10 || (event.ctrlKey && event.keyCode == 13)) review.add()"
></textarea>
<div class="clear"></div>
<div class="button_rev fl_r" style="margin-top:-61px;"><button style="height:51px" onClick="review.add(); return false" id="add_text">Отправить</button></div>
<div class="clear"></div>
</div><br />