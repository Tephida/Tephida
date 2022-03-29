<div class="h1" style="margin-top:10px">Общие настройки</div>

<style media="all">
    .inpu{width:300px;} textarea{width:300px;height:100px;}

        /* ERRORS */
    .err_yellow{padding:10px;background:#f4f7fa;border:1px solid #bfd2e4;margin-bottom:10px} .err_red{padding:10px;background:#faebeb;margin-bottom:10px;line-height:17px} .listing {list-style: square;color: #d20000;margin:0px;padding-left:10px} ul.listing li {padding: 1px 0px} ul.listing li span {color: #000} .privacy_err

    {background:#ffb4a3;position:fixed;left:0px;top:0px;padding:7px;border-bottom-right-radius:7px;-moz-border-bottom-right-radius:7px;-webkit-border-bottom-right-radius:7px;margin-top:48px;z-index:100}

</style>
<script>
    var Settings = {
        save: function () {

            const data = {
                'home': $('#home').val(),
                'charset': $('#charset').val(),
                'home_url': $('#home_url').val(),
                'admin_index': $('#admin_index').val(),
                'temp': $('#temp').val(),
                'online_time': $('#online_time').val(),
                'lang': $('#lang').val(),
                'gzip': $('#gzip').val(),
                'gzip_js': $('#gzip_js').val(),
                'offline': $('#offline').val(),
                'offline_msg': $('#offline_msg').val(),
                'bonus_rate': $('#bonus_rate').val(),
                'cost_balance': $('#cost_balance').val(),
                'video_mod': $('#video_mod').val(),
                'video_mod_comm': $('#video_mod_comm').val(),
                'video_mod_add': $('#video_mod_add').val(),
                'video_mod_add_my': $('#video_mod_add_my').val(),
                'video_mod_search': $('#video_mod_search').val(),
                'audio_mod': $('#audio_mod').val(),
                'audio_mod_add': $('#audio_mod_add').val(),
                'audio_mod_search': $('#audio_mod_search').val(),
                'album_mod': $('#album_mod').val(),
                'max_albums': $('#max_albums').val(),
                'max_album_photos': $('#max_album_photos').val(),
                'max_photo_size': $('#max_photo_size').val(),
                'photo_format': $('#photo_format').val(),
                'albums_drag': $('#albums_drag').val(),
                'photos_drag': $('#photos_drag').val(),
                'rate_price': $('#rate_price').val(),
                'admin_mail': $('#admin_mail').val(),
                'mail_metod': $('#mail_metod').val(),
                'smtp_host': $('#smtp_host').val(),
                'smtp_port': $('#smtp_port').val(),
                'smtp_user': $('#smtp_user').val(),
                'smtp_pass': $('#smtp_pass').val(),
                'news_mail_1': $('#news_mail_1').val(),
                'news_mail_2': $('#news_mail_2').val(),
                'news_mail_3': $('#news_mail_3').val(),
                'news_mail_4': $('#news_mail_4').val(),
                'news_mail_5': $('#news_mail_5').val(),
                'news_mail_6': $('#news_mail_6').val(),
                'news_mail_7': $('#news_mail_7').val(),
                'news_mail_8': $('#news_mail_8').val(),
            };
            $.post('/adminpanel.php?mod=system&act=save', {
                save: JSON.stringify(data),
                saveconf: '',
            }, function (response) {
                // addAllErr(data.info);
                Page.addAllErr(response.info);
            });
        }
    }
</script>
<div>

    <div class="fllogall">Название сайта:</div>
    <input type="text" name="save[home]" id="home" class="inpu" value="{config_home}"/>
    <div class="mgcler"></div>

    <div class="fllogall">Используемая кодировка на сайте:</div>
    <input type="text" name="save[charset]" id="charset" class="inpu" value="{config_charset}"/>
    <div class="mgcler"></div>

    <div class="fllogall">Адрес сайта:</div>
    <input type="text" name="save[home_url]" id="home_url" class="inpu" value="{config_home_url}"/>
    <div class="mgcler"></div>

    <div class="fllogall">Адрес панели управления:</div>
    <input type="text" name="save[admin_index]" id="admin_index" class="inpu" value="{config_admin_index}"/>
    <div class="mgcler"></div>

    <div class="fllogall">Шаблон сайта по умолчанию:</div>
    <select name="save[temp]" id="temp" class="inpu" style="width:auto">{for_select}</select>
    <div class="mgcler"></div>

    <div class="fllogall">Время онлайна людей в секундах:</div>
    <input type="text" name="save[online_time]" id="online_time" class="inpu" value="{config_online_time}"/>
    <div class="mgcler"></div>

    <div class="fllogall">Включить Gzip сжатие HTML страниц:</div>
    <select name="save[gzip]" id="gzip" class="inpu" style="width:auto">{for_select_gzip}</select>
    <div class="mgcler"></div>

    <div class="fllogall">Включить Gzip сжатие JS файлов:</div>
    <select name="save[gzip_js]" id="gzip_js" class="inpu" style="width:auto">{for_select_gzip_js}</select>
    <div class="mgcler"></div>

    <div class="fllogall">Выключить сайт:</div>
    <select name="save[offline]" id="offline" class="inpu" style="width:auto">{for_select_offline}</select>
    <div class="mgcler"></div>

    <div class="fllogall">Причина отключения сайта:</div>
    <textarea class="inpu" name="save[offline_msg]" id="offline_msg">{config_offline_msg}</textarea>

    <div class="fllogall">Бонусный рейтинг за подарок (цена подарка):</div>
    <input type="text" name="save[bonus_rate]" id="bonus_rate" class="inpu" value="{config_bonus_rate}"/>
    <div class="mgcler"></div>

    <div class="fllogall">Стоимость 1 голоса:</div>
    <input type="text" name="save[cost_balance]" id="cost_balance" class="inpu" value="{config_cost_balance}"/>
    <div class="mgcler"></div>

    <div class="h1" style="margin-top:10px"><a name="video"></a>Настройки видео</div>

    <div class="fllogall">Выключить модуль:</div>
    <select name="save[video_mod]" id="video_mod" class="inpu" style="width:auto">{for_select_video_mod}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Разрешить комментирование видео:</div>
    <select name="save[video_mod_comm]" id="video_mod_comm" class="inpu"
            style="width:auto">{for_select_video_mod_comm}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Разрешить добавление видео:</div>
    <select name="save[video_mod_add]" id="video_mod_add" class="inpu"
            style="width:auto">{for_select_video_mod_add}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Включить функцию "Добавить в Мои Видеозаписи":</div>
    <select name="save[video_mod_add_my]" id="video_mod_add_my" class="inpu"
            style="width:auto">{for_select_video_mod_add_my}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Разрешить поиск по видео:</div>
    <select name="save[video_mod_search]" id="video_mod_search" class="inpu"
            style="width:auto">{for_select_video_mod_search}</select>

    <div class="h1" style="margin-top:10px"><a name="audio"></a>Настройки аудио</div>

    <div class="fllogall">Выключить модуль:</div>
    <select name="save[audio_mod]" id="audio_mod" class="inpu" style="width:auto">{for_select_audio_mod}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Разрешить добавление музыки:</div>
    <select name="save[audio_mod_add]" id="audio_mod_add" class="inpu"
            style="width:auto">{for_select_audio_mod_add}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Разрешить поиск по музыке:</div>
    <select name="save[audio_mod_search]" id="audio_mod_search" class="inpu"
            style="width:auto">{for_select_audio_mod_search}</select>

    <div class="h1" style="margin-top:10px"><a name="photos"></a>Настройки фото</div>

    <div class="fllogall">Выключить модуль "Альбомы":</div>
    <select name="save[album_mod]" id="album_mod" class="inpu" style="width:auto">{for_select_album_mod}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Максимальное количество альбомов:</div>
    <input type="text" name="save[max_albums]" id="max_albums" class="inpu" value="{config_max_albums}"/>
    <div class="mgcler"></div>
    <div class="fllogall">Максимальное количество фото в один альбом:</div>
    <input type="text" name="save[max_album_photos]" id="max_album_photos" class="inpu"
           value="{config_max_album_photos}"/>
    <div class="mgcler"></div>
    <div class="fllogall">Максимальный размер загужаемой фотографии (кб):</div>
    <input type="text" name="save[max_photo_size]" id="max_photo_size" class="inpu" value="{config_max_photo_size}"/>
    <div class="mgcler"></div>
    <div class="fllogall">Расширение фотографий, допустимых к загрузке:<br/>
        <small>Например: <b>jpg, jpeg, png</b></small>
    </div>
    <input type="text" name="save[photo_format]" id="photo_format" class="inpu" value="{config_photo_format}"/>
    <div class="mgcler"></div>
    <div class="fllogall">Разрешить менять порядок альбомов:</div>
    <select name="save[albums_drag]" id="albums_drag" class="inpu" style="width:auto">{for_select_albums_drag}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Разрешить менять порядок фотографий:</div>
    <select name="save[photos_drag]" id="photos_drag" class="inpu" style="width:auto">{for_select_photos_drag}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Стоимость оценки <b>5+</b>:</div>
    <input type="text" name="save[rate_price]" id="rate_price" class="inpu" value="{config_rate_price}"/>
    <div class="mgcler"></div>

    <div class="h1" style="margin-top:10px">Настройки E-Mail</div>

    <div class="fllogall">E-Mail адрес администратора:</div>
    <input type="text" name="save[admin_mail]" id="admin_mail" class="inpu" value="{config_admin_mail}"/>
    <div class="mgcler"></div>
    <div class="fllogall">Метод отправки почты:</div>
    <select name="save[mail_metod]" id="mail_metod" class="inpu" style="width:auto">{for_select_mail_metod}</select>
    <div class="mgcler"></div>
    <div class="fllogall">SMTP хост:</div>
    <input type="text" name="save[smtp_host]" id="smtp_host" class="inpu" value="{config_smtp_host}"/>
    <div class="mgcler"></div>
    <div class="fllogall">SMTP порт:</div>
    <input type="text" name="save[smtp_port]" id="smtp_port" class="inpu" value="{config_smtp_port}"/>
    <div class="mgcler"></div>
    <div class="fllogall">SMTP Имя Пользователя:</div>
    <input type="text" name="save[smtp_user]" id="smtp_user" class="inpu" value="{config_smtp_user}"/>
    <div class="mgcler"></div>
    <div class="fllogall">SMTP Пароль:</div>
    <input type="text" name="save[smtp_pass]" id="smtp_pass" class="inpu" value="{config_smtp_pass}"/>
    <div class="mgcler"></div>

    <div class="h1" style="margin-top:10px">Настройки E-Mail оповещаний</div>

    <div class="fllogall">Включить уведомление при новой заявки в друзья:</div>
    <select name="save[news_mail_1]" id="news_mail_1" class="inpu" style="width:auto">{for_select_news_mail_1}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Включить уведомление при ответе на запись:</div>
    <select name="save[news_mail_2]" id="news_mail_2" class="inpu" style="width:auto">{for_select_news_mail_2}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Включить уведомление при комментировании видео:</div>
    <select name="save[news_mail_3]" id="news_mail_3" class="inpu" style="width:auto">{for_select_news_mail_3}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Включить уведомление при комментировании фото:</div>
    <select name="save[news_mail_4]" id="news_mail_4" class="inpu" style="width:auto">{for_select_news_mail_4}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Включить уведомление при комментировании заметки:</div>
    <select name="save[news_mail_5]" id="news_mail_5" class="inpu" style="width:auto">{for_select_news_mail_5}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Включить уведомление при новом подарке:</div>
    <select name="save[news_mail_6]" id="news_mail_6" class="inpu" style="width:auto">{for_select_news_mail_6}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Включить уведомление при новой записи на стене:</div>
    <select name="save[news_mail_7]" id="news_mail_7" class="inpu" style="width:auto">{for_select_news_mail_7}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Включить уведомление при новом персональном сообщении:</div>
    <select name="save[news_mail_8]" id="news_mail_8" class="inpu" style="width:auto">{for_select_news_mail_8}</select>
    <div class="mgcler"></div>

    <div class="fllogall">&nbsp;</div>
    <input type="button" value="Сохранить" name="saveconf" onclick="Settings.save()" class="inp"
           style="margin-top:0px"/>
</div>


