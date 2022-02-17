<div class="h1" style="margin-top:10px">Общие настройки</div>

<style type="text/css" media="all">
    .inpu{width:300px;} textarea

    {width:300px;height:100px;}
</style>

<form method="POST" action="">

    <div class="fllogall">Название сайта:</div>
    <input type="text" name="save[home]" class="inpu" value="{config_home}"/>
    <div class="mgcler"></div>

    <div class="fllogall">Используемая кодировка на сайте:</div>
    <input type="text" name="save[charset]" class="inpu" value="{config_charset}"/>
    <div class="mgcler"></div>

    <div class="fllogall">Адрес сайта:</div>
    <input type="text" name="save[home_url]" class="inpu" value="{config_home_url}"/>
    <div class="mgcler"></div>

    <div class="fllogall">Шаблон сайта по умолчанию:</div>
    <select name="save[temp]" class="inpu" style="width:auto">{for_select}</select>
    <div class="mgcler"></div>

    <div class="fllogall">Время онлайна людей в секундах:</div>
    <input type="text" name="save[online_time]" class="inpu" value="{config_online_time}"/>
    <div class="mgcler"></div>

    <div class="fllogall">Используемый язык:</div>
    <select name="save[lang]" class="inpu" style="width:auto">{for_select_lang}</select>
    <div class="mgcler"></div>

    <div class="fllogall">Включить Gzip сжатие HTML страниц:</div>
    <select name="save[gzip]" class="inpu" style="width:auto">{for_select_gzip}</select>
    <div class="mgcler"></div>

    <div class="fllogall">Включить Gzip сжатие JS файлов:</div>
    <select name="save[gzip_js]" class="inpu" style="width:auto">{for_select_gzip_js}</select>
    <div class="mgcler"></div>

    <div class="fllogall">Выключить сайт:</div>
    <select name="save[offline]" class="inpu" style="width:auto">{for_select_offline}</select>
    <div class="mgcler"></div>

    <div class="fllogall">Причина отключения сайта:</div>
    <textarea class="inpu" name="save[offline_msg]">{config_offline_msg}</textarea>

    <div class="fllogall">Список используемых языков (название папок): <br/>
        <br/>пример: <b>Русский | Russian</b>
    </div>
    <textarea class="inpu" name="save[lang_list]">{config_lang_list}</textarea>

    <div class="fllogall">Бонусный рейтинг за подарок (цена подарка):</div>
    <input type="text" name="save[bonus_rate]" class="inpu" value="{config_bonus_rate}"/>
    <div class="mgcler"></div>

    <div class="fllogall">Стоимость 1 голоса:</div>
    <input type="text" name="save[cost_balance]" class="inpu" value="{config_cost_balance}"/>
    <div class="mgcler"></div>

    <div class="h1" style="margin-top:10px"><a name="video"></a>Настройки видео</div>

    <div class="fllogall">Выключить модуль:</div>
    <select name="save[video_mod]" class="inpu" style="width:auto">{for_select_video_mod}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Разрешить комментирование видео:</div>
    <select name="save[video_mod_comm]" class="inpu" style="width:auto">{for_select_video_mod_comm}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Разрешить добавление видео:</div>
    <select name="save[video_mod_add]" class="inpu" style="width:auto">{for_select_video_mod_add}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Включить функцию "Добавить в Мои Видеозаписи":</div>
    <select name="save[video_mod_add_my]" class="inpu" style="width:auto">{for_select_video_mod_add_my}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Разрешить поиск по видео:</div>
    <select name="save[video_mod_search]" class="inpu" style="width:auto">{for_select_video_mod_search}</select>

    <div class="h1" style="margin-top:10px"><a name="audio"></a>Настройки аудио</div>

    <div class="fllogall">Выключить модуль:</div>
    <select name="save[audio_mod]" class="inpu" style="width:auto">{for_select_audio_mod}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Разрешить добавление музыки:</div>
    <select name="save[audio_mod_add]" class="inpu" style="width:auto">{for_select_audio_mod_add}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Разрешить поиск по музыке:</div>
    <select name="save[audio_mod_search]" class="inpu" style="width:auto">{for_select_audio_mod_search}</select>

    <div class="h1" style="margin-top:10px"><a name="photos"></a>Настройки фото</div>

    <div class="fllogall">Выключить модуль "Альбомы":</div>
    <select name="save[album_mod]" class="inpu" style="width:auto">{for_select_album_mod}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Максимальное количество альбомов:</div>
    <input type="text" name="save[max_albums]" class="inpu" value="{config_max_albums}"/>
    <div class="mgcler"></div>
    <div class="fllogall">Максимальное количество фото в один альбом:</div>
    <input type="text" name="save[max_album_photos]" class="inpu" value="{config_max_album_photos}"/>
    <div class="mgcler"></div>
    <div class="fllogall">Максимальный размер загужаемой фотографии (кб):</div>
    <input type="text" name="save[max_photo_size]" class="inpu" value="{config_max_photo_size}"/>
    <div class="mgcler"></div>
    <div class="fllogall">Расширение фотографий, допустимых к загрузке:<br/>
        <small>Например: <b>jpg, jpeg, png</b></small>
    </div>
    <input type="text" name="save[photo_format]" class="inpu" value="{config_photo_format}"/>
    <div class="mgcler"></div>
    <div class="fllogall">Разрешить менять порядок альбомов:</div>
    <select name="save[albums_drag]" class="inpu" style="width:auto">{for_select_albums_drag}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Разрешить менять порядок фотографий:</div>
    <select name="save[photos_drag]" class="inpu" style="width:auto">{for_select_photos_drag}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Стоимость оценки <b>5+</b>:</div>
    <input type="text" name="save[rate_price]" class="inpu" value="{config_rate_price}"/>
    <div class="mgcler"></div>

    <div class="h1" style="margin-top:10px">Настройки E-Mail</div>

    <div class="fllogall">E-Mail адрес администратора:</div>
    <input type="text" name="save[admin_mail]" class="inpu" value="{config_admin_mail}"/>
    <div class="mgcler"></div>
    <div class="fllogall">Метод отправки почты:</div>
    <select name="save[mail_metod]" class="inpu" style="width:auto">{for_select_mail_metod}</select>
    <div class="mgcler"></div>
    <div class="fllogall">SMTP хост:</div>
    <input type="text" name="save[smtp_host]" class="inpu" value="{config_smtp_host}"/>
    <div class="mgcler"></div>
    <div class="fllogall">SMTP порт:</div>
    <input type="text" name="save[smtp_port]" class="inpu" value="{config_smtp_port}"/>
    <div class="mgcler"></div>
    <div class="fllogall">SMTP Имя Пользователя:</div>
    <input type="text" name="save[smtp_user]" class="inpu" value="{config_smtp_user}"/>
    <div class="mgcler"></div>
    <div class="fllogall">SMTP Пароль:</div>
    <input type="text" name="save[smtp_pass]" class="inpu" value="{config_smtp_pass}"/>
    <div class="mgcler"></div>

    <div class="h1" style="margin-top:10px">Настройки E-Mail оповещаний</div>

    <div class="fllogall">Включить уведомление при новой заявки в друзья:</div>
    <select name="save[news_mail_1]" class="inpu" style="width:auto">{for_select_news_mail_1}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Включить уведомление при ответе на запись:</div>
    <select name="save[news_mail_2]" class="inpu" style="width:auto">{for_select_news_mail_2}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Включить уведомление при комментировании видео:</div>
    <select name="save[news_mail_3]" class="inpu" style="width:auto">{for_select_news_mail_3}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Включить уведомление при комментировании фото:</div>
    <select name="save[news_mail_4]" class="inpu" style="width:auto">{for_select_news_mail_4}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Включить уведомление при комментировании заметки:</div>
    <select name="save[news_mail_5]" class="inpu" style="width:auto">{for_select_news_mail_5}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Включить уведомление при новом подарке:</div>
    <select name="save[news_mail_6]" class="inpu" style="width:auto">{for_select_news_mail_6}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Включить уведомление при новой записи на стене:</div>
    <select name="save[news_mail_7]" class="inpu" style="width:auto">{for_select_news_mail_7}</select>
    <div class="mgcler"></div>
    <div class="fllogall">Включить уведомление при новом персональном сообщении:</div>
    <select name="save[news_mail_8]" class="inpu" style="width:auto">{for_select_news_mail_8}</select>
    <div class="mgcler"></div>

    <div class="fllogall">&nbsp;</div>
    <input type="submit" value="Сохранить" name="saveconf" class="inp" style="margin-top:0px"/>
</form>


