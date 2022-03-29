<div class="h1" style="margin-top:10px">Фейковые люди</div>

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
                'num_fake': $('#num_fake').val(),
            };
            $.post('/adminpanel.php?mod=fake&act=people', {
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

    <div class="fllogall">количество</div>
    <input type="text" name="save[home]" id="num_fake" class="inpu" value="1"/>
    <div class="mgcler"></div>


    <div class="fllogall">&nbsp;</div>
    <input type="button" value="Сохранить" name="saveconf" onclick="Settings.save()" class="inp"
           style="margin-top:0px"/>
</div>


