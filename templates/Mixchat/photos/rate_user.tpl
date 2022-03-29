<div class="rate_block" id="{id-rate}">
 <a href="/u{user-id}" onClick="Page.Go(this.href); return false"><img src="{ava}" width="50" height="50" /></a>
 <a href="/u{user-id}" onClick="Page.Go(this.href); return false"><b>{name}</b></a>
 <div class="rate_vbss" id="rate_vbss{id-rate}">{rate}</div>
 <div class="rate_date" id="delrate{id-rate}">{date}, <a class="cursor_pointer" onClick="Photo.delrate({id-rate})">удалить оценку</a></div>
</div>