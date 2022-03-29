<script type="text/javascript">
    $(document).ready(function(){
        payment.update();
    });
</script>
<div class="miniature_box">
    <div class="miniature_pos">
        <div class="payment_title">
            <img src="{{ $ava }}" width="50" height="50" />
            <div class="">
                Вы собираетесь пополнить Ваш счёт mix <b>MixNet</b>.<br />
                Ваш текущий баланс: <b>{{ $balance }} mix</b>
            </div>
{{--            <div class="fl_r">--}}
{{--                <a class="cursor_pointer" onClick="viiBox.clos('payment_2', 1)">Закрыть</a>--}}
{{--            </div>--}}
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
        <div class="payment_h2" style="text-align:center">Введите желаемое количество mix:</div>
        <center>
            <input type="text" class="inpst payment_inp" maxlength="4" id="cost_balance" onKeyUp="payment.update()" />
            <div class="rating_text_balance">У Вас <span id="rt">останется</span> <b id="num">{{ $rub }}</b> руб.</div>
            <input type="hidden" id="balance" value="{{ $rub }}" />
            <input type="hidden" id="cost" value="{{ $cost }}" />
        </center>
        <div class="button_div fl_l" style="margin-left:210px;margin-top:15px"><button onClick="payment.send()" id="saverate">Оплатить</button></div>
        <div class="clear"></div>
    </div>
    <div class="clear" style="height:50px"></div>
</div>