<div class="tmenuf">
 <a href="/support">[group=4]Вопросы от пользователей[/group][not-group=4]Мои вопросы[/not-group]</a>
 <div><a href="/support?act=show&qid={qid}">Просмотр</a></div>
</div>
<div class="clr"></div>
<div class="onfr">
<a href="/support?act=show&qid={qid}" style="background:#f5f5f5;font-size:13px;color:#000"><b>{title}</b></a><br />
<small>{status}</small>
<div class="clr"></div><div style="height:5px"></div>
<a href="/u{uid}"><img src="{ava}" width="45" height="45" align="left" /></a>
<div style="margin-left:55px"><div><a href="/u{uid}" style="font-size:12px"><b>{name}</b></a></div>
<div style="color:#000">{question}</div>
<div>{date} &nbsp;-&nbsp; <a href="/" onClick="support.delquest('{qid}'); return false">Удалить вопрос</a></div></div>
<div class="clr"></div>
</div>
<div class="clr"></div>
<div id="answers">{answers}</div>
<div class="wallformbg" style="margin-bottom:-12px;border-top:1px solid #EFEFEF">
<textarea id="answer" class="wall_fast_text inp" placeholder="Комментировать.."></textarea>
<button class="button" onClick="support.answer('{qid}', '{uid}'); return false" id="send">Отправить</button>
[group=4]<button onClick="support.close('{qid}'); return false" id="close" class="button">Закрыть вопрос</button>[/group]
</div>