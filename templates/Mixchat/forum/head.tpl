<script type="text/javascript">
    var page = 1;
    $(document).ready(function () {
        $(window).scroll(function () {
            if ($(document).height() - $(window).height() <= $(window).scrollTop() + ($(document).height() / 2 - 250)) {
                Forum.Page('{id}');
            }
        });
        Page.langNumric('langForum', '{forum-num}', 'тема', 'темы', 'тем', 'тема', 'В сообществе ещё нет тем.');
    });
</script>
<div class="buttonsprofile">
    <a href="/public{id}" onClick="Page.Go(this.href); return false;">К сообществу</a>
    <div class="buttonsprofileSec"><a href="/forum{id}" onClick="Page.Go(this.href); return false;">Обсуждения</a></div>
    <a href="/forum{id}?act=new" onClick="Page.Go(this.href); return false;" class="{no}">Новая тема</a>
</div>
<div class="clear"></div>
<div class="msg_speedbar margin_bottom_10">{forum-num} <span id="langForum">В сообществе ещё нет тем.</span></div>