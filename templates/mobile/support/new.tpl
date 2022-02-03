<div id="data">
<div class="tmenuf">
 <a href="/support">[group=4]Вопросы от пользователей[/group][not-group=4]Мои вопросы[/not-group]</a>
 [not-group=4]<div><a href="/support?act=new">Задать вопрос</a></div>[/not-group]
</div>
<div class="clr"></div>
[not-group=4]
<div class="support_bg">
<input type="text" 
	class="inp" 
	style="width:99%;margin-bottom:10px" 
	maxlength="65" 
	id="title"
	placeholder="Пожалуйста, добавьте заголовок к Вашему вопросу.." 
/>
<textarea 
	class="inp" 
	id="question" 
	style="width:99%;height:100px;margin-bottom:10px;"
	placeholder="Пожалуйста, расскажите о Вашей проблеме чуть подробнее.."
></textarea>
<div class="clear"></div>
<button onClick="support.send(); return false" id="send" class="button">Отправить</button>
<div class="clear"></div>
</div>
[/not-group]
[group=4]<div class="info_center"><br /><br /><br />Вы должны всё знать.<br /><br /><br /></div>[/group]
</div>