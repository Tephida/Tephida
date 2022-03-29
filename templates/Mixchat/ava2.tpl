<script type="text/javascript">
$(document).ready(function(){
	Xajax = new AjaxUpload('upload', {
		action: '/index.php?go=editprofile&act=upload_ava2',
		name: 'uploadfile',
		onSubmit: function (file, ext) {
		if (!(ext && /^(jpg|png|jpeg|gif|jpe)$/.test(ext))) {
			Box.Info('load_photo_er', lang_dd2f_no, lang_bad_format, 400);
				return false;
			}
			butloading('upload', '113', 'disabled', '');
		},
		onComplete: function (file, response) {
			if(response == 'bad_format')
				$('.err_red').show().text(lang_bad_format);
			else if(response == 'big_size')
				$('.err_red').show().html(lang_bad_size);
			else if(response == 'bad')
				$('.err_red').show().text(lang_bad_aaa);
			else
				window.location.href = location.href;
		}
	});
});
</script>
<div class="miniature_box">
 <div class="miniature_pos" style="width:400px">
  <div class="miniature_title fl_l apps_box_text">Загрузка аватарки</div><a class="cursor_pointer fl_r" style="font-size:12px" onClick="viiBox.clos('ava2', 1)">Закрыть</a>
  <div class="clear"></div>
   <div class="rating_text">Вы можете загрузить сюда аватарку которая будет отображаться в списке ваших друзей, на стене, в ленте и т.д. Поддерживаются форматы JPG, PNG и GIF.</div>
   <div class="load_photo_pad">
   <div class="err_red" style="display:none;font-weight:normal;"></div>
   <div class="load_photo_but" style="margin-bottom:20px"><div class="button_div fl_l"><button id="upload">Выбрать фотографию</button></div></div>
   <small>Файл не должен превышать 5 Mб. Если у Вас возникают проблемы с загрузкой, попробуйте использовать фотографию меньшего размера.</small>
   </div>
  <div class="clear"></div>
 </div>
 <div class="clear" style="height:20px"></div>
</div>