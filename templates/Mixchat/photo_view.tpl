<div id="photo_view_{id}" class="photo_view" onClick="Photo.setEvent(event, '{close-link}')">
    <div class="photo_close" onClick="Photo.Close('{close-link}'); return false;"></div>
    <div class="photo_bg">
        <div class="photo_com_title" style="padding-top:0px;">[all]Фотография {jid} из {photo-num}[/all][wall]Просмотр
            фотографии[/wall]
            <div><a href="/" onClick="Photo.Close('{close-link}'); return false;">Закрыть</a></div>
        </div>
        [all]<a href="/photo{uid}_{prev-id}{section}" onClick="Photo.Show(this.href); return false">
            <div class="photo_prev_but"></div>
        </a>
        <a href="/photo{uid}_{next-id}{section}" onClick="Photo.Show(this.href); return false">
            <div class="photo_next_but"></div>
        </a>[/all]
        [mark-block]
        <div class="mark_userid_bg" id="mark_userid_bg{id}">
            <div class="fl_l"><a href="/u{mark-user-id}"
                                 onClick="Page.Go(this.href); return false">{mark-user-name}</a> {mark-gram-text} Вас на
                этой фотографии.</a></div>
            <div class="button_div_gray margin_left fl_r" style="margin-top:-5px">
                <button onClick="{mark-del-link}; return false">Удалить отметку</button>
            </div>
            <div class="button_div fl_r" style="margin-top:-5px">
                <button onClick="Distinguish.OkUser({id}); return false">Потвердить</button>
            </div>
            <div class="clear"></div>
        </div>
        [/mark-block]
        <div id="distinguishSettings{id}" class="distinguishSettings" style="display:none"
             onMouseOver="Distinguish.HideTag({id})">
            <div style="position:absolute;border-right:3px solid #dbe6f0;cursor:default"
                 id="distinguishSettingsBorder_left{id}"></div>
            <div style="position:absolute;border-bottom:3px solid #dbe6f0;cursor:default"
                 id="distinguishSettingsBorder_top{id}"></div>
            <div style="position:absolute;border-left:3px solid #dbe6f0;cursor:default"
                 id="distinguishSettingsBorder_right{id}"></div>
            <div style="position:absolute;border-top:3px solid #dbe6f0;cursor:default"
                 id="distinguishSettingsBorder_bottom{id}"></div>
            <div style="position:absolute;cursor:default" class="imgareaselect-outer"
                 id="distinguishSettings_left{id}"></div>
            <div style="position:absolute;cursor:default" class="imgareaselect-outer"
                 id="distinguishSettings_top{id}"></div>
            <div style="position:absolute;cursor:default" class="imgareaselect-outer"
                 id="distinguishSettings_right{id}"></div>
            <div style="position:absolute;cursor:default" class="imgareaselect-outer"
                 id="distinguishSettings_bottom{id}"></div>
        </div>
        <a href="/photo{uid}_{next-id}{section}"
           onClick="[all]Photo.Show(this.href)[/all][wall]Photo.Close('{close-link}')[/wall]; return false"
           id="photo_href">
            <div class="photo_img_box">
                <img id="ladybug_ant{id}" style="width: 100%;" class="ladybug_ant" src="{photo}" alt=""/>
                <div id="frameedito{id}"></div>
        </a></div>
    <div class="clear"></div>
    <div id="save_crop_text{id}" class="save_crop_text no_display" style="padding:20px;padding-left:0px">
        Укажите область, которая будет сохранена как фотография Вашей страницы.
        <div class="button_div_gray margin_left fl_r" style="margin-top:-5px">
            <button onClick="crop.close({id}); return false">Отмена</button>
        </div>
        <div class="button_div fl_r" style="margin-top:-5px">
            <button onClick="crop.save({id}, {uid}); return false">Готово</button>
        </div>
    </div>
    <!--likes-->
    <div class="photos_likes_but cursor_pointer" id="photos_likes_but{id}" onClick="Photo.{likes-func}({id})"
         style="width:{likes-width}">
        <span><div class="fl_l">Мне нравится</div></span>
        <img src="/images/ph_like_hand.png" align="left"/>
        <b>
            <div id="photos_like_{id}" style="color:{likes-color}">{likes-num}</div>
        </b>
    </div>
    <!--/likes-->
    <div id="pinfo_{id}" class="pinfo">
        <div class="photo_leftcol">
            <div class="ratingbg">
{*                [not-owner]*}
{*                <div class="ratpos {rate-check}" id="ratpos{id}">*}
{*                    <div onClick="Photo.addrating(1, {id}, this.style)" class="rating"*}
{*                         style="background:url('/images/rating3.png')">1*}
{*                    </div>*}
{*                    <div onClick="Photo.addrating(2, {id})" class="rating rating2">2</div>*}
{*                    <div onClick="Photo.addrating(3, {id})" class="rating rating2">3</div>*}
{*                    <div onClick="Photo.addrating(4, {id})" class="rating rating2">4</div>*}
{*                    <div onClick="Photo.addrating(5, {id})" class="rating rating2">5</div>*}
{*                    <div onClick="Photo.addrating(6, {id})" class="rating rating2">6</div>*}
{*                    <div onClick="Photo.addrating(7, {id})" class="rating rating2">7</div>*}
{*                    <div onClick="Photo.addrating(8, {id})" class="rating"*}
                {*                         style="background:url('/images/rating2.png')">7+*}
{*                    </div>*}
{*                </div>*}
{*                <img src="/images/ajax-loader.gif" id="rateload{id}" class="no_display"*}
{*                     style="margin-left:255px;margin-top:12px"/>*}
{*                <div class="ratingyes {rate-check-2}" id="ratingyes{id}">*}
{*                    <div class="ratingyestext fl_l">Ваша оценка</div>*}
{*                    <div id="addratingyes{id}">{ok-rate}</div>*}
{*                </div>*}
{*                [/not-owner]*}
{*                [owner]*}
{*                <div class="rateforowne">Всего оценок: &nbsp;<a class="cursor_pointer"*}
{*                                                                onClick="Photo.allrating({id})">{rate-all}</a>&nbsp;&nbsp;&nbsp;*}
{*                    Семерок с плюсом: &nbsp;<a class="cursor_pointer" onClick="Photo.allrating({id})">{rate-max}</a>&nbsp;&nbsp;&nbsp;*}
{*                    Рейтинг: &nbsp;<a class="cursor_pointer" onClick="Photo.allrating({id})">{rate}</a></div>*}
{*                [/owner]*}
            </div>
            <input type="hidden" id="i_left{id}"/>
            <input type="hidden" id="i_top{id}"/>
            <input type="hidden" id="i_width{id}"/>
            <input type="hidden" id="i_height{id}"/>
            <div class="peoples_on_this_photos" id="peoples_on_this_photos{id}">{mark-peoples}</div>
            <div class="photo_descr clear" id="photo_descr_{id}">{descr}</div>
            <div class="photo_info">Добавлена {date}</div>
            <br/>
            [all-comm]<a href="/" onClick="comments.all({id}, {num}); return false" id="all_href_lnk_comm_{id}">
                <div class="photo_all_comm_bg" id="all_lnk_comm_{id}">Показать предыдущие {comm_num}</div>
            </a><span id="all_comments_{id}"></span>[/all-comm]
            <span id="comments_{id}">{comments}</span>
            [add-comm]
            <div class="photo_com_title">Ваш комментарий</div>
            <textarea id="textcom_{id}" class="inpst" style="width:520px;height:70px;margin-bottom:10px;"></textarea>
            <div class="button_div fl_l">
                <button id="add_comm" onClick="comments.add({id}); return false">Отправить</button>
            </div>
            [/add-comm]
        </div>
        <div class="photo_rightcol">
            Из альбома:<br/>
            <a href="/albums/view/{aid}" onClick="Page.Go(this.href); return false">{album-name}</a><br/><br/>
            Отправитель:<br/>
            <div><a href="/u{uid}" onClick="Page.Go(this.href); return false">{author}</a></div>
            <span style="color:#888">{author-info}</span><br/>
            <div class="menuleft" style="width:209px;">
                <a href="/" onClick="Distinguish.Start({id}); return false">
                    <div>Отметить человека</div>
                </a>
                [owner]<a href="/" onClick="photoeditor.start('{photo}', '{id}', '{height}'); return false">
                    <div>Корректировать</div>
                </a>
                <a href="/" onClick="crop.start({id}); return false">
                    <div>Поместить на мою страницу</div>
                </a>
                <a href="/" onClick="Photo.EditBox({id}, 0); return false">
                    <div>Редактировать фотографию</div>
                </a>
                <a href="/" onClick="Photo.MsgDelete({id}, {aid}, 1); return false">
                    <div>Удалить фотографию</div>
                </a>[/owner]
                <a onClick="Report.Box('photo', '{id}')">
                    <div>Пожаловаться на фотаграфию</div>
                </a>
                [owner]
                <div class="photos_gradus_pos">
                    <div class="fl_l">Повернуть:</div>
                    <div class="photos_gradus_left fl_l" onClick="Photo.Rotation('right', '{id}')"></div>
                    <div class="photos_gradus_right fl_l" onClick="Photo.Rotation('left', '{id}')"></div>
                    <div class="fl_l" style="margin-left:5px"><img src="/images/loading_mini.gif"
                                                                   id="loading_gradus{id}" class="no_display"/></div>
                </div>
                [/owner]
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>