@extends('main.main')
@section('content')

    <div class="card">
        <div class="card-body">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/settings">@_e('settings')</a></li>
                    <li class="breadcrumb-item active" aria-current="page">@_e('settings_general')</li>
                </ol>
            </nav>
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">@_e('settings_general')</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">@_e('settings_privacy')</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">@_e('settings_blacklist')</a>
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

            <h3 class="mt-3">@_e('edit_pass')</h3>
            <div class="p-3">
                <div class="mb-3">
                    <label for="old_pass" class="form-label">@_e('old_pass')</label>
                    <input type="password" class="form-control" id="old_pass" style="width:150px;">
                </div>
                <div class="mb-3">
                    <label for="new_pass" class="form-label">@_e('new_pass')</label>
                    <input type="password" class="form-control" id="new_pass" style="width:150px;">
                </div>
                <div class="mb-3">
                    <label for="new_pass2" class="form-label">@_e('new_pass2')</label>
                    <input type="password" class="form-control" id="new_pass2" style="width:150px;">
                </div>
                <button onClick="settings.saveNewPwd(); return false" id="saveNewPwd" class="btn btn-primary">
                    @_e('save')
                </button>
            </div>

            <h3>@_e('edit_name')</h3>
            <div class="p-3">
                <div class="err_red no_display name_errors" id="err_name_1"></div>
                <div class="mb-3">
                    <label for="name" class="form-label">@_e('new_name')</label>
                    <input type="text" id="name" class="form-control" maxlength="100" style="width:150px;"
                           value="{{ $user['user_info']['user_name'] }}"/>
                </div>
                <div class="mb-3">
                    <label for="lastname" class="form-label">@_e('new_surname')</label>
                    <input type="text" id="lastname" class="form-control" maxlength="100" style="width:150px;"
                           value="{{ $user['user_info']['user_lastname'] }}"/>
                </div>
                <button onClick="settings.saveNewName(); return false" id="saveNewName" class="btn btn-primary">
                    @_e('save')
                </button>
            </div>

            <h3>@_e('email_edit')</h3>
            <div class="p-3">
                <div class="err_red no_display name_errors" id="err_name_1"></div>
                <div class="mb-3">
                    <label for="name" class="form-label">@_e('email_old')</label>
                    <input type="text" id="name" class="form-control" maxlength="100" style="width:150px;"
                           value="{{ $user['user_info']['user_email'] }}"/>
                </div>
                <div class="mb-3">
                    <label for="lastname" class="form-label">@_e('email_new')</label>
                    <input type="text" id="email" class="form-control" maxlength="100" style="width:150px;"
                           value=""/>
                </div>
                <button onClick="settings.savenewmail(); return false" id="saveNewEmail" class="btn btn-primary">
                    @_e('save')
                </button>
            </div>


            {{--                                    <div class="allbar_title">@_e('time_title')</div>--}}
            {{--                                    <div class="err_yellow no_display" id="ok_timez" style="font-weight:normal;">@_e('ok_timez')</div>--}}
            {{--                                    <div class="texta">Текущее время</div>--}}
            {{--                                                        <div style="color:#555;margin-top:13px;margin-bottom:10px">{{ $date_today }}</div>--}}
            {{--                                    <div class="texta">@_e('time_zona')</div>--}}
            {{--                                    <label for="timezona"></label>--}}
            {{--                                    <select id="timezona" class="inpst" style="width:200px">--}}
            {{--                                        <option value="0">@_e('time_zona_no_select')</option>--}}
            {{--                                        {{ $timezs }}--}}
            {{--                                    </select>--}}
            {{--                                    <div class="mgclr"></div>--}}

            {{--                                    <div class="texta">&nbsp;</div>--}}
            {{--                                    <div class="button_div fl_l">--}}
            {{--                                        <button onClick="settings.savetimezona(); return false" id="saveTimezona">@_e('save')</button>--}}
            {{--                                    </div>--}}
            {{--                                    <div class="mgclr"></div>--}}

            <div class="nSDelPg">@_e('you_make') <a class="cursor_pointer"
                                                    onClick="delMyPage()">@_e('remove_profile')</a>.
            </div>
        </div>
    </div>
@endsection