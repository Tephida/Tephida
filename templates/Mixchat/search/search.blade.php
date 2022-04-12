@extends('main.main')
@section('content')
    <script type="text/javascript">
        $(document).ready(function () {
            @if($search_tab || $type === 0)
            $('#page').css('min-height', '444px');
            $(window).scroll(function () {
                if ($(window).scrollTop() > 103)
                    $('.search_sotrt_tab').css('position', 'fixed').css('margin-top', '-10px');
                else
                    $('.search_sotrt_tab').css('position', 'absolute').css('margin-top', '160px');
            });
            @endif
            {{--            myhtml.checked(['{{ $checked_online }}', '{{ $checked_user_photo }}']);--}}
            var query = $('#query_full').val();
            if (query == 'Начните вводить любое слово или имя')
                $('#query_full').css('color', '#c1cad0');
        });
    </script>
    <style>.site_menu_fix {
            overflow-y: auto;
        }</style>
    <div class="">
        <div class="">
            <div class="search_form_tab">
                {{--                <div class="col-12 d-flex justify-content-around mt-3">--}}
                {{--                    <input type="text" value="{{ $query_search }}" class="fave_input" id="query_full"--}}
                {{--                           onBlur="if(this.value===''){this.value='Начните вводить любое слово или имя';this.style.color = '#c1cad0';}"--}}
                {{--                           onFocus="if(this.value==='Начните вводить любое слово или имя'){this.value='';this.style.color = '#000'}"--}}
                {{--                           onKeyPress="if(event.keyCode === 13)gSearch.go();"--}}
                {{--                           style="margin:0;color:#000"--}}
                {{--                           maxlength="65" />--}}
                {{--                    <div class="button_div fl_r"><button onClick="gSearch.go(); return false">Поиск</button></div>--}}
                {{--                </div>--}}

                {{--                <div class="buttonsprofile albumsbuttonsprofile buttonsprofileSecond d-none" style="margin-top:10px;height:22px">--}}
                {{--                    <div class="{activetab-1}"><a href="/search/?{{ $query_people }}" onClick="Page.Go(this.href); return false;"><div><b>Все</b></div></a></div>--}}
                {{--                    <div class="{activetab-1}"><a href="/search/?{{ $query_people }}" onClick="Page.Go(this.href); return false;"><div><b>Люди</b></div></a></div>--}}
                {{--                    <div class="{activetab-4}"><a href="/search/?{{ $query_groups }}" onClick="Page.Go(this.href); return false;"><div><b>Сообщества</b></div></a></div>--}}
                {{--                    <div class="{activetab-5}"><a href="/search/?{{ $query_audios }}" onClick="Page.Go(this.href); return false;"><div><b>Аудиозаписи</b></div></a></div>--}}
                {{--                    <div class="{activetab-2}"><a href="/search/?{{ $query_videos }}" onClick="Page.Go(this.href); return false;"><div><b>Видеозаписи</b></div></a></div>--}}
                {{--                </div>--}}
                <div class="site_menu_fix">
                    <a href="/search/?{{ $query_all }}" onclick="Page.Go(this.href); return false;" class="left_row">
      <span class="left_label inl_bl ml-5">
       <svg class="bi bi-files" width="24" height="24" viewBox="0 0 16 16" fill="currentColor"
            xmlns="http://www.w3.org/2000/svg">
  <path fill-rule="evenodd"
        d="M3 2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zm0 1a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H3z"/>
  <path d="M5 0h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2v-1a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1H3a2 2 0 0 1 2-2z"/>
</svg>@_e('all')</span>
                    </a>
                    <a href="/search/?{{ $query_people }}" onclick="Page.Go(this.href); return false;" class="left_row">
      <span class="left_label inl_bl  ml-5">
                 <svg class="bi bi-files" width="24" height="24" viewBox="0 0 16 16" fill="currentColor"
                      xmlns="http://www.w3.org/2000/svg">
  <path fill-rule="evenodd"
        d="M3 2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zm0 1a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H3z"/>
  <path d="M5 0h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2v-1a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1H3a2 2 0 0 1 2-2z"/>
