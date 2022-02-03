<script type="text/javascript">
$(document).ready(function(){
	Xajax = new AjaxUpload('upload_2', {
		action: '/index.php?go=doc&act=upload',
		name: 'uploadfile',
		onSubmit: function (file, ext) {
			if(!(ext && /^(doc|docx|xls|xlsx|ppt|pptx|rtf|pdf|png|jpg|gif|psd|mp3|djvu|fb2|ps|jpeg|txt)$/.test(ext))) {
				addAllErr('Неверный формат файла', 3300);
				return false;
			}
			Page.Loading('start');
		},
		onComplete: function (file, row){
			if(row == 1)
				addAllErr('Превышен максимальный размер файла 10 МБ', 3300);
			else {
				row = row.split('"');
				Doc.AddAttach(row[0], row[1]);
			}
			Page.Loading('stop');
		}
	});
});

langNumric('langNumric', '{doc-num}', 'документ', 'документа', 'документов', 'документ', 'документов');

var page_cnt = 1;
function docAddedLoadAjax(){
	$('#wall_l_href_doc').attr('onClick', '');
	textLoad('wall_l_href_doc_load');
	$.post('/index.php?go=doc', {page_cnt: page_cnt}, function(d){
		$('#docAddedLoadAjax').append(d);
		$('#wall_l_href_doc').attr('onClick', 'docAddedLoadAjax()');
		$('#wall_l_href_doc_load').html('Показать еще документы');
		if(!d) $('#wall_l_href_doc').hide();
		page_cnt++;
	});
}
</script>
<div class="cover_edit_title fixed" style="width:590px">
<div class="fl_l margin_top_5">Всего {doc-num} <span id="langNumric"></span></div>
<div class="button_div_gray fl_r"><button id="upload_2">Загрузить новый документ</button></div>
<div class="clear"></div>
</div>
<div class="public_wall_photos_shadow fixed"></div>
<div style="height:50px"></div>
<div class="clear"></div>