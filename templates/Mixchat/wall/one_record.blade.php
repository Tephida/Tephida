@foreach($wall_records as $row)
    @if($row['record'])
        <div class="wallrecord wall_upage2 page_bg border_radius_5 margin_bottom_10"
             id="wall_record_{{ $row['rec_id'] }}"
             style="border:0;padding:20px">
            <div style="float:left;width:60px">
                <div class="ava_mini @if(isset($row['privacy_comment']) && $row['privacy_comment'] === true && !$row['comments_link']) wall_ava_mini @endif"
                     id="ava_rec_{{ $row['rec_id'] }}">
                    <a href="/u{{ $user_id }}" onClick="Page.Go(this.href); return false">
                        <img src="{{ $row['ava'] }}" alt="" title=""/></a>{online}
                </div>
            </div>
            <div>
                <div class="wallauthor fl_l"><a href="/u{{ $user_id }}"
                                                onClick="Page.Go(this.href); return false">{{ $row['name'] }}</a>
                    <span class="color777">{{ $row['type'] }}</span></div>
                @if($row['owner_record'])
                    <div class="wall_delete"
                         onMouseOver="myhtml.title('{{ $row['rec_id'] }}', 'Удалить запись', 'wall_del_')"
                     onClick="wall.delet('{{ $row['rec_id'] }}'); return false" id="wall_del_{{ $row['rec_id'] }}"></div>
                @endif
                <div class="wall_tell_all cursor_pointer"
                     onMouseOver="myhtml.title('{{ $row['rec_id'] }}', 'Отправить в сообщество или другу', 'wall_tell_all_')"
                     onClick="Repost.Box('{{ $row['rec_id'] }}'); return false " id="wall_tell_all_{{ $row['rec_id'] }}"></div>
                @if(!$row['owner_record'])
                <div class="wall_tell cursor_pointer"
                     onMouseOver="myhtml.title('{{ $row['rec_id'] }}', 'Рассказать друзьям', 'wall_tell_')"
                     onClick="wall.tell('{{ $row['rec_id'] }}'); return false" id="wall_tell_{{ $row['rec_id'] }}"
                     style="margin-top:2px;margin-left:4px"></div>
                <div class="wall_tell_ok no_display" id="wall_ok_tell_{{ $row['rec_id'] }}"
                     style="margin-left:2px;margin-top:1px"></div>
                <div class="wall_delete" onMouseOver="myhtml.title('{{ $row['rec_id'] }}', 'Отметить как спам', 'wall_spam_')"
                     onClick="Report.WallSend('wall', '{{ $row['rec_id'] }}'); return false" id="wall_spam_{{ $row['rec_id'] }}"></div>
                @endif
                <div class="wall_clear"></div>
                <div class="walltext">{{ $row['text'] }}</div>
                <div class="infowalltext_f clear">
                    <div class="fl_l">
                        <a href="/wall{author-id}_{{ $row['rec_id'] }}" onClick="Page.Go(this.href); return false"
                            class="online">{{ $row['date'] }}</a>
                        @if(isset($row['privacy_comment']) && !$row['privacy_comment'] && !$row['comments_link'])
                        <span id="fast_comm_link_{{ $row['rec_id'] }}" class="fast_comm_link">&nbsp;|&nbsp;
                            <a href="/" id="fast_link_{{ $row['rec_id'] }}"
                               onClick="wall.open_fast_form('{{ $row['rec_id'] }}'); wall.fast_open_textarea('{{ $row['rec_id'] }}'); return false">Комментировать</a>
                        </span> @endif
                    </div>
                    <div class="public_likes_user_block no_display" id="public_likes_user_block{{ $row['rec_id'] }}"
                         onMouseOver="groups.wall_like_users_five('{{ $row['rec_id'] }}')"
                         onMouseOut="groups.wall_like_users_five_hide('{{ $row['rec_id'] }}')"
                         style="margin-left:610px">
                        <div onClick="wall.all_liked_users('{{ $row['rec_id'] }}', '', '{{ $row['likes'] }}')">
                            Понравилось {{ $row['likes-text'] }}</div>
                        <div class="public_wall_likes_hidden">
                            <div class="public_wall_likes_hidden2">
                                <a href="/u{{ $row['viewer_id'] }}"
                                   id="like_user{{ $row['viewer_id'] }}_{{ $row['rec_id'] }}" class="no_display"
                                   onClick="Page.Go(this.href); return false">
                                    <img src="{{ $row['viewer_ava'] }}" width="32"/>
                                </a>
                                <div id="likes_users{{ $row['rec_id'] }}"></div>
                            </div>
                        </div>
                        <div class="public_like_strelka"></div>
                    </div>
                    <input type="hidden" id="update_like{{ $row['rec_id'] }}" value="0"/>
                    <div class="fl_r public_wall_like cursor_pointer" onClick="{like-js-function}"
                         onMouseOver="groups.wall_like_users_five('{{ $row['rec_id'] }}', 'uPages')"
                         onMouseOut="groups.wall_like_users_five_hide('{{ $row['rec_id'] }}')" id="wall_like_link{{ $row['rec_id'] }}">
                        <div class="fl_l" id="wall_like_active"></div>
                        <div class="public_wall_like_no {yes-like}" id="wall_active_ic{{ $row['rec_id'] }}"></div>
                        <div style="margin-top:-3px;font-size:15px" class="fl_r">
                            <b id="wall_like_cnt{{ $row['rec_id'] }}" class="{yes-like-color}">{{ $row['likes'] }}</b>
                        </div>
                    </div>
                    @if(isset($row['privacy_comment']) && !$row['privacy_comment'] && !$row['comments_link'])
                    <div class="wall_fast_form no_display" id="fast_form_{{ $row['rec_id'] }}">
                        <div class="no_display wall_fast_texatrea" id="fast_textarea_{{ $row['rec_id'] }}">
                            <textarea class="wall_inpst fast_form_width wall_fast_text" style="height:33px;color:#000;margin:0px;width:100%"
                                id="fast_text_{{ $row['rec_id'] }}"
                                onKeyPress="if(event.keyCode == 10 || (event.ctrlKey && event.keyCode == 13))wall.fast_send('{{ $row['rec_id'] }}', '{author-id}', 2)">

                            </textarea>
                            <div class="button_div fl_l margin_top_5">
                                <button onClick="wall.fast_send('{{ $row['rec_id'] }}', '{{ $row['author_id'] }}', 2); return false"
                                        id="fast_buts_{{ $row['rec_id'] }}">Отправить
                                </button>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                    @endif
                </div>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
        </div>
    @endif  @if($row['all_comm'])
        <div class="cursor_pointer" onClick="wall.all_comments('{{ $row['rec_id'] }}', '{author-id}', 1); return false"
             id="wall_all_but_link_{{ $row['rec_id'] }}">
            <div class="public_wall_all_comm border_radius_5" id="wall_all_comm_but_{{ $row['rec_id'] }}">Показать
                {{ $row['gram_record_all_comm'] }}
            </div>
        </div>
    @endif
    @if($row['comment'])
        <div class="wall_fast_block page_bg border_radius_5 margin_top_10" id="wall_fast_comment_{comm-id}"
             onMouseOver="ge('fast_del_{comm-id}').style.display = 'block'"
             onMouseOut="ge('fast_del_{comm-id}').style.display = 'none'" style="border:0px;padding:10px">
            <div class="wall_fast_ava">
                <a href="/u{{ $user_id }}" onClick="Page.Go(this.href); return false">
                    <img src="{{ $row['ava'] }}" alt=""/>
                </a>
            </div>
            <div><a href="/u{{ $user_id }}" onClick="Page.Go(this.href); return false">{name}</a></div>
            <div class="wall_fast_comment_text">{text}</div>
            <div class="wall_fast_date fl_l">{date} [not-owner]&nbsp;-&nbsp;
                <a href="#" onClick="wall.Answer('{{ $row['rec_id'] }}', '{comm-id}', '{name}'); return false"
                    id="answer_lnk">Ответить</a>
                [/not-owner]
            </div>
            [owner]<a href="/" class="size10 fl_r no_display" id="fast_del_{comm-id}"
                      onClick="wall.fast_comm_del('{comm-id}'); return false">Удалить</a>[/owner]
            <div class="clear"></div>
        </div>
    @endif
    @if($row['comment_form'])
        <div class="wall_fast_opened_form margin-top_10 border_radius_5" style="margin-top:10px" id="fast_form">
            <input type="text" class="wall_inpst fast_form_width wall_fast_input" value="Комментировать..."
                   id="fast_inpt_{{ $row['rec_id'] }}" onMouseDown="wall.fast_open_textarea('{{ $row['rec_id'] }}', 2); return false"
                   style="margin:0px;width:778px"/>
            <div class="no_display wall_fast_texatrea" id="fast_textarea_{{ $row['rec_id'] }}">
            <textarea class="wall_inpst fast_form_width wall_fast_text" style="height:33px;color:#000;margin:0;width: 100%"
            id="fast_text_{{ $row['rec_id'] }}"
            onKeyPress="if(event.keyCode == 10 || (event.ctrlKey && event.keyCode == 13))wall.fast_send('{{ $row['rec_id'] }}', '{author-id}', 2)"></textarea>
                <div class="button_div fl_l margin_top_5">
                    <button onClick="wall.fast_send('{{ $row['rec_id'] }}', '{author-id}', 2); return false"
                            id="fast_buts_{{ $row['rec_id'] }}">Отправить
                    </button>
                </div>
                <div class="wall_answer_for_comm fl_l">
                    <a class="cursor_pointer answer_comm_for" id="answer_comm_for_{{ $row['rec_id'] }}"></a>
                    <input type="hidden" class="answer_comm_id" id="answer_comm_id{{ $row['rec_id'] }}"/>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    @endif
@endforeach