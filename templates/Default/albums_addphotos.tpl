<script type="text/javascript">
	var cnt = 0;
	var UploadedFiles = 0;
	
	function AlbumDeletePhoto(i) {
		Page.Loading('start');
		$.get('/index.php?go=albums&act=del_photo', {
			id: i
		}, function() {
			$('#photo_' + i).remove();
			var count_img = $('#count_img img').size();
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
	
	function uploadSuccess(file, serverData) {
		response = serverData;
		if (response == 'max_img') {
			Box.Info('load_photo_er2', lang_dd2f_no, lang_max_imgs, 340);
			return false
		}
		if (response == 'big_size') {
			Box.Info('load_photo_er2', lang_dd2f_no, lang_max_size, 250);
			return false
		}
		if (response == 'hacking') {
			return false
		} else {
			response = response.split('|||');
			$('<span id="photo_' + response[0] + '"></span>').appendTo('#photos').html('<div class="hralbum" style="margin:0px;background:#efefef;"></div><div id="cover_' + response[0] + '" class="covers" style="padding-bottom:10px;padding-top:10px;padding-left:10px;"><a href="/photo' + response[2] + '_' + response[0] + '_sec=loaded" onClick="Photo.Show(this.href); return false"><div class="albums_cover"><span id="count_img"><img src="' + response[1] + '" alt="" /></span></div></a><div style="float:left;"><div class="albums_name" style="color:#888;padding-bottom:5px;"><b>' + lang_albums_add_photo + '</b></div><textarea class="inpst" id="descr_' + response[0] + '" style="width:406px;height:73px;"></textarea><div class="clear"></div></div><div class="menuleft l_pppho"><a href="/" onClick="SetNewCover(\'' + response[0] + '\'); return false;" id="cover_link_' + response[0] + '" class="cover_links"><img class="icon editphoto_ic" src="/templates/Default/images/spacer.gif" alt="" /><div>' + lang_albums_set_cover + '</div></a><a href="/" onClick="AlbumDeletePhoto(\'' + response[0] + '\'); return false;"><img class="icon del_photo_ic" src="/templates/Default/images/spacer.gif" alt="" /><div>' + lang_albums_del_photo + '</div></a><a href="/" onClick="PhotoSaveDescr(\'' + response[0] + '\'); return false;"><img class="icon save_ic" src="/templates/Default/images/spacer.gif" alt="" /><div>' + lang_albums_save_descr + '</div></a></div><div class="clear"></div></div>');
			count_img = $('#count_img img').size();
			if (count_img == 1) $('#l_text').show();
			$('body, html').animate({
				scrollTop: 99999
			}, 250);
		}
	}
	
	function uploadComplete(file) {
		UploadedFiles++;
		if (UploadedFiles == cnt) {
			$('#status').html($('<p>Загружено ' + cnt + ' из ' + cnt + '</p>'));
			$('.uploadButton').css('width', '145px').css('height', '11px').css('overflow', 'inherit');
			$('#upBar, .uploadbuttbg').hide();
			$('#uploadproc').css('width', '0px');
		} else $('#status').html($('<p>Загружено ' + UploadedFiles + ' из ' + cnt + '</p>'));
	}
	
	function uploadStart(file) {
		$('.uploadButton').css('width', '0px').css('height', '0px').css('overflow', 'hidden');
		$('#upBar, .uploadbuttbg').show();
		if (cnt > 1) $('#status').html($('<p>Загружено ' + UploadedFiles + ' из ' + cnt + '</p>'));
		else $('#status').html('Фотография загружается..');
		return true;
	}
	
	function uploadProgress(file, bytesLoaded, bytesTotal) {
		pw = 270;
		var w = Math.ceil(pw * (UploadedFiles / cnt + (bytesLoaded / (file.size * cnt))));
		$('#uploadproc').css('width', w + 'px');
	}
	
	function fileDialogComplete(numFilesSelected, numFilesQueued) {
		cnt = numFilesSelected;
		UploadedFiles = 0;
	}
	
	function photos_fileQueueError(file, errorCode, message) {
		try {
			switch (errorCode) {
				case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
					$('.uploadButton').css('width', '0px').css('height', '0px').css('overflow', 'hidden');
					$('.uploadbuttbg').show();
					Box.Info('load_photo_er2', lang_dd2f_no, 'Максимально можно загрузить 20 фотографий за один раз.', 350, 3000);
					setTimeout(function() {
						$('.uploadButton').css('width', '145px').css('height', '11px').css('overflow', 'inherit');
						$('.uploadbuttbg').hide();
					}, 3000);
					break;
				case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
				case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
				case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
				case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
					break;
			}
		} catch (ex) {
			//false 
		}
	}
	var photoUpload = {
		arr: [],
		maxSize: 5 * 1024 * 1024,
		go: function(e) {
			var _a = photoUpload,
				files = e.files,
				len = files.length,
				queue = [],
				maxlen = 10;
			if (!len) return false;
			if (len > maxlen) {
				e.value = '';
				return alert('Можно загрузить до 10 фотографий');
			}
			for (var i = 0; i < len; i++) {
				var file = files[i],
					ext = file.name.split('.'),
					ext = ext[ext.length - 1];
				if (file.size > _a.maxSize) {
					e.value = '';
					return alert('Превышен максимальный размер');
				}
				queue.push(file);
			}
			_a.arr = queue;
			fileDialogComplete(len);
			uploadStart();
			_a.startUpload(queue[0]);
		},
		startUpload: function(file) {
			var _a = photoUpload,
				xhr = new XMLHttpRequest();
			xhr.upload.addEventListener('progress', function(evt) {
				if (evt.lengthComputable) {
					uploadProgress(file, evt.loaded, evt.total);
				}
			}, false);
			xhr.onreadystatechange = function(e) {
				if (e.target.readyState == 4) {
					if (e.target.status == 200) {
						_a.arr = _a.arr.slice(1);
						if (_a.arr.length > 0) _a.startUpload(_a.arr[0]);
						uploadComplete();
						uploadSuccess(file, xhr.responseText);
					}
				}
			};
			xhr.open("POST", '/index.php?go=albums&act=upload&aid={aid}', true);
			var form = new FormData();
			form.append('uploadfile', file);
			xhr.send(form);
		}
	}
</script>
<div class="buttonsprofile albumsbuttonsprofile">
	<a href="/albums/{user-id}" onClick="Page.Go(this.href); return false;">Все альбомы</a>
	<a href="/albums/view/{aid}" onClick="Page.Go(this.href); return false;">{album-name}</a>
	<div class="activetab">
		<a href="/albums/add/{aid}" onClick="Page.Go(this.href); return false;">
			<div>Добавление фотографий</div>
		</a>
	</div>
</div>
<div class="clear"></div>
<div class="load_photo_quote">Поддерживаемые форматы файлов:  JPG, PNG и GIF.</div>
<div class="h1" id="l_text" style="display:none;">Загруженные фотографии</div>
<span id="photos"></span>
<div class="clear"></div>
<div class="load_photo_but" style="margin-left:245px;">
	<div class="fl_l">
		<div class="button_div fl_l"><button onclick="this.nextSibling.click();">Выберите фотографии</button><input type="file" multiple="true" style="display:none;" onchange="photoUpload.go(this);"></div>
		<div class="uploadButton">
			<div id="uploadButton"></div>
		</div>
		<div class="uploadbuttbg no_display"></div>
	</div>
	<div class="button_div_gray fl_l" style="margin-left:10px;"><button onClick="Page.Go('/albums/view/{aid}'); return false;">Просмотр альбома</button></div>
</div>
<div class="swf_loaded" id="upBar">
	<div class="video_show_bg swf_uploaded">
		<div class="upProcLotitle" id="status"></div>
		<div style="background:url('{theme}/images/progress_grad.gif?1');border:1px solid #45688e;height:18px;position:absolute" id="uploadproc"></div>
		<div style="background:#fff;border:1px solid #cccccc;width:270px;height:18px;margin-bottom:10px"></div>
		Не закрывайте эту вкладку, пока не завершится загрузка..
	</div>
</div>
<input type="hidden" value="{aid}" id="aid" />