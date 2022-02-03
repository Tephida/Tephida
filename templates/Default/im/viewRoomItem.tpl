<div class="" data-id="{id}" style="
	width: 100%;
	display: flex;
	margin-bottom: 10px;
	">
	<div><img src="{avatar}" style="
		display: block;
		"></div>
	<a href="/u{id}" onclick="Page.Go(this.href); return false;" style="
		line-height: 50px;
		margin-left: 10px;
		">{name}</a>
	<div style="
		line-height: 50px;
		margin-left: auto;
		margin-right: 0;
		">
		[admin]<a onclick="imRoom.exclude(this, {room});" style="
			cursor: pointer;
			">Исключить</a>[/admin]
	</div>
</div>