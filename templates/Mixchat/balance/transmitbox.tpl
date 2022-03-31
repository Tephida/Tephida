<div class="miniature_box">
    <div class="miniature_pos" style="width:500px">
        <div class="payment_title">
            <img src="{ava}" width="50" height="50"/>
            <div class="fl_l">
                Вы собираетесь перевести mix другу на <b>MixNet</b>.<br/>
                Ваш текущий баланс: <b>{balance} mix</b>
            </div>
            <div class="fl_r">
                <a class="cursor_pointer" onClick="viiBox.clos('transmitBox', 1)">Закрыть</a>
            </div>
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
        <div class="payment_h2" style="text-align:center">Введите сколько хотите перевести mix:</div>
        <center>
            <input type="text" class="inpst payment_inp" maxlength="4" id="num_mix"/>
            <div class="rating_text_balance">Стоимость перевода <b>{cost} руб.</b></div>
        </center>
        <div class="button_div fl_l" style="margin-left:210px;margin-top:15px">
            <button onClick="transmit.send('{user-id}')" id="sending">Перевести</button>
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear" style="height:50px"></div>
</div>