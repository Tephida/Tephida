<script type="text/javascript">
$(document).ready(function(){
	videos.scroll();
});
</script>
<div class="buttonsprofile albumsbuttonsprofile" style="height:10px;">
 <div class="activetab"><a href="/videos/{user-id}" onClick="Page.Go(this.href); return false;"><div>[owner]Все видеозаписи[/owner][not-owner]К видеозаписям {name}[/not-owner]</div></a></div>
 [admin-video-add][owner]<a href="/" onClick="videos.add(); return false;">Добавить видеоролик</a>[/owner][/admin-video-add]
 [not-owner]<a href="/u{user-id}" onClick="Page.Go(this.href); return false;">К странице {name}</a>[/not-owner]
</div>
<div class="clear"></div><div style="margin-top:10px;"></div>
<input type="hidden" value="{user-id}" id="user_id" />
<input type="hidden" id="set_last_id" />
<input type="hidden" id="videos_num" value="{videos_num}" />