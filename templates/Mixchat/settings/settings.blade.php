@extends('main.main')
@section('content')

    <div class="card">
        <div class="card-body">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/settings">@_e('settings')</a></li>
                    <li class="breadcrumb-item active" aria-current="page">@_e('all')</li>
                </ol>
            </nav>

            <div class="search_form_tab d-none" style="margin-top:-9px">
                <div class="buttonsprofile albumsbuttonsprofile buttonsprofileSecond" style="height:22px">
                    <div class="buttonsprofileSec"><a href="/settings" onClick="Page.Go(this.href); return false;">
                            <div><b>@_e('all')</b></div>
                        </a></div>
                    <a href="/settings/privacy/" onClick="Page.Go(this.href); return false;">
                        <div><b>@_e('settings_privacy')</b></div>
                    </a>
                    <a href="/settings/blacklist/" onClick="Page.Go(this.href); return false;">
                        <div><b>@_e('blacklist')</b></div>
                    </a>
                </div>
            </div>

            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">@_e('all')</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">@_e('settings_privacy')</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">@_e('blacklist')</a>
                </li>
            </ul>

            <div class="err_yellow name_errors {{ $code_1 }}" style="font-weight:normal;margin-top:25px">
                @_e('privacy_err_code1')
            </div>
            <div class="err_yellow name_errors {{ $code_2 }}" style="font-weight:normal;margin-top:25px">
                @_e('privacy_err_code2')
            </div>
            <div class="err_yellow name_errors {{ $code_3 }}" style="font-weight:normal;margin-top:25px">
                @_e('privacy_err_code3')
            </div>
            <div class="margin_top_10"></div>
            <div class="allbar_title">@_e('edit_pass')</div>
            <div class="err_red no_display pass_errors" id="err_pass_1" style="font-weight:normal;">@_e('err_pass_1')
            </div>
            <div class="err_red no_display pass_errors" id="err_pass_2" style="font-weight:normal;">@_e('err_pass_2')
            </div>
            <div class="err_yellow no_display pass_errors" id="ok_pass" style="font-weight:normal;">@_e('ok_pass')</div>
            <div class="texta">@_e('old_pass')</div>
            <input type="password" id="old_pass" class="inpst" maxlength="100" style="width:150px;"/>
            <span id="validOldpass"></span>
            <div class="mgclr"></div>
            <div class="texta">@_e('new_pass')</div>
            <input type="password" id="new_pass" class="inpst" maxlength="100" style="width:150px;"
                   onMouseOver="myhtml.title('', 'Пароль должен быть не менее 6 символов в длину', 'new_pass')"/>
            <span id="validNewpass"></span>
            <div class="mgclr"></div>
            <div class="texta">@_e('new_pass2')</div>
            <input type="password" id="new_pass2" class="inpst" maxlength="100" style="width:150px;"
                   onMouseOver="myhtml.title('', 'Введите еще раз новый пароль', 'new_pass2')"/><span
                    id="validNewpass2"></span>
            <div class="mgclr"></div>
            <div class="texta">&nbsp;</div>
            <button onClick="settings.saveNewPwd(); return false" id="saveNewPwd" class="btn btn-success">
                @_e('saveNewPwd')
            </button>
            <div class="mgclr"></div>
            <div class="margin_top_10"></div>
            <div class="allbar_title">@_e('edit_name')</div>
            <div class="err_red no_display name_errors" id="err_name_1" style="font-weight:normal;">@_e('err_name_1')
            </div>
            <div class="err_yellow no_display name_errors" id="ok_name" style="font-weight:normal;">@_e('ok_name')</div>
            <div class="texta">@_e('new_name')</div>
            <label for="name"></label>
            <input type="text" id="name" class="inpst" maxlength="100" style="width:150px;"
                   value="{{ $user['user_info']['user_name'] }}"/>
            <span id="validName"></span>
            <div class="mgclr"></div>
            <div class="texta">@_e('new_surname')</div>
            <label for="lastname"></label>
            <input type="text" id="lastname" class="inpst" maxlength="100" style="width:150px;"
                   value="{{ $user['user_info']['user_lastname'] }}"/>
            <span id="validLastname"></span>
            <div class="mgclr"></div>
            <div class="texta">&nbsp;</div>
            <button onClick="settings.saveNewName(); return false" id="saveNewName" class="btn btn-success">
                @_e('edit_name')
            </button>
            <div class="mgclr"></div>
            <div class="margin_top_10"></div>
            <div class="allbar_title">@_e('email_edit')</div>
            <div class="err_yellow name_errors no_display" id="ok_email" style="font-weight:normal;">@_e('ok_email')
            </div>
            <div class="err_red no_display name_errors" id="err_email" style="font-weight:normal;">@_e('rrr')</div>
            <div class="texta">@_e('email_old')</div>
            <div style="color:#555;margin-top:13px;margin-bottom:10px">{{ $user['user_info']['user_email'] }}</div>
            <div class="mgclr"></div>
            <div class="texta">@_e('email_new')</div>
            <label for="email"></label>
            <input type="text" id="email" class="inpst" maxlength="100" style="width:150px;"/>
            <span id="validName"></span>
            <div class="mgclr"></div>
            <div class="texta">&nbsp;</div>
            <button onClick="settings.savenewmail(); return false" id="saveNewEmail" class="btn btn-success">
                @_e('saveNewEmail')
            </button>
            <div class="mgclr"></div>

            {{--                        <div class="allbar_title">@_e('time_title')</div>
                                    <div class="err_yellow no_display" id="ok_timez" style="font-weight:normal;">@_e('ok_timez')</div>
                                    <div class="texta">Текущее время</div>
                                    --}}{{--                    <div style="color:#555;margin-top:13px;margin-bottom:10px">{{ $date_today }}</div>--}}{{--
                                    <div class="texta">@_e('time_zona')</div>
                                    <label for="timezona"></label>
                                    <select id="timezona" class="inpst" style="width:200px">
                                        <option value="0">@_e('time_zona_no_select')</option>
                                        {{ $timezs }}
                                    </select>
                                    <div class="mgclr"></div>

                                    <div class="texta">&nbsp;</div>
                                    <div class="button_div fl_l">
                                        <button onClick="settings.savetimezona(); return false" id="saveTimezona">@_e('save')</button>
                                    </div>
                                    <div class="mgclr"></div>--}}

            <div class="nSDelPg">@_e('you_make') <a class="cursor_pointer"
                                                    onClick="delMyPage()">@_e('remove_profile')</a>.
            </div>
        </div>
    </div>
@endsection