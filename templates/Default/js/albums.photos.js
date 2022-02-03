$(document).ready(function() {
	var aid = $('#aid').val();
	Xajax = new AjaxUpload('upload', {
		action: '/index.php?go=albums&act=upload&aid=' + aid,
		name: 'uploadfile',
		onSubmit: function(file, ext) {
			if (!(ext && /^(jpg|png|jpeg|gif|jpe)$/.test(ext))) {
				Box.Info('load_photo_er', lang_dd2f_no, lang_bad_format, 400);
				return false;
			}
			Page.Loading('start');
		},
		onComplete: function(file, response) {
			if (response == 'max_img') {
				Box.Info('load_photo_er2', lang_dd2f_no, lang_max_imgs, 340);
				Page.Loading('stop');
				return false
			}
			if (response == 'big_size') {
				Box.Info('load_photo_er2', lang_dd2f_no, lang_max_size, 250);
				Page.Loading('stop');
				return false
			}
			if (response == 'hacking') {
				return false
			} else {
				response = response.split('|||');
				$('<span id="photo_' + response[0] + '"></span>').appendTo('#photos').html('<div class="hralbum" style="margin:0px;background:#efefef;"></div><div id="cover_' + response[0] + '" class="covers" style="padding-bottom:10px;padding-top:10px;padding-left:10px;"><a href="/photo' + response[2] + '_' + response[0] + '_sec=loaded" onClick="Photo.Show(this.href); return false"><div class="albums_cover"><span id="count_img"><img src="' + response[1] + '" alt="" /></span></div></a><div style="float:left;"><div class="albums_name" style="color:#888;padding-bottom:5px;"><b>' + lang_albums_add_photo + '</b></div><textarea class="inpst" id="descr_' + response[0] + '" style="width:406px;height:73px;"></textarea><div class="clear"></div></div><div class="menuleft l_pppho"><a href="/" onClick="SetNewCover(\'' + response[0] + '\'); return false;" id="cover_link_' + response[0] + '" class="cover_links"><img class="icon editphoto_ic" src="/templates/Default/images/spacer.gif" alt="" /><div>' + lang_albums_set_cover + '</div></a><a href="/" onClick="AlbumDeletePhoto(\'' + response[0] + '\'); return false;"><img class="icon del_photo_ic" src="/templates/Default/images/spacer.gif" alt="" /><div>' + lang_albums_del_photo + '</div></a><a href="/" onClick="PhotoSaveDescr(\'' + response[0] + '\'); return false;"><img class="icon save_ic" src="/templates/Default/images/spacer.gif" alt="" /><div>' + lang_albums_save_descr + '</div></a></div><div class="clear"></div></div>');
				var count_img = $('#count_img img').length;
				if (count_img == 1) $('#l_text').show();
				$('body, html').animate({
					scrollTop: 99999
				}, 250);
				Page.Loading('stop');
			}
		}
	});
});

function AlbumDeletePhoto(i) {
	Page.Loading('start');
	$.get('/index.php?go=albums&act=del_photo', {
		id: i
	}, function() {
		$('#photo_' + i).remove();
		var count_img = $('#count_img img').length;
		if (count_img < 1) $('#l_text').hide();
		Page.Loading('stop');
	});
}

function SetNewCover(i) {
	Page.Loading('start');
	$.get('/index.php?go=albums&act=set_cover', {
		id: i
	}, function() {
		$('.covers').css('background', '#fff');
		$('#cover_' + i).css('background', '#f6f9fb').css('border-top', '1px solid #fff');
		$('.cover_links').show();
		$('#cover_link_' + i).hide();
		Page.Loading('stop');
	});
}

function PhotoSaveDescr(i) {
	var descr = $('#descr_' + i).val();
	Page.Loading('start');
	$.post('/index.php?go=albums&act=save_descr', {
		id: i,
		descr: descr
	}, function(d) {
		Page.Loading('stop');
	});
}