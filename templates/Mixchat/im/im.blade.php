@extends('main.main')
@section('content')
    <div class="d-flex justify-content-center">
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <div class="row">
                <div class="col-3 bg-white im_flblock pl-4" id="im_left_bl">
                    <div class="row">
                        <h2 class="font-bold mb-6">@_e('chats')</h2>
                    </div>
                    <span id="updateDialogs"></span>
                    <nav class="pl-3">
                        @foreach($dialog as $row)
                            {{--                            <div id="1okim{{ $row['uid'] }}" style="height:45px">--}}
                            {{--                                <div class="im_oneusr cursor_pointer" onClick="im.open('{{ $row['uid'] }}')" onMouseOver="$('#deia{{ $row['uid'] }}').show()" onMouseOut="$('#deia{{ $row['uid'] }}').hide()" id="1dialog{{ $row['uid'] }}">--}}
                            {{--                                    <img src="{{ $row['ava'] }}"  alt="{{ $row['name'] }}"/>--}}
                            {{--                                    <div class="im_nameu fl_l">{{ $row['name'] }}</div>--}}
                            {{--                                    <span id="1upNewMsg{{ $row['uid'] }}">{{ $row['msg_num'] }}</span>--}}
                            {{--                                </div>--}}
                            {{--                                <div class="im_del_dialog no_display cursor_pointer" onClick="im.box_del('{{ $row['uid'] }}'); return false" onMouseOver="$('#deia{{ $row['uid'] }}').show(); myhtml.title('{{ $row['uid'] }}', 'Удалить диалог', 'deia', 6)" id="deia{{ $row['uid'] }}" onMouseOut="$('#deia{{ $row['uid'] }}').hide()"></div>--}}
                            {{--                            </div>--}}

                            <div class="bg_im_lcard border-0 card mb-6 " id="okim{{ $row['uid'] }}">
                                <div class="card-body" onClick="im.open('{{ $row['uid'] }}')"
                                     onMouseOver="$('#deia{{ $row['uid'] }}').show()"
                                     onMouseOut="$('#deia{{ $row['uid'] }}').hide()" id="dialog{{ $row['uid'] }}">
                                    <div class="media">
                                        <div class="avatar {{--avatar-online--}} mr-5">
                                            <img class="avatar-img" src="{{ $row['ava'] }}" alt="{{ $row['name'] }}">
                                        </div>
                                        <div class="media-body align-self-center">
                                            <h6 class="mb-0">{{ $row['name'] }}</h6>
                                            {{--                                            <small class="text-muted">Online</small>--}}
                                            <span id="upNewMsg{{ $row['uid'] }}">{{ $row['msg_num'] }}</span>
                                        </div>
                                        <div class="align-self-center ml-5">
                                            <div class="im_del_dialog no_display cursor_pointer"
                                                 onClick="im.box_del('{{ $row['uid'] }}'); return false"
                                                 onMouseOver="$('#deia{{ $row['uid'] }}').show(); myhtml.title('{{ $row['uid'] }}', 'Удалить диалог', 'deia', 6)"
                                                 id="deia{{ $row['uid'] }}"
                                                 onMouseOut="$('#deia{{ $row['uid'] }}').hide()"></div>
                                        </div>
                                    </div>
                                    <a href="#" onClick="im.open('{{ $row['uid'] }}'); return false"
                                       class="stretched-link"></a>
                                </div>
                            </div>
                        @endforeach

                    </nav>
                </div>
                <div class="col-6 bg-white im_head border-right pr-0" id="imViewMsg">
                    <div class="no_select">
                        <div class="mb-3">
                            <svg width="5em" height="5em" viewBox="0 0 16 16" class="bi bi-chat-dots"
                                 fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                                <path d="M5 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                            </svg>
                        </div>
                        <div class="text">
                            @_e('im_info')
                        </div>
                    </div>
                </div>
                <div class="col-3 bg-white"></div>
            </div>

        </div>
    </div>
    <style>
        footer {
            display: none
        }
    </style>
    {{--    <script>
            $(document).ready(function(){
                setInterval('im.resize()', 300);
            });
        </script>--}}
@endsection