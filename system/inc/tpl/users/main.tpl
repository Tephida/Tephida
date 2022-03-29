<style media="all">
    .inpu{width:300px;} textarea{width:300px;height:100px;} .user_status {
        width: 10px;
        height: 10px;
        background: black;
        border: 1px solid black;
        margin-right: 5px;
    }

    #easyTooltip {
        color: #fff;
        background: rgba(0, 0, 0, 0.7);
        text-shadow: 0 1px 0 #262626;
        font-size: 11px;
        padding: 5px 7px 5px 7px;
        border: 0;
        margin-left: -2px;
        font-weight: bold;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        border-radius: 5px;
        -moz-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.28);
        -webkit-box-shadow: 0 1px 3px rgb(0 0 0 / 28%);
        box-shadow: 0 1px 3px rgb(0 0 0 / 28%);
    }

    .tooltip {
        background: url(../images/like_icons_bl.png) no-repeat center 0;
        width: 17px;
        height: 10px;
        margin-top: -1px;
        margin-left: 2px;
    }
</style>
<script>
    var Massaction = {
        save: function () {
            const data = {
                'num_fake': $('#num_fake').val(),
            };
            $.post('/adminpanel.php?mod=massaction&act=users', {
                save: JSON.stringify(data),
                saveconf: '',
            }, function (response) {
                // addAllErr(data.info);
                Page.addAllErr(response.info);
            });
        }
    }
</script>

<form action="{admin_index}" method="GET">

    <input type="hidden" name="mod" value="users"/>

    <div class="fllogall">Поиск по ID:</div>
    <input type="text" name="se_uid" class="inpu" value="{se_uid}"/>
    <div class="mgcler"></div>

    <div class="fllogall">Поиск по имени:</div>
    <input type="text" name="se_name" class="inpu" value="{se_name}"/>
    <div class="mgcler"></div>

    <div class="fllogall">Поиск по email:</div>
    <input type="text" name="se_email" class="inpu" value="{se_email}"/>
    <div class="mgcler"></div>

    <div class="fllogall">Бан:</div>
    <input type="checkbox" name="ban" style="margin-bottom:10px" {checked_ban} />
    <div class="mgcler"></div>

    <div class="fllogall">Удалены:</div>
    <input type="checkbox" name="delete" style="margin-bottom:10px" {checked_delete} />
    <div class="mgcler"></div>

    <div class="fllogall">Сортировка:</div>
    <select name="sort" class="inpu">
        <option value="0"></option>
        {selsorlist}
    </select>
    <div class="mgcler"></div>

    <div class="fllogall">&nbsp;</div>
    <input type="submit" value="Найти" class="inp" style="margin-top:0px"/>

</form>
<script type="text/javascript">
    function ckeck_uncheck_all() {
        var frm = document.editusers;
        for (var i = 0; i < frm.elements.length; i++) {
            var elmnt = frm.elements[i];
            if (elmnt.type == 'checkbox') {
                if (frm.master_box.checked == true) {
                    elmnt.checked = false;
                } else {
                    elmnt.checked = true;
                }
            }
        }
        if (frm.master_box.checked == true) {
            frm.master_box.checked = false;
        } else {
            frm.master_box.checked = true;
        }
    }

    var myhtml = {
        checkbox: function (id) {
            name = '#' + id;
            $(name).addClass('html_checked');
            if (ge('checknox_' + id)) {
                myhtml.checkbox_off(id);
            } else {
                $(name).append('<div id="checknox_' + id + '"><input type="hidden" id="' + id + '" /></div>');
                $(name).val('1');
            }
        },
        checkbox_off: function (id) {
            name = '#' + id;
            $('#checknox_' + id).remove();
            $(name).removeClass('html_checked');
            $(name).val('');
        },
        checked: function (arr) {
            $.each(arr, function () {
                myhtml.checkbox(this);
            });
        },
        title: function (id, text, prefix_id, pad_left) {
            if (!pad_left) pad_left = 5;
            $("body").append('<div id="js_title_' + id + '" class="js_titleRemove"><div id="easyTooltip">' + text + '</div><div class="tooltip"></div></div>');
            xOffset = $('#' + prefix_id + id).offset().left - pad_left;
            yOffset = $('#' + prefix_id + id).offset().top - 32;
            $('#js_title_' + id).css("position", "absolute").css("top", yOffset + "px").css("left", xOffset + "px").css("z-index", "1000").fadeIn('fast');
            $('#' + prefix_id + id).mouseout(function () {
                $('.js_titleRemove').remove();
            });
        },
        title_close: function (id) {
            $('#js_title_' + id).remove();
        },
        updateAjaxNav: function (gc, pref, num, page) {
            $.get('/updateAjaxNav', {
                gcount: gc,
                pref: pref,
                num: num,
                page: page
            }, function (data) {
                $('#nav').html(data);
            });
        },
        scrollTop: function () {
            $('.scroll_fix_bg').hide();
            $(window).scrollTop(0);
        }
    }
