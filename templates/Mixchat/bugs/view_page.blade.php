@extends('app.app')
@section('content')
    <script type="text/javascript" src="/js/bugs.js"></script>
    <link href="/style/bugs.css" type="text/css" rel="stylesheet">
    <style>
        .nav a:hover {
            text-decoration: none;
            text-shadow: 1px 1px 4px #000000;
            border-bottom: none;
        }
    </style>
    <div class="container">
        <div class="d-flex justify-content-between">
            <div class="col">
                <h2 onclick="Page.Go('/bugs/'); return false;">Ошибки</h2>
                {{ $menu }}
            </div>
            <div >
                <button type="button" class="btn btn-primary icon-plus-6" onclick="bugs.box();" id="bugs_add_btn2"
                        onMouseOver="myhtml.title('_btn2', 'Сообщить о баге', 'bugs_add');">Сообщить о баге
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                @if($bugs)
                    @include('bugs.record_one', array('bugs' => $bugs))
                @else
                    <div class="info_center"><br><br>Ни чего не найдено<br><br></div>
                @endif
            </div>
        </div>
    </div>
@endsection