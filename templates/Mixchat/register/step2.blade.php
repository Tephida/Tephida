<div class="reg_box_pad">
    <div class="err_yellow no_display" id="ok_reg" style="font-weight:normal;font-size:11px">
        <b>Активация аккаунта</b><br/>
        Запрос на регистрацию принят.<br><br>
        Администрация сайта требует реальности всех вводимых E-mail адресов. Через <b>10 минут</b> (возможно и раньше)
        Вы <b>получите письмо с инструкциями следующего шага</b>.
        Еще немного, и Вы будете зарегистрированы на сайте.
        Если в течении этого времени Вы не получили письма с подтверждением, то повторите попытку используя другой
        E-Mail адрес или обратитесь к администратору сайта.<br>Внимание!
        Возможны проблемы с доставкой на E-mail адреса бесплатных почтовых серверов.
        Если письма нет в папке <b>Входящие</b>, то посмотрите в папке <b>Спам</b>.
        <div class="clear"></div>
    </div>
    <div id="reg_box_step2">
        <b>Пожалуйста, укажите Ваш электронный адрес.</b><br/>
        На указанный Вами email адрес, будет отправлено письмо с дальнейшими инструкциями.
        <div class="err_red no_display" id="err_reg_2"
             style="font-weight:normal;position:absolute;margin-top:10px;width:400px;margin-left:20px"></div>
        <div style="margin-left:155px;margin-top:60px">
            <div class="videos_text">Ваш электронный адрес</div>
            <label for="reg_email"></label>
            <input type="text" class="videos_input" style="width:155px" placeholder="Введите email" maxlength="65"
                   id="reg_email"/>
            <div class="videos_text">Защитный код</div>
            <div class="cursor_pointer" onClick="updateCode(); return false">
                <div id="sec_code"><img src="/security/img?rndval={{ $rndval }}" alt="" title="Показать другой код"
                                        width="120" height="50"/></div>
            </div>
            <input type="text" class="videos_input" style="width:155px;margin-top:10px"
                   placeholder="Введите защитный код" maxlength="5" id="reg_sec_code"/>
        </div>
    </div>
</div>