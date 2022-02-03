var uagent = navigator.userAgent.toLowerCase();
var is_safari = ((uagent.indexOf('safari') != -1) || (navigator.vendor == "Apple Computer, Inc."));
var is_ie = ((uagent.indexOf('msie') != -1) && (!is_opera) && (!is_safari) && (!is_webtv));
var is_ie4 = ((is_ie) && (uagent.indexOf("msie 4.") != -1));
var is_moz = (navigator.product == 'Gecko');
var is_ns = ((uagent.indexOf('compatible') == -1) && (uagent.indexOf('mozilla') != -1) && (!is_opera) && (!is_webtv) && (!is_safari));
var is_ns4 = ((is_ns) && (parseInt(navigator.appVersion) == 4));
var is_opera = (uagent.indexOf('opera') != -1);
var is_kon = (uagent.indexOf('konqueror') != -1);
var is_webtv = (uagent.indexOf('webtv') != -1);
var is_win = ((uagent.indexOf("win") != -1) || (uagent.indexOf("16bit") != -1));
var is_mac = ((uagent.indexOf("mac") != -1) || (navigator.vendor == "Apple Computer, Inc."));
var is_chrome = (uagent.match(/Chrome\/\w+\.\w+/i));
if (is_chrome == 'null' || !is_chrome || is_chrome == 0) is_chrome = '';
var ua_vers = parseInt(navigator.appVersion);
var req_href = location.href;
var vii_interval = false;
var vii_interval_im = false;
var scrollTopForFirefox = 0;
var url_next_id = 1;
$(document).ready(function() {
	var mw = ($('html, body').width() - 800) / 2;
	if ($('.autowr').css('padding-left', mw + 'px').css('padding-right', mw + 'px')) {
		$('body').show();
		history.pushState({
			link: location.href
		}, '', location.href);
	}
	$('.update_code').click(function() {
		var rndval = new Date().getTime();
		$('#sec_code').html('<img src="/antibot/antibot.php?rndval=' + rndval + '" alt="" title="Показать другой код" width="120" height="50" />');
		return false;
	});
	$(window).scroll(function() {
		if ($(document).scrollTop() > ($(window).height() / 2)) $('.scroll_fix_bg').fadeIn(200);
		else $('.scroll_fix_bg').fadeOut(200);
	});
});
if (CheckRequestPhoto(req_href)) {
	$(document).ready(function() {
		Photo.Show(req_href);
	});
}
if (CheckRequestVideo(req_href)) {
	$(document).ready(function() {
		var video_id = req_href.split('_');
		var section = req_href.split('sec=');
		var fuser = req_href.split('wall/fuser=');
		if (fuser[1]) var close_link = '/u' + fuser[1];
		else var close_link = '';
		if (section[1]) {
			var xSection = section[1].split('/');
			if (xSection[0] == 'news') var close_link = 'news';
			if (xSection[0] == 'msg') {
				var msg_id = xSection[1].split('id=');
				var close_link = '/messages/show/' + msg_id[1];
			}
		}
		videos.show(video_id[1], req_href, close_link);
	});
}
//AJAX PAGES
window.onload = function() {
	window.setTimeout(function() {
		window.addEventListener("popstate", function(e) {
			e.preventDefault();
			if (CheckRequestPhoto(e.state.link)) Photo.Prev(e.state.link);
			else if (CheckRequestVideo(e.state.link)) videos.prev(e.state.link);
			else Page.Prev(e.state.link);
		}, false);
	}, 1);
}

function CheckRequestPhoto(request) {
	var pattern = new RegExp(/photo[0-9]/i);
	return pattern.test(request);
}

function CheckRequestVideo(request) {
	var pattern = new RegExp(/video[0-9]/i);
	return pattern.test(request);
}

