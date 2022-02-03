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
				$('#loadedDocAjax').html('<div class="doc_block" style="margin-left:0px;margin-right:0px" id="doc_block'+row[1]+'"><a href="/index.php?go=doc&act=download&did='+row[1]+'"><div class="doc_format_bg cursor_pointer"><img src="{theme}/images/darr.gif" style="margin-right:5px" />'+row[3]+'</div></a><div id="data_doc'+row[1]+'"><a href="/index.php?go=doc&act=download&did='+row[1]+'"><div class="doc_name cursor_pointer" id="edit_doc_name'+row[1]+'" style="max-width:580px">'+row[0]+'</div></a><img class="fl_l cursor_pointer" style="margin-top:5px;margin-left:5px" src="{theme}/images/close_a.png" onClick="Doc.Del('+row[1]+')" onMouseOver="myhtml.title('+row[1]+', \'Удалить документ\', \'wall_doc_\')" id="wall_doc_'+row[1]+'" /></div><div id="edit_doc_tab'+row[1]+'" class="no_display"><input type="text" class="inpst doc_input" value="'+row[0]+'" maxlength="60" id="edit_val'+row[1]+'" size="60" /><div class="clear" style="margin-top:5px;margin-bottom:35px;margin-left:62px"><div class="button_div fl_l"><button onClick="Doc.SaveEdit('+row[1]+', \'editLnkDoc'+row[1]+'\')">Сохранить</button></div><div class="button_div_gray fl_l margin_left"><button onClick="Doc.CloseEdit('+row[1]+', \'editLnkDoc'+row[1]+'\')">Отмена</button></div></div> </div><div class="doc_sel" onClick="Doc.ShowEdit('+row[1]+', this.id)" id="editLnkDoc'+row[1]+'">Редактировать</div><div class="doc_date clear">'+row[2]+', Добавлено '+row[4]+'</div><div class="clear"></div></div>'+$('#loadedDocAjax').html());
				updateNum('#upDocNum', 1);
				langNumric('langNumric', $('#upDocNum').text(), 'документ', 'документа', 'документов', 'документ', 'документов');
				if($('.doc_block').length != $('#upDocNum').text())
					$('#'+$('.doc_block:last').attr('id')).remove();
			}
			Page.Loading('stop');
		}
	});
	
	langNumric('langNumric', '{doc-num}', 'документ', 'документа', 'документов', 'документ', 'документов');
	
});
var page_cnt = 1;
function docAddedLoadAjax(){
	$('#wall_l_href_doc').attr('onClick', '');
	textLoad('wall_l_href_doc_load');
	$.post('/index.php?go=doc&act=list', {page_cnt: page_cnt}, function(d){
		$('#docAddedLoadAjax').append(d);
		$('#wall_l_href_doc').attr('onClick', 'docAddedLoadAjax()');
		$('#wall_l_href_doc_load').html('Показать еще документы');
		if(!d) $('#wall_l_href_doc').hide();
		page_cnt++;
	});
}
</script>
<div class="cover_edit_title doc_full_pg_top">
<div class="fl_l margin_top_5">У Вас <span id="upDocNum">{doc-num}</span> <span id="langNumric"></span></div>
<div class="button_div fl_r"><button id="upload_2">Добавить документ</button></div>
<div class="clear"></div>
</div>
<div style="height:15px"></div>
<div class="clear"></div>
<div id="loadedDocAjax"></div>