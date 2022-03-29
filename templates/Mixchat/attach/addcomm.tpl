<script type="text/javascript">var page = 1;</script>
<div class="photo_leftcol">
<div class="color777" style="margin-bottom:10px">Добавлено {date}</div>
[comm]<div class="cursor_pointer" onClick="attach.page('{purl}'); return false" id="attach_comm_msg_lnk"><div class="public_wall_all_comm" id="load_attach_comm_msg_lnk" style="margin-left:0px">Показать предыдущие комментарии</div></div>[/comm]
<span id="attachcommPrev"></span>
<span id="pcomments">{comments}</span>
<div class="photo_com_title">Ваш комментарий</div>
<textarea id="textcom{purl-js}" class="inpst" style="width:520px;height:70px;margin-bottom:10px;"></textarea>
<div class="clear"></div>
<div class="button_div fl_l"><button id="add_comm{purl-js}" onClick="attach.addcomm('{purl}', '{purl-js}'); return false">Отправить</button></div>
<div class="clear"></div>
</div>
<div class="photo_rightcol" style="margin-top:0px">
 Отправитель:<br />
 <div><a href="/u{uid}" onClick="Page.Go(this.href); return false">{author}</a></div><span style="color:#888">{author-info}</span><br />
</div>
<div class="clear"></div>