function onBodyResize() {
	var mw = ($('html, body').width() - 800) / 2;
	$('.autowr').css('padding-left', mw + 'px').css('padding-right', mw + 'px');
}
var Page = {
	Loading: function(f) {
		var top_pad = $(window).height() / 2 - 50;
		if (f == 'start') {
			$('#loading').remove();
			$('html, body').append('<div id="loading" style="margin-top:' + top_pad + 'px"><div class="loadstyle"></div></div>');
			$('#loading').show();
		}
		if (f == 'stop') {
			$('#loading').remove();
		}
	},
	Go: function(h) {
		hh = (location.href).replace('http://' + location.host + '/', '');
		if (hh == 'my_stats' || ((hh).substring(0, 5)) == 'stats') {
			window.location.href = h;
			return false;
		}
		history.pushState({
			link: h
		}, null, h);
		$('.js_titleRemove, .vii_box').remove();
		clearInterval(vii_interval);
		clearInterval(vii_interval_im);
		Page.Loading('start');
		$('#page').load(h, {
			ajax: 'yes'
		}, function(data) {
			Page.Loading('stop');
			$('html, body').scrollTop(0);
			//$('.ladybug_ant').imgAreaSelect({remove: true});
			//Чистим стили AuroResizeWall
			$('#addStyleClass').remove();
			//Удаляем кеш фоток, видео, модальных окон
			$('.photo_view, .box_pos, .box_info, .video_view').remove();
			//Возвращаем scroll
			$('html, body').css('overflow-y', 'auto');
			//Возвращаем дизайн плеера
			if ($('.staticPlbg').length) {
				$('.staticPlbg').css('margin-top', '-500px');
				player.reestablish();
			}
		}).css('min-height', '0px');
	},
	Prev: function(h) {
		clearInterval(vii_interval);
		clearInterval(vii_interval_im);
		$('.vii_box').remove();
		Page.Loading('start');
		$('#page').load(h, {
			ajax: 'yes'
		}, function(data) {
			Page.Loading('stop');
			$('html, body').scrollTop(0);
			//Чистим стили AuroResizeWall
			$('#addStyleClass').remove();
			//$('.ladybug_ant').imgAreaSelect({remove: true});
			//Удаляем кеш фоток, видео, модальных окон
			$('.photo_view, .box_pos, .box_info, .video_view').remove();
			//Возвращаем scroll
			$('html, body').css('overflow-y', 'auto');
			//Возвращаем дизайн плеера
			if ($('.staticPlbg').length) {
				$('.staticPlbg').css('margin-top', '-500px');
				player.reestablish();
			}
		}).css('min-height', '0px');
	}
}
//PROFILE FUNC
var Profile = {
	miniature: function() {
		Page.Loading('start');
		$.post('/index.php?go=editprofile&act=miniature', function(d) {
			Page.Loading('stop');
			if (d == 1) addAllErr('Вы пока что не загрузили фотографию.');
			else {
				if (is_moz && !is_chrome) scrollTopForFirefox = $(window).scrollTop();
				$('html, body').css('overflow-y', 'hidden');
				if (is_moz && !is_chrome) $(window).scrollTop(scrollTopForFirefox);
				$('body').append('<div id="newbox_miniature">' + d + '</div>');
			}
			$(window).keydown(function(event) {
				if (event.keyCode == 27) Profile.miniatureClose();
			});
		});
	},
	preview: function(img, selection) {
		if (!selection.width || !selection.height) return;
		var scaleX = 100 / selection.width;
		var scaleY = 100 / selection.height;
		var scaleX50 = 50 / selection.width;
		var scaleY50 = 50 / selection.height;
		$('#miniature_crop_100 img').css({
			width: Math.round(scaleX * $('#miniature_crop').width()),
			height: Math.round(scaleY * $('#miniature_crop').height()),
			marginLeft: -Math.round(scaleX * selection.x1),
			marginTop: -Math.round(scaleY * selection.y1)
		});
		$('#miniature_crop_50 img').css({
			width: Math.round(scaleX50 * $('#miniature_crop').width()),
			height: Math.round(scaleY50 * $('#miniature_crop').height()),
			marginLeft: -Math.round(scaleX50 * selection.x1),
			marginTop: -Math.round(scaleY50 * selection.y1)
		});
	},
	miniatureSave: function() {
		var i_left = $('#mi_left').val();
		var i_top = $('#mi_top').val();
		var i_width = $('#mi_width').val();
		var i_height = $('#mi_height').val();
		butloading('miniatureSave', '111', 'disabled', '');
		$.post('/index.php?go=editprofile&act=miniature_save', {
			i_left: i_left,
			i_top: i_top,
			i_width: i_width,
			i_height: i_height
		}, function(d) {
			if (d == 'err') addAllErr('Ошибка');
			else window.location.href = '/u' + d;
			butloading('miniatureSave', '111', 'enabled', 'Сохранить изменения');
		});
	},
	miniatureClose: function() {
		//$('#miniature_crop').imgAreaSelect({remove: true});
		$('#newbox_miniature').remove();
		$('html, body').css('overflow-y', 'auto');
	},
	LoadCity: function(id) {
		$('#load_mini').show();
		if (id > 0) {
			$('#city').slideDown();
			$('#select_city').load('/index.php?go=loadcity', {
				country: id
			});
		} else {
			$('#city').slideUp();
			$('#load_mini').hide();
		}
	},
	//MAIN PHOTOS
	LoadPhoto: function() {
		Page.Loading('start');
		$.get('/index.php?go=editprofile&act=load_photo', function(data) {
			Box.Show('photo', 400, lang_title_load_photo, data, lang_box_canсel);
			Page.Loading('stop');
		});
	},
	DelPhoto: function() {
		Box.Show('del_photo', 400, lang_title_del_photo, '<div style="padding:15px;">' + lang_del_photo + '</div>', lang_box_canсel, lang_box_yes, 'Profile.StartDelPhoto(); return false;');
	},
	StartDelPhoto: function() {
		$('#box_loading').show();
		$.get('/index.php?go=editprofile&act=del_photo', function() {
			$('#ava').html('<img src="/templates/Default/images/no_ava.gif" alt="" />');
			$('#del_pho_but').hide();
			Box.Close('del_photo');
			Page.Loading('stop');
		});
	},
	MoreInfo: function() {
		$('#moreInfo').show();
		$('#moreInfoText').text('Скрыть подробную информацию');
		$('#moreInfoLnk').attr('onClick', 'Profile.HideInfo()');
	},
	HideInfo: function() {
		$('#moreInfo').hide();
		$('#moreInfoText').text('Показать подробную информацию');
		$('#moreInfoLnk').attr('onClick', 'Profile.MoreInfo()');
	}
}
//VII BOX
var viiBox = {
	start: function() {
		Page.Loading('start');
	},
	stop: function() {
		Page.Loading('stop');
	},
	win: function(i, d, o, h) {
		viiBox.stop();
		if (is_moz && !is_chrome) scrollTopForFirefox = $(window).scrollTop();
		$('html, body').css('overflow-y', 'hidden');
		if (is_moz && !is_chrome) $(window).scrollTop(scrollTopForFirefox);
		$('body').append('<div class="vii_box" id="newbox_miniature' + i + '">' + d + '</div>');
		$(window).keydown(function(event) {
			if (event.keyCode == 27) viiBox.clos(i, o, h);
		});
	},
	clos: function(i, o, h) {
		$('#newbox_miniature' + i).remove();
		if (o) $('html, body').css('overflow-y', 'auto');
		if (h) history.pushState({
			link: h
		}, null, h);
	}
}
//MODAL BOX
var Box = {
	Page: function(url, data, name, width, title, cancel_text, func_text, func, height, overflow, bg_show, bg_show_bottom, input_focus, cache) {
		//url - ссылка которую будем загружать
		//data - POST данные
		//name - id окна
		//width - ширина окна
		//title - заголовк окна
		//content - контент окна
		//close_text - текст закрытия
		//func_text - текст который будет выполнять функцию
		//func - функция текста "func_text"
		//height - высота окна
		//overflow - постоянный скролл
		//bg_show - тень внтури окна сверху
		//bg_show_bottom - "1" - с тенью внтури, "0" - без тени внутри
		//input_focus - ИД текстового поля на котором будет фиксация
		//cache - "1" - кешировоть, "0" - не кешировать
		if (cache)
			if (ge('box_' + name)) {
				Box.Close(name, cache);
				$('#box_' + name).show();
				$('#box_content_' + name).scrollTop(0);
				if (is_moz && !is_chrome) scrollTopForFirefox = $(window).scrollTop();
				$('html').css('overflow', 'hidden');
				if (is_moz && !is_chrome) $(window).scrollTop(scrollTopForFirefox);
				return false;
			}
		Page.Loading('start');
		$.post(url, data, function(html) {
			if (!CheckRequestVideo(location.href)) Box.Close(name, cache);
			Box.Show(name, width, title, html, cancel_text, func_text, func, height, overflow, bg_show, bg_show_bottom, cache);
			Page.Loading('stop');
			if (input_focus) $('#' + input_focus).focus();
		});
	},
	Show: function(name, width, title, content, close_text, func_text, func, height, overflow, bg_show, bg_show_bottom, cache) {
		//name - id окна
		//width - ширина окна
		//title - заголовк окна
		//content - контент окна
		//close_text - текст закрытия
		//func_text - текст который будет выполнять функцию
		//func - функция текста "func_text"
		//height - высота окна
		//overflow - постоянный скролл
		//bg_show - тень внтури окна сверху
		//bg_show_bottom - тень внтури внтури снизу
		//cache - "1" - кешировоть, "0" - не кешировать
		if (func_text) var func_but = '<div class="button_div fl_r" style="margin-right:10px;" id="box_but"><button onClick="' + func + '" id="box_butt_create">' + func_text + '</button></div>';
		else var func_but = '';
		var close_but = '<div class="button_div_gray fl_r"><button onClick="Box.Close(\'' + name + '\', ' + cache + '); return false;">' + close_text + '</button></div>';
		var box_loading = '<img id="box_loading" style="display:none;padding-top:8px;padding-left:5px;" src="/templates/Default/images/loading_mini.gif" alt="" />';
		if (height) var top_pad = ($(window).height() - 150 - height) / 2;
		if (top_pad < 0) top_pad = 100;
		if (overflow) var overflow = 'overflow-y:scroll;';
		else var overflow = '';
		if (bg_show)
			if (overflow) var bg_show = '<div class="bg_show" style="width:' + (width - 19) + 'px;"></div>';
			else var bg_show = '<div class="bg_show" style="width:' + (width - 2) + 'px;"></div>';
		else var bg_show = '';
		if (bg_show_bottom)
			if (overflow) var bg_show_bottom = '<div class="bg_show_bottom" style="width:' + (width - 17) + 'px;"></div>';
			else var bg_show_bottom = '<div class="bg_show_bottom" style="width:' + (width - 2) + 'px;"></div>';
		else var bg_show_bottom = '';
		if (height) var sheight = 'height:' + height + 'px';
		else var sheight = '';
		$('body').append('<div id="modal_box"><div id="box_' + name + '" class="box_pos"><div class="box_bg" style="width:' + width + 'px;margin-top:' + top_pad + 'px;"><div class="box_title" id="box_title_' + name + '">' + title + '<div class="box_close" onClick="Box.Close(\'' + name + '\', ' + cache + '); return false;"></div></div><div class="box_conetnt" id="box_content_' + name + '" style="' + sheight + ';' + overflow + '">' + bg_show + content + '<div class="clear"></div></div>' + bg_show_bottom + '<div class="box_footer"><div id="box_bottom_left_text" class="fl_l">' + box_loading + '</div>' + close_but + func_but + '</div></div></div></div>');
		$('#box_' + name).show();
		if (is_moz && !is_chrome) scrollTopForFirefox = $(window).scrollTop();
		$('html').css('overflow', 'hidden');
		if (is_moz && !is_chrome) $(window).scrollTop(scrollTopForFirefox);
		$(window).keydown(function(event) {
			if (event.keyCode == 27) {
				Box.Close(name, cache);
			}
		});
	},
	Close: function(name, cache) {
		if (!cache) $('.box_pos').remove();
		else $('.box_pos').hide();
		if (CheckRequestVideo(location.href) == false && CheckRequestPhoto(location.href) == false) $('html, body').css('overflow-y', 'auto');
		if (CheckRequestVideo(location.href)) $('#video_object').show();
		if (is_moz && !is_chrome) $(window).scrollTop(scrollTopForFirefox);
	},
	GeneralClose: function() {
		$('#modal_box').hide();
	},
	Info: function(bid, title, content, width, tout) {
		var top_pad = ($(window).height() - 115) / 2;
		$('body').append('<div id="' + bid + '" class="box_info"><div class="box_info_margin" style="width: ' + width + 'px; margin-top: ' + top_pad + 'px"><b><span>' + title + '</span></b><br /><br />' + content + '</div></div>');
		$(bid).show();
		if (!tout) var tout = 1400;
		setTimeout("Box.InfoClose()", tout);
		$(window).keydown(function(event) {
			if (event.keyCode == 27) {
				Box.InfoClose();
			}
		});
	},
	InfoClose: function() {
		$('.box_info').fadeOut();
	}
}

