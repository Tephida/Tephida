<div class="search_form_tab" style="background:#fff;margin-top:-9px">
 <div class="buttonsprofile albumsbuttonsprofile" style="height:14px">
  <div><b>Восстановление доступа к странице</b></div>
 </div>
</div>
<div style="margin-top:29px"></div>
<div class="note_add_bg support_bg" id="step1">
<div class="err_red no_display name_errors" id="err" style="font-weight:normal;width:370px"></div>
Пожалуйста, укажите <b>e-mail</b>, который Вы использовали для входа на сайт. 
<input type="text" 
	class="videos_input fl_l" 
	style="width:300px;margin-top:10px;color:#c1cad0" 
	maxlength="65" 
	id="email"
	onblur="if(this.value==''){this.value='Ваш электронный адрес';this.style.color = '#c1cad0';}" 
	onfocus="if(this.value=='Ваш электронный адрес'){this.value='';this.style.color = '#000'}"
	value="Ваш электронный адрес"
/>
<div class="button_div fl_l margin_left" style="margin-top:11px"><button onClick="restore.next(); return false" id="send">Далее</button></div>
<div class="clear"></div>
<div class="input_hr" style="width:315px"></div>
</div>
<div class="note_add_bg support_bg no_display" id="step2">
Это та страница, к которой необходимо восстановить доступ?
<div class="clear"></div>
<img src="" alt="" style="margin-top:11px;margin-right:10px" align="left" id="c_src" />
<div style="margin-top:11px;font-size:13px;color:#21578b" id="c_name"></div>
<div class="clear"></div>
<div class="button_div fl_l" style="margin-top:11px"><button onClick="restore.send(); return false" id="send2">Да, это нужная страница</button></div>
<div class="clear"></div>
</div>
<div class="note_add_bg support_bg no_display"  id="step3">На ваш электронный ящик были высланы инструкции по восстановлению пароля.</div>