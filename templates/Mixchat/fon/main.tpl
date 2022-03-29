<script type="text/javascript">
	ajaxUpload = new AjaxUpload('upload_fon_facemy', {
		action: '/index.php?go=fon&act=upload_fon',
		name: 'uploadfile',
		onSubmit: function (file, ext) {
			if(!(ext && /^(jpg|png|jpeg|gif|jpe)$/.test(ext))) {
                Page.addAllErr(lang_bad_format, 3300);
				return false;
			}
			$('.fon_facemy_bg').css('opacity', '0.4');
		},
		onComplete: function (file, row){
			if(row == 1 || row == 2)
                Page.addAllErr('Максимальны размер 10 МБ.', 3300);
			else {
				$('.fon_facemy_hidden, .fon_facemy_load_img').show();
				$('#upload_fon_facemy').text('Изменить фото');
				$('.fon_facemy_bg').css('opacity', '1');
				row = row.split('|');
				rheihht = row[1];
				fon_facemy.init(''+row[0], rheihht);
			}
		}
	});
</script>

<div class="miniature_box">

<div class="miniature_pos" style="width:400px">

<div class="miniature_title fl_l apps_box_text">Облик</div><a class="cursor_pointer fl_r" style="font-size:12px" onClick="viiBox.clos('fon_mouse', 1)">Закрыть</a>

<div class="clear"></div>

<div class="fm_item_info"><center>Сделайте внешний вид удобным, и привлекательным, для вас и других. Поддерживаются форматы JPG, PNG и GIF.<br /> -  Если у вас имеется облик, то вы можете его убрать. <br /><a class="cursor_pointer" onClick="fon_facemy.del('{id}')"><b>Очистить облик</b></a></center>

</div>

<div class="clear"></div>

<div style="margin:10px 0;padding-bottom: 10px;border-bottom: 1px dashed #EBDFDF;">Загрузить с компьютера.</div>

<div class="fon_facemy_bg">

<div class="fon_facemy_add"><div class="fon_facemy_hidden" style="float:left;margin-right:10px;" onClick="fon_facemy.reload_facemy('{id}')">Просмотреть</div>

<div id="upload_fon_facemy" style="float:left;">Добавить облик</div>

</div> 

<div class="fon_facemy_load_img"><img src="{fon_facemy}" width="400" id="fon_facemy_img" /><div id="fon_facemy_restart"></div></div></div>

<div class="clear"></div></div></div>