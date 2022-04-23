@extends('install.install')
@section('content')
    <div class="box ">
        <a href="/install.php">
            <div class="head">
                <div style="color: white;font-size: 1.5em;padding: 10px;margin-left: 5px">Установка</div>
            </div>
        </a>

        <div style="padding: 10px">

            <div class="h1">Установка приложения автоматически заблокирована</div>
            <div style="font-weight: bold">
                Внимание, на сервере обнаружена уже установленная копия приложения.
            </div>
            <div style=" display: flex">
                <input type="submit" class="inp fl_r" style="background: #f44336; margin: 10px" value="Очистить Tephida"
                       onClick="location.href='/install.php?act=clean'"/>
                <input type="submit" class="inp fl_r" style="background: #f44336; margin: 10px;"
                       value="Удалить инсталятор" onClick="location.href='/install.php?act=remove_installer'"/>
            </div>

            <div style="width: 100%;height: 50px">
                <input type="submit" class="inp fl_r" value="Обновить" onClick="location.href='/install.php'"/>
            </div>
        </div>
    </div>
@endsection