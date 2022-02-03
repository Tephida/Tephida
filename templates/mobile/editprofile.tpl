<script type="text/javascript" src="{theme}/js/profile_edit.js"></script>
[general]
<div class="tmenuf">
 <div><a href="/editmypage">Основное</a></div>
 <a href="/editmypage/contact">Контакты</a>
 <a href="/editmypage/interests">Интересы</a>
</div>
<div class="clr"></div>
<div class="infobl" id="info_save" style="display:none;font-weight:normal;"></div>
<div class="texta" style="margin-top:0px">Пол:</div>
 <div class="padstylej"><select id="sex" class="inp" onChange="sp.check()">
  <option value="0">- Не выбрано -</option>
  {sex}
 </select></div>
<div class="mgclr"></div>
<div class="no_display" id="sp_block"><div class="texta">Семейное положение:</div>
 <div class="padstylej">
 <div class="[user-m]no_display[/user-m]" id="sp_sel_m"><select id="sp" class="inp" onChange="sp.openfriends()">
  <option value="0">- Не выбрано -</option>
  <option value="1" [instSelect-sp-1]>Не женат</option>
  <option value="2" [instSelect-sp-2]>Есть подруга</option>
  <option value="3" [instSelect-sp-3]>Помовлен</option>
  <option value="4" [instSelect-sp-4]>Женат</option>
  <option value="5" [instSelect-sp-5]>Влюблён</option>
  <option value="6" [instSelect-sp-6]>Всё сложно</option>
  <option value="7" [instSelect-sp-7]>В активном поиске</option>
 </select></div>
 <div class="[user-w]no_display[/user-w]" id="sp_sel_w"><select id="sp_w" class="inp" onChange="sp.openfriends()">
  <option value="0">- Не выбрано -</option>
  <option value="1" [instSelect-sp-1]>Не замужем</option>
  <option value="2" [instSelect-sp-2]>Есть друг</option>
  <option value="3" [instSelect-sp-3]>Помовлена</option>
  <option value="4" [instSelect-sp-4]>Замужем</option>
  <option value="5" [instSelect-sp-5]>Влюблена</option>
  <option value="6" [instSelect-sp-6]>Всё сложно</option>
  <option value="7" [instSelect-sp-7]>В активном поиске</option>
 </select></div>
 </div>
<div class="mgclr"></div></div>
<div class="[sp]no_display[/sp]" id="sp_type">
<div class="texta" id="sp_text">{sp-text}</div>
 <div class="padstylej fl_l"><div style="margin-top:3px;margin-bottom:10px;padding-left:1px;float:left"><a href="/" id="sp_name" onClick="sp.openfriends(); return false">{sp-name}</a></div><img src="{theme}/images/close_a_wall.png" class="sp_del" onClick="sp.del()" /></div>
<div class="mgclr"></div>
<input type="hidden" id="sp_val" />
</div>
<div class="texta">Дата рождения:</div>
 <div class="padstylej"><select id="day" class="inp" style="width:100px">
  <option>- День -</option>
  {user-day}
 </select>
 <select id="month" class="inp" style="width:100px">
  <option>- Месяц -</option>
  {user-month}
 </select>
 <select id="year" class="inp" style="width:100px">
  <option>- Год -</option>{user-year}
 </select></div>
<div class="mgclr"></div>
<div class="texta">Страна:</div>
 <div class="padstylej"><select id="country" class="inp" onChange="Profile.LoadCity(this.value); return false;">
  <option value="0">- Не выбрано -</option>
  {country}
 </select></div>
<div class="mgclr"></div>
<span id="city"><div class="texta">Город:</div>
 <div class="padstylej"><select id="select_city" class="inp">
  <option value="0">- Не выбрано -</option>
  {city}
 </select></div>
<div class="mgclr"></div></span>
<div class="texta" style="margin-top:-10px">&nbsp;</div><button id="saveform" class="button">Сохранить</button>
[/general]

[contact]
<div class="tmenuf">
 <a href="/editmypage">Основное</a>
 <div><a href="/editmypage/contact">Контакты</a></div>
 <a href="/editmypage/interests">Интересы</a>
</div>
<div class="clr"></div>
<div class="infobl" id="info_save" style="display:none;font-weight:normal;"></div>
<div class="clear"></div>
<div class="texta" style="margin-top:0px">Мобильный телефон:</div><input type="text" id="phone" class="inp" maxlength="50" value="{phone}"  /><span id="validPhone"></span><div class="mgclr"></div>
<div class="texta">В контакте:</div><input type="text" id="vk" class="inp" maxlength="100" value="{vk}" /><span id="validVk"></span><div class="mgclr"></div>
<div class="texta">Одноклассники:</div><input type="text" id="od" class="inp" maxlength="100" value="{od}" /><span id="validOd"></span><div class="mgclr"></div>
<div class="texta">FaceBook:</div><input type="text" id="fb" class="inp" maxlength="100" value="{fb}" /><span id="validFb"></span><div class="mgclr"></div>
<div class="texta">Skype:</div><input type="text" id="skype" class="inp" maxlength="100" value="{skype}" /><span id="validSkype"></span><div class="mgclr"></div>
<div class="texta">ICQ:</div><input type="text" id="icq" class="inp" maxlength="9" value="{icq}" /><span id="validIcq"></span><div class="mgclr"></div>
<div class="texta">Личный сайт:</div><input type="text" id="site" class="inp" maxlength="100" value="{site}" /><span id="validSite"></span><div class="mgclr"></div>
<div class="texta" style="margin-top:-10px">&nbsp;</div><div class="button_div fl_l"><button name="save" id="saveform_contact" class="button">Сохранить</button></div><div class="mgclr"></div>
[/contact]
[interests]
<div class="tmenuf">
 <a href="/editmypage">Основное</a>
 <a href="/editmypage/contact">Контакты</a>
 <div><a href="/editmypage/interests">Интересы</a></div>
</div>
<div class="clr"></div>
<div class="infobl" id="info_save" style="display:none;font-weight:normal;"></div>
<div class="clear"></div>
<div class="texta" style="margin-top:0px">Деятельность:</div><textarea id="activity" class="inp" style="overflow:hidden;">{activity}</textarea><div class="mgclr"></div>
<div class="texta">Интересы:</div><textarea id="interests" class="inp" style="">{interests}</textarea><div class="mgclr"></div>
<div class="texta">Любимая музыка:</div><textarea id="music" class="inp" style="">{music}</textarea><div class="mgclr"></div>
<div class="texta">Любимые фильмы:</div><textarea id="kino" class="inp" style="">{kino}</textarea><div class="mgclr"></div>
<div class="texta">Любимые книги:</div><textarea id="books" class="inp" style="">{books}</textarea><div class="mgclr"></div>
<div class="texta">Любимые игры:</div><textarea id="games" class="inp" style="">{games}</textarea><div class="mgclr"></div>
<div class="texta">Любимые цитаты:</div><textarea id="quote" class="inp" style="">{quote}</textarea><div class="mgclr"></div>
<div class="texta">О себе:</div><textarea id="myinfo" class="inp" style="">{myinfo}</textarea><div class="mgclr"></div>
<div class="texta" style="margin-top:-10px">&nbsp;</div><div class="button_div fl_l"><button name="save" id="saveform_interests" class="button">Сохранить</button></div><div class="mgclr"></div>
[/interests]