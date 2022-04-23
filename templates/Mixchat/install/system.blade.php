@extends('install.install')
@section('content')
    <div class="box ">
        <a href="/install.php">
            <div class="head">
                <div style="color: white;font-size: 1.5em;padding: 10px;margin-left: 5px">Установка</div>
            </div>
        </a>

        <div style="padding: 10px">


            <div class="h1">Системные требования</div>

            <div class="container">
                <div class="row">
                    <div class="col" style="padding:10px;border: 1px solid #ddd;background: #f7f7f7;">
                        Требования
                    </div>
                    <div class="col" style="padding:10px;border: 1px solid #ddd;background: #f7f7f7;">
                        Статус
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="row">
                    <div class="col" style="padding:10px;border: 1px solid #ddd;">
                        PHP 8.0
                    </div>
                    <div class="col" style="padding:10px;border: 1px solid #ddd;">
                        @if($ext_php)
                            <div style="color: green;">
                                <div style="font-weight: bold">Совместимо</div>
                            </div>
                        @else
                            <div style="color: red;">
                                <div style="font-weight: bold">Не совместимо</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="row">
                    <div class="col" style="padding:10px;border: 1px solid #ddd;">
                        Поддержка MySQLi
                    </div>
                    <div class="col" style="padding:10px;border: 1px solid #ddd;">
                        @if($ext_mysql)
                            <div style="color: green;">
                                <div style="font-weight: bold">Совместимо</div>
                            </div>
                        @else
                            <div style="color: red;">
                                <div style="font-weight: bold">Не совместимо</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="row">
                    <div class="col" style="padding:10px;border: 1px solid #ddd;">
                        Поддержка ZLib
                    </div>
                    <div class="col" style="padding:10px;border: 1px solid #ddd;">
                        @if($ext_zlib)
                            <div style="color: green;">
                                <div style="font-weight: bold">Совместимо</div>
                            </div>
                        @else
                            <div style="color: red;">
                                <div style="font-weight: bold">Не совместимо</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="row">
                    <div class="col" style="padding:10px;border: 1px solid #ddd;">
                        Поддержка GD
                    </div>
                    <div class="col" style="padding:10px;border: 1px solid #ddd;">
                        @if($ext_gd)
                            <div style="color: green;">
                                <div style="font-weight: bold">Совместимо</div>
                            </div>
                        @else
                            <div style="color: red;">
                                <div style="font-weight: bold">Не совместимо</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="row">
                    <div class="col" style="padding:10px;border: 1px solid #ddd;">
                        Поддержка ICONV
                    </div>
                    <div class="col" style="padding:10px;border: 1px solid #ddd;">
                        @if($ext_iconv)
                            <div style="color: green;">
                                <div style="font-weight: bold">Совместимо</div>
                            </div>
                        @else
                            <div style="color: red;">
                                <div style="font-weight: bold">Не совместимо</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div style="background:lightyellow;padding:10px;margin-bottom:10px;margin-top:10px;border:1px dashed #ccc;">
                <div style="margin-bottom:7px;text-align: center;font-size: 12px;">
                    <div style="font-weight: bold">Если любой из этих пунктов выделен красным,
                        то выполните действия для исправления положения. <br/>
                        В случае несоблюдения минимальных требований скрипта возможна
                        его некорректная работа в системе.
                    </div>
                </div>
            </div>

            <input type="submit" class="inp" value="Продолжить &raquo;"
                   onClick="location.href='/install.php?act=settings'"/>


        </div>
    </div>
@endsection