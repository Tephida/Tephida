<div class="apps_game page_bg border_radius_5" style="padding:20px;padding-bottom:36px" id="app{app-id}">
 <a href="/app{app-id}" onClick="Page.Go(this.href); return false"><img src="{poster}" class="fl_l" height="50" width="50" /></a>
 <a href="/app{app-id}" onClick="Page.Go(this.href); return false">{title}</a>
 <div class="apps_fast_del fl_r cursor_pointer" onClick="apps.mydel('{app-id}', true)" onMouseOver="myhtml.title('{app-id}', 'Удалить игру', 'appsgan')" id="appsgan{app-id}"><img src="{theme}/images/close_a.png" /></div>
 <div class="apps_num">{traf}</div>
</div>
<div class="clear"></div>