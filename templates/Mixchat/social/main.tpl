<script type="text/javascript">
$(document).ready(function(){
	myhtml.checked(['vk_save_cook']);
	{authologin}
});
</script>
<div class="search_form_tab" style="margin-top:-9px">
 <div class="buttonsprofile albumsbuttonsprofile buttonsprofileSecond" style="height:22px">
  <div class="buttonsprofileSec"><a href="/social#vk" onClick="; return false;"><div><b>ВКонтакте</b></div></a></div>
 </div>
</div>
<div class="clear" style="margin-top:15px"></div>
<div id="vk_page">
 <img src="{theme}/images/loading_im.gif" class="no_display" id="vk_load" style="position:absolute;margin-left:350px;margin-top:27px" />
 <div id="vk_disabled" class="no_display" style="background:#fff;width:328px;height:130px;position:absolute;opacity:0.5"></div>
 <div class="err_red no_display margin_top_10 err_logged fl_l" id="err_pass_1" style="font-weight:normal;margin-bottom:0px"><b>Не удается войти.</b><br />
Пожалуйста, проверьте правильность написания <b>логина</b> и <b>пароля.</b></div>
 <div class="clear"></div>
 <input type="text" 
	class="videos_input" 
	style="width:250px;margin-top:10px" 
	id="vk_login"
	value="{vk_login}"
	placeholder="Телефон или e-mail:" 
 />
 <div class="clear"></div>
 <input type="password" 
	class="videos_input" 
	style="width:250px" 
	id="vk_pass"
	value="{vk_pass}"
	placeholder="Пароль:" 
 />
 <div class="clear"></div>
 <div class="html_checkbox" id="vk_save_cook" onClick="myhtml.checkbox(this.id)" style="color:#777">Сохранить и автоматически входить при открытии раздела</div>
 <div class="clear" style="margin-top:20px"></div>
 <div class="clear"></div>
 <div class="html_checkbox" id="vk_data" onClick="myhtml.checkbox(this.id)" style="color:#777;margin-bottom:10px">Я даю согласие, что мои данные от ВКонтакте будут хранится в базе данных Mixnet.pro</div>
 <div class="clear" style="margin-top:20px"></div>
 <div class="button_div_gray fl_l"><button style="width:260px" onClick="vk.login(); return false">Войти в VK</button></div>
</div>