<div id="data">
    <div class="buttonsprofile">
        <a href="/support" onClick="Page.Go(this.href); return false;">[group=4]Вопросы от
            пользователей[/group][not-group=4]Мои вопросы[/not-group]</a>
        <div class="activetab"><a href="/support?act=new" onClick="Page.Go(this.href); return false;">Задать вопрос</a>
        </div>
    </div>
    [not-group=4]
    <div class="page_bg clear support_bg border_radius_5">
        Здесь Вы можете сообщить нам о любой проблеме, связанной с <b>нашим сайтом</b>.
        <input type="text"
               class="videos_input"
               style="width:580px;margin-top:10px;color:#c1cad0"
               maxlength="65"
               id="title"
               value="Пожалуйста, добавьте заголовок к Вашему вопросу.."
               onblur="if(this.value==''){this.value='Пожалуйста, добавьте заголовок к Вашему вопросу..';this.style.color = '#c1cad0';}"
               onfocus="if(this.value=='Пожалуйста, добавьте заголовок к Вашему вопросу..'){this.value='';this.style.color = '#000'}"
        />
        <textarea
                class="videos_input wysiwyg_inpt"
                id="question"
                style="width:580px;height:100px;color:#c1cad0"
                onblur="if(this.value==''){this.value='Пожалуйста, расскажите о Вашей проблеме чуть подробнее..';this.style.color = '#c1cad0';}"
                onfocus="if(this.value=='Пожалуйста, расскажите о Вашей проблеме чуть подробнее..'){this.value='';this.style.color = '#000'}"
        >Пожалуйста, расскажите о Вашей проблеме чуть подробнее..</textarea>
        <div class="clear"></div>
        <div class="button_div fl_l">
            <button onClick="support.send(); return false" id="send">Отправить</button>
        </div>
        <div class="button_div_gray fl_l margin_left" id="cancel">
            <button onClick="Page.Go('/u{uid}'); return false;">Отмена</button>
        </div>
        <div class="clear"></div>
    </div>
    [/not-group]
    [group=4]
    <div class="info_center"><br/><br/><br/>Вы должны всё знать.<br/><br/><br/></div>
    [/group]
</div>