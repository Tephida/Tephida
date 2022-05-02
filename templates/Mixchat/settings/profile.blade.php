@extends('main.main')
@section('content')
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Основное</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Контакты</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Интересы</a>
                </li>
            </ul>

            <div class="err_yellow" id="info_save" style="display:none;font-weight:normal;"></div>
            <div class="clear"></div>
            <div class="texta">Пол:</div>
            <div class="padstylej">
                <label for="sex"></label>
                <select id="sex" class="inpst" onChange="sp.check()">
                    <option value="0">- Не выбрано -</option>
                    {{ $sex }}
                </select>
            </div>
            <div class="mgclr"></div>
            <div class="[sp-all]no_display[/sp-all]" id="sp_block">
                <div class="texta">Семейное положение:</div>
                <div class="padstylej">
                    @if($gender == 'male')
                        <div class="" id="sp_sel_m">
                            <select id="sp" class="inpst" onChange="sp.openfriends()">
                                <option value="0">- Не выбрано -</option>
                                <option value="1" [instSelect-sp-1]>Не женат</option>
                                <option value="2" [instSelect-sp-2]>Есть подруга</option>
                                <option value="3" [instSelect-sp-3]>Помовлен</option>
                                <option value="4" [instSelect-sp-4]>Женат</option>
                                <option value="5" [instSelect-sp-5]>Влюблён</option>
                                <option value="6" [instSelect-sp-6]>Всё сложно</option>
                                <option value="7" [instSelect-sp-7]>В активном поиске</option>
                            </select>
                        </div>
                    @else
                        <div class="" id="sp_sel_w">
                            <select id="sp_w" class="inpst" onChange="sp.openfriends()">
                                <option value="0">- Не выбрано -</option>
                                <option value="1" [instSelect-sp-1]>Не замужем</option>
                                <option value="2" [instSelect-sp-2]>Есть друг</option>
                                <option value="3" [instSelect-sp-3]>Помовлена</option>
                                <option value="4" [instSelect-sp-4]>Замужем</option>
                                <option value="5" [instSelect-sp-5]>Влюблена</option>
                                <option value="6" [instSelect-sp-6]>Всё сложно</option>
                                <option value="7" [instSelect-sp-7]>В активном поиске</option>
                            </select>
                        </div>
                    @endif
                </div>
                <div class="mgclr"></div>
            </div>
            <div class="[sp]no_display[/sp]" id="sp_type">
                <div class="texta" id="sp_text">{{ $sp_text }}</div>
                <div class="padstylej fl_l">
                    <div style="margin-top:3px;margin-bottom:10px;padding-left:1px;float:left">
                        <a href="/" id="sp_name" onClick="sp.openfriends(); return false">{{ $sp_name }}</a>
                    </div>
                    <img src="/images/close_a_wall.png" class="sp_del" onClick="sp.del()"/>
                </div>
                <div class="mgclr"></div>
                <input type="hidden" id="sp_val"/>
            </div>
            <div class="texta">Дата рождения:</div>
            <div class="padstylej"><select id="day" class="inpst">
                    <option>- День -</option>
                    {{ $user_day }}
                </select>
                <select id="month" class="inpst">
                    <option>- Месяц -</option>
                    {{ $user_month }}
                </select>
                <select id="year" class="inpst">
                    <option>- Год -</option>{{ $user_year }}
                </select></div>
            <div class="mgclr"></div>
            <div class="texta">Страна:</div>
            <div class="padstylej"><select id="country" class="inpst"
                                           onChange="Profile.LoadCity(this.value); return false;">
                    <option value="0">- Не выбрано -</option>
                    {{ $country }}
                </select><img src="/images/loading_mini.gif" alt="" class="load_mini" id="load_mini"/></div>
            <div class="mgclr"></div>
            <span id="city"><div class="texta">Город:</div>
 <div class="padstylej"><select id="select_city" class="inpst">
  <option value="0">- Не выбрано -</option>
  {{ $city }}
 </select><img src="/images/loading_mini.gif" alt="" class="load_mini" id="load_mini"/></div>
<div class="mgclr"></div></span>
            <div class="texta">&nbsp;</div>
            <div class="button_div fl_l">
                <button id="saveform">Сохранить</button>
            </div>
            <div class="mgclr"></div>

        </div>
    </div>
@endsection