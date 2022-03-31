<div class="buttonsprofile">
    <a href="/support" onClick="Page.Go(this.href); return false;">[group=4]Вопросы от
        пользователей[/group][not-group=4]Мои вопросы[/not-group]</a>
    <div class="activetab"><a href="/support?act=show&qid={qid}" onClick="Page.Go(this.href); return false;">Просмотр
            вопроса</a></div>
</div>
<div class="note_full_title border_radius_5 clear">
    <span><a href="/support?act=show&qid={qid}" onClick="Page.Go(this.href); return false">{title}</a></span><br/>
    <div id="status">{status}</div>
</div>
<div class="page_bg border_radius_5">
    <div class="ava_mini" style="float:width:60px"><a href="/u{uid}" onClick="Page.Go(this.href); return false"><img
                    src="{ava}" alt="" title=""/></a></div>
    <div class="wallauthor" style="padding-left:0px"><a href="/u{uid}"
                                                        onClick="Page.Go(this.href); return false">{name}</a></div>
    <div style="float:left;width:760px">
        <div class="walltext">
            <div style="padding-left:2px">
                {question}
                <br/><span class="color777">{date}</span> <a href="/" class="fl_r"
                                                             onClick="support.delquest('{qid}'); return false">Удалить
                    вопрос</a>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
<div class="clear"></div>
<div id="answers">{answers}</div>
<div class="support_addform border_radius_5">
    <div class="ava_mini">
        [group=4]<img src="/images/support.png" alt=""/>[/group]
        [not-group=4]<a href="/u{uid}" onClick="Page.Go(this.href); return false"><img src="{ava}" alt=""/></a>[/not-group]
    </div>
    <textarea
            class="videos_input wysiwyg_inpt fl_l"
            id="answer"
            style="width:755px;height:78px;color:#c1cad0"
            onblur="if(this.value==''){this.value='Комментировать..';this.style.color = '#c1cad0';}"
            onfocus="if(this.value=='Комментировать..'){this.value='';this.style.color = '#000'}"
    >Комментировать..</textarea>
    <div class="clear"></div>
    <div class="button_div fl_l" style="margin-left:60px">
        <button onClick="support.answer('{qid}', '{uid}'); return false" id="send">Отправить</button>
    </div>
    [group=4]
    <div class="button_div_nostl fl_r" id="close_but">
        <button onClick="support.close('{qid}'); return false" id="close">Закрыть вопрос</button>
    </div>
    [/group]
    <div class="clear"></div>
</div>