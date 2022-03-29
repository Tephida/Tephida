<script type="text/javascript" src="/system/inc/js/jquery.js"></script>
<script type="text/javascript" src="/system/inc/js/upload.photo.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	aj1 = new AjaxUpload('upload', {
		action: '?go=apps&act=add',
		name: 'uploadfile',
		data: {
			add_act: 'upload'
		},
		accept: 'image/*',
		onSubmit: function (file, ext) {
			if(!(ext && /^(jpg|png|jpeg|gif|jpe)$/.test(ext))) {
				alert('Неверный формат файла');
				return false;
			}
			$('#upload').hide();
			$('#prog_poster').show();
		},
		onComplete: function (file, row){
			if(row == 1){
				alert('Файл привышает 5 МБ');
			} else {
				$('#r_poster').attr('src', '/uploads/apps/temp/'+row).show();
			}
			$('#upload').show();
			$('#prog_poster').hide();
		}
	});
	aj2 = new AjaxUpload('upload_2', {
		action: '?go=apps&act=add',
		name: 'uploadfile',
		data: {
			add_act: 'upload_swf',
		},
		onSubmit: function (file, ext) {
			if(!(ext && /^(swf)$/.test(ext))) {
				alert('Неверный формат файла');
				return false;
			}
			$('#upload_2').hide();
			$('#prog_flash').show();
		},
		onComplete: function (file, row){
			if(row == 1){
				alert('Файл привышает 100 МБ');
			} else {
				$('#ok_swf').text('Файл загружен!').css('color', 'green');
			}
			$('#upload_2').show();
			$('#prog_flash').hide();
		}
	});
	aj3 = new AjaxUpload('upload_3', {
		action: '?go=apps&act=add',
		name: 'uploadfile',
		data: {
			add_act: 'upload_scrin'
		},
		accept: 'image/*',
		onSubmit: function (file, ext) {
			if(!(ext && /^(jpg|png|jpeg|gif|jpe)$/.test(ext))) {
				alert('Неверный формат файла');
				return false;
			}
			$('#upload_3').hide();
			$('#prog_scrins').show();
		},
		onComplete: function (file, row){
			if(row == 1){
				alert('Файл привышает 5 МБ');
			} else if(row == 3){
				alert('Максимальное кол-во скринов: 4');
			} else {
				file = row.split('/m');
				fid = file[1].split('.');
				$('#scrins').append('<img id="'+fid[0]+'" onClick="del(\''+file[1]+'\')" src="/uploads/apps/temp/'+row+'" title="Удалить" style="cursor:pointer;margin-top:8px;margin-bottom:8px;margin-left:2px;margin-right:2px" />');
			}
			$('#upload_3').show();
			$('#prog_scrins').hide();
		}
	});
});
function del(f){
	fid = f.split('.');
	$('#'+fid[0]).remove();
	$.post('?go=apps&act=add', {file: f, add_act: 'del'});
}
</script>

<style type="text/css" media="all">
.inpu{width:350px;}
textarea{width:450px;height:100px;}
</style>

<div class="msg_speedbar clear">Добавить игру</div>

<div class="page_bg margin_top_10 border_radius_5">
<form action="" method="POST">
<input type="hidden" name="add_act" value="send" />

<div class="texta">Название:</div>
<input type="text" name="title" class="inpst" style="width:200px;" />
<div class="mgclr"></div>

<div class="texta">Описание:</div>
<textarea type="text" name="descr" class="inpst" style="width:200px;"></textarea>
<div class="mgclr"></div>

<div class="texta">Постер 75х75:</div>
<input type="submit" value="Выбрать файл" class="inp" id="upload" /><br />
<div id="prog_poster" style="display:none;margin-top:-11px;background:url('/system/inc/images/progress_grad.gif');width:94px;height:18px;border:1px solid #006699;margin-left:182px"></div>
 <div style="margin-left:182px"><small>Файл не должен превышать 5 Mб.</small></div>
 <img src="" id="r_poster" style="margin-left:182px;display:none" />
<div class="mgclr"></div>

<div class="texta">Flash игра .swf:</div>
<input type="submit" value="Выбрать файл" class="inp" id="upload_2" style="margin-top:0px" /><br />
<div id="prog_flash" style="display:none;margin-top:-11px;background:url('/system/inc/images/progress_grad.gif');width:94px;height:18px;border:1px solid #006699;margin-left:182px"></div>
<div style="margin-left:180px"><small id="ok_swf">Файл не должен превышать 100 Mб.</small></div>
<div class="mgclr"></div>

<div class="texta">Ширина flash игры (px):</div>
<input type="text" name="width" class="inpst" value="795" style="width:200px;" />
<div class="mgclr"></div>

<div class="texta">Высота flash игры (px):</div>
<input type="text" name="height" class="inpst" value="495" style="width:200px;" />
<div class="mgclr"></div>

<div class="texta">Скриншоты 607х376:</div>
<input type="submit" value="Выбрать файл" class="inp" id="upload_3" style="margin-top:0px" /><br />
<div id="prog_scrins" style="display:none;margin-top:-11px;background:url('/system/inc/images/progress_grad.gif');width:94px;height:18px;border:1px solid #006699;margin-left:182px"></div>
<div style="margin-left:180px"><small>Файл не должен превышать 5 Mб.</small></div>
<center><span id="scrins"></span></center>
<div class="mgclr"></div>


<div class="texta">&nbsp;</div>
<div class="button_div fl_l">
<input type="submit" value="Добавить игру" name="send" style="margin-top:0px" />
</div>
<div class="mgclr"></div>
</form>
</div>

