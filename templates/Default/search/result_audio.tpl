<div class="audio_onetrack" style="border-bottom:0px;border-top:1px dashed #dde5ed;margin-left:0px">
 <div class="audio_playic cursor_pointer fl_l" onClick="music.newStartPlay('{jid}')" id="icPlay_{jid}"></div>
  <span id="music_{jid}" data="{url}">
   <a href="/?go=search&query={artist}&type=5&n=1" onClick="Page.Go(this.href); return false"><b><span id="artis{aid}">{artist}</span></b></a> &ndash; <span id="name{aid}">{name}</span> <a href="/u{author-id}" onClick="Page.Go(this.href); return false"><small>({author-n}. {author-f})</small></a>
  </span>
 <div class="audio_addmylistic cursor_pointer fl_r" onClick="audio.addMyList('{aid}')" onMouseOver="myhtml.title('{aid}', 'Добавить в мои аудиозаписи', 'atrack_', 3)" id="atrack_{aid}"></div><div class="audio_addmylisticOk no_display fl_r" id="atrackAddOk{aid}"></div>
 <div id="play_time{jid}" class="color777 fl_r no_display" style="margin-top:2px;margin-right:5px"></div>
 <div class="clear"></div>
 <div class="player_mini_mbar fl_l no_display" id="ppbarPro{jid}"></div> 
</div>