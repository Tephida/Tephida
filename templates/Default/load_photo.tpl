<script type="text/javascript">
$(document).ready(function(){
	Xajax = new AjaxUpload('upload', {
		action: '/index.php?go=editprofile&act=upload',
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
			else {
				Box.Close('photo');
				$('#ava').html('<img src="'+response+'" alt="" />');
				$('body, html').animate({scrollTop: 0}, 250);
				$('#del_pho_but').show();
			}
		}
	});
});
</script>
<div class="load_photo_pad">
<div class="err_red" style="display:none;font-weight:normal;"></div>
<div class="load_photo_quote">Вы можете загрузить сюда только собственную фотографию. Поддерживаются форматы JPG, PNG и GIF.</div>
<div class="load_photo_but"><div class="button_div fl_l"><button id="upload">Выбрать фотографию</button></div></div>
<small>Файл не должен превышать 5 Mб. Если у Вас возникают проблемы с загрузкой, попробуйте использовать фотографию меньшего размера.</small>
</div>