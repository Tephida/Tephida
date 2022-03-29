<div class="videos_pad">
 <div class="videos_text">Название</div>
 <input type="text" class="videos_input" id="title" maxlength="65" value="{title}" />
 <div class="videos_text">Описание</div>
 <textarea class="videos_input" id="descr" style="height:70px">{descr}</textarea>
 <input type="hidden" id="good_video_lnk" />
<div class="clear"></div>
<div class="fl_l" style="padding:3px">Кто может смотреть это видео?</div>
<div class="sett_privacy" onClick="settings.privacyOpen('privacy')" id="privacy_lnk_privacy">{privacy-text}</div>
<div class="sett_openmenu no_display" id="privacyMenu_privacy" style="margin-top:-1px;margin-left:166px">
 <div id="selected_p_privacy_lnk_privacy" class="sett_selected" onClick="settings.privacyClose('privacy')">{privacy-text}</div>
 <div class="sett_hover" onClick="settings.setPrivacy('privacy', 'Все пользователи', '1', 'privacy_lnk_privacy')">Все пользователи</div>
 <div class="sett_hover" onClick="settings.setPrivacy('privacy', 'Только друзья', '2', 'privacy_lnk_privacy')">Только друзья</div>
 <div class="sett_hover" onClick="settings.setPrivacy('privacy', 'Только я', '3', 'privacy_lnk_privacy')">Только я</div>
</div>
<input type="hidden" id="privacy" value="{privacy}" />
</div>
<div class="clear"></div>