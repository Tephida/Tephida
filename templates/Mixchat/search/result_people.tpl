<div class="friends_onefriend border_radius_5 d-flex justify-content-start" style="width:543px">
    <div>
        <a href="" onClick="messages.new_({user-id}); return false" class="no_display"
           style="position:absolute;margin-top:82px;margin-left:0">
            <img src="/images/badsr.png" style="width:22px;height:18px" alt="{name}">
        </a>
        <a href="u{user-id}" onClick="Page.Go(this.href); return false">
            <div class=""><img src="{ava}" alt=""/></div>
        </a>
    </div>
    <div>
        <a href="u{user-id}" onClick="Page.Go(this.href); return false">
            <b>{name}</b></a>
        <div class="friends_clr"></div>
        {country}{city}
        <div class="friends_clr"></div>
        {age}
        <div class="friends_clr"></div>
        <span class="online">{online}</span>
        <div class="friends_clr"></div>
    </div>
</div>
