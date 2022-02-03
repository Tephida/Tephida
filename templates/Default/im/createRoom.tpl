<div class="miniature_box">
	<div class="miniature_pos">
		<div class="miniature_title fl_l">Создание беседы</div>
		<a class="cursor_pointer fl_r" onClick="viiBox.clos('createRoom', 1)">Закрыть</a>
		<div class="clear"></div>
		<div><input type="text" placeholder="Название" class="videos_input" maxlength="100" style="width: 100%;box-sizing: border-box;"></div>
		<div class="miniature_text clear">
			Выберите друзей которых хотите пригласить в беседу.
			<div class="button_div fl_r" style="margin-top:-2px;margin-left:10px;line-height:15px"><button onClick="imRoom.create(this)">Создать беседу</button></div>
		</div>
		<div style="margin-right:-20px">{friends}</div>
		<div class="clear"></div>
	</div>
</div>