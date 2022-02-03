<script type="text/javascript">
$(document).ready(function(){
	$('#miniature_crop').imgAreaSelect({
		handles: true,
		aspectRatio: '4:4',
		minHeight: 100,
		minWidth: 100,
		x1: 0,
		y1: 0,
		x2: 100,
		y2: 100,
		onSelectEnd: function(img, selection){
			$('#mi_left').val(selection.x1);
			$('#mi_top').val(selection.y1);
			$('#mi_width').val(selection.width);
			$('#mi_height').val(selection.height);
		},
		onSelectChange: Profile.preview
	});
});
</script>
<div class="miniature_box">
 <div class="miniature_pos">
  <div class="miniature_title fl_l">Выбор миниатюры</div><a class="cursor_pointer fl_r" onClick="Profile.miniatureClose()">Закрыть</a>
  <div class="miniature_text clear">Осталось выбрать квадратную область для маленьких фотографий.<br />
  Выбранная миниатюра будет использоваться в новостях, личных сообщениях и комментариях.</div>
  <div class="miniature_img">
   <img src="/uploads/users/{user-id}/{ava}" width="200" id="miniature_crop" class="fl_l" />
   <div id="miniature_crop_100" style="width:100px;height:100px;overflow:hidden"><img src="/uploads/users/{user-id}/{ava}" /></div>
   <div id="miniature_crop_50" style="width:50px;height:50px;overflow:hidden"><img src="/uploads/users/{user-id}/{ava}" /></div>
   <div class="button_div fl_l"><button onClick="Profile.miniatureSave()" id="miniatureSave">Сохранить изменения</button></div>
  </div>
  <input type="hidden" id="mi_left" />
  <input type="hidden" id="mi_top" />
  <input type="hidden" id="mi_width" />
  <input type="hidden" id="mi_height" />
  <div class="clear"></div>
 </div>
</div>