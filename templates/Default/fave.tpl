<tr class="onefaveu" id="user_{user-id}">
    <td>
        <div class="fave_del_ic" onMouseOver="myhtml.title('{user-id}', 'Удалить из закладок', 'fave_user_')"
             onClick="fave.del_box('{user-id}'); return false" id="fave_user_{user-id}"></div>
        <a href="/u{user-id}" onClick="Page.Go(this.href); return false"><img src="{ava}" alt=""/>
            <div class="fave_tpad"><b>{name}</b></div>
        </a><span class="online">{online}</span></td>
</tr>