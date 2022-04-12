@if($search)
    @foreach($search as $row)
        <div id="link_tag_{{ $row['user_id'] }}_{{ $row['user_id'] }}"></div>
        <div class="card mt-3">
            <div class="card-body">
                <div class="media">
                    <div class="avatar {{ $row['ava_online'] }} mr-5">
                        <img src="{{ $row['ava'] }}" alt="{{ $row['name'] }}" class="avatar-img rounded-circle"
                             style="width: 60px;height: 60px;"
                             onmouseover="wall.showTag({{ $row['user_id'] }}, {{ $row['user_id'] }}, 1)"
                             onmouseout="wall.hideTag({{ $row['user_id'] }}, {{ $row['user_id'] }}, 1)">
                        {{--                <a href="/u{{ $row['user_id'] }}" onClick="Page.Go(this.href); return false" class="avatar"--}}
                        {{--                   onmouseover="wall.showTag({{ $row['user_id'] }}, {{ $row['user_id'] }}, 1)" onmouseout="wall.hideTag({{ $row['user_id'] }}, {{ $row['user_id'] }}, 1)">--}}
                        {{--                </a>--}}
                    </div>
                    <div class="media-body align-self-center">
                        <h4 class="mb-1">
                            <a href="/u{{ $row['user_id'] }}" onClick="Page.Go(this.href); return false"
                               onmouseover="wall.showTag({{ $row['user_id'] }}, {{ $row['user_id'] }}, 1)"
                               onmouseout="wall.hideTag({{ $row['user_id'] }}, {{ $row['user_id'] }}, 1)">{{ $row['name'] }}</a>
                        </h4>
                        {{--                <p class="card-text small">--}}
                        {{--                    <span class="text-success">{{ $row['online'] }}</span>--}}
                        {{--                </p>--}}
                        <p>{{ $row['country'] }}{{ $row['city'] }}</p>
                        <p>{{ $row['age'] }}</p>
                    </div>
                    <div class="align-self-center ml-5">
                        {{--                <div class="im_del_dialog no_display cursor_pointer" onclick="im.box_del('16'); return false" onmouseover="$('#deia16').show(); myhtml.title('16', 'Удалить диалог', 'deia', 6)" id="deia16" onmouseout="$('#deia16').hide()" style="display: none;"></div>--}}
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif
