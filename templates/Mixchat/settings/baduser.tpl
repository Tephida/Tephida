<div class="sett_oneblack border_radius_5 page_bg" id="u{user-id}">
    <a href="/u{user-id}" onClick="Page.Go(this.href); return false">
        <img src="{ava}" alt="" align="left"/>
    </a>
    <a href="/u{user-id}" onClick="Page.Go(this.href); return false"><b>{name}</b></a>
    <div style="margin-top:7px">
        <a href="/u{user-id}" onClick="settings.delblacklist('{user-id}'); return false"
           id="del_{user-id}">Удалить из списка</a>
    </div>
    <div class="clear"></div>
</div>