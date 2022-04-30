@extends('main.main')
@section('content')
    <div class="reg_box_pad" style="padding-bottom:40px">
        <div class="err_red no_display" id="err_reg_3" style="font-weight:normal;"></div>
        <div class="videos_text">Ваше имя</div>
        <input type="text" class="videos_input" style="width:163px" placeholder="Введите имя" maxlength="30"
               id="reg_name"/>
        <div class="videos_text">Ваша фамилия</div>
        <input type="text" class="videos_input" style="width:163px" placeholder="Введите фамилию" maxlength="30"
               id="reg_lastname"/>
        <div class="videos_text">Пароль</div>
        <input type="password" class="videos_input" style="width:163px" placeholder="Введите пароль" maxlength="60"
               id="reg_pass1"/>
        <div class="videos_text">Повторите пароль</div>
        <input type="password" class="videos_input" style="width:163px" placeholder="Введите еще раз пароль"
               maxlength="60"
               id="reg_pass2"/>
        <div class="clear"></div>
        <div class="button_div fl_l" style="margin-top:5px">
            <button style="width:175px;height:24px;font-size:11px;padding-top:3px" onClick="reg.end('{{ $hash }}')"
                    id="reg_fini">Завершить регистрацию
            </button>
        </div>
    </div>
@endsection