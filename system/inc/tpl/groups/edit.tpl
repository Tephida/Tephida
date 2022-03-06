<div class="h1" style="margin-top:10px"><a name="video"></a>Редактирование сообщества</div>

<style media="all">
    .inpu{width:447px;} textarea

    {width:450px;height:100px;}
</style>

<form action="" method="POST">

    <input type="hidden" name="mod" value="notes"/>

    <div class="fllogall" style="width:140px">Название:</div>
    <label>
        <input type="text" name="title" class="inpu" value="{title}"/>
    </label>
    <div class="mgcler"></div>

    <div class="fllogall" style="width:140px">Описание:</div>
    <label>
        <textarea name="descr" class="inpu">{descr}</textarea>
    </label>
    <div class="mgcler"></div>

    <div class="fllogall" style="width:140px">Комментарии включены:</div>
    <label>
        <input type="checkbox" name="comments" style="margin-bottom:10px" {checked} />
    </label>
    <div class="mgcler"></div>

    <div class="fllogall" style="width:140px">Удалить фото:</div>
    <label>
        <input type="checkbox" name="del_photo" style="margin-bottom:10px"/>
    </label>
    <div class="mgcler"></div>

    <div class="fllogall" style="width:140px">&nbsp;</div>
    <input type="submit" value="Сохранить" class="inp" name="save" style="margin-top:0"/>
    <input type="submit" value="Назад" class="inp" style="margin-top:0" onClick="history.go(-1); return false"/>

</form>