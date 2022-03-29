<script type="text/javascript">
var page_cnt_app = 1;
var page_cnt_app_old = 1;
var apphre = req_href.split('apps?i=');
if(apphre[1]) apps.view(apphre[1], req_href, '/apps');
$(window).scroll(function(){
	if($(document).height() - $(window).height() <= $(window).scrollTop()+($(document).height()/2-250)){
		apps.showMore();
	}
});
</script>
<div class="search_form_tab">
<img src="{theme}/images/loading_mini.gif" class="fl_r no_display" id="apps_se_load" style="margin-left:815px;margin-top:14px;position:absolute" />
<div class="button_div fl_r addapp_button">
	<button onclick="Page.Go('/addapp');">Добавить игру</button>
</div>
<input type="text" value="Поиск по играм" class="msg_se_inp" id="query_games" 
	onBlur="if(this.value==''){this.value='Поиск по играм';this.style.color = '#c1cad0';}" 
	onFocus="if(this.value=='Поиск по играм'){this.value='';this.style.color = '#000'}" 
	onKeyPress="if(event.keyCode == 13)gSearch.go();"
	onKeyUp="apps.gSearch()"
	style="width:719px;margin:0px;color:#c1cad0" 
maxlength="65" />
</div>
<div class="clear" style="height:10px"></div>
<div id="apps_all">
<div class="apps_block" style="margin-right:20px">
 <div class="msg_speedbar">Мои игры</div>
 <div id="apps_my_games">{my_games}</div>
</div>
<div class="apps_block">
 <div class="msg_speedbar">Активность друзей</div>
 <div id="apps_activity">{activity}</div>
</div>
<div class="clear"></div>
[but-preload]<div class="public_wall_all_comm apps_but cursor_pointer doc_all_but margin_bottom_10 border_radius_5" onClick="apps.showMoreOld()"><span id="apps_text_load_old">Показать больше приложений</span></div>[/but-preload]
<div class="apps_block" style="margin-right:20px">
 <div class="apps_top border_radius_5">Популярные</div>
 <div id="apps_pop">{pop_games}</div>
</div>
<div class="apps_block"> 
 <div class="apps_top border_radius_5">Новые</div>
 <div id="apps_new">{new_games}</div>
</div>
<div class="clear" style="height:10px"></div>
[but-preload-2]<div class="public_wall_all_comm apps_but2 cursor_pointer doc_all_but margin_bottom_10" onClick="apps.showMore()"><span id="apps_text_load">Показать больше приложений</span></div>[/but-preload-2]
</div>
<div id="apps_search" class="no_display">
 <div class="msg_speedbar margin_bottom_10">Найденые игры</div>
 <div id="apps_search_res"></div>
</div>
