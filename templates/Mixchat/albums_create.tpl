<script type="text/javascript" src="{theme}/js/albums.create.js"></script>
<div class="videos_pad">
 <div class="videos_text">Название</div>
 <input type="text" class="videos_input" id="name" maxlength="100" style="width:375px;" />
 <div class="videos_text">Описание</div>
 <textarea class="videos_input" id="descr" style="width:375px;height:70px"></textarea>
<div class="clear"></div>
<div class="fl_l" style="padding:3px">Кто может просматривать этот альбом?</div>
<div class="sett_privacy" onClick="settings.privacyOpen('privacy')" id="privacy_lnk_privacy">Все пользователи</div>
<div class="sett_openmenu no_display" id="privacyMenu_privacy" style="margin-top:-1px;margin-left:207px">
 <div id="selected_p_privacy_lnk_privacy" class="sett_selected" onClick="settings.privacyClose('privacy')">Все пользователи</div>
 <div class="sett_hover" onClick="settings.setPrivacy('privacy', 'Все пользователи', '1', 'privacy_lnk_privacy')">Все пользователи</div>
 <div class="sett_hover" onClick="settings.setPrivacy('privacy', 'Только друзья', '2', 'privacy_lnk_privacy')">Только друзья</div>
 <div class="sett_hover" onClick="settings.setPrivacy('privacy', 'Только я', '3', 'privacy_lnk_privacy')">Только я</div>
</div>
<input type="hidden" id="privacy" value="1" />
<div class="clear"></div>
<div class="fl_l" style="padding:3px">Кто может комментировать фотографии?</div>
<div class="sett_privacy" onClick="settings.privacyOpen('privacy_comment')" id="privacy_lnk_privacy_comment">Все пользователи</div>
<div class="sett_openmenu no_display" id="privacyMenu_privacy_comment" style="margin-top:-1px;margin-left:215px">
 <div id="selected_p_privacy_lnk_privacy_comment" class="sett_selected" onClick="settings.privacyClose('privacy_comment')">Все пользователи</div>
 <div class="sett_hover" onClick="settings.setPrivacy('privacy_comment', 'Все пользователи', '1', 'privacy_lnk_privacy_comment')">Все пользователи</div>
 <div class="sett_hover" onClick="settings.setPrivacy('privacy_comment', 'Только друзья', '2', 'privacy_lnk_privacy_comment')">Только друзья</div>
 <div class="sett_hover" onClick="settings.setPrivacy('privacy_comment', 'Только я', '3', 'privacy_lnk_privacy_comment')">Только я</div>
</div>
<input type="hidden" id="privacy_comment" value="{privacy-comment}" />
</div>
<br />
<div class="clear"></div>