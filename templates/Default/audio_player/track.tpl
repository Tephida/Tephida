<div class="staticpl_panel" id="staticpl_panel{jid}">
 [owner]<div class="staticpl_delic" onClick="audio.del('{aid}')" onMouseOver="myhtml.title('{jid}', 'Удалить песню', 'deltack', 5)" id="deltack{jid}"></div>
 <div class="staticpl_editic" onClick="audio.edit('{aid}')" onMouseOver="myhtml.title('{jid}', 'Редактировать песню', 'dtrack', 5)" id="dtrack{jid}"></div>[/owner]
 [not-owner]<div class="staticpl_addmylisy" onClick="audio.addMyList('{aid}', '{jid}')" onMouseOver="myhtml.title('{jid}', 'Добавить в мои аудиозаписи', 'atrack_', 5)" id="atrack_{jid}"></div>
 <div class="staticpl_addmylisok no_display fl_r" id="atrackAddOk{jid}"></div>[/not-owner]
 </div>
<div class="staticpl_audio" onClick="player.play('{jid}')" data="{url}" id="xPlayer{jid}">
 <div class="staticpl_ic" id="xPlayerPlay{jid}"></div><div class="staticpl_ic_pause" id="xPlayerPause{jid}"></div>
 <div class="staticpl_autit" id="xPlayerTitle{jid}"><a class="cursor_pointer" onClick="player.doPast('{jid}')" id="jQpauido"><b id="artis{aid}">{artist}</b></a> &ndash; <span id="name{aid}">{name}</span></div>
 <div class="clear"></div>
</div>