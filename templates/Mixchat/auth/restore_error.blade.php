@extends('main.main')
@section('content')
    <div class="err_red">
        <a href="/restore" onClick="Page.Go(this.href); return false">Забыли пароль?</a>
    </div>
@endsection