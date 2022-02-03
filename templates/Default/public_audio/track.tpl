<div class="audio_onetrack">
 <div class="audio_playic cursor_pointer fl_l" onClick="music.newStartPlay('{jid}')" id="icPlay_{jid}"></div>
  <span id="music_{jid}" data="{url}">
   <a href="/?go=search&query={artist}&type=5&n=1" onClick="Page.Go(this.href); return false"><b><span id="artis{aid}">{artist}</span></b></a> &ndash; <span id="name{aid}">{name}</span>
  </span>
 [all-users]<div class="audio_addmylistic cursor_pointer fl_r" onClick="audio.addMyList('{aid}')" onMouseOver="myhtml.title('{aid}', 'Добавить в мои аудиозаписи', 'atrack_', 3)" id="atrack_{aid}"></div><div class="audio_addmylisticOk no_display fl_r" id="atrackAddOk{aid}"></div>[/all-users]
 [admin-group]<div class="audio_deletic cursor_pointer fl_r" onClick="PublicAudioDelete('{aid}')" onMouseOver="myhtml.title('{aid}', 'Удалить', 'dtrack_', 4)" id="dtrack_{aid}"></div>
 <div class="audio_edittic cursor_pointer fl_r" onClick="audio.edit('{aid}', '{pid}')" onMouseOver="myhtml.title('{aid}', 'Редактировать', 'etrack_', 4)" id="etrack_{aid}"></div>[/admin-group]
 <div id="play_time{jid}" class="color777 fl_r no_display" style="margin-top:2px;margin-right:5px"></div>
 <div class="clear"></div>
 <div class="player_mini_mbar fl_l no_display" id="ppbarPro{jid}"></div> 
</div>