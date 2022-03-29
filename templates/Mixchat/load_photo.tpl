<script type="text/javascript">
    async function upload() {
        const input = document.querySelector('input[type="file"]');
        const data = new FormData();
        data.append('uploadfile', input.files[0]);
        data.append('user', 'hubot');
        let response = await fetch('/index.php?go=editprofile&act=upload', {
            method: 'POST',
            body: data,
        });
        let result = await response.json();

        if (result.status === 15)
            $('.err_red').show().text(lang_bad_format);
        else if (result.status === 14)
            $('.err_red').show().html(lang_bad_size);
        else if (result.status === 0)
            $('.err_red').show().text(lang_bad_aaa);
        else {
            Box.Close('photo');
            $('#ava').html('<a class="cursor_pointer" onClick="Profile.ava(\'' + result.photo + '\', \'' + location.href.replace('https://' + location.host + '/u', '') + '\')"><img src="' + result.photo + '" alt="" /></a>');
            $('body, html').animate({scrollTop: 0}, 250);
            $('#del_pho_but').show();
        }
    }
</script>
<div class="load_photo_pad">
    <div class="err_red" style="display:none;font-weight:normal;"></div>
    <div class="load_photo_quote">Вы можете загрузить сюда только собственную фотографию. Поддерживаются форматы JPG,
        PNG и GIF.
    </div>
    <div class="load_photo_but">
        <div class="button_div fl_l">
            <input type="file" id="avatar" oninput="upload()">
            <button id="upload">Выбрать фотографию</button>
        </div>
    </div>
    <div class="clear"></div>
    <small>Файл не должен превышать 5 Mб. Если у Вас возникают проблемы с загрузкой, попробуйте использовать фотографию
        меньшего размера.</small>
</div>