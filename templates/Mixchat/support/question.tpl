<div class="support_questtitle border_radius_5 d-flex justify-content-between">
    <div style="">
        <div style="padding: 15px;">
            <a href="/support?act=show&qid={qid}" onClick="Page.Go(this.href); return false"><b>{title}</b></a><br/>
            {status}
        </div>
    </div>
    <div class="border_radius_5" style="background-color: #d44b28;padding: 10px;">
        <a href="/support?act=show&qid={qid}" onClick="Page.Go(this.href); return false" class="d-flex">
            <div>
                <img src="{ava}" alt="" width="35"/>
            </div>
            <div style="padding: 5px;font-size:11px;color: #fff;">
                <div>
                    {name}
                </div>
                <div>
                    {answer} {date}
                </div>
            </div>
        </a>
    </div>
</div>
