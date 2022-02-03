<div class="miniature_box">
	<div class="miniature_pos">
		<div class="miniature_title fl_l">Настройки беседы</div>
		<a class="cursor_pointer fl_r" onClick="viiBox.clos('viewRoom', 1)">Закрыть</a>
		<div class="clear"></div>
		<div>
			<img src="{avatar}" alt="" style="
				display: block;
				float: left;
				width: 50px;
				height: 50px;
				border-radius: 50%;
				">
			<input type="text" placeholder="Название" value="{name}" class="videos_input" maxlength="100" style="margin: 10px;width: 575px;display: block;float: left;box-sizing: border-box;" {nameAttr}>
		</div>
		[owner]
		<div class="miniature_text clear">
			<div class="button_div fl_r" style="margin: 0 10px;"><button onClick="imRoom.saveName(this, {id})">Сохранить</button></div>
			<div class="button_div fl_r"><button onClick="this.nextSibling.click();">Изменить изображение</button><input type="file" style="display:none;" onchange="imRoom.uploadAvatar(this, {id});"></div>
		</div>
		[/owner]
		<div>{users}</div>
		<div class="clear"></div>
	</div>
</div>