@extends('main.main')
@section('content')
    <script type="text/javascript">
        var startResizeCss = false;
        var user_id = '{{ $user_id }}';
        $(document).ready(function () {
            $(window).scroll(function () {
                if ($('#type_page').val() == 'profile') {
                    if ($(document).height() - $(window).height() <= $(window).scrollTop() + ($(document).height() / 2 - 250)) {
                        wall.page(user_id);
                    }
                    if ($(window).scrollTop() < $('#fortoAutoSizeStyleProfile').offset().top) {
                        startResizeCss = false;
                        $('#addStyleClass').remove();
                    }
                    if ($(window).scrollTop() > $('#fortoAutoSizeStyleProfile').offset().top && !startResizeCss) {
                        startResizeCss = true;
                        $('body').append('<div id="addStyleClass"><style type="text/css" media="all">.wallrecord{width:822px;margin-left:-210px}.infowalltext_f{font-size:11px}.wall_inpst{width:738px}.public_likes_user_block{margin-left:610px}.wall_fast_opened_form{width:788px;margin-left:-150px}.wall_fast_block{width:780px;margin-left:-150px}.public_wall_all_comm{width:780px;margin-left:-150px}.player_mini_mbar_wall{width:710px;margin-bottom:0px}#audioForSize{min-width:700px}.wall_rec_autoresize{width:760px}.wall_fast_ava img{width:50px}.wall_fast_ava{width:60px}.wall_fast_comment_text{margin-left:57px}.wall_fast_date{margin-left:57px;font-size:11px}.size10{font-size:11px}.fast_form_width2{width:778px}.audio_onetrack, .player_mini_mbar{width:760px}</style></div>');
                    }
                }
            });
            $('#wall_text, .fast_form_width').autoResize();
            {{--        {*        [owner]
                if ($('.profile_onefriend_happy').size() > 4) $('#happyAllLnk').show();
                [/owner]*} --}}
        });
        $(document).click(function (event) {
            wall.event(event);
        });

        function nshb() {
            $('#nshb').text('Скрыть блоки').attr('onClick', 'nshbb(); return false');
            $('.b_friends, .b_friends_online, .b_people, .b_pages, .b_video, .b_audio, .b_notes, .b_albums, .b_gifts, .b_photo, .b_wall').show();
        }

        function nshbb() {
            $('#nshb').text('Показать скрытые блоки').attr('onClick', 'nshb(); return false');
            $('.b_friends, .b_friends_online, .b_people, .b_pages, .b_video, .b_audio, .b_notes, .b_albums, .b_wall, .b_gifts@if($owner), .b_photo@endif').hide();
        }
    </script>
    <input type="hidden" id="type_page" value="profile"/>
    <style>
        .newcolor000 {
            color: #000
        }

        .audio_onetrack, .player_mini_mbar {
            width: 560px
        }

        .wall_none {
            margin-top: 10px
        }
    </style>
    <div id="jquery_jplayer"></div>
    <input type="hidden" id="teck_id" value=""/>
    <input type="hidden" id="teck_prefix" value=""/>
    <input type="hidden" id="typePlay" value="standart"/>
    <div class="ava">
        <div class="b_photo {b_photo}">
            <div id="ava">
                <img src="{{ $ava }}" alt="page_enlarge" id="ava_{{ $user_id }}"/>
            </div>
        </div>
        @if(!$blacklist )
            <div class="menuleft2" style="margin-top:-3px">
                @if($owner)
                    {{--                    <a href="/docs" onClick="Page.Go(this.href); return false;">--}}
                    {{--                        <div class="fl_l pr_ic_docs"></div>--}}
                    {{--                        <div>Мои документы</div>--}}
                    {{--                    </a>--}}
                    <a href="/editmypage" onClick="Page.Go(this.href); return false;">
                        <div class="fl_l pr_ic_edit"></div>
                        <div>Редактировать страницу</div>
                    </a>
                    <a href="/" onClick="Profile.LoadPhoto(); return false;">
                        <div class="fl_l pr_ic_ph"></div>
                        <div>Изменить фотографию</div>
                    </a>
                    <a href="/" onClick="Profile.miniature(); return false;">
                        <div class="fl_l pr_ic_mini"></div>
                        <div>Изменить миниатюру</div>
                    </a>
                    <a href="/" onClick="Profile.DelPhoto(); return false;" id="del_pho_but" {{ $display_ava }}>
                        <div class="fl_l pr_ic_del"></div>
                        <div>Удалить фотографию</div>
                    </a>
                @endif
                @if(!$owner)
                    @if($privacy_msg)
                        <a href="/" onClick="messages.new_({{ $user_id }}); return false">
                            <div>Отправить сообщение</div>
                        </a>
                    @endif



                    @if(!$yes_friends)
                        <a href="/" onClick="friends.add({{ $user_id }}); return false">
                            <div>Добавить в друзья</div>
                        </a>
                    @else
                        <a href="/" onClick="friends.delet({{ $user_id }}, 1); return false">
                            <div>Убрать из друзей</div>
                        </a>
                    @endif
                    {{--        {*        [yes-demand]<a href="/" onClick="friends.add({{ $user_id }}); return false">*}
                                {*            <div>Принять дружбу</div>*}
                                {*        </a>[/yes-demand]*}--}}
                    @if(!$yes_subscription)
                        <a href="/" onClick="subscriptions.add({{ $user_id }}); return false"
                           id="lnk_unsubscription">
                            <div><span id="text_add_subscription">Подписаться на обновления</span> <img
                                        src="/images/loading_mini.gif" alt="" id="addsubscription_load"
                                        class="no_display" style="margin-right:-13px"/></div>
                        </a>
                    @else
                        <a href="/" onClick="subscriptions.del({{ $user_id }}); return false" id="lnk_unsubscription">
                            <div><span id="text_add_subscription">Отписаться от обновлений</span> <img
                                        src="/images/loading_mini.gif" alt="" id="addsubscription_load"
                                        class="no_display" style="margin-right:-13px"/></div>
                        </a>
                    @endif
                    <a href="/" onClick="gifts.box('{{ $user_id }}'); return false">
                        <div>Отправить подарок</div>
                    </a>
                    @if(!$yes_fave)
                        <a href="/" onClick="fave.add({{ $user_id }}); return false" id="addfave_but">
                            <div><span id="text_add_fave">Добавить в закладки</span> <img
                                        src="/images/loading_mini.gif" alt="" id="addfave_load" class="no_display"/>
                            </div>
                        </a>
                    @else
                        <a href="/" onClick="fave.delet({{ $user_id }}); return false" id="addfave_but">
                            <div><span id="text_add_fave">Удалить из закладок</span> <img
                                        src="/images/loading_mini.gif" alt="" id="addfave_load" class="no_display"/>
                            </div>
                        </a>
                    @endif
                    @if($yes_blacklist)
                        <a href="/" onClick="settings.delblacklist({{ $user_id }}, 1); return false"
                           id="addblacklist_but">
                            <div><span id="text_add_blacklist">Разблокировать</span> <img
                                        src="/images/loading_mini.gif" alt="" id="addblacklist_load"
                                        class="no_display"/></div>
                        </a>
                    @else
                        <a href="/" onClick="settings.addblacklist({{ $user_id }}); return false" id="addblacklist_but">
                            <div><span id="text_add_blacklist">Заблокировать</span> <img
                                        src="/images/loading_mini.gif" alt="" id="addblacklist_load"
                                        class="no_display"/></div>
                        </a>
                    @endif

                    {{--        {*        <a class="cursor_pointer" onClick="transmit.box('{{ $user_id }}')">*}
                                {*            <div>Передать mix</div>*}
                                {*        </a>*}--}}
                @endif
                <a href="/" id="nshb" onClick="nshb(); return false" style="margin-bottom:1px">
                    <div><span id="text_add_blacklist">Показать скрытые блоки</span></div>
                </a>
            </div>


            <div class="leftcbor">
                @if($owner && $happy_friends)
                    <div id="happyBLockSess">
                        <div class="albtitle">Дни рожденья друзей <span>{{ $happy_friends_num }}</span>
                            <div class="profile_happy_hide">
                                <img src="/images/hide_lef.gif"
                                     onMouseOver="myhtml.title('1', 'Скрыть', 'happy_block_')"
                                     id="happy_block_1" onClick="HappyFr.HideSess(); return false"/>
                            </div>
                        </div>
                        <div class="newmesnobg profile_block_happy_friends"
                             style="padding:0px;padding-left:4px;padding-top:5px;">
                            {happy-friends}
                            <div class="clear"></div>
                        </div>
                        <div class="cursor_pointer no_display" onMouseDown="HappyFr.Show(); return false"
                             id="happyAllLnk">
                            <div class="public_wall_all_comm profile_block_happy_friends_lnk">Показать все</div>
                        </div>
                    </div>
                @endif

                @if($mutual_friends)
                    <a href="/friends/common/{{ $user_id }}" style="text-decoration:none"
                       onClick="Page.Go(this.href); return false">
                        <div class="albtitle">
                            <div class="profile_ic_frieds fl_l"></div>
                            Общие друзья <span>{{ $mutual_num }}</span></div>
                    </a>
                    <div class="newmesnobg" style="padding:0px;padding-top:10px;">
                        @foreach($mutual_friends as $row)
                            <div class="onefriend">
                                <a href="/u{{ $row['user_id'] }}" onClick="Page.Go(this.href); return false">
                                    <div>
                                        <img src="{{ $row['ava'] }}" alt=""/>
                                    </div>{{ $row['name'] }}<br/><span>{{ $row['last_name'] }}</span>
                                </a>
                            </div>
                        @endforeach
                        <div class="clear"></div>
                    </div>
                @endif

                @if(!empty($all_friends))
                    <div class="b_friends {b_friends}">
                        <a href="/friends/{{ $user_id }}" onClick="Page.Go(this.href); return false"
                           style="text-decoration:none">
                            <div class="albtitle">
                                <div class="profile_ic_frieds fl_l"></div>
                                Друзья <span>{{ $all_friends_num }}</span>
                            </div>
                        </a>
                        <div class="newmesnobg" style="padding:0px;padding-top:10px;">
                            @foreach($all_friends as $row)
                                <div class="onefriend">
                                    <a href="/u{{ $row['user_id'] }}" onClick="Page.Go(this.href); return false">
                                        <div>
                                            <img src="{{ $row['ava'] }}" alt=""/>
                                        </div>
                                        <p class="p-2"> {{ $row['name'] }} </p>
                                    </a>
                                </div>
                            @endforeach
                            <div class="clear"></div>
                        </div>
                    </div>
                @endif

                @if($online_friends)
                    <div class="b_friends_online {b_friends_online}">
                        <a href="/friends/online/{{ $user_id }}" onClick="Page.Go(this.href); return false"
                           style="text-decoration:none">
                            <div class="albtitle">
                                <div class="profile_ic_frieds fl_l"></div>
                                Друзья на сайте <span>{{ $online_friends_num }}</span>
                            </div>
                        </a>
                        <div class="newmesnobg" style="padding: 10px 0 0;">
                            @foreach($online_friends as $row)
                                <div class="onefriend">
                                    <a href="/u{{ $row['user_id'] }}" onClick="Page.Go(this.href); return false">
                                        <div>
                                            <img src="{{ $row['ava'] }}" alt=""/>
                                        </div>{{ $row['name'] }}<br/><span>{{ $row['last_name'] }}</span>
                                    </a>
                                </div>
                            @endforeach
                            <div class="clear"></div>
                        </div>
                    </div>
                @endif

                @if($subscriptions)
                    <div class="b_people {b_people}">
                        <a href="/"
                           onClick="subscriptions.all({{ $user_id }}, '', {{ $subscriptions_num }}); return false"
                           style="text-decoration:none">
                            <div class="albtitle">
                                <div class="profile_ic_frieds fl_l"></div>
                                Интересные люди <span>{{ $subscriptions_num }}</span>
                            </div>
                        </a>
                        <div class="newmesnobg" style="padding-right:0px;padding-bottom:0px;">
                            @foreach($subscriptions as $row)
                                <div class="onesubscription onesubscriptio2n">
                                    <a href="/u{{ $row['user_id'] }}" onClick="Page.Go(this.href); return false">
                                        <img src="{{ $row['ava'] }}" alt=""/>
                                        <div class="onesubscriptiontitle">{{ $row['name'] }}</div>
                                    </a>
                                    <div class="nesubscriptstatus">{{ $row['info'] }}</div>
                                </div>
                            @endforeach
                            <div class="clear"></div>
                        </div>
                    </div>
                @endif

                    @if(!empty($groups))
                    <div class="b_pages {b_pages}">
                        <div class="albtitle cursor_pointer" onClick="groups.all_groups_user('{{ $user_id }}')">
                            <div class="profile_ic_pages fl_l"></div>
                            Подписки <span id="groups_num">{{ $groups_num }}</span>
                        </div>
                        <div class="newmesnobg" style="padding: 10px 0 0;">
                            @foreach($groups as $row)
                                <div class="onesubscription onesubscriptio2n pl-3">
                                    <a href="/{{ $row['adres'] }}" onClick="Page.Go(this.href); return false">
                                        <img src="{{ $row['ava'] }}" alt=""/>
                                        <div class="onesubscriptiontitle">{{ $row['name'] }}</div>
                                    </a>
                                    <div class="nesubscriptstatus">{{ $row['info'] }}</div>
                                </div>
                            @endforeach
                            <div class="clear"></div>
                        </div>
                    </div>
                @endif

                    @if(!empty($videos))
                    <div class="b_video {b_video}">
                        <a href="/videos/{{ $user_id }}" onClick="Page.Go(this.href); return false"
                           style="text-decoration:none">
                            <div class="albtitle">
                                <div class="profile_ic_videos fl_l"></div>
                                Видеозаписи <span>{{ $groups_num }}</span>
                            </div>
                        </a>
                        <div class="newmesnobg" style="padding-right:0px;padding-bottom:0px;">
                            @foreach($groups as $row)
                                <div class="onesubscription onesubscriptio2n pl-3">
                                    <a href="/{{ $row['adres'] }}" onClick="Page.Go(this.href); return false">
                                        <img src="{{ $row['ava'] }}" alt=""/>
                                        <div class="onesubscriptiontitle">{{ $row['name'] }}</div>
                                    </a>
                                    <div class="nesubscriptstatus">{{ $row['info'] }}</div>
                                </div>
                            @endforeach
                            <div class="clear"></div>
                        </div>
                    </div>
                @endif

                    @if(!empty($notes))
                    <div class="{b_notes}">
                        <a href="/notes/{{ $user_id }}" onClick="Page.Go(this.href); return false"
                           style="text-decoration:none">
                            <div class="albtitle">
                                <div class="profile_ic_notes fl_l"></div>
                                Заметки <span>{notes-num}</span>
                            </div>
                        </a>
                        <div class="newmesnobg" style="padding-right:0px;padding-left:5px">
                            {notes}
                            <div class="clear"></div>
                        </div>
                    </div>
                @endif

                <div class="clear"></div>
                <span id="fortoAutoSizeStyleProfile"></span>
            </div>
        @endif
    </div>
    <div class="profiewr">
        @if($owner)
            <div class="set_status_bg no_display" id="set_status_bg">
                <input type="text" id="status_text" class="status_inp" value="{{ $status_text }}" style="width:645px"
                       maxlength="255" onKeyPress="if(event.keyCode == 13)gStatus.set()"/>
                <div class="fl_l status_text">
                    <span class="no_status_text @if(!empty($status_text)) no_display @endif ">Введите здесь текст Вашего статуса.</span>
                    <a href="/" class="yes_status_text @if(empty($status_text)) no_display @endif "
                       onClick="gStatus.set(1); return false">Удалить статус</a>
                </div>
                @if(!empty($status_text))
                    <div class="button_div_gray fl_r status_but margin_left">
                        <button>Отмена</button>
                    </div>
                @endif
                <div class="button_div fl_r status_but">
                    <button id="status_but" onClick="gStatus.set()">Сохранить</button>
                </div>
            </div>
        @endif

        <div class="titleu">{{ $name }} {{ $lastname }}</div>

        <div class="profile_rate_pos">
            <div class="profile_rate_text">&nbsp;</div>
            @if($owner)
                <a class="cursor_pointer" onClick="rating.view()">@endif
                    <div class="profile_rate_100_left {{ $rating_class_left }}"></div>
                    @if($owner)</a>
            @endif
            <div class="profile_rate_add" onClick="rating.addbox('{{ $user_id }}')"
                 onMouseOver="myhtml.title('1', 'Повысить рейтинг', 'rate', 1)" id="rate1">
                <img src="/images/icons/rate_ic.png"/></div>
            @if($owner)
                <a class="cursor_pointer" onClick="rating.view()" style="text-decoration:none">@endif
                    <div class="profile_rate_100_right {{ $rating_class_right }}"></div>
                    <div class="profile_rate_100_head {{ $rating_class_head }}"
                         id="profile_rate_num">{{ $rating }}</div>
                    @if($owner)</a>
            @endif
        </div>

        @if(!$owner && !empty($status_text))
            <div class="status_tri"></div>
            <div class="status border_radius_5">@endif
                @if($owner)
                    <div class="status_tri"></div>
                    <div class="status border_radius_5">@endif
                        <div>
                            @if($owner)
                                <a href="/" id="new_status" onClick="gStatus.open(); return false">@endif
                                    @if(!$blacklist)
                                        {{ $status_text }}
                                    @endif
                                    @if($owner)</a>
                            @endif

                        </div>
                        @if($owner)
                            <span id="tellBlockPos"></span>
                            <div class="status_tell_friends no_display" style="z-index:200">
                                <div class="status_str"></div>
                                <div class="html_checkbox" id="tell_friends"
                                     onClick="myhtml.checkbox(this.id); gStatus.startTell()">
                                    Рассказать друзьям
                                </div>
                            </div>

                            <a href="#" onClick="gStatus.open(); return false"
                               id="status_link" @if(!empty($status_text))class="no_display" @endif>установить
                                статус</a>
                        @endif
                        @if(!$owner && !empty($status_text))
                    </div>
                @endif
                @if($owner)
            </div>
        @endif
        <div class="clear"></div>
        <div class="page_bg border_radius_5 margin_top_10">
            <div class="fl_r online"><b>{{ $online }}</b></div>
            @if(!empty($country))
                <div class="flpodtext">Страна:</div>
                <div class="flpodinfo">
                    <a href="/?go=search&country={{ $country_id }}"
                       onClick="Page.Go(this.href); return false">{{ $country }}</a>
                </div>
            @endif
            @if(!empty($city))
                <div class="flpodtext">Город:</div>
                <div class="flpodinfo">
                    <a href="/?go=search&country={{ $country_id }}&city={{ $city_id }}"
                       onClick="Page.Go(this.href); return false">{{ $city }}</a>
                </div>
            @endif
            @if(!$blacklist)
                @if(!empty($birth_day))
                    <div class="flpodtext">День рождения:</div>
                    <div class="flpodinfo">{{ $birth_day }}</div>
                @endif
                @if($privacy_info && $sp)
                    <div class="flpodtext">Семейное положение:</div>
                    <div class="flpodinfo">{{ $sp }}</div>
                @endif
                <div class="cursor_pointer" onClick="Profile.MoreInfo(); return false" id="moreInfoLnk">
                    <div class="profile_hide_opne" id="moreInfoText">Показать подробную информацию</div>
                </div>
                <div id="moreInfo" class="no_display">
                    @if($privacy_info)
                        @if(!$owner && !empty($phone) || !$owner && !empty($site) || $owner)
                            <div class="fieldset">
                                <div class="w2_a">Контактная информация
                                    @if($owner)
                                        <span>
                            <a href="/editmypage/contact" onClick="Page.Go(this.href); return false;">редактировать</a>
                        </span>
                                    @endif
                                </div>
                            </div>
                            @if(!empty($phone))
                                <div class="flpodtext">Моб. телефон:</div>
                                <div class="flpodinfo">{{ $phone }}</div>
                            @endif
                            @if(!empty($site))
                                <div class="flpodtext">Веб-сайт:</div>
                                <div class="flpodinfo"><a href="/away.php?url={{ $site }}" title="Перейти на сайт"
                                                          target="_blank">{{ $site }}</a>
                                </div>
                            @endif
                        @endif
                        <div class="fieldset">
                            <div class="w2_b">Личная информация
                                @if($owner)
                                    <span>
                        <a href="/editmypage/interests" onClick="Page.Go(this.href); return false;">редактировать</a>
                    </span>
                                @endif
                            </div>
                        </div>
                        {{ $not_block_info }}
                        @if(!empty($myinfo))
                            <div class="flpodtext">О себе:</div>
                            <div class="flpodinfo">{{ $myinfo }}</div>
                        @endif
                    @endif
                </div>

        </div>

            @if(!empty($albums))
            <div class="b_albums {b_albums}">
                <div class="page_bg border_radius_5 margin_top_10">
                    <a href="/albums/{{ $user_id }}" onClick="Page.Go(this.href); return false"
                       style="text-decoration:none">
                        <div class="albtitle albtitle2">
                            <div class="profile_ic_albums fl_l"></div>
                            Альбомы <span>{{ $albums_num }}</span></div>
                    </a>
                    @foreach($albums as $row)
                        <a href="/albums/view/{{ $row['aid'] }}" onClick="Page.Go(this.href); return false"
                           style="text-decoration:none">
                            <div class="profile_albums">
                                <img src="{{ $row['album_cover'] }}"/>
                                <div class="profile_title_album">{{ $row['name'] }}</div>
                                {{ $row['photo_num'] }} {{ $row['albums_photonums'] }}<br/>Обновлён {{ $row['date'] }}
                                <div class="clear"></div>
                            </div>
                        </a>
                    @endforeach

                    <div class="clear"></div>
                </div>
            </div>
            @endif
            @if(!empty($audios))
            <div class="b_audio {b_audio}">
                <div class="page_bg border_radius_5 margin_top_10" style="padding-bottom:10px">
                    <div id="jquery_jplayer"></div>
                    <input type="hidden" id="teck_id" value="1"/>
                    <a href="/audio{{ $user_id }}" onClick="Page.Go(this.href); return false"
                       style="text-decoration:none">
                        <div class="albtitle albtitle2">
                            <div class="profile_ic_audios fl_l"></div>
                            {{ $audios_num }}
                        </div>
                    </a>@foreach($audios as $row)
                        <div class="audioPage audioElem" id="audio_{{ $row['id'] }}_{{ $user_id }}_{{ $row['plname'] }}"
                             onclick="playNewAudio('{{ $row['id'] }}_{{ $user_id }}_{{ $row['plname'] }}', event);">
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
                                            <input type="hidden"
                                                   value="{{ $row['url'] }},{{ $row['duration'] }},page"
                                                   id="audio_url_{{ $row['id'] }}_{{ $user_id }}_{{ $row['plname'] }}">
                                        </td>
                                        <td class="info">
                                            <div class="audioNames">
                                                <b class="author"
                                                   onclick="Page.Go('/?go=search&query={{ $row['search_artist'] }}&type=5&n=1'); return false;"
                                                   id="artist">{{ $row['artist'] }}</b> –
                                                <span class="name" id="name">{{ $row['title'] }}</span>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="audioElTime" id="audio_time_{{ $row['id'] }}_{{ $user_id }}_{{ $row['plname'] }}">
                                                {{ $row['stime'] }}
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div id="player{{ $row['id'] }}_{{ $user_id }}_{{ $row['plname'] }}" class="audioPlayer" border="0"
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
                                                        <div class="audioTAP_strlka">100%</div>
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
                    <div class="clear"></div>
                </div>
            </div>
            @endif
            @if(!empty($gifts))
        <div class="b_gifts {b_gifts}">
            <div class="page_bg border_radius_5 margin_top_10">
                <a href="/gifts{{ $user_id }}" onClick="Page.Go(this.href); return false" style="text-decoration:none">
                    <div class="albtitle  albtitle2">
                        <div class="profile_ic_gifts fl_l"></div>
                        {{ $gifts_text }}
                    </div>
                    <div style="text-align: center;">
                        @foreach($gifts as $row)
                            <img src=\"/uploads/gifts/{{ $row['gift'] }}.png\" class=\"gift_onepage\" />
                        @endforeach
                    </div>
                    <div class="clear"></div>
                </a>
            </div>
        </div>
        @endif

            {{--        <div class="b_wall {b_wall}">
                        <div class="page_bg border_radius_5 margin_top_10 page_bg_wall"
                             style="padding-bottom:15px">
                            <a href="/wall{{ $user_id }}" onClick="Page.Go(this.href); return false" style="text-decoration:none">
                                <div class="albtitle albtitle2" style="border-bottom:0">Стена <span
                                            id="wall_rec_num">{{ $wall_num }}</span></div>
                            </a>
                            @if($privacy_wall)
                            <div class="newmes" id="wall_tab"
                                 style="border-bottom:0px;margin-left:-13px;margin-top:-15px;margin-bottom:-10px">
                                <input type="hidden"
                                       value=" @if($owner) Что у Вас нового? @else Написать сообщение... @endif "
                                       id="wall_input_text"/>
                                <input type="text" class="msg_se_inp"
                                       value=" @if($owner) Что у Вас нового? @else Написать сообщение... @endif "
                                       onMouseDown="wall.form_open(); return false" id="wall_input" style="width:600px"/>
                                <div class="no_display" id="wall_textarea">
                                   <textarea id="wall_text" class="wall_inpst wall_fast_opened_texta"
                                             style="width:612px"
                                             onKeyUp="wall.CheckLinkText(this.value)"
                                             onBlur="wall.CheckLinkText(this.value, 1)"
                                   >
                                   </textarea>
                                    <div id="attach_files" class="margin_top_10 no_display"></div>
                                    <div id="attach_block_lnk" class="no_display clear">
                                        <div class="attach_link_bg">
                                            <div align="center" id="loading_att_lnk">
                                                <img src="/images/loading_mini.gif" style="margin-bottom:-2px"/>
                                            </div>
                                            <img src="" align="left" id="attatch_link_img" class="no_display cursor_pointer"
                                                 onClick="wall.UrlNextImg()"/>
                                            <div id="attatch_link_title"></div>
                                            <div id="attatch_link_descr"></div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="attach_toolip_but"></div>
                                        <div class="attach_link_block_ic fl_l"></div>
                                        <div class="attach_link_block_te">
                                            <div class="fl_l">Ссылка: <a href="/" id="attatch_link_url" target="_blank"></a></div>
                                            <img class="fl_l cursor_pointer" style="margin-top:2px;margin-left:5px"
                                                 src="/images/close_a.png"
                                                 onMouseOver="myhtml.title('1', 'Не прикреплять', 'attach_lnk_')" id="attach_lnk_1"
                                                 onClick="wall.RemoveAttachLnk()"/></div>
                                        <input type="hidden" id="attach_lnk_stared"/>
                                        <input type="hidden" id="teck_link_attach"/>
                                        <span id="urlParseImgs" class="no_display"></span>
                                    </div>
                                    <div class="clear"></div>
                                    <div id="attach_block_vote" class="no_display">
                                        <div class="attach_link_bg">
                                            <div class="texta">Тема опроса:</div>
                                            <input type="text" id="vote_title" class="inpst" maxlength="80" value=""
                                                   style="width:355px;margin-left:5px"
                                                   onKeyUp="$('#attatch_vote_title').text(this.value)"
                                            />
                                            <div class="mgclr"></div>
                                            <div class="texta">Варианты ответа:<br/>
                                                <small>
                                            <span id="addNewAnswer">
                                                <a class="cursor_pointer" onClick="Votes.AddInp()">добавить</a>
                                            </span>
                                                    | <span id="addDelAnswer">удалить</span>
                                                </small>
                                            </div>
                                            <input type="text" id="vote_answer_1" class="inpst" maxlength="80" value=""
                                                   style="width:355px;margin-left:5px"/>
                                            <div class="mgclr"></div>
                                            <div class="texta">&nbsp;</div>
                                            <input type="text" id="vote_answer_2" class="inpst" maxlength="80" value=""
                                                   style="width:355px;margin-left:5px"/>
                                            <div class="mgclr"></div>
                                            <div id="addAnswerInp"></div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="attach_toolip_but"></div>
                                        <div class="attach_link_block_ic fl_l"></div>
                                        <div class="attach_link_block_te">
                                            <div class="fl_l">Опрос: <a id="attatch_vote_title"
                                                                        style="text-decoration:none;cursor:default"></a>
                                            </div>
                                            <img class="fl_l cursor_pointer" style="margin-top:2px;margin-left:5px"
                                                 src="/images/close_a.png"
                                                 onMouseOver="myhtml.title('1', 'Не прикреплять', 'attach_vote_')"
                                                 id="attach_vote_1"
                                                 onClick="Votes.RemoveForAttach()"/>
                                        </div>
                                        <input type="hidden" id="answerNum" value="2"/>
                                    </div>
                                    <div class="clear"></div>
                                    <input id="vaLattach_files" type="hidden"/>
                                    <div class="clear"></div>
                                    <div class="button_div fl_l margin_top_10">
                                        <button onClick="wall.send(); return false" id="wall_send">Отправить</button>
                                    </div>
                                    <div class="wall_attach fl_r" style="margin-right:-14px"
                                         onMouseOver="wall.attach_menu('open', this.id, 'wall_attach_menu')"
                                         onMouseOut="wall.attach_menu('close', this.id, 'wall_attach_menu')" id="wall_attach">
                                        Прикрепить
                                    </div>
                                    <div class="wall_attach_menu no_display"
                                         onMouseOver="wall.attach_menu('open', 'wall_attach', 'wall_attach_menu')"
                                         onMouseOut="wall.attach_menu('close', 'wall_attach', 'wall_attach_menu')"
                                         id="wall_attach_menu">
                                        <div class="wall_attach_icon_smile border_radius_5" id="wall_attach_link"
                                             onClick="wall.attach_addsmile()">Смайлик
                                        </div>
                                        <div class="wall_attach_icon_photo border_radius_5" id="wall_attach_link"
                                             onClick="wall.attach_addphoto()">Фотографию
                                        </div>
                                        <div class="wall_attach_icon_video border_radius_5" id="wall_attach_link"
                                             onClick="wall.attach_addvideo()">Видеозапись
                                        </div>
                                        <div class="wall_attach_icon_audio border_radius_5" id="wall_attach_link"
                                             onClick="wall.attach_addaudio()">Аудиозапись
                                        </div>
                                        <div class="wall_attach_icon_doc border_radius_5" id="wall_attach_link"
                                             onClick="wall.attach_addDoc()">Документ
                                        </div>
                                        <div class="wall_attach_icon_vote border_radius_5" id="wall_attach_link"
                                             onClick="$('#attach_block_vote').slideDown('fast');wall.attach_menu('close', 'wall_attach', 'wall_attach_menu');$('#vote_title').focus();$('#vaLattach_files').val($('#vaLattach_files').val()+'vote|start||')">
                                            Опрос
                                        </div>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                            @endif
                        </div>
                        <div id="wall_records">
                            @if($user_id === 0 && $wall_num > 0 && !$blacklist)
                                                    @include('wall.one_record', ['wall_records' => $wall_records])
                            @else
                                <div class="wall_none">На стене пока нет ни одной записи.</div>
                            @endif
                        </div>
                        @if($wall_link)
                            <span id="wall_all_record"></span>
                            <div onClick="wall.page('{{ $user_id }}'); return false" id="wall_l_href" class="cursor_pointer">
                                <div class="doc_all_but margin_top_10 border_radius_5" id="wall_link">к предыдущим записям</div>
                            </div>
                        @endif

                    </div>--}}
            @endif
            @if($blacklist)
                <div class="err_yellow" style="font-weight:normal;margin-top:5px">{name} ограничил доступ к своей
                    странице.
                </div>
            @endif
    </div>
    <div class="clear"></div>
@endsection