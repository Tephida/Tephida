<div class="miniature_box apps_box_pos_marg">
 <div class="miniature_pos apps_box_pos">
  <div class="miniature_title fl_l apps_box_text">Запуск игры</div><a class="cursor_pointer fl_r" onClick="viiBox.clos('ap', 1, '/apps')">Закрыть</a>
  <div class="clear"></div>
  [yes-game]<div class="apps_view_pos">
   <img src="{poster}" width="75" height="75" />
   <a>{title}</a>
   <div>{descr}</div>
  </div>
  <div class="clear"></div>
  <div class="apps_view_block">
   <div class="apps_view_block_txt fl_l">{traf}</div>
   <div class="button_div fl_r"><button onClick="Page.Go('/app{app-id}')">Запустить игру</button></div>
   <div class="clear"></div>
  </div>
  [poster]<div class="appsfh" onClick="Page.Go('/app{app-id}')">
  <div class="apps_i_run_box">Запустить игру</div>
  <div class="apps_main_poster cursor_pointer">
   <div class="apps_inimgs">
    <img src="{poster-1}" width="607" height="376" />
    <img src="{poster-2}" width="607" height="376" />
    <img src="{poster-3}" width="607" height="376" />
    <img src="{poster-4}" width="607" height="376" />
   </div>
  </div>
  </div>
  <div align="center" class="apps_mini_img">
   {m-poster-1}
   {m-poster-2}
   {m-poster-3}
   {m-poster-4}
  </div>[/poster][/yes-game]
  [no-game]<div class="info_center"><br /><br />Игра не найдена.<br /><br /><br /></div>[/no-game]
  <div class="clear"></div>
 </div>
 <div class="clear" style="height:20px"></div>
</div>