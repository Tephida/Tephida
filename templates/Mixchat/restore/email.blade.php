<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="viewport" content="width=device-width"/>
@include('main.email_style', [])
<table class="body-wrap">
    <tr>
        <td class="container">
            <table>
                <tr>
                    <td class="content">
                        <p>Здравствуйте, {{ $user_name }}</p>
                        <p>Чтобы сменить ваш пароль, пройдите по этой ссылке:</p>
                        <table>
                            <tbody><tr>
                                <td align="center">
                                    <p>
                                        <a href="{{ $home_url }}restore/prefinish?h={{ $hash }}" class="button">Сменить пароль</a>
                                    </p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <p>С уважением, <em>Mixchat</em> {{ $home_url }}.</p>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
    <tr>
        <td class="container">
            <table>
                <tr>
                    <td class="content footer" align="center">
                        <p>Sent by <a href="https://mixchat.ru">Mixchat</a></p>
                        <p><a href="mailto:">support@mixchat.ru</a></p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>