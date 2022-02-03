[logged]<div style="height:108px">
<div class="search_form_tab" style="margin-top:-9px;border:0px;position:absolute;width:774px">
 <div class="videos_text">Новый отзыв</div>
 <textarea 
	class="videos_input wysiwyg_inpt fl_l" 
	id="text" 
	style="width:760px;height:38px;color:#c1cad0"
	onblur="if(this.value==''){this.value='Введите текст Вашего отзыва..';this.style.color = '#c1cad0';}" 
	onfocus="if(this.value=='Введите текст Вашего отзыва..'){this.value='';this.style.color = '#000'}"
>Введите текст Вашего отзыва..</textarea>
 <div class="button_div fl_l"><button onClick="reviews.send()" id="sending">Оставить отзыв</button></div>
 <div class="clear"></div>
</div>
</div>
<div class="clear"></div>[/logged]
[not-logged]<div style="margin-top:-18px"></div>[/not-logged]
<div class="err_yellow no_display" id="ok_send" style="font-weight:normal;margin-top:30px">Ваш отзыв успешно отправлен, после проверки модератором он будет добавлен на сайт.</div>