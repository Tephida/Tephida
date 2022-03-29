@extends('main.main')
@section('content')
    <div class="buttonsprofile">
        <div class="buttonsprofileSec"><a href="/balance" onClick="Page.Go(this.href); return false;">Личный счёт</a>
        </div>
        {{--        <a href="/balance?act=invite" onClick="Page.Go(this.href); return false;">Пригласить друга</a>--}}
        {{--        <a href="/balance?act=invited" onClick="Page.Go(this.href); return false;">Приглашённые друзья</a>--}}
    </div>
    <div class="msg_speedbar clear">Состояние личного счёта</div>
    <div class="ubm_descr border_radius_5">
        <div class="balanmce_text">
            <b>Mix</b> – это универсальная валюта для всех приложений на нашем сайте. Mix можно заработать, выиграть и
            купить.
            Стоимость <b>1 Mix</b>, равна <b>{{ $cost }} руб</b>.
            <div style="text-align: center;">
                <span class="color777">На Вашем счёте:</span>&nbsp;&nbsp;
                <b><span id="num2">{{ $ubm }}</span> mix</b> и
                <b><span id="rub2">{{ $rub }}</span> {{ $text_rub }}</b>
            </div>
            <center></center>
            <div class="button_div fl_l" style="line-height:15px;margin-left:172px;margin-top:15px">
                <button onClick="payment.box_two();" style="width:161px">Купить mix</button>
            </div>
            <div class="button_div_gray fl_l" style="line-height:15px;margin-left:172px;margin-top:15px">
                <button onClick="payment.box()" style="width:161px">Пополнить баланс</button>
            </div>
{{--            <div class="button_div_gray fl_l" style="line-height:15px;margin-left:172px;margin-top:15px">
                <button onClick="alert('{msg}')" style="width:161px">Вывести деньги</button>
            </div>--}}
{{--            <div class="button_div fl_l" style="line-height:15px;margin-left:172px;margin-top:15px">
                <button onClick="payment.box_tree();" style="width:161px">Обменять mix на руб.</button>
            </div>--}}
            <div class="clear"></div>
        </div>
    </div>
@endsection