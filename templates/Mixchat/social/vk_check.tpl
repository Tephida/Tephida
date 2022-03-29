<script type="text/javascript">
$(document).ready(function(){
	$('#vk_mob_number').focus();
});
function vk_check_mob(){
	var vk_mob_number = $('#vk_mob_number').val();
	if(vk_mob_number != 0){
		butloading('vk_check_mob', '69', 'disabled', '');
		$.ajax({
			type: "POST",
			url: "/index.php?go=social&act=vk_check_mob",
			data: {code: vk_mob_number, to: '{mob_to}', al_page: '{al_page}', hash: '{mob_hash}'},
			success: function(d){
				if(d){
					$('#vk_err_check').html(d).show();
					butloading('vk_check_mob', '69', 'enabled', 'Подтвердить');
				} else 
					Page.Go('/mysocial');
			}
		});
	} else
		setErrorInputMsg('vk_mob_number');
}
</script>
<div style="padding-top:5px">
<div class="err_red no_display pass_errors" id="vk_err_check" style="font-weight:normal;"></div>
{text}
<div style="margin-top:30px">
<div class="fl_l"><span style="color:#777">{mob_number}&nbsp;</span> <input type="text" class="inpst" id="vk_mob_number" /></div>
<div class="button_div fl_l margin_left" style="margin-top:-1px"><button onClick="vk_check_mob(); return false" id="vk_check_mob">Подтвердить</button></div>
<div class="button_div_gray fl_l margin_left" style="margin-top:-1px"><button onClick="vk.logout(); return false">Выйти</button></div>
</div>
</div>