<script type="text/javascript">
$(document).ready(function(){
  langNumric('langNumricAll', '{x-videos-num}', 'видеозапись', 'видеозаписи', 'видеозаписей', 'видеозапись', 'видеозаписей');
});
var page_cnt = 1;
function videoAddedLoadAjax(){
  $('#wall_l_href_se_audio').attr('onClick', '');
  textLoad('wall_l_href_audio_se_load');
  $.post('/index.php?go=public_videos&act=search', {page: page_cnt, query: $('#query_audio').val(), pid: '9'}, function(d){
    $('#videoAddedLoadAjax').append(d);
    $('#wall_l_href_se_audio').attr('onClick', 'videoAddedLoadAjax()');
    $('#wall_l_href_audio_se_load').html('Показать больше видеозаписей');
    if(!d) $('#wall_l_href_se_audio').hide();
    page_cnt++;
  });
}
function PublicVideoSearch(){
  if($('#query_video').val() != 'Поиск видеозаписей по названию' && $('#query_video').val() != 0){
    butloading('se_but_load', 31, 'disabled');
    $.post('/index.php?go=public_videos&act=search', {query: $('#query_video').val(), adres: '{adres}', pid: '{pid}'}, function(d){
	  page_cnt = 1;
      $('#allGrAudis').hide();
      $('#seResult').html('<div class="clear" style="height:10px"></div>'+d);
	  if($('#seAudioNum').text() > 20){
		$('#seResult').html($('#seResult').html()+'<div id="videoAddedLoadAjax"></div><div class="cursor_pointer" style="margin-top:-4px" onClick="videoAddedLoadAjax()" id="wall_l_href_se_audio"><div class="public_wall_all_comm profile_hide_opne" style="width:754px" id="wall_l_href_audio_se_load">Показать больше видеозаписей</div></div>');
	  }
	  butloading('se_but_load', 31, 'enabled', 'Найти');
    });
  } else
    $('#query_video').focus();
}
function addVideoForPublic(i, p){
  $('#addVideoForPublic'+i).html('Добавлено');
  $.post('/index.php?go=public_videos&act=add', {id: i, pid: p});
}
var xpage_cnt = 1;
function ListAudioAddedLoadAjax(){
  $('#wall_l_href_se_audiox').attr('onClick', '');
  textLoad('wall_l_href_audio_se_loadx');
  $.post('/index.php?go=public_videos&pid={pid}', {page: xpage_cnt}, function(d){
    $('#ListAudioAddedLoadAjax').append(d);
    $('#wall_l_href_se_audiox').attr('onClick', 'ListAudioAddedLoadAjax()');
    $('#wall_l_href_audio_se_loadx').html('Показать больше видеозаписей');
    if(!d) $('#wall_l_href_se_audiox').hide();
    xpage_cnt++;
  });
}
function delVideoOutPublic(i, p){
  $('#v'+i).html(lang_videos_delok);
  $.post('/index.php?go=public_videos&act=del', {id: i, pid: p});
}
function videoPubEditBox(i, p){
  Box.Page('/index.php?go=public_videos&act=edit', 'id='+i+'&pid='+p, 'edit_video', 510, lang_video_edit, lang_box_canсel, lang_box_save, 'videoPubEditSave('+i+', '+p+'); return false', 255, 0, 1, 1, 0);
}
function videoPubEditSave(i, p){
  var title = $('#title').val();
  var descr = $('#descr').val();
  $('#box_but').hide();
  $('#box_loading').fadeIn();
  $.post('/index.php?go=public_videos&act=edit_save', {id: i, pid: p, title: title, descr: descr}, function(d){
    $('#video_title_'+i).text(title);
    $('#video_descr_'+i).html(d);
    Box.Close('edit_video');
  });
}
</script>
<div class="cover_edit_title doc_full_pg_top" style="padding-top:13px">
<input type="text" value="Поиск видеозаписей по названию" class="fave_input" id="query_video" 
	onBlur="if(this.value==''){this.value='Поиск видеозаписей по названию';this.style.color = '#c1cad0';}" 
	onFocus="if(this.value=='Поиск видеозаписей по названию'){this.value='';this.style.color = '#000'}" 
	onKeyPress="if(event.keyCode == 13) PublicVideoSearch();" 
	style="width:670px;margin:0px;color:#c1cad0" 
maxlength="80" />
<div class="button_div fl_r"><button onClick="PublicVideoSearch(); return false" id="se_but_load">Найти</button></div>
<div class="clear"></div>
</div>
<div class="clear"></div>
<div id="seResult">
<div class="margin_top_10"></div><div class="allbar_title" style="margin-bottom:0px;[yes]border-bottom:0px[/yes]">{videos-num} | <a href="/{adres}" onClick="Page.Go(this.href); return false" style="font-weight:normal">К сообществу</a></div>
[no]<div class="info_center"><br /><br /><br />На странице еще нет видеозаписей.<br /><br /><br /></div>[/no]
</div>