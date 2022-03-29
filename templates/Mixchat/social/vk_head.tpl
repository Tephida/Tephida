<div style="margin-left:200px;height:110px">
 <div class="vk_panel fl_l vk_panel_active" onMouseOver="myhtml.title('1', '<b>Мои Новости</b>', 'vk_panel_')" onClick="vk.page_go('news')" id="vk_panel_1"><img src="{theme}/images/icons/vk_news.png" /></div>
 <div class="vk_panel fl_l" onMouseOver="myhtml.title('2', '<b>Мои Друзья</b>', 'vk_panel_')" onClick="vk.page_go('friends')" id="vk_panel_2"><img src="{theme}/images/icons/vk_friends.png" /></div>
 <div class="vk_panel fl_l" onMouseOver="myhtml.title('3', '<b>Мои Сообщения</b>', 'vk_panel_')" onClick="vk.page_go('msg')" id="vk_panel_3">
 <div style="background:url('{theme}/images/icons/vk_msg.png');width:64px;height:52px;text-align:center;font-size:21px;font-weight:bold;padding-top:12px" id="vk_new_msg_num">{vk_new_msg_num}</div>
 </div>
 <div class="vk_panel fl_l" onMouseOver="myhtml.title('4', '<b>Выйти из ВК</b>', 'vk_panel_')" onClick="vk.logout()" id="vk_panel_4"><img src="{theme}/images/icons/vk_exit.png" /></div>
</div>
<div class="clear"></div>
<div id="vk_head_bg">
<div class="vk_post_bg">
<textarea id="vk_text" class="wall_inpst wall_fast_opened_texta" placeholder="Что у Вас нового?" style="width:760px;margin-top:10px"></textarea>
<div class="button_div fl_l margin_top_10"><button onClick="vk.send_post('{vk_post_hash}', '{next_page_id}', '{to_id}'); return false" id="vk_sending_post">Отправить</button></div>
<div class="clear"></div>
</div>
<div class="clear"></div>
<div class="margin_top_10"></div><div class="allbar_title" style="margin-bottom:0px">Показаны все новости</div>
<div id="vk_new_post_ok"></div>
</div>