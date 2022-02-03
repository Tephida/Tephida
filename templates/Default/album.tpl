<div id="album_{aid}" class="albums" [owner]style="cursor:move"[/owner]>
<div class="hralbum"></div>
<a href="/albums/view/{aid}" onClick="Page.Go(this.href); return false"><div class="albums_cover"><span id="cover_{aid}"><img src="{cover}" alt="" /></span></div></a>
<div class="albums_name"><a href="/albums/view/{aid}" onClick="Page.Go(this.href); return false" id="albums_name_{aid}">{name}</a></div>
<div class="albums_photo_num">{photo-num}, {comm-num}</div>
<div class="albums_photo_num">Обновлён {date}</div>
<div class="album_desc"><span id="descr_{aid}">{descr}</span></div>
[owner]<div class="infowalltext albums_infowalltext"><a href="/" onClick="Albums.EditBox({aid}); return false">Редактировать</a>&nbsp; | &nbsp;<a href="/" onClick="Albums.EditCover({aid}); return false">Изменить обложку</a>&nbsp; | &nbsp;<a href="/albums/add/{aid}" onClick="Page.Go(this.href); return false">Добавить фотографии</a>&nbsp; | &nbsp;<a href="/" onClick="Albums.Delete({aid}, '{hash}'); return false">Удалить</a></div>[/owner]
<div class="clear"></div>
</div>