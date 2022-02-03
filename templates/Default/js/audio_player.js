var jQplast_id = 1;
var jQpRepeat = 0;
var jQpRand = 0;
var jQpTranslate = 0;
var jQpSeachDelay = false;
var jQpSearchVal = '';
var jQpPreload = true;
var jQlastPage = 0;
var jQaudioPage = 0;
var jQpUserId = 0;
var player = {
	open: function(type, uid) {
		if (!uid) uid = '';
		var pos = $('#fplayer_pos').offset().left - 220;
		var ln = $('#staticPlbg').length;
		if (!jQaudioPage) {
			if (ln) {
				var tPos = $('.staticPlbg').css('margin-top').replace('px', '');
				if (tPos == 60) $('.staticPlbg').animate({
					'margin-top': -500
				});
				else $('.staticPlbg').animate({
					'margin-top': 60
				});
			} else {
				var temp = '<div class="staticPlbg staticPlbgLoadig no_display" style="margin-left:' + pos + 'px"><div class="staticPlbgTitle"><div class="staticpl_ictop"></div><div align="center" style="margin-left:-10px;margin-bottom:1px"><img src="' + template_dir + '/images/load_player.gif" width="32" height="32" /></div></div></div>';
				$('#audioPlayer').html(temp);
				$('.staticPlbgLoadig').fadeIn(300);
				jQpUserId = uid;
				$.post('/index.php?go=audio_player', {
					get_user_id: uid
				}, function(d) {
					$('#audioPlayer').html('<div class="staticPlbg" id="staticPlbg" style="margin-left:' + pos + 'px">' + d + '</div>');
					if (type) player.change_list(uid);
				});
			}
		}
	},
	close: function() {
		$('.staticPlbg').animate({
			'margin-top': -500
		});
	},
	jPlayerInc: function() {
		$("#Xjquery_jplayer").jPlayer({
			ready: function() {
				var url = $('.staticpl_audio:first').attr('data');
				var name = $('.staticpl_autit:first').text().split(' – ');
				$('#XjArtis').html(name[0]);
				$('#XjTitle').html(name[1]);
				$(this).jPlayer("setMedia", {
					mp3: url
				});
			},
			timeupdate: function(event) {
				var myPlayedTime = parseInt(event.jPlayer.status.currentTime);
				$("#play_time").text(myPlayedTime.toString().toHHMMSS());
				if (event.jPlayer.status.currentTime && event.jPlayer.status.currentTime == event.jPlayer.status.duration) player.next();
			},
			swfPath: "/templates/Default/js/jplayer",
			cssPrefix: "different_prefix_example",
			cssSelectorAncestor: '',
			cssSelector: {
				play: "#player_pause_2",
				pause: "#player_pause_2",
				stop: "#player_stop_2",
				seekBar: "#player_progress_load_bar_2",
				playBar: "#player_progress_play_bar_2",
				volumeBar: "#player_volume_bar_2",
				volumeBarValue: "#player_volume_bar_value_2",
				volumeMax: "#player_volume_max_2"
			}
		});
	},
	onePlay: function() {
		$("#Xjquery_jplayer").jPlayer('play');
		$('.staticpl_play, #xPlayerPlay' + jQplast_id).hide();
		$('.staticpl_pause, #xPlayerPause' + jQplast_id).show();
		$('#xPlayer' + jQplast_id).addClass('staticpl_audio_active');
		$('#xPlayer' + jQplast_id).attr('onClick', 'player.pause()');
	},
	play: function(i, change) {
		var url = $('#xPlayer' + i).attr('data');
		var name = $('#xPlayerTitle' + i).text().split(' – ');
		//Онулируем пред. плеер
		if (jQplast_id != i) {
			$('#xPlayer' + jQplast_id).attr('onClick', 'player.play(' + jQplast_id + ')');
			$('#xPlayer' + jQplast_id).removeClass('staticpl_audio_active');
			$('#xPlayerPlay' + jQplast_id).show();
			$('#xPlayerPause' + jQplast_id).hide();
			$('#deltack' + jQplast_id).removeClass('staticpl_delic_white');
			$('#dtrack' + jQplast_id).removeClass('staticpl_editic_white');
			$('#atrack_' + jQplast_id).removeClass('staticpl_addmylisy_white');
			$('#atrackAddOk' + jQplast_id).removeClass('staticpl_addmylisok_white');
		}
		jQplast_id = i;
		$('#XjArtis').html(name[0]);
		$('#XjTitle').html(name[1]);
		if (!change) $("#Xjquery_jplayer").jPlayer("setMedia", {
			mp3: url
		});
		$("#Xjquery_jplayer").jPlayer('play');
		$('#xPlayer' + i).addClass('staticpl_audio_active');
		$('#deltack' + i).addClass('staticpl_delic_white');
		$('#dtrack' + i).addClass('staticpl_editic_white');
		$('#atrack_' + i).addClass('staticpl_addmylisy_white');
		$('#atrackAddOk' + i).addClass('staticpl_addmylisok_white');
		$('.staticpl_play, #xPlayerPlay' + i).hide();
		$('.staticpl_pause, #xPlayerPause' + i).show();
		$('#xPlayer' + i).attr('onClick', 'player.pause()');
	},
	pause: function() {
		$('#xPlayer' + jQplast_id).attr('onClick', 'player.play(' + jQplast_id + ', 1)');
		$('#Xjquery_jplayer').jPlayer('pause');
		$('.staticpl_play, #xPlayerPlay' + jQplast_id).show();
		$('.staticpl_pause, #xPlayerPause' + jQplast_id).hide();
	},
	next: function() {
		var new_id = parseInt(jQplast_id) + 1;
		var size = $('.staticpl_audio').length;
		var randId = Math.floor(Math.random() * size);
		if (randId == 0) randId = 1;
		if (jQpRand) {
			if (randId != jQplast_id) new_id = randId;
			else new_id = randId + 1;
		}
		if (jQpRepeat) new_id = jQplast_id;
		var check = $('#xPlayer' + new_id).length;
		var check2 = $('#xPlayer1').length;
		if (check) player.play(new_id);
		else if (!check2) player.stop();
		else player.play(1);
		//DO LOAD AUDIOS
		var allNum = size - 10;
		if (new_id >= allNum) player.page();
		//AUTO SCROLL
		var scroll = 36 * jQplast_id - 180;
		$('.staticpl_audios').animate({
			scrollTop: scroll
		});
	},
	prev: function() {
		var new_id = parseInt(jQplast_id) - 1;
		var check = $('#xPlayer' + new_id).length;
		var check2 = $('#xPlayer1').length;
		if (check) player.play(new_id);
		else if (!check2) player.stop();
		else player.play(1);
		//AUTO SCROLL
		var scroll = 36 * jQplast_id - 180;
		$('.staticpl_audios').animate({
			scrollTop: scroll
		});
	},
	stop: function() {
		$('#Xjquery_jplayer').stop();
		$('.staticpl_play').show();
		$('.staticpl_pause').hide();
	},
	refresh: function() {
		$('.staticpl_repeat').css('opacity', 1).attr('onClick', 'player.noRefresh()');
		jQpRepeat = 1;
	},
	noRefresh: function() {
		$('.staticpl_repeat').css('opacity', 0.8).attr('onClick', 'player.refresh()');
		jQpRepeat = 0;
	},
	rand: function() {
		$('.staticpl_rand').css('opacity', 1).attr('onClick', 'player.noRand()');
		jQpRand = 1;
	},
	noRand: function() {
		$('.staticpl_rand').css('opacity', 0.8).attr('onClick', 'player.rand()');
		jQpRand = 0;
	},
	translate: function() {
		$('.staticpl_translate').css('opacity', 1).attr('onClick', 'player.noTranslate()');
		jQpTranslate = 1;
	},
	noTranslate: function() {
		$('.staticpl_translate').css('opacity', 0.8).attr('onClick', 'player.translate()');
		jQpTranslate = 0;
	},
	page: function() {
		if ($('#jQp_page_but').text() == 'Показать больше аудиозаписей') {
			var a = $('#jQpSeachVal').val();
			if (a == 'Поиск') a = '';
			textLoad('jQp_page_but');
			$.post('/index.php?go=audio_player', {
				page_cnt: jQpage_cnt,
				query: a,
				doload: 1,
				get_user_id: jQpUserId
			}, function(d) {
				jQpage_cnt++;
				if (!a) jQlastPage = jQpage_cnt;
				$('#jQaudios').append(d);
				$('#jQp_page_but').text('Показать больше аудиозаписей');
				if (!d) $('.staticpl_albut').hide();
				if (jQaudioPage) $('.staticpl_panel').show();
			});
		}
	},
	gSearch: function() {
		var a = $('#jQpSeachVal').val();
		$('#jQpLoad').fadeOut('fast');
		if (!a) {
			player.xSearch();
		}
		0 == jQpSearchVal != a && a != 0 < a.length && (clearInterval(jQpSeachDelay), jQpSeachDelay = setInterval(function() {
			player.xSearch();
		}, 200));
	},
	xSearch: function(uid) {
		if (uid) jQpUserId = uid;
		clearInterval(jQpSeachDelay);
		var a = $('#jQpSeachVal').val();
		$('#jQpLoad').fadeIn('fast');
		$('.staticpl_audios').scrollTop(0);
		if (a == 'Поиск') a = '';
		$.post('/index.php?go=audio_player', {
			query: a,
			doload: 1,
			get_user_id: jQpUserId
		}, function(d) {
			jQpage_cnt = 1;
			$('#jQpLoad').fadeOut('fast');
			$('#jQaudios').html(d);
			var size = $('.staticpl_audio').length;
			if (size == 20) $('.staticpl_albut').show();
			else $('.staticpl_albut').hide();
			if (jQaudioPage) $('.staticpl_panel').show();
		});
	},
	doPast: function(i) {
		var name = $('#xPlayerTitle' + i).text().split(' – ');
		$('#jQpSeachVal').val(name[0]).css('color', '#000');
		player.xSearch();
	},
	change_list: function(uid) {
		if (!uid) uid = '';
		if (jQpUserId) uid = jQpUserId;
		jQpUserId = uid;
		document.title = 'Аудиозаписи';
		history.pushState({
			link: '/audio' + uid
		}, null, '/audio' + uid);
		jQaudioPage = 1;
		$('#speedbar, .staticpl_bottom').hide();
		$('#page').html('');
		$('.staticPlbg').addClass('page_audio').css('margin', '-12px').css('margin-top', '-12px');
		$('.staticpl_progress_bar').css('width', '570px');
		$('.staticpl_audios').css('width', '774px');
		$('.staticpl_shadow').css('width', '778px');
		$('#jQpSeachVal').css('width', '710px').css('float', 'left');
		$('#jQpLoad').css('margin-left', '706px');
		$('.staticpl_rtitle').css('max-width', '539px');
		$('.staticpl_trackname').css('width', '580px');
		$('.staticpl_panel').show();
		$('#jQpaddbutpos').html('<div class="jQpnewloadbut" onClick="audio.addBox()" onMouseOver="myhtml.title(\'1\', \'Добавить аудиозапись\', \'jqploadbut\', -2)" id="jqploadbut1"><div class="staticpl_addmylisy staticpl_addmylisy_white"></div></div>');
	},
	reestablish: function() {
		jQaudioPage = 0;
		var pos = $('#fplayer_pos').offset().left - 220;
		$('.staticpl_bottom').show();
		$('.staticPlbg').removeClass('page_audio').css('margin', '20px').css('margin-top', '60px').css('margin-left', pos + 'px').css('margin-top', '-500px');
		$('.staticpl_progress_bar').css('width', '250px');
		$('.staticpl_audios').css('width', '455px');
		$('.staticpl_shadow').css('width', '459px');
		$('#jQpSeachVal').css('width', '415px').css('float', 'none');
		$('#jQpLoad').css('margin-left', '410px');
		$('.staticpl_rtitle').css('max-width', '220px');
		$('.staticpl_trackname').css('width', '250px');
		$('.staticpl_panel').hide();
		$('#jQpaddbutpos').html('');
	}
}