</svg>@_e('people')</span>
                    </a>

                    @if($search_tab || $type === 0)
                        <div class="ml-5 mb-3">

                            <b>Основное</b>
                            <div class="search_clear"></div>

                            <div class="padstylej">
                                <label for="country"></label>
                                <select name="country" id="country" class="inpst search_sel"
                                        onChange="Profile.LoadCity(this.value); gSearch.go();">
                                    <option value="0">Любая страна</option>{{ $country }}</select>
                                <img src="/images/loading_mini.gif" alt="" class="load_mini" id="load_mini"/>
                            </div>
                            <div class="search_clear"></div>

                            <div class="padstylej">
                                <label for="select_city"></label>
                                <select name="city" id="select_city" class="inpst search_sel" onChange="gSearch.go();">
                                    <option value="0">Любой город</option>{{ $city }}</select>
                            </div>
                            <div class="search_clear"></div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-check form-switch mb-3" id="Online">
                                        <input class="form-check-input" type="checkbox" id="SwitchCheckOnline"
                                               onChange="/*myhtml.checkbox(this.id);*/ gSearch.go()" {{ $checked_online }}>
                                        <input type="hidden" id="SwitchCheckOnline_r" value="0"/>
                                        <label class="form-check-label" for="SwitchCheckOnline">сейчас на сайте</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch mb-3" id="UserPhoto">
                                        <input class="form-check-input" type="checkbox" id="SwitchCheckUserPhoto"
                                               onChange="/*checkbox(this.id);*/ gSearch.go()" {{ $checked_user_photo }}>
                                        <label class="form-check-label" for="SwitchCheckUserPhoto">с фотографией</label>
                                    </div>
                                </div>
                            </div>

                            <div class="search_clear"></div>

                            <b>Пол</b>
                            <div class="search_clear"></div>

                            <div class="padstylej">
                                <label for="sex"></label>
                                <select name="sex" id="sex" class="inpst search_sel" onChange="gSearch.go();">
                                    <option value="0">Все</option>{{ $sex }}</select></div>
                            <div class="search_clear"></div>

                            <b>День рождения</b>
                            <div class="search_clear"></div>

                            <div class="padstylej">
                                <label for="day"></label>
                                <select name="day" class="inpst search_sel" id="day" onChange="gSearch.go();">
                                    <option value="0">Любой день</option>{{ $day }}</select>
                                <div class="search_clear"></div>

                                <label for="month"></label>
                                <select name="month" class="inpst search_sel" id="month" onChange="gSearch.go();">
                                    <option value="0">Любой месяц</option>{{ $month }}</select>
                                <div class="search_clear"></div>

                                <label for="year"></label>
                                <select name="year" class="inpst search_sel" id="year" onChange="gSearch.go();">
                                    <option value="0">Любой год</option>{{ $year }}</select></div>
                            <div class="search_clear"></div>

                        </div>@endif

                    <a href="/search/?{{ $query_groups }}" onclick="Page.Go(this.href); return false;" class="left_row">
      <span class="left_label inl_bl  ml-5">
                 <svg class="bi bi-files" width="24" height="24" viewBox="0 0 16 16" fill="currentColor"
                      xmlns="http://www.w3.org/2000/svg">
  <path fill-rule="evenodd"
        d="M3 2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zm0 1a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H3z"/>
  <path d="M5 0h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2v-1a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1H3a2 2 0 0 1 2-2z"/>
</svg>
          Сообщества</span>
                    </a>
                    <a href="/search/?{{ $query_audios }}" onclick="Page.Go(this.href); return false;" class="left_row">
      <span class="left_label inl_bl  ml-5">
          <svg class="bi bi-files" width="24" height="24" viewBox="0 0 16 16" fill="currentColor"
               xmlns="http://www.w3.org/2000/svg">
  <path fill-rule="evenodd"
        d="M3 2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zm0 1a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H3z"/>
  <path d="M5 0h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2v-1a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1H3a2 2 0 0 1 2-2z"/>
