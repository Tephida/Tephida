function upClose(xnid) {
	$('#event' + xnid).remove();
	$('#updates').css('height', $('.update_box').length * 123 + 'px');
}

function GoPage(event, p) {
	var oi = (event.target) ? event.target.id : ((event.srcElement) ? event.srcElement.id : null);
	if (oi == 'no_ev' || oi == 'update_close' || oi == 'update_close2') return false;
	else {
		pattern = new RegExp(/photo[0-9]/i);
		pattern2 = new RegExp(/video[0-9]/i);
		if (pattern.test(p)) Photo.Show(p);
		else if (pattern2.test(p)) {
			vid = p.replace('/video', '');
			vid = vid.split('_');
			videos.show(vid[1], p, location.href);
		} else Page.Go(p);
	}
}
$(document).ready(function() {
	setInterval(function() {
		$.post('/index.php?go=updates', function(d) {
			row = d.split('|');
			if (d && row[1]) {
				if (row[0] == 1) uTitle = 'Новый ответ на стене';
				else if (row[0] == 2) uTitle = 'Новый комментарий к фотографии';
				else if (row[0] == 3) uTitle = 'Новый комментарий к видеозаписи';
				else if (row[0] == 4) uTitle = 'Новый комментарий к заметке';
				else if (row[0] == 5) uTitle = 'Новый ответ на Ваш комментарий';
				else if (row[0] == 6) uTitle = 'Новый ответ в теме';
				else if (row[0] == 7) uTitle = 'Новый подарок';
				else if (row[0] == 8) uTitle = 'Новое сообщение';
				else if (row[0] == 9) uTitle = 'Новая оценка';
				else if (row[0] == 10) uTitle = 'Ваша запись понравилась';
				else if (row[0] == 11) uTitle = 'Новая заявка';
				else if (row[0] == 12) uTitle = 'Заявка принята';
				else if (row[0] == 13) uTitle = 'Подписки';
				else uTitle = 'Событие';
				if (row[0] == 8) {
					sli = row[6].split('/');
					tURL = (location.href).replace('http://' + location.host, '').replace('/', '').split('#');
					if (!sli[2] && tURL[0] == 'messages') return false;
					if ($('#new_msg').text()) msg_num = parseInt($('#new_msg').text().replace(')', '').replace('(', '')) + 1;
					else msg_num = 1;
					$('#new_msg').html("<div class=\"headm_newac\" style=\"margin-left:37px\">" + msg_num + "</div>");
				}
				temp = '<div class="update_box cursor_pointer" id="event' + row[4] + '" onClick="GoPage(event, \'' + row[6] + '\'); upClose(' + row[4] + ')"><div class="update_box_margin"><div style="height:19px"><span>' + uTitle + '</span><div class="update_close fl_r no_display" id="update_close" onMouseDown="upClose(' + row[4] + ')"><div class="update_close_ic" id="update_close2"></div></div></div><div class="clear"></div><div class="update_inpad"><a href="/u' + row[2] + '" onClick="Page.Go(this.href); return false"><div class="update_box_marginimg"><img src="' + row[5] + '" id="no_ev" /></div></a><div class="update_data"><a id="no_ev" href="/u' + row[2] + '" onClick="Page.Go(this.href); return false">' + row[1] + '</a>&nbsp;&nbsp;' + row[3] + '</div></div><div class="clear"></div></div></div>';
				$('#updates').html($('#updates').html() + temp);
				var beepThree = $("#beep-three")[0];
				beepThree.play();
				if ($('.update_box').length <= 5) $('#updates').animate({
					'height': (123 * $('.update_box').length) + 'px'
				});
				if ($('.update_box').length > 5) {
					evFirst = $('.update_box:first').attr('id');
					$('#' + evFirst).animate({
						'margin-top': '-123px'
					}, 400, function() {
						$('#' + evFirst).fadeOut('fast', function() {
							$('#' + evFirst).remove();
						});
					});
				}
			}
		});
	}, 2000);
});