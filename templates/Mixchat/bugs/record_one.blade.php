@foreach($bugs as $row)
    <div class="card mt-3 mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div class="col-6">
                    <h5 class="card-title">{{ $row['title'] }}</h5>
                    <div class="date">обновлено {{ $row['date'] }}</div>
                </div>
                <div class="col-2">
                    <span class="state">Статус: {{ $row['status'] }}</span>&nbsp;&nbsp;
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3 mb-3">
        <div class="card-body">
            {{ $row['status_bug'] }}
        </div>
    </div>

    <style>
        ._3t4q {
            margin: 0 auto;
            position: relative;
            top: 0;
        }
        ._3t4s ._3t4u, ._4vsc ._3t4u, ._4vsb ._3t4u, ._997u ._3t4u {
            background: #000;
            opacity: .1;
        }
        ._3t4u {
            height: 2px;
            margin: 0 auto;
            position: absolute;
            top: 15px;
        }
        ._3t4s ._3t4v, ._3t4s .active ._3t4l {
            background: #3578e5;
            opacity: 1;
        }
        ._3t4v {
            opacity: 1;
            width: 0;
        }
        ._3t51 {
            overflow: hidden;
            padding-top: 12px;
            vertical-align: baseline;
        }
        ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }
        ._3t4j {
            float: left;
            height: 37px;
            position: relative;
            text-align: center;
        }
        ._3t4l {
            background: #999;
            border-radius: 32px;
            display: block;
            height: 8px;
            margin: 0 auto;
            position: relative;
            width: 8px;
        }
        ._3t4s ._3t4j._6wt9._71_e label {
            color: #3578e5;
        }
        ._3t4j._6wt9 label {
            opacity: 1;
        }
        ._3t4j label {
            cursor: default;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 14px;
            font-weight: normal;
        }
        ._3t4m {
            display: block;
            left: 0;
            position: absolute;
            right: 0;
            top: 17px;
        }
        body, button, input, label, select, td, textarea {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 12px;
        }
        label {
            color: #606770;
            cursor: default;
            font-weight: 600;
            vertical-align: middle;
        }
    </style>

    <div class="card mt-3 mb-3">
        <div class="card-body">
            <div id="link_tag_{{ $row['uid'] }}_{{ $row['id'] }}"></div>
            <div class="mb-3">
                <div class="row align-items-center">
                    <div class="col-auto  " id="ava_rec_1">
                        <a href="/u{{ $row['uid'] }}" class="avatar t1{{ $row['uid'] }}"  onmouseover="wall.showTag({{ $row['uid'] }}, {{ $row['id'] }}, 1, 't1')" onmouseout="wall.hideTag({{ $row['uid'] }}, {{ $row['id'] }}, 1, 't1')">

                            <div class="wall-avatar wall-avatar-online mr-5">
                                <img src="{{ $row['ava'] }}" alt="u{{ $row['uid'] }}" class="wall-avatar-img rounded-circle t2{{ $row['uid'] }}" style="width: 34px;height: 34px;" onmouseover="wall.showTag({{ $row['uid'] }}, {{ $row['id'] }}, 1, 't2')" onmouseout="wall.hideTag({{ $row['uid'] }}, {{ $row['id'] }}, 1, 't2')">
                            </div>
                        </a>
                    </div>
                    <div class="col ml-n2">
                        <h4 class="mb-1 t3{{ $row['uid'] }}" onmouseover="wall.showTag({{ $row['uid'] }}, {{ $row['id'] }}, 1, 't3')" onmouseout="wall.hideTag({{ $row['uid'] }}, {{ $row['id'] }}, 1, 't3')">{{ $row['user_search_pref'] }}</h4>
                        <p class="card-text small text-muted">
                            <time datetime="{{ $row['datetime'] }}">{{ $row['add_date'] }}</time>
                        </p>
                    </div>
                    <div class="col-auto">
                        @if($row['moderator'] || $row['user_id'] == $row['uid'])
                            <div class="wall_delete" onmouseover="myhtml.title('{{ $row['id'] }}', 'Удалить запись', 'wall_del_')" onclick="bugs.Delete('{{ $row['id'] }}'); return false" id="wall_del_{{ $row['id'] }}"></div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="bug_content">
                    <div class="form">{{ $row['text'] }}
                        <div style="margin-top:10px;">
                            <div style="margin-top:5px;width: 472px;">
                                <div class="wall_photo" style="width:250px;max-height: 400px;" onclick="Photos.openAll(this, 18922, 0, 0, 0)">
                                    {{ $row['photo'] }}
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>

                    <span class="color777">{{ $row['sex'] }} {{ $row['name'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3 mb-3">
        <div class="card-body">
            <h5 class="card-title">Комментарии</h5>
        </div>
    </div>

    <div class="wall_fast_opened_form" id="fast_form" style="margin: 0;padding: 20px 10px;">
        <label for="fast_inpt_{{ $row['id'] }}"></label>
        <input type="text" style="width: 100%;" class="wall_inpst fast_form_width wall_fast_input" value="Комментировать..." id="fast_inpt_{{ $row['id'] }}"
               onmousedown="wall.fast_open_textarea('{{ $row['id'] }}', 2); return false">
        <div class="no_display wall_fast_texatrea" id="fast_textarea_{{ $row['id'] }}">
            <div class="mb-3">
                <label for="fast_text_{{ $row['id'] }}"></label>
                <textarea class="wall_inpst fast_form_width wall_fast_text"
                          style="height:33px;color:#000;margin:0px;width: 100%;" id="fast_text_{{ $row['id'] }}"
                          onkeypress="if(event.keyCode === 10 || (event.ctrlKey &amp;&amp; event.keyCode === 13))bugs.create_comment('{{ $row['id'] }}', '{{ $row['user_id'] }}')"></textarea>
            </div>
            @if($row['moderator'])
                <div class="mb-3 row">
                    <label for="inputPassword" class="col-sm-2 col-form-label">Статус</label>
                    <div class="col-sm-10">
                        <select class="form-select" aria-label="Default select example" id="fast_status_{{ $row['id'] }}">
                            <option value="9">В работе</option>
                            <option value="2">Исправлено</option>
                            <option value="3" selected>Отклонено</option>
                            <option value="4">На рассмотрении</option>
                            <option value="6">Решено</option>
                            <option value="8">Заблокировано</option>
                            <option value="5">Переоткрыто</option>
                            <option value="10">Не воспроизводится</option>
                            <option value="7">Отложено</option>
                            <option value="11">Требует корректировки</option>
                        </select>
                    </div>
                    @endif
                    <div class="mb-3">
                        <div class="button_div fl_l margin_top_5"><button onclick="bugs.create_comment('{{ $row['id'] }}', '{{ $row['user_id'] }}'); return false" id="fast_buts_{{ $row['id'] }}">Отправить</button></div>
                    </div>
                </div>
                <div class="clear"></div>
        </div>

        <span id="news">
        @if($row['comments'])
                @foreach($row['comments'] as $comments)
                    <div class="card mt-3 wallrecord" id="wall_record_{{ $comments['id'] }}">
                <div id="link_tag_{{ $comments['author_user_id'] }}_{{ $comments['id'] }}"></div>
                <div class="card-body">
                    <div class="mb-3">
                    <div class="row align-items-center">
                    <div class="col-auto  " id="ava_rec_{{ $comments['id'] }}">
                    <a href="/u{{ $comments['author_user_id'] }}" class="avatar {{ $comments['id'] }}{{ $comments['author_user_id'] }}"
                       onmouseover="wall.showTag({{ $comments['author_user_id'] }}, {{ $comments['id'] }}, 1, {{ $comments['id'] }})"
                       onmouseout="wall.hideTag({{ $comments['author_user_id'] }}, {{ $comments['id'] }}, 1, {{ $comments['id'] }})">

                    <div class="wall-avatar wall-avatar-online mr-5">
                        <img src="/images/no_ava_50.png" alt="u{{ $comments['author_user_id'] }}"
                             class="wall-avatar-img rounded-circle" style="width: 34px;height: 34px;"
                             onmouseover="wall.showTag({{ $comments['author_user_id'] }}, {{ $comments['id'] }}, 1, {{ $comments['id'] }})"
                             onmouseout="wall.hideTag({{ $comments['author_user_id'] }}, {{ $comments['id'] }}, 1, {{ $comments['id'] }})">
                    </div>
                    </a>
                    </div>
                    <div class="col ml-n2">
                        <h4 class="mb-1"
                            onmouseover="wall.showTag({{ $comments['author_user_id'] }}, {{ $comments['id'] }}, 1, {{ $comments['id'] }})"
                            onmouseout="wall.hideTag({{ $comments['author_user_id'] }}, {{ $comments['id'] }}, 1, {{ $comments['id'] }})">u{{ $comments['author_user_id'] }}</h4>
                        <p class="card-text small text-muted">
                        <time datetime="2018-05-24">{{ $comments['add_date'] }}</time>
                        </p>
                    </div>
                    <div class="col-auto">
{{--                    <div class="wall_tell_all cursor_pointer"--}}
                        {{--                         onmouseover="myhtml.title('{{ $comments['id'] }}', 'Отправить в сообщество или другу', 'wall_tell_all_')"--}}
                        {{--                         onclick="Repost.Box('110'); return false " id="wall_tell_all_110">--}}

                        {{--                    </div>--}}
                    </div>
                    </div>
                    </div>
                    <p class="mb-3">{{ $comments['text'] }}</p><p>{{ $comments['status_info'] }}</p>
                </div>
            </div>
                @endforeach
            @endif
    </span>


@endforeach