</script>
<form action="?mod=massaction&act=users" method="post" name="editusers">
    <div style="background:#f0f0f0;float:left;padding:5px;width:170px;text-align:center;font-weight:bold;margin-top:-5px">
        Пользователь
    </div>
    <div style="background:#f0f0f0;float:left;padding:5px;width:110px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">
        Дата регистрации
    </div>
    <div style="background:#f0f0f0;float:left;padding:5px;width:100px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">
        Дата посещения
    </div>
    <div style="background:#f0f0f0;float:left;padding:5px;width:148px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">
        E-mail
    </div>
    <div style="background:#f0f0f0;float:left;padding:5px;width:148px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">
        Статус
    </div>
    <div style="background:#f0f0f0;float:left;padding:4px;width:20px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">
        <input type="checkbox" name="master_box" title="Выбрать все" onclick="ckeck_uncheck_all()" style="float:right;">
    </div>
    <div class="clr"></div>
    {users}
    <div style="font:normal 11px Tahoma;padding:10px 5px">
        <div>Статусы:</div>
    </div>
    <div style="font:normal 11px Tahoma;padding:10px 5px;border-bottom:1px dashed #CCC;display: flex;">
        <div class="user_status" style="background: red" onmouseover="myhtml.title('1', 'Удален', 'legend', '1')"
             id="legend1"></div>
        <div class="user_status" style="background: blue" onmouseover="myhtml.title('2', 'Заблокирован', 'legend', '2')"
             id="legend2"></div>
        <div class="user_status" style="background: yellow"
             onmouseover="myhtml.title('3', 'Администратор', 'legend', '3')" id="legend3"></div>
        <div class="user_status" style="background: green"
             onmouseover="myhtml.title('4', 'Техподдержка', 'legend', '4')" id="legend4"></div>
        <div class="user_status" style="background: purple" onmouseover="myhtml.title('5', 'Проверен', 'legend', '5')"
             id="legend5"></div>
    </div>
    <div style="font:normal 11px Tahoma;padding:10px 5px;border-bottom:1px dashed #CCC;display: flex;">
        <div style="color:purple;margin: 5px;">Проверенные</div>
        <div style="color:red;margin: 5px;">Удаленные</div>
        <div style="color:blue;margin: 5px;">Заблокированные</div>
        <div style="color:green;margin: 5px;">Техподдержка</div>
    </div>
    <div style="float:right">
        <label>
            <select name="mass_type" class="inpu" style="width:260px">
                <option value="0">- Выберите действие -</option>
                <option value="1">Удалить пользователей</option>
                <option value="7">Воостановить пользователей</option>
                <option value="2">Заблокировать пользователей</option>
                <option value="9">Разблокировать пользователей</option>
                <option value="3">Удалить отправленные сообщения</option>
                <option value="4">Удалить комментарии к фото</option>
                <option value="5">Удалить комментарии к видео</option>
                <option value="11">Удалить комментарии к заметкам</option>
                <option value="6">Удалить записи на стенах</option>
                <option value="12">Начислить голоса</option>
                <option value="13">Забрать голоса</option>
                <option value="16">Перевести в «Техподдержка»</option>
                <option value="17">Перевести в «Пользователи»</option>
                <option value="18">Подтвердить аккаунт</option>
                <option value="19">Снять подтверждение</option>
            </select>
        </label>
        <input type="submit" value="Выолнить" class="inp"/>
        <button class="inp" onclick="">Выполнить</button>
    </div>
</form>
<div class="clr"></div>
{navigation}