</svg>
          Аудиозаписи</span>
                    </a>
                    <a href="/search/?{{ $query_videos }}" onclick="Page.Go(this.href); return false;" class="left_row">
      <span class="left_label inl_bl  ml-5">
                 <svg class="bi bi-files" width="24" height="24" viewBox="0 0 16 16" fill="currentColor"
                      xmlns="http://www.w3.org/2000/svg">
  <path fill-rule="evenodd"
        d="M3 2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zm0 1a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H3z"/>
  <path d="M5 0h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2v-1a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1H3a2 2 0 0 1 2-2z"/>
</svg>
          Видеозаписи</span>
                    </a>
                </div>

                <input type="hidden" value="{{ $type }}" id="se_type_full"/>
            </div>

            <div class="clear"></div>
            @if($search)
                <div class="margin_top_10"></div>
                <div class="search_result_title">Найдено {{ $count }}</div>@endif
            <div id="jquery_jplayer"></div>
            <input type="hidden" id="teck_id" value="0"/>
            <input type="hidden" id="typePlay" value="standart"/>
            <input type="hidden" id="teck_prefix" value=""/>
        </div>
        <div class="">
            @if($type === 0)
                @include('search.people', $search)
            @elseif($type === 1)
                @include('search.video', $search)
            @elseif($type === 2)

                <div class="card mt-3">
                    <a href="" style="text-decoration:none" onclick="Page.Go(this.href); return false">
                        <div class="albtitle">Люди<span class="pl-2">{{ $users_count }}</span></div>
                    </a>
                    <div class="d-flex">
                        @foreach($last_users as $row)
                            <div id="link_tag_{{ $row['user_id'] }}_{{ $row['user_id'] }}" class="newmesnobg"
                                 style="padding:0px;padding-top:10px;">
                                <div class="onefriend">
                                    <a href="/u{{ $row['user_id'] }}" onclick="Page.Go(this.href); return false">
                                        <div>
                                            <img src="{{ $row['ava'] }}" alt="{{ $row['name'] }}" class="avatar-img"
                                                 style="width: 120px;height: 120px;"
                                                 onmouseover="wall.showTag({{ $row['user_id'] }}, {{ $row['user_id'] }}, 1)"
                                                 onmouseout="wall.hideTag({{ $row['user_id'] }}, {{ $row['user_id'] }}, 1)">
                                        </div>
                                        <div>
                                            {{ $row['name'] }}
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>

                <div class="card mt-3">
                    <a href="" style="text-decoration:none" onclick="Page.Go(this.href); return false">
                        <div class="albtitle">Сообщества<span class="pl-2">{{ $users_count }}</span></div>
                    </a>
                    <div class="d-flex">
                        @foreach($last_groups as $row)
                            <div id="link_tag_{{ $row['public_id'] }}_{{ $row['public_id'] }}" class="newmesnobg"
                                 style="padding:0px;padding-top:10px;">
                                <div class="onefriend">
                                    <a href="/public{{ $row['public_id'] }}" onclick="Page.Go(this.href); return false">
                                        <div>
                                            <img src="{{ $row['ava'] }}" alt="{{ $row['name'] }}" class="avatar-img"
                                                 style="width: 120px;height: 120px;"
                                                 onmouseover="wall.showTag({{ $row['public_id'] }}, {{ $row['id'] }}, 1)"
                                                 onmouseout="wall.hideTag({{ $row['public_id'] }}, {{ $row['public_id'] }}, 1)">
                                        </div>
                                        <div>
                                            {{ $row['name'] }}
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>

                <div class="card mt-3">
                    <a href="" style="text-decoration:none" onclick="Page.Go(this.href); return false">
                        <div class="albtitle">Аудиозаписи<span class="pl-2">{{ $audios_count }}</span></div>
                    </a>
                    @foreach($audios as $row)
                        <div class="audioPage audioElem search search_item"
                             id="audio_{$row['id']}_{$row['oid']}_{$plname}"
                             onclick="playNewAudio('{$row['id']}_{$row['oid']}_{$plname}', event);">
                            <div class="area">
                                <table cellspacing="0" cellpadding="0" width="100%">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <div class="audioPlayBut new_play_btn">
                                                <div class="bl">
                                                    <div class="figure"></div>
                                                </div>
                                            </div>
                                            <input type="hidden" value="{$row['url']},{$row['duration']},page"
                                                   id="audio_url_{$row['id']}_{$row['oid']}_{$plname}">
                                        </td>
                                        <td class="info">
                                            <div class="audioNames"><b class="author"
                                                                       onclick="Page.Go('/?go=search&query=&type=5&q='+this.innerHTML);"
                                                                       id="artist">{{ $row['artist'] }}</b> – <span
                                                        class="name" id="name">{{ $row['title'] }}</span>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="audioElTime"
                                                 id="audio_time_{$row['id']}_{$row['oid']}_{$plname}">{$stime}
                                            </div>
                                            <div class="vk_audio_dl_btn cursor_pointer fl_l" href="{$row['url']}"
                                                 style="
