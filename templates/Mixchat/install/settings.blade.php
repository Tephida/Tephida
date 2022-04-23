@extends('install.install')
@section('content')
    <div class="box ">
        <a href="/install.php">
            <div class="head">
                <div style="color: white;font-size: 1.5em;padding: 10px;margin-left: 5px">Установка</div>
            </div>
        </a>

        <div style="padding: 10px">
            <form method="POST" action="/install.php?act=install">

                <div class="h1">Настройка конфигурации системы</div>
                <div class="fllogall">Адрес сайта:</div>
                <input type="text" name="url" class="inpu" value="https://{{ $url }}/"/>&nbsp;&nbsp;<span
                        style="display:flex;color:#777">Укажите путь без имени файла, знак слеша <div
                            style="color:red;"> / </div> на конце обязателен</span>
                <div class="mgcler"></div>

                <div class="h1" style="margin-top:15px">Данные для доступа к MySQL серверу</div>
                <div class="fllogall">Сервер MySQL:</div>
                <input type="text" name="mysql_server" class="inpu" value="localhost"/>
                <div class="mgcler"></div>
                <div class="fllogall">Имя базы данных:</div>
                <input type="text" name="mysql_dbname" class="inpu"/>
                <div class="mgcler"></div>
                <div class="fllogall">Имя пользователя:</div>
                <input type="text" name="mysql_dbuser" class="inpu"/>
                <div class="mgcler"></div>
                <div class="fllogall">Пароль:</div>
                <input type="text" name="mysql_pass" class="inpu"/>
                <div class="mgcler"></div>

                <div class="h1" style="margin-top:15px">Данные для доступа к панели управления</div>
                <div class="fllogall">файл админпанели:</div>
                <input type="text" name="adminfile" class="inpu" value="adminpanel.php"/>
                <div class="mgcler"></div>
                <div class="fllogall">Имя администратора:</div>
                <input type="text" name="name" class="inpu"/>
                <div class="mgcler"></div>
                <div class="fllogall">Фамилия администратора:</div>
                <input type="text" name="lastname" class="inpu"/>
                <div class="mgcler"></div>
                <div class="fllogall">E-mail:</div>
                <input type="text" name="email" class="inpu"/>
                <div class="mgcler"></div>
                <div class="fllogall">Пароль:</div>
                <input type="password" name="pass" class="inpu"/>
                <div class="mgcler"></div>

                <input type="submit" class="inp" value="Завершить установку &raquo;"
                       onClick="location.href=\'/install.php?act=settings\'"/>

            </form>
        </div>
    </div>
@endsection