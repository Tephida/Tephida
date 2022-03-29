<script type="text/javascript">
    $(document).ready(function () {
        vii_interval = setInterval('im.updateDialogs()', 2000);
        pHate = location.hash.replace('#', '');
        if (pHate)
            im.open(pHate);
    });
</script>
<style>.audio_onetrack{background:none}</style>
<div class="im_flblock">
    <a href="/" style="font-weight:normal" onClick="imRoom.createBox(this); return false">Создать беседу</a>
    <div class="clear"></div>
    <span id="updateDialogs"></span>
    <div id="alldialogs" class="d-flex flex-column">{dialogs}</div>
    <div class="clear"></div>
</div>
<div class="im_head fl_l" id="imViewMsg">
    <div class="info_center" style="padding-top:205px;padding-bottom:205px;margin-top:4px;margin-right:10px">
        <div>
            <img src="/images/im.png"/>
            <br/>Вы можете выбрать собеседника из левой колоны и начать с ним общение
            в онлайн режиме, без обновления страницы.
        </div>
    </div>
</div>