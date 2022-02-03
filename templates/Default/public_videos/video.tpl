<div class="onevideo" id="v{id}">
 <a href="/video{user-id}_{id}" onClick="videos.show({id}, this.href, '/public/videos{pid}'); return false"><div class="onevideo_img"><img src="{photo}" alt="" /></div></a>
 <div class="onevideo_title"><a href="/video{user-id}_{id}" onClick="videos.show({id}, this.href, '/public/videos{pid}'); return false" id="video_title_{id}">{title}</a></div>
 <div class="onevideo_inf2" id="video_descr_{id}">{descr}</div>
 <div class="onevideo_inf">{comm}</div>
 <div class="onevideo_inf">Добавлено {date}</div>
 [admin-group]<div class="onevideo_inf" id="addVideoForPublic{id}"><a class="cursor_pointer" onClick="videoPubEditBox('{id}', '{pid}'); return false">Редактировать</a> &nbsp; | &nbsp; <a class="cursor_pointer" onClick="delVideoOutPublic('{id}', '{pid}'); return false">Удалить</a></div>[/admin-group]
</div>
<div class="clear"></div>