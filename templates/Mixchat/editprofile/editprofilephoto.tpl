<script type="text/javascript" src="{theme}/js/profile_edit.js"></script>
<div class="buttonsprofile">
 <a href="/editmypage" onClick="Page.Go(this.href); return false;">��������</a>
 <a href="/editmypage/contact" onClick="Page.Go(this.href); return false;">��������</a>
 <a href="/editmypage/interests" onClick="Page.Go(this.href); return false;">��������</a>
 <a href="/editmypage/all" onClick="Page.Go(this.href); return false;">������</a>
 <div class="activetab"><a href="/editmypage/photo" onClick="Page.Go(this.href); return false;"><div>����</div></a></div>
</div>
<div class="clear"></div>
<div class="err_red no_display pass_errors" id="delete_ok" style="font-weight:normal;">���������� ������� ������.</div>
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
<h4>�������� ����������</h4>
<p>�� ������ ��������� ���� ������ ����������� ����������<br />
���������� JPG, JPEG, GIF ��� PNG. �������� ������������<br />
����������� ������� � �������� ������ ��������.</p>
<div class="texta">&nbsp;</div><div class="button_div fl_l" style="margin-right:60%;"><button onClick="Profile.LoadPhoto(); return false" id="saveNewPhoto">������� ����</button></div><div class="mgclr"></div>
<small><br />����� �������� ����� 5 MB �� ����������.<br />
� ������ ������������� ������� ���������� ��������� ���������� �������� �������.<br />
<br /></small>
<h4>�������� ����������</h4>
<p>�� ������ ������� ������� ����������, �� �� �������� ��������� �����, ����� �� � ����� ����� ������ ������� �������������� ����.</p>
<p><div class="texta">&nbsp;</div><div class="button_div fl_l" style="margin-right:60%;"><button onClick="Profile.DelPhoto(); return false" id="delNewPhoto">������� ����</button></div><div class="mgclr"></div></p>
[effects-yes]
<br />
<h4>�������� �����������</h4>
<p>�� ������ ������� ������� ����������, ���� �� ������, ����� �� ��������� �� ����� ����������.</p>
<p><div class="texta">&nbsp;</div><div class="button_div fl_l" style="margin-right:60%;"><button onClick="Profile.DelEffect(); return false" id="delNewEffect">������� ������</button></div><div class="mgclr"></div></p>[/effects-yes]
[effects]
<br />
<h4>������������ �����������</h4>
<p>�� ������ ���������� ���������� �� ���� ����, ���� ������, ����� ��� ��������� ������� ���������������.</p>
<p><div class="texta">&nbsp;</div><div class="button_div fl_l" style="margin-right:60%;"><button onClick="Page.Go('/editmypage/photo/effect'); return false" id="delNewEffect">���������� ������</button></div><div class="mgclr"></div></p>
[/effects]
</td>
</tr>
</table>