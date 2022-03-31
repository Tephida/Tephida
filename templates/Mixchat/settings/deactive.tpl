<div style="padding: 10px 40px 50px;">
    <input id="value_reason" type="hidden"/>
    <h1>Пожалуйста, укажите причину удаления Вашей страницы</h1>

    <div id="settings_deact_reasons">
        <div class="radiobtn settings_reason" onclick="deactive.changeReason(1);" style="padding-top:15px;">
            <div></div>
            У меня есть другая страница
            <div class="settings_reason_desc">Когда-то я создал эту страницу для своей собаки, но теперь она сама
                зарегистрировалась.
            </div>
        </div>
        <br>
        <div class="radiobtn settings_reason" onclick="deactive.changeReason(2);">
            <div></div>
            Сеть отнимает у меня слишком много времени
            <div class="settings_reason_desc">Я не могу жить и работать, пока в интернете есть страница с моим именем.
                Счастливо оставаться, безвольные овощи!
            </div>
        </div>
        <br>
        <div class="radiobtn settings_reason" onclick="deactive.changeReason(3);">
            <div></div>
            Сеть слишком много неприемлемых материалов
            <div class="settings_reason_desc">Я нашел достаточно порнографии и пиратского контента — хватит на всю
                жизнь. Теперь я ухожу.
            </div>
        </div>
        <br>
        <div class="radiobtn settings_reason" onclick="deactive.changeReason(4);">
            <div></div>
            Меня беспокоит безопасность моих данных
            <div class="settings_reason_desc">Тайное мировое правительство, иллюминаты и сионисты охотятся за моими
                личными данными. Я ухожу в подполье.
            </div>
        </div>
        <br>
        <div class="radiobtn settings_reason" onclick="deactive.changeReason(5);">
            <div></div>
            Мою страницу не комментируют
            <div class="settings_reason_desc">Меня окружает стена невнимания. Мои друзья пожалеют о моем уходе, но будет
                поздно.
            </div>
        </div>
        <br>
        <div class="radiobtn settings_reason" id="settings_reason_last" onclick="deactive.changeReason(6);">
            <div></div>
            Другая причина
        </div>
        <br>
        <textarea id="settings_reason_text"
                  onblur="if(this.value=='') this.value='Ваше сообщение..';this.style.color = '#777';"
                  onfocus="if(this.value=='Ваше сообщение..') this.value='';this.style.color = '#000'"
                  class="text settings_reason_text">Ваше сообщение..</textarea>
        <div class="mgclr"></div>
        <div class="html_checkbox html_checked" id="deact" onclick="myhtml.checkbox(this.id)" style="margin-bottom:8px">
            Рассказать друзьям
            <div id="checknox_deact"><input type="hidden" id="deact"></div>
        </div>
        <div class="mgclr"></div>
        <br>
        <div class="button_blue fl_l">
            <button id="deact_page" onClick="deactive.Go(); return false">Удалить страницу</button>
        </div>
    </div>


</div>