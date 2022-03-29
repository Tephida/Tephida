/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

class LazyLoading {
    data(i){
        this.js(i);
    }
    js(i){
        const arr = ['audio_player', 'rating', 'payment'];
        const check = $('#dojs' + arr[i]).length;
        if (!check)
            $('#doLoad').append('<div id="dojs' + arr[i] + '"><script type="text/javascript" src="/js/' + arr[i] + '.js"></script></div>');
    }
}


var req_href = location.href;
var vii_interval = false;
var vii_interval_im = false;
var url_next_id = 1;
$(document).ready(function () {
    var mw = ($('html, body').width() - 800) / 2;
    if ($('.autowr').css('padding-left', mw + 'px').css('padding-right', mw + 'px')) {
        $('body').show();
        history.pushState({
            link: location.href
        }, '', location.href);
    }
    $('.update_code').click(function () {
        var rndval = new Date().getTime();
        $('#sec_code').html('<img src="/antibot/antibot.php?rndval=' + rndval + '" alt="" title="Показать другой код" width="120" height="50" />');
        return false;
    });
    $(window).scroll(function () {
        if ($(document).scrollTop() > ($(window).height() / 2)) $('.scroll_fix_bg').fadeIn(200);
        else $('.scroll_fix_bg').fadeOut(200);
    });
});
if (CheckRequestPhoto(req_href)) {
    $(document).ready(function () {
        Photo.Show(req_href);
    });
}
if (CheckRequestVideo(req_href)) {
    $(document).ready(function () {
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
window.onload = function () {
    window.setTimeout(function () {
        window.addEventListener("popstate", function (e) {
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

class PageTools {
    Loading(f) {
        var top_pad = $(window).height() / 2 - 50;
        if (f == 'start') {
            $('#loading').remove();
            $('html, body').append('<div id="loading" style="margin-top:' + top_pad + 'px"><div class="loadstyle"></div></div>');
            $('#loading').show();
        }
        if (f == 'stop') {
            $('#loading').remove();
        }
    }
    Go(url)
    {
        async function get_content(url)
        {
            // let hh = (location.href).replace('https://' + location.host + '/', '');
            history.pushState({
                link: url
            }, null, url);
            $('.js_titleRemove').remove();
            clearInterval(vii_interval);
            clearInterval(vii_interval_im);
            Page.Loading('start');
            let data = {ajax: 'yes'};
            let response = await fetch(url, {
                method: 'POST',
                body: new URLSearchParams(data),
            });
            let result = await response.json();
            $('#page').html(result.content);
            $('html, body').scrollTop(0);
            if(window.audio_player && !audio_player.pause) audio_player.command('play', {style_only: true});
            $('#addStyleClass').remove();
            $('.photo_view, .box_pos, .box_info, .video_view').remove();
            document.title = result.title;
            $('#new_msg').html(result.user_pm_num);
            $('#new_news').html(result.new_news);
            $('#new_ubm').html(result.new_ubm);
            $('#ubm_link').attr('href', result.gifts_link);
            $('#new_support').html(result.support);
            $('#news_link').attr('href', '/news' + result.news_link);
            $('#new_requests').html(result.demands);
            $('#new_guests').html(result.guests);
            $('#new_photos').html(result.new_photos);
            $('#requests_link_new_photos').attr('href', '/albums/' + result.new_photos_link);
            $('#requests_link').attr('href', '/friends' + result.requests_link);
            $('#new_groups').html(result.new_groups);
            $('#new_groups_lnk').attr('href', result.new_groups_lnk);
            if ($('.staticPlbg').length) {
                $('.staticPlbg').css('margin-top', '-500px');
                player.reestablish();
            }
            let timerId = setInterval(() => Page.Loading('stop'), 100);
            setTimeout(() => {
                clearInterval(timerId);
                Page.Loading('stop');
            }, 200);
            clearTimeout(timerId);
        }
        get_content(url);
    }
    Prev(url)
    {
        async function get_content(url){
            clearInterval(vii_interval);
            clearInterval(vii_interval_im);
            Page.Loading('start');
            let data = {ajax: 'yes'};
            let response = await fetch(url, {
                method: 'POST',
                body: new URLSearchParams(data),
            });
            let result = await response.json();
            $('#page').html(result.content).css('min-height', '0px');
            Page.Loading('stop');
            $('html, body').scrollTop(0).css('overflow-y', 'auto');
            // $('.ladybug_ant').imgAreaSelect({remove: true});
            if(window.audio_player && !audio_player.pause) audio_player.command('play', {style_only: true});
            $('#addStyleClass').remove();
            $('.photo_view, .box_pos, .box_info, .video_view').remove();
            document.title = result.title;
            $('#new_msg').html(result.user_pm_num);
            $('#new_news').html(result.new_news);
            $('#new_ubm').html(result.new_ubm);
            $('#ubm_link').attr('href', result.gifts_link);
            $('#new_support').html(result.support);
            $('#news_link').attr('href', '/news' + result.news_link);
            $('#new_requests').html(result.demands);
            $('#new_guests').html(result.guests);
            $('#new_photos').html(result.new_photos);
            $('#requests_link_new_photos').attr('href', '/albums/' + result.new_photos_link);
            $('#requests_link').attr('href', '/friends' + result.requests_link);
            $('#new_groups').html(result.new_groups);
            $('#new_groups_lnk').attr('href', result.new_groups_lnk);
        }
        get_content(url);
    }
    ge(i){
        return document.getElementById(i);
    }
    addAllErr(text, tim = 2500){
        $('.privacy_err').remove();
        $('body').append('<div class="privacy_err no_display">' + text + '</div>');
        $('.privacy_err').fadeIn('fast');
        setTimeout("$('.privacy_err').fadeOut('fast')", tim);
        $('.privacy_err').remove();
    }
    langNumric(id, num, text1, text2, text3, text4, text5) {
        let strlen_num = num.length;
        let numres;
        let parsnum;

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

}
const Page = new PageTools();

//todo remove
function ge(i) {
    return document.getElementById(i);
}

//PROFILE FUNC
var Profile = {
    miniature: function () {
        Page.Loading('start');
        $.post('/index.php?go=editprofile&act=miniature', function (d) {
            Box.Show('createRoom', 400, 'Выбор миниатюры', d, lang_box_cancel);

            Page.Loading('stop');
            //fixme
            // if (d == 1) addAllErr('Вы пока что не загрузили фотографию.');
            // else {
            //     $('html, body').css('overflow-y', 'hidden');
            //     $('body').append('<div id="newbox_miniature">' + d + '</div>');
            // }
            // $(window).keydown(function (event) {
            //     if (event.keyCode == 27) Profile.miniatureClose();
            // });
        });
    },
    preview: function (img, selection) {
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
    miniatureSave: function () {
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
        }, function (d) {
            if (d == 'err')
                Page.addAllErr('Ошибка');
            else window.location.href = '/u' + d;
            butloading('miniatureSave', '111', 'enabled', 'Сохранить изменения');
        });
    },
    miniatureClose: function () {
        //$('#miniature_crop').imgAreaSelect({remove: true});
        $('#newbox_miniature').remove();
        $('html, body').css('overflow-y', 'auto');
    },
    LoadCity: function (id) {
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
    LoadPhoto: function () {
        Page.Loading('start');
        $.get('/index.php?go=editprofile&act=load_photo', function (data) {
            Box.Show('photo', 400, lang_title_load_photo, data, lang_box_cancel);
            Page.Loading('stop');
        });
    },
    DelPhoto: function () {
        Box.Show('del_photo', 400, lang_title_del_photo, '<div style="padding:15px;">' + lang_del_photo + '</div>', lang_box_cancel, lang_box_yes, 'Profile.StartDelPhoto(); return false;');
    },
    StartDelPhoto: function () {
        $('#box_loading').show();
        $.get('/index.php?go=editprofile&act=del_photo', function () {
            $('#ava').html('<img src="/images/no_ava.gif" alt="" />');
            $('#del_pho_but').hide();
            Box.Close('del_photo');
            Page.Loading('stop');
        });
    },
    MoreInfo: function () {
        $('#moreInfo').show();
        $('#moreInfoText').text('Скрыть подробную информацию');
        $('#moreInfoLnk').attr('onClick', 'Profile.HideInfo()');
    },
    HideInfo: function () {
        $('#moreInfo').hide();
        $('#moreInfoText').text('Показать подробную информацию');
        $('#moreInfoLnk').attr('onClick', 'Profile.MoreInfo()');
    }
}

//MODAL BOX
class ModalBox {
    Page(url, data, name, width, title, cancel_text, func_text, func, height, overflow, bg_show, bg_show_bottom, input_focus, cache) {
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
                this.Close(name, cache);
                $('#box_' + name).show();
                $('#box_content_' + name).scrollTop(0);
                $('html').css('overflow', 'hidden');
                return false;
            }
        Page.Loading('start');
        $.post(url, data, function (html) {
            if (!CheckRequestVideo(location.href)){
                this.Close(name, cache);
            }
            this.Show(name, width, title, html, cancel_text, func_text, func, height, overflow, bg_show, bg_show_bottom, cache);
            Page.Loading('stop');
            if (input_focus)
                $('#' + input_focus).focus();
        });
    }
    /**
     *
     * @param name - id окна
     * @param width - ширина окна
     * @param title - заголовк окна
     * @param content - контент окна
     * @param close_text - текст закрытия
     * @param func_text - текст который будет выполнять функцию
     * @param func - функция текста "func_text"
     * @param height - высота окна
     * @param overflow - постоянный скролл
     * @param bg_show - тень внтури окна сверху
     * @param bg_show_bottom - тень внтури внтури снизу
     * @param cache - "1" - кешировоть, "0" - не кешировать
     * @constructor
     */
    Show(name, width, title, content, close_text, func_text, func, height, overflow, bg_show, bg_show_bottom, cache) {
        var func_but;
        if (func_text)
            func_but = '<div class="button_div fl_r" style="margin-right:10px;" id="box_but">' +
                '<button onClick="' + func + '" id="box_butt_create">' + func_text + '</button></div>';
        else
            func_but = '';

        if (!cache)
            cache = false;
        var close_but = '<div class="button_div_gray fl_r">' +
            '<button ' +
            'onClick="Box.Close(\'' + name + '\', ' + cache + '); return false;">' + close_text + '</button></div>';
        var box_loading = '<img id="box_loading" style="display:none;padding-top:8px;padding-left:5px;" ' +
            'src="/images/loading_mini.gif" alt="" />';
        if (height) var top_pad = ($(window).height() - 150 - height) / 2;
        if (top_pad < 0) top_pad = 100;
        if (overflow)
            overflow = 'overflow-y:scroll;';
        else
            overflow = '';
        if (bg_show)
            if (overflow)
                bg_show = '<div class="bg_show" style="width:' + (width - 19) + 'px;"></div>';
            else
                bg_show = '<div class="bg_show" style="width:' + (width - 2) + 'px;"></div>';
        else
            bg_show = '';
        if (bg_show_bottom)
            if (overflow)
                bg_show_bottom = '<div class="bg_show_bottom" style="width:' + (width - 17) + 'px;"></div>';
            else
                bg_show_bottom = '<div class="bg_show_bottom" style="width:' + (width - 2) + 'px;"></div>';
        else
            bg_show_bottom = '';
        var sheight;
        if (height)
            sheight = 'height:' + height + 'px';
        else
            sheight = '';
        $('body').append('<div id="modal_box"><div id="box_' + name + '" class="box_pos">' +
            '<div class="box_bg" style="width:' + width + 'px;margin-top:' + top_pad + 'px;">' +
            '<div class="box_title" id="box_title_' + name + '">' + title +
            '<div class="box_close" onClick="Box.Close(\'' + name + '\', ' + cache + '); return false;"></div></div>' +
            '<div class="box_conetnt" id="box_content_' + name + '" style="' + sheight + ';' + overflow + '">' +
            bg_show + content + '<div class="clear"></div></div>' + bg_show_bottom +
            '<div class="box_footer"><div id="box_bottom_left_text" class="fl_l">' +
            box_loading + '</div>' + close_but + func_but + '</div></div></div></div>');
        $('#box_' + name).show();
        $('html').css('overflow', 'hidden');
        $(window).keydown(function (event) {
            if (event.keyCode === 27) {
                this.Close(name, cache);
            }
        });
    }
    Close(name, cache) {
        if (!cache) {
            $('.box_pos').remove();
            $('#modal_box').remove();

        } else
            $('.box_pos').hide();

        if (CheckRequestVideo(location.href) === false && CheckRequestPhoto(location.href) === false)
            $('html, body').css('overflow-y', 'auto');
        if (CheckRequestVideo(location.href))
            $('#video_object').show();
    }
    GeneralClose() {
        $('#modal_box').hide();
    }
    Info(bid, title, content, width = 300, tout = 1400) {
        var top_pad = ($(window).height() - 115) / 2;
        $('body').append('<div id="' + bid + '" class="box_info"><div class="box_info_margin" style="width: ' + width + 'px; margin-top: ' + top_pad + 'px"><b><span>' + title + '</span></b><br /><br />' + content + '</div></div>');
        $(bid).show();
        setTimeout("Box.InfoClose()", tout);
        $(window).keydown(function (event) {
            if (event.keyCode === 27) {
                this.InfoClose();
            }
        });
    }
    InfoClose() {
        $('.box_info').fadeOut();
    }
}
const Box = new ModalBox();

function butloading(i, w, d, t) {
    if (d == 'disabled') {
        $('#' + i).html('<div style="width:' + w + 'px;text-align:center;"><img src="/images/loading_mini.gif" alt="" /></div>');
        ge(i).disabled = true;
    } else {
        $('#' + i).html(t);
        ge(i).disabled = false;
    }
}

function textLoad(i) {
    $('#' + i).html('<img src="/images/loading_mini.gif" alt="" />').attr('onClick', '').attr('href', '#');
}

function updateNum(i, type) {
    if (type) $(i).text(parseInt($(i).text()) + 1);
    else $(i).text($(i).text() - 1);
}

function setErrorInputMsg(i) {
    $("#" + i).css('background', '#ffefef').focus();
    setTimeout("$('#" + i + "').css('background', '#fff').focus()", 700);
}

//LANG
const trsn = {
    box: function () {
        $('.js_titleRemove').remove();
        $.post('/langs/box', function (d) {
            Box.Show('lang', 270, 'Выбор языка', d, lang_box_cancel);
        });
    }
};

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
    Box.Show('del_page', 400, 'Удаление страницы', '<div style="padding:15px;">Вы уверены, что хотите удалить свою страницу ?</div>', lang_box_cancel, 'Да, удалить страницу', 'startDelpage()');
}

function startDelpage() {
    $('#box_loading').fadeIn('fast');
    $('.box_footer .button_div, .box_footer .button_div_gray').fadeOut('fast');
    $.post('/index.php?go=del_my_page', function () {
        window.location.href = '/';
    });
}

String.prototype.toHHMMSS = function () {
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
const doLoad = new LazyLoading();