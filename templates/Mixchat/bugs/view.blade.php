<div id="box_view" class="box_pos" cb-datas="" style="display: block">
    <div class="box_bg" style="width: 500px; margin-top: 90px;">
        <div class="box_title">
            <span id="btitle" dir="auto"></span>
            Просмотр бага
            <div class="box_close" onclick="viiBox.clos('view', 1); return false;"><span aria-hidden="true">×</span></div>
        </div>
        <div class="box_conetnt">
            @foreach($bugs as $row)
                <div class="bug_head">
                    <a href="/u{uid}" onclick="Page.Go(this.href); return false;"><img src="{ava}"></a>
                    <div class="cont">
                        <div class="title">{title}</div>
                        <div class="date">обновлено {date}</div>
                        <div style="margin-left: 355px;margin-top: -39px;">{delete}</div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="bug_content">
                    <div class="form">{{ $row['text'] }}
                        <div style="margin-top:10px;">
                            <div style="margin-top:5px;width: 472px;">
                                <div class="wall_photo" style="width:250px;max-height: 400px;" onclick="Photos.openAll(this, 18922, 0, 0, 0)">
                                    {{ $row['photo'] }}
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <span class="state">Статус: {{ $row['status'] }}</span>&nbsp;&nbsp;
                    <span class="color777">{sex} {name}</span>
                </div>
            @endforeach
            <div class="bug_content">
                <div class="bug_comment">
                    <div class="adm"><a href="/u{admin_id}">{admin}</a> изменил статус на <b>{status}</b></div>
                    <div class="comm">{admin_text}</div>
                </div>
            </div>
        </div>
    </div>
</div>