position: absolute;
right: 28px;
top: 9px;
display: none;
" onclick="vkDownloadFile(this,'{$row['artist']} - {$row['title']} - kalibri.co.ua');
cancelEvent(event);" onMouseOver="myhtml.title('{$row['id']}', 'Скачать песню', 'ddtrack_', 4)"
                                                 id="ddtrack_{$row['id']}"></div>
                                            <div class="audioSettingsBut">
                                                <li class="icon-plus-6"
                                                    onClick="gSearch.addAudio('{$row['id']}_{$row['oid']}_{$plname}')"
                                                    onmouseover="showTooltip(this, {text: 'Добавить в мой список', shift: [6,5,0]});"
                                                    id="no_play"></li>
                                                <div class="clear"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div id="player{$row['id']}_{$row['oid']}_{$plname}" class="audioPlayer" border="0"
                                     cellpadding="0">
                                    <table cellspacing="0" cellpadding="0" width="100%">
                                        <tbody>
                                        <tr>
                                            <td style="width: 100%;">
                                                <div class="progressBar fl_l" style="width: 100%;"
                                                     onclick="cancelEvent(event);"
                                                     onmousedown="audio_player.progressDown(event, this);" id="no_play"
                                                     onmousemove="audio_player.playerPrMove(event, this)"
                                                     onmouseout="audio_player.playerPrOut()">
                                                    <div class="audioTimesAP" id="main_timeView">
                                                        <div
                                                                class="audioTAP_strlka">100%
                                                        </div>
                                                    </div>
                                                    <div class="audioBGProgress"></div>
                                                    <div class="audioLoadProgress"></div>
                                                    <div class="audioPlayProgress" id="playerPlayLine">
                                                        <div class="audioSlider"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="audioVolumeBar fl_l" onclick="cancelEvent(event);"
                                                     onmousedown="audio_player.volumeDown(event, this);" id="no_play">
                                                    <div class="audioTimesAP">
                                                        <div class="audioTAP_strlka">100%</div>
                                                    </div>
                                                    <div class="audioBGProgress"></div>
                                                    <div class="audioPlayProgress" id="playerVolumeBar">
                                                        <div class="audioSlider"></div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="card mt-3">
                    <a href="" style="text-decoration:none" onclick="Page.Go(this.href); return false">
                        <div class="albtitle">Видеозаписи<span class="pl-2">2</span></div>
                    </a>
                    {videos}
                </div>
            @elseif($type === 3)
                @include('search.groups', $search)
            @elseif($type === 4)
                @include('search.audios', $search)
            @endif
            {{ $navigation }}
        </div>
    </div>
@endsection