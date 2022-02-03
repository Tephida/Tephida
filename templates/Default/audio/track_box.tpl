<div class="audio_onetrack" style="margin-left:0px;margin-top:-10px;margin-bottom:10px">
 <div class="audio_playic cursor_pointer fl_l" onClick="music.newStartPlay('{jid}')" id="icPlay_{jid}"></div>
  <span id="music_{jid}" data="{url}">
   <a href="/?go=search&query={artist}&type=5&n=1" onClick="Page.Go(this.href); return false"><b><span id="artis{aid}">{artist}</span></b></a> &ndash; <span id="name{aid}">{name}</span>
  </span>
 <a href="/" class="fl_r" onClick="wall.attach_insert('audio', '{aid}', '{aid}'); return false">Добавить аудиозапись</a>
 <div id="play_time{jid}" class="color777 fl_r no_display" style="margin-right:5px"></div>
 <div class="clear"></div>
 <div class="player_mini_mbar fl_l no_display" id="ppbarPro{jid}" style="width:585px"></div> 
</div>