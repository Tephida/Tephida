{*<script type="text/javascript">
    $(document).ready(function () {
        $('#miniature_crop').imgAreaSelect({
            handles: true,
            aspectRatio: '4:4',
            minHeight: 100,
            minWidth: 100,
            x1: 0,
            y1: 0,
            x2: 100,
            y2: 100,
            onSelectEnd: function (img, selection) {
                $('#mi_left').val(selection.x1);
                $('#mi_top').val(selection.y1);
                $('#mi_width').val(selection.width);
                $('#mi_height').val(selection.height);
            },
            onSelectChange: Profile.preview
        });
    });
</script>*}
<script src="/js/cropper.js"></script>
<link rel="stylesheet" href="/css/cropper.css">
{*<script>
    const image = document.getElementById('miniature_crop');
    const cropper = new Cropper(image, {
        aspectRatio: 1,
        viewMode: 2,
        crop(event) {
            // console.log(event.detail.x);
            // console.log(event.detail.y);
            // console.log(event.detail.width);
            // console.log(event.detail.height);

            // console.log(event.detail.rotate);
            // console.log(event.detail.scaleX);
            // console.log(event.detail.scaleY);



            var scaleX = 100 / event.detail.width;
            var scaleY = 100 / event.detail.height;
            // console.log(scaleX);
            // console.log(scaleY);

            $('#miniature_crop_100 img').css({
                width: Math.round(scaleX * event.detail.width),
                height: Math.round(scaleY * event.detail.height),
                marginLeft: -Math.round(scaleX * event.detail.x),
                marginTop: -Math.round(scaleY * event.detail.y)
            });

            // var scaleX50 = 50 / selection.width;
            // var scaleY50 = 50 / selection.height;
            // $('#miniature_crop_100 img').css({
            //     width: Math.round(scaleX * $('#miniature_crop').width()),
            //     height: Math.round(scaleY * $('#miniature_crop').height()),
            //     marginLeft: -Math.round(scaleX * selection.x1),
            //     marginTop: -Math.round(scaleY * selection.y1)
            // });
            // $('#miniature_crop_50 img').css({
            //     width: Math.round(scaleX50 * $('#miniature_crop').width()),
            //     height: Math.round(scaleY50 * $('#miniature_crop').height()),
            //     marginLeft: -Math.round(scaleX50 * selection.x1),
            //     marginTop: -Math.round(scaleY50 * selection.y1)
            // });
        },
    });
</script>*}
<div class="miniature_box">
    <div class="miniature_pos">
        <div class="miniature_text clear">Осталось выбрать квадратную область для маленьких фотографий.<br/>
            Выбранная миниатюра будет использоваться в новостях, личных сообщениях и комментариях.
        </div>
        {*        <div class="miniature_img">*}
        {*            <img src="/uploads/users/{user-id}/{ava}" width="200" id="miniature_crop" class="fl_l" alt="{ava}"/>*}
        {*            <div id="miniature_crop_100" style="width:100px;height:100px;overflow:hidden">*}
        {*                <img src="/uploads/users/{user-id}/{ava}" alt="{ava}"/></div>*}
        {*            <div id="miniature_crop_50" style="width:50px;height:50px;overflow:hidden">*}
        {*                <img src="/uploads/users/{user-id}/{ava}" alt="{ava}"/></div>*}
        {*            <div class="button_div fl_l">*}
        {*                <button onClick="Profile.miniatureSave()" id="miniatureSave">Сохранить изменения</button>*}
        {*            </div>*}
        {*        </div>*}

        <style>
            .container {
                margin: 20px auto;
                max-width: 960px;
            }

            img {
                max-width: 100%;
            }

            .row,
            .preview {
                overflow: hidden;
            }

            .col {
                float: left;
            }

            .col-6 {
                width: 50%;
            }

            .col-3 {
                width: 25%;
            }

            .col-2 {
                width: 16.7%;
            }

            .col-1 {
                width: 8.3%;
            }
        </style>
        <div class="row">
            <div class="container">
                <h1>Customize preview for Cropper</h1>
                <div class="row">
                    <div class="col col-6">
                        <img id="image" src="/uploads/users/{user-id}/{ava}" alt="Picture">
                    </div>

                    {*                    <div class="col col-1">*}
                    {*                        <div class="preview"></div>*}
                    {*                    </div>*}
                </div>
                <div class="row">
                    <div class="col col-3">
                        <div class="preview"></div>
                    </div>
                    <div class="col col-2">
                        <div class="preview"></div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function each(arr, callback) {
                var length = arr.length;
                var i;
                for (i = 0; i < length; i++) {
                    callback.call(arr, arr[i], i, arr);
                }
                return arr;
            }

            // window.addEventListener('DOMContentLoaded', function () {
            var image = document.querySelector('#image');
            var previews = document.querySelectorAll('.preview');
            var previewReady = false;

            var cropper = new Cropper(image, {
                aspectRatio: 4 / 4,
                viewMode: 2,
                ready: function () {
                    var clone = this.cloneNode();

                    clone.className = '';
                    clone.style.cssText = (
                        'display: block;' +
                        'width: 100%;' +
                        'min-width: 0;' +
                        'min-height: 0;' +
                        'max-width: none;' +
                        'max-height: none;'
                    );

                    each(previews, function (elem) {
                        elem.appendChild(clone.cloneNode());
                    });
                    previewReady = true;
                },

                crop: function (event) {
                    if (!previewReady) {
                        return;
                    }

                    var data = event.detail;
                    var cropper = this.cropper;
                    var imageData = cropper.getImageData();
                    var previewAspectRatio = data.width / data.height;

                    each(previews, function (elem) {
                        var previewImage = elem.getElementsByTagName('img').item(0);
                        var previewWidth = elem.offsetWidth;
                        var previewHeight = previewWidth / previewAspectRatio;
                        var imageScaledRatio = data.width / previewWidth;

                        elem.style.height = previewHeight + 'px';
                        previewImage.style.width = imageData.naturalWidth / imageScaledRatio + 'px';
                        previewImage.style.height = imageData.naturalHeight / imageScaledRatio + 'px';
                        previewImage.style.marginLeft = -data.x / imageScaledRatio + 'px';
                        previewImage.style.marginTop = -data.y / imageScaledRatio + 'px';

                        $('#mi_left').val(data.x / imageScaledRatio);
                        $('#mi_top').val(data.y / imageScaledRatio);
                        $('#mi_width').val(data.width);
                        $('#mi_height').val(data.height);
                    });
                },
            });
            // });
        </script>
        <input type="hidden" id="mi_left"/>
        <input type="hidden" id="mi_top"/>
        <input type="hidden" id="mi_width"/>
        <input type="hidden" id="mi_height"/>
        <div class="button_div fl_l">
            <button onClick="Profile.miniatureSave()" id="miniatureSave">Сохранить изменения</button>
        </div>
        <div class="clear"></div>
    </div>
</div>