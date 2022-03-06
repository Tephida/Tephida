<style media="all">
    .inpu{width:300px;} textarea

    {width:300px;height:100px;}
</style>

<form action="{admin_index}" method="GET">

    <input type="hidden" name="mod" value="groups"/>

    <div class="fllogall">Поиск по ID сообщества:</div>
    <input type="text" name="se_uid" class="inpu" value="{se_uid}"/>
    <div class="mgcler"></div>

    <div class="fllogall">Поиск по названию:</div>
    <input type="text" name="se_name" class="inpu" value="{se_name}"/>
    <div class="mgcler"></div>

    <div class="fllogall">Поиск по ID создателя:</div>
    <input type="text" name="se_user_id" class="inpu" value="{se_user_id}"/>
    <div class="mgcler"></div>

    <div class="fllogall">Бан:</div>
    <input type="checkbox" name="ban" style="margin-bottom:10px" {checked_ban} />
    <div class="mgcler"></div>

    <div class="fllogall">Удалены:</div>
    <input type="checkbox" name="delet" style="margin-bottom:10px" {checked_delet} />
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

<div class="h1" style="margin-top:10px"><a name="video"></a>Список сообществ {groups_num}</div>

<script type="text/javascript">
    function ckeck_uncheck_all() {
        var frm = document.edit;
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
</script>
<form action="?mod=massaction&act=groups" method="post" name="edit">
    <div style="background:#f0f0f0;float:left;padding:5px;width:100px;text-align:center;font-weight:bold;margin-top:-5px">
        Создатель
    </div>
    <div style="background:#f0f0f0;float:left;padding:5px;width:243px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">
        Сообщество
    </div>
    <div style="background:#f0f0f0;float:left;padding:5px;width:75px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">
        Участников
    </div>
    <div style="background:#f0f0f0;float:left;padding:5px;width:110px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">
        Дата создания
    </div>
    <div style="background:#f0f0f0;float:left;padding:4px;width:20px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">
        <input type="checkbox" name="master_box" title="Выбрать все" onclick="ckeck_uncheck_all()" style="float:right;">
    </div>
    <div class="clr"></div>
    {groups}
    <div style="float:left;font-size:10px">
        <font color="red">Удаленные сообщества помечены красным цветом</font><br/>
        <font color="blue">Забаненые сообщества помечены синим цветом</font>
    </div>
    <div style="float:right">
        <select name="mass_type" class="inpu" style="width:260px">
            <option value="0">- Действие -</option>
            <option value="1">Удалить сообщества</option>
            <option value="2">Заблокировать сообщества</option>
            <option value="3">Воостановить сообщества</option>
            <option value="4">Разблокировать сообщества</option>
        </select>
        <input type="submit" value="Выолнить" class="inp"/>
    </div>
</form>
<div class="clr"></div>
{navigation}