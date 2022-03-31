<script type="text/javascript">
    $(function () {
        window.friends_loading = false;
        window.friends_page = 1;
        var friends_handler = function () {
            if (!friends_loading && $(document).height() - ($(window).scrollTop() + $(window).height()) < 500) {
                friends_loading = true;
                $('.friends_loading').show();
                var act = decodeURI((RegExp('(online|requests)').exec(location.href) || [, null])[1]);
                var id = decodeURI((RegExp('([0-9]+)').exec(location.href) || [, null])[1]);
                if (act == 'online') {
                    act = '&act=online';
                } else if (act == 'requests') {
                    act = '&act=requests';
                } else {
                    act = '';
                }
                if (id == 'null') {
                    id = my_id;
                }
                $.post('/index.php?go=friends&user_id=' + id + act + '&page=' + ++friends_page, {"ajax": "yes"}, function (d) {
                    if (d == 'last_page') {
                        $(window).unbind('scroll', friends_handler);
                        return;
                    }
                    $('.friends_onefriend:last').after(d);
                    friends_loading = false;
                    $('.friends_loading').hide();
                });
            }
        };
        $(window).scroll(friends_handler);
    });
</script>
[all-friends]
<div class="buttonsprofile">
    <div class="activetab"><a href="/friends/{user-id}" onClick="Page.Go(this.href); return false;">
            <div>Все друзья</div>
        </a></div>
    <a href="/friends/online/{user-id}" onClick="Page.Go(this.href); return false;">Друзья на сайте</a>
    [owner]<a href="/friends/requests" onClick="Page.Go(this.href); return false;">Заявки в друзья {demands}</a>[/owner]
    [not-owner]<a href="/friends/common/{user-id}" onClick="Page.Go(this.href); return false;">Общие друзья</a>
    <a href="/u{user-id}" onClick="Page.Go(this.href); return false;">К странице {name}</a>[/not-owner]

</div>
<div class="clear"></div>
[/all-friends]
[request-friends]
<div class="buttonsprofile">
    <a href="/friends/{user-id}" onClick="Page.Go(this.href); return false;">Все друзья</a>
    <a href="/friends/online/{user-id}" onClick="Page.Go(this.href); return false;">Друзья на сайте</a>
    <div class="activetab"><a href="/friends/requests" onClick="Page.Go(this.href); return false;">
            <div>Заявки в друзья {demands}</div>
        </a></div>
</div>
<div class="clear"></div>
[/request-friends]
[online-friends]
<div class="buttonsprofile">
    <a href="/friends/{user-id}" onClick="Page.Go(this.href); return false;">Все друзья</a>
    <div class="activetab"><a href="/friends/online/{user-id}" onClick="Page.Go(this.href); return false;">
            <div>Друзья на сайте</div>
        </a></div>
    [owner]<a href="/friends/requests" onClick="Page.Go(this.href); return false;">Заявки в друзья {demands}</a>[/owner]
    [not-owner]<a href="/friends/common/{user-id}" onClick="Page.Go(this.href); return false;">Общие друзья</a>
    <a href="/u{user-id}" onClick="Page.Go(this.href); return false;">К странице {name}</a>[/not-owner]

</div>
<div class="clear"></div>
[/online-friends]
