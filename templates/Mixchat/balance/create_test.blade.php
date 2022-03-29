<div class="miniature_box">
    <div class="miniature_pos">
        <div class="margin_top_10"></div>
<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Оплата test</h5>
                <div class="msg_speedbar clear">Оплата test</div>
                <div class="page_bg border_radius_5 margin_top_10">
                    {{--    <h2>Оплата через <a href="https://freekassa.ru">freekassa.ru</a></h2>--}}
                    <div id="error"></div>
                    <form method=GET action="/pay/test/create/">
                        {{--                <input type="hidden" name="m" value="{{ $fk_merchant_id }}">--}}
                        <input type="hidden" name="kassa" value="test">
                        <input type="hidden" name="product" value="{{ $product }}">
                        <div class="mgclr"></div>
                        <input type="submit" id="submit" value="Оплатить">
                        <div class="button_div fl_l">
                            {{--                    <button onclick="pay.saveNewPwd(); return false" id="submit" disabled>Оплатить</button>--}}
                        </div>
                        <div class="mgclr"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Frekassa</h5>
                <div class="page_bg border_radius_5 margin_top_10">
                    {{--    <h2>Оплата через <a href="https://freekassa.ru">freekassa.ru</a></h2>--}}
                    <div id="error"></div>
                    <form method=GET action="/pay/fkw/create/">
                        {{--                <input type="hidden" name="m" value="{{ $fk_merchant_id }}">--}}
                        <input type="hidden" name="kassa" value="test">
                        <input type="hidden" name="product" value="{{ $product }}">
                        <div class="mgclr"></div>
                        <input type="submit" id="submit" value="Оплатить 100р">
                        <div class="button_div fl_l">
                            {{--                    <button onclick="pay.saveNewPwd(); return false" id="submit" disabled>Оплатить</button>--}}
                        </div>
                        <div class="mgclr"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
    </div>
</div>