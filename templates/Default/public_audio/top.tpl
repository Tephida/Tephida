<script type="text/javascript">
$(document).ready(function(){
	music.jPlayerInc();
	langNumric('langNumricAll', '{x-audio-num}', 'аудиозапись', 'аудиозаписи', 'аудиозаписей', 'аудиозапись', 'аудиозаписей');
});
var page_cnt = 1;
function audioAddedLoadAjax(){
	$('#wall_l_href_se_audio').attr('onClick', '');
	textLoad('wall_l_href_audio_se_load');
	$.post('/index.php?go=public_audio&act=search', {page: page_cnt, query: $('#query_audio').val(), pid: '{pid}'}, function(d){
		$('#audioAddedLoadAjax').append(d);
		$('#wall_l_href_se_audio').attr('onClick', 'audioAddedLoadAjax()');
		$('#wall_l_href_audio_se_load').html('Показать больше аудиозаписей');
		if(!d) $('#wall_l_href_se_audio').hide();
		page_cnt++;
	});
}
var xpage_cnt = 1;
function ListAudioAddedLoadAjax(){
	$('#wall_l_href_se_audiox').attr('onClick', '');
	textLoad('wall_l_href_audio_se_loadx');
	$.post('/index.php?go=public_audio&pid={pid}', {page: xpage_cnt}, function(d){
		$('#ListAudioAddedLoadAjax').append(d);
		$('#wall_l_href_se_audiox').attr('onClick', 'ListAudioAddedLoadAjax()');
		$('#wall_l_href_audio_se_loadx').html('Показать больше аудиозаписей');
		if(!d) $('#wall_l_href_se_audiox').hide();
		xpage_cnt++;
	});
}
function PublicAudioSearch(){
	if($('#query_audio').val() != 'Поиск по композициям и исполнителям' && $('#query_audio').val() != 0){
		butloading('se_but_load', 31, 'disabled');
		$.post('/index.php?go=public_audio&act=search', {query: $('#query_audio').val(), adres: '{adres}', pid: '{pid}'}, function(d){
			$('#allGrAudis').hide();
			$('#seResult').html('<div class="clear" style="height:10px"></div>'+d);
			if($('#seAudioNum').text() > 20){
				$('#seResult').html($('#seResult').html()+'<div id="audioAddedLoadAjax"></div><div class="cursor_pointer" style="margin-top:-4px" onClick="audioAddedLoadAjax()" id="wall_l_href_se_audio"><div class="public_wall_all_comm profile_hide_opne" style="width:754px" id="wall_l_href_audio_se_load">Показать больше аудиозаписей</div></div>');
			}
			butloading('se_but_load', 31, 'enabled', 'Найти');
		});
	} else
		$('#query_audio').focus();
}
function GroupAudioAddMyList(aid){
		$('.js_titleRemove').hide();
		$('#atrack_'+aid).remove();
		$('#atrackAddOk'+aid).show();
		$.post('/index.php?go=public_audio&act=addlistgroup', {aid: aid, pid: '{pid}'});
}
function PublicAudioEditsave(aid, pid){
	if($('#valartis'+aid).val() != 0) $('#artis'+aid).text($('#valartis'+aid).val());
	else $('#artis'+aid).text('Неизвестный исполнитель');
	if($('#vaname'+aid).val() != 0)	$('#name'+aid).text($('#vaname'+aid).val());
	else $('#name'+aid).text('Без названия');
	$.post('/index.php?go=public_audio&act=editsave', {aid: aid, artist: $('#valartis'+aid).val(), name: $('#vaname'+aid).val(), pid: pid});
	Box.Close();
}
function PublicAudioDelete(aid){
	Page.Loading('start');
	$('.js_titleRemove').hide();
	$.post('/index.php?go=public_audio&act=del', {aid: aid, pid: '{pid}'}, function(){
		Page.Go(location.href);
	});
}
</script>
<div id="jquery_jplayer"></div>
<input type="hidden" id="teck_id" value="0" />
<input type="hidden" id="typePlay" value="standart" />
<input type="hidden" id="teck_prefix" value="" />
<div class="cover_edit_title doc_full_pg_top" style="padding-top:13px">
<input type="text" value="Поиск по композициям и исполнителям" class="fave_input" id="query_audio" 
	onBlur="if(this.value==''){this.value='Поиск по композициям и исполнителям';this.style.color = '#c1cad0';}" 
	onFocus="if(this.value=='Поиск по композициям и исполнителям'){this.value='';this.style.color = '#000'}" 
	onKeyPress="if(event.keyCode == 13)PublicAudioSearch();" 
	style="width:670px;margin:0px;color:#c1cad0" 
maxlength="80" />
<div class="button_div fl_r"><button onClick="PublicAudioSearch(); return false" id="se_but_load">Найти</button></div>
<div class="clear"></div>
</div>
<div class="clear"></div>
<div id="seResult">
<div class="margin_top_10"></div><div class="allbar_title" style="margin-bottom:0px">{audio-num} | <a href="/{adres}" onClick="Page.Go(this.href); return false" style="font-weight:normal">К сообществу</a></div>
[no]<div class="info_center"><br /><br /><br />На странице еще нет аудиозаписей.<br /><br /><br /></div>[/no]
</div>