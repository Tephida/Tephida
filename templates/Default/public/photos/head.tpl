[top]<script type="text/javascript">
$(document).ready(function(){
	Xajax = new AjaxUpload('upload_2', {
		action: '/index.php?go=attach_groups&public_id={public_id}',
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
				Box.Close();
				groups.wall_attach_insert('photo', '/uploads/groups/{public_id}/photos/c_'+response, response)
				Page.Loading('stop');
			}
		}
	});
});
</script>
<div class="cover_edit_title fixed" style="width:590px">
<div class="fl_l margin_top_5">В альбоме сообщества {photo-num}</div>
<div class="button_div_gray fl_r"><button id="upload_2">Загрузить новую фотографию</button></div>
<div class="clear"></div>
</div>
<div class="public_wall_photos_shadow fixed"></div>
<div class="clear"></div>
<div style="padding:10px;padding-bottom:15px;margin-top:40px">[/top]
[bottom]<div class="clear"></div></div>[/bottom]