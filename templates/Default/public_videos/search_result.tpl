<div class="onevideo">
 <a href="/video{user-id}_{id}" onClick="videos.show({id}, this.href, '/public/videos{pid}'); return false"><div class="onevideo_img"><img src="{photo}" alt="" /></div></a>
 <div class="onevideo_title"><a href="/video{user-id}_{id}" onClick="videos.show({id}, this.href, '/public/videos{pid}'); return false">{title}</a></div>
 <div class="onevideo_inf2">{descr}</div>
 <div class="onevideo_inf">{comm}</div>
 <div class="onevideo_inf">Добавлено {date}</div>
 [admin-group]<div class="onevideo_inf" id="addVideoForPublic{id}"><a class="cursor_pointer" onClick="addVideoForPublic('{id}', '{pid}'); return false">Добавить</a></div>[/admin-group]
</div>
<div class="clear"></div>