function ge(i) {
	return document.getElementById(i);
}

function butloading(i, w, d, t) {
	if (d == 'disabled') {
		$('#' + i).html('<div style="width:' + w + 'px;text-align:center;"><img src="/templates/Default/images/loading_mini.gif" alt="" /></div>');
		ge(i).disabled = true;
	} else {
		$('#' + i).html(t);
		ge(i).disabled = false;
	}
}

function textLoad(i) {
	$('#' + i).html('<img src="/templates/Default/images/loading_mini.gif" alt="" />').attr('onClick', '').attr('href', '#');
}

function updateNum(i, type) {
	if (type) $(i).text(parseInt($(i).text()) + 1);
	else $(i).text($(i).text() - 1);
}

function setErrorInputMsg(i) {
	$("#" + i).css('background', '#ffefef');
	$("#" + i).focus();
	setTimeout("$('#" + i + "').css('background', '#fff').focus()", 700);
}

function addAllErr(text, tim) {
	if (!tim) var tim = 2500;
	$('.privacy_err').remove();
	$('body').append('<div class="privacy_err no_display">' + text + '</div>');
	$('.privacy_err').fadeIn('fast');
	setTimeout("$('.privacy_err').fadeOut('fast')", tim);
}

function langNumric(id, num, text1, text2, text3, text4, text5) {
	strlen_num = num.length;
	if (num <= 21) {
		numres = num;
	} else if (strlen_num == 2) {
		parsnum = num.substring(1, 2);
		numres = parsnum.replace('0', '10');
	} else if (strlen_num == 3) {
		parsnum = num.substring(2, 3);
		numres = parsnum.replace('0', '10');
	} else if (strlen_num == 4) {
		parsnum = num.substring(3, 4);
		numres = parsnum.replace('0', '10');
	} else if (strlen_num == 5) {
		parsnum = num.substring(4, 5);
		numres = parsnum.replace('0', '10');
	}
	if (numres <= 0) var gram_num_record = text5;
	else if (numres == 1) var gram_num_record = text1;
	else if (numres < 5) var gram_num_record = text2;
	else if (numres < 21) var gram_num_record = text3;
	else if (numres == 21) var gram_num_record = text4;
	else var gram_num_record = '';
	$('#' + id).html(gram_num_record);
}
//LANG
var trsn = {
	box: function() {
		$('.js_titleRemove').remove();
		viiBox.start();
		$.post('/index.php?go=lang', function(d) {
			viiBox.win('vii_lang_box', d);
		});
	}
}

