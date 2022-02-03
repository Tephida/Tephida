[all-friends]
<div class="buttonsprofile albumsbuttonsprofile" style="height:10px;">
 <div class="activetab"><a href="/friends/{user-id}" onClick="Page.Go(this.href); return false;"><div>Все друзья</div></a></div>
 <a href="/friends/online/{user-id}" onClick="Page.Go(this.href); return false;">Друзья на сайте</a>
 [owner]<a href="/friends/requests" onClick="Page.Go(this.href); return false;">Заявки в друзья {demands}</a>[/owner]
 [not-owner]<a href="/friends/common/{user-id}" onClick="Page.Go(this.href); return false;">Общие друзья</a>
 <a href="/u{user-id}" onClick="Page.Go(this.href); return false;">К странице {name}</a>[/not-owner]
</div>
<div class="clear"></div><div style="margin-top:10px;"></div>
[/all-friends]

[request-friends]
<div class="buttonsprofile albumsbuttonsprofile" style="height:10px;">
 <a href="/friends/{user-id}" onClick="Page.Go(this.href); return false;">Все друзья</a>
 <a href="/friends/online/{user-id}" onClick="Page.Go(this.href); return false;">Друзья на сайте</a>
 <div class="activetab"><a href="/friends/requests" onClick="Page.Go(this.href); return false;"><div>Заявки в друзья {demands}</div></a></div>
</div>
<div class="clear"></div><div style="margin-top:10px;"></div>
[/request-friends]

[online-friends]
<div class="buttonsprofile albumsbuttonsprofile" style="height:10px;">
 <a href="/friends/{user-id}" onClick="Page.Go(this.href); return false;">Все друзья</a>
 <div class="activetab"><a href="/friends/online/{user-id}" onClick="Page.Go(this.href); return false;"><div>Друзья на сайте</div></a></div>
 [owner]<a href="/friends/requests" onClick="Page.Go(this.href); return false;">Заявки в друзья {demands}</a>[/owner]
 [not-owner]<a href="/friends/common/{user-id}" onClick="Page.Go(this.href); return false;">Общие друзья</a>
 <a href="/u{user-id}" onClick="Page.Go(this.href); return false;">К странице {name}</a>[/not-owner]
</div>
<div class="clear"></div><div style="margin-top:10px;"></div>
[/online-friends]