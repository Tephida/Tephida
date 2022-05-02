@extends('main.main')
@section('content')
    <div class="container-lg">
        <div class="card">
            <div class="card-body">
                <div class="row-">
                    <div class="col-4-">
                        <nav class="navbar navbar-light">
                            <div class="container-fluid">
                                <a href="/support/" onClick="Page.Go(this.href); return false;"
                                   class="navbar-brand mb-0 h1">{{ $support_title }}</a>
                            </div>
                        </nav>
                        @if($group > 4)
                            <nav class="navbar navbar-light">
                                <div class="container-fluid">
                                    <a href="/support/new/"
                                       onClick="Page.Go(this.href); return false;" class="navbar-brand">Задать
                                        вопрос</a>
                                </div>
                            </nav>@endif
                    </div>
                    <div class="col-8-">
                        <div class="margin_top_10"></div>
                        <div class="allbar_title" style="border-bottom:0;margin-bottom:0">{{ $cnt }}</div>
                        @if(isset($questions))
                            @foreach($questions as $row)
                                <div class="support_questtitle">
                                    <div class="support_title_inpad fl_l">
                                        <a href="/support/show/{{ $row['qid'] }}/"
                                           onClick="Page.Go(this.href); return false">
                                            <b>{{ $row['title'] }}</b>
                                        </a>
                                        <br/>
                                        {{ $row['status'] }}
                                    </div>
                                    <a href="/support/show/{{ $row['id'] }}/" onClick="Page.Go(this.href); return false"
                                       class="support_last_answer fl_r" style="font-size:11px">
                                        <img src="{{ $row['ava'] }}" alt="" width="35"/>
                                        {{ $row['name'] }}
                                        <br/>
                                        <span class="color777">{{ $row['answer'] }} {{ $row['date'] }}</span>
                                    </a>
                                    <div class="clear"></div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection