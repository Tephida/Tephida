//ALBUMS
var Albums = {
	CreatAlbum: function() {
		Page.Loading('start');
		$.post('/index.php?go=albums&act=create_page', function(data) {
			Box.Show('albums', 450, lang_title_new_album, data, lang_box_canсel, lang_album_create, 'StartCreatAlbum(); return false;', 0, 0, 1, 1);
			$('#name').focus();
			Page.Loading('stop');
		});
	},
	Delete: function(id, hash) {
		Box.Show('del_album_' + id, 350, lang_title_del_photo, '<div style="padding:15px;">' + lang_del_album + '</div>', lang_box_canсel, lang_box_yes, 'Albums.StartDelete(' + id + ', \'' + hash + '\'); return false;');
	},
	StartDelete: function(id, hash) {
		$('#box_loading').show();
		$.post('/index.php?go=albums&act=del_album', {
			id: id,
			hash: hash
		}, function(d) {
			Box.Close('del_album_' + id);
			$('#album_' + id).remove();
			updateNum('#albums_num');
			if ($('.albums').length < 1) Page.Go(location.href);
		});
	},
	Drag: function() {
		$("#dragndrop ul").sortable({
			cursor: 'move',
			opacity: 0.9,
			scroll: false,
			update: function() {
				var order = $(this).sortable("serialize");
				$.post("/index.php?go=albums&act=save_pos_albums", order, function() {});
			}
		});
	},
	EditBox: function(id) {
		Page.Loading('start');
		$.post('/index.php?go=albums&act=edit_page', {
			id: id
		}, function(d) {
			Page.Loading('stop');
			Box.Show('edit_albums_' + id, 450, lang_edit_albums, d, lang_box_canсel, lang_box_save, 'Albums.SaveDescr(' + id + '); return false', 0, 0, 1, 1);
		});
	},
	SaveDescr: function(id) {
		var name = $("#name_" + id).val();
		var descr = $("#descr_t" + id).val();
		if (name != 0) {
			$("#name_" + id).css('background', '#fff');
			$('#box_loading').show();
			$.post('/index.php?go=albums&act=save_album', {
				id: id,
				name: name,
				descr: descr,
				privacy: $('#privacy').val(),
				privacy_comm: $('#privacy_comment').val()
			}, function(data) {
				$('#box_loading').hide();
				if (data == 'no_name') {
					$('.err_red').show().text(lang_empty);
					ge('box_but').disabled = false;
				} else if (data == 'no') {
					$('.err_red').show().text(lang_nooo_er);
					ge('box_but').disabled = false;
				} else {
					Box.Close('edit_albums_' + id);
					row = data.split('|#|||#row#|||#|');
					$('#descr_' + id).html('<div style="padding-top:4px;">' + row[1] + '</div>');
					$('#albums_name_' + id).html(row[0]);
				}
			});
		} else {
			$("#name_" + id).css('background', '#ffefef');
			setTimeout("$('#name_" + id + "').css('background', '#fff').focus()", 800);
			$('#box_loading').hide();
		}
	},
	EditCover: function(id, page_num) {
		if (page_num) page = '&page=' + page_num;
		else {
			page = '';
			page_num = 1;
		}
		Box.Page('/index.php?go=albums&act=edit_cover', //URL
			'id=' + id + page, //POST данные
			'edit_cover_' + id + page_num, //ID
			627, //Ширина окна
			lang_edit_cover_album, //Заголовок окна
			lang_box_canсel, //Имя кнопки для закртие окна
			0, //Текст кнопки выполняющая функцию
			0, //Сама функция для выполнения
			400, //Высота окна
			'overflow', //Скролл
			'bg_show_top', //Внутреняя тень окна верх
			'bg_show_bottom', //Внутреняя тень окна низ
			'', 1);
	},
	SetCover: function(id, aid, photo) {
		$('#box_loading').show();
		$.get('/index.php?go=albums&act=set_cover', {
			id: id
		}, function() {
			$('#cover_' + aid).html('<img src="' + photo + '" alt="" />');
			Box.Close('edit_cover_' + aid);
			$('#box_loading').hide();
		});
	}
}
//PHOTOS
var Photo = {
	addrating: function(r, i, s) {
		$('#ratpos' + i).hide();
		if (r == 6) {
			$('#rateload' + i).show();
		} else {
			$('#ratingyes' + i).html('<div class="ratingyestext fl_l">Ваша оценка</div> <div id="addratingyes' + i + '"></div>').css('width', '120px').css('padding-top', '0px');
			if (r == 1) $('#addratingyes' + i).html('<div class="rating rating3" style="background:url(\'' + template_dir + '/images/rating3.png\')">1</div>');
			else $('#addratingyes' + i).html('<div class="rating rating3">' + r + '</div>');
			$('#ratingyes' + i).fadeIn('fast');
		}
		$.post('/index.php?go=photo&act=addrating', {
			rating: r,
			pid: i
		}, function(d) {
			$('#rateload' + i).hide();
			if (d == 1) {
				$('#ratingyes' + i).html('У Вас недостаточно голосов. <a class="cursor_pointer" onClick="$(\'#ratpos' + i + '\').show();$(\'#ratingyes' + i + '\').hide();">Поставить другую оценку</a>').css('width', '290px').css('color', '#777').css('padding-top', '19px').fadeIn('fast');
				return false;
			}
			if (r == 6) {
				$('#addratingyes' + i).html('<div class="rating rating3" style="background:url(\'' + template_dir + '/images/rating2.png\')">5+</div>');
				$('#ratingyes' + i).fadeIn('fast');
			}
		});
	},
	allrating: function(i) {
		viiBox.start();
		$.post('/index.php?go=photo&act=view_rating', {
			pid: i
		}, function(d) {
			viiBox.win('ph', d);
		});
	},
	prev_users: function(i) {
		if ($('#load_rate_prev_ubut').text() == 'Показать предыдущие оценки') {
			textLoad('load_rate_prev_ubut');
			var lid = $('.rate_block:last').attr('id');
			$.post('/index.php?go=photo&act=view_rating', {
				pid: i,
				lid: lid
			}, function(d) {
				$('#rates_usersJQ').append(d);
				$('#load_rate_prev_ubut').text('Показать предыдущие оценки');
				if (!d) $('#rate_prev_ubut').hide();
			});
		}
	},
	delrate: function(i) {
		$('#delrate' + i).html('Оценка удалена.');
		$('#rate_vbss' + i).fadeOut(100);
		$.post('/index.php?go=photo&act=del_rate', {
			id: i
		});
	},
	Drag: function() {
		$("#dragndrop ul").sortable({
			cursor: 'move',
			scroll: false,
			update: function() {
				var order = $(this).sortable("serialize");
				$.post("/index.php?go=albums&act=save_pos_photos", order);
			}
		});
	},
	Show: function(h) {
		Distinguish.GeneralClose();
		var id = h.split('_');
		var uid = id[0].split('photo');
		var section = h.split('sec=');
		var fuser = h.split('wall/fuser=');
		var note_id = h.split('notes/id=');
		var msg_id = h.split('msg/id=');
		if (fuser[1]) section[1] = 'wall';
		if (note_id[1]) {
			section[1] = 'notes';
			fuser[1] = note_id[1];
		}
		if (msg_id[1]) {
			section[1] = 'msg';
			fuser[1] = msg_id[1];
		}
		$('.photo_view').hide();
		if (is_moz && !is_chrome) scrollTopForFirefox = $(window).scrollTop();
		$('html').css('overflow', 'hidden');
		if (is_moz && !is_chrome) $(window).scrollTop(scrollTopForFirefox);
		if (ge('photo_view_' + id[1])) {
			$('#photo_view_' + id[1]).show();
			history.pushState({
				link: h
			}, null, h);
		} else {
			Photo.Loading('start');
			$.post('/index.php?go=photo', {
				uid: uid[1],
				pid: id[1],
				section: section[1],
				fuser: fuser[1]
			}, function(d) {
				if (d == 'no_photo') {
					Photo.Loading('stop');
					Box.Info('no_video', lang_dd2f_no, lang_photo_info_text, 300);
					$('html, body').css('overflow-y', 'auto');
					return false;
				} else if (d == 'err_privacy') {
					Photo.Loading('stop');
					addAllErr(lang_pr_no_title);
					$('html, body').css('overflow-y', 'auto');
				}
				if (section[1] != 'loaded') history.pushState({
					link: h
				}, null, h);
				$('body').append(d);
				$('#photo_view_' + id[1]).show();
				Photo.Loading('stop');
			});
		}
	},
	Profile: function(uid, photo, type) {
		Photo.Loading('start');
		$.post('/index.php?go=photo&act=profile', {
			uid: uid,
			photo: photo,
			type: type
		}, function(d) {
			Photo.Loading('stop');
			if (d == 'no_photo') {
				Box.Info('no_video', lang_dd2f_no, lang_photo_info_text, 300);
				$('html, body').css('overflow-y', 'auto');
			} else {
				$('body').append(d);
				$('#photo_view').show();
				$('html, body').css('overflow-y', 'hidden');
			}
		});
	},
	Prev: function(h) {
		var id = h.split('_');
		$('.photo_view').hide();
		$('html, body').css('overflow', 'hidden');
		$('.pinfo, .photo_prev_but, .photo_next_but').show();
		$('.save_crop_text').hide();
		$('.ladybug_ant').imgAreaSelect({
			remove: true
		});
		if (ge('photo_view_' + id[1])) {
			$('#photo_view_' + id[1]).show();
			return false;
		} else {
			Photo.Show(h);
		}
	},
	Close: function(close_link) {
		$('.ladybug_ant').imgAreaSelect({
			remove: true
		});
		$('.photo_view').remove();
		$('html, body').css('overflow-y', 'auto');
		if (is_moz && !is_chrome) $(window).scrollTop(scrollTopForFirefox);
		if (close_link != false) history.pushState({
			link: close_link
		}, null, close_link);
	},
	Loading: function(f) {
		if (f == 'start') {
			if (is_moz && !is_chrome) scrollTopForFirefox = $(window).scrollTop();
			$('html').css('overflow', 'hidden');
			if (is_moz && !is_chrome) $(window).scrollTop(scrollTopForFirefox);
			var loadcontent = '<div class="photo_view" id="photo_load" style="padding-right:17px" onClick="Photo.setEvent(event, false)">' + '<div class="photo_close" onClick="Photo.LoadingClose(); return false" style="right:15px;"></div>' + '<div class="photo_bg" style="height:310px;padding-top:290px;">' + '<center><img src="/templates/Default/images/progress.gif" alt="" /></center>' + '</div>' + '</div>';
			$('body').append(loadcontent);
			$('#photo_load').show();
		}
		if (f == 'stop') $('#photo_load').remove();
	},
	LoadingClose: function() {
		$('#photo_load').remove();
		$('html, body').css('overflow-y', 'auto');
	},
	Init: function(target) {
		this.target = $(target);
		var that = this;
		$(window).scroll(function() {
			if ($(document).height() - $(window).height() <= $(window).scrollTop()) {
				alert(1);
			}
		});
	},
	Panel: function(id, f) {
		if (f == 'show') $('#albums_photo_panel_' + id).show();
		else $('#albums_photo_panel_' + id).hide();
	},
	MsgDelete: function(id, aid, type) {
		Box.Show('del_photo_' + id, '400', lang_title_del_photo, '<div style="padding:15px;">' + lang_del_photo + '</div>', lang_box_canсel, lang_box_yes, 'Photo.Delete(' + id + ', ' + aid + ', ' + type + '); return false');
	},
	Delete: function(id, aid, type) {
		$('#box_loading').show();
		$.get('/index.php?go=albums&act=del_photo', {
			id: id
		}, function() {
			Box.Close('del_photo_' + id);
			if (!type) {
				$('#a_photo_' + id).remove();
				$('#p_jid' + id).remove();
				updateNum('#photo_num');
			} else $('#pinfo_' + id).html(lang_photo_info_delok);
		});
	},
	SetCover: function(id, jid) {
		Page.Loading('start');
		$.get('/index.php?go=albums&act=set_cover', {
			id: id
		}, function() {
			$('.albums_new_cover').fadeOut();
			$('#albums_new_cover_' + jid).fadeIn();
			Page.Loading('stop');
		});
	},
	EditBox: function(id, r) {
		Page.Loading('start');
		$.get('/index.php?go=albums&act=editphoto', {
			id: id
		}, function(data) {
			Box.Show('edit_photo_' + id, '400', 'Редактирование фотографии', '<div class="box_ppad"><div  style="color:#888;padding-bottom:5px;"><b>Описание фотографии</b></div><textarea class="inpst" id="descr_' + id + '" style="width:355px;height:71px;">' + data + '</textarea></div>', 'Отмена', 'Сохранить', 'Photo.SaveDescr(' + id + ', ' + r + '); return false');
			Page.Loading('stop');
		});
	},
	SaveDescr: function(id, r) {
		var descr = $('#descr_' + id).val();
		$('#box_loading').show();
		$.post('/index.php?go=albums&act=save_descr', {
			id: id,
			descr: descr
		}, function(d) {
			Box.Close('edit_photo_' + id);
			if (r == 1) $('.photo_view').remove();
			else $('#photo_descr_' + id).html(d);
		});
	},
	setEvent: function(event, close_link) {
		var oi = (event.target) ? event.target.id : ((event.srcElement) ? event.srcElement.id : null);
		var el = oi.substring(0, 10);
		if (el == 'photo_view' || el == 'photo_load') Photo.Close(close_link);
	},
	Rotation: function(pos, id) {
		$('#loading_gradus' + id).show();
		$.post('/index.php?go=photo&act=rotation', {
			id: id,
			pos: pos
		}, function(d) {
			var rndval = new Date().getTime();
			$('#ladybug_ant' + id).attr('src', d + '?' + rndval);
			$('#loading_gradus' + id).hide();
		});
	}
}
//PHOTOS COMMENTS
var comments = {
	add: function(id) {
		var comment = $('#textcom_' + id).val();
		if (comment != 0) {
			butloading('add_comm', '56', 'disabled', '');
			$.post('/index.php?go=photo&act=addcomm', {
				pid: id,
				comment: comment
			}, function(data) {
				if (data == 'err_privacy') {
					addAllErr(lang_pr_no_title);
				} else {
					$('#comments_' + id).append(data);
					$('#textcom_' + id).val('');
				}
				butloading('add_comm', '56', 'enabled', lang_box_send);
			});
		} else {
			$('#textcom_' + id).val('');
			$('#textcom_' + id).focus();
		}
	},
	delet: function(id, hash) {
		textLoad('del_but_' + id);
		$.post('/index.php?go=photo&act=del_comm', {
			hash: hash
		}, function() {
			$('#comment_' + id).html('<div style="padding-bottom:5px;color:#777;">' + lang_del_comm + '</div>');
		});
	},
	delet_page_comm: function(id, hash) {
		textLoad('full_del_but_' + id);
		$.post('/index.php?go=photo&act=del_comm', {
			hash: hash
		}, function() {
			$('#comment_all_' + id).html('<div style="padding-bottom:5px;color:#777;">' + lang_del_comm + '</div>');
		});
	},
	all: function(id, num) {
		textLoad('all_lnk_comm_' + id);
		$('#all_href_lnk_comm_' + id).attr('onClick', '').attr('href', '#');
		$.post('/index.php?go=photo&act=all_comm', {
			pid: id,
			num: num
		}, function(d) {
			$('#all_href_lnk_comm_' + id).hide();
			$('#all_comments_' + id).html(d);
		});
	},
}
//FRIENDS
var friends = {
	add: function(for_id, user_name) {
		if (for_id) {
			Page.Loading('start');
			if (user_name) name = user_name;
			else name = $('title').text();
			$.get('/friedns/send_demand/' + for_id, function(data) {
				if (data == 'antispam_err') {
					AntiSpam('friends');
					return false;
				}
				if (data == 'yes_demand') Box.Info('add_demand_' + for_id, lang_demand_ok, lang_demand_no, 300);
				else if (data == 'yes_demand2') Box.Info('add_demand_k_' + for_id, lang_dd2f_no, lang_dd2f22_no, 300);
				else if (data == 'yes_friend') Box.Info('add_demand_k_' + for_id, lang_dd2f_no, lang_22dd2f22_no, 300);
				else Box.Info('add_demand_ok_' + for_id, lang_demand_ok, '<b><a href="' + location.href + '" onClick="Page.Go(this.href); return false">' + name + '</a></b> ' + lang_demand_s_ok, 400);
				Page.Loading('stop');
			});
		}
	},
	sending_demand: function(for_id) {
		Box.Info('add_sending_demand_' + for_id, lang_demand_sending, lang_demand_sending_t);
	},
	take: function(take_user_id) {
		Page.Loading('start');
		$.get('/friedns/take/' + take_user_id, function(data) {
			Page.Loading('stop');
			$('#action_' + take_user_id).html(lang_take_ok).css('color', '#777');
		});
	},
	reject: function(reject_user_id) {
		Page.Loading('start');
		$.get('/friedns/reject/' + reject_user_id, function(data) {
			Page.Loading('stop');
			$('#action_' + reject_user_id).html(lang_take_no);
		});
	},
	delet: function(user_id, atype) {
		if (atype) {
			var ava_s1 = $('#ava_' + user_id).attr('src');
			var ava = ava_s1.replace('/users/' + user_id + '/', '/users/' + user_id + '/100_');
		} else var ava = $('#ava_' + user_id).attr('src');
		Box.Show('del_friend_' + user_id, 410, lang_title_del_photo, '<div style="padding:15px;text-align:center"><img src="' + ava + '" alt="" /><br /><br />Вы уверены, что хотите удалить этого пользователя из списка друзей?</div>', lang_box_canсel, lang_box_yes, 'friends.goDelte(' + user_id + ', ' + atype + '); return false');
	},
	goDelte: function(user_id, atype) {
		$('#box_loading').show();
		$.post('/index.php?go=friends&act=delete', {
			delet_user_id: user_id
		}, function(data) {
			if (atype > 0) {
				Page.Go(location.href);
			} else {
				$('#friend_' + user_id).remove();
				updateNum('#friend_num');
			}
			Box.Close('del_friend_' + user_id);
		});
	}
}
//FAVE
var fave = {
	add: function(fave_id) {
		$('#addfave_load').show();
		$.post('/index.php?go=fave&act=add', {
			fave_id: fave_id
		}, function(data) {
			if (data == 'no_user') Box.Info('add_fave_err_' + fave_id, lang_dd2f_no, lang_no_user_fave, 300);
			else if (data == 'yes_user') Box.Info('add_fave_err_' + fave_id, lang_dd2f_no, lang_yes_user_fave, 300);
			$('#addfave_but').attr('onClick', 'fave.delet(' + fave_id + '); return false').attr('href', '/');
			$('#text_add_fave').text(lang_del_fave);
			$('#addfave_load').hide();
		});
	},
	delet: function(fave_id) {
		$('#addfave_load').show();
		$.post('/index.php?go=fave&act=delet', {
			fave_id: fave_id
		}, function(data) {
			$('#addfave_but').attr('onClick', 'fave.add(' + fave_id + '); return false').attr('href', '/');
			$('#text_add_fave').text(lang_add_fave);
			$('#addfave_load').hide();
		});
	},
	del_box: function(fave_id) {
		Box.Show('del_fave', 410, lang_title_del_photo, '<div style="padding:15px;">' + lang_fave_info + '</div>', lang_box_canсel, lang_box_yes, 'fave.gDelet(' + fave_id + '); return false');
	},
	gDelet: function(fave_id) {
		$('#box_loading').show();
		$.post('/index.php?go=fave&act=delet', {
			fave_id: fave_id
		}, function(data) {
			$('#user_' + fave_id).remove();
			Box.Close('del_fave');
			fave_num = $('#fave_num').text();
			$('#fave_num').text(fave_num - 1);
			if ($('#fave_num').text() < 1) {
				$('#speedbar').text(lang_dd2f_no);
				$('#page').html(lang_fave_no_users);
			}
		});
	}
}
//MESSAGES
var messages = {
	new_: function(user_id) {
		var content = '<div style="padding:20px">' + '<div class="texta" style="width:100px">Тема:</div><input type="text" id="theme" class="inpst" maxlength="255" style="width:300px" /><div class="mgclr"></div>' + '<div class="texta" style="width:100px">Сообщение:</div><textarea id="msg" class="inpst" style="width:300px;height:120px;"></textarea><div class="mgclr"></div>' + '</div>';
		Box.Show('new_msg', 460, lang_new_msg, content, lang_box_canсel, lang_new_msg_send, 'messages.send(' + user_id + '); return false');
		$('#msg').focus();
	},
	send: function(for_user_id) {
		var theme = $('#theme').val();
		var msg = $('#msg').val();
		if (msg != 0) {
			$('#box_loading').show();
			$.post('/index.php?go=im&act=send', {
				for_user_id: for_user_id,
				theme: theme,
				msg: msg
			}, function(data) {
				Box.Close('new_msg');
				if (data == 'antispam_err') {
					AntiSpam('messages');
					return false;
				}
				if (data == 'max_strlen') Box.Info('msg_info', lang_dd2f_no, lang_msg_max_strlen, 300);
				else if (data == 'no_user') Box.Info('msg_info', lang_dd2f_no, lang_no_user_fave, 300);
				else if (data == 'err_privacy') Box.Info('msg_info', lang_pr_no_title, lang_pr_no_msg, 400, 4000);
				else Box.Info('msg_info', lang_msg_ok_title, lang_msg_ok_text, 300);
			});
		} else {
			$('#msg').val('');
			$('#msg').focus();
		}
	}
}
//NOTES
var notes = {
	send: function() {
		var title = $('#title_n').val();
		var text = $('#text').val();
		if (title != 0) {
			if (text != 0) {
				butloading('notes_sending', 74, 'disabled');
				$.post('/index.php?go=notes&act=save', {
					title: title,
					text: text
				}, function(d) {
					if (d == 'min_strlen') Box.Info('msg_notes', lang_dd2f_no, lang_notes_no_text, 300);
					else Page.Go('/notes/view/' + d);
					butloading('notes_sending', 74, 'enabled', 'Опубликовать');
				});
			} else {
				$("#text").val('');
				$("#text").css('background', '#ffefef').focus();
				setTimeout("$('#text').css('background', '#fff').focus()", 800);
			}
		} else {
			$("#title").val('');
			$("#title").css('background', '#ffefef').focus();
			setTimeout("$('#title').css('background', '#fff').focus()", 800);
		}
	},
	editsave: function(note_id) {
		var title = $('#title_n').val();
		var text = $('#text').val();
		if (title != 0) {
			if (text != 0) {
				butloading('notes_sending', 111, 'disabled');
				$.post('/index.php?go=notes&act=editsave', {
					note_id: note_id,
					title: title,
					text: text
				}, function(d) {
					if (d == 'min_strlen') Box.Info('msg_notes', lang_dd2f_no, lang_notes_no_text, 300);
					else Page.Go('/notes/view/' + note_id);
					butloading('notes_sending', 111, 'enabled', 'Сохранить изменения');
				});
			} else {
				$("#text").val('');
				$("#text").css('background', '#ffefef').focus();
				setTimeout("$('#text').css('background', '#fff').focus()", 800);
			}
		} else {
			$("#title").val('');
			$("#title").css('background', '#ffefef').focus();
			setTimeout("$('#title').css('background', '#fff').focus()", 800);
		}
	},
	delet: function(note_id, lnk, uid) {
		Box.Show('del_note_' + note_id, 400, lang_title_del_photo, '<div style="padding:15px;" id="text_del_note_' + note_id + '">' + lang_del_note + '</div>', lang_box_canсel, lang_box_yes, 'notes.startDel(' + note_id + ', ' + lnk + ', ' + uid + '); return false');
	},
	startDel: function(note_id, lnk, uid) {
		$('#box_loading').show();
		$('#text_del_note_' + note_id).text(lang_del_process);
		$.post('/index.php?go=notes&act=delet', {
			note_id: note_id
		}, function() {
			if (lnk) Page.Go('/notes');
			else {
				$('#note_' + note_id).remove();
				updateNum('#notes_num');
			}
			Box.Close('del_note_' + note_id);
		});
	},
	addcomment: function(note_id) {
		var textcom = $('#textcom').val();
		if (textcom != 0) {
			butloading('add_comm_notes', 119, 'disabled');
			$.post('/index.php?go=notes&act=addcomment', {
				note_id: note_id,
				textcom: textcom
			}, function(data) {
				$('#resultadd').append(data);
				$('#textcom').val('');
				butloading('add_comm_notes', 119, 'enabled', 'Добавить комментарий');
			});
		} else {
			$('#textcom').focus();
			$('#textcom').val('');
		}
	},
	deletcomment: function(comm_id) {
		textLoad('note_del_but_' + comm_id);
		$.post('/index.php?go=notes&act=delcomment', {
			comm_id: comm_id
		}, function(d) {
			$('#note_comment_' + comm_id).html(lang_del_comm);
		});
	},
	allcomments: function(note_id, comm_num) {
		textLoad('all_lnk_comm');
		$('#all_href_lnk_comm').attr('onClick', '').attr('href', '#');
		$.post('/index.php?go=notes&act=allcomment', {
			note_id: note_id,
			comm_num: comm_num
		}, function(data) {
			$('#all_comments').html(data);
			$('#all_href_lnk_comm').hide();
		});
	},
	preview: function() {
		var title = $('#title_n').val();
		var text = $('#text').val();
		if (title != 0) {
			if (text != 0) {
				Box.Page('/index.php?go=notes&act=preview', 'text=' + text + '&title=' + title, 'preview', 820, lang_notes_preview, lang_msg_close, 0, 0, 500, 1, 1, 1, 0, 0);
			} else {
				$("#text").html('');
				$("#text").css('background', '#ffefef').focus();
				setTimeout("$('#text').css('background', '#fff').focus()", 800);
			}
		} else {
			$("#title").val('');
			$("#title").css('background', '#ffefef').focus();
			setTimeout("$('#title').css('background', '#fff').focus()", 800);
		}
	}
}
//SUBSCRIPTIONS
var subscriptions = {
	add: function(for_user_id) {
		$('#addsubscription_load').show();
		$.post('/index.php?go=subscriptions&act=add', {
			for_user_id: for_user_id
		}, function(d) {
			$('#addsubscription_load').hide();
			$('#text_add_subscription').text(lang_unsubscribe);
			$('#lnk_unsubscription').attr('onClick', 'subscriptions.del(' + for_user_id + '); return false');
		});
	},
	del: function(del_user_id) {
		$('#addsubscription_load').show();
		$.post('/index.php?go=subscriptions&act=del', {
			del_user_id: del_user_id
		}, function() {
			$('#addsubscription_load').hide();
			$('#text_add_subscription').text(lang_subscription);
			$('#lnk_unsubscription').attr('onClick', 'subscriptions.add(' + del_user_id + '); return false');
		});
	},
	all: function(for_user_id, page_num, subscr_num) {
		if (page_num) page = '&page=' + page_num;
		else {
			page = '';
			page_num = 1;
		}
		Box.Page('/index.php?go=subscriptions&act=all', 'for_user_id=' + for_user_id + '&subscr_num=' + subscr_num + page, 'subscriptions_' + page_num, 525, lang_subscription_box_title, lang_msg_close, 0, 0, 345, 1, 1, 1, 0, 1);
	}
}
//VIDEOS
var videos = {
	add: function(notes) {
		Box.Page('/index.php?go=videos&act=add', '', 'add_video', 510, lang_video_new, lang_box_canсel, lang_album_create, 'videos.send(' + notes + '); return false', 0, 0, 1, 1, 'video_lnk');
	},
	load: function() {
		video_lnk = $('#video_lnk').val();
		good_video_lnk = $('#good_video_lnk').val();
		if (videos.serviece(video_lnk)) {
			if (video_lnk != 0) {
				if (video_lnk != good_video_lnk) {
					$('#box_loading').show();
					$.post('/index.php?go=videos&act=load', {
						video_lnk: video_lnk
					}, function(data) {
						if (data == 'no_serviece') {
							$('#no_serviece').show();
						} else {
							row = data.split(':|:');
							$('#result_load').show();
							$('#photo').html('<img src="' + row[0] + '" alt="" id="res_photo_ok" />');
							$('#title').val(row[1]);
							$('#descr').val(row[2]);
							$('#good_video_lnk').val(video_lnk);
							$('#no_serviece').hide();
						}
						$('#box_but').show();
						$('#box_loading').hide();
					});
				} else $('#no_serviece').hide();
			} else $('#result_load').hide();
		} else $('#no_serviece').show();
	},
	serviece: function(request) {
		var pattern = new RegExp(/http:\/\/www.youtube.com|http:\/\/youtube.com|http:\/\/rutube.ru|http:\/\/www.rutube.ru|http:\/\/www.vimeo.com|http:\/\/vimeo.com|http:\/\/smotri.com|http:\/\/www.smotri.com/i);
		return pattern.test(request);
	},
	send: function(notes) {
		title = $('#title').val();
		good_video_lnk = $('#good_video_lnk').val();
		descr = $('#descr').val();
		photo = $('#res_photo_ok').attr('src');
		if (good_video_lnk != 0) {
			if (title != 0) {
				$('#box_loading').show();
				$('#box_but').hide();
				$.post('/index.php?go=videos&act=send', {
					title: title,
					good_video_lnk: good_video_lnk,
					descr: descr,
					photo: photo,
					privacy: $('#privacy').val(),
					notes: notes
				}, function(d) {
					$('#box_loading').hide();
					Box.Close('add_video');
					d = d.split('|');
					if (notes == 1) wysiwyg.boxVideo('http://' + location.host + d[0], d[1], d[2]);
					else Page.Go('/videos');
				});
			} else Box.Info('msg_videos', lang_dd2f_no, lang_videos_no_url, 300);
		} else Box.Info('msg_videos', lang_dd2f_no, lang_videos_no_url, 300);
	},
	page: function() {
		name = $('.scroll_page').attr('id');
		get_user_id = $('#user_id').val();
		last_id = $('#' + name + ' input:last').attr('value');
		set_last_id = $('#set_last_id').val();
		videos_size = $('#videos_num').val();
		videos_opened_num = $('.onevideo').length;
		if (set_last_id != last_id && videos_size > 20 && videos_size != videos_opened_num) {
			$('#' + name).append(lang_scroll_loading);
			$.post('/index.php?go=videos&act=page', {
				get_user_id: get_user_id,
				last_id: last_id
			}, function(d) {
				$('#' + name).append(d);
				$('#scroll_loading').remove();
			});
			$('#set_last_id').val(last_id);
		}
	},
	scroll: function() {
		$(window).scroll(function() {
			if ($(document).height() - $(window).height() <= $(window).scrollTop() + 250) {
				videos.page();
			}
		});
	},
	delet: function(vid, type) {
		Box.Show('del_video_' + vid, 400, lang_title_del_photo, '<div style="padding:15px;" id="text_del_video_' + vid + '">' + lang_videos_del_text + '</div>', lang_box_canсel, lang_box_yes, 'videos.startDel(' + vid + ', \'' + type + '\'); return false');
		$('#video_object').hide(); //скрываем код видео, чтоб модал-окно норм появилось
	},
	startDel: function(vid, type) {
		$('#box_but').hide();
		$('#box_loading').show();
		$('#text_del_video_' + vid).text(lang_videos_deletes);
		$.post('/index.php?go=videos&act=delet', {
			vid: vid
		}, function() {
			$('#video_' + vid).html(lang_videos_delok);
			Box.Close('del_video_' + vid);
			updateNum('#nums');
			if (type == 1) $('#video_del_info').html(lang_videos_delok_2);
		});
	},
	editbox: function(vid) {
		Box.Page('/index.php?go=videos&act=edit', 'vid=' + vid, 'edit_video', 510, lang_video_edit, lang_box_canсel, lang_box_save, 'videos.editsave(' + vid + '); return false', 255, 0, 1, 1, 0);
		$('#video_object').hide(); //скрываем код видео, чтоб модал-окно норм появилось
	},
	editsave: function(vid) {
		var title = $('#title').val();
		var descr = $('#descr').val();
		$('#box_but').hide();
		$('#box_loading').fadeIn();
		$.post('/index.php?go=videos&act=editsave', {
			vid: vid,
			title: title,
			descr: descr,
			privacy: $('#privacy').val()
		}, function(d) {
			$('#video_title_' + vid + ', #video_full_title_' + vid).text(title);
			$('#video_descr_' + vid + ', #video_full_descr_' + vid).html(d);
			Box.Close('edit_video');
			$('#video_object').show(); //показываем код видео, чтоб модал-окно норм появилось
		});
	},
	loading: function(f) {
		if (f == 'start') {
			$('html').css('overflow', 'hidden');
			var loadcontent = '<div class="photo_view" id="video_load">' + '<div class="photo_close" onClick="videos.loadingClose(); return false" style="right:15px;"></div>' + '<div class="video_show_bg">' + '<div class="video_show_object" style="padding-top:230px;height:230px"><center><img src="/templates/Default/images/progress_gray.gif" alt="" /></center></div><div class="video_show_panel" style="height:20px"></div>' + '</div>' + '</div>';
			$('body').append(loadcontent);
			$('#video_load').show();
		}
		if (f == 'stop') $('#video_load').remove();
	},
	loadingClose: function() {
		$('#video_load').remove();
	},
	show: function(vid, h, close_link) {
		if (vid) {
			videos.loading('start');
			$.post('/index.php?go=videos&act=view', {
				vid: vid,
				close_link: close_link
			}, function(data) {
				videos.loading('stop');
				if (data == 'no_video') {
					Box.Info('no_video', lang_dd2f_no, lang_video_info_text, 300);
					$('html, body').css('overflow-y', 'auto');
				} else if (data == 'err_privacy') {
					addAllErr(lang_pr_no_title);
					$('html, body').css('overflow-y', 'auto');
				} else {
					$('html').css('overflow', 'hidden');
					history.pushState({
						link: h
					}, null, h);
					$('body').append(data);
					$('#video_show_' + vid).show();
				}
			});
		}
	},
	prev: function(req_href) {
		filter_one = req_href.split('_');
		filter_two = filter_one[0].split('video');
		video_url = '/video' + filter_two[1] + '_' + filter_one[1];
		videos.show(filter_one[1], video_url);
	},
	close: function(owner_id, close_link) {
		$('.video_view').remove();
		$('html, body').css('overflow-y', 'auto');
		if (is_moz && !is_chrome) $(window).scrollTop(scrollTopForFirefox);
		if (close_link) history.pushState({
			link: close_link
		}, null, close_link);
		else history.pushState({
			link: '/videos/' + owner_id
		}, null, '/videos/' + owner_id);
	},
	addcomment: function(vid) {
		comment = $('#comment').val();
		if (comment != 0) {
			butloading('add_comm', '56', 'disabled', '');
			$.post('/index.php?go=videos&act=addcomment', {
				vid: vid,
				comment: comment
			}, function(d) {
				$('#comments').append(d);
				$('#comment').val('');
				butloading('add_comm', '56', 'enabled', lang_box_send);
			});
		} else {
			$('#comment').val('');
			$('#comment').focus();
		}
	},
	allcomment: function(vid, num, owner_id) {
		textLoad('all_lnk_comm');
		$('#all_href_lnk_comm').attr('onClick', '').attr('href', '#');
		$.post('/index.php?go=videos&act=all_comm', {
			vid: vid,
			num: num,
			owner_id: owner_id
		}, function(d) {
			$('#all_href_lnk_comm').hide();
			$('#all_comments').html(d);
		});
	},
	deletcomm: function(comm_id) {
		textLoad('video_del_but_' + comm_id);
		$.post('/index.php?go=videos&act=delcomment', {
			comm_id: comm_id
		}, function() {
			$('#video_comment_' + comm_id).html(lang_del_comm);
		});
	},
	setEvent: function(event, owner_id, close_link) {
		var oi = (event.target) ? event.target.id : ((event.srcElement) ? event.srcElement.id : null);
		var el = oi.substring(0, 10);
		if (el == 'video_show') videos.close(owner_id, close_link);
	},
	addmylist: function(vid) {
		$('#addok').html('Добавлено');
		$.post('/index.php?go=videos&act=addmylist', {
			vid: vid
		});
	}
}
//SEARCH
var selenter = false;
$(function() {
	$('#search_types, #search_tab, #se_link').on('mouseenter', function() {
		selenter = true;
	});
	$('#search_types, #search_tab, #se_link').on('mouseleave', function() {
		selenter = false;
	});
});
$(document).click(function() {
	if (!selenter) {
		$('#sel_types, #search_tab, .fast_search_bg').hide();
		$('#query').val('');
	}
});

