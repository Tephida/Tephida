<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="viewport" content="width=device-width"/>
@include('main.email_style', [])
<table class="body-wrap">
    <tr>
        <td class="container">
            <table>
                <tr>
                    <td align="center" class="masthead">
                        <h1>Завершение регистрации</h1>
                    </td>
                </tr>
                <tr>
                    <td class="content">
                        <p>Благодарим Вас за регистрацию.</p>
                        <p>
                            Мы требуем от Вас подтверждения Вашей регистрации, для проверки того,
                            что введённый Вами e-mail адрес - реальный.
                            Это требуется для защиты от нежелательных злоупотреблений и спама.
                        </p>
                        <p>Для активации Вашего аккаунта, зайдите по следующей ссылке:</p>
                        <table>
                            <tbody><tr>
                                <td align="center">
                                    <p>
                                        <a href="{{ $home_url }}register/activate?hash={{ $hash }}" class="button">Подтвердить e-mail адрес</a>
                                    </p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <p>
                            Если и при этих действиях ничего не получилось, возможно Ваш аккаунт удалён.
                            В этом случае обратитесь в поддержку, для разрешения проблемы.</p>
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