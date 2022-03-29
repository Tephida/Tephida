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
</script><style type="text/css">
<!--
/** 
 * Slideshow style rules.
 */
#slideshow {
    height: 146px;
    margin: 10px auto 25px;
    position: relative;
    width: 770px;
}
#slideshow #slidesContainer {
    height: 180px;
    margin: 0 auto;
    overflow: auto;
    position: relative;
    width: 720px;
}
#slideshow #slidesContainer .slide {
   float: left;
    height: 180px;
    width: 240px;
}


.control {
	cursor: pointer;
    display: block;
    height: 147px;
    position: absolute;
    text-indent: -10000px;
    width: 30px;
}

.slide img {
  float:right;
  margin:0 15px;
  border-radius: 10px;
}
.appsslider{
margin-top:10px;
}
-->
</style>
<script type="text/javascript">

$(document).ready(function(){
	var currentPosition = 0;
	var slideWidth = 720;
	var slides = $('.slide');
	var numberOfSlides = slides.length;

	$('#slidesContainer').css('overflow', 'hidden');
	slides
    .wrapAll('<div id="slideInner"></div>');
	$('#slideInner').css('width', slideWidth * numberOfSlides);
	$('#slideshow')
    .prepend('<span class="control" id="leftControl"></span>')
    .append('<span class="control" id="rightControl"></span>');
	manageControls(currentPosition);
	$('.control')
	.bind('click', function(){
	if(document.getElementById('slideInner').style.marginLeft=='-1440px'){currentPosition=0;}else{
		currentPosition = ($(this).attr('id')=='rightControl') ? currentPosition+1 : currentPosition-1;
		}
		manageControls(currentPosition);
		
		$('#slideInner').animate({
			'marginLeft' : slideWidth*(-currentPosition)
		});
		
	});
	function manageControls(position){
		if(position==0){ $('#leftControl').hide() } else{ $('#leftControl').show() }
		if(position==numberOfSlides-1){ $('#rightControl').hide() } else{ $('#rightControl').show(); }
	}	
});
</script>
<div class="search_form_tab">
<img src="/images/loading_mini.gif" class="fl_r no_display" id="apps_se_load" style="margin-left:730px;margin-top:8px;position:absolute" />
<input type="text" value="Поиск по играм" class="fave_input" id="query_games" 
	onBlur="if(this.value==''){this.value='Поиск по играм';this.style.color = '#c1cad0';}" 
	onFocus="if(this.value=='Поиск по играм'){this.value='';this.style.color = '#000'}" 
	onKeyPress="if(event.keyCode == 13)gSearch.go();"
	onKeyUp="apps.gSearch()"
	style="width:740px;margin:0px;color:#c1cad0" 
	maxlength="65" />
</div>


<div class="clear" style="height:20px"></div>
<div class="appsslider">
<div class="apps_title">Рекомендуемые приложения</div>
<div id="slideshow">
    <div id="slidesContainer">
		{slider}
    </div>
</div>
</div>
<div id="apps_all">
	<div class="apps_block" style="margin-right:20px">
		<div class="apps_title">Мои игры</div>
		<div id="apps_my_games">
			{mygame}
		</div>
	</div>
	<div class="apps_block">
		<div class="apps_title">Активность друзей</div>
		<div id="apps_activity">
			{frgame}
		</div>
	</div>
	<div class="clear" style="height:20px"></div>
	<div class="public_wall_all_comm apps_but cursor_pointer" onClick="apps.showMoreOld()">
		<span id="apps_text_load_old">Показать больше приложений</span>
	</div>
	<div class="apps_block" style="margin-right:20px">
		<div class="apps_top">Популярные</div>
		<div id="apps_pop">
			{popgame}
		</div>
	</div>
	<div class="apps_block"> 
		<div class="apps_top">Новые</div>
		<div id="apps_new">
			{newgame}
			<div class="clear"></div>
		</div>
	</div>
	<div class="clear" style="height:20px"></div>
	<div class="public_wall_all_comm apps_but2 cursor_pointer" onClick="apps.showMore()">
		<span id="apps_text_load">Показать больше приложений</span>
	</div>
</div>
<div id="apps_search" class="no_display">
	<div class="apps_title">Найденые игры</div>
	<div id="apps_search_res"></div>
</div>
<div class="clear"></div>