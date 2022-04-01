<script type="text/javascript">
    $(document).ready(function () {
        $('.blog_left_tab').css('min-height', ($('.blog_left_tab').height() + 10) + 'px').css('height', ($('.blog_left').height() + 10) + 'px');
    });
</script>
<style>.speedbar{background:#fff;color:#5081b1}</style>
<div class="blogthr"></div>
<div class="blog_left fl_l">
    <div class="notes_ava"><img src="/images/support2.png" alt=""/></div>
    <div class="one_note border_radius_5" style="width:550px">
        <span><a href="/blog?id={id}" onClick="Page.Go(this.href); return false">{title}</a></span><br/>
        <div>{date}</div>
    </div>
    <div class="clear"></div>
    <div class="note_text clear page_bg border_radius_5 margin_top_10" style="width:610px">{story}</div>
</div>
<div class="blog_left_tab border_radius_5 fl_r">
    {last-news}
    [group=1]<br/>
    <a href="?act=add" onClick="Page.Go(this.href); return false">Добавить новость</a>
    <a href="" onClick="blog.del('{id}'); return false">Удалить новость</a>
    <a href="?act=edit&id={id}" onClick="Page.Go(this.href); return false">Редактировать новость</a>
    [/group]
</div>