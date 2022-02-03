<script type="text/javascript">
$(document).ready(function(){
	$(window).scroll(function(){
		if($(document).height() - $(window).height() <= $(window).scrollTop()+($(document).height()/2-250)){
			groups.invitePage()
		}
	});
});
var page_cnt_invite_gr = 1;
</script>
<div id="preLoadedGr"></div>
<div class="clear"></div>
<div class="rate_alluser cursor_pointer" style="margin:0px" onClick="groups.invitePage()" id="gr_invite_prev_ubut"><div id="load_gr_invite_prev_ubut">Показать больше приглашений</div></div>