[inbox]
<script type="text/javascript">
    $(document).ready(function () {
        var query = $('#msg_query').val();
        if (query == 'Поиск по полученным сообщениям')
            $('#msg_query').css('color', '#c1cad0');
    });
</script>
<style>.nav{margin-top:10px}</style>
<div class="buttonsprofile">
    <div class="activetab"><a href="/messages" onClick="Page.Go(this.href); return false;">
            <div>Полученные</div>
        </a></div>
    <a href="/messages/outbox" onClick="Page.Go(this.href); return false;">Отправленные</a>
    <div class="msg_se_bg">
        <input type="text" value="{query}" class="msg_se_inp"
               onBlur="if(this.value==''){this.value='Поиск по полученным сообщениям';this.style.color = '#c1cad0';}"
               onFocus="if(this.value=='Поиск по полученным сообщениям'){this.value='';this.style.color = '#000'}"
               id="msg_query" maxlength="130" onKeyPress="if(event.keyCode == 13)messages.search();"/>
        <button class="button fl_l" onClick="messages.search(); return false">Найти</button>
    </div>
</div>
<div class="clear"></div>
<div class="msg_speedbar">{msg-cnt} &nbsp;|&nbsp; <a href="/" style="font-weight:normal"
                                                     onClick="im.settTypeMsg(); return false"
                                                     id="settTypeMsg">{msg-type}</a></div>[/inbox]
[outbox]
<script type="text/javascript">
    $(document).ready(function () {
        var query = $('#msg_query').val();
        if (query == 'Поиск по отправленным сообщениям')
            $('#msg_query').css('color', '#c1cad0')
    });
</script>
<style>.nav{margin-top:10px}</style>
<div class="buttonsprofile">
    <a href="/messages" onClick="Page.Go(this.href); return false;">Полученные</a>
    <div class="activetab"><a href="/messages/outbox" onClick="Page.Go(this.href); return false;">
            <div>Отправленные</div>
        </a></div>
    <div class="msg_se_bg"><input type="text" value="{query}" class="msg_se_inp"
                                  onblur="if(this.value==''){this.value='Поиск по отправленным сообщениям';this.style.color = '#c1cad0';}"
                                  onfocus="if(this.value=='Поиск по отправленным сообщениям'){this.value='';this.style.color = '#000'}"
                                  id="msg_query" maxlength="130"
                                  onKeyPress="if(event.keyCode == 13)messages.search(1);"/>
        <button onClick="messages.search(1); return false" class="button">Найти</button>
        <div class="clear"></div>
    </div>
</div>
<div class="clear"></div>
<div class="msg_speedbar">{msg-cnt}</div>[/outbox]
[review]
<div class="buttonsprofile albumsbuttonsprofile" style="height:10px">
    <a href="/messages" onClick="Page.Go(this.href); return false;">Полученные</a>
    <a href="/messages/outbox" onClick="Page.Go(this.href); return false;">Отправленные</a>
    <div class="activetab"><a href="/" onClick="Page.Go('/messages/show/{mid}'); return false">
            <div>Просмотр сообщения</div>
        </a></div>
</div>
<div class="clear"></div>[/review]