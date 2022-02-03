[top]<script type="text/javascript">
$(document).ready(function(){
	Xajax = new AjaxUpload('upload', {
		action: '/index.php?go=attach',
		name: 'uploadfile',
		onSubmit: function (file, ext) {
			if (!(ext && /^(jpg|png|jpeg|gif|jpe)$/.test(ext))) {
				addAllErr(lang_bad_format, 3300);
				return false;
			}
			Page.Loading('start');
		},
		onComplete: function (file, response){
			if(response == 'big_size'){
				addAllErr(lang_max_size, 3300);
				Page.Loading('stop');
			} else {
				var response = response.split('|||');
				response[1] = response[1].replace('/c_', '/');
				wysiwyg.boxPhoto(response[1], 0, 0);
				Page.Loading('stop');
			}
		}
	});
});
</script>
<div class="cover_edit_title">
<div class="fl_l margin_top_5">Всего {photo-num}</div>
<div class="button_div_gray fl_r"><button id="upload">Загрузить новую фотографию</button></div>
<div class="clear"></div>
</div>
<div class="clear"></div>
<div style="padding:10px;padding-bottom:15px;">[/top]
[bottom]<div class="clear"></div></div>[/bottom]