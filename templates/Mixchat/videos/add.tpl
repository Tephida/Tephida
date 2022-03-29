<script type="text/javascript">$('#box_but').css('display', 'none');</script>
<div class="videos_pad">
 <div class="videos_text">Ссылка на видеоролик</div>
 <input type="text" class="videos_input" id="video_lnk" onKeyUp="videos.load()" style="margin-bottom:5px" />
 <span id="vi_info">
  <span id="no_serviece">Видеосервис не поддерживается либо ссылка является неправильной<br /></span>
  Поддерживаемые видеосервисы: <b>YouTube</b>, <b>RuTube.Ru</b>, <b>Vimeo.Com</b>, <b>Smotri.Com</b>
 </span>
 <div id="result_load" class="no_display">
  <br />
  <div class="videos_text">Изображение</div>
  <div id="photo" class="videos_res_photos"></div>
  <div class="clear"></div>
  <div class="videos_text">Название</div>
  <input type="text" class="videos_input" id="title" maxlength="65" />
  <div class="videos_text">Описание</div>
  <textarea class="videos_input" id="descr" style="height:70px"></textarea>
  <input type="hidden" id="good_video_lnk" />
  <div class="clear"></div>
  <div class="fl_l" style="padding:3px">Кто может смотреть это видео?</div>
  <div class="sett_privacy" onClick="settings.privacyOpen('privacy')" id="privacy_lnk_privacy">Все пользователи</div>
  <div class="sett_openmenu no_display" id="privacyMenu_privacy" style="margin-top:-1px;margin-left:166px">
   <div id="selected_p_privacy_lnk_privacy" class="sett_selected" onClick="settings.privacyClose('privacy')">Все пользователи</div>
   <div class="sett_hover" onClick="settings.setPrivacy('privacy', 'Все пользователи', '1', 'privacy_lnk_privacy')">Все пользователи</div>
   <div class="sett_hover" onClick="settings.setPrivacy('privacy', 'Только друзья', '2', 'privacy_lnk_privacy')">Только друзья</div>
   <div class="sett_hover" onClick="settings.setPrivacy('privacy', 'Только я', '3', 'privacy_lnk_privacy')">Только я</div>
  </div>
  <input type="hidden" id="privacy" value="1" />
 </div>
</div>
<div class="clear"></div>