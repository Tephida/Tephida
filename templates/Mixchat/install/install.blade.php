@if((new \FluffyDollop\Http\Request)->checkAjax() === false)<!DOCTYPE>
<html lang="ru">
<head>
    <title>Установка приложения</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<style media="all">
    body {
        font-size: 0.8em;
        font-family: Tahoma;
        background: linear-gradient(180deg, #0d789c, #c8eeb1, white, white) repeat-x;
    }

    a {
        color: #4274a5;
        text-decoration: underline
    }

    a:hover {
        color: #4274a5;
        text-decoration: none
    }

    .box {
        margin: auto;
        width: 800px;
        background: #fff;
        box-shadow: 0 1px 4px 1px #cfcfcf;

        border-radius: 5px;
    }

    .head {
        background: linear-gradient(0deg, #1993b0, #1993b0, #3db9c2) repeat-x;
        height: 49px;
        border-top-left-radius: 5px;
    }

    .h1 {
        font-size: 1.2em;
        font-weight: bold;
        color: #4274a5;
        margin: 5px;
        padding-bottom: 2px;
        border-bottom: 1px solid #e5edf5;
        padding-left: 2px
    }

    .clr {
        clear: both
    }

    .fl_l {
        float: left
    }

    .fl_r {
        float: right
    }

    .inp {
        padding: 5px 10px 5px 10px;
        background: linear-gradient(45deg, #b7c42d, #8d991b);
        color: #fff;
        font-size: 11px;
        font-family: Tahoma, Verdana, Arial, sans-serif, Lucida Sans;
        text-shadow: 0 1px 0 #767f18;
        border: 0;
        border-top: 1px solid #cdd483;
        cursor: pointer;
        margin: 10px 0 0 0;
        font-weight: bold;
        border-radius: 2px;
        box-shadow: inset 0 1px 3px 0 #d2d2d2;
    }

    .inp:hover {
        background: linear-gradient(180deg, #c6d059, #a3ae36);
    }

    .inp:active {
        background: #848f18;
        position: relative;
        border-top: 1px solid #727c0e;
        outline: none
    }

    .inpu {
        width: 200px;
        box-shadow: inset 0 1px 3px 0 #d2d2d2;
        border: 1px solid #ccc;
        padding: 4px;
        border-radius: 3px;
        font-size: 11px;
        font-family: tahoma;
        margin-bottom: 3px;
    }

    textarea {
        width: 300px;
        height: 100px;
    }

    .fllogall {
        color: #555
    }
</style>
@endif
@yield('content')
@if((new \FluffyDollop\Http\Request)->checkAjax() === false)
</body>
</html>
@endif