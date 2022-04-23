@extends('install.install')
@section('content')
    <div class="box ">
        <a href="/install.php">
            <div class="head">
                <div style="color: white;font-size: 1.5em;padding: 10px;margin-left: 5px">Установка</div>
            </div>
        </a>

        <div style="padding: 10px">
            <div class="h1">Мастер установки скрипта</div>

            <div style="margin: 10px 0 10px 0">Добро пожаловать в мастер установки приложения.</div>
            Данный мастер поможет вам установить приложение всего за пару минут.

            <div style="color: red;margin-bottom: 1em">Внимание: при установке приложения создается структура базы
                данных,
                создается аккаунт администратора,
                а также прописываются основные настройки системы.
            </div>

            <div class="d-flex justify-content-between">
                Приятной Вам работы!
                <input type="submit" class="inp" value="Начать установку"
                       onClick="location.href='/install.php?act=settings'"/>
            </div>
        </div>
    </div>
@endsection