function CheckRequestSearch(request) {
	var pattern = new RegExp(/search/i);
	return pattern.test(request);
}
var gSearch = {
	open_tab: function() {
		$('#search_tab').fadeIn('fast');
		$('#query').focus();
		if ($('#fast_search_txt').text()) $('.fast_search_bg').fadeIn('fast');
	},
	open_types: function(id) {
		$(id).show();
	},
	select_type: function(type, text) {
		$('#se_type').val(type);
		$('#search_selected_text').text(text);
		$('.search_type_selected').removeClass();
		$('#' + type).addClass('search_type_selected');
		$('#sel_types').hide();
		$('#query').focus();
	},
	go: function() {
		var query = $('#query').val();
		var type = $('#se_type').val();
		if (query == 'Поиск' || !query) var query = $('#fast_search_txt').text();
		//Если открыта страница поиска
		if (CheckRequestSearch(location.href)) {
			query = $('#query_full').val();
			sex = $('#sex').val();
			day = $('#day').val();
			month = $('#month').val();
			year = $('#year').val();
			country = $('#country').val();
			city = $('#select_city').val();
			type = $('#se_type_full').val();
			online = $('#online').val();
			user_photo = $('#user_photo').val();
		}
		if (query == 'Поиск' || query == 'Начните вводить любое слово или имя') {
			query = '';
		}
		//if(query != 0 && query != 'Поиск' && query != 'Начните вводить любое слово или имя'){
		if (CheckRequestSearch(location.href) && type == 1) {
			if (sex != 0) all_queryeis_sex = '&sex=' + sex;
			else all_queryeis_sex = '';
			if (day != 0) all_queryeis_day = '&day=' + day;
			else all_queryeis_day = '';
			if (month != 0) all_queryeis_month = '&month=' + month;
			else all_queryeis_month = '';
			if (year != 0) all_queryeis_year = '&year=' + year;
			else all_queryeis_year = '';
			if (country != 0) all_queryeis_country = '&country=' + country;
			else all_queryeis_country = '';
			if (city != 0) all_queryeis_city = '&city=' + city;
			else all_queryeis_city = '';
			if (online != 0) all_queryeis_online = '&online=' + online;
			else all_queryeis_online = '';
			if (user_photo != 0) all_queryeis_user_photo = '&user_photo=' + user_photo;
			else all_queryeis_user_photo = '';
			res_sort_query = all_queryeis_sex + all_queryeis_day + all_queryeis_month + all_queryeis_year + all_queryeis_country + all_queryeis_city + all_queryeis_online + all_queryeis_user_photo;
		} else res_sort_query = '';
		lnk = '/?go=search&query=' + encodeURIComponent(query) + '&type=' + type + res_sort_query;
		Page.Loading('start');
		$.post(lnk, {
			ajax: 'yes'
		}, function(data) {
			Page.Loading('stop');
			history.pushState({
				link: lnk
			}, null, lnk);
			$('#page').html(data);
			//Прокручиваем страницу в самый верх
			$('html, body').scrollTop(0);
			//Удаляем кеш фоток и видео
			$('.photo_view, .box_pos, .box_info, .video_view').remove();
			//Возвращаем scroll
			$('html, body').css('overflow-y', 'auto');
			$('#sel_types, #search_tab, .fast_search_bg').hide();
			$('#query').val('');
		});
		/*} else {
			$('#query, #query_full').val('');
			$('#query, #query_full').focus();
		}*/
	}
}
//CHECKBOX
var myhtml = {
	checkbox: function(id) {
		name = '#' + id;
		$(name).addClass('html_checked');
		if (ge('checknox_' + id)) {
			myhtml.checkbox_off(id);
		} else {
			$(name).append('<div id="checknox_' + id + '"><input type="hidden" id="' + id + '" /></div>');
			$(name).val('1');
		}
	},
	checkbox_off: function(id) {
		name = '#' + id;
		$('#checknox_' + id).remove();
		$(name).removeClass('html_checked');
		$(name).val('');
	},
	checked: function(arr) {
		$.each(arr, function() {
			myhtml.checkbox(this);
		});
	},
	title: function(id, text, prefix_id, pad_left) {
		if (!pad_left) pad_left = 5;
		$("body").append('<div id="js_title_' + id + '" class="js_titleRemove"><div id="easyTooltip">' + text + '</div><div class="tooltip"></div></div>');
		xOffset = $('#' + prefix_id + id).offset().left - pad_left;
		yOffset = $('#' + prefix_id + id).offset().top - 32;
		$('#js_title_' + id).css("position", "absolute").css("top", yOffset + "px").css("left", xOffset + "px").css("z-index", "1000").fadeIn('fast');
		$('#' + prefix_id + id).mouseout(function() {
			$('.js_titleRemove').remove();
		});
	},
	title_close: function(id) {
		$('#js_title_' + id).remove();
	},
	updateAjaxNav: function(gc, pref, num, page) {
		$.get('/updateAjaxNav', {
			gcount: gc,
			pref: pref,
			num: num,
			page: page
		}, function(data) {
			$('#nav').html(data);
		});
	},
	scrollTop: function() {
		$('.scroll_fix_bg').hide();
		$(window).scrollTop(0);
	}
}
//WALL
var prevAnsweName = false;
var comFormValID = false;
var wall = {
	form_open: function() {
		$('#wall_input').hide();
		$('#wall_textarea').show();
		$('#wall_text').val('');
		$('#wall_text').focus();
	},
	form_close: function() {
		wall_text = $('#wall_text').val();
		if (wall_text != 0) {
			$('#wall_input').val($('#wall_text').val());
		} else {
			$('#wall_input').show();
			$('#wall_textarea').hide();
			$('#wall_input').val($('#wall_input_text').val());
		}
	},
	event: function(event) {
		oi = (event.target) ? event.target.id : ((event.srcElement) ? event.srcElement.id : null);
		fast_oi = oi.substring(0, 9);
		attach_files = $('#vaLattach_files').val();
		if (oi != 'wall_tab' && oi != 'wall_input' && oi != 'wall_textarea' && oi != 'wall_text' && oi != 'wall_send' && oi != 'wall_attach' && oi != 'wall_attach_link' && !attach_files) wall.form_close();
		if (fast_oi != 'fast_form' && fast_oi != 'fast_link' && fast_oi != 'fast_inpt' && fast_oi != 'fast_text' && fast_oi != 'fast_buts' && oi != 'answer_lnk') wall.fast_form_close();
		//скрываем форму установки статуса
		if (oi != 'set_status_bg' && oi != 'status_text' && oi != 'status_but' && oi != 'status_link' && oi != 'new_status') gStatus.close();
	},
	send: function() {
		wall_text = $('#wall_text').val();
		attach_files = $('#vaLattach_files').val();
		for_user_id = location.href.split('http://' + location.host + '/u');
		rec_num = parseInt($('#wall_rec_num').text()) + 1;
		if (!rec_num) rec_num = 1;
		if (wall_text != 0 || attach_files != 0) {
			butloading('wall_send', 56, 'disabled');
			$.post('/index.php?go=wall&act=send', {
				wall_text: wall_text,
				for_user_id: for_user_id[1],
				attach_files: attach_files,
				vote_title: $('#vote_title').val(),
				vote_answer_1: $('#vote_answer_1').val(),
				vote_answer_2: $('#vote_answer_2').val(),
				vote_answer_3: $('#vote_answer_3').val(),
				vote_answer_4: $('#vote_answer_4').val(),
				vote_answer_5: $('#vote_answer_5').val(),
				vote_answer_6: $('#vote_answer_6').val(),
				vote_answer_7: $('#vote_answer_7').val(),
				vote_answer_8: $('#vote_answer_8').val(),
				vote_answer_9: $('#vote_answer_9').val(),
				vote_answer_10: $('#vote_answer_10').val()
			}, function(data) {
				if (data == 'antispam_err') {
					AntiSpam('wall');
					return false;
				}
				if (data == 'err_privacy') {
					addAllErr(lang_pr_no_title);
				} else {
					$('#wall_records').html(data);
					$('#wall_all_record').html('');
					$('#wall_rec_num').text(rec_num)
					$('#wall_text').val('');
					$('#attach_files').hide();
					$('#attach_files').html('');
					$('#vaLattach_files').val('');
					wall.form_close();
					wall.RemoveAttachLnk();
					Votes.RemoveForAttach();
				}
				butloading('wall_send', 56, 'enabled', lang_box_send);
			});
		} else {
			$('#wall_text').val('');
			$('#wall_text').focus();
		}
	},
	delet: function(rid) {
		var rec_num = parseInt($('#wall_rec_num').text()) - 1;
		if (!rec_num) rec_num = '';
		$('#wall_record_' + rid).html(lang_wall_del_ok);
		$('#wall_fast_block_' + rid).remove();
		$('#wall_rec_num').text(rec_num);
		myhtml.title_close(rid);
		$.post('/index.php?go=wall&act=delet', {
			rid: rid
		});
	},
	fast_comm_del: function(rid) {
		$('#wall_fast_comment_' + rid).html(lang_wall_del_com_ok);
		$.post('/index.php?go=wall&act=delet', {
			rid: rid
		});
	},
	page: function(for_user_id) {
		if ($('#wall_link').text() == 'к предыдущим записям') {
			textLoad('wall_link');
			$('#wall_l_href').attr('onClick', '');
			last_id = $('.wallrecord:last').attr('id').replace('wall_record_', '');
			rec_num = parseInt($('#wall_rec_num').text());
			$.post('/index.php?go=wall&act=page', {
				last_id: last_id,
				for_user_id: for_user_id
			}, function(data) {
				$('#wall_all_record').append(data);
				$('#wall_l_href').attr('onClick', 'wall.page(' + for_user_id + '); return false');
				$('#wall_link').html(lang_wall_all_lnk);
				count_record = $('.wallrecord').length;
				if (count_record >= rec_num) $('#wall_l_href').hide();
			});
		}
	},
	open_fast_form: function(rid) {
		val = $('.wall_fast_text').val();
		$('.wall_fast_text').val(''); //Текстовое значение полей Texatrea делаем 0
		$('.wall_fast_form, .wall_fast_texatrea').hide(); //закрываем окно комментирование и полей textarea комментирования
		$('.wall_fast_input, .fast_comm_link').show(); //возвращаем input поле со словом "Комментировать..." и кнопку комменатировать
		$('#fast_form_' + rid).show(); //показываем форум комментирования
		$('#fast_comm_link_' + rid).hide(); //скрываем кнопку комментировать
	},
	fast_form_close: function() {
		if (!$('#fast_text_' + comFormValID).val()) {
			$('.wall_fast_text, .answer_comm_id').val(''); //Текстовое значение полей Texatrea делаем 0
			$('.wall_fast_form, .wall_fast_texatrea').hide(); //закрываем окно комментирование и полей textarea комментирования
			$('.wall_fast_input, .fast_comm_link').show(); //возвращаем input поле со словом "Комментировать..." и кнопку комменатировать
			$('.answer_comm_for').text('');
		}
	},
	fast_open_textarea: function(rid, type) {
		$('.wall_fast_text').val(''); //Текстовое значение полей Texatrea делаем 0
		comFormValID = rid;
		//Если действия уже из открытой формы
		if (type == 2) {
			$('.wall_fast_input').show(); //Возвращаем всем input слово "Комментировать..."
			$('.wall_fast_texatrea, .wall_fast_form').hide(); //Скрываем все поля textarea и открытые формы комментировования
			$('#fast_inpt_' + rid).hide(); //скрываем input слово "Комментировать..."
			$('#fast_textarea_' + rid).show(); //показываем саму форму ответа
			$('#fast_text_' + rid).focus(); //фокусируем на форме ответа
			$('.fast_comm_link').show(); //кнопку комменатировать
		} else {
			$('#fast_textarea_' + rid).show(); //показываем саму форму ответа
			$('#fast_text_' + rid).focus(); //фокусируем на форме ответа
		}
	},
	fast_send: function(rid, for_user_id, type) {
		wall_text = $('#fast_text_' + rid).val();
		if (wall_text != 0) {
			butloading('fast_buts_' + rid, 56, 'disabled');
			$.post('/index.php?go=wall&act=send', {
				wall_text: wall_text,
				for_user_id: for_user_id,
				rid: rid,
				type: type,
				answer_comm_id: $('#answer_comm_id' + rid).val()
			}, function(data) {
				if (data == 'antispam_err') {
					AntiSpam('comm');
					return false;
				}
				if (data == 'err_privacy') {
					addAllErr(lang_pr_no_title);
				} else {
					$('#ava_rec_' + rid).addClass('wall_ava_mini'); //добавляем для авы класс wall_ava_mini
					$('#fast_textarea_' + rid).remove(); //удаляем полей texatra 
					$('#fast_comm_link_' + rid).remove(); //удаляем кнопку комментировать
					$('#wall_fast_block_' + rid).html(data); //выводим сам результат
					$('.wall_fast_text').val(''); //Текстовое значение полей Texatrea делаем 0
					wall.fast_form_close();
				}
				butloading('fast_buts_' + rid, 56, 'enabled', lang_box_send);
			});
		} else {
			$('#fast_text_' + rid).val('');
			$('#fast_text_' + rid).focus();
		}
	},
	all_comments: function(rid, for_user_id, type) {
		textLoad('wall_all_comm_but_' + rid);
		$('#wall_all_but_link_' + rid).attr('onClick', '');
		$.post('/index.php?go=wall&act=all_comm', {
			fast_comm_id: rid,
			for_user_id: for_user_id,
			type: type
		}, function(data) {
			if (data == 'err_privacy') addAllErr(lang_pr_no_title);
			else $('#wall_fast_block_' + rid).html(data); //выводим сам результат
		});
	},
	all_liked_users: function(rid, page_num, liked_num) {
		if (page_num) page = '&page=' + page_num;
		else {
			page = '';
			page_num = 1;
		}
		Box.Page('/index.php?go=wall&act=all_liked_users', 'rid=' + rid + '&liked_num=' + liked_num + page, 'all_liked_users_' + rid + page_num, 525, lang_wall_liked_users, lang_msg_close, 0, 0, 345, 1, 1, 1, 0, 1);
	},
	attach_menu: function(type, id, show_id) {
		if (type == 'open') {
			$('#' + id).addClass('wall_attach_selected');
			$('#' + show_id).show();
		}
		if (type == 'close') {
			$('#' + show_id).hide();
			$('#' + id).removeClass('wall_attach_selected');
		}
	},
	attach_insert: function(type, data, action_url, uid) {
		if (!$('#wall_text').val()) wall.form_open();
		$('#attach_files').show();
		var attach_id = Math.floor(Math.random() * (1000 - 1 + 1)) + 1;
		var for_user_id = location.href.split('/u');
		if (uid) for_user_id[1] = uid;
		//Если вставляем смайлик
		if (type == 'smile') {
			Box.Close('attach_smile', 1);
			smile = data.split('smiles/');
			res_attach_id = 'smile_' + attach_id;
			$('#attach_files').append('<span id="attach_file_' + res_attach_id + '" class="attach_file"><img src="' + data + '" class="wall_attach_smile fl_l" onClick="wall.attach_delete(\'' + res_attach_id + '\', \'smile|' + smile[1] + '||\')" onMouseOver="myhtml.title(\'' + res_attach_id + '\', \'' + lang_wall_no_atttach + '\', \'wall_smile_\')" onMouseOut="myhtml.title_close(\'' + res_attach_id + '\')" id="wall_smile_' + res_attach_id + '" style="margin-top:0px" /></span>');
			$('#vaLattach_files').val($('#vaLattach_files').val() + 'smile|' + smile[1] + '||');
		}
		//Если вставляем фотографию
		if (type == 'photo') {
			Box.Close('all_photos', 1);
			res_attach_id = 'photo_' + attach_id;
			$('#attach_files').append('<span id="attach_file_' + res_attach_id + '" class="attach_file"><div class="wall_attach_photo fl_l"><div class="wall_attach_del" onMouseOver="myhtml.title(\'' + res_attach_id + '\', \'' + lang_wall_no_atttach + '\', \'wall_photo_\')" onMouseOut="myhtml.title_close(\'' + res_attach_id + '\')" onClick="wall.attach_delete(\'' + res_attach_id + '\', \'photo_u|' + action_url + '||\')" id="wall_photo_' + res_attach_id + '"></div><img src="' + data + '" alt="" /></div></span>');
			$('#vaLattach_files').val($('#vaLattach_files').val() + 'photo_u|' + action_url + '||');
		}
		//Если вставляем видео
		if (type == 'video') {
			Box.Close('all_videos', 1);
			res_attach_id = 'video_' + attach_id;
			aPslit = action_url.split('|');
			action_url = action_url.replace('http://' + location.host + '/uploads/videos/' + aPslit[2] + '/', '');
			$('#attach_files').append('<span id="attach_file_' + res_attach_id + '" class="attach_file"><div class="wall_attach_photo fl_l"><div class="wall_attach_del" onMouseOver="myhtml.title(\'' + res_attach_id + '\', \'' + lang_wall_no_atttach + '\', \'wall_video_\')" onMouseOut="myhtml.title_close(\'' + res_attach_id + '\')" onClick="wall.attach_delete(\'' + res_attach_id + '\', \'video|' + action_url + '||\')" id="wall_video_' + res_attach_id + '"></div><img src="' + data + '" alt="" /></div></span>');
			$('#vaLattach_files').val($('#vaLattach_files').val() + 'video|' + action_url + '||');
		}
		//Если вставляем аудио
		if (type == 'audio') {
			var artist = $('#artis' + action_url).text();
			var name = $('#name' + action_url).text();
			Box.Close();
			res_attach_id = 'audio_' + attach_id;
			$('#attach_files').append('<span id="attach_file_' + res_attach_id + '" class="attach_file fl_l" style="display:block;width:100%"><div class="audio_wall_attach"><div class="fl_l"><b>' + artist + '</b> &ndash; ' + name + '</div><img src="/templates/Default/images/close_a.png" onMouseOver="myhtml.title(\'' + res_attach_id + '\', \'' + lang_wall_no_atttach + '\', \'wall_audio_\')" onClick="wall.attach_delete(\'' + res_attach_id + '\', \'audio|' + action_url + '||\')" id="wall_audio_' + res_attach_id + '" class="fl_l cursor_pointer" style="margin-left:5px;margin-top:1px" /></span></div>');
			$('#vaLattach_files').val($('#vaLattach_files').val() + 'audio|' + action_url + '||');
		}
		count = $('.attach_file').length;
		if (count > 9) $('#wall_attach').hide();
	},
	attach_delete: function(id, realId) {
		$('#vaLattach_files').val($('#vaLattach_files').val().replace(realId, ''));
		$('#attach_file_' + id).remove();
		myhtml.title_close(id);
		count = $('.attach_file').length;
		if (!count) $('#attach_files').hide();
		if (count < 10) $('#wall_attach').show();
	},
	attach_addsmile: function() {
		wall.attach_menu('close', 'wall_attach', 'wall_attach_menu');
		Box.Show('attach_smile', 395, lang_wall_atttach_addsmile, lang_wall_attach_smiles, lang_box_canсel, '', '', 0, 1, 1, 1);
	},
	attach_addphoto: function(id, page_num, notes) {
		wall.attach_menu('close', 'wall_attach', 'wall_attach_menu');
		if (page_num) page = '&page=' + page_num;
		else {
			page = '';
			page_num = 1;
		}
		if (notes) notes = '&notes=1';
		else notes = '';
		Box.Page('/index.php?go=albums&act=all_photos_box', page + notes, 'all_photos_' + page_num, 627, lang_wall_attatch_photos, lang_box_canсel, 0, 0, 400, 1, 1, 1, 0, 1);
	},
	attach_addvideo: function(id, page_num, notes) {
		wall.attach_menu('close', 'wall_attach', 'wall_attach_menu');
		if (page_num) page = '&page=' + page_num;
		else {
			page = '';
			page_num = 1;
		}
		if (notes) notes = '&notes=1';
		else notes = '';
		Box.Page('/index.php?go=videos&act=all_videos', page + notes, 'all_videos_' + page_num, 627, lang_wall_attatch_videos, lang_box_canсel, 0, 0, 400, 1, 1, 1, 0, 1);
	},
	attach_addvideo_public: function(id, page_num, pid) {
		wall.attach_menu('close', 'wall_attach', 'wall_attach_menu');
		if (page_num) page = '&page=' + page_num;
		else {
			page = '';
			page_num = 1;
		}
		Box.Page('/index.php?go=videos&act=all_videos_public', 'pid=' + pid + page, 'all_videos_' + page_num, 627, lang_wall_attatch_videos, lang_box_canсel, 0, 0, 400, 1, 1, 1, 0, 1);
	},
	attach_addaudio: function(id, page_num) {
		wall.attach_menu('close', 'wall_attach', 'wall_attach_menu');
		if (page_num) page = 'page=' + page_num;
		else {
			page = '';
			page_num = 1;
		}
		Box.Page('/index.php?go=audio&act=allMyAudiosBox', page, 'all_audios', 627, lang_audio_wall_attatch, lang_box_canсel, 0, 0, 400, 1, 1, 1, 0, 0);
		music.jPlayerInc();
	},
	attach_addDoc: function() {
		Box.Page('/index.php?go=doc', '', 'all_doc', 627, 'Выберите документ', lang_box_canсel, 0, 0, 400, 1, 0, 1, 0, 0);
	},
	tell: function(id) {
		$('#wall_tell_' + id).hide();
		myhtml.title_close(id);
		$('#wall_ok_tell_' + id).fadeIn(150);
		$.post('/index.php?go=wall&act=tell', {
			rid: id
		}, function(data) {
			if (data == 1) addAllErr(lang_wall_tell_tes);
		});
	},
	CheckLinkText: function(val, f) {
		if (!$('#attach_lnk_stared').val()) {
			matches = val.split('http://');
			url = matches[1].split('\r');
			if (!url[1]) url = matches[1].split(' ');
			if (val == 'http://' + url[0] && f) fast_check = 1;
			if (url[1] || fast_check) {
				rUrl = url[0].split(' ');
				$('#attach_block_lnk').show();
				$('#teck_link_attach').val(rUrl[0]);
				txurl = rUrl[0].replace('http://', '');
				spurl = txurl.split('/');
				$('#attatch_link_url').text(spurl[0]).attr('href', '/away.php?url=http://' + rUrl[0]);
				$('#attach_lnk_stared').val('started');
				$.post('/index.php?go=wall&act=parse_link', {
					lnk: rUrl[0]
				}, function(d) {
					$('#loading_att_lnk').hide();
					rndval = new Date().getTime();
					row = d.split('<f>');
					if (d != 1) {
						$('#attatch_link_title').html(row[0]);
						$('#attatch_link_descr').html(row[1]);
					}
					if (row[2] && d != 1) $('#attatch_link_img').attr('src', row[2]).show();
					if (!row[1]) row[1] = '0';
					if (d != 1) {
						$('#vaLattach_files').val($('#vaLattach_files').val() + 'link|http://' + rUrl[0] + '|' + row[0] + '|' + row[1] + '|' + row[2] + '||');
						$('#urlParseImgs').text(row[3]);
					}
				});
			}
		}
	},
	UrlNextImg: function() {
		neUrl = $('#urlParseImgs').text().split('|');
		if (!neUrl[url_next_id]) url_next_id = 0;
		$('#vaLattach_files').val($('#vaLattach_files').val().replace($('#attatch_link_img').attr('src'), neUrl[url_next_id]));
		$('#attatch_link_img').attr('src', neUrl[url_next_id]);
		url_next_id++;
	},
	RemoveAttachLnk: function() {
		delstr = 'link|http://' + $('#teck_link_attach').val() + '|' + $('#attatch_link_title').html() + '|' + $('#attatch_link_descr').html() + '|' + $('#attatch_link_img').attr('src') + '||';
		$('#vaLattach_files').val($('#vaLattach_files').val().replace(delstr, ''));
		$('#attach_lnk_stared').val('');
		$('#attach_block_lnk').hide();
		$('.js_titleRemove').remove();
		$('#attatch_link_title, #attatch_link_descr').html('');
		$('#attatch_link_img').attr('src', '').hide();
		$('#loading_att_lnk').show();
		$('#attatch_link_url').text('').attr('href', '');
		$('#teck_link_attach').val('');
		$('#urlParseImgs').text('');
	},
	FullText: function(rid) {
		$('#hide_wall_rec' + rid).css('max-height', 'none');
		$('#hide_wall_rec_lnk' + rid).hide();
	},
	Answer: function(r, i, n, v) {
		if (!v) vlid = 'fast_text_' + r;
		else vlid = v;
		nm = n.split(' ');
		x = $('#' + vlid).val().length;
		if (x <= 0 || prevAnsweName == $('#' + vlid).val()) {
			if (!v) wall.fast_open_textarea(r, 2);
			$('#' + vlid).val(nm[0] + ', ');
		}
		$('#answer_comm_id' + r).val(i);
		$('#answer_comm_for_' + r).text(n);
		prevAnsweName = nm[0] + ', ';
	}
}
//BBCODES
var bbcodes = {
	tag: function(ibTag, ibClsTag, source) {
		if (!source) source = '';
		bbcodes.insert(ibTag + source, ibClsTag);
	},
	insert: function(ibTag, ibClsTag) {
		var obj_ta = eval('document.entryform.text');
		var ss = obj_ta.selectionStart;
		var st = obj_ta.scrollTop;
		var es = obj_ta.selectionEnd;
		var start = (obj_ta.value).substring(0, ss);
		var middle = (obj_ta.value).substring(ss, es);
		var end = (obj_ta.value).substring(es, obj_ta.textLength);
		middle = ibTag + middle + ibClsTag;
		obj_ta.value = start + middle + end;
		var cpos = ss + (middle.length);
		obj_ta.selectionStart = cpos;
		obj_ta.selectionEnd = cpos;
		obj_ta.focus();
	}
}
var wysiwyg = {
	boxPhoto: function(img, uid, pid) {
		Box.Close('all_photos', 1);
		Box.Close('box_note_add_photo_0');
		lang_notes_sett_box_content = '<div style="padding:15px">' + '<div class="texta" style="width:90px">Ширина:</div><input type="text" id="width_' + pid + '" class="inpst" maxlength="3" size="3" value="140" /> &nbsp;px<div class="mgclr"></div>' + '<div class="texta" style="width:90px">Высота:</div><input type="text" id="height_' + pid + '" class="inpst" maxlength="3" size="3" value="100" /> &nbsp;px<div class="mgclr"></div>' + '<div class="texta" style="width:90px">Выравнивание:</div><div class="padstylej"><select class="inpst" id="pos_' + pid + '"><option value="0">стандартно</option><option value="1">по левому краю</option><option value="2">по правому краю</option><option value="3">по центру</option></select></div><div class="mgclr"></div>' + '<div class="texta" style="width:90px">&nbsp;</div><div class="html_checkbox" id="img_link_' + pid + '" onClick="myhtml.checkbox(this.id)">Добавить ссылку</div><div class="mgclr"></div>' + '<div class="texta" style="width:90px">&nbsp;</div><div class="html_checkbox" id="img_blank_' + pid + '" onClick="myhtml.checkbox(this.id)" style="margin-top:5px">Открывать в новом окне</div><div class="mgclr"></div>' + '<div class="texta" style="width:90px">&nbsp;</div><div class="html_checkbox" id="img_border_' + pid + '" onClick="myhtml.checkbox(this.id)" style="margin-top:5px">Показывать рамку</div><div class="mgclr"></div>' + '</div>';
		Box.Show('note_add_photo_' + pid, 300, lang_notes_setting_addphoto, lang_notes_sett_box_content, lang_box_canсel, lang_box_save, 'wysiwyg.inPhoto(\'' + img + '\', ' + uid + ', ' + pid + ')', 0, 0, 0, 0, 1);
		myhtml.checked(['img_link_' + pid, '0']);
	},
	inPhoto: function(img, uid, pid) {
		Box.Close('note_add_photo_' + pid, 1);
		width = $('#width_' + pid).val();
		height = $('#height_' + pid).val();
		pos = $('#pos_' + pid).val();
		img_link = $('#img_link_' + pid).val();
		img_blank = $('#img_blank_' + pid).val();
		img_border = $('#img_border_' + pid).val();
		if (pos == 3) {
			spos = '[center]';
			epos = '[/center]';
		} else {
			spos = '';
			epos = '';
		}
		bbcodes.tag(spos + '[photo]', '[/photo]' + epos, uid + '|' + pid + '|' + img + '|' + width + '|' + height + '|' + img_border + '|' + img_blank + '|' + pos + '|' + img_link);
	},
	boxVideo: function(img, uid, vid) {
		Box.Close('all_videos', 1);
		lang_notes_sett_box_content = '<div style="padding:15px">' + '<div class="texta" style="width:90px">Ширина:</div><input type="text" id="v_width_' + vid + '" class="inpst" maxlength="3" size="3" value="175" /> &nbsp;px<div class="mgclr"></div>' + '<div class="texta" style="width:90px">Высота:</div><input type="text" id="v_height_' + vid + '" class="inpst" maxlength="3" size="3" value="131" /> &nbsp;px<div class="mgclr"></div>' + '<div class="texta" style="width:90px">Выравнивание:</div><div class="padstylej"><select class="inpst" id="v_pos_' + vid + '"><option value="0">стандартно</option><option value="1">по левому краю</option><option value="2">по правому краю</option><option value="3">по центру</option></select></div><div class="mgclr"></div>' + '<div class="texta" style="width:90px">&nbsp;</div><div class="html_checkbox" id="v_img_blank_' + vid + '" onClick="myhtml.checkbox(this.id)" style="margin-top:5px">Открывать в новом окне</div><div class="mgclr"></div>' + '<div class="texta" style="width:90px">&nbsp;</div><div class="html_checkbox" id="v_img_border_' + vid + '" onClick="myhtml.checkbox(this.id)" style="margin-top:5px">Показывать рамку</div><div class="mgclr"></div>' + '</div>';
		Box.Show('note_add_video_' + vid, 300, lang_notes_setting_addvdeio, lang_notes_sett_box_content, lang_box_canсel, lang_box_save, 'wysiwyg.inVideo(\'' + img + '\', ' + uid + ', ' + vid + ')', 0, 0, 0, 0, 1);
	},
	inVideo: function(img, uid, vid) {
		Box.Close('note_add_video_' + vid, 1);
		width = $('#v_width_' + vid).val();
		height = $('#v_height_' + vid).val();
		pos = $('#v_pos_' + vid).val();
		img_blank = $('#v_img_blank_' + vid).val();
		img_border = $('#v_img_border_' + vid).val();
		if (pos == 3) {
			spos = '[center]';
			epos = '[/center]';
		} else {
			spos = '';
			epos = '';
		}
		bbcodes.tag(spos + '[video]', '[/video]' + epos, uid + '|' + vid + '|' + img + '|' + width + '|' + height + '|' + img_border + '|' + img_blank + '|' + pos);
	},
	linkBox: function() {
		lang_wysiwyg_box_content = '<div style="padding:15px">' + '<div class="texta" style="width:90px">Адрес ссылки:</div><input type="text" id="l_http" class="inpst" style="width:300px" /><div class="mgclr"></div>' + '<div class="texta" style="width:90px">Текст ссылки:</div><input type="text" id="l_text" class="inpst" style="width:300px" /><div class="mgclr"></div>' + '</div>';
		Box.Show('w_link', 450, lang_wysiwyg_title, lang_wysiwyg_box_content, lang_box_canсel, lang_box_save, 'wysiwyg.insertLink()');
		$('#l_http').focus();
	},
	insertLink: function() {
		link = $('#l_http').val();
		link_text = '|' + $('#l_text').val();
		if (!$('#l_text').val()) link_text = '';
		if (link != 0) bbcodes.tag('[link]', '[/link]', link + link_text);
		Box.Close('w_link');
	}
}
//STATUS
var gStatus = {
	open: function() {
		$('#set_status_bg').fadeIn(100);
		$('#status_text').focus();
		$('#status_text').select();
		if ($('#status_text').val()) {
			$('.yes_status_text').show();
			$('.no_status_text').hide();
		} else {
			$('.yes_status_text').hide();
			$('.no_status_text').show();
		}
		$('.status_tell_friends').hide();
	},
	close: function() {
		$('#set_status_bg').hide();
		$('#status_text').val($('#status_text').val());
	},
	set: function(clear, a) {
		text = $('#status_text').val();
		if (clear) {
			text = '';
			$('#status_text').val('');
		}
		if (text != $('#new_status').text()) {
			butloading('status_but', 55, 'disabled');
			if (a) {
				act = '&act=public';
				public_id = $('#public_id').val();
			} else {
				act = '';
				public_id = '';
			}
			$.post('/index.php?go=status' + act, {
				text: text,
				public_id: public_id
			}, function(data) {
				if (data) {
					$('#status_link').hide();
					gStatus.tell();
					$('#new_status').attr('onMouseOver', 'gStatus.tell()');
				} else $('#status_link').show();
				$('#new_status').html(data);
				gStatus.close();
				butloading('status_but', 55, 'enabled', lang_box_save);
			});
		} else gStatus.close();
	},
	tell: function() {
		$('.status_tell_friends').hide();
		pos = $('#tellBlockPos').position().top;
		$('.status_tell_friends').fadeIn('fast');
		setTimeout(function() {
			$('.status_tell_friends').fadeOut('fast');
		}, 2500);
	},
	startTell: function() {
		for_user_id = location.href.split('http://' + location.host + '/u');
		text = $('#status_text').val();
		tell_friends = $('#tell_friends').val();
		if (tell_friends) {
			if (text != 0) {
				$.post('/index.php?go=wall&act=send', {
					wall_text: text,
					for_user_id: for_user_id[1]
				}, function(data) {
					$('#wall_records').html(data);
					$('#wall_all_record').html('');
					updateNum('#wall_rec_num', 1);
				});
			}
		} else {
			insert_id = $('.wallrecord:first').attr('id').replace('wall_record_', '');
			wall.delet(insert_id);
		}
	},
	startTellPublic: function(i) {
		tell_friends = $('#tell_friends').val();
		if (tell_friends) {
			if ($('#status_text').val() != 0) {
				$.post('/index.php?go=groups&act=wall_send', {
					id: i,
					wall_text: $('#status_text').val()
				}, function(data) {
					if ($('#rec_num').text() == 'Нет записей') $('.albtitle').html('<b id="rec_num">1</b> запись');
					else updateNum('#rec_num', 1);
					$('#public_wall_records').html(data);
					if ($('#rec_num').text() > 10) {
						$('#page_cnt').val('1');
						$('#wall_all_records').show();
						$('#load_wall_all_records').html('к предыдущим записям');
					}
				});
			}
		} else {
			insert_id = $('.public_wall:first').attr('id').replace('wall_record_', '');
			groups.wall_delet(insert_id);
		}
	}
}
//NEWS
var news = {
	page: function() {
		var type = $('#type').val();
		$('#wall_l_href_news').attr('onClick', '');
		if ($('#loading_news').text() == 'Показать предыдущие новости') {
			textLoad('loading_news');
			$.post('/index.php?go=news&type=' + type, {
				page: 1,
				page_cnt: page_cnt
			}, function(d) {
				if (d != 'no_news') {
					$('#news').append(d);
					$('#wall_l_href_news').attr('onClick', 'news.page(\'' + type + '\')');
					$('#loading_news').html('Показать предыдущие новости');
					page_cnt++;
				} else $('#wall_l_href_news').hide();
			});
		}
	},
	showWallText: function(id) {
		var wh2 = $('#2href_text_' + id).width();
		var wh = $('#href_text_' + id).width() - wh2 - 40;
		$('.news_wall_msg_bg').hide();
		$('#wall_text_' + id).fadeIn('fast').css('margin-left', wh);
		$('#wall_text_' + id).mouseover(function() {
			$('#wall_text_' + id).fadeOut('fast');
		});
	},
	hideWallText: function(id) {
		$('#wall_text_' + id).fadeOut('fast');
	}
}
//SETTINGS
var settings = {
	savenewmail: function() {
		var email = $('#email').val();
		if (settings.isValidEmailAddress(email)) {
			butloading('saveNewEmail', '88', 'disabled', '');
			$.post('/index.php?go=settings&act=change_mail', {
				email: email
			}, function(d) {
				if (d == 1) {
					$('#err_email').html('Этот E-Mail адрес уже занят.').show();
				} else {
					$('#err_email').hide();
					$('#ok_email').show();
				}
				butloading('saveNewEmail', '88', 'enabled', 'Сохранить адрес');
			});
		} else {
			$('#err_email').show();
			setErrorInputMsg('email');
		}
	},
	isValidEmailAddress: function(emailAddress) {
		var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
		return pattern.test(emailAddress);
	},
	saveNewPwd: function() {
		var old_pass = $('#old_pass').val();
		var new_pass = $('#new_pass').val();
		var new_pass2 = $('#new_pass2').val();
		if (old_pass != 0) {
			if (new_pass != 0) {
				if (new_pass2 != 0) {
					butloading('saveNewPwd', 87, 'disabled');
					$.post('/index.php?go=settings&act=newpass', {
						old_pass: old_pass,
						new_pass: new_pass,
						new_pass2: new_pass2
					}, function(data) {
						$('.pass_errors').hide();
						if (data == 1) $('#err_pass_1').show();
						else if (data == 2) $('#err_pass_2').show();
						else $('#ok_pass').show();
						butloading('saveNewPwd', 87, 'enabled', 'Изменить пароль');
					});
				} else setErrorInputMsg('new_pass2');
			} else setErrorInputMsg('new_pass');
		} else setErrorInputMsg('old_pass');
	},
	saveNewName: function() {
		var name = $('#name').val();
		var lastname = $('#lastname').val();
		if (name.length >= 2 && name != 0 && settings.isValidName(name)) {
			if (lastname.length >= 2 && lastname != 0 && settings.isValidName(lastname)) {
				butloading('saveNewName', 69, 'disabled');
				$.post('/index.php?go=settings&act=newname', {
					name: name,
					lastname: lastname
				}, function(data) {
					$('.name_errors').hide();
					$('#ok_name').show();
					butloading('saveNewName', 69, 'enabled', 'Изменить имя');
				});
			} else {
				$('.name_errors').hide();
				$('#err_name_1').show();
				setErrorInputMsg('lastname');
			}
		} else {
			$('.name_errors').hide();
			$('#err_name_1').show();
			setErrorInputMsg('name');
		}
	},
	isValidName: function(xname) {
		var pattern = new RegExp(/^[a-zA-Zа-яА-Я]+$/);
		return pattern.test(xname);
	},
	privacyOpen: function(id) {
		$('.sett_openmenu').hide();
		$('#privacyMenu_' + id).show();
	},
	privacyClose: function(id) {
		$('#privacyMenu_' + id).fadeOut(120);
	},
	setPrivacy: function(val_id, mtext, opt, text_id) {
		$('#' + val_id).val(opt);
		$('#' + text_id).text(mtext);
		$('#selected_p_' + text_id).text(mtext);
		settings.privacyClose(val_id);
	},
	event: function(event) {
		var oi = (event.target) ? event.target.id : ((event.srcElement) ? event.srcElement.id : null);
		var fast_oi = oi.substring(0, 9);
		if (oi != 'privacyMenu_msg' && oi != 'privacy_lnk_msg' && oi != 'privacyMenu_wall1' && oi != 'privacy_lnk_wall1' && oi != 'privacyMenu_wall2' && oi != 'privacy_lnk_wall2' && oi != 'privacyMenu_wall3' && oi != 'privacy_lnk_wall3' && oi != 'privacyMenu_info' && oi != 'privacy_lnk_info') $('#privacyMenu_msg, #privacyMenu_wall1, #privacyMenu_wall2, #privacyMenu_wall3, #privacyMenu_info').fadeOut(120);
	},
	savePrivacy: function() {
		var val_msg = $('#val_msg').val();
		var val_wall1 = $('#val_wall1').val();
		var val_wall2 = $('#val_wall2').val();
		var val_wall3 = $('#val_wall3').val();
		var val_info = $('#val_info').val();
		butloading('savePrivacy', 55, 'disabled');
		$.post('/index.php?go=settings&act=saveprivacy', {
			val_msg: val_msg,
			val_wall1: val_wall1,
			val_wall2: val_wall2,
			val_wall3: val_wall3,
			val_info: val_info
		}, function() {
			$('#ok_update').show();
			butloading('savePrivacy', 55, 'enabled', 'Сохранить');
		});
	},
	addblacklist: function(bad_user_id) {
		$('#addblacklist_load').show();
		$.post('/index.php?go=settings&act=addblacklist', {
			bad_user_id: bad_user_id
		}, function() {
			$('#addblacklist_but').attr('onClick', 'settings.delblacklist(' + bad_user_id + ', 1); return false');
			$('#text_add_blacklist').text('Разблокировать');
			$('#addblacklist_load').hide();
		});
	},
	delblacklist: function(bad_user_id, type) {
		$('#addblacklist_load').show();
		textLoad('del_' + bad_user_id);
		$.post('/index.php?go=settings&act=delblacklist', {
			bad_user_id: bad_user_id
		}, function() {
			if (type) {
				$('#addblacklist_but').attr('onClick', 'settings.addblacklist(' + bad_user_id + '); return false');
				$('#text_add_blacklist').text('Заблокировать');
				$('#addblacklist_load').hide();
			} else {
				$('#u' + bad_user_id).remove();
				updateNum('#badlistnum');
			}
		});
	}
}
//CROP
var crop = {
	start: function(id) {
		$('.pinfo, .photo_prev_but, .photo_next_but').hide();
		$('#save_crop_text' + id).show();
		var x1w = $('#ladybug_ant' + id).width() - 50;
		var y1h = $('#ladybug_ant' + id).height() - 50;
		$('#i_left' + id).val('50');
		$('#i_top' + id).val('50');
		$('#i_width' + id).val(x1w);
		$('#i_height' + id).val(y1h);
		$('#ladybug_ant' + id).imgAreaSelect({
			minWidth: 100,
			minHeight: 100,
			handles: true,
			x1: 50,
			y1: 50,
			x2: x1w,
			y2: y1h,
			onSelectEnd: function(img, selection) {
				$('#i_left' + id).val(selection.x1);
				$('#i_top' + id).val(selection.y1);
				$('#i_width' + id).val(selection.width);
				$('#i_height' + id).val(selection.height);
			}
		});
	},
	close: function(id) {
		$('.pinfo, .photo_prev_but, .photo_next_but').show();
		$('#save_crop_text' + id).hide();
		$('#ladybug_ant' + id).imgAreaSelect({
			remove: true
		});
	},
	save: function(pid, uid) {
		var i_left = $('#i_left' + pid).val();
		var i_top = $('#i_top' + pid).val();
		var i_width = $('#i_width' + pid).val();
		var i_height = $('#i_height' + pid).val();
		Page.Loading('start');
		$.post('/index.php?go=photo&act=crop', {
			i_left: i_left,
			i_top: i_top,
			i_width: i_width,
			i_height: i_height,
			pid: pid
		}, function(data) {
			Page.Go('/u' + uid);
		});
	}
}
//SUPPORT
var support = {
	send: function() {
		var title = $('#title').val();
		var question = $('#question').val();
		if (title != 0 && title != 'Пожалуйста, добавьте заголовок к Вашему вопросу..') {
			if (question != 0 && question != 'Пожалуйста, расскажите о Вашей проблеме чуть подробнее..') {
				$('#cancel').hide();
				butloading('send', '56', 'disabled', '');
				$.post('/index.php?go=support&act=send', {
					title: title,
					question: question
				}, function(data) {
					if (data == 'limit') {
						Box.Info('err', lang_support_ltitle, lang_support_ltext, 280, 2000);
					} else {
						var qid = data.split('r|x');
						$('#data').html(qid[0]);
						history.pushState({
							link: '/support?act=show&qid=' + qid[1]
						}, null, '/support?act=show&qid=' + qid[1]);
					}
					butloading('send', '56', 'enabled', 'Отправить');
				});
			} else setErrorInputMsg('question');
		} else setErrorInputMsg('title');
	},
	delquest: function(qid) {
		Box.Show('del_quest', 400, lang_title_del_photo, '<div style="padding:15px;" id="text_del_quest">' + lang_support_text + '</div>', lang_box_canсel, lang_box_yes, 'support.startDel(' + qid + '); return false');
	},
	startDel: function(qid) {
		$('#box_loading').show();
		$.post('/index.php?go=support&act=delet', {
			qid: qid
		}, function() {
			Page.Go('/support');
		});
	},
	answer: function(qid, uid) {
		var answer = $('#answer').val();
		if (answer != 0 && answer != 'Комментировать..') {
			butloading('send', '56', 'disabled', '');
			$.post('/index.php?go=support&act=answer', {
				answer: answer,
				qid: qid
			}, function(data) {
				if (uid == 0) $('#status').text('Есть ответ.');
				else $('#status').text('Вопрос ожидает обработки.');
				$('#answers').append(data);
				$('#answer').val('');
				butloading('send', '56', 'enabled', lang_box_send);
			});
		} else setErrorInputMsg('answer');
	},
	delanswe: function(id) {
		$('#asnwe_' + id).html(lang_del_comm);
		$.post('/index.php?go=support&act=delet_answer', {
			id: id
		});
	},
	close: function(qid) {
		butloading('close', '30', 'disabled', '');
		$.post('/index.php?go=support&act=close', {
			qid: qid
		}, function() {
			$('#status').text('Есть ответ.');
			$('#close_but').hide();
		});
	}
}
//BLOG
var blog = {
	add: function() {
		var title = $('#title').val();
		var text = $('#text').val();
		if (title != 0) {
			if (text != 0) {
				butloading('notes_sending', 74, 'disabled');
				$.post('/index.php?go=blog&act=send', {
					title: title,
					text: text
				}, function() {
					Page.Go('/blog');
				});
			} else setErrorInputMsg('text');
		} else setErrorInputMsg('title');
	},
	del: function(id) {
		Box.Show('del_quest', 400, lang_title_del_photo, '<div style="padding:15px;" id="text_del_quest">' + lang_news_text + '</div>', lang_box_canсel, lang_box_yes, 'blog.startDel(' + id + '); return false');
	},
	startDel: function(id) {
		$('#box_loading').show();
		$.post('/index.php?go=blog&act=del', {
			id: id
		}, function() {
			Page.Go('/blog');
		});
	},
	save: function(id) {
		var title = $('#title').val();
		var text = $('#text').val();
		if (title != 0) {
			if (text != 0) {
				butloading('notes_sending', 55, 'disabled');
				$.post('/index.php?go=blog&act=save', {
					id: id,
					title: title,
					text: text
				}, function() {
					Page.Go('/blog?id=' + id);
				});
			} else setErrorInputMsg('text');
		} else setErrorInputMsg('title');
	}
}
//GIFTS
var gifts = {
	box: function(user_id, c) {
		if (c) var cache = 0;
		else var cache = 1;
		Box.Page('/index.php?go=gifts&act=view', 'user_id=' + user_id, 'gifts', 679, lang_gifts_title, lang_box_canсel, 0, 0, 450, 1, 1, 1, 0, cache);
	},
	showgift: function(id) {
		$('#g' + id).show();
	},
	showhide: function(id) {
		$('#g' + id).hide();
	},
	select: function(gid, fid) {
		Box.Close(0, 1);
		Box.Show('send_gift' + gid, 460, lang_gifts_title, '<center><img src="/uploads/gifts/' + gid + '.jpg" style="margin-top:30px" /></center><div class="fl_l color777" style="padding:3px;margin-left:100px;margin-right:5px">Тип подарка:</div><div class="sett_privacy" onClick="settings.privacyOpen(\'privacy_comment' + gid + '\')" id="privacy_lnk_privacy_comment' + gid + '">Виден всем</div><div class="sett_openmenu no_display" id="privacyMenu_privacy_comment' + gid + '" style="margin-top:-1px;margin-left:176px;width:100px"><div id="selected_p_privacy_lnk_privacy_comment' + gid + '" class="sett_selected" onClick="settings.privacyClose(\'privacy_comment' + gid + '\')">Виден всем</div><div class="sett_hover" onClick="settings.setPrivacy(\'privacy_comment' + gid + '\', \'Виден всем\', \'1\', \'privacy_lnk_privacy_comment' + gid + '\')">Виден всем</div><div class="sett_hover" onClick="settings.setPrivacy(\'privacy_comment' + gid + '\', \'Личный\', \'2\', \'privacy_lnk_privacy_comment' + gid + '\')">Личный</div><div class="sett_hover" onClick="settings.setPrivacy(\'privacy_comment' + gid + '\', \'Анонимный\', \'3\', \'privacy_lnk_privacy_comment' + gid + '\')">Анонимный</div></div><input type="hidden" id="privacy_comment' + gid + '" value="1" /><div class="clear"></div><div class="fl_l color777" style="margin-left:182px;margin-right:5px" id="addmsgtext' + gid + '"><a href="" onClick="gifts.addmssbox(' + gid + '); return false">Добавить сообщение</a></div>', lang_box_canсel, lang_box_send, 'gifts.send(' + gid + ', ' + fid + ')', 340, 0, 0, 0, 0);
	},
	send: function(gfid, fid) {
		var privacy = $('#privacy_comment' + gfid).val();
		var msgfgift = $('#msgfgift' + gfid).val();
		$('#box_loading').show().css('margin-top', '-5px');
		$.post('/index.php?go=gifts&act=send', {
			for_user_id: fid,
			gift: gfid,
			privacy: privacy,
			msg: msgfgift
		}, function(d) {
			if (d == 1) {
				addAllErr(lang_gifts_tnoubm, 3000);
				Box.Close();
			} else {
				Box.Close();
				Box.Info('giftok', lang_gifts_oktitle, lang_gifts_oktext, 250, 2000);
			}
		});
	},
	addmssbox: function(gid) {
		$('.box_conetnt').css('height', '375px');
		$('#addmsgtext' + gid).html('<textarea id="msgfgift' + gid + '" class="inpst" style="width:200px;height:40px"></textarea>');
		$('#msgfgift' + gid).focus();
	},
	delet: function(gid) {
		$('#gift_' + gid).html('<div class="color777" style="margin-bottom:5px">Подарок удалён.</div>');
		updateNum('#num');
		$.post('/index.php?go=gifts&act=del', {
			gid: gid
		});
	}
}
//GROUPS
var groups = {
	createbox: function() {
		Box.Show('create', 490, lang_groups_new, '<div style="padding:20px"><div class="videos_text">Название</div><input type="text" class="videos_input" id="title" maxlength="65" /></div>', lang_box_canсel, lang_groups_cretate, 'groups.creat()', 100, 0, 0, 0, 0, 'title');
		$('#title').focus();
	},
	creat: function() {
		var title = $('#title').val();
		if (title != 0) {
			$('#box_loading').show();
			ge('box_butt_create').disabled = true;
			$.post('/index.php?go=groups&act=send', {
				title: title
			}, function(id) {
				if (id == 'antispam_err') AntiSpam('groups');
				else Page.Go('/public' + id);
				Box.Close();
			});
		} else setErrorInputMsg('title');
	},
	exit: function(id) {
		$('#exitlink' + id).html('<div class="color777" style="margin-top:6px;margin-right:7px">Вы вышли из сообщества.</div>');
		$.post('/index.php?go=groups&act=exit', {
			id: id
		});
	},
	exit2: function(id, user_id) {
		$('#no').hide();
		$('#yes').fadeIn('fast');
		updateNum('#traf');
		updateNum('#traf2');
		if ($('#traf').text() == 0) {
			$('#users_block').hide();
			$('#num2').html('<span class="color777">Вы будете первым.</span>');
		}
		$('#subUser' + user_id).remove();
		$.post('/index.php?go=groups&act=exit', {
			id: id
		});
	},
	login: function(id) {
		$('#yes').hide();
		$('#no').fadeIn('fast');
		if ($('#traf').text() == 0) $('#users_block').show();
		updateNum('#traf', 1);
		updateNum('#traf2', 1);
		$.post('/index.php?go=groups&act=login', {
			id: id
		});
	},
	loadphoto: function(id) {
		Box.Page('/index.php?go=groups&act=loadphoto_page', 'id=' + id, 'loadphoto', 400, lang_title_load_photo, lang_box_canсel, 0, 0, 0, 0, 0, 0, 0, 1);
	},
	delphoto: function(id) {
		Box.Show('del_photo', 400, lang_title_del_photo, '<div style="padding:15px;">' + lang_del_photo + '</div>', lang_box_canсel, lang_box_yes, 'groups.startdelete(' + id + ')');
	},
	startdelete: function(id) {
		$('#box_loading').show();
		ge('box_butt_create').disabled = true;
		$.post('/index.php?go=groups&act=delphoto', {
			id: id
		}, function() {
			$('#ava').attr('src', template_dir + '/images/no_ava.gif');
			$('#del_pho_but').hide();
			Box.Close();
		});
	},
	addcontact: function(id) {
		Box.Page('/index.php?go=groups&act=addfeedback_pg', 'id=' + id, 'addfeedback', 400, 'Добавление контактного лица', lang_box_canсel, 'Сохранить', 'groups.savefeedback(' + id + ')', 0, 0, 0, 0, 'upage', 0);
	},
	savefeedback: function(id) {
		var upage = $('#upage').val();
		var office = $('#office').val();
		var phone = $('#phone').val();
		var email = $('#email').val();
		if ($('#feedimg').attr('src') != template_dir + '/images/contact_info.png') {
			$('#box_loading').show();
			ge('box_butt_create').disabled = true;
			$.post('/index.php?go=groups&act=addfeedback_db', {
				id: id,
				upage: upage,
				office: office,
				phone: phone,
				email: email
			}, function(d) {
				if (d == 1) {
					Box.Info('err', 'Информация', 'Этот пользователь уже есть в списке контактов.', 300, 2000);
					ge('box_butt_create').disabled = false;
					$('#box_loading').hide();
				} else {
					Box.Close();
					Page.Go('/public' + id);
				}
			});
		} else setErrorInputMsg('upage');
	},
	allfeedbacklist: function(id) {
		Box.Page('/index.php?go=groups&act=allfeedbacklist', 'id=' + id, 'allfeedbacklist', 450, 'Контакты', 'Закрыть', 0, 0, 300, 1, 1, 1, 0, 0);
	},
	delfeedback: function(id, uid) {
		$('#f' + uid + ', #fb' + uid).remove();
		var si = $('.public_obefeed').length;
		updateNum('#fnumu');
		if (si <= 0) {
			$('#feddbackusers').html('<div class="line_height color777" align="center">Страницы представителей, номера телефонов, e-mail<br /><a href="/public' + id + '" onClick="groups.addcontact(' + id + '); return false">Добавить контакты</a></div>');
			$('.box_conetnt').html('<div align="center" style="padding-top:10px;color:#777;font-size:13px;">Список контактов пуст.</div><style>#box_bottom_left_text{padding-top:6px}</style>');
		}
		$.post('/index.php?go=groups&act=delfeedback', {
			id: id,
			uid: uid
		});
	},
	editfeedback: function(uid) {
		$('#close_editf' + uid).hide();
		$('#editf' + uid).show();
		$('#email' + uid).val($('#email' + uid).val().replace(', ', ''));
	},
	editfeeddave: function(id, uid) {
		var office = $('#office' + uid).val();
		var phone = $('#phone' + uid).val();
		var email = $('#email' + uid).val();
		$('#close_editf' + uid).show();
		$('#editf' + uid).hide();
		$('#okoffice' + uid).text(office);
		$('#okphone' + uid).text(phone);
		if (phone != 0 && email != 0) $('#okemail' + uid).text(', ' + email);
		else $('#okemail' + uid).text(email);
		$.post('/index.php?go=groups&act=editfeeddave', {
			id: id,
			uid: uid,
			office: office,
			phone: phone,
			email: email
		});
	},
	checkFeedUser: function() {
		var upage = $('#upage').val();
		var pattern = new RegExp(/^[0-9]+$/);
		if (pattern.test(upage)) {
			$.post('/index.php?go=groups&act=checkFeedUser', {
				id: upage
			}, function(d) {
				d = d.split('|');
				if (d[0]) {
					if (d[1]) $('#feedimg').attr('src', '/uploads/users/' + upage + '/100_' + d[1]);
					else $('#feedimg').attr('src', template_dir + '/images/100_no_ava.png');
					$('#office').focus();
				} else {
					setErrorInputMsg('upage');
					$('#feedimg').attr('src', template_dir + '/images/contact_info.png');
				}
			});
		} else $('#feedimg').attr('src', template_dir + '/images/contact_info.png');
	},
	saveinfo: function(id) {
		var title = $('#title').val();
		var descr = $('#descr').val();
		var adres_page = $('#adres_page').val();
		var comments = $('#comments').val();
		$('#e_public_title').text(title);
		if (descr != 0) {
			$('#descr_display').show();
			$('#e_descr').html(descr);
		}
		if (!adres_page) var adres_page = 'public' + id;
		var pattern = new RegExp(/^[a-zA-Z0-9_-]+$/);
		if (pattern.test(adres_page)) {
			butloading('pubInfoSave', 55, 'disabled');
			$.post('/index.php?go=groups&act=saveinfo', {
				id: id,
				title: title,
				descr: descr,
				comments: comments,
				adres_page: adres_page,
				discussion: $('#discussion').val(),
				web: $('#web').val()
			}, function(d) {
				if (d == 'err_adres') Box.Info('err', 'Ошибка', 'Такой адрес уже занят', 130, 1500);
				else
				if (adres_page != 'public' + id) Page.Go('/public' + id);
				else Page.Go('/' + adres_page);
				butloading('pubInfoSave', 55, 'enabled', 'Сохранить');
			});
		} else {
			setErrorInputMsg('adres_page');
			Box.Info('err', 'Ошибка', 'Вы можете изменить короткий адрес Вашей страницы на более удобный и запоминающийся. Для этого введите имя страницы, состоящее из латинских букв, цифр или знаков «_» .', 300, 5500);
		}
	},
	editform: function() {
		$('#edittab1').slideDown('fast');
		$('#public_editbg_container').animate({
			scrollLeft: "+560"
		});
	},
	editformClose: function() {
		$('#public_editbg_container').animate({
			scrollLeft: "-560"
		}, 1000);
		setTimeout("$('#edittab1').slideUp('fast')", 200);
		$('#edittab2').hide();
	},
	edittab_admin: function(id) {
		$('#edittab2').show();
		$('#public_editbg_container').animate({
			scrollLeft: "+1120"
		});
	},
	addadmin: function(id) {
		var new_admin_id = $('#new_admin_id').val().replace('http://udinbala.com/u', '');
		var check_adm = $('#admin' + new_admin_id).text();
		if (new_admin_id && !check_adm) {
			Box.Page('/index.php?go=groups&act=new_admin', 'new_admin_id=' + new_admin_id, 'new_admin_id', 400, 'Назначение руководителя', 'Закрыть', 'Назначить руководителем', 'groups.send_new_admin(' + id + ', ' + new_admin_id + ')', 130, 0, 0, 0, 0, 0);
		} else addAllErr('Этот пользователь уже есть в списке руководителей.');
	},
	send_new_admin: function(id, new_admin_id) {
		var ava = $('#adm_ava').attr('src');
		var adm_name = $('#adm_name').text();
		var data = '<div class="public_oneadmin" id="admin' + new_admin_id + '"><a href="/u' + new_admin_id + '" onClick="Page.Go(this.href); return false"><img src="' + ava + '" align="left" width="32" /></a><a href="/u' + new_admin_id + '" onClick="Page.Go(this.href); return false">' + adm_name + '</a><br /><a href="/" onClick="groups.deladmin(\'' + id + '\', \'' + new_admin_id + '\'); return false"><small>Удалить</small></a></div>';
		$('#admins_tab').append(data);
		Box.Close();
		$('#new_admin_id').val('');
		$.post('/index.php?go=groups&act=send_new_admin', {
			id: id,
			new_admin_id: new_admin_id
		});
	},
	deladmin: function(id, uid) {
		$('#admin' + uid).remove();
		$.post('/index.php?go=groups&act=deladmin', {
			id: id,
			uid: uid
		});
	},
	wall_send: function(id) {
		var wall_text = $('#wall_text').val();
		var attach_files = $('#vaLattach_files').val();
		if (wall_text != 0 || attach_files != 0) {
			butloading('wall_send', 56, 'disabled');
			$.post('/index.php?go=groups&act=wall_send', {
				id: id,
				wall_text: wall_text,
				attach_files: attach_files,
				vote_title: $('#vote_title').val(),
				vote_answer_1: $('#vote_answer_1').val(),
				vote_answer_2: $('#vote_answer_2').val(),
				vote_answer_3: $('#vote_answer_3').val(),
				vote_answer_4: $('#vote_answer_4').val(),
				vote_answer_5: $('#vote_answer_5').val(),
				vote_answer_6: $('#vote_answer_6').val(),
				vote_answer_7: $('#vote_answer_7').val(),
				vote_answer_8: $('#vote_answer_8').val(),
				vote_answer_9: $('#vote_answer_9').val(),
				vote_answer_10: $('#vote_answer_10').val()
			}, function(data) {
				if ($('#rec_num').text() == 'Нет записей') $('.albtitle').html('<b id="rec_num">1</b> запись');
				else updateNum('#rec_num', 1);
				$('#wall_text').val('');
				$('#attach_files').hide();
				$('#attach_files').html('');
				$('#vaLattach_files').val('');
				wall.form_close();
				wall.RemoveAttachLnk();
				butloading('wall_send', 56, 'enabled', lang_box_send);
				$('#public_wall_records').html(data);
				if ($('#rec_num').text() > 10) {
					$('#page_cnt').val('1');
					$('#wall_all_records').show();
					$('#load_wall_all_records').html('к предыдущим записям');
				}
			});
		} else setErrorInputMsg('wall_text');
	},
	wall_send_comm: function(rec_id, public_id) {
		var wall_text = $('#fast_text_' + rec_id).val();
		if (wall_text != 0) {
			butloading('fast_buts_' + rec_id, 56, 'disabled');
			$.post('/index.php?go=groups&act=wall_send_comm', {
				rec_id: rec_id,
				wall_text: wall_text,
				public_id: public_id,
				answer_comm_id: $('#answer_comm_id' + rec_id).val()
			}, function(data) {
				$('#fast_form_' + rec_id + ', #fast_comm_link_' + rec_id).remove();
				$('#wall_fast_block_' + rec_id).html(data);
				var pattern = new RegExp(/news/i);
				if (pattern.test(location.href)) $('#fast_text_' + rec_id + ', #fast_inpt_' + rec_id).css('width', '688px');
			});
		} else setErrorInputMsg('fast_text_' + rec_id);
	},
	wall_delet: function(rec_id) {
		$('#wall_record_' + rec_id).html('<span class="color777">Запись удалена.</span>');
		$('#wall_fast_block_' + rec_id + ', .wall_fast_opened_form').remove();
		$('#wall_record_' + rec_id).css('padding-bottom', '5px');
		myhtml.title_close(rec_id);
		updateNum('#rec_num');
		$.post('/index.php?go=groups&act=wall_del', {
			rec_id: rec_id
		});
	},
	comm_wall_delet: function(rec_id, public_id) {
		$('#wall_fast_comment_' + rec_id).html('<div class="color777" style="margin-bottom:7px">Комментарий удалён.</div>');
		$.post('/index.php?go=groups&act=wall_del', {
			rec_id: rec_id,
			public_id: public_id
		});
	},
	wall_all_comments: function(rec_id, public_id) {
		textLoad('wall_all_comm_but_' + rec_id);
		$('#wall_all_but_link_' + rec_id).attr('onClick', '');
		$.post('/index.php?go=groups&act=all_comm', {
			rec_id: rec_id,
			public_id: public_id
		}, function(data) {
			$('#wall_fast_block_' + rec_id).html(data); //выводим сам результат
			var pattern = new RegExp(/news/i);
			if (pattern.test(location.href)) $('#fast_text_' + rec_id + ', #fast_inpt_' + rec_id).css('width', '688px');
		});
	},
	wall_page: function() {
		var page_cnt = $('#page_cnt').val();
		var public_id = $('#public_id').val();
		$('#wall_all_records').attr('onClick', '');
		if ($('#load_wall_all_records').text() == 'к предыдущим записям' && $('#rec_num').text() > 10) {
			textLoad('load_wall_all_records');
			$.post('/index.php?go=public&pid=' + public_id, {
				page_cnt: page_cnt
			}, function(data) {
				$('#public_wall_records').append(data);
				$('#page_cnt').val((parseInt($('#page_cnt').val()) + 1));
				if ($('.wallrecord').length == $('#rec_num').text()) {
					$('#wall_all_records').hide();
				} else {
					$('#wall_all_records').attr('onClick', 'groups.wall_page(\'' + public_id + '\')');
					$('#load_wall_all_records').html('к предыдущим записям');
				}
			});
		}
	},
	wall_attach_addphoto: function(id, page_num, public_id) {
		wall.attach_menu('close', 'wall_attach', 'wall_attach_menu');
		if (page_num) page = '&page=' + page_num;
		else {
			page = '';
			page_num = 1;
		}
		Box.Page('/index.php?go=groups&act=photos', 'public_id=' + public_id + page, 'c_all_photos_' + page_num, 627, lang_wall_attatch_photos, lang_box_canсel, 0, 0, 400, 1, 0, 1, 0, 1);
	},
	wall_attach_insert: function(type, data, action_url) {
		if (!$('#wall_text').val()) wall.form_open();
		$('#attach_files').show();
		var attach_id = Math.floor(Math.random() * (1000 - 1 + 1)) + 1;
		//Если вставляем фотографию
		if (type == 'photo') {
			Box.Close('all_photos', 1);
			res_attach_id = 'photo_' + attach_id;
			$('#attach_files').append('<span id="attach_file_' + res_attach_id + '" class="attach_file"><div class="wall_attach_photo fl_l"><div class="wall_attach_del" onMouseOver="myhtml.title(\'' + res_attach_id + '\', \'' + lang_wall_no_atttach + '\', \'wall_photo_\')" onMouseOut="myhtml.title_close(\'' + res_attach_id + '\')" onClick="wall.attach_delete(\'' + res_attach_id + '\', \'photo|' + action_url + '||\')" id="wall_photo_' + res_attach_id + '"></div><img src="' + data + '" alt="" /></div></span>');
			$('#vaLattach_files').val($('#vaLattach_files').val() + 'photo|' + action_url + '||');
		}
		//Если вставляем видео
		if (type == 'video') {
			Box.Close('attach_videos');
			res_attach_id = 'video_' + attach_id;
			$('#attach_files').append('<span id="attach_file_' + res_attach_id + '" class="attach_file"><div class="wall_attach_photo fl_l"><div class="wall_attach_del" onMouseOver="myhtml.title(\'' + res_attach_id + '\', \'' + lang_wall_no_atttach + '\', \'wall_photo_\')" onMouseOut="myhtml.title_close(\'' + res_attach_id + '\')" onClick="wall.attach_delete(\'' + res_attach_id + '\', \'video|' + action_url + '||\')" id="wall_photo_' + res_attach_id + '"></div><img src="' + data + '" alt="" /></div></span>');
			$('#vaLattach_files').val($('#vaLattach_files').val() + 'video|' + action_url + '||');
		}
		var count = $('.attach_file').length;
		if (count > 9) $('#wall_attach').hide();
	},
	wall_photo_view: function(rec_id, public_id, src, pos, type) {
		var photo = $('#photo_wall_' + rec_id + '_' + pos).attr('src').replace('c_', '');
		var size = $('.page_num' + rec_id).length;
		if (size == 1) {
			var topTxt = 'Просмотр фотографии';
			var next = 'Photo.Close(\'\'); return false';
		} else {
			var topTxt = 'Фотография <span id="pTekPost">' + pos + '</span> из ' + size;
			var next = 'groups.wall_photo_view_next(' + rec_id + '); return false';
		}
		$.post('/index.php?go=attach_comm', {
			photo: photo
		}, function(d) {
			$('#cData').html(d);
		});
		var content = '<div id="photo_view" class="photo_view" onClick="groups.wall_photo_view_setEvent(event)">' + '<div class="photo_close" onClick="Photo.Close(\'\'); return false;"></div>' + '<div class="photo_bg" style="min-height:400px">' + '<div class="photo_com_title" style="padding-top:0px;">' + topTxt + '<div><a href="/" onClick="Photo.Close(\'\'); return false">Закрыть</a></div></div>' + '<div class="photo_img_box cursor_pointer" onClick="' + next + '"><img src="' + photo + '" id=\"photo_view_src\" style="margin-bottom:7px" /></div><div class="line_height">' + '<input type="hidden" id="photo_pos" value="' + pos + '" />' + '</div><div class="clear"></div>' + '<div id="cData"><center><img src="/templates/Default/images/progress.gif" style="margin-top:20px;margin-bottom:20px" /></center></div>' + '</div>' + '<div class="clear"></div>' + '</div>';
		$('body').append(content);
		$('#photo_view').show();
		if (is_moz && !is_chrome) scrollTopForFirefox = $(window).scrollTop();
		$('html, body').css('overflow-y', 'hidden');
		if (is_moz && !is_chrome) $(window).scrollTop(scrollTopForFirefox);
	},
	wall_photo_view_next: function(rec_id) {
		var pos = parseInt($('#photo_pos').val()) + 1;
		if ($('#photo_wall_' + rec_id + '_' + pos).attr('src')) var next_src = $('#photo_wall_' + rec_id + '_' + pos).attr('src').replace('c_', '');
		else var next_src = false;
		$('#photo_pos').val(pos);
		$('#pTekPost').text(pos);
		//Если уже последняя фотка, то следующей фоткой делаем первую
		if (pos > $('.page_num' + rec_id).length) {
			$('#photo_pos').val('1');
			$('#pTekPost').text('1');
			var next_src = $('#photo_wall_' + rec_id + '_1').attr('src').replace('c_', '');
		}
		$('#photo_view_src').attr('src', next_src);
		$('#cData').html('<center><img src="/templates/Default/images/progress.gif" style="margin-top:20px;margin-bottom:20px" /></center>');
		$.post('/index.php?go=attach_comm', {
			photo: next_src
		}, function(d) {
			$('#cData').html(d);
		});
	},
	wall_photo_view_setEvent: function(event) {
		var oi = (event.target) ? event.target.id : ((event.srcElement) ? event.srcElement.id : null);
		if (oi == 'photo_view') Photo.Close('');
	},
	wall_video_add_box: function() {
		wall.attach_menu('close', 'wall_attach', 'wall_attach_menu');
		Box.Show('attach_videos', 400, 'Ссылка видеозаписи на УдинБала', '<div style="padding:15px;"><input  type="text"  placeholder="Введите ссылку видеозаписи на УдинБала.."  class="videos_input" id="video_attach_lnk" style="width:355px;margin-top:10px" /></div>', lang_box_canсel, 'Прикрпепить', 'groups.wall_video_add_select()');
		$('#video_attach_lnk').focus();
	},
	wall_video_add_select: function() {
		var video_attach_lnk = $('#video_attach_lnk').val().replace('http://' + location.host + '/video', '');
		var data = video_attach_lnk.split('_');
		if (video_attach_lnk != 0) {
			$('#box_loading').show();
			ge('box_butt_create').disabled = true;
			$.post('/index.php?go=groups&act=select_video_info', {
				video_id: data[1]
			}, function(row) {
				if (row == 1) {
					addAllErr('Неверный адрес видеозаписи');
					$('#box_loading').hide();
					ge('box_butt_create').disabled = false;
				} else {
					groups.wall_attach_insert('video', '/uploads/videos/' + data[0] + '/' + row, row + '|' + data[1] + '|' + data[0]);
					$('#video_attach_lnk').val('');
				}
			});
		} else setErrorInputMsg('video_attach_lnk');
	},
	wall_add_like: function(rec_id, user_id, type) {
		if ($('#wall_like_cnt' + rec_id).text()) var wall_like_cnt = parseInt($('#wall_like_cnt' + rec_id).text()) + 1;
		else {
			$('#public_likes_user_block' + rec_id).show();
			$('#update_like' + rec_id).val('1');
			var wall_like_cnt = 1;
		}
		$('#wall_like_cnt' + rec_id).html(wall_like_cnt).css('color', '#2f5879');
		$('#wall_active_ic' + rec_id).addClass('public_wall_like_yes');
		$('#wall_like_link' + rec_id).attr('onClick', 'groups.wall_remove_like(' + rec_id + ', ' + user_id + ', \'' + type + '\')');
		$('#like_user' + user_id + '_' + rec_id).show();
		updateNum('#like_text_num' + rec_id, 1);
		if (type == 'uPages') $.post('/index.php?go=wall&act=like_yes', {
			rid: rec_id
		});
		else $.post('/index.php?go=groups&act=wall_like_yes', {
			rec_id: rec_id
		});
	},
	wall_remove_like: function(rec_id, user_id, type) {
		var wall_like_cnt = parseInt($('#wall_like_cnt' + rec_id).text()) - 1;
		if (wall_like_cnt <= 0) {
			var wall_like_cnt = '';
			$('#public_likes_user_block' + rec_id).hide();
		}
		$('#wall_like_cnt' + rec_id).html(wall_like_cnt).css('color', '#95adc0');
		$('#wall_active_ic' + rec_id).removeClass('public_wall_like_yes');
		$('#wall_like_link' + rec_id).attr('onClick', 'groups.wall_add_like(' + rec_id + ', ' + user_id + ', \'' + type + '\')');
		$('#Xlike_user' + user_id + '_' + rec_id).hide();
		$('#like_user' + user_id + '_' + rec_id).hide();
		updateNum('#like_text_num' + rec_id);
		if (type == 'uPages') $.post('/index.php?go=wall&act=like_no', {
			rid: rec_id
		});
		else $.post('/index.php?go=groups&act=wall_like_remove', {
			rec_id: rec_id
		});
	},
	wall_like_users_five: function(rec_id, type) {
		$('.public_likes_user_block').hide();
		if (!ge('like_cache_block' + rec_id) && $('#wall_like_cnt' + rec_id).text() && $('#update_like' + rec_id).val() == 0) {
			if (type == 'uPages') {
				$.post('/index.php?go=wall&act=liked_users', {
					rid: rec_id
				}, function(data) {
					$('#likes_users' + rec_id).html(data + '<span id="like_cache_block' + rec_id + '"></span>');
					$('#public_likes_user_block' + rec_id).show();
				});
			} else {
				$.post('/index.php?go=groups&act=wall_like_users_five', {
					rec_id: rec_id
				}, function(data) {
					$('#likes_users' + rec_id).html(data + '<span id="like_cache_block' + rec_id + '"></span>');
					$('#public_likes_user_block' + rec_id).show();
				});
			}
		} else
		if ($('#wall_like_cnt' + rec_id).text()) $('#public_likes_user_block' + rec_id).show();
	},
	wall_like_users_five_hide: function() {
		$('.public_likes_user_block').hide();
	},
	wall_all_liked_users: function(rid, page_num, liked_num) {
		$('.public_likes_user_block').hide();
		if (page_num) page = '&page=' + page_num;
		else {
			page = '';
			page_num = 1;
		}
		if (!liked_num) liked_num = 1;
		Box.Page('/index.php?go=groups&act=all_liked_users', 'rid=' + rid + '&liked_num=' + liked_num + page, 'all_liked_users_' + rid + page_num, 525, lang_wall_liked_users, lang_msg_close, 0, 0, 345, 1, 1, 1, 0, 1);
	},
	wall_tell: function(rec_id) {
		$('#wall_tell_' + rec_id).hide();
		myhtml.title_close(rec_id);
		$('#wall_ok_tell_' + rec_id).fadeIn(150);
		$.post('/index.php?go=groups&act=wall_tell', {
			rec_id: rec_id
		}, function(data) {
			if (data == 1) addAllErr(lang_wall_tell_tes);
		});
	},
	all_people: function(public_id, page_num) {
		if (page_num) page = '&page=' + page_num;
		else {
			page = '';
			page_num = 1;
		}
		var num = $('#traf').text();
		Box.Page('/index.php?go=groups&act=all_people', 'public_id=' + public_id + '&num=' + num + page, 'all_peoples_users_' + public_id + page_num, 525, 'Подписчики', lang_msg_close, 0, 0, 345, 1, 1, 1, 0, 1);
	},
	all_groups_user: function(for_user_id, page_num) {
		if (page_num) page = '&page=' + page_num;
		else {
			page = '';
			page_num = 1;
		}
		var num = $('#groups_num').text();
		Box.Page('/index.php?go=groups&act=all_groups_user', 'for_user_id=' + for_user_id + '&num=' + num + page, 'all_groups_users_' + for_user_id + page_num, 525, 'Интересные страницы', lang_msg_close, 0, 0, 345, 1, 1, 1, 0, 1);
	},
	inviteBox: function(i) {
		viiBox.start();
		$.post('/index.php?go=groups&act=invitebox', {
			id: i
		}, function(d) {
			viiBox.win('inviteBox', d);
		});
	},
	inviteSet: function(i) {
		var check = $('#user' + i).attr('class').replace('grIntiveUser', '');
		var numCheck = parseInt($('#usernum2').text());
		var limit = 50;
		if (!check) {
			if (numCheck >= limit) {
				Box.Info('load_photo_er', 'Информация', 'Вы можете пригласить в сообщество не более ' + limit + ' друзей за один раз.', 380, 3000);
				return false;
			}
			if (numCheck <= 0) $('#usernum, #buttomDiv').fadeIn('fast');
			$('#usernum2').text(numCheck + 1);
			$('#user' + i).addClass('grIntiveUserActive');
			$('#userInviteList').val($('#userInviteList').val() + '|' + i + '|');
		} else {
			$('#user' + i).removeClass('grIntiveUserActive');
			$('#userInviteList').val($('#userInviteList').val().replace('|' + i + '|', ''));
			$('#usernum2').text(numCheck - 1);
			if (parseInt($('#usernum2').text()) <= 0) $('#usernum, #buttomDiv').fadeOut('fast');
		}
	},
	inviteSend: function(i) {
		var userInviteList = $('#userInviteList').val();
		butloading('invSending', 126, 'disabled');
		$.post('/index.php?go=groups&act=invitesend', {
			id: i,
			ulist: userInviteList
		}, function(d) {
			if (d == 1) Box.Info('load_photo_er', 'Информация', 'Вы можете пригласить в сообщество не более 50 друзей в день.', 380, 3000);
			else Box.Info('load_photo_er', 'Информация', 'Приглашения успешно разосланы.', 230, 2600);
			viiBox.clos('inviteBox', 1);
		});
	},
	inviteFriendsPage: function(i) {
		if ($('#load_invite_prev_ubut').text() == 'Показать больше друзей') {
			textLoad('load_invite_prev_ubut');
			$.post('/index.php?go=groups&act=invitebox', {
				page_cnt: page_cnt_invite,
				id: i
			}, function(d) {
				page_cnt_invite++;
				$('#inviteUsers').append(d);
				$('#load_invite_prev_ubut').text('Показать больше друзей');
				if (!d) $('#invite_prev_ubut').remove();
			});
		}
	},
	InviteOk: function(i) {
		$('#action_' + i).html('<span class="color777">Вы вступили в сообщество.</span>');
		$.post('/index.php?go=groups&act=login', {
			id: i
		});
	},
	InviteNo: function(i) {
		$('#action_' + i).html('<span class="color777">Приглашение отклонено.</span>');
		$.post('/index.php?go=groups&act=invite_no', {
			id: i
		});
	},
	invitePage: function() {
		if ($('#load_gr_invite_prev_ubut').text() == 'Показать больше приглашений') {
			textLoad('load_gr_invite_prev_ubut');
			$.post('/index.php?go=groups&act=invites', {
				page_cnt: page_cnt_invite_gr
			}, function(d) {
				page_cnt_invite_gr++;
				$('#preLoadedGr').append(d);
				$('#load_gr_invite_prev_ubut').text('Показать больше приглашений');
				if (!d) $('#gr_invite_prev_ubut').remove();
			});
		}
	},
	wall_fasten: function(i) {
		$('.wall_fasten').css('opacity', '0.5');
		$('#wall_fasten_' + i).css('opacity', '1').attr('onClick', 'groups.wall_unfasten(' + i + ')');
		$.post('/index.php?go=groups&act=fasten', {
			rec_id: i
		});
	},
	wall_unfasten: function(i) {
		$('.wall_fasten').css('opacity', '0.5');
		$('#wall_fasten_' + i).attr('onClick', 'groups.wall_fasten(' + i + ')');
		$.post('/index.php?go=groups&act=unfasten', {
			rec_id: i
		});
	}
}
//AUDIO
var first = '';
var audio = {
	addBox: function(f) {
		first = f;
		$('.js_titleRemove').remove();
		Box.Close();
		Box.Show('addaudio', 510, lang_audio_add, '<div class="videos_pad"><div class="buttonsprofile albumsbuttonsprofile buttonsprofileSecond" style="height:22px;margin-bottom:20px;margin-top:-5px"><div class="buttonsprofileSec cursor_pointer"><a><div><b>По ссылке</b></div></a></div><a class="cursor_pointer" onClick="audio.addBoxComp()"><div><b>С компьютера</b></div></a></div><div class="videos_text">Вставьте ссылку на mp3 файл</div><input type="text" class="videos_input" id="audio_lnk" style="margin-top:5px" /><span id="vi_info">Например: <b>http://music.com/uploads/files/audio/2012/faxo_-_kalp.mp3</b></span></div>', lang_box_canсel, lang_album_create, 'audio.send()', 0, 0, 1, 1);
		$('#audio_lnk').focus();
	},
	addBoxComp: function() {
		Box.Close();
		Box.Show('addaudio_comp', 510, lang_audio_add, '<div class="videos_pad"><div class="buttonsprofile albumsbuttonsprofile buttonsprofileSecond" style="height:22px;margin-bottom:20px;margin-top:-5px"><a onClick="audio.addBox()" class="cursor_pointer"><div><b>По ссылке</b></div></a><div class="buttonsprofileSec cursor_pointer"><a><div><b>С компьютера</b></div></a></div></div><div class="videos_text">Ограничения<div class="clear"></div><li style="font-weight:normal;color:#000;font-size:11px;margin-top:10px">Аудиофайл не должен превышать 10 Мб и должен быть в формате MP3.</li><li style="font-weight:normal;color:#000;font-size:11px;margin-bottom:15px">Аудиофайл не должен нарушать авторские права.</li><div class="button_div fl_l" style="margin-left:170px"><button id="upload">Выбрать файл</button></div><div class="clear"></div><div style="margin-top:15px;font-size:11px;color:#000;font-weight:normal">Вы также можете добавить аудиозапись из числа уже загруженных файлов, воспользовавшись <a href="/?go=search&type=5"><b>поиском по аудио.</b></a></div></div></div>', lang_box_canсel, lang_album_create, 'audio.send()', 0, 0, 1, 1);
		$('#audio_lnk').focus();
		$('#box_but').hide();
		Xajax = new AjaxUpload('upload', {
			action: '/index.php?go=audio&act=upload',
			name: 'uploadfile',
			onSubmit: function(file, ext) {
				if (!(ext && /^(mp3)$/.test(ext))) {
					Box.Info('load_photo_er', lang_dd2f_no, 'Аудиофайл должен быть в формате MP3.', 250);
					return false;
				}
				butloading('upload', '73', 'disabled', '');
			},
			onComplete: function(file, data) {
				butloading('upload', '73', 'enabled', 'Выбрать файл');
				if (data == 1) Box.Info('load_photo_er', lang_dd2f_no, 'Аудиофайл не должен превышать 10 Мб и должен быть в формате MP3.', 250);
				else {
					Box.Close();
					if (first == 1) window.location.href = '/audio';
					else player.xSearch();
				}
			}
		});
	},
	send: function() {
		var lnk = $('#audio_lnk').val();
		if (lnk != 0) {
			$('#box_loading').show();
			ge('box_butt_create').disabled = true;
			$.post('/index.php?go=audio&act=send', {
				lnk: lnk
			}, function(d) {
				if (d) {
					addAllErr(lang_audio_err);
					ge('box_butt_create').disabled = false;
				} else {
					Box.Close();
					if (first == 1) window.location.href = '/audio';
					else player.xSearch();
				}
				$('#box_loading').hide();
			});
		} else setErrorInputMsg('audio_lnk');
	},
	edit: function(aid, pid) {
		if (pid) funcsave = 'PublicAudioEditsave(' + aid + ', ' + pid + ')';
		else funcsave = 'audio.editsave(' + aid + ')';
		Box.Show('edit' + aid, 510, 'Редактирование аудиозаписи', '<div class="videos_pad"><div class="videos_text">Исполнитель</div><input type="text" class="videos_input" id="valartis' + aid + '" style="margin-bottom:15px" value="' + $('#artis' + aid).html() + '" /><div class="videos_text">Название</div><input type="text" class="videos_input" id="vaname' + aid + '" value="' + $('#name' + aid).html() + '" /></div>', lang_box_canсel, 'Сохранить', funcsave, 0, 0, 1, 1);
		$('#audio_lnk').focus();
	},
	editsave: function(aid) {
		if ($('#valartis' + aid).val() != 0) $('#artis' + aid).text($('#valartis' + aid).val());
		else $('#artis' + aid).text('Неизвестный исполнитель');
		if ($('#vaname' + aid).val() != 0) $('#name' + aid).text($('#vaname' + aid).val());
		else $('#name' + aid).text('Без названия');
		$.post('/index.php?go=audio&act=editsave', {
			aid: aid,
			artist: $('#valartis' + aid).val(),
			name: $('#vaname' + aid).val()
		});
		Box.Close();
	},
	del: function(aid) {
		$('.js_titleRemove').remove();
		$('#jQaudios').html('<center><img src="' + template_dir + '/images/loading_im.gif" style="margin-top:135px" /></center>');
		$('.staticpl_albut').hide();
		$.post('/index.php?go=audio&act=del', {
			aid: aid
		}, function(d) {
			player.xSearch();
		});
	},
	addMyList: function(aid, jid) {
		if (!jid) jid = aid;
		$('.js_titleRemove').remove();
		$('#atrack_' + jid).remove();
		$('#atrackAddOk' + jid).show();
		$.post('/index.php?go=audio&act=addmylist', {
			aid: aid
		});
	}
}
//AUDIO -> PLAYER
var music = {
	jPlayerInc: function() {
		var hs = location.hash.replace('#', '');
		if (hs >= 1 && hs <= 3) {
			$('#teck_id').val(hs);
		}
		$("#jquery_jplayer").jPlayer({
			ready: function() {
				var musId = $('#music_' + $('#teck_id').val()).attr('data');
				var musName = $('#music_' + $('#teck_id').val()).text();
				$('#teck_track_name').text(musName);
				$(this).jPlayer("setMedia", {
					mp3: musId
				});
				if (hs >= 1 && hs <= 3) music.nullPlay();
			},
			timeupdate: function(event) {
				if ($('#typePlay').val() == 'standart') {
					var myPlayedTime = parseInt(event.jPlayer.status.currentTime);
					if ($('#typePlay').val() == 'standart') $("#play_time" + $('#teck_prefix').val() + $('#teck_id').val()).text(myPlayedTime.toString().toHHMMSS());
					else $("#play_time").text(myPlayedTime.toString().toHHMMSS());
					if (event.jPlayer.status.currentTime && event.jPlayer.status.currentTime == event.jPlayer.status.duration) music.next();
				}
			},
			swfPath: "/templates/Default/js/jplayer",
			cssPrefix: "different_prefix_example",
			cssSelectorAncestor: '',
			cssSelector: {
				play: "#player_play",
				pause: "#player_pause",
				stop: "#player_stop",
				seekBar: "#player_progress_load_bar",
				playBar: "#player_progress_play_bar",
				volumeBar: "#player_volume_bar",
				volumeBarValue: "#player_volume_bar_value",
				volumeMax: "#player_volume_max"
			}
		});
	},
	newStartPlay: function(id, prefix) {
		if (!prefix) var prefix = '';
		if ($('#typePlay').val() == 'standart') {
			$('#ppbarPro' + $('#teck_prefix').val() + $('#teck_id').val()).html('').hide();
			$("#play_time" + $('#teck_prefix').val() + $('#teck_id').val()).hide();
			//$('#ppbarPro'+prefix+id).html('<div id="player_progress_load_bar" onClick="" style="height:5px"><div id="player_progress_play_bar" style="height:5px"></div></div>').show();
			$("#play_time" + prefix + id).show();
		} else {
			if (!prefix) {
				var size = $('.audio_onetrack').length;
				var randId = Math.floor(Math.random() * size);
				if (randId == 0) randId = 1;
				if ($('#rand').val() == 1) id = randId;
				var idUload = size - 7;
				if (id >= idUload) audio.page();
			}
		}
		if ($('#refresh').val() > 0) {
			$("#jquery_jplayer").jPlayer('pause');
			$("#jquery_jplayer").jPlayer('play');
			$('#icPlay_' + $('#teck_id').val()).addClass('audio_stopic').attr('onClick', '$("#jquery_jplayer").jPlayer(\'pause\'); music.pause()');
		} else {
			if ($('#teck_prefix').val()) $('#icPlay_' + $('#teck_prefix').val() + $('#teck_id').val()).removeClass('audio_stopic').attr('onClick', 'music.newStartPlay(' + $('#teck_id').val() + ', ' + $('#teck_prefix').val() + ')');
			else $('#icPlay_' + $('#teck_prefix').val() + $('#teck_id').val()).removeClass('audio_stopic').attr('onClick', 'music.newStartPlay(' + $('#teck_id').val() + ')');
			$('#teck_id').val(id);
			$("#jquery_jplayer").jPlayer('pause');
			$('#icPlay_' + prefix + id).addClass('audio_stopic').attr('onClick', '$("#jquery_jplayer").jPlayer(\'pause\'); music.pause()');
			$('#teck_prefix').val(prefix);
			if ($('#music_' + prefix + $('#teck_id').val()).attr('data')) {
				var musId = $('#music_' + prefix + $('#teck_id').val()).attr('data');
				var musName = $('#music_' + prefix + $('#teck_id').val()).text();
				$('#teck_track_name').text(musName);
				$("#jquery_jplayer").jPlayer("setMedia", {
					mp3: musId
				});
				$("#jquery_jplayer").jPlayer('play');
			} else music.newStartPlay(1, $('#teck_prefix').val());
		}
	},
	next: function() {
		$('#icPlay_' + $('#teck_prefix').val() + $('#teck_id').val()).removeClass('audio_stopic');
		if ($('#teck_prefix').val()) {
			var size = $('.audioForSize' + $('#teck_prefix').val()).length;
			if (size > 1 && $('#teck_id').val() < size) {
				music.newStartPlay((parseInt($('#teck_id').val()) + 1), $('#teck_prefix').val());
			} else {
				$('#ppbarPro' + $('#teck_prefix').val() + $('#teck_id').val()).html('').hide();
				$("#play_time" + $('#teck_prefix').val() + $('#teck_id').val()).hide();
				$('#icPlay_' + $('#teck_prefix').val() + $('#teck_id').val()).removeClass('audio_stopic').attr('onClick', 'music.newStartPlay(' + $('#teck_id').val() + ', ' + $('#teck_prefix').val() + ')');
			}
		} else music.newStartPlay((parseInt($('#teck_id').val()) + 1));
	},
	prev: function() {
		$('#icPlay_' + $('#teck_prefix').val() + $('#teck_id').val()).removeClass('audio_stopic');
		music.newStartPlay((parseInt($('#teck_id').val()) - 1));
	},
	pause: function() {
		$('#icPlay_' + $('#teck_prefix').val() + $('#teck_id').val()).removeClass('audio_stopic').attr('onClick', 'music.proceed()');
	},
	proceed: function() {
		$("#jquery_jplayer").jPlayer('play');
		$('#icPlay_' + $('#teck_prefix').val() + $('#teck_id').val()).addClass('audio_stopic').attr('onClick', '$("#jquery_jplayer").jPlayer(\'pause\'); music.pause()');
	},
	nullPlay: function() {
		$('#icPlay_' + $('#teck_id').val()).addClass('audio_stopic').attr('onClick', '$("#jquery_jplayer").jPlayer(\'pause\'); music.pause()');
		$("#jquery_jplayer").jPlayer('play');
	},
	nullPause: function() {
		$('#icPlay_' + $('#teck_id').val()).removeClass('audio_stopic').attr('onClick', 'music.nullPlay()');
		$("#jquery_jplayer").jPlayer('pause');
	},
	volumeOff: function() {
		$('.player_del_volume').css('opacity', '1');
		$('.player_max_volume').css('opacity', '0.5');
		$('#jquery_jplayer').volume(0);
	},
	volumeMax: function() {
		$('.player_del_volume').css('opacity', '0.5');
		$('.player_max_volume').css('opacity', '1');
		$('#jquery_jplayer').volume(100);
	},
	volume: function() {
		$('.player_max_volume, .player_del_volume').css('opacity', '0.5');
	},
	refresh: function() {
		$('.player_refresh').css('opacity', '1').attr('onClick', 'music.refreshOff()');
		$('#refresh').val($('#teck_id').val());
		music.randOff();
	},
	refreshOff: function() {
		$('.player_refresh').css('opacity', '0.5').attr('onClick', 'music.refresh()');
		$('#refresh').val(0);
	},
	randOn: function() {
		$('.player_rand').css('opacity', '1').attr('onClick', 'music.randOff()');
		$('#rand').val(1);
		music.refreshOff();
	},
	randOff: function() {
		$('.player_rand').css('opacity', '0.5').attr('onClick', 'music.randOn()');
		$('#rand').val(0);
	}
}
//IM
var i = 0;
var imrearstart = 1;
var vii_typograf_delay = false;
var vii_msg_te_val = '';
var vii_typograf = true;
var im = {
	typograf: function() {
		var for_user_id = $('#for_user_id').val();
		var a = $('#msg_text').val();
		if (vii_typograf) {
			$.post('/index.php?go=im&act=typograf', {
				for_user_id: for_user_id
			});
			vii_typograf = false;
		}
		if (!vii_typograf) {
			0 == vii_msg_te_val != a && a != 0 < a.length && (clearInterval(vii_typograf_delay), vii_typograf_delay = setInterval(function() {
				$.post('/index.php?go=im&act=typograf&stop=1', {
					for_user_id: for_user_id
				});
				vii_typograf = true;
			}, 3000));
		}
	},
	settTypeMsg: function() {
		Page.Loading('start');
		$.post('/index.php?go=messages&act=settTypeMsg', function(d) {
			Page.Go('/messages');
		});
	},
	open: function(uid) {
		var room_id = uid.toString().substr(0, 1) == 'c' ? uid.toString().substr(1) : 0;
		$('.im_oneusr').removeClass('im_usactive');
		$('#dialog' + uid).addClass('im_usactive');
		$('#imViewMsg').html('<img src="' + template_dir + '/images/loading_im.gif" style="margin-left:225px;margin-top:220px" />');
		$.post('/index.php?go=im&act=history', {
			need_read: 1,
			for_user_id: uid,
			room_id: room_id
		}, function(d) {
			$('#imViewMsg').html(d);
			$('.im_scroll').append('<div class="im_typograf"></div>').scrollTop(99999);
			var aco = $('.im_usactive').text().split(' ');
			$('.im_typograf').html('<div class="no_display" id="im_typograf"><img src="' + template_dir + '/images/typing.gif" /> ' + aco[0] + ' набирает сообщение..</div>');
			$('#msg_text').focus();
			var count = parseInt($('#msg_num' + uid).text());
			if (count) {
				var msg_num = parseInt($('#new_msg').text().replace(')', '').replace('(', '')),
					new_count = msg_num - count;
				if (new_count) $('#new_msg').html("<div class=\"headm_newac\" style=\"margin-left:37px\">" + new_count + "</div>");
				else $('#new_msg').html("");
				$('#msg_num' + uid).text('').hide();
			}
		});
	},
	read: function(msg_id, auth_id, my_id, room_id = 0) {
		if (auth_id != my_id && imrearstart) {
			if (room_id > 0) auth_id = 'c' + room_id;
			imrearstart = 0;
			var msg_num = parseInt($('#new_msg').text().replace(')', '').replace('(', '')) - 1;
			$.post('/index.php?go=im&act=read', {
				msg_id: msg_id
			}, function() {
				imrearstart = 1;
				if (msg_num > 0) $('#new_msg').html("<div class=\"headm_newac\" style=\"margin-left:37px\">" + msg_num + "</div>");
				else $('#new_msg').html('');
				updateNum('#msg_num' + auth_id);
				if ($('#msg_num' + auth_id).text() <= 0) $('#msg_num' + auth_id).hide();
				$('#imMsg' + msg_id).css('background', '#fff').attr('onMouseOver', '');
			});
		}
	},
	send: function(for_user_id, my_name, my_ava) {
		var room_id = for_user_id.toString().substr(0, 1) == 'c' ? for_user_id.toString().substr(1) : 0;
		var msg_text = $('#msg_text').val();
		var attach_files = $('#vaLattach_files').val();
		if (msg_text != 0 && $('#status_sending').val() == 1 || attach_files != 0) {
			butloading('sending', 56, 'disabled');
			$('#status_sending').val('0');
			$.post('/index.php?go=im&act=send', {
				room_id: room_id,
				for_user_id: for_user_id,
				my_name: my_name,
				my_ava: my_ava,
				msg: msg_text,
				attach_files: attach_files
			}, function(data) {
				if (data == 'antispam_err') {
					AntiSpam('messages');
					return false;
				}
				if (data == 'err_privacy') Box.Info('msg_info', lang_pr_no_title, lang_pr_no_msg, 400, 4000);
				else {
					$('#im_scroll').append(data);
					$('.im_scroll').scrollTop(99999);
					$('#msg_text, #vaLattach_files').val('');
					$('#attach_files').html('');
					$('#msg_text').focus();
					$('#status_sending').val('1');
					butloading('sending', 56, 'enabled', 'Отправить');
				}
			});
		} else setErrorInputMsg('msg_text');
	},
	delet: function(mid, folder) {
		$('.js_titleRemove, #imMsg' + mid).remove();
		$.post('/index.php?go=im&act=delet', {
			mid: mid,
			folder: folder
		});
	},
	update: function(for_user_id) {
		var room_id = for_user_id.toString().substr(0, 1) == 'c' ? for_user_id.toString().substr(1) : 0;
		var for_user_id = $('#for_user_id').val();
		var last_id = $('.im_msg:last').attr('id').replace('imMsg', '');
		$.post('/index.php?go=im&act=update', {
			for_user_id: for_user_id,
			last_id: last_id,
			room_id: room_id
		}, function(d) {
			if (d.length != '49' && d != 'no_new') {
				$('#im_scroll').html(d);
				$('.im_scroll').scrollTop(99999);
			}
			if (d.length == 49) $('#im_typograf').fadeIn();
			else $('#im_typograf').fadeOut()
		});
	},
	page: function(for_user_id) {
		var room_id = for_user_id.toString().substr(0, 1) == 'c' ? for_user_id.toString().substr(1) : 0;
		var first_id = $('.im_msg:first').attr('id').replace('imMsg', '');
		$('#wall_all_records').attr('onClick', '');
		if ($('#load_wall_all_records').text() == 'Показать предыдущие сообщения') {
			textLoad('load_wall_all_records');
			$.post('/index.php?go=im&act=history', {
				first_id: first_id,
				for_user_id: for_user_id,
				room_id: room_id
			}, function(data) {
				i++;
				var imHeiah = $('.im_scroll').height();
				$('#prevMsg').html('<div id="appMsgFScroll' + i + '" class="no_display">' + data + '</div>' + $('#prevMsg').html());
				$('.im_scroll').scrollTop($('#appMsgFScroll' + i).show().height() + imHeiah);
				if (!data) {
					$('#wall_all_records').hide();
				} else {
					$('#wall_all_records').attr('onClick', 'im.page(' + for_user_id + ')');
					$('#load_wall_all_records').html('Показать предыдущие сообщения');
				}
			});
		}
	},
	box_del: function(u) {
		Box.Show('im_del' + u, 350, 'Удалить все сообщения', '<div style="padding:15px;" id="del_status_text_im">Вы действительно хотите удалить всю переписку с данным пользователем?<br /><br />Отменить это действие будет невозможно.</div>', lang_box_canсel, lang_box_yes, 'im.del(' + u + ')');
	},
	del: function(u) {
		var room_id = u.toString().substr(0, 1) == 'c' ? u.toString().substr(1) : 0;
		$('#box_loading').show();
		ge('box_butt_create').disabled = true;
		$('#del_status_text_im').text('Переписка удаляется..');
		$.post('/index.php?go=im&act=del', {
			im_user_id: u,
			room_id: room_id
		}, function(d) {
			Box.Close('im_del' + u);
			Box.Info('ok_im', 'История переписки удалена', 'Все сообщения диалога были успешно удалены.', 300, 3000);
			$('#okim' + u).remove();
		});
	},
	updateDialogs: function() {
		$.post('/index.php?go=im&act=upDialogs', function(d) {
			$('#updateDialogs').html(d);
		});
	}
}
//Distinguish
var Distinguish = {
	Start: function(id) {
		var x1w = $('#ladybug_ant' + id).width();
		var y1h = $('#ladybug_ant' + id).height();
		var scH = $(window).height();
		var scW = $(window).width();
		$('#i_left' + id).val('30');
		$('#i_top' + id).val('30');
		$('#i_width' + id).val(x1w);
		$('#i_height' + id).val(y1h);
		$('#ladybug_ant' + id).css('cursor', 'crosshair');
		if (!$('.distin_friends_list').text()) {
			$('#friends_block').remove();
			$('html, body').append('<div id="friends_block"><div class="box_title">Введите имя<div class="box_close" onClick="Distinguish.Close(' + id + ')"></div></div><div class="distin_inpbg"><input type="text" id="filter" class="inpst" maxlength="50" value="" style="width:160px;" /></div><div class="distin_friends_list"><center><img src="/templates/Default/images/loading_mini.gif" style="margin-top:10px;margin-bottom:10px" /></center></div><div class="distin_inpbg"><div class="button_div fl_l"><button onClick="Distinguish.SelectUser(0, 0, ' + id + ', 0); return false">Добавить</button></div><div class="button_div_gray fl_l margin_left"><button onClick="Distinguish.Close(' + id + '); return false;" >Отмена</button></div><div class="clear"></div></div></div>');
		}
		$('#ladybug_ant' + id).imgAreaSelect({
			handles: true,
			onSelectEnd: function(img, selection) {
				var pvW = $('#ladybug_ant' + id).position().left + selection.x1 + selection.width + 20;
				var pvH = $('#ladybug_ant' + id).position().top + selection.y1;
				$('#i_left' + id).val(selection.x1);
				$('#i_top' + id).val(selection.y1);
				$('#i_width' + id).val(selection.width);
				$('#i_height' + id).val(selection.height);
				$('#friends_block').css('margin-left', pvW + 'px').css('top', '0px').css('margin-top', pvH + 'px').fadeIn(400);
				$('#filter').focus();
				if (!$('.distin_friends_list').text()) {
					$.post('/index.php?go=distinguish&act=load_friends', {
						photo_id: id
					}, function(d) {
						$('.distin_friends_list').html(d).css('padding-bottom', '3px').css('padding-top', '3px');
					});
				}
			},
			onSelectChange: function() {
				$('#friends_block').hide();
			}
		});
	},
	ShowTag: function(left, top, width, height, id) {
		Distinguish.HideTag();
		var imgHeight = $('#ladybug_ant' + id).height();
		var imgWidth = $('#ladybug_ant' + id).width();
		var aTop = $('#ladybug_ant' + id).position().top;
		var aLeft = $('#ladybug_ant' + id).position().left;
		if (aTop < 56)
			if ($('#mark_userid_bg' + id).text()) var aTop = 114;
			else var aTop = 55;
		if (aLeft < 0) var aLeft = 0;
		$('#distinguishSettings_left' + id).css('width', left + 'px').css('height', imgHeight + 'px').css('left', aLeft + 'px');
		$('#distinguishSettings_top' + id).css('height', top + 'px').css('width', (imgWidth - left) + 'px').css('left', (aLeft + left) + 'px');
		$('#distinguishSettings_right' + id).css('left', (width + aLeft + left) + 'px').css('height', (imgHeight - top) + 'px').css('width', (imgWidth - left - width) + 'px').css('top', (aTop + top) + 'px');
		$('#distinguishSettings_bottom' + id).css('top', (aTop + height + top) + 'px').css('width', width + 'px').css('height', (imgHeight - height - top) + 'px').css('left', (aLeft + left) + 'px');
		$('#distinguishSettingsBorder_left' + id).css('width', left + 'px').css('height', height + 'px').css('top', (aTop + top) + 'px').css('left', aLeft + 'px');
		$('#distinguishSettingsBorder_top' + id).css('width', width + 'px').css('height', top + 'px').css('left', (aLeft + left) + 'px');
		$('#distinguishSettingsBorder_right' + id).css('left', (width + aLeft + left - 3) + 'px').css('height', height + 'px').css('width', (imgWidth - left - width) + 'px').css('top', (aTop + top) + 'px');
		$('#distinguishSettingsBorder_bottom' + id).css('top', (aTop + height + top - 3) + 'px').css('width', width + 'px').css('height', (imgHeight - height - top) + 'px').css('left', (aLeft + left) + 'px');
		$('#distinguishSettings' + id).show();
	},
	HideTag: function(id) {
		$('#distinguishSettings' + id).hide();
	},
	Close: function(id) {
		$('#ladybug_ant' + id).css('cursor', 'pointer');
		$('#friends_block').hide();
		$('#ladybug_ant' + id).imgAreaSelect({
			remove: true
		});
	},
	GeneralClose: function() {
		$('#friends_block, .distin_friends_list').remove();
		$('.distinguishSettings').hide();
		$('.ladybug_ant').css('cursor', 'pointer');
		$('.ladybug_ant').imgAreaSelect({
			remove: true
		});
	},
	FriendPage: function(page, photo_id) {
		$.post('/index.php?go=distinguish&act=load_friends', {
			page: page,
			photo_id: photo_id
		}, function(d) {
			$('.distin_friends_list').append(d);
		});
	},
	SelectUser: function(user_id, user_name, photo_id, no_user) {
		if (!user_name) var user_name = $('#filter').val();
		var i_left = $('#i_left' + photo_id).val();
		var i_top = $('#i_top' + photo_id).val();
		var i_width = $('#i_width' + photo_id).val();
		var i_height = $('#i_height' + photo_id).val();
		var size = $('.one_dis_user' + photo_id).length;
		if (size >= 1) {
			var comma = '<div class="fl_l" style="margin-right:4px">, </div>';
			var comma2 = '';
		} else {
			var comma = '';
			var comma2 = '<div class="fl_l" id="peopleOnPhotoText' + photo_id + '" style="margin-right:5px">На этой фотографии:</div>';
		}
		Distinguish.Close(photo_id);
		Distinguish.Start(photo_id);
		if (no_user != 0) {
			var lnk = '<a href="/u' + user_id + '" id="selected_us_' + user_id + photo_id + '" onClick="Page.Go(this.href); return false" onMouseOver="Distinguish.ShowTag(' + i_left + ', ' + i_top + ', ' + i_width + ', ' + i_height + ', ' + photo_id + ')" onMouseOut="Distinguish.HideTag(' + photo_id + ')" class="one_dis_user' + photo_id + '">';
			var lnk_end = '</a>';
			var user_ok = 'yes';
		} else {
			var lnk = '<span style="color:#000" onMouseOver="Distinguish.ShowTag(' + i_left + ', ' + i_top + ', ' + i_width + ', ' + i_height + ', ' + photo_id + ')" onMouseOut="Distinguish.HideTag(' + photo_id + ')" class="one_dis_user' + photo_id + '">';
			var lnk_end = '</span>';
			var user_id = 0;
			var user_ok = 'no';
		}
		if ($('#selected_us_' + user_id + photo_id).text()) $('#selected_us_' + user_id + photo_id).attr('onMouseOver', 'Distinguish.ShowTag(' + i_left + ', ' + i_top + ', ' + i_width + ', ' + i_height + ', ' + photo_id + ')');
		else $('#peoples_on_this_photos' + photo_id).append(comma2 + '<span id="selectedDivIser' + user_id + photo_id + '">' + comma + '<div class="fl_l">' + lnk + user_name + lnk_end + '</div><div class="fl_l"><img src="/templates/Default/images/hide_lef.gif" class="distin_del_user" title="Удалить отметку" onClick="Distinguish.DeletUser(' + user_id + ', ' + photo_id + ')" /></div></span>');
		$('#filter').val('');
		$('.echoUsersList').show();
		if (user_ok == 'yes') var user_name = '';
		$.post('/index.php?go=distinguish&act=mark', {
			i_left: i_left,
			i_top: i_top,
			i_width: i_width,
			i_height: i_height,
			photo_id: photo_id,
			user_id: user_id,
			user_name: user_name,
			user_ok: user_ok
		});
	},
	DeletUser: function(user_id, photo_id, user_name) {
		$('#mark_userid_bg' + photo_id).remove().text('');
		$('#selectedDivIser' + user_id + photo_id).remove();
		var size = $('.one_dis_user' + photo_id).length;
		if (size <= 0) $('#peopleOnPhotoText' + photo_id).remove();
		if (user_name) var user_id = 0;
		$.post('/index.php?go=distinguish&act=mark_del', {
			photo_id: photo_id,
			user_id: user_id,
			user_name: user_name
		});
	},
	OkUser: function(photo_id) {
		$('#mark_userid_bg' + photo_id).remove().text('');
		$.post('/index.php?go=distinguish&act=mark_ok', {
			photo_id: photo_id
		});
	}
}
//HAPPY FRIENDS
var HappyFr = {
	Show: function() {
		$('.profile_block_happy_friends').css('max-height', (($('.profile_onefriend_happy').length - 4) / 2) * 190 + 190 + 'px');
		$('#happyAllLnk').attr('onClick', 'HappyFr.Close()');
		$('.profile_block_happy_friends_lnk').text('Скрыть');
	},
	Close: function() {
		$('.profile_block_happy_friends').css('max-height', '190px');
		$('#happyAllLnk').attr('onClick', 'HappyFr.Show()');
		$('.profile_block_happy_friends_lnk').text('Показать все');
	},
	HideSess: function() {
		$('.js_titleRemove').remove();
		$('#happyBLockSess').hide();
		$.post('/index.php?go=happy_friends_block_hide');
	}
}
//FAST SEARCH
var vii_search_delay = false;
var vii_search_val = '';
var FSE = {
	Txt: function() {
		var a = $('#query').val();
		if (a.length > 43) {
			tch = '..';
			nVal = a.substring(0, 43);
		} else {
			tch = '';
			nVal = a;
		}
		$('#fast_search_txt').text(nVal + tch);
		0 == a.length ? $(".fast_search_bg").hide() : vii_search_val != a && a != 0 < a.length && (clearInterval(vii_search_delay), vii_search_delay = setInterval(function() {
			FSE.GoSe(a);
		}, 600));
		if (a != 0) $(".fast_search_bg").show();
	},
	GoSe: function(val) {
		clearInterval(vii_search_delay);
		if (val != 0) {
			if ($('#se_type').val() == 1 || $('#se_type').val() == 2 || $('#se_type').val() == 4) {
				$.post('/index.php?go=fast_search', {
					query: val,
					se_type: $('#se_type').val()
				}, function(d) {
					$('#reFastSearch').html(d);
				});
			} else $('#reFastSearch').html('');
		} else {
			$(".fast_search_bg").hide();
			$('#reFastSearch').html('');
		}
		vii_search_val = val;
	},
	ClrHovered: function(id) {
		for (i = 0; i <= 8; i++) {
			$('#all_fast_res_clr' + i).css('background', '#fff');
		}
		$('#' + id).css('background', '#eef3f5');
	}
}
//COMPLAIT / REPORT
var Report = {
	Box: function(act, id) {
		Box.Close();
		if (act == 'photo') lang_report = 'Жалоба на фотографию';
		else if (act == 'video') lang_report = 'Жалоба на видеозапись';
		else if (act == 'note') lang_report = 'Жалоба на заметку';
		else lang_report = '';
		Box.Show('report', 400, lang_report, '<div class="report_pad">Пожалуйста, выберите причину, по которой Вы хотите сообщить администрации сайта об этом материале.<div class="clear"></div><br /><select id="type_report" class="inpst" style="width:212px" onChange="if(this.value > 1) {$(\'#report_comm_block\').show();$(\'#text_report\').focus()} else {$(\'#report_comm_block\').hide();$(\'#text_report\').val(\'\')}"><option value="1">Материал для взрослых</opyion><option value="2">Детская порнография</opyion><option value="3">Эктремизм</opyion><option value="4">Насилие</opyion><option value="5">Пропаганда наркотиков</opyion></select><div class="clear"></div><div id="report_comm_block" class="no_display"><br />Комментарий:<br /><br /><textarea id="text_report" class="inpst" style="width:200px;height:80px"></textarea></div></div>', lang_msg_close, lang_box_send, 'Report.Send(\'' + act + '\', ' + id + ')');
		$('#audio_lnk').focus();
		$('#video_object').hide();
	},
	Send: function(act, id) {
		$('#box_loading').show();
		ge('box_butt_create').disabled = true;
		$.post('/index.php?go=report', {
			act: act,
			id: id,
			type_report: $('#type_report').val(),
			text_report: $('#text_report').val()
		}, function(d) {
			Box.Close();
			Box.Info('yes_report', 'Спасибо', 'Ваша жалоба отправлена администрации сайта и будет рассмотрена в ближайшее время.', 300, 3000);
			$('#video_object').show();
		});
	},
	WallSend: function(act, id) {
		$('#wall_record_' + id).html('<div class="color777">Сообщение помечено как спам.</div>');
		$('#wall_fast_block_' + id).remove();
		$('.js_titleRemove').remove();
		$.post('/index.php?go=report', {
			act: act,
			id: id
		});
	}
}
//REPOST
var Repost = {
	Box: function(rec_id, g_tell) {
		Box.Page('/index.php?go=repost&act=all', 'rec_id=' + rec_id, 'repost', 430, 'Отправка записи', lang_box_canсel, 'Поделиться записью', 'Repost.Send(' + rec_id + ', ' + g_tell + ')', 0, 0, 0, 0, 'comment_repost');
	},
	Send: function(rec_id, g_tell) {
		comm = $('#comment_repost').val();
		type = $('#type_repost').val();
		if (type == 1) cas = 'for_wall';
		else if (type == 2)
			if (g_tell) cas = 'groups_2';
			else cas = 'groups';
		else if (type == 3) cas = 'message';
		else cas = '';
		$('#box_loading').show();
		ge('box_butt_create').disabled = true;
		$.post('/index.php?go=repost&act=' + cas, {
			rec_id: rec_id,
			comm: comm,
			sel_group: $('#sel_group').val(),
			g_tell: g_tell,
			for_user_id: $('#for_user_id').val()
		}, function(d) {
			if (d == 1) {
				$('#box_loading').hide();
				ge('box_butt_create').disabled = false;
				addAllErr(lang_wall_tell_tes);
			} else {
				if (type == 1) Box.Info('yes_report', 'Запись отправлена.', 'Теперь эта запись появится в новостях у Ваших друзей.', 300, 2500);
				if (type == 2) Box.Info('yes_report', 'Запись отправлена.', 'Теперь эта запись появится на странице сообщества.', 300, 2500);
				if (type == 3) Box.Info('yes_report', 'Сообщение отправлено.', 'Ваше сообщение отправлено.', 300, 2500);
				Box.Close();
			}
		});
	}
}
//DOCUMENTS
var Doc = {
	AddAttach: function(name, id) {
		if (!$('#wall_text').val()) wall.form_open();
		$('#attach_files').show();
		attach_id = Math.floor(Math.random() * (1000 - 1 + 1)) + 1;
		Box.Close();
		ln = name.length;
		if (ln > 50) name = name.substring(0, 12) + '..' + name.substring(ln - 4, ln);
		res_attach_id = 'doc_' + attach_id;
		$('#attach_files').append('<div style="padding-bottom:6px;padding-top:6px;display:block;width:100%" id="attach_file_' + res_attach_id + '" class="attach_file" ><div class="doc_attach_ic fl_l"></div><div class="doc_attach_text"><div class="fl_l">' + name + '</div><img class="fl_l cursor_pointer" style="margin-top:2px;margin-left:5px" src="' + template_dir + '/images/close_a.png" onMouseOver="myhtml.title(\'' + res_attach_id + '\', \'' + lang_wall_no_atttach + '\', \'wall_doc_\')" id="wall_doc_' + res_attach_id + '" onClick="wall.attach_delete(\'' + res_attach_id + '\', \'doc|' + id + '||\')" /></div><div class="clear"></div></div><div class="clear"></div>');
		$('#vaLattach_files').val($('#vaLattach_files').val() + 'doc|' + id + '||');
		if ($('.attach_file').length > 9) $('#wall_attach').hide();
	},
	Del: function(did) {
		$('.js_titleRemove').remove();
		$('#doc_block' + did).html('Документ был удалён.');
		updateNum('#upDocNum');
		langNumric('langNumric', $('#upDocNum').text(), 'документ', 'документа', 'документов', 'документ', 'документов');
		$.post('/index.php?go=doc&act=del', {
			did: did
		});
	},
	ShowEdit: function(did, id) {
		$('#' + id + ', #data_doc' + did).hide();
		$('#edit_doc_tab' + did).show();
	},
	CloseEdit: function(did, id) {
		$('#' + id + ', #data_doc' + did).show();
		$('#edit_doc_tab' + did).hide();
	},
	SaveEdit: function(did, id) {
		if ($('#edit_val' + did).val() != 0) {
			$('#edit_doc_name' + did).text($('#edit_val' + did).val());
			$('#' + id + ', #data_doc' + did).show();
			$('#edit_doc_tab' + did).hide();
			$.post('/index.php?go=doc&act=editsave', {
				did: did,
				name: $('#edit_val' + did).val()
			});
		} else setErrorInputMsg('edit_val' + did);
	}
}
//VOTES
var Votes = {
	AddInp: function() {
		$('#answerNum').val(parseInt($('#answerNum').val()) + 1);
		$('#addAnswerInp').append('<div id="div_inp_answr_' + $('#answerNum').val() + '"><div class="texta">&nbsp;</div><input type="text" id="vote_answer_' + $('#answerNum').val() + '" class="inpst vote_answer" maxlength="80" value="" style="width:355px;margin-left:5px" /><div class="mgclr"></div></div>');
		if ($('#answerNum').val() == 10) $('#addNewAnswer').html('добавить');
		if ($('#answerNum').val() > 2) $('#addDelAnswer').html('<a class="cursor_pointer" onClick="Votes.DelInp()">удалить</a>');
		$('#vote_answer_' + $('#answerNum').val()).focus();
	},
	DelInp: function(id) {
		if ($('#answerNum').val() > 2) {
			$('#answerNum').val(parseInt($('#answerNum').val()) - 1);
			$('#div_inp_answr_' + $('.vote_answer:last').attr('id').replace('vote_answer_', '')).remove();
			$('#addNewAnswer').html('<a class="cursor_pointer" onClick="Votes.AddInp()">добавить</a>');
		}
		if ($('#answerNum').val() == 2) $('#addDelAnswer').html('удалить');
	},
	RemoveForAttach: function() {
		$('#vaLattach_files').val($('#vaLattach_files').val().replace('vote|start||', ''));
		$('.js_titleRemove').remove();
		$('#attach_block_vote').hide();
		$('#vote_title, #vote_answer_1, #vote_answer_2').val('');
		$('#addNewAnswer').html('<a class="cursor_pointer" onClick="Votes.AddInp()">добавить</a>');
		$('#addDelAnswer').html('удалить');
		$('#attatch_vote_title').text('');
		$('#answerNum').val('2');
		for (i = 2; i <= 10; i++) $('#div_inp_answr_' + i).remove();
	},
	Send: function(answer_id, vote_id) {
		$('#answer_load' + answer_id).append('<img src="' + template_dir + '/images/loading_mini.gif" style="margin-left:5px" />');
		for (i = 0; i <= 10; i++) $('#wall_vote_oneanswe' + i).attr('onClick', '');
		$.post('/index.php?go=votes', {
			vote_id: vote_id,
			answer_id: answer_id
		}, function(d) {
			$('#result_vote_block' + vote_id).html(d);
		});
	}
}
//FORUM
var at = '';
var Forum = {
	New: function(i) {
		if ($('#title_n').val() != 0) {
			if ($('#text').val() != 0 || $('#vaLattach_files').val() != 0) {
				butloading('forum_sending', 70, 'disabled');
				$.post('/index.php?go=groups_forum&act=new_send', {
					public_id: i,
					title: $('#title_n').val(),
					text: $('#text').val(),
					attach_files: $('#vaLattach_files').val()
				}, function(d) {
					Page.Go('/forum' + i + '?act=view&id=' + d);
				});
			} else setErrorInputMsg('text');
		} else setErrorInputMsg('title_n');
	},
	Page: function(p) {
		if ($('#load_forum_page_lnk').text() == 'Показать больше тем') {
			textLoad('load_forum_page_lnk');
			$.post('/index.php?go=groups_forum&public_id=' + p, {
				a: '1',
				page: page
			}, function(d) {
				page++;
				$('#ForumPage').append(d);
				$('#load_forum_page_lnk').text('Показать больше тем');
				if (!d) {
					$('#' + $('.forum_bg2:last').attr('id')).css('margin-bottom', '-15px');
					$('#forum_page_lnk').hide();
					$('#load_forum_page_lnk').text('');
				}
			});
		}
	},
	SendMsg: function(i) {
		if ($('#fast_text_1').val() != 0) {
			butloading('msg_send', 56, 'disabled');
			$.post('/index.php?go=groups_forum&act=add_msg', {
				fid: i,
				msg: $('#fast_text_1').val(),
				answer_id: $('#answer_comm_id1').val()
			}, function(d) {
				updateNum('#msgNumJS', 1);
				langNumric('langMsg', $('#msgNumJS').text(), 'сообщение', 'сообщения', 'сообщений', 'сообщение', 'сообщение');
				$('#msg').append(d);
				$('#fast_text_1').val('').focus();
				butloading('msg_send', 56, 'enabled', 'Отправить');
				$('#answer_comm_for_1').html('');
				$('#answer_comm_id1').val('');
			});
		} else setErrorInputMsg('fast_text_1');
	},
	MsgPage: function(f) {
		if ($('#load_forum_msg_lnk').text() == 'Показать предыдущие сообщения') {
			textLoad('load_forum_msg_lnk');
			$.post('/index.php?go=groups_forum&act=prev_msg', {
				fid: f,
				first_id: $('.forum_msg_border2:first').attr('id'),
				page: page
			}, function(d) {
				page++;
				$('#msgPrev').html(d + $('#msgPrev').html());
				$('#load_forum_msg_lnk').text('Показать предыдущие сообщения');
				if (!d) {
					$('#load_forum_msg_lnk').text('Скрыть сообщения').css('background', '#fff');
					$('#forum_msg_lnk').attr('onClick', 'Forum.HidePage(' + f + ')');
				}
			});
		}
	},
	HidePage: function(f) {
		$('#forum_msg_lnk').attr('onClick', 'Forum.MsgPage(' + f + ')');
		$('#load_forum_msg_lnk').text('Показать предыдущие сообщения').css('background', 'rgb(233, 237, 241)');
		$('#msgPrev').html('');
		page = 1;
	},
	EditText: function() {
		at = $('#attach').html();
		$('#teckText, #editLnk').hide();
		$('#editTextTab').show();
		$('#editText').focus();
	},
	CloseEdit: function() {
		$('#teckText, #editLnk, #editClose').show();
		$('#editTextTab').hide();
	},
	SaveEdit: function(i) {
		$('#editClose').hide();
		butloading('saveedit', 55, 'disabled');
		$.post('/index.php?go=groups_forum&act=saveedit', {
			text: $('#editText').val(),
			fid: i
		}, function(d) {
			if (!at) at = '';
			$('#teckText').html(d + '<span id="attach">' + at + '</span>');
			Forum.CloseEdit();
			butloading('saveedit', 55, 'enabled', 'Сохранить');
		});
	},
	EditTitle: function() {
		settings.privacyClose('msg');
		$('#titleTeck').hide();
		$('#editTitle').show();
		$('#title').focus();
	},
	CloseEditTitle: function() {
		$('#titleTeck').show();
		$('#editTitle').hide();
	},
	SaveEditTitle: function(f) {
		if ($('#title').val() != 0) {
			Forum.CloseEditTitle();
			$('#editTitleSaved').text($('#title').val());
			$.post('/index.php?go=groups_forum&act=savetitle', {
				fid: f,
				title: $('#title').val()
			});
		} else setErrorInputMsg('title');
	},
	Fix: function(f) {
		settings.privacyClose('msg');
		if ($('#fix_text').text() == 'Закрепить тему') {
			$('#fix_text').text('Не закреплять тему');
			$('.forum_infos_div').html('<b>Тема закреплена.</b><br />Теперь эта тема всегда будет выводиться над остальными в списке обсуждений.').fadeIn('fast');
		} else {
			$('#fix_text').text('Закрепить тему');
			$('.forum_infos_div').html('<b>Тема больше не закреплена.</b><br />Эта тема будет выводиться на своем месте в списке обсуждений.').fadeIn('fast');
		}
		$.post('/index.php?go=groups_forum&act=fix', {
			fid: f
		});
	},
	Status: function(f) {
		settings.privacyClose('msg');
		if ($('#status_text').text() == 'Закрыть тему') {
			$('#status_text').text('Открыть тему');
			$('.forum_infos_div').html('<b>Тема закрыта.</b><br />Участники сообщества больше не смогут оставлять сообщения в этой теме.').fadeIn('fast');
			$('.forum_addmsgbg').hide();
		} else {
			$('#status_text').text('Закрыть тему');
			$('.forum_infos_div').html('<b>Тема открыта.</b><br />Все участники сообщества смогут оставлять сообщения в этой теме.').fadeIn('fast');
			$('.forum_addmsgbg').show();
		}
		$.post('/index.php?go=groups_forum&act=status', {
			fid: f
		});
	},
	DelBox: function(f, p) {
		settings.privacyClose('msg');
		Box.Show('del_forthe', 350, lang_title_del_photo, '<div style="padding:15px;" id="del_status_text_forum">Вы уверены, что хотите удалить эту тему?</div>', lang_box_canсel, lang_box_yes, 'Forum.StartDelete(' + f + ', ' + p + ')');
	},
	StartDelete: function(f, p) {
		$('#box_loading').show();
		ge('box_butt_create').disabled = true;
		$('#del_status_text_forum').text('Тема удаляется..');
		$.post('/index.php?go=groups_forum&act=del', {
			fid: f
		}, function(d) {
			Page.Go('/forum' + p);
		});
	},
	DelMsg: function(i) {
		$('#' + i).html('<span class="color777">Сообщение удалено.</span>');
		updateNum('#msgNumJS');
		langNumric('langMsg', $('#msgNumJS').text(), 'сообщение', 'сообщения', 'сообщений', 'сообщение', 'сообщение');
		$.post('/index.php?go=groups_forum&act=delmsg', {
			mid: i
		});
	},
	CreateVote: function(f) {
		if ($('#vote_title').val() != 0) {
			if ($('#vote_answer_1').val() != 0) {
				butloading('savevote', 75, 'disabled', '');
				$.post('/index.php?go=groups_forum&act=createvote', {
					fid: f,
					vote_title: $('#vote_title').val(),
					vote_answer_1: $('#vote_answer_1').val(),
					vote_answer_2: $('#vote_answer_2').val(),
					vote_answer_3: $('#vote_answer_3').val(),
					vote_answer_4: $('#vote_answer_4').val(),
					vote_answer_5: $('#vote_answer_5').val(),
					vote_answer_6: $('#vote_answer_6').val(),
					vote_answer_7: $('#vote_answer_7').val(),
					vote_answer_8: $('#vote_answer_8').val(),
					vote_answer_9: $('#vote_answer_9').val(),
					vote_answer_10: $('#vote_answer_10').val()
				}, function(d) {
					Page.Go(location.href);
				});
			} else setErrorInputMsg('vote_answer_1');
		} else setErrorInputMsg('vote_title');
	},
	RemoveForAttach: function() {
		$('#attach_block_vote').hide();
		$('#vote_title, #vote_answer_1, #vote_answer_2').val('');
		$('#addNewAnswer').html('<a class="cursor_pointer" onClick="Votes.AddInp()">добавить</a>');
		$('#addDelAnswer').html('удалить');
		$('#attatch_vote_title').text('');
		$('#answerNum').val('2');
		for (i = 2; i <= 10; i++) $('#div_inp_answr_' + i).remove();
	},
	VoteDelBox: function(f) {
		Box.Show('del_forthe', 350, lang_title_del_photo, '<div style="padding:15px;" id="del_status_text_forum">Вы уверены, что хотите удалить опрос?</div>', lang_box_canсel, lang_box_yes, 'Forum.StartVoteDelete(' + f + ')');
	},
	StartVoteDelete: function(f) {
		Box.Close();
		$('#voteblockk').hide();
		$('#votelnk').html('<div class="sett_hover" onClick="settings.privacyClose(\'msg\'); $(\'#attach_block_vote\').slideDown(100); $(\'#vote_title\').focus()">Прикрепить опрос</div>');
		$.post('/index.php?go=groups_forum&act=delvote', {
			fid: f
		});
	}
}
//ATTACH COMM
var attach = {
	addcomm: function(purl, purl_js) {
		if ($('#textcom' + purl_js).val() != 0) {
			butloading('add_comm' + purl_js, '56', 'disabled', '');
			$.post('/index.php?go=attach_comm&act=addcomm', {
				purl: purl,
				text: $('#textcom' + purl_js).val()
			}, function(d) {
				butloading('add_comm' + purl_js, '56', 'enabled', lang_box_send);
				$('#pcomments').append(d);
				$('#textcom' + purl_js).val('').focus();
			});
		} else setErrorInputMsg('textcom' + purl_js);
	},
	delet_comm: function(i, p) {
		$('#comment_' + i).html('<div class="color777" style="margin-bottom:5px">Комментарий удалён.</div>');
		$.post('/index.php?go=attach_comm&act=delcomm', {
			id: i,
			purl: p
		});
	},
	page: function(p) {
		if ($('#load_attach_comm_msg_lnk').text() == 'Показать предыдущие комментарии') {
			textLoad('load_attach_comm_msg_lnk');
			$.post('/index.php?go=attach_comm&act=prevcomm', {
				purl: p,
				first_id: $('.attach_comm_photo:first').attr('id').replace('comment_', ''),
				page: page
			}, function(d) {
				page++;
				$('#attachcommPrev').html(d + $('#attachcommPrev').html());
				$('#load_attach_comm_msg_lnk').text('Показать предыдущие комментарии');
				if (!d) {
					$('#load_attach_comm_msg_lnk').text('Скрыть комментарии').css('background', '#fff');
					$('#attach_comm_msg_lnk').attr('onClick', 'attach.hide_page(\'' + p + '\')');
				}
			});
		}
	},
	hide_page: function(f) {
		$('#attach_comm_msg_lnk').attr('onClick', 'attach.page(\'' + f + '\')');
		$('#load_attach_comm_msg_lnk').text('Показать предыдущие комментарии').css('background', 'rgb(233, 237, 241)');
		$('#attachcommPrev').html('');
		page = 1;
	},
}
//PHOTO EDITOR
var photoeditor = {
	start: function(img, id, h) {
		var height = parseInt(h) + 180;
		$('#ladybug_ant' + id).hide();
		$('#frameedito' + id).html('<iframe src="http://pixlr.com/express/?s=c&image=' + escape(img) + '&title=photo&target=' + escape('http://' + location.host + '/index.php?go=photo_editor&pid=' + id) + '&exit=' + escape('http://' + location.host + '/index.php?go=photo_editor&act=close&image=' + img) + '" width="770" height="' + height + '" frameborder="0"></iframe>');
	}
}
//APPS
var vii_apps_search_delay = false;
var vii_apps_search_val = '';
var apps_preload = true;
var apps = {
	gSearch: function() {
		var a = $('#query_games').val();
		if (!a) {
			$('#apps_all').show();
			$('#apps_search').hide();
			$('#apps_se_load').fadeOut(100);
			apps_preload = true;
		}
		if (a != 0) {
			apps_preload = false;
			$('#apps_se_load').fadeIn('fast');
			0 == vii_apps_search_val != a && a != 0 < a.length && (clearInterval(vii_apps_search_delay), vii_apps_search_delay = setInterval(function() {
				apps.xSearch();
			}, 600));
		}
	},
	xSearch: function() {
		clearInterval(vii_apps_search_delay);
		var a = $('#query_games').val();
		$.post('/index.php?go=apps&act=search', {
			query_games: a
		}, function(d) {
			if (a != 0) {
				$('#apps_all').hide();
				$('#apps_search').show();
				$('#apps_se_load').fadeOut(100);
				$('#apps_search_res').html(d);
				$(".apps_last:last").css('border', '0px');
			} else {
				$('#apps_all').show();
				$('#apps_search').hide();
				$('#apps_se_load').fadeOut(100);
			}
		});
	},
	xSearchMore: function() {
		if ($('#apps_text_load_search').text() == 'Показать больше приложений') {
			textLoad('apps_text_load_search');
			var lastid = $(".apps_last:last").attr('id');
			var a = $('#query_games').val();
			$.post('/index.php?go=apps&act=search', {
				lastid: lastid,
				query_games: a
			}, function(d) {
				$('#apps_search_pages').append(d);
				$(".apps_last:last").css('border', '0px');
				$('#apps_text_load_search').text('Показать больше приложений');
				if (!d) $('.apps_but3').remove();
			});
		}
	},
	showMore: function() {
		if ($('#apps_text_load').text() == 'Показать больше приложений' && apps_preload) {
			textLoad('apps_text_load');
			$.post('/index.php?go=apps', {
				doload: 1,
				page_cnt: page_cnt_app
			}, function(d) {
				page_cnt_app++;
				row = d.split('||');
				$('#apps_pop').append(row[0]);
				$('#apps_new').append(row[1]);
				$('#apps_text_load').text('Показать больше приложений');
				if (!row[0] && !row[1]) $('.apps_but2').remove();
			});
		}
	},
	showMoreOld: function() {
		if ($('#apps_text_load_old').text() == 'Показать больше приложений') {
			textLoad('apps_text_load_old');
			$.post('/index.php?go=apps', {
				doload: 2,
				page_cnt_old: page_cnt_app_old
			}, function(d) {
				page_cnt_app_old++;
				row = d.split('||');
				$('#apps_my_games').append(row[0]);
				$('#apps_activity').append(row[1]);
				$('#apps_text_load_old').text('Показать больше приложений');
				if (!row[0] && !row[1]) $('.apps_but').remove();
			});
		}
	},
	view: function(a, h, c) {
		history.pushState({
			link: h
		}, null, h);
		viiBox.start();
		$.post('/index.php?go=apps&act=view', {
			id: a
		}, function(d) {
			viiBox.win('ap', d, 1, c);
		});
	},
	gallery: function(i) {
		$('.apps_mini_img img').css('opacity', 0.5);
		$('#apmpos' + i).css('opacity', 1);
		if (i == 2) $('.apps_inimgs').animate({
			'margin-left': '-611'
		}, 450);
		else if (i == 3) $('.apps_inimgs').animate({
			'margin-left': '-1221'
		}, 450);
		else if (i == 4) $('.apps_inimgs').animate({
			'margin-left': '-1832'
		}, 450);
		else $('.apps_inimgs').animate({
			'margin-left': '0'
		}, 450);
	},
	mydel: function(i, t) {
		if (t) {
			$('.js_titleRemove').remove();
			$('#app' + i).html('<div align="center" style="color:#777;height:40px;padding-top:10px">Игра успешно удалена.</div>');
		} else $('#apps_rdel_txt').html('<div class="fl_r" style="color:#777;margin-top:6px;font-weight:normal">Игра удалена из списках ваших игр</div>');
		$.post('/index.php?go=apps&act=mydel', {
			id: i
		});
	}
}
//COVER
var cover = {
	init: function(i, hi) {
		$('#cover_img').attr('src', i);
		$("#les10_ex2").draggable({
			axis: 'y',
			stop: function() {
				$('.cover_addut, .cover_descring').show();
			},
			drag: function(event, ui) {
				var d = ui.position.top;
				$('.cover_addut, .cover_descring').hide();
				if (d >= 0) {
					$("#les10_ex2").remove();
					$('#cover_restart').html('<div style="width:800px;height:' + hi + 'px;position:relative;top:0px;z-index:1" id="les10_ex2"><img src="' + i + '" width="800" id="cover_img" /></div>');
					$('.cover_addut, .cover_descring').show();
					cover.init(i, hi);
				}
				h = parseInt('-' + (hi - 230));
				if (d <= h) {
					$("#les10_ex2").remove();
					$('#cover_restart').html('<div style="width:800px;height:' + hi + 'px;position:relative;top:' + h + 'px;z-index:1" id="les10_ex2"><img src="' + i + '" width="800" id="cover_img" /></div>');
					$('.cover_addut, .cover_descring').show();
					cover.init(i, hi);
				}
			}
		});
	},
	del: function(public_id) {
		$('.cover_descring, .cover_addut').hide();
		$('#upload_cover').show().text('Добавить обложку');
		$('.cover_newpos').css('margin-left', '430px');
		$('#cover_img').attr('src', '');
		$('.cover_loaddef_bg').css('cursor', 'default').hide();
		$('#cover_restart').html('');
		$("#les10_ex2").draggable('destroy');
		if (public_id) $.post('/index.php?go=groups&act=delcover&id=' + public_id);
		else $.post('/index.php?go=editprofile&act=delcover');
	},
	save: function(public_id) {
		cover.cancel();
		t = $("#les10_ex2").attr('style').split('top:');
		s = t[1].split('px');
		s[0] = s[0].replace('-', '');
		if (public_id) $.post('/index.php?go=groups&act=savecoverpos&id=' + public_id, {
			pos: s[0]
		});
		else $.post('/index.php?go=editprofile&act=savecoverpos', {
			pos: s[0]
		});
	},
	cancel: function(t) {
		$('.cover_descring, .cover_addut').hide();
		$('.cover_addut_edit').show();
		$('.cover_newpos').css('margin-left', '397px');
		$('.cover_loaddef_bg').css('cursor', 'default');
		$("#les10_ex2").draggable('destroy');
		if (t) $("#les10_ex2").css('top', '-' + t + 'px');
	},
	startedit: function(i, h, public_id) {
		$('#upload_cover').show().text('Изменить фото');
		$('.cover_descring, .cover_addut').show();
		$('.cover_newpos').css('margin-left', '197px');
		$('.cover_addut_edit').hide();
		$('.cover_loaddef_bg').css('cursor', 'move');
		cover.init(i, h);
	}
}
//REVIEWS
var reviews = {
	send: function() {
		var text = $('#text').val().replace('Введите текст Вашего отзыва..', '');
		if (text != 0) {
			butloading('sending', 83, 'disabled');
			$('#ok_send').hide();
			$.post('/index.php?go=reviews&act=send', {
				text: text
			}, function() {
				$('#ok_send').show();
				$('#text').val('');
				butloading('sending', 83, 'enabled', 'Оставить отзыв');
			});
		} else setErrorInputMsg('text');
	}
}
var imRoom = {
	createBox: function(el) {
		var el = $(el);
		el.attr('disabled', true);
		viiBox.start();
		$.post('/index.php?go=im&act=createRoomBox', function(d) {
			el.removeAttr('disabled');
			viiBox.win('createRoom', d);
		});
	},
	inviteBox: function(el, room_id) {
		var el = $(el);
		el.attr('disabled', true);
		viiBox.start();
		$.post('/index.php?go=im&act=inviteToRoomBox', {
			room_id: room_id.substr(1)
		}, function(d) {
			el.removeAttr('disabled');
			viiBox.win('inviteToRoom', d);
		});
	},
	viewRoom: function(el, room_id) {
		var el = $(el);
		el.attr('disabled', true);
		viiBox.start();
		$.post('/index.php?go=im&act=viewRoomBox', {
			room_id: room_id.substr(1)
		}, function(d) {
			el.removeAttr('disabled');
			viiBox.win('viewRoom', d);
		});
	},
	saveName: function(el, room_id) {
		var el = $(el),
			box = el.closest('.miniature_box'),
			title = box.find('input:eq(0)');
		if (!title.val()) return title.focus();
		el.attr('disabled', true);
		$.post('/index.php?go=im&act=saveRoomName', {
			title: title.val(),
			room_id: room_id
		}, function(d) {
			el.removeAttr('disabled');
			var d = $.parseJSON(d);
			if (d.response) {
				Page.Go('/messages#c' + room_id);
			} else if (d.error) alert(d.error);
		});
	},
	uploadAvatar: function(el, room_id) {
		var file = el.files[0];
		if (!file) return;
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function(e) {
			if (e.target.readyState == 4) {
				if (e.target.status == 200) {
					var d = $.parseJSON(xhr.responseText);
					if (d.response) Page.Go('/messages#c' + room_id);
					else if (d.error) alert(d.error);
				}
			}
		};
		xhr.open("POST", '/index.php?go=im&act=uploadRoomAvatar&room_id=' + room_id, true);
		var form = new FormData();
		form.append('uploadfile', file);
		xhr.send(form);
	},
	exclude: function(el, room_id) {
		var el = $(el),
			item = el.closest('[data-id]'),
			id = item.attr('data-id');
		el.attr('disabled', true);
		$.post('/index.php?go=im&act=exclude', {
			id: id,
			room_id: room_id
		}, function(d) {
			el.removeAttr('disabled');
			var d = $.parseJSON(d);
			if (d.response) {
				item.remove();
			} else if (d.error) alert(d.error);
		});
	},
	toogleItem: function(el) {
		var el = $(el).parent(),
			box = el.closest('.miniature_box');
		var limit = 100;
		if (!el.hasClass('grIntiveUserActive')) {
			if (box.find('.grIntiveUserActive').length >= limit) return Box.Info('load_photo_er', 'Информация', 'Вы не можете пригласить более ' + limit + ' друзей.', 380, 3000);
			el.addClass('grIntiveUserActive');
		} else el.removeClass('grIntiveUserActive');
	},
	create: function(el) {
		var el = $(el),
			box = el.closest('.miniature_box'),
			items = box.find('.grIntiveUserActive'),
			title = box.find('input:eq(0)'),
			user_ids = [];
		if (title.val()) {
			$.when($.each(items, function(k, v) {
				user_ids.push($(v).find('[data-id]').attr('data-id'));
			})).then(function() {
				el.attr('disabled', true);
				$.post('/index.php?go=im&act=createRoom', {
					title: title.val(),
					user_ids: user_ids
				}, function(d) {
					el.removeAttr('disabled');
					var d = $.parseJSON(d);
					if (d.response) {
						viiBox.clos('createRoom', 1);
						Page.Go('/messages#c' + d.response);
					} else if (d.error) alert(d.error);
				});
			});
		} else title.focus();
	},
	invite: function(el, room_id) {
		var el = $(el),
			box = el.closest('.miniature_box'),
			items = box.find('.grIntiveUserActive'),
			user_ids = [];
		$.when($.each(items, function(k, v) {
			user_ids.push($(v).find('[data-id]').attr('data-id'));
		})).then(function() {
			el.attr('disabled', true);
			$.post('/index.php?go=im&act=inviteToRoom', {
				room_id: room_id,
				user_ids: user_ids
			}, function(d) {
				el.removeAttr('disabled');
				var d = $.parseJSON(d);
				if (d.response) {
					viiBox.clos('inviteToRoom', 1);
					Page.Go('/messages#c' + d.response);
				} else if (d.error) alert(d.error);
			});
		});
	},
	exit: function(el, room_id) {
		var el = $(el);
		el.attr('disabled', true);
		$.post('/index.php?go=im&act=exitFromRoom', {
			room_id: room_id.substr(1)
		}, function(d) {
			el.removeAttr('disabled');
			var d = $.parseJSON(d);
			if (d.response) {
				Page.Go('/messages');
			} else if (d.error) alert(d.error);
		});
	}
}