function AntiSpam(act) {
	Page.Loading('stop');
	var max_friends = 40;
	var max_msg = 40;
	var max_wall = 500;
	var max_comm = 2000;
	if (act == 'friends') {
		Box.Info('antispam_' + act, 'Информация', 'В день Вы можете отправить не более ' + max_friends + ' заявок в друзья.', 300, 4000);
	} else if (act == 'messages') {
		Box.Info('antispam_' + act, 'Информация', 'В день Вы можете отправить не более ' + max_msg + ' сообщений. Если Вы хотите продолжить общение с этим пользователем, то добавьте его в список своих друзей.', 350, 5000);
	} else if (act == 'wall') {
		Box.Info('antispam_' + act, 'Информация', 'В день Вы можете отправить не более ' + max_wall + ' записей на стену.', 350, 4000);
	} else if (act == 'comm') {
		Box.Info('antispam_' + act, 'Информация', 'В день Вы можете отправить не более ' + max_comm + ' комментариев.', 350, 4000);
	} else if (act == 'groups') {
		Box.Info('antispam_' + act, 'Информация', 'В день Вы можете создать не более <b>5</b> сообществ.', 350, 3000);
	}
}

function delMyPage() {
	Box.Show('del_page', 400, 'Удаление страницы', '<div style="padding:15px;">Вы уверены, что хотите удалить свою страницу ?</div>', lang_box_canсel, 'Да, удалить страницу', 'startDelpage()');
}

function startDelpage() {
	$('#box_loading').fadeIn('fast');
	$('.box_footer .button_div, .box_footer .button_div_gray').fadeOut('fast');
	$.post('/index.php?go=del_my_page', function() {
		window.location.href = '/';
	});
}
String.prototype.toHHMMSS = function() {
	var sec_num = parseInt(this, 10);
	var hours = Math.floor(sec_num / 3600);
	var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
	var seconds = sec_num - (hours * 3600) - (minutes * 60);
	/*if (hours   < 10) {hours   = "0"+hours;}*/
	if (minutes < 10) {
		minutes = "0" + minutes;
	}
	if (seconds < 10) {
		seconds = "0" + seconds;
	}
	return /*hours+':'+*/ minutes + ':' + seconds;
}