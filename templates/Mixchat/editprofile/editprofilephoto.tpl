<script type="text/javascript" src="{theme}/js/profile_edit.js"></script>
<div class="buttonsprofile">
 <a href="/editmypage" onClick="Page.Go(this.href); return false;">Основное</a>
 <a href="/editmypage/contact" onClick="Page.Go(this.href); return false;">Контакты</a>
 <a href="/editmypage/interests" onClick="Page.Go(this.href); return false;">Интересы</a>
 <a href="/editmypage/all" onClick="Page.Go(this.href); return false;">Другое</a>
 <div class="activetab"><a href="/editmypage/photo" onClick="Page.Go(this.href); return false;"><div>Фото</div></a></div>
</div>
<div class="clear"></div>
<div class="err_red no_display pass_errors" id="delete_ok" style="font-weight:normal;">Спецэффект успешно удален.</div>
<style>
/* EDIT PHOTO */
.photo h4 {border-bottom: 1px solid #B9C4DA;font-size: 13px;margin: 0;padding: 0 0 2px;}
.editorPanel {padding: 10px 0px;background: #f7f7f7; }
.editor {margin: 3px 0px 11px 22px;width: 580px;}
.editor_panel {padding: 10px 0px;background: #f7f7f7}
.editor td {border: none;margin: 0px;padding: 5px 1px 1px;vertical-align: top;}
.editor td.label {text-align: right;padding-right: 15px;width:150px;color: #555;}
.editor td.labelHigh {text-align: right;vertical-align: top;padding: 10px 15px 0px 0px;width: 150px;color: #555;}
.editor td.labelHigh div{font-weight: normal;font-size:10px;color: #999;}
</style>

<div class="editorPanel">
<table class="editor" border="0" cellspacing="0">
<tr>
<td style="width:210px; text-align:center">
[effects-yes]
<div style="background-image: url({ava}); width: {width}px; height: {height}px; background-repeat: no-repeat;" align="center">
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://pdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" id="lecteur" width="{width}" height="{height}"><param name="wmode" value="transparent">
<param name="movie" value="/templates/silver/editprofile/effects/eff_{effect}.swf">
<param name="allowScriptAccess" value="never">
<embed allowscriptaccess="never" type="application/x-shockwave-flash" src="/templates/silver/editprofile/effects/eff_{effect}.swf" wmode="transparent" width="{width}" height="{height}">
</object></div>
[/effects-yes]
[effects]
<div style="background-image: url({ava}); width: {width}px; height: {height}px; background-repeat: no-repeat;"></div>
[/effects]
</td>
<td>
<div class="photo">
<h4>Загрузка фотографии</h4>
<p>Вы можете загрузить сюда только собственную фотографию<br />
расширения JPG, JPEG, GIF или PNG. Загрузка постороннего<br />
изображения приведёт к удалению Вашего аккаунта.</p>
<div class="texta">&nbsp;</div><div class="button_div fl_l" style="margin-right:60%;"><button onClick="Profile.LoadPhoto(); return false" id="saveNewPhoto">Выбрать фото</button></div><div class="mgclr"></div>
<small><br />Файлы размером более 5 MB не загрузятся.<br />
В случае возникновения проблем попробуйте загрузить фотографию меньшего размера.<br />
<br /></small>
<h4>Удаление фотографии</h4>
<p>Вы можете удалить текущую фотографию, но не забудьте загрузить новую, иначе на её месте будет стоять большой вопросительный знак.</p>
<p><div class="texta">&nbsp;</div><div class="button_div fl_l" style="margin-right:60%;"><button onClick="Profile.DelPhoto(); return false" id="delNewPhoto">Удалить фото</button></div><div class="mgclr"></div></p>
[effects-yes]
<br />
<h4>Удаление спецэффекта</h4>
<p>Вы можете удалить текущий спецэффект, если не хотите, чтобы он отражался на вашей фотографии.</p>
<p><div class="texta">&nbsp;</div><div class="button_div fl_l" style="margin-right:60%;"><button onClick="Profile.DelEffect(); return false" id="delNewEffect">Удалить эффект</button></div><div class="mgclr"></div></p>[/effects-yes]
[effects]
<br />
<h4>Установление спецэффекта</h4>
<p>Вы можете установить спецэффект на свое фото, если хотите, чтобы оно выглядело гораздо привлекательнее.</p>
<p><div class="texta">&nbsp;</div><div class="button_div fl_l" style="margin-right:60%;"><button onClick="Page.Go('/editmypage/photo/effect'); return false" id="delNewEffect">Установить эффект</button></div><div class="mgclr"></div></p>
[/effects]
</td>
</tr>
</table>