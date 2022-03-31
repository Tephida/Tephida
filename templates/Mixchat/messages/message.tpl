<div id="bmsg_{mid}">
    <div class="msg_div">
        <div class="msg_ava fl_l">
            <a href="u{user-id}" onClick="Page.Go(this.href); return false">
                <img src="{ava}" alt=""/>
            </a>
        </div>
        <div class="msg_name [new]msg_new_date[/new]">
            <a href="u{user-id}" onClick="Page.Go(this.href); return false">{name}</a>
            &nbsp;&nbsp;<font>{online}</font>
            <span [new]class="msg_new" [/new]>{date}</span>
        </div>
        <div class="msg_text">
            <div class="delicious"></div>
            {text}
            <div class="panel_msg">
                <a href="/messages/show/{mid}" onClick="Page.Go(this.href); return false">Ответить</a>
                <a href="/" class="fl_r"
                   onClick="messages.delet({mid}, '{folder}'); return false" id="del_text_{mid}">Удалить</a>
                <img src="/images/loading_mini.gif" id="del_load_{mid}" class="no_display"/>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
