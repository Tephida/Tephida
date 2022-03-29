<script type="text/javascript" src="/js/upload.photo.js"></script>
<script type="text/javascript">
    var loading_photo_pins = false;
    var loaded_pins_name = null;
    $(document).ready(function () {
        aj1 = new AjaxUpload('upload', {
            action: '/bugs/load_img/',
            name: 'uploadfile',
            data: {
                add_act: 'upload'
            },
            accept: 'image/*',
            onSubmit: function (file, ext) {
                if (!(ext && /^(jpg|png|jpeg|gif|jpe)$/.test(ext))) {
                    Box.Info('err', 'Ошибка', 'Неверный формат файла');
                    return false;
                }
                $('#upload').hide();
                $('#prog_poster').show();
            },
            onComplete: function (file, row) {
                var exp = row.split('|');
                if (exp[0] == 'size') {
                    Box.Info('err', 'Ошибка', 'Файл превышает 5 МБ');
                } else {
                    $('#r_poster').attr('src', '/uploads/bugs/' + exp[0] + '/' + exp[1]).show();
                }
                $('#upload').show();
                $('#prog_poster, #size_small, #upload_butt').hide();
                loading_photo_pins = true;
                loaded_pins_name = exp[1];
            }
        });
    });
</script>
<div id="box_bugs" class="miniature_box" style="display: block">
    <div class="miniature_pos" style="width: 500px; margin-top: 30px;">
        <div class="modal-header">
            <h5 class="modal-title">Сообщение о баге</h5>
            <button type="button" onclick="viiBox.clos('bugs', 1); return false;" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close">X
            </button>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="title" class="form-label">Заголовок</label>
                <input type="text" class="form-control" id="title" aria-describedby="titleHelp">
                {{--                <div id="titleHelp" class="form-text">We'll never share your email with anyone else.</div>--}}
            </div>
            <div class="mb-3">
                <label for="text" class="form-label">Описание</label>
                <textarea class="form-control" id="text" rows="3"></textarea>
            </div>
            <div class="mb-3 d-flex justify-content-between">
                <div class="input-group mb-3 d-none">
                    <label class="input-group-text" for="upload">Upload</label>
                    <input type="file" class="form-control" id="upload">
                    {{--                    <div id="emailHelp" class="form-text">Файл не должен превышать 5 Mб.</div>--}}

                </div>
                <div class="input-group mb-3 d-none">
                    <div id="prog_poster"
                         style="display: none;background:url('/images/progress_grad.gif');width:94px;height:18px;border:1px solid #006699; float:left"></div>
                    <div class="clear"></div>
                    {{--                    <div id="size_small" style="margin-left:-10px"><small> Файл не должен превышать 5 Mб.</small></div>--}}
                    {{--                <img src="/uploads/bugs/" id="r_poster" style="display:none;" width="100" height="100"  alt=""/>--}}
                    {{--                <div class="mgclr"></div>--}}
                </div>
                <div class="input-group mb-3 ">
                    {{--                    <div class="button_div fl_l">--}}
                    {{--                        <button onclick="bugs.create();" id="saveShortLink">Отправить</button>--}}
                    {{--                    </div>--}}
                    <button type="button" class="btn btn-primary" onclick="bugs.create();" id="saveShortLink">Отправить</button>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="viiBox.clos('bugs', 2); return false;">Закрыть</button>
            <button type="button" class="btn btn-primary" onclick="bugs.create();" id="saveShortLink">Отправить</button>
        </div>
    </div>
</div>