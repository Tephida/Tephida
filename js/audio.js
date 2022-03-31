/* HTML 5 AUDIO PLAYER */
var cur = {
    destroy: []
};
cur.langs = {};
cur.Media = {};
var kjTimers = {};

function Scroller(id, opts) {
    if (!opts) opts = {};
    this.bl = $('#' + id);
    this.cont = this.bl.children('.scroller_cont');
    this.bl.append('<div class="scroller_panel"><div class="slider"></div></div>').addClass('scroller');
    this.panel = this.bl.children('.scroller_panel');
    this.slider = this.panel.children('.slider');
    var _s = this;
    _s.state = {
        t: 0,
        h: 0
    };
    this.bl.bind('mousewheel DOMMouseScroll', function (o) {
        var e = o;
        o.preventDefault();
        if (!_s.state.visible) return;
        var top = e.wheelDelta > 0 ? true : false,
            t = _s.state.t;
        var delta = e.detail ? (e.detail / 3) * (-100) : e.wheelDelta;
        t += (delta);
        var max = _s.state.bl_h - _s.state.cont_h;
        t = Math.min(0, Math.max(max, t));
        if (_s.state.t != t) {
            if (t <= max && opts.onBottom) opts.onBottom();
            _s.state.t = t;
            _s.cont.css('top', t + 'px');
            _s.update_slider();
        }
    });
    var win = $(window);

    function slide(e) {
        if (!_s.state.visible) return;
        e.preventDefault();
        var st = e.clientY,
            gl_p = 100 - (_s.state.bl_h / _s.state.cont_h) * 100,
            bl_pos = _s.bl.offset().top,
            pos = _s.slider.offset().top - bl_pos,
            h = _s.slider.height(),
            bottom = _s.state.cont_h - _s.state.bl_h;
        function Move(e1) {
            var nt = e1.clientY - st,
                r = Math.max(0, Math.min(_s.state.bl_h - h, (pos + (nt))));
            _s.slider.css('margin-top', r + 'px');
            var p = r / _s.state.bl_h * 100,
                top = p * _s.state.cont_h / 100;
            if (_s.state.t != -top) {
                if (top >= bottom && opts.onBottom) opts.onBottom();
                _s.cont.css('top', -top + 'px');
                _s.state.t = -top;
            }
        }
        function Up() {
            win.unbind('mousemove', Move).unbind('mouseup', Up);
        }
        Up();
        win.bind('mousemove', Move).bind('mouseup', Up);
        Move(e);
    }
    this.slider.bind('mousedown', slide);
    this.panel.bind('mousedown', function (e) {
        if (!_s.state.visible || e.target.className == 'slider') return;
        var bl_pos = _s.bl.offset().top,
            h = _s.slider.height(),
            move_slider = Math.max(0, Math.min(_s.state.bl_h - h, (e.clientY - bl_pos) - (h / 2)));
        _s.slider.css('margin-top', move_slider + 'px');
        slide(e);
    });
    this.check_scroll();
}

$.extend(Scroller.prototype, {
    check_scroll: function (opts) {
        if (!opts) opts = {};
        var bl_h = this.bl.height(),
            cont_h = this.cont[0].scrollHeight;
        this.state.visible = false;
        if (!opts.no_top) this.cont.css('top', '0px');
        if (cont_h > bl_h) {
            var p = Math.floor((bl_h / cont_h) * 100),
                h = Math.min(bl_h, Math.round(p * bl_h / 100));
            this.slider.css('height', h + 'px').css('margin-top', '0px');
            this.panel.show();
            this.state.h = h;
            this.state.bl_h = bl_h;
            this.state.cont_h = cont_h;
            this.state.visible = true;
            this.update_slider();
        } else this.panel.hide();
    },
    update_slider: function () {
        var p = Math.round((Math.abs(this.state.t) / this.state.cont_h) * 100),
            mtop = p * this.state.bl_h / 100;
        mtop = Math.min(this.state.bl_h - 30, mtop);
        this.slider.css('margin-top', mtop + 'px');
    },
    toBottom: function () {
        var bl_h = this.bl.height(),
            cont_h = this.cont.get(0).scrollHeight,
            top = cont_h - bl_h;
        this.cont.css('top', '-' + top + 'px');
        this.state.t = -top;
        this.update_slider();
    },
    toTop: function () {
        this.cont.css('top', '0px');
        this.state.t = 0;
        this.update_slider();
    },
    checklAndBottom: function () {
        this.check_scroll({
            no_top: 1
        });
        this.toBottom();
    }
});

function str_replace(search, replace, subject) {
    if (!(replace instanceof Array)) {
        replace = new Array(replace);
        if (search instanceof Array) {
            while (search.length > replace.length) {
                replace[replace.length] = replace[0];
            }
        }
    }
    if (!(search instanceof Array)) search = new Array(search);
    while (search.length > replace.length) {
        replace[replace.length] = '';
    }
    if (subject instanceof Array) {
        for (k in subject) {
            subject[k] = str_replace(search, replace, subject[k]);
        }
        return subject;
    }
    for (var k = 0; k < search.length; k++) {
        var i = subject.indexOf(search[k]);
        while (i > -1) {
            subject = subject.replace(search[k], replace[k]);
            i = subject.indexOf(search[k], i);
        }
    }
    return subject;
}
if (!window.Kj) window.Kj = {};
Kj.Selector = {
    oldID: null,
    init: function (id, options, def, opts) {
        var count = 0,
            liClass = '',
            defText = '';
        if (!opts) opts = {};
        $(id).html('');
        $(id).append('<div class="kjSelectorContainer"></div><div class="kjSelectorStrelkaBL"></div>');
        for (var i in options) {
            if (def == options[i]) {
                liClass = 'class="kjSelectorLiHover"';
                defText = i;
                Kj.Selector.old_titles[id] = i;
            } else liClass = '';
            if (options[i] == 0) $(id + ' .kjSelectorContainer').prepend('<li ' + liClass + ' value="' + options[i] + '" dir="auto">' + i + '</li>');
            else $(id + ' .kjSelectorContainer').append('<li ' + liClass + ' value="' + options[i] + '" dir="auto">' + i + '</li>');
            count++;
        }
        if (!def) {
            var firstLi = $(id + ' li:first');
            defText = firstLi.text();
            Kj.Selector.old_titles[id] = defText;
            def = firstLi.attr('value');
            firstLi.addClass('kjSelectorLiHover');
        }
        var editable = opts.search ? 'contenteditable="true" spellcheck="true"' : '';
        $(id).prepend('<div class="kjSelectorTop" ' + editable + ' dir="auto">' + defText + '</div>').val(def);
        if (opts.search) $(id + ' .kjSelectorTop').bind('keypress', function (e) {
            Kj.Selector.keyPress(e, id);
        }).bind('keyup', function (e) {
            Kj.Selector.search(opts.type, $(this).text(), id, e);
        });
        $(id + ' .kjSelectorTop, ' + id + ' .kjSelectorStrelkaBL').mousedown(function () {
            if (Kj.Selector.oldID != id) {
                setTimeout(function () {
                    var elPos = $(window).height() - ($(id).offset().top + 20),
                        blContainer = $(id + ' .kjSelectorContainer');
                    if (blContainer.height() > elPos) blContainer.addClass('kjSelectorContBottom').removeClass('kjSelectorContTop').css('top', '-' + $(id + ' .kjSelectorContainer').height() + 'px');
                    else blContainer.addClass('kjSelectorContTop').removeClass('kjSelectorContBottom');
                    $(id + ' .kjSelectorContainer').show().scrollTop(0);
                    $(id + ' .kjSelectorStrelkaBL').addClass('kjSelectorStrelkaHover');
                    $(id + ' li').removeClass('kjSelectorLiHover');
                    $(id + ' li[value=' + $(id).val() + ']').addClass('kjSelectorLiHover');
                });
                Kj.Selector.oldID = id;
            } else {
                $('.kjSelector .kjSelectorContainer').hide();
                $('.kjSelector .kjSelectorStrelkaBL').removeClass('kjSelectorStrelkaHover');
                Kj.Selector.oldID = null;
            }
        });
        $(id + ' li').mouseover(function () {
            $(id + ' li').removeClass('kjSelectorLiHover');
            $(this).addClass('kjSelectorLiHover');
        }).mousedown(function () {
            var val = $(this).attr('value'),
                text = $(this).text();
            $(id).val(val);
            $(id + ' .kjSelectorTop').html(text);
            Kj.Selector.old_titles[id] = text;
            $(id).change();
            Kj.Selector.oldID = null;
        });
        var contBL = $(id + ' .kjSelectorContainer');
        contBL.width($(id).width());
        if (count > 10) contBL.addClass('kjSelectorScroll');
        $(window).mousedown(function (e) {
            if ($(e.target).filter('.kjSelectorContainer:visible').length >= 1) return;
            $.each($('.kjSelector'), function () {
                var id = '#' + $(this).attr('id');
                $(id + ' .kjSelectorTop').html(Kj.Selector.old_titles[id]);
                $(id + ' .kjSelectorStrelkaBL').removeClass('kjSelectorStrelkaHover');
                $(id + ' .kjSelectorContainer').hide();
            });
            Kj.Selector.oldID = null;
        });
    },
    keyPress: function (e, id) {
        if (e.keyCode == 13) {
            e.preventDefault();
            $(id + ' li.kjSelectorLiHover').trigger('mousedown');
        }
    },
    old_titles: {},
    search: function (type, val, id, e) {
        removeTimer('search_select');
        addTimer('search_select', function () {
            if (e.keyCode == 40) {
                e.preventDefault();
                var next = $(id + ' li.kjSelectorLiHover').next();
                if (next.length == 0) next = $(id + ' li:first');
                next.trigger('mouseover');
                return;
            } else if (e.keyCode == 38) {
                e.preventDefault();
                var prev = $(id + ' li.kjSelectorLiHover').prev();
                if (prev.length == 0) prev = $(id + ' li:last');
                prev.trigger('mouseover');
                return;
            } else if (e.keyCode == 39 || e.keyCode == 37) return;
            var data = {
                query: val
            };
            if (type == 'country') {
                var url = '/edit?act=country';
                $('#country_preloader').show();
            } else if (type == 'city') {
                var url = '/edit?act=city';
                $('#city_preloader').show();
                data['id'] = $('#edit_country').val();
            }
            var first = 0;
            ajax.post(url, data, function (d) {
                if (d) {
                    $(id + ' .kjSelectorContainer').html('');
                    options = JSON.parse(d);
                    for (var i in options) {
                        var liClass = first ? '' : 'class="kjSelectorLiHover"';
                        first = 1;
                        if (options[i] == 0) $(id + ' .kjSelectorContainer').prepend('<li ' + liClass + ' value="' + options[i] + '">' + i + '</li>');
                        else $(id + ' .kjSelectorContainer').append('<li ' + liClass + ' value="' + options[i] + '">' + i + '</li>');
                    }
                    $(id + ' li').mouseover(function () {
                        $(id + ' li').removeClass('kjSelectorLiHover');
                        $(this).addClass('kjSelectorLiHover');
                    }).mousedown(function () {
                        var val = $(this).attr('value'),
                            text = $(this).text();
                        $(id).val(val);
                        $(id + ' .kjSelectorTop').html(text);
                        Kj.Selector.old_titles[id] = text;
                        $(id).change();
                        Kj.Selector.oldID = null;
                    });
                    $(id + ' .kjSelectorContainer').removeClass('kjSelectorScroll');
                }
                if (type == 'country') $('#country_preloader').hide();
                else if (type == 'city') $('#city_preloader').hide();
            });
        }, 200);
    }
};

Kj.dropMenu = {
    Init: function (p) {
        $('#' + p.id).addClass('DropMenu').attr('onClick', 'Kj.dropMenu.Hover(\'' + p.id + '\')').attr('onMouseOut', 'Kj.dropMenu.Out()');
        $('#' + p.id).html('<div onmouseover="Kj.dropMenu.over()" onMouseOut="Kj.dropMenu.Out()"><div id="titleDrop"></div><div class="DropMenuItems">' + $('#' + p.id).html() + '</div></div>');
        if (!p.nochange) {
            if (!p.selected) {
                var value = $('#' + p.id + ' li:first').attr('value');
                $('#' + p.id + ' #titleDrop').html($('#' + p.id + ' li:first').html());
                $('#' + p.id).attr('value', value).val(value);
            } else var value = p.selected;
        } else $('#' + p.id + ' #titleDrop').html(p.title);
        $.each($('#' + p.id + ' li'), function () {
            var val = $(this).attr('value');
            $(this).addClass('DropMenuItem');
            if (!p.nochange) $(this).attr('onClick', 'Kj.dropMenu.Change(\'' + p.id + '\', this, \'' + val + '\')');
            if (val == value && !p.nochange) {
                $('#' + p.id + ' #titleDrop').html($(this).html());
                $('#' + p.id).attr('value', val).val(val);
                $(this).addClass('DropMenuItemSelected');
            }
        });
        $('#' + p.id + ' .DropMenuItems').css('margin-left', '-1px');
        setTimeout(function () {
            window.objF = $('#' + p.id + ' .DropMenuItems');
            $('#' + p.id).css('width', ($('#' + p.id + ' .DropMenuItems').width() - 2) + 'px').after('<div class="clear"></div>');
        });
    },
    over: function () {
        removeTimer('dropmenu');
    },
    opened: 0,
    Hover: function (id) {
        if (Kj.dropMenu.opened == id) {
            Kj.dropMenu.opened = 0;
            return;
        }
        setTimeout(function () {
            Kj.dropMenu.opened = id;
            removeTimer('dropmenu');
            $('.DropMenu').removeClass('DropMenuHover');
            $('#' + id).addClass('DropMenuHover');
            $('#' + id + ' .DropMenuItems').show();
        }, 100);
    },
    Out: function () {
        removeTimer('dropmenu');
        addTimer('dropmenu', function () {
            Kj.dropMenu.opened = 0;
            $('.DropMenu').removeClass('DropMenuHover');
            $('.DropMenuItems').hide();
        }, 700);
    },
    Change: function (id, block, value) {
        var bl = $(block);
        $('#' + id + ' li').removeClass('DropMenuItemSelected');
        bl.addClass('DropMenuItemSelected');
        $('#' + id).attr('value', value).val(value);
        $('#' + id + ' #titleDrop').html(bl.html());
        $('#' + id).change();
        Kj.dropMenu.opened = id;
        cancelEvent(window.event);
    },
    inputValues: {},
    callbacks: {},
    InitInput: function (p) {
        if (!p.width) p.width = 400;
        if (!p.noAdd) p.noAdd = 15;
        $(p.id).addClass('DropInput').css('width', p.width + 'px');
        Kj.dropMenu.inputValues[p.id] = {};
        $(p.id).html('<div class="DropInputBlock" onMouseDown="Kj.dropMenu.DownInput(\'' + p.id + '\', ' + p.noAdd + ')"><div id="titleDrop"><div class="dropInputItems"><div id="items"></div><input type="text"/><div class="clear"></div></div></div><div class="DropInputStrelka"></div></div><div class="DropInputItem" style="width: ' + ($(p.id).width() - 2) + 'px">' + $(p.id).html() + '</div>');
        if (p.type == 'friends') var url = '/index.php?go=repost&act=loadFriends';
        else if (p.type == 'groups') var url = '/index.php?go=repost&act=loadGroups¬_id=' + p.notID;
        else if (p.type == 'matirial') var url = '/edit?act=loadMatirial&male=1';
        else if (p.type == 'matirial2') var url = '/edit?act=loadMatirial';
        $(p.id + ' input').attr('placeholder', p.text).keyup(function () {
            Kj.dropMenu.sershInput({
                id: p.id,
                url: url,
                query: $(p.id + ' input').val(),
                noAdd: p.noAdd
            });
        });
        Kj.dropMenu.sershInput({
            id: p.id,
            url: url,
            query: '',
            noAdd: p.noAdd,
            sel: p.selected
        });
        $(p.id).after('<div class="clear"></div>');
        if (p.cb) this.callbacks[p.id] = p.cb;
        Kj.dropMenu.size_input(p.id);
    },
    size_input: function (id) {
        var inp = $(id + ' #titleDrop input'),
            w = $(id).width() - 30,
            items = $(id + ' #items'),
            items_w = items.width(),
            rw = Math.max(100, w - items_w);
        if (items_w >= w) {
            var ww = 0;
            $(id + ' #items li').each(function () {
                ww += $(this).width() + 3;
            });
            var lines = Math.ceil(ww / w);
            rw = w * (ww / w) - ww;
            items.css('float', 'none');
        } else items.css('float', 'left');
        inp.css('width', rw + 'px');
    },
    sershInput: function (p) {
        $(p.id + ' .DropInputItem').load(p.url, {
            query: p.query,
            values: JSON.stringify(Kj.dropMenu.inputValues[p.id]),
            sel: p.sel
        }, function (d) {
            $.each($(p.id + ' .DropInputItem li'), function () {
                var img = $(this).attr('data-img');
                var name = $(this).attr('data-name');
                var value = $(this).val();
                var traf = $(this).attr('data-traf') || '';
                $(this).html('<div class="fl_l"><img src="' + img + '" style="width: 30px; height: 30px"/></div><div class="fl_l" style="margin-left: 5px"><div class="uName">' + name + '</div><div style="color: #666; font-size: 10px; margin-top: 2px">' + traf + '</div></div><div class="clear"></div>').mouseover(function () {
                    $(p.id + ' .DropInputItem li').removeClass('DropInputItemsHover');
                    $(this).addClass('DropInputItemsHover');
                }).mousedown(function () {
                    Kj.dropMenu.insertItem({
                        id: p.id,
                        val: value,
                        name: name,
                        img: img,
                        elem: this,
                        traf: traf,
                        noAdd: p.noAdd
                    });
                });
                if (value == p.sel) Kj.dropMenu.insertItem({
                    id: p.id,
                    val: value,
                    name: name,
                    img: img,
                    elem: this,
                    traf: traf,
                    noAdd: p.noAdd
                });
            });
        });
    },
    DownInput: function (id, noAdd) {
        if ($(id + ' .dropInputItems #items li').length < noAdd) {
            $(id + ' .DropInputItem li').removeClass('DropInputItemsHover');
            $(id + ' .DropInputItem li:first').addClass('DropInputItemsHover');
            $(id + ' .DropInputItem').show();
            $(id + ' input').focus().blur(function () {
                $(id + ' .DropInputItem').hide();
            });
            Kj.dropMenu.size_input(id);
        }
    },
    insertItem: function (p) {
        $(p.id + ' input').hide().val('');
        $(p.elem).remove();
        var arrInp = Kj.dropMenu.inputValues[p.id];
        arrInp[p.val] = 1;
        if (Kj.dropMenu.callbacks[p.id]) Kj.dropMenu.callbacks[p.id](Kj.dropMenu.inputValues[p.id]);
        var ins_li = $('<li/>').appendTo(p.id + ' .dropInputItems #items');
        ins_li.attr({
            'data-name': p.name,
            'data-img': p.img,
            value: p.val,
            'data-traf': p.traf
        }).html(p.name + ' ');
        var clos_lnk = $('<span/>').appendTo(ins_li);
        clos_lnk.html('x').addClass('clos').mousedown(function () {
            ins_li.remove();
            delete arrInp[p.val];
            $(p.id + ' .DropInputItem').hide();
            if ($(p.id + ' .dropInputItems #items li').length >= p.noAdd || !$(p.id + ' .dropInputItems #items li').size()) {
                $(p.id + ' .dropInputItems #items #addbut').remove();
                $(p.id + ' input').show();
                var height = $(p.id + ' .dropInputItems').height() - 8;
            } else var height = $(p.id + ' .dropInputItems').height() + 4;
            $(p.id + ' .DropInputBlock').css('height', height + 'px');
            var new_elem = $('<li/>').appendTo($(p.id + ' .DropInputItem'));
            new_elem.attr({
                'data-name': p.name,
                'data-img': p.img,
                value: p.val,
                'data-traf': p.traf
            }).html('<div class="fl_l"><img src="' + p.img + '" style="width: 30px; height: 30px"/></div><div class="fl_l" style="margin-left: 5px"><div class="uName">' + p.name + '</div><div style="color: #666; font-size: 10px; margin-top: 2px">' + p.traf + '</div></div><div class="clear"></div>').mouseover(function () {
                $(p.id + ' .DropInputItem li').removeClass('DropInputItemsHover');
                $(new_elem).addClass('DropInputItemsHover');
            }).mousedown(function () {
                Kj.dropMenu.insertItem({
                    id: p.id,
                    val: p.val,
                    name: p.name,
                    img: p.img,
                    elem: new_elem,
                    traf: p.traf,
                    noAdd: p.noAdd
                });
            });
            $(p.id + ' #addbut').trigger('mousedown');
            if (Kj.dropMenu.callbacks[p.id]) Kj.dropMenu.callbacks[p.id](Kj.dropMenu.inputValues[p.id]);
            Kj.dropMenu.size_input(p.id);
        });
        $(p.id + ' .dropInputItems #items #addbut').remove();
        if ($(p.id + ' .dropInputItems #items li').length < p.noAdd) {
            var add_li = $('<li/>').appendTo(p.id + ' .dropInputItems #items');
            add_li.mousedown(function () {
                $(p.id + ' .dropInputItems #items #addbut').remove();
                $(p.id + ' input').show().val('');
                var height = $(p.id + ' .dropInputItems').height() - 8;
                $(p.id + ' .DropInputBlock').css('height', height + 'px');
                setTimeout(function () {
                    Kj.dropMenu.DownInput(p.id, p.noAdd);
                }, 100);
            }).html(langs.media_video_add + ' <span class="clos">+</span>').addClass('addItemDropInput').attr('id', 'addbut');
        }
        var height = $(p.id + ' .dropInputItems').height() + 4;
        $(p.id + ' .DropInputBlock').css('height', height + 'px');
        Kj.dropMenu.size_input(p.id);
    }
};

Kj.radioBtn = {
    radioInit: function (bl, def, cb) {
        if (!def && arguments[0] == false) def = $(bl + ' div:first').attr('value');
        $(bl).attr('value', def);
        $.each($(bl + ' div'), function () {
            var val = $(this).attr('value');
            if (val == def) var classAdd = 'uiButtonBgActive';
            else var classAdd = '';
            $(this).html('<div class="ui_radioDiv" onmousedown="Kj.radioBtn.radioDown(this, \'' + bl + '\', ' + cb + ')" value="' + val + '"><div class="uiButtonBg ' + classAdd + '"></div><div class="fl_l">' + $(this).html() + '</div><div class="clear"></div></div>');
        });
    },
    radioDown: function (el, bl, cb) {
        $(bl + ' .uiButtonBg').removeClass('uiButtonBgActive');
        var elem = $(el).children('.uiButtonBg');
        elem.addClass('uiButtonBgActive');
        $(bl).val($(el).attr('value')).change();
        if (cb) cb();
    }
};
var kjSelectArea = {
    data: {},
    Init: function (id, p) {
        var _s = this;
        _s.data[id] = p;
        var width = $(id).width(),
            height = $(id).height(),
            bright = width - p.sw - 50,
            bbottom = height - p.sh - 50;
        $(id).prepend('<div class="kjSelectAreaBL" style="border-width: 50px ' + bright + 'px ' + bbottom + 'px 50px; width: ' + p.width + 'px;height: ' + p.height + 'px;"></div><div class="dropAreaBlock" style="width: ' + p.width + 'px;height: ' + p.height + 'px;">\
		<div class="kjSelectAreaResize select_resize_1" onmousedown="return kjSelectArea.resize(\'rtl\', \'' + id + '\');"></div>\
		<div class="kjSelectAreaResize select_resize_2" onmousedown="return kjSelectArea.resize(\'mt\', \'' + id + '\');"></div>\
		<div class="kjSelectAreaResize select_resize_3" onmousedown="return kjSelectArea.resize(\'rtr\', \'' + id + '\');"></div>\
		<div class="kjSelectAreaResize select_resize_4" onmousedown="return kjSelectArea.resize(\'mr\', \'' + id + '\');"></div>\
		<div class="kjSelectAreaResize select_resize_5" onmousedown="return kjSelectArea.resize(\'rbr\', \'' + id + '\');"></div>\
		<div class="kjSelectAreaResize select_resize_6" onmousedown="return kjSelectArea.resize(\'mb\', \'' + id + '\');"></div>\
		<div class="kjSelectAreaResize select_resize_7" onmousedown="return kjSelectArea.resize(\'rbl\', \'' + id + '\');"></div>\
		<div class="kjSelectAreaResize select_resize_8" onmousedown="return kjSelectArea.resize(\'ml\', \'' + id + '\');"></div>\
		</div>');
        var bl = $(id + ' .dropAreaBlock'),
            left = $(id).offset().left,
            top = $(id).offset().top,
            height = $(id).height(),
            width = $(id).width();
        if (p.hide) {
            bl.hide();
            $(id + ' .kjSelectAreaBL').css('border-color', 'rgba(0,0,0,0)');
        }
        bl.bind('mousedown', function (e) {
            if ($(e.target).filter('.kjSelectAreaResize').length > 0) return;
            e.preventDefault();
            left = $(id).offset().left, top = $(id).offset().top;
            _s.data[id].dl = e.pageX - bl.offset().left, _s.data[id].dt = e.pageY - bl.offset().top;
            $(window).bind('mousemove', Move);
        });
        $(window).bind('mouseup', Up);
        function Move(e1) {
            e1.preventDefault();
            if (_s.data[id].onStart) _s.data[id].onStart();
            var w1 = bl.width(),
                pos = e1.pageX - left - _s.data[id].dl,
                h1 = bl.height(),
                pos1 = e1.pageY - top - _s.data[id].dt;
            if ((pos + w1) > width) pos = width - w1;
            if (pos < 0) pos = 0;
            if (pos1 < 0) pos1 = 0;
            if ((pos1 + h1) > height) pos1 = height - h1;
            var bb = height - bl.height() - pos1,
                br = width - (bl.width() + pos),
                bt = top;
            bl.css('margin', pos1 + 'px ' + br + 'px ' + bb + 'px ' + pos + 'px');
            $(id + ' .kjSelectAreaBL').css('border-width', pos1 + 'px ' + br + 'px ' + bb + 'px ' + pos + 'px');
        }
        function Up() {
            $(window).unbind('mousemove', Move);
            if (_s.data[id].onEnd) _s.data[id].onEnd();
        }
        if (p.creator) {
            var pos_creat = {};
            $(id + ' .kjSelectAreaBL').bind('mousedown', function (e2) {
                left = $(id).offset().left, top = $(id).offset().top;
                var b1 = e2.pageX - left,
                    b2 = e2.pageY - top,
                    br = width - b1,
                    bb = height - b2;
                pos_creat = {
                    x: e2.pageX,
                    y: e2.pageY
                };
                bl.css({
                    'margin': b2 + 'px ' + br + 'px ' + bb + 'px ' + b1 + 'px',
                    width: 0,
                    height: 0
                }).show();
                $(id + ' .kjSelectAreaBL').css({
                    'border-width': b2 + 'px ' + (br - bl.width()) + 'px ' + (bb - bl.height()) + 'px ' + b1 + 'px',
                    width: 0,
                    height: 0
                }).css('border-color', 'rgba(0,0,0,0.7)');
                $(window).bind('mousemove', moveCreat);
                $(window).bind('mouseup', upCreat);
            }).css('cursor', 'crosshair');
            function moveCreat(e3) {
                e3.preventDefault();
                if (_s.data[id].onStart) _s.data[id].onStart();
                var p1 = e3.pageX,
                    p2 = e3.pageY,
                    l = bl.offset().left - left,
                    t = bl.offset().top - top;
                var w_nav = (p1 > pos_creat.x) ? true : false;
                var h_nav = (p2 > pos_creat.y) ? true : false;
                if (w_nav) {
                    var ml = pos_creat.x - left;
                    var w = Math.min(p1 - left - l, (width - ml));
                } else {
                    var ml = Math.max(e3.pageX - left, 0);
                    var w = Math.min(pos_creat.x - e3.pageX, (width - ml));
                }
                if (h_nav) {
                    var mt = pos_creat.y - top;
                    var h = Math.min(p2 - top - t, (height - mt));
                } else {
                    var mt = Math.max(e3.pageY - top, 0);
                    var h = Math.min(pos_creat.y - e3.pageY, (height - ml));
                }
                if (mt == 0) h = bl.height();
                if (ml == 0) w = bl.width();
                bl.css({
                    width: w + 'px',
                    'margin-left': ml + 'px',
                    height: h + 'px',
                    'margin-top': mt + 'px'
                });
                $(id + ' .kjSelectAreaBL').css({
                    'border-width': mt + 'px ' + ((width - ml) - bl.width()) + 'px ' + ((height - mt) - bl.height()) + 'px ' + ml + 'px',
                    width: w + 'px',
                    height: h + 'px'
                });
            }
            function upCreat() {
                $(window).unbind('mousemove', moveCreat);
                $(window).unbind('mouseup', upCreat);
                var h1 = bl.height();
                if (bl.width() < p.width) bl.css('width', p.width + 'px');
                if (h1 < p.height) bl.css('height', p.height + 'px');
                if (bl.width() > p.max_width) bl.css('width', p.max_width + 'px');
                if (h1 > p.max_height) bl.css('height', p.max_height + 'px');
                h1 = bl.height();
                if (((bl.offset().top - top) + h1) > height) bl.css('margin-top', (height - h1) + 'px');
                var mt = bl.offset().top - top,
                    ml = bl.offset().left - left;
                $(id + ' .kjSelectAreaBL').css({
                    'border-width': mt + 'px ' + ((width - ml) - bl.width()) + 'px ' + ((height - mt) - bl.height()) + 'px ' + ml + 'px',
                    width: bl.width() + 'px',
                    height: bl.height() + 'px'
                });
                if (_s.data[id].onEnd) _s.data[id].onEnd();
            }
        }
    },
    resize: function (type, id) {
        var width = $(id).width(),
            height = $(id).height(),
            bl = $(id + ' .dropAreaBlock'),
            left = $(id).offset().left,
            top = $(id).offset().top,
            _s = kjSelectArea,
            Move = false;
        if (_s.data[id].onStart) _s.data[id].onStart();
        if (type == 'mt') {
            Move = function (e) {
                e.preventDefault();
                var pos = Math.round(e.pageY - top),
                    mt = bl.offset().top - top,
                    h1 = bl.height();
                pos = Math.max(0, pos);
                var res = mt - pos,
                    res_h = res + h1;
                if (res_h < _s.data[id].max_height && res_h > _s.data[id].height) {
                    if (_s.data[id].sizes) {
                        var prop = res_h / bl.width();
                        if (prop > _s.data[id].sizeh) return;
                    }
                    bl.css({
                        height: res_h + 'px',
                        'margin-top': pos + 'px'
                    });
                    $(id + ' .kjSelectAreaBL').css({
                        'border-top-width': pos + 'px',
                        height: res_h + 'px'
                    });
                }
            };
        } else if (type == 'mr') {
            Move = function (e) {
                e.preventDefault();
                var pos = Math.round(e.pageX - left),
                    ml = bl.offset().left - left,
                    w1 = bl.width();
                pos = Math.min(pos, width);
                var res = pos - ml - w1,
                    res_w = res + w1,
                    dleft = width - (res_w + ml);
                if (res_w < _s.data[id].max_width && res_w > _s.data[id].width) {
                    if (_s.data[id].sizes) {
                        var prop = res_w / bl.height();
                        if (prop > _s.data[id].sizew) return;
                    }
                    bl.css({
                        width: res_w + 'px',
                        'margin-right': dleft + 'px'
                    });
                    $(id + ' .kjSelectAreaBL').css({
                        'border-right-width': dleft + 'px',
                        width: res_w + 'px'
                    });
                }
            };
        } else if (type == 'mb') {
            Move = function (e) {
                e.preventDefault();
                var pos = Math.round(e.pageY - top),
                    mt = bl.offset().top - top,
                    h1 = bl.height();
                pos = Math.min(pos, height);
                var res = pos - (mt + h1),
                    res_h = res + h1;
                if (res_h < _s.data[id].max_height && res_h > _s.data[id].height) {
                    if (_s.data[id].sizes) {
                        var prop = res_h / bl.width();
                        if (prop > _s.data[id].sizeh) return;
                    }
                    bl.css({
                        height: res_h + 'px',
                        'margin-bottom': (height - pos) + 'px'
                    });
                    $(id + ' .kjSelectAreaBL').css({
                        'border-bottom-width': (height - (res_h * 1) - mt) + 'px',
                        height: res_h + 'px'
                    });
                }
            };
        } else if (type == 'ml') {
            Move = function (e) {
                e.preventDefault();
                var pos = Math.round(e.pageX - left),
                    ml = bl.offset().left - left,
                    w1 = bl.width();
                pos = Math.max(0, pos);
                var res = ml - pos,
                    res_w = res + w1;
                if (res_w < _s.data[id].max_width && res_w > _s.data[id].width) {
                    if (_s.data[id].sizes) {
                        var prop = res_w / bl.height();
                        if (prop > _s.data[id].sizew) return;
                    }
                    bl.css({
                        width: res_w + 'px',
                        'margin-left': pos + 'px'
                    });
                    $(id + ' .kjSelectAreaBL').css({
                        'border-left-width': pos + 'px',
                        width: res_w + 'px'
                    });
                }
            };
        } else if (type == 'rtl') {
            Move = function (e) {
                e.preventDefault();
                var t1 = Math.round(e.pageY - top),
                    l1 = Math.round(e.pageX - left),
                    w1 = bl.width(),
                    h1 = bl.height(),
                    ml = bl.offset().left - left,
                    mt = bl.offset().top - top;
                l1 = Math.max(0, l1);
                t1 = Math.max(0, t1);
                var res = ml - l1,
                    res_w = res + w1,
                    res1 = mt - t1,
                    res_h = res1 + h1;
                if (res_w < _s.data[id].max_width && res_w > _s.data[id].width) {
                    bl.css({
                        width: res_w + 'px',
                        'margin-left': l1 + 'px'
                    });
                    $(id + ' .kjSelectAreaBL').css({
                        'border-left-width': l1 + 'px',
                        width: res_w + 'px'
                    });
                }
                if (res_h < _s.data[id].max_height && res_h > _s.data[id].height) {
                    bl.css({
                        height: res_h + 'px',
                        'margin-top': t1 + 'px'
                    });
                    $(id + ' .kjSelectAreaBL').css({
                        height: res_h + 'px',
                        'border-top-width': t1 + 'px'
                    });
                }
            };
        } else if (type == 'rtr') {
            Move = function (e) {
                e.preventDefault();
                var t1 = Math.round(e.pageY - top),
                    l1 = Math.round(e.pageX - left),
                    w1 = bl.width(),
                    h1 = bl.height(),
                    ml = bl.offset().left - left,
                    mt = bl.offset().top - top;
                l1 = Math.min(width, l1);
                t1 = Math.max(0, t1);
                var res = l1 - ml - w1,
                    res_w = res + w1,
                    res1 = mt - t1,
                    res_h = res1 + h1;
                if (res_w < _s.data[id].max_width && res_w > _s.data[id].width) {
                    bl.css({
                        width: res_w + 'px',
                        'margin-right': (width - res_w - ml) + 'px'
                    });
                    $(id + ' .kjSelectAreaBL').css({
                        'border-right-width': (width - res_w - ml) + 'px',
                        width: res_w + 'px'
                    });
                }
                if (res_h < _s.data[id].max_height && res_h > _s.data[id].height) {
                    bl.css({
                        height: res_h + 'px',
                        'margin-top': t1 + 'px'
                    });
                    $(id + ' .kjSelectAreaBL').css({
                        height: res_h + 'px',
                        'border-top-width': t1 + 'px'
                    });
                }
            }
        } else if (type == 'rbr') {
            Move = function (e) {
                e.preventDefault();
                var t1 = Math.round(e.pageY - top),
                    l1 = Math.round(e.pageX - left),
                    w1 = bl.width(),
                    h1 = bl.height(),
                    ml = bl.offset().left - left,
                    mt = bl.offset().top - top;
                l1 = Math.min(width, l1);
                t1 = Math.min(height, t1);
                var res = l1 - ml - w1,
                    res_w = res + w1,
                    res1 = t1 - (mt + h1),
                    res_h = res1 + h1;
                if (res_w < _s.data[id].max_width && res_w > _s.data[id].width) {
                    bl.css({
                        width: res_w + 'px',
                        'margin-right': (width - res_w - ml) + 'px'
                    });
                    $(id + ' .kjSelectAreaBL').css({
                        'border-right-width': (width - res_w - ml) + 'px',
                        width: res_w + 'px'
                    });
                }
                if (res_h < _s.data[id].max_height && res_h > _s.data[id].height) {
                    bl.css({
                        height: res_h + 'px',
                        'margin-bottom': (height - t1) + 'px'
                    });
                    $(id + ' .kjSelectAreaBL').css({
                        height: res_h + 'px',
                        'border-bottom-width': (height - (res_h * 1) - mt) + 'px'
                    });
                }
            };
        } else if (type == 'rbl') {
            Move = function (e) {
                e.preventDefault();
                var t1 = Math.round(e.pageY - top),
                    l1 = Math.round(e.pageX - left),
                    w1 = bl.width(),
                    h1 = bl.height(),
                    ml = bl.offset().left - left,
                    mt = bl.offset().top - top;
                l1 = Math.max(0, l1);
                t1 = Math.min(height, t1);
                var res = ml - l1,
                    res_w = res + w1,
                    res1 = t1 - (mt + h1),
                    res_h = res1 + h1;
                if (res_w < _s.data[id].max_width && res_w > _s.data[id].width) {
                    bl.css({
                        width: res_w + 'px',
                        'margin-left': l1 + 'px'
                    });
                    $(id + ' .kjSelectAreaBL').css({
                        'border-left-width': l1 + 'px',
                        width: res_w + 'px'
                    });
                }
                if (res_h < _s.data[id].max_height && res_h > _s.data[id].height) {
                    bl.css({
                        height: res_h + 'px',
                        'margin-bottom': (height - t1) + 'px'
                    });
                    $(id + ' .kjSelectAreaBL').css({
                        height: res_h + 'px',
                        'border-bottom-width': (height - (res_h * 1) - mt) + 'px'
                    });
                }
            };
        }
        $(window).bind('mousemove', Move);
        $(window).bind('mouseup', Up);
        function Up() {
            if (_s.data[id].onEnd) _s.data[id].onEnd();
            $(window).unbind('mouseup', Up);
            $(window).unbind('mousemove', Move);
        }
    },
    getPos: function (id, img) {
        var top1 = $(id).offset().top,
            left1 = $(id).offset().left;
        var bl = $(id + ' .dropAreaBlock'),
            img = img ? img : $(id + ' img'),
            top = bl.offset().top - top1,
            left = bl.offset().left - left1,
            width = img.width(),
            height = img.height(),
            w = bl.width(),
            h = bl.height();
        img.removeAttr('width').removeAttr('height').css({
            width: '',
            height: ''
        });
        var o_width = img.width(),
            o_height = img.height();
        img.attr('width', width + 'px').attr('height', height + 'px')
        var p_width = 100 - ((width / o_width) * 100),
            p_height = 100 - ((height / o_height) * 100);
        var r_top = top + ((p_height * top) / 100),
            r_left = left + ((p_width * left) / 100),
            r_width = w + ((p_width * w) / 100),
            r_height = h + ((p_height * h) / 100);
        return {
            top: Math.round(r_top),
            left: Math.round(r_left),
            width: Math.round(r_width),
            height: Math.round(r_height)
        };
    }
};

function Selector(p) {
    this.p = p;
    this.make();
    this.make_data();
}
$.extend(Selector.prototype, {
    make: function () {
        var _s = this;
        var readonly = this.p.no_write ? 'readonly' : '';
        $('#' + this.p.id).addClass('kj_selector').html('<div class="header">\
			<input type="text" class="title" value="- Not selected -" ' + readonly + ' dir="auto"/>\
			<div class="arrow_box"></div><div class="arrow"></div>\
		</div><div class="cont"></div>');
        $('#' + this.p.id + ' .title').bind('keyup', function (e) {
            if (e.keyCode == 40) {
                e.preventDefault();
                var next = $('#' + _s.p.id + ' .cont li.active').next();
                if (next.length == 0) next = $('#' + _s.p.id + ' .cont li:first');
                next = next.get(0);
                _s.over_item(next, 1);
                return;
            } else if (e.keyCode == 38) {
                e.preventDefault();
                var prev = $('#' + _s.p.id + ' .cont li.active').prev();
                if (prev.length == 0) prev = $('#' + _s.p.id + ' .cont li:last');
                prev = prev.get(0);
                _s.over_item(prev, 1);
                return;
            } else if (e.keyCode == 39 || e.keyCode == 37) return;
            else if (e.keyCode == 13) {
                var el = $('#' + _s.p.id + ' .cont li.active').get(0);
                if (el) _s.down_item(el);
                return;
            }
            _s.search(this.value);
        });
        if (this.p.no_write) {
            $('#' + this.p.id + ' .title').bind('click', function () {
                if ($('#' + _s.p.id + ' .title').attr('disabled')) return;
                if ($('#' + _s.p.id).hasClass('show')) $('#' + _s.p.id).removeClass('show');
                else _s.open_items();
            });
        } else {
            $('#' + this.p.id + ' .title').bind('focus', function () {
                if ($('#' + _s.p.id + ' .title').attr('disabled')) return;
                if (_s.curent[0] == 0) this.value = '';
                _s.open_items();
            });
        }
        $('#' + this.p.id + ' .arrow_box').bind('mousedown', function () {
            if ($('#' + _s.p.id + ' .title').attr('disabled')) return;
            if ($('#' + _s.p.id).hasClass('show')) $('#' + _s.p.id).removeClass('show');
            else _s.open_items();
        });
        $('#' + this.p.id + ' .cont').bind('mousewheel', function (e) {
            e.preventDefault();
            var delta = e.deltaY,
                direct = delta / Math.abs(delta);
            if (direct == 1) $(this).scrollTop($(this).scrollTop() + 15);
            else $(this).scrollTop($(this).scrollTop() - 15);
        });
    },
    curent: false,
    make_data: function () {
        if (!this.p.data.length) return;
        $('#' + this.p.id + ' .title').attr('placeholder', this.p.data[0][1]);
        if (!this.p.def) {
            $('#' + this.p.id).val(this.p.data[0][0]);
            $('#' + this.p.id + ' .title').val(this.p.data[0][1]);
            this.curent = this.p.data[0];
        } else {
            var d = this.p.data;
            for (var i = 0; i < d.length; i++) {
                if (d[i][0] == this.p.def) {
                    $('#' + this.p.id).val(d[i][0]);
                    $('#' + this.p.id + ' .title').val(String(d[i][1]).replace(/<\/?[^>]+>/gi, ''));
                    this.curent = d[i];
                    break;
                }
            }
        }
    },
    open_items: function (r) {
        var res = '',
            d = r ? r : this.p.data,
            _s = this;
        for (var i = 0; i < d.length; i++) {
            if (d[i][0] == this.curent[0] && !r || r && i == 0) res += '<li class="active" value="' + d[i][0] + '" dir="auto">' + d[i][1] + '</li>';
            else res += '<li value="' + d[i][0] + '" dir="auto">' + d[i][1] + '</li>';
        }
        $('#' + this.p.id + ' .cont').html(res);
        $('#' + this.p.id).addClass('show');
        $('#' + this.p.id + ' .cont li').bind('mouseover', function () {
            _s.over_item.apply(_s, [this]);
        }).bind('mousedown', function () {
            _s.down_item(this);
        });
        var wh = $(window).height() - ($('#' + this.p.id).offset().top - $(window).scrollTop()) - $('#' + this.p.id).height();
        if (wh < $('#' + this.p.id + ' .cont').height()) $('#' + this.p.id).addClass('top');
        else $('#' + this.p.id).removeClass('top');
        if (!r) $('#' + this.p.id + ' .cont').get(0).scrollTop = $('#' + this.p.id + ' .cont li.active').position().top - 50;
    },
    over_item: function (el, scroll) {
        $('#' + this.p.id + ' .cont li.active').removeClass('active');
        $(el).addClass('active');
        if (scroll) {
            var scroll1 = $('#' + this.p.id + ' .cont').get(0).scrollTop,
                top = $(el).position().top,
                w = $(el).height();
            if (scroll1 + w < top || top + w < scroll1) $('#' + this.p.id + ' .cont').get(0).scrollTop = top;
        }
    },
    down_item: function (el, no_change) {
        var val = (typeof el == 'number' || typeof el == 'string') ? el : el.value,
            d = this.p.data;
        for (var i = 0; i < d.length; i++) {
            if (d[i][0] == val) {
                $('#' + this.p.id + ' .title').val(String(d[i][1]).replace(/<\/?[^>]+>/gi, ''));
                this.curent = d[i];
                $('#' + this.p.id).removeClass('show');
                if (!no_change) $('#' + this.p.id).val(val).change();
                break;
            }
        }
    },
    search: function (val) {
        var res = '',
            d = this.p.data,
            r = [];
        for (var i = 1; i < d.length; i++)
            if (String(d[i][1]).toLowerCase().indexOf(val.toLowerCase()) != -1) r.push(d[i]);
        if (r.length) this.open_items(r);
        else $('#' + this.p.id + ' .cont').html('<li class="disabled">' + langs.global_not_found + '</li>');
    },
    blur: function () {
        $('#' + this.p.id + ' .title').val(String(this.curent[1]).replace(/<\/?[^>]+>/gi, ''));
    },
    change_data: function (data, current) {
        try {
            data = JSON.parse(data);
        } catch (e) {
            try {
                data = eval(data);
            } catch (e) {}
        }
        this.p.data = data;
        if (current) this.down_item(current, true);
        else {
            $('#' + this.p.id).val(data[0][0]);
            $('#' + this.p.id + ' .title').val(data[0][1]);
            $('#' + this.p.id + ' .title').attr('placeholder', data[0][1]);
            this.curent = data[0];
        }
    },
    open: function () {
        var _s = this;
        setTimeout(function () {
            $('#' + _s.p.id + ' .title').focus();
            $('#' + _s.p.id).addClass('show');
        }, 30);
    },
    disable: function () {
        $('#' + this.p.id + ' .title').attr('disabled', 'true');
    },
    enable: function () {
        $('#' + this.p.id + ' .title').removeAttr('disabled');
    }
});

function addTimer(name, callback, time) {
    if (kjTimers[name]) removeTimer(name);
    kjTimers[name] = setTimeout(function () {
        callback();
        delete kjTimers[name];
    }, time);
}

function removeTimer(name) {
    if (!kjTimers[name]) return;
    if (isArray(name)) {
        for (var i = 0; i <= name.length; i++) removeTimer(name[i]);
        return;
    }
    clearTimeout(kjTimers[name]);
    delete kjTimers[name];
}

function declOfNum(number, titles) {
    cases = [2, 0, 1, 1, 1, 2];
    return titles[(number % 100 > 4 && number % 100 < 20) ? 2 : cases[(number % 10 < 5) ? number % 10 : 5]];
}

var opened_title_html = 0;
function titleHtml(p){
    return showTooltip(document.getElementById(p.id), {text: p.text});
}

function showTooltip(el, opt) {
    if (el.ttTimer) {
        clearTimeout(el.ttTimer);
        el.ttTimer = 0;
        return;
    }
    if (el.err_tip) return;
    if (el.tt) {
        if (el.tt.showing || el.tt.load) return;
        if (el.tt.show_timer) {
            clearTimeout(el.tt.show_timer);
            el.tt.show_timer = 0;
        }
    }
    try {
        el.tt.el.style.display = 'block';
        if (el.tt.el.scrollHeight > 0) var is_bl = true;
        else var is_bl = false;
        el.tt.el.style.display = 'none';
    } catch (e) {
        var is_bl = false;
    }
    if (!is_bl) {
        var tt = document.createElement('div');
        tt.className = 'titleHtml  no_center' + (opt.className ? ' ' + opt.className : '');
        tt.innerHTML = '<div dir="auto" style="position: relative">' + opt.text + '<div class="black_strelka"></div></div>';
        document.body.appendChild(tt);
        el.tt = {};
        el.tt.opt = opt;
        el.tt.el = tt;
        if (!opt.shift) opt.shift = [0, 0, 0];
        el.tt.shift = opt.shift;
        el.tt.show = function () {
            if (this.tt.showing) return;
            this.tt.show_timer = 0;
            var ttobj = $(el.tt.el),
                ttw = ttobj.width(),
                tth = ttobj.height(),
                st = window.scrollY,
                obj = $(this),
                pos = obj.offset(),
                elh = obj.height();
            if ((pos.top - tth - this.tt.opt.shift[1]) < st || el.tt.opt.onBottom) {
                ttobj.addClass('down');
                var top = pos.top + (opt.shift[2]) + elh,
                    down = true;
            } else {
                ttobj.removeClass('down');
                var top = pos.top - (opt.shift[1]) - tth,
                    down = false;
            }
            ttobj.css({
                top: (top - 10) + 'px',
                left: (pos.left + (opt.shift[0])) + 'px'
            }).fadeIn(100);
            if (this.tt.opt.slide) {
                if (down) ttobj.css('margin-top', (this.tt.opt.slide + elh) + 'px');
                else ttobj.css('margin-top', '-' + this.tt.opt.slide + 'px');
                ttobj.animate({
                    marginTop: 0
                }, this.tt.opt.atime);
            }
            this.tt.showing = true;
        }.bind(el);
        el.tt.destroy = function () {
            var obj = $(el);
            obj.unbind('mouseout');
            clearTimeout(el.ttTimer);
            clearTimeout(this.tt.show_timer);
            $(el.tt.el).remove();
            el.tt = false;
        }.bind(el);
        function tooltipout(e, fast) {
            var hovered = $('div:hover');
            if (!fast && this.tt.opt.nohide && (hovered.index(this) != -1 || hovered.index(this.tt.el) != -1 || (this.tt.opt.check_parent && hovered.index($(this.parentNode)) != -1))) return;
            if (this.tt.show_timer) {
                clearTimeout(this.tt.show_timer);
                this.tt.show_timer = false;
            }
            if (!this.tt.showing) return;
            var time = fast ? 0 : (this.tt.opt.hideWt || 0),
                _s = this;
            this.ttTimer = setTimeout(function () {
                var tt_el = $(_s.tt.el);
                tt_el.fadeOut(100);
                _s.tt.showing = false;
                _s.ttTimer = false;
            }, time);
        }
        el.tt.hide = tooltipout.bind(el, false);
        $(el).mouseout(tooltipout.bind(el, false));
        if (opt.nohide) $(el.tt.el).bind('mouseover', showTooltip.bind(el, el, opt)).mouseout(tooltipout.bind(el, false));
        if (opt.url) {
            el.tt.load = true;
            $.post(opt.url, opt.data, function (d) {
                if (d == 'fail') {
                    el.tt.destroy();
                    el.err_tip = true;
                    return;
                }
                el.tt.el.innerHTML = d + '<div class="black_strelka"></div>';
                el.tt.load = false;
                if (el.tt.opt.complete) el.tt.opt.complete(el);
            });
            return;
        }
    }

    el.tt.show_timer = setTimeout(el.tt.show.apply(el), opt.showWt || 0);
}

function cancelEvent(event) {
    event = (event || window.event);
    if (!event) return false;
    while (event.originalEvent) event = event.originalEvent;
    if (event.preventDefault) event.preventDefault();
    if (event.stopPropagation) event.stopPropagation();
    event.cancelBubble = true;
    event.returnValue = false;
    return false;
}

function set_cookie(name, value, exp_y, exp_m, exp_d) {
    var cookie_string = name + "=" + escape(value);
    if (exp_y) {
        var expires = new Date(exp_y, exp_m, exp_d);
        cookie_string += "; expires=" + expires.toGMTString();
    }
    document.cookie = cookie_string;
}

function get_cookie(cookie_name) {
    var results = document.cookie.match('(^|;) ?' + cookie_name + '=([^;]*)(;|$)');
    if (results) return (unescape(results[2]));
    else return false;
}

function delete_cookie(cookie_name) {
    var cookie_date = new Date();
    cookie_date.setTime(cookie_date.getTime() - 1);
    document.cookie = cookie_name += "=; expires=" + cookie_date.toGMTString();
}

function playNewAudio(id, event) {
    if ($(event.target).parents('.tools, #no_play, .audioPlayer').length != 0 || $(event.target).filter('.text_avilable, #audio_text_res, #artist, #no_play').length != 0) return;
    cancelEvent(event);
    audio_player.playNew(id);

}

function isArray(obj) {
    return Object.prototype.toString.call(obj) === '[object Array]';
}

function a(a) {
    console.log(audio_player);
}

var audio_player = {
    old_type: '',
    players: {},
    aID: 0,
    aInfo: false,
    aOwner: 0,
    aType: '',
    fullID: '',
    inited: false,
    player: false,
    play: false,
    cplay: false,
    pause: true,
    is_html5: false,
    time: 0,
    pr_click: false,
    curTime: 0,
    timeDir: 0,
    playList: false,
    playLists: {},
    currentPos: 0,
    vol: get_cookie('audioVol') || 1,
    loop: false,
    shuffle: false,
    curPL: false,
    init: function (id) {
        var _a = audio_player;
        _a.player = document.getElementById('audioplayer');
        _a.player.addEventListener('canplay', _a.canPlay);
        _a.player.addEventListener('progress', _a.load_progress);
        _a.player.addEventListener('timeupdate', _a.play_progress);
        _a.player.addEventListener('ended', _a.play_finish);
        _a.player.addEventListener('error', function () {
            _a.nextTrack();
            _a.on_error;
        });
        _a.inited = true;
        _a.is_html5 = true;
        _a.player.volume = _a.vol;
        _a.playNew(id);
        $(window).bind('keyup', function (e) {
            if (!e.keyCode) return;
            if (e.keyCode == 179) {
                if (_a.pause) _a.command('play');
                else _a.command('pause');
            } else if (e.keyCode == 176) _a.nextTrack();
            else if (e.keyCode == 177) _a.prevTrack();
        });
    },
    addPlayer: function (d) {
        var _a = audio_player;
        _a.players[d.id] = d;
        if (!_a.inited) _a.init();
        $(d.play_but).bind('click', function (e) {
            if ($(this).hasClass('play')) _a.command('pause');
            else _a.command('play');
        });
        $(d.prbl).bind('mousedown', function (e) {
            _a.progressDown(e, d.id);
        }).bind('mousemove', function (e) {
            _a.progressMove(e, d.id);
        }).bind('mouseout', function () {
            $(_a.players[d.id].timeBl).hide();
        });
        $(d.prev).bind('click', _a.prevTrack);
        $(d.next).bind('click', _a.nextTrack);
        $(d.volume).bind('mousedown', _a.volumeDown);
        $(d.add).bind('click', _a.addAudio);
        _a.playLists = {};
        for (var i in d.playList) {
            var pl = d.playList,
                pl_data = {
                    data: [],
                    pname: d.pname
                };
            for (var j in pl) pl_data.data.push(pl[j]);
            _a.playLists[d.playList[i][7]] = pl_data;
            break;
        }
        if (!_a.aInfo) {
            for (var i in d.playList) {
                _a.aID = d.playList[i][1];
                _a.aOwner = d.playList[i][0];
                _a.aInfo = d.playList[i];
                var type = d.playList[i][7];
                _a.fullID = _a.aID + '_' + _a.aOwner + (type ? '_' + type : '');
                _a.time = d.playList[i][5];
                var s = parseInt(d.playList[i][5] % 60),
                    m = parseInt((d.playList[i][5] / 60) % 60);
                $(d.time).html(m + ':' + s);
                if (_a.is_html5) {
                    _a.player.src = d.playList[i][2];
                    _a.player.load();
                }
                _a.compilePlayList(d.playList[i][7]);
                break;
            }
            $('#audio_' + _a.fullID + ', #audio_' + _a.fullID + '_pad').addClass('play').addClass('preactiv');
            _a.play = false;
            _a.cplay = false;
        } else {
            if (_a.pause) $('#audio_' + _a.fullID + ', #audio_' + _a.fullID + '_pad').addClass('preactiv');
            else _a.command('play', {
                style_only: true
            });
        }
        _a.command('set_info', {
            player: d.id
        });
        var vol_percent = _a.vol * 100;
        $(d.volume_line).css('width', vol_percent + '%');
        $(d.loop).bind('click', _a.clickLoop);
        $(d.shuffle).bind('click', _a.clickShuffle);
        if (_a.loop) $(d.loop).addClass('active');
        if (_a.shuffle) $(d.shuffle).addClass('active');
        _a.check_add();
    },
    clickLoop: function () {
        var _a = audio_player;
        if (_a.loop) _a.command('off_loop');
        else _a.command('on_loop');
    },
    clickShuffle: function () {
        var _a = audio_player;
        if (_a.shuffle) _a.command('off_shuffle');
        else _a.command('on_shuffle');
    },
    play_pause: function () {
        var _a = audio_player;
        if (_a.pause) _a.command('play');
        else _a.command('pause');
    },
    command: function (type, params) {
        var _a = audio_player;
        if (!params) params = {};

        if (type == 'pause') {
            for (var i in _a.players) $(_a.players[i].play_but).removeClass('play');
            $('#audio_' + _a.fullID + ', #audio_' + _a.fullID + '_pad').addClass('pause');
            $('#audioMP .playBtn').removeClass('icon-pause').addClass('icon-play-4');
            if (params.style_only) return;
            if (_a.inited) {
                if (_a.is_html5) {
                    _a.play = false;
                    _a.player.pause();
                } else _a.player.pauseAudio();
            }
            _a.pause = true;
        } else if (type == 'play') {
            for (var i in _a.players) $(_a.players[i].play_but).addClass('play');
            $('#audio_' + _a.fullID + ', #audio_' + _a.fullID + '_pad').removeClass('pause').removeClass('preactiv').addClass('play');
            $('#player' + _a.fullID).css('display', 'block');

            $('.player' + _a.fullID).css('display', 'block');

            $('#player' + _a.fullID + ' #playerVolumeBar').css('width', (_a.vol * 100) + '%');
            $('#audioMP .playBtn').removeClass('icon-play-4').addClass('icon-pause');
            _a.initMP();
            if (params.style_only) return;
            if (_a.inited) {
                if (_a.cplay) {
                    if (Math.round(_a.player.currentTime) == 0) _a.player.load();
                    _a.player.play();
                } else _a.player.play();
            }
            _a.pause = false;
        } else if (type == 'set_info') {
            if (params.player) $(_a.players[params.player].names).html('<b>' + _a.aInfo[3] + '</b> – ' + _a.aInfo[4]);
            else
                for (var i in _a.players) $(_a.players[i].names).html('<b>' + _a.aInfo[3] + '</b> – ' + _a.aInfo[4]);
        } else if (type == 'load_progress') {
            for (var i in _a.players) $(_a.players[i].load).css('width', params.p + '%');
            $('#player' + _a.fullID + ' .audioLoadProgress').css('width', params.p + '%');
        } else if (type == 'play_progress') {
            if (_a.pr_click) return;
            for (var i in _a.players) $(_a.players[i].pr).css('width', params.p + '%');
            $('#player' + _a.fullID + ' #playerPlayLine').css('width', params.p + '%');
        } else if (type == 'update_time') {
            for (var i in _a.players) $(_a.players[i].time).html(params.time);
            $('#audio_time_' + _a.fullID + ', #audio_time_' + _a.fullID + '_pad').html(params.time);
        } else if (type == 'off_loop') {
            _a.loop = false;
            for (var i in _a.players) $(_a.players[i].loop).removeClass('active');
        } else if (type == 'on_loop') {
            _a.loop = true;
            for (var i in _a.players) $(_a.players[i].loop).addClass('active');
        } else if (type == 'off_shuffle') {
            _a.shuffle = false;
            for (var i in _a.players) $(_a.players[i].shuffle).removeClass('active');
        } else if (type == 'on_shuffle') {
            _a.shuffle = true;
            for (var i in _a.players) $(_a.players[i].shuffle).addClass('active');
        } else if (type == 'show_add') {
            for (var i in _a.players) {
                $(_a.players[i].add).show();
                if (params.added) $(_a.players[i].add).addClass('icon-ok-3');
                else $(_a.players[i].add).removeClass('icon-ok-3');
            }
        } else if (type == 'hide_add') {
            for (var i in _a.players) $(_a.players[i].add).hide();
        }
    },
    playNew: function (id) {
        var _a = audio_player;
        if (!id) return;
        if (!_a.inited) {
            _a.init(id);
            return;
        }
        id = id.replace('_pad', '');
        if (_a.fullID == id) _a.command(_a.pause ? 'play' : 'pause');
        else {
            if (_a.fullID) {
                $('#audio_' + _a.fullID + ', #audio_' + _a.fullID + '_pad').removeClass('play').removeClass('pause').removeClass('preactiv');
                _a.backTime(_a.fullID, _a.time);
            }
            _a.player.pause();
            _a.player = null;
            $('.audioPlayer').hide();
            var adata = id.split('_');
            _a.aID = adata[0];
            _a.aOwner = adata[1];
            _a.aType = adata[2] ? adata[2] : '';
            _a.fullID = _a.aID + '_' + _a.aOwner + ((adata[2] && adata[2] != 'pad') ? '_' + adata[2] : '');
            _a.getInfoFromDom();
            $('#audio_' + _a.fullID + ', #audio_' + _a.fullID + '_pad').addClass('play');
            _a.play = true;
            _a.cplay = false;
            _a.player = document.getElementById('audioplayer');
            _a.command('play', {
                style_only: true
            });
            _a.curTime = 0;
            _a.player.src = _a.aInfo[2];
            _a.player.load();
            _a.command('set_info');
            if (adata[3] != 'pad') {
                _a.compilePlayList(_a.aInfo[7]);
            }
            if (_a.aInfo[8] != 'page') _a.scrollToAudio();
            try {
                var pl = _a.playlist.data,
                    cnt = 0;
                for (var i in pl) {
                    var id = pl[i][1] + '_' + pl[i][0] + (pl[i][7] ? '_' + pl[i][7] : '');
                    if (id == _a.fullID) {
                        _a.currentPos = cnt;
                    }
                    cnt++;
                }
            } catch (e) {}

            _a.check_add();
        }
    },
    getInfoFromDom: function () {
        var _a = audio_player,
            aid = _a.fullID
        if ($('#audio_url_' + aid).length) {
            var url = $('#audio_url_' + aid).val().split(',');
            _a.aInfo = [_a.aOwner, _a.aID, url[0], $('#audio_' + aid + ' #artist').html(), $('#audio_' + aid + ' #name').html(), url[1], $('#audio_time_' + aid).text(), _a.aType, url[2]];
            _a.time = url[1];
        } else if ($('#audio_url_' + aid + '_pad').length) {
            var url = $('#audio_url_' + aid + '_pad').val().split(',');
            _a.aInfo = [_a.aOwner, _a.aID, url[0], $('#audio_' + aid + '_pad' + ' #artist').html(), $('#audio_' + aid + '_pad' + ' #name').html(), url[1], $('#audio_time_' + aid + '_pad').text(), _a.aType, url[2]];
            _a.time = url[1];
        }
    },
    canPlay: function () {
        var _a = audio_player;
        if (_a.play) {
            _a.player.play();
            _a.pause = false;
        }
        _a.cplay = true;
    },
    play_progress: function (curTime, totalTime) {
        var _a = audio_player;
        if (_a.is_html5) {
            curTime = Math.floor(_a.player.currentTime * 1000) / 1000;
            totalTime = Math.floor(_a.player.duration * 1000) / 1000;
        } else {
            if (isNaN(totalTime)) totalTime = _a.aInfo[5];
        }
        var percent = Math.ceil(curTime / totalTime * 100);
        percent = Math.min(100, Math.max(0, percent));
        _a.command('play_progress', {
            p: percent
        });
        if (!_a.pause) _a.updateTime(curTime, totalTime);
    },
    play_finish: function () {
        var _a = audio_player;
        $('.audioPlayer').hide();
        if (_a.loop) {
            if (_a.is_html5) _a.player.play();
            else _a.player.playAudio(0);
        } else if (!_a.loop && _a.shuffle) {
            var i = Math.floor(Math.random() * _a.playlist.data.length);
            _a.playToPlayList(i);
        } else _a.nextTrack();
    },
    on_error: function (e) {
        Box.Show('error', 400, 'Ошибка', '<div style="padding: 15px;" dir="auto">При загрузке аудиозаписи произошла ошибка, обновите страницу и попробуйте снова.</div>', 'Закрыть');
    },
    errorPL: function () {
        Box.Show('error', 400, 'Ошибка', '<div style="padding: 15px;" dir="auto">Идет загрузка плейлиста, попробуйте чуть позже..</div>', 'Закрыть');
    },
    end_load: function () {
    },
    load_progress: function (bufferedTime, totalTime) {
        var _a = audio_player;
        if (_a.is_html5) {
            totalTime = Math.floor(_a.player.duration * 1000) / 1000;
            try {
                bufferedTime = (Math.floor(_a.player.buffered.end(0) * 1000) / 1000) || 0;
            } catch (e) {}
        }
        var percent = (bufferedTime / totalTime) * 100;
        _a.command('load_progress', {
            p: percent
        });
    },
    progressDown: function (e1, id) {
        var _a = audio_player,
            el = typeof id == 'string' ? _a.players[id].prbl : id,
            left = $(el).offset().left,
            w = $(el).width(),
            percent;
        function Move(e) {
            e.preventDefault();
            var l = Math.min(Math.max(0, e.pageX - left - 1), w),
                p = (l / w) * 100;
            percent = p;
            for (var i in _a.players) $(_a.players[i].pr).css('width', p + '%');
            $('#player' + _a.fullID + ' #playerPlayLine').css('width', p + '%');
        }
        function Up(ev) {
            cancelEvent(ev);
            $(window).unbind('mousemove', Move).unbind('mouseup', Up);
            var time = (_a.time * percent) / 100;
            _a.setTime(time);
            _a.pr_click = false;
            if (typeof id == 'string') $(_a.players[id].slider).hide();
        }
        _a.pr_click = true;
        Move(e1);
        if (typeof id == 'string') $(_a.players[id].slider).show();
        $(window).bind('mousemove', Move).bind('mouseup', Up);
    },
    progressMove: function (e, id) {
        var _a = audio_player,
            el = _a.players[id].prbl,
            left = $(el).offset().left,
            w = $(el).width(),
            l = Math.min(Math.max(0, e.pageX - left - 1), w),
            p = (l / w) * 100,
            time = (_a.time * p) / 100;
        $(_a.players[id].timeBl).css('left', p + '%').show();
        var s = parseInt(time % 60),
            m = parseInt((time / 60) % 60);
        if (s < 10) s = '0' + s;
        $(_a.players[id].timeBl).children('.audioTAP_strlka').html(m + ':' + s);
    },
    setTime: function (time) {
        var _a = audio_player;
        if (_a.is_html5) {
            _a.player.currentTime = time;
            if (!_a.pause) _a.player.play();
        } else {
            _a.player.playAudio(time);
            if (_a.pause) _a.player.pauseAudio();
        }
    },
    updateTime: function (cur, len) {
        var _a = audio_player;
        if (_a.preloadUrl) return;
        _a.curTime = cur;
        var cur_time = _a.timeDir ? cur : (len - cur);
        var s = parseInt(cur_time % 60),
            m = parseInt((cur_time / 60) % 60);
        if (s < 10) s = '0' + s;
        var resTime = (_a.timeDir ? '' : '-') + m + ':' + s;
        if (parseFloat(resTime) != 'NaN') _a.command('update_time', {
            time: resTime
        });
    },
    backTime: function (id, time) {
        var _a = audio_player,
            s = parseInt(time % 60),
            m = parseInt((time / 60) % 60);
        if (parseFloat(m) != 'NaN' || parseFloat(s) != 'NaN') $('#audio_time_' + id + ', #audio_time_' + _a.fullID + '_pad').html(m + ':' + s);
    },
    compilePlayList: function (type) {
        var _a = audio_player;
        _a.curPL = type;
        if (type) {
            _a.startLoadPL = true;
            if (type == 'search') {
                var start = false,
                    cnt = 0,
                    res = [];
                if ($('#audios_res .audio').length) {
                    $('#audios_res .audio').each(function () {
                        var aid = $(this).attr('id').replace('audio_', ''),
                            adata = aid.split('_');
                        if (cnt < 60) {
                            if (adata[0] == _a.aID) _a.currentPos = cnt;
                            var url = $('#audio_url_' + aid).val().split(',');
                            var inf = [adata[1], adata[0], url[0], $('#audio_' + aid + ' #artist').html(), $('#audio_' + aid + ' #name').html(), url[1], $('#audio_time_' + aid).text(), 'search', url[2]];
                            res.push(inf);
                            cnt++
                        } else return;
                    });
                } else {
                    $('#page .audioPage').each(function () {
                        var aid = $(this).attr('id').replace('audio_', ''),
                            adata = aid.split('_');
                        if (cnt < 60) {
                            if (adata[0] == _a.aID) _a.currentPos = cnt;
                            var url = $('#audio_url_' + aid).val().split(',');
                            var inf = [adata[1], adata[0], url[0], $('#audio_' + aid + ' #artist').html(), $('#audio_' + aid + ' #name').html(), url[1], $('#audio_time_' + aid).text(), 'search', url[2]];
                            res.push(inf);
                            cnt++
                        } else return;
                    });
                }
                _a.playlist = {
                    data: res,
                    name: 'Сейчас играют результаты поиска'
                };
                window.cur.audios = _a.playlist;
                _a.startLoadPL = false;
            } else if (type == 'attach') {
                _a.playlist = {
                    data: [_a.aInfo],
                    name: ''
                };
                _a.currentPos = 0;
                _a.startLoadPL = false;
            } else if (type == 'wall') {
                var res = [],
                    cur = 0,
                    cnt = 0;
                $('#audio_' + _a.fullID).parent().children('.audioPage').each(function () {
                    var aid = this.id.replace('audio_', ''),
                        adata = aid.split('_');
                    if (aid == _a.fullID) {
                        cur = cnt;
                        _a.currentPos = cnt;
                    }
                    cnt++;
                    var url = $('#audio_url_' + aid).val().split(',');
                    res.push([adata[1], adata[0], url[0], $('#audio_' + aid + ' #artist').html(), $('#audio_' + aid + ' #name').html(), url[1], $('#audio_time_' + aid).text(), 'wall', url[2]]);
                });
                _a.startLoadPL = false;
                _a.playlist = {
                    data: res,
                    name: ''
                };
            } else {
                if (!kj.uid) return;
                $.post('/audio?act=load_play_list', {
                    data: _a.fullID
                }, function (d) {
                    d = JSON.parse(d);
                    _a.playlist = {
                        data: d.playList,
                        name: d.pname
                    };
                    var pl = d.playList,
                        cnt = 0;
                    for (var i in pl) {
                        var id = pl[i][1] + '_' + pl[i][0] + (pl[i][7] ? '_' + pl[i][7] : '');
                        if (id == _a.fullID) {
                            _a.currentPos = cnt;
                        }
                        cnt++;
                    }
                    _a.startLoadPL = false;
                });
            }
        }
    },
    nextTrack: function () {
        var _a = audio_player;
        if (_a.startLoadPL) {
            _a.errorPL();
            return;
        }
        var nid = _a.currentPos + 1;
        if (!_a.playlist.data[nid]) nid = 0;
        _a.playToPlayList(nid);
    },
    prevTrack: function () {
        var _a = audio_player;
        if (_a.startLoadPL) {
            _a.errorPL();
            return;
        }
        var nid = _a.currentPos - 1;
        if (!_a.playlist.data[nid]) nid = _a.playlist.data.length - 1;
        _a.playToPlayList(nid);
    },
    playToPlayList: function (i) {
        var _a = audio_player;
        if (_a.fullID) {
            $('#audio_' + _a.fullID + ', #audio_' + _a.fullID + '_pad').removeClass('play').removeClass('pause').removeClass('preactiv');
            _a.backTime(_a.fullID, _a.time);
        }
        $('.audioPlayer').hide();
        _a.currentPos = i;
        _a.aID = _a.playlist.data[i][1];
        _a.aOwner = _a.playlist.data[i][0];
        _a.aType = _a.playlist.data[i][7] ? _a.playlist.data[i][7] : '';
        _a.fullID = _a.aID + '_' + _a.aOwner + (_a.playlist.data[i][7] ? '_' + _a.playlist.data[i][7] : '');
        _a.aInfo = [_a.aOwner, _a.aID, _a.playlist.data[i][2], _a.playlist.data[i][3], _a.playlist.data[i][4], _a.playlist.data[i][5], _a.playlist.data[i][6], _a.aType, _a.playlist.data[i][7]];
        _a.time = _a.playlist.data[i][5];
        $('#audio_' + _a.fullID + ', #audio_' + _a.fullID + '_pad').addClass('play');
        _a.play = true;
        _a.cplay = false;
        _a.command('play', {
            style_only: true
        });
        _a.curTime = 0;
        if (_a.is_html5) {
            _a.player.src = _a.aInfo[2];
            audio_player.player.load();
        } else {
            _a.player.loadAudio(_a.aInfo[2]);
            _a.pause = false;
        }
        _a.command('set_info');
        if (_a.aInfo[8] != 'page' && _a.aInfo[7] != 'wall') _a.scrollToAudio();
        _a.check_add();
    },
    scrollToAudio: function () {
        var _a = audio_player;
        if ($('#audio_' + _a.fullID).length) {
            var top = $('#audio_' + _a.fullID).offset().top,
                h = ($(window).height() / 2),
                r = top - h;
            $('body').animate({
                scrollTop: r
            }, 200);
        }
    },
    volumeDown: function (e1, elem) {
        cancelEvent(e1);
        var _a = audio_player,
            el = elem ? elem : this,
            left = $(el).offset().left,
            w = $(el).width(),
            pbl = $(el).children('.audioTimesAP').get(0),
            pblstr = $(pbl).children('.audioTAP_strlka'),
            vol;
        pbl = $(pbl);
        function Move(e) {
            e.preventDefault();
            var l = Math.min(Math.max(0, e.pageX - left - 1), w),
                p = (l / w) * 100;
            for (var i in _a.players) $(_a.players[i].volume_line).css('width', p + '%');
            $('#player' + _a.fullID + ' #playerVolumeBar').css('width', p + '%');
            var str = Math.round(p);
            vol = p / 100;
            _a.vol = vol;
            if (_a.is_html5) _a.player.volume = vol;
            else _a.player.setVolume(vol);
            var l1 = (p * w) / 100 - (pblstr.width() / 2) - 6 + (elem ? 17 : 0);
            pbl.css('left', l1 + 'px');
            pblstr.html(str + '%');
        }
        function Up(ev) {
            cancelEvent(ev);
            $(window).unbind('mousemove', Move).unbind('mouseup', Up);
            pbl.hide();
            var d = new Date(),
                date = d.getDate() + 5,
                month = d.getMonth(),
                year = d.getFullYear();
            set_cookie('audioVol', vol, year, month, date);
        }
        pbl.show();
        $(window).bind('mousemove', Move).bind('mouseup', Up);
        Move(e1);
    },
    playerPrMove: function (e, el) {
        var _a = audio_player;
        _a.mouseoverProgress = true;
        var elem = $(el),
            pos = e.clientX,
            w = elem.width(),
            left = elem.offset().left;
        pos = pos - left;
        var val = (pos / w) * 100;
        var curTime = val / 100 * _a.time,
            prTP = elem.children('.audioTimesAP'),
            prTPtext = prTP.children('.audioTAP_strlka');
        $('.audioTimesAP').hide();
        var s = parseInt(curTime % 60),
            m = parseInt((curTime / 60) % 60);
        if (s < 10) s = '0' + s;
        prTPtext.html(m + ':' + s);
        var left = val / 100 * w;
        prTP.css('left', (left - (prTPtext.width() / 2)) + 'px').show();
    },
    playerPrOut: function () {
        var _a = audio_player;
        _a.mouseoverProgress = false;
        $('.audioTimesAP').hide();
    },
    //pad
    initedPad: false,
    initedMP: false,
    padScroll: false,
    initMP: function () {
        var _a = audio_player;
        if (!_a.initedMP) {
            _a.initedMP = true;
            var content = '<div class="min_player_names"><marquee scrollamount="3"><span id="minPlayerArtist"></span> – <span id="minPlayerName"></span></marquee></div>\
			<div class="cButs"><div class="nextPrevBtn icon-fast-bw" id="no_play" onClick="audio_player.prevTrack();"></div>\
			<div class="playBtn icon-pause" id="no_play" onClick="audio_player.play_pause()"></div>\
			<div class="nextPrevBtn icon-fast-fw" id="no_play" onClick="audio_player.nextTrack();"></div><div class="clear"></div></div>';
            $('#audioMP').html(content).attr('onClick', 'audio_player.showPad(event)');
        }
        $('#audioMP').addClass('show');
        if (_a.aInfo) {
            $('#minPlayerArtist').html(_a.aInfo[3]);
            $('#minPlayerName').html(_a.aInfo[4]);
        }
    },
    showPad: function (e) {
        var _a = audio_player;
        if (e && e.target.id == 'no_play') return;
        if (!_a.initedPad) {
            _a.initedPad = true;
            var content = '<div class="audio_head">\
				<div class="bigPlay_but icon-play-1" id="pad_play"></div>\
				<div class="prevision icon-fast-bw" id="pad_prev"></div>\
				<div class="prevision next icon-fast-fw" id="pad_next"></div>\
				<div class="fl_l" style="width:268px;margin-left: 14px;margin-top: 2px;" id="pad_cont_progress">\
					<div>\
						<div class="names fl_l" id="pad_names"></div>\
						<div class="fl_r time" id="pad_time">0:00</div>\
						<div class="clear"></div>\
					</div>\
					<div class="audio_progres_bl" id="pad_progress_bl">\
						<div class="bg"></div>\
						<div class="play" id="pad_play_line"><div class="slider" id="pad_slider"></div></div>\
						<div class="load" id="pad_load_line"></div>\
						<div class="audioTimesAP" id="pad_time_bl"><div class="audioTAP_strlka">3:00</div></div>\
					</div>\
				</div>\
				<div class="volume_bar" id="pad_volume">\
					<div class="volume_bg"></div>\
					<div class="volume_line" id="pad_volume_line"><div class="slider"></div></div>\
					<div class="audioTimesAP"><div class="audioTAP_strlka">3:00</div></div>\
				</div>\
				<div class="fl_l plcontols_buts">\
					<li class="icon-plus-6" id="pad_add" onmouseover="showTooltip(this, {text: \'Добавить в мой список\', shift:[0,5,0]});"></li>\
					<li class="icon-loop-1" id="pad_loop" onmouseover="showTooltip(this, {text: \'Повторять эту композицию\', shift:[0,5,0]});"></li>\
					<li class="icon-shuffle-2" id="pad_shuffle" onmouseover="showTooltip(this, {text: \'Случайный порядок\', shift:[0,5,0]});"></li>\
					<div class="clear"></div>\
				</div>\
			</div><div style="position:relative;"><div class="rightStrelka"></div></div>\
			<div class="audios_scroll_bl" id="pad_scroll">\
				<div id="audios_scroll_cont" class="scroller_cont"><div id="audioPadRes"></div></div>\
			</div>\
			<div class="padFooter">\
				<div class="plName fl_l"></div>\
				<div class="button_div fl_r" style="margin-right: 5px;margin-top: 2px;"><button onClick="audio_player.showPad();">Закрыть</button></div>\
				</div><div></div><div></div></div>\
				<div class="clear"></div>\
			</div>';
            $('#audioPad').html(content);

            var data = {
                id: 'pad',
                play_but: $('#pad_play').get(0),
                names: $('#pad_names').get(0),
                pr: $('#pad_play_line').get(0),
                load: $('#pad_load_line').get(0),
                prbl: $('#pad_progress_bl').get(0),
                slider: $('#pad_slider').get(0),
                timeBl: $('#pad_time_bl').get(0),
                time: $('#pad_time').get(0),
                prev: $('#pad_prev').get(0),
                next: $('#pad_next').get(0),
                volume: $('#pad_volume').get(0),
                volume_line: $('#pad_volume_line').get(0),
                loop: $('#pad_loop').get(0),
                shuffle: $('#pad_shuffle').get(0),
                add: $('#pad_add').get(0)
            };
            _a.addPlayer(data);
            _a.padScroll = new Scroller('pad_scroll');
            var _s = _a;
        }
        if ($('#audioPad').hasClass('show')) {
            $('#audioPad').css('top', -($('#audioPad').height() + 70) + 'px').removeClass('show');
            $('#audioMP').removeClass('active');
            $('#audioPadRes').html('');
        } else {
            $('#audioPad').css('margin-left', 'auto').css('top', '50px').addClass('show');
            $('#audioMP').addClass('active');
            if (_a.aType == 'wall' || _a.aType == 'search' || _a.aType == 'attach') {
                var pl = _a.playlist.data,
                    res = '';
                for (var i = 0; i < pl.length; i++) res += _a.compile_audio(pl[i]);
                $('#audioPadRes').html(res);
                $('.plName').html(_a.playlist.name);
                if (_a.pause) $('#audio_' + _a.fullID + ', #audio_' + _a.fullID + '_pad').addClass('preactiv');
                else _a.command('play', {
                    style_only: true
                });
                if (_a.play) $('#audio_' + _a.fullID + ', #audio_' + _a.fullID + '_pad').removeClass('preactiv');
                if ($('#pad_add').css('display') == 'none') $('#pad_cont_progress').css('width', '268px');
                else $('#pad_cont_progress').css('width', '245px');
                _a.padScroll.check_scroll();
            } else {
                $.post('/audio?act=load_play_list', {
                    data: _a.fullID
                }, function (d) {
                    d = JSON.parse(d);
                    _a.playlist = {
                        data: d.playList,
                        name: d.pname
                    };
                    var pl = d.playList,
                        cnt = 0,
                        res = '';
                    for (var i in pl) {
                        var id = pl[i][1] + '_' + pl[i][0] + (pl[i][7] ? '_' + pl[i][7] : '');
                        if (id == _a.fullID) _a.currentPos = cnt;
                        if (cnt > 50) break;
                        cnt++;
                        res += _a.compile_audio(pl[i]);
                    }
                    if (pl.length > 50) res += '<div onclick="audio_player.show_more(' + cnt + '); return false;" class="public_wall_all_comm" id="audio_show_more">Показать больше аудиозаписей</div>';
                    _a.startLoadPL = false;
                    $('#audioPadRes').html(res);
                    $('.plName').html(_a.playlist.name);
                    if (_a.pause) $('#audio_' + _a.fullID + ', #audio_' + _a.fullID + '_pad').addClass('preactiv');
                    else _a.command('play', {
                        style_only: true
                    });
                    if (_a.play) $('#audio_' + _a.fullID + ', #audio_' + _a.fullID + '_pad').removeClass('preactiv');
                    if ($('#pad_add').css('display') == 'none') $('#pad_cont_progress').css('width', '268px');
                    else $('#pad_cont_progress').css('width', '245px');
                    _a.padScroll.check_scroll();
                });
            }
        }
        _a.padScroll.check_scroll();
    },
    show_more: function (offset) {
        $('#audio_show_more').remove();
        var _a = audio_player;
        var pl = _a.playlist.data,
            cnt = 0,
            num = 0,
            res = '';
        for (var i in pl) {
            cnt++;
            if (cnt > offset && num < 51) {
                num++;
                res += _a.compile_audio(pl[i]);
            }
        }
        var q = parseInt(num) + parseInt(offset);
        if (_a.playlist.data.length > q) res += '<div onclick="audio_player.show_more(' + q + '); return false;" class="public_wall_all_comm" id="audio_show_more">Показать больше аудиозаписей</div>';
        $('#audioPadRes').append(res);
        _a.padScroll.check_scroll();
    },
    compile_audio: function (d) {

        if (d[1]) {
            var _a = audio_player;
            var full_id = d[1] + '_' + d[0] + '_' + d[7] + '_pad';
            if (d[7] == 'audios' + kj.uid) var add = '',
                hclass = 'no_tools';
            else var add = '<li class="icon-plus-6 ' + hclass + '" onclick="audio.add(\'' + full_id + '\')" id="add_tt_' + full_id + '" onmouseover="titleHtml({text: \'Добавить аудиозапись\', id: this.id, top: 29, left: 12})"></li>',
                hclass = '';
            if (_a.fullID == d[1] + '_' + d[0] + '_' + d[7]) {
                if (_a.play) {
                    hclass += ' play';
                    _a.command('play', {
                        style_only: true
                    });
                } else {
                    hclass += ' pause';
                    _a.command('pause', {
                        style_only: true
                    });
                }
            }
            return '<div class="audio ' + hclass + '" id="audio_' + full_id + '" onclick="playNewAudio(\'' + full_id + '\', event);">\
			<div class="audio_cont">\
				<div class="play_btn icon-play-4"></div>\
				<div class="name"><span id="artist">' + d[3] + '</span> – <span id="name">' + d[4] + '</span></div>\
				<div class="fl_r">\
					<div class="time" id="audio_time_' + full_id + '">' + d[6] + '</div>\
					<div class="tools">' + add + '\
						<div class="clear"></div>\
					</div>\
				</div>\
				<input type="hidden" value="' + d[2] + ',' + d[5] + ',pad" id="audio_url_' + full_id + '"/>\
				<div class="clear"></div>\
			</div>\
			<div id="audio_text_res"></div>\
		</div>';
        } else return '';
    },
    added: {},
    check_add: function () {
        var _a = audio_player,
            type = _a.fullID.split('_');
        if (type[2] == 'public') {
            if (!_a.added[_a.aID]) _a.command('show_add', {
                added: false
            });
            else if (_a.added[_a.aID]) _a.command('show_add', {
                added: true
            });
        } else {
            if (_a.aOwner != kj.uid && !_a.added[_a.aID]) _a.command('show_add', {
                added: false
            });
            else if (_a.aOwner != kj.uid && _a.added[_a.aID]) _a.command('show_add', {
                added: true
            });
            else _a.command('hide_add');
        }
    },
    addAudio: function () {
        var _a = audio_player;
        if (_a.added[_a.aID]) return;
        _a.added[_a.aID] = true;
        $('#pad_add, #pl_add').addClass('icon-ok-3').removeClass('icon-plus-6');
        $('#audio_' + _a.fullID + ' .tools, #audio_' + _a.fullID + '_pad .tools').html('<li class="icon-ok-3" style="padding-top: 2px;font-size: 16px;"></li><div class="clear"></div>');
        $.post('/audio?act=add', {
            id: _a.aID
        });
        $('.titleHtml').remove();
    },
    get_text: function (id, el) {
        if (el && !$(el).hasClass('text_avilable')) return;
        var tbl = $('#audio_' + id + ' #audio_text_res');
        if (tbl.hasClass('opened')) tbl.removeClass('opened');
        else {
            tbl.addClass('opened');
            var html = tbl.html();
            if (html.length == 0) {
                tbl.html('<div style="padding:20px 0;text-align:center;"><img src="/images/loading_mini.gif"></div>');
                $.post('/audio?act=get_text', {
                    id: id
                }, function (d) {
                    tbl.html(d);
                });
            }
        }
    }
};

var audio = {
    user_id: 0,
    a_user_fid: 0,
    init: function (d) {
        $.extend(d, {
            play_but: $('#pl_play').get(0),
            names: $('#pl_names').get(0),
            pr: $('#pl_play_line').get(0),
            load: $('#pl_load_line').get(0),
            prbl: $('#pl_progress_bl').get(0),
            slider: $('#pl_slider').get(0),
            timeBl: $('#pl_time_bl').get(0),
            time: $('#pl_time').get(0),
            prev: $('#pl_prev').get(0),
            next: $('#pl_next').get(0),
            volume: $('#pl_volume').get(0),
            volume_line: $('#pl_volume_line').get(0),
            loop: $('#pl_loop').get(0),
            shuffle: $('#pl_shuffle').get(0),
            add: $('#pl_add').get(0)
        });
        audio_player.addPlayer(d);
        audio.load_page = 1;
        $(window).scroll(function () {
            if (!audio.start_load && $(window).scrollTop() + $(window).height() >= $(document).height()) {
                if (audio.moreSaerch) audio.loadMoreSearch();
                else audio.loadMore();
            }
        });
    },
    change_tab: function (type) {
        $('#search_tab2').hide();
        $('#search_preloader').show();
        $('.audio_menu li').removeClass('active');
        $('.menu_item.active').removeClass('active');
        $('#friendBlockMain li').removeClass('audioFrActive');
        $('#' + type).addClass('active');
        var url = '/audio?type=' + type;
        history.pushState({
            link: url
        }, null, url);
        this.load_page = 1;
        this.start_load = false;
        audio.tabType = type;
        $('#search_audio_val').val('');
        this.moreSaerch = false;
        if (type == 'my_music' && kj.uid == audio.user_id && audio.loaded_len > 0) {
            $('#search_preloader').hide();
            var text = kj.uid == audio.user_id ? 'У вас' : 'У ' + audio.uname;
            $('#atitle').html('<div class="audio_page_title">' + text + ' ' + declOfNum(audio.loaded_len, ['аудиозапись', 'аудиозаписи', 'аудиозаписей']) + '</div>');
            var len = Math.min(40, audio.loaded_len),
                result = '',
                tpl, res = audio.audiosRes;
            for (var i = 0; i < len; i++) {
                tpl = str_replace(['{id}', '{uid}', '{plname}', '{artist}', '{name}', '{stime}', '{time}', '{url}', '{is_text}'], [res[i][1], res[i][0], res[i][7], res[i][3], res[i][4], res[i][6], res[i][5], res[i][2], res[i][9] ? 'text_avilable' : ''], audio.tpl_audio);
                tpl = tpl.replace(/\[tools\](.*?)\[\/tools\]/gim, kj.uid == audio.user_id ? '$1' : '');
                tpl = tpl.replace(/\[add\](.*?)\[\/add\]/gim, kj.uid == audio.user_id ? '' : '$1');
                result += tpl;
            }
            $('#audios_res').html(result);
            var _a = audio_player;
            _a.playLists = {};
            _a.playLists['audios' + kj.uid] = {
                data: res,
                pname: 'Сейчас играют аудиозаписи ' + audio.uname + ' | ' + declOfNum(audio.loaded_len, 'аудиозапись', 'аудиозаписи', 'аудиозаписей'),
            };
        } else {
            $.post(url, {
                doload: 1
            }, function (d) {
                $('#search_preloader').hide();
                d = JSON.parse(d);
                $('#atitle').html(d.title);
                $('#audios_res').html(d.result);
                $('#load_but').html(d.but);
                var _a = audio_player;
                _a.playLists = {};
                var type = audio.tabType == 'my_music' ? 'audios' + kj.uid : audio.tabType;
                _a.playLists[type] = {
                    data: [],
                    pname: d.pname
                };
                for (var i in d.playList) _a.playLists[type].data.push(d.playList[i]);
                if (_a.pause) $('#audio_' + _a.fullID).addClass('preactiv');
                else _a.command('play', {
                    style_only: true
                });
                audio.loadAll(kj.uid, 0);
            });
        }
        audio.user_id = kj.uid;
        audio.uname = kj.name;
    },
    openFriends: function (uid, fid) {
        $('#search_tab2').hide();
        $('#search_preloader').show();
        $('.audio_menu li').removeClass('active');
        $('.menu_item.active').removeClass('active');
        var url = '/audio' + uid,
            old_uid = this.user_id;
        this.user_id = uid;
        this.a_user_fid = fid;
        history.pushState({
            link: url
        }, null, url);
        this.load_page = 1;
        this.start_load = false;
        this.uname = $('#user_' + fid + ' .audioFriendsBlockName').html();
        $('#search_audio_val').val('');
        this.moreSaerch = false;
        $('#friendBlockMain li').removeClass('audioFrActive');
        $('#friendBlockMain #user_' + fid).addClass('audioFrActive');
        $.post(url, {
            doload: 1
        }, function (d) {
            d = JSON.parse(d);
            $('#search_preloader').hide();
            $('#atitle').html(d.title);
            $('#audios_res').html(d.result);
            $('#load_but').html(d.but);
            var _a = audio_player;
            _a.playLists = {};
            _a.playLists['audios' + uid] = {
                data: [],
                pname: d.pname
            };
            for (var i in d.playList) _a.playLists['audios' + uid].data.push(d.playList[i]);
            if (_a.pause) $('#audio_' + _a.fullID).addClass('preactiv');
            else _a.command('play', {
                style_only: true
            });
            audio.loadAll(audio.user_id, 0);
        });
    },
    load_page: 1,
    start_load: false,
    loadMore: function () {
        if (this.start_load) return;
        this.start_load = true;
        if (this.tabType != 'my_music') return this.moreOther();
        if (!audio.searchResult) audio.searchResult = {
            cnt: audio.loaded_len,
            data: audio.audiosRes
        };
        var offset = audio.load_page * 40,
            len = Math.min(audio.searchResult.cnt, offset + 40),
            result = '',
            res = audio.searchResult.data;
        for (var i = offset; i < len; i++) {
            tpl = str_replace(['{id}', '{uid}', '{plname}', '{artist}', '{name}', '{stime}', '{time}', '{url}', '{is_text}'], [res[i][1], res[i][0], res[i][7], res[i][3], res[i][4], res[i][6], res[i][5], res[i][2], res[i][9] ? 'text_avilable' : ''], audio.tpl_audio);
            tpl = tpl.replace(/\[tools\](.*?)\[\/tools\]/gim, kj.uid == audio.user_id ? '$1' : '');
            tpl = tpl.replace(/\[add\](.*?)\[\/add\]/gim, kj.uid == audio.user_id ? '' : '$1');
            result += tpl;
        }
        $('#audios_res').append(result);
        audio.load_page++;
        if (result) audio.start_load = false;
        else $('#audio_more_but').remove();
    },
    moreOther: function () {
        var but = $('#audio_more_but');
        but.html('<img src="/images/loading_mini.gif">');
        $.post(location.href, {
            doload: 1,
            more: 1,
            page: audio.load_page
        }, function (d) {
            d = JSON.stringify(d);

            audio.load_page++;
            if (d.result) {
                but.html('Показать больше');
                $('#audios_res').append(d.result);
                audio.start_load = false;
            } else but.remove();
        });
    },
    moreSaerch: false,
    search: function (val, pid) {
        if (!pid) pid = 0;
        audio.searchClient(val, pid);
    },
    loadMoreSearch: function () {
        if (this.start_load) return;
        this.start_load = true;
        $('#audio_more_but').html('<img src="/images/loading_mini.gif"/>');
        var q = $('#search_audio_val').val();
        $.post('/audio?act=search_all', {
            doload: 1,
            page: this.load_page,
            q: q,
            more: 1
        }, function (d) {
            audio.load_page++;
            d = JSON.parse(d);
            if (d.search) {
                audio.start_load = false;
                $('#audios_res').append(d.search);
                $('#audio_more_but').html('Показать больше');
                var _a = audio_player,
                    type = _a.aInfo[7];
                for (var i = 0; i < d.audios; i++) {
                    if (type == 'search') cur.audios.data.push(d.audios[i]);
                    _a.playLists['search'].data.push(d.audios[i]);
                }
                _a.playList.data = cur.audios;
            } else $('#audio_more_but').remove();
        });
    },
    edit_box: function (id) {
        Page.Loading('start');
        $('.titleHtml').remove();
        var q = id.split('_');
        aid = q[0];
        $.post('/audio?act=get_info', {
            id: aid
        }, function (d) {
            d = JSON.parse(d);
            Page.Loading('stop');
            if (d.error) addAllErr('Неизвестная ошибка');
            else {
                var content = '<div style="padding: 15px;background: #EEF0F2;">\
				<div class="audioEditDescr">Исполнитель:</div><input type="text" class="audioEditInput" id="audio_artist" value="' + d.artist + '"/><div class="clear"></div>\
				<div class="audioEditDescr">Название:</div><input type="text" class="audioEditInput" id="audio_name" value="' + d.name + '"/><div class="clear"></div>\
				<a href="/" class="audio_edit_more_btn" onClick="audio.showMoreSettings(this); return false;">Дополнительно</a>\
				<div id="audio_edit_more" class="no_display">\
				<div class="audioEditDescr">Жанр:</div><div id="audio_genre" style="width: 281px;" class="kjSelector fl_l"></div><div class="clear"></div><br/>\
				<div class="audioEditDescr">Текст:</div><textarea class="audioEditInput" id="audio_text">' + (d.text ? str_replace(['<br>', '<br />'], ['\n', '\n'], d.text) : '') + '</textarea><div class="clear"></div>\
				</div>\
				<div class="audioEditDescr"> </div><div class="button_div fl_l"><button onClick="audio.save_audio(\'' + id + '\', ' + aid + ')" id="saveabutton">Сохранить</button></div><div class="clear"></div>\
				</div>\
				<style>#audio_genre .kjSelectorTop{padding: 10px 10px}#audio_genre li{padding: 6px 10px}</style>';
                Box.Show('audio_edit', 440, 'Редактирование аудиозаписи', content, 'Закрыть');
                cur.selects = {};
                cur.selects['audio_genre'] = new Selector({
                    id: 'audio_genre',
                    data: d.genres,
                    def: d.genre
                });
            }
        });
    },
    showMoreSettings: function (el) {
        $(el).remove();
        $('#audio_edit_more').show();
    },
    save_audio: function (id, aid) {
        var artist = $('#audio_artist').val(),
            name = $('#audio_name').val(),
            genre = $('#audio_genre').val(),
            text = $('#audio_text').val();
        if (!artist) {
            setErrorInputMsg('audio_artist');
            return;
        }
        if (!name) {
            setErrorInputMsg('audio_name');
            return;
        }
        $('#saveabutton').html('<img src="/images/loading_mini.gif"/>').attr('onClick', '');
        $.post('/audio?act=save_edit', {
            id: aid,
            genre: genre,
            artist: artist,
            name: name,
            text: text
        }, function () {
            Box.Close('audio_edit');
            $('#audio_' + id + ' #artist').html(artist);
            $('#audio_' + id + ' #name').html(name);
        });
    },
    delete_box: function (id, pid) {
        if (!pid) pid = 0;
        $('.titleHtml').remove();
        var aid = id.split('_');
        aid = aid[0];
        Box.Show('del', 400, 'Удаление аудиозаписи', '<div style="padding: 15px">Вы действительно хотите удалить эту аудиозапись?</div>', 'Отмена', 'Да, удалить', 'audio.start_delete(\'' + id + '\', ' + aid + ', ' + pid + ')');
    },
    start_delete: function (id, aid, pid) {
        $('#box_del .button_div_gray').remove();
        $('#box_del .button_div').html('<img src="/images/loading_mini.gif"/>');
        $.post('/audio?act=del_audio', {
            id: aid
        }, function (d) {
            if (d == 'error') addAllErr('Неизвестная ошибка');
            else if (pid) Page.Go('/public/audio' + pid);
            else Page.Go('/audio');
        });
    },
    uploadBox: function (pid) {
        Page.Loading('start');
        if (!pid) type = 'audio';
        else type = '?go=public_audio&pid=' + pid;
        $.post('/' + type, {
            act: 'upload_box'
        }, function (d) {
            Box.Show('upload', 475, 'Добавление новой песни', d, 'Отмена');
            Page.Loading('stop');
            $(document).bind('drop', audio.onDropFile).bind('dragover', audio.dragOver);
            $('.audio_drop_wrap').bind('dragenter', audio.dragOver).bind('dragleave', audio.dragOut);
        });
    },
    onDropFile: function (e) {
        e = e || window.event;
        cancelEvent(e);
        $('.audio_upload_drop').hide();
        $('.chat_tab').show();
        audio.onFile(e.dataTransfer.files);
        return false;
    },
    dragOver: function (e) {
        e = e || window.event;
        $('.audio_upload_drop').show();
        $('.chat_tab').hide();
        cancelEvent(e);
        return false;
    },
    dragOut: function (e) {
        e = e || window.event;
        $('.audio_upload_drop').hide();
        $('.chat_tab').show();
        cancelEvent(e);
        return false;
    },
    audioUploadErrorBox: function (str) {
        Box.Show('err', 450, 'Ошибка', '<div style="padding:15px;line-height:160%;">' + str + '</div>', 'Закрыть');
    },
    onFile: function (e, pid) {
        if (!pid) pid = 0;
        var _a = audio,
            files = e.files,
            len = files.length,
            maxlen = 500;
        if (!len) return;
        var queue = [];
        if (len > maxlen) {
            var err_msg = 'За один раз Вы не можете загрузить более {name}'.replace('{name}', maxlen + ' ' + declOfNum(maxlen, [
                'аудиозапись', 'аудиозаписи', 'аудиозаписей'
            ]));
            _a.audioUploadErrorBox(err_msg);
            e.value = '';
            return;
        }
        for (var i = 0; i < len; i++) {
            var file = files[i];
            var ext = file.name.split('.');
            ext = ext[ext.length - 1];
            if (ext != 'mp3') {
                var err_msg = 'Аудиозапись <b>{name}</b> имеет не верный формат.<br>Выбирите аудиозаписи с форматом MP3!'.replace('{name}', file.name);
                _a.audioUploadErrorBox(err_msg);
                e.value = '';
                return;
            }

            if (file.size > 209715200) {
                var err_msg = 'Аудиозапись <b>{name}</b> превышает максимально допустимый размер.<br>Выбирите аудиозапись размером не более 200 МБ'.replace('{name}', file.name);
                _a.audioUploadErrorBox(err_msg);
                e.value = '';
                return;
            }
            queue.push(file);
        }
        $('#audio_choose_wrap').hide();
        $('.audio_upload_progress').show();
        $('#box_upload #btitle').html('Идет загрузка..');
        $('#box_upload .box_bottom, #box_upload .box_close').remove();
        $('#box_upload').unbind('click');
        _a.upload_queue = queue;
        _a.uploaded_num = 0;
        _a.upload_total = len;
        _a.startUpload(queue[0], pid);
    },
    startUpload: function (file, pid) {
        var _a = audio,
            queue = _a.upload_queue;
        $('#audio_num_download').html('Загруженно {num} из {total}'.replace('{num}', _a.uploaded_num).replace('{total}', _a.upload_total));
        var xhr = new XMLHttpRequest();
        progress = $('.audio_upload_pr_line'),
            progress_str = $('.audio_upload_pr_line .str, #progress_str');
        xhr.upload.addEventListener('progress', function (e) {
            if (e.lengthComputable) {
                var p = (e.loaded / e.total) * 100;
                progress.css('width', p + '%');
                progress_str.html(parseInt(p) + '%');
            }
        });
        xhr.onreadystatechange = function (e) {
            if (e.target.readyState == 4) {
                if (e.target.status == 200) {
                    _a.uploaded_num++;
                    $('#audio_num_download').html('Загруженно {num} из {total}'.replace('{num}', _a.uploaded_num).replace('{total}', _a.upload_total));
                    _a.upload_queue = _a.upload_queue.slice(1);
                    if (_a.upload_queue.length > 0) _a.startUpload(_a.upload_queue[0], pid);
                    else {
                        $('#box_upload #btitle').html('Информация');
                        $('.audio_upload_cont').html('Загрузка завершена..');
                        setTimeout(function () {
                            if (!pid) Page.Go('/audio');
                            else Page.Go('/public/audio' + pid);
                        }, 3000);
                    }
                }
            }
        };
        if (pid) url = '/index.php?go=public_audio&act=upload&pid=' + pid;
        else url = '/audio?act=upload';
        xhr.open('POST', url, true);
        var form = new FormData();
        form.append('file', file);
        xhr.send(form);
    },
    add: function (id) {
        if (!id) id = audio_player.fullID;
        $('.titleHtml').remove();
        var aid = id.split('_');
        aid = aid[0];
        id = id.replace('_pad', '');
        $('#audio_' + id + ' .tools, #audio_' + id + '_pad .tools').html('<li class="icon-ok-3" style="padding-top: 2px;font-size: 16px;"></li><div class="clear"></div>');
        $('#pad_add, #pl_add').addClass('icon-ok-3').removeClass('icon-plus-6');
        $.post('/audio?act=add', {
            id: aid
        });
    },
    pageFriends: 1,
    friend_tpl: '<li id="user_{fid}" onmousedown="{js}.openFriends({uid}, {fid})">\
	<img src="{ava}"/>\
		<div class="fl_l" style="line-height: 130%;margin-left: 5px;">\
			<div class="audioFriendsBlockName">{name}</div>\
			<div class="cnt_music" dir="auto">{count}</div>\
		</div>\
		<div class="clear"></div>\
	</li>',
    compile_friends: function (d) {
        var count = d.count + ' ' + declOfNum(d.count, ['аудиозапись', 'аудиозаписи', 'аудиозаписей']);
        return str_replace(['{fid}', '{uid}', '{name}', '{ava}', '{js}', '{count}'], [d.fid, d.uid, d.name, d.ava, d.js, count], audio.friend_tpl);
    },
    LoadFriends: function () {
        $.post('/audio?act=loadFriends', function (d) {
            d = JSON.parse(d);
            audio.pageFriends = 1;
            var res = '';
            for (var i = 0; i < d.res.length; i++) res += audio.compile_friends(d.res[i]);
            if (d.count > 6) res += '<div class="audioFrLoadBut" id="audioFrMainLoadBut" onClick="audio.nextFriends()">Показать следующие</div>';
            $('#friendBlockMain').html(res);
            audio.cssFr();
        });
    },
    nextFriends: function () {
        $('#audioFrMainLoadBut').html('Ждите, идёт загрузка...').attr('onClick', '');
        var q = $('#mainFrSearch').val();
        $.post('/audio?act=loadFriends', {
            q: q,
            page: audio.pageFriends
        }, function (d) {
            d = JSON.parse(d);
            if (d.reset) audio.pageFriends = 0;
            else audio.pageFriends++;
            var res = '';
            for (var i = 0; i < d.res.length; i++) res += audio.compile_friends(d.res[i]);
            if (d.count > 6) res += '<div class="audioFrLoadBut" id="audioFrMainLoadBut" onClick="audio.nextFriends()">Показать следующие</div>';
            $('#friendBlockMain').html(res);
            audio.cssFr();
        });
    },
    friendSearch: function () {
        removeTimer('mainFrSearch');
        addTimer('mainFrSearch', function () {
            audio.pageFriends = 1;
            var q = $('#mainFrSearch').val();
            $.post('/audio?act=loadFriends', {
                q: q
            }, function (d) {
                d = JSON.parse(d);
                var len = d.res.length;
                if (len > 0) {
                    var res = '';
                    for (var i = 0; i < len; i++) res += audio.compile_friends(d.res[i]);
                    $('#friendBlockMain').html(res);
                    audio.cssFr();
                } else $('#friendBlockMain').html('<div style="color: #666; margin-top: 20px; margin-bottom: 20px;text-align:center;">Ничего не найдено</div>');
                if (len == 8) $('#audioFrMainLoadBut').show();
                else $('#audioFrMainLoadBut').hide();
            });
        }, 300);
    },
    cssFr: function () {
        $('#friendBlockMain #user_' + this.a_user_fid).addClass('audioFrActive');
        $('#audio_content_block').css('min-height', (parseInt($('.fixed_audio_right').height()) + 'px'));
    },
    loaded_len: 0,
    searchResult: 0,
    loadAll: function (uid, page) {
        $.post('/audio' + uid + '?act=load_all', {
            page: page
        }, function (d) {
            d = JSON.stringify(d);;
            page++;
            if (d.loaded == 1) {
                audio.audiosRes = d.res;
                audio.loaded_len = d.res.length;
                audio.searchResult = {
                    data: d.res,
                    cnt: audio.loaded_len
                };
            } else audio.loadAll(uid, page);
            if (audio.loaded_len > 40) {
                $('#load_but').html('<div class="audioLoadBut" style="margin-top:10px" onClick="audio.loadMore()" id="audio_more_but">Показать больше</div>');
            }
        });
    },
    searchClient: function (val, pid) {
        if (val) {
            var cnt = 0,
                a, res = [];
            val = String(val).toLowerCase();
            for (var i = 0; i < audio.loaded_len; i++) {
                a = audio.audiosRes[i];
                if (String(a[3]).toLowerCase().indexOf(val) != -1 || String(a[4]).toLowerCase().indexOf(val) != -1) {
                    res.push(a);
                    cnt++;
                }
            }
            audio.searchResult = {
                data: res,
                cnt: cnt
            };
            audio_player.playLists['audios' + audio.user_id] = {
                pname: 'Сейчас играют аудиозаписи ' + audio.uname + ' | ' + declOfNum(cnt, 'аудиозапись', 'аудиозаписи', 'аудиозаписей'),
                data: res
            };
            $('.audio_menu li').removeClass('active');
            $('#search_tab2').show().addClass('active');
            if (cnt > 0) {
                var len = Math.min(40, cnt),
                    result = '',
                    tpl;
                for (var i = 0; i < len; i++) {
                    tpl = str_replace(['{id}', '{uid}', '{plname}', '{artist}', '{name}', '{stime}', '{time}', '{url}', '{is_text}'], [res[i][1], res[i][0], res[i][7], res[i][3], res[i][4], res[i][6], res[i][5], res[i][2], res[i][9] ? 'text_avilable' : ''], audio.tpl_audio);
                    tpl = tpl.replace(/\[tools\](.*?)\[\/tools\]/gim, kj.uid == audio.user_id ? '$1' : '');
                    tpl = tpl.replace(/\[add\](.*?)\[\/add\]/gim, kj.uid == audio.user_id ? '' : '$1');
                    result += tpl;
                }
                $('#audios_res').html(result);
                if (audio_player.pause) $('#audio_' + audio_player.fullID).addClass('preactiv');
                else audio_player.command('play', {
                    style_only: true
                });
                if (cnt < 15) audio.searchServer(val, pid);
            } else {
                $('#audios_res').html('');
                audio.searchServer(val, pid);
            }
        } else {
            if (audio.tabType == 'publicaudios' + audio.user_id) Page.Go('/public/audio' + audio.user_id);
            else if (kj.uid == audio.user_id) audio.change_tab('my_music');
            else audio.openFriends(audio.user_id, audio.a_user_fid);
            $('#search_preloader').hide();
        }
    },
    searchServer: function (val, pid) {
        removeTimer('search');
        addTimer('search', function () {
            audio.start_load = false;
            $('#search_preloader').show();
            $.post('/audio?act=search_all', {
                q: val,
                pid: pid
            }, function (d) {
                audio.moreSaerch = true;
                d = JSON.parse(d);
                audio_player.playLists['search'] = {
                    pname: 'Сейчас играют результаты поиска',
                    data: d.audios
                };
                $('#atitle').html('<div class="audio_page_title" style="margin: 15px 0;">В поиске найдено ' + d.search_cnt + ' ' + declOfNum(d.search_cnt, ['аудиозапись', 'аудиозаписи', 'аудиозаписей']) + '</div>');
                $('#audios_res').append(d.search);
                if (d.search_cnt > 40) $('#load_but').html('<div class="audioLoadBut" style="margin-top:10px" onClick="audio.loadMoreSearch()" id="audio_more_but">Показать больше</div>');
                else $('#load_but').html('');
                $('#search_preloader').hide();
                if (audio_player.pause) $('#audio_' + audio_player.fullID).addClass('preactiv');
                else audio_player.command('play', {
                    style_only: true
                });
            });
        });
    },
    tpl_audio: '<div class="audio" id="audio_{id}_{uid}_{plname}" onclick="playNewAudio(\'{id}_{uid}_{plname}\', event);">\
		<div class="audio_cont">\
			<div class="play_btn icon-play-4"></div>\
			<div class="name"><span id="artist" onClick="Page.Go(\'/?go=search&query=&type=5&q={artist}\')">{artist}</span> – <span id="name" class="{is_text}" onClick="audio_player.get_text(\'{id}_{uid}_{plname}\', this);">{name}</span></div>\
			<div class="fl_r">\
				<div class="time" id="audio_time_{id}_{uid}_{plname}">{stime}</div>\
				<div class="tools">\
					[tools]<li class="icon-pencil-7" onclick="audio.edit_box(\'{id}_{uid}_{plname}\')" id="edit_tt_{id}_{uid}_{plname}" onmouseover="showTooltip(this, {text: \'Редактировать аудиозапись\', shift:[0,7,0]});"></li>\
					<li class="icon-cancel-3" onclick="audio.delete_box(\'{id}_{uid}_{plname}\')" id="del_tt_{id}_{uid}_{plname}" onmouseover="showTooltip(this, {text: \'Удалить аудиозапись\', shift:[0,5,0]});"></li>[/tools]\
					[add]<li class="icon-plus-6" onclick="audio.add(\'{id}_{uid}_{plname}\')" id="add_tt_{id}_{uid}_{plname}" onmouseover="showTooltip(this, {text: \'Добавить аудиозапись\', shift:[0,7,0]});"></li>[/add]\
					<div class="clear"></div>\
				</div>\
			</div>\
			<input type="hidden" value="{url},{time},user_audios" id="audio_url_{id}_{uid}_{plname}"/>\
			<div class="clear"></div>\
		</div>\
		<div id="audio_text_res"></div>\
	</div>'
};

function download(data, strFileName, strMimeType) {
    var self = window,
        u = "application/octet-stream",
        m = strMimeType || u,
        x = data,
        D = document,
        a = D.createElement("a"),
        z = function (a) {
            return String(a);
        },
        B = self.Blob || self.MozBlob || self.WebKitBlob || z,
        BB = self.MSBlobBuilder || self.WebKitBlobBuilder || self.BlobBuilder,
        fn = strFileName || "download",
        blob,
        b,
        ua,
        fr;
    if (String(this) === "true") {
        x = [x, m];
        m = x[0];
        x = x[1];
    }

    if (String(x).match(/^data\:[\w+\-]+\/[\w+\-]+[,;]/)) {
        return navigator.msSaveBlob ?
            navigator.msSaveBlob(d2b(x), fn) :
            saver(x);
    }
    try {
        blob = x instanceof B ?
            x :
            new B([x], {
                type: m
            });
    } catch (y) {
        if (BB) {
            b = new BB();
            b.append([x]);
            blob = b.getBlob(m);
        }
    }
    function d2b(u) {
        var p = u.split(/[:;,]/),
            t = p[1],
            dec = p[2] == "base64" ? atob : decodeURIComponent,
            bin = dec(p.pop()),
            mx = bin.length,
            i = 0,
            uia = new Uint8Array(mx);
        for (i; i < mx; ++i) uia[i] = bin.charCodeAt(i);
        return new B([uia], {
            type: t
        });
    }
    function saver(url, winMode) {
        if ('download' in a) {
            a.href = url;
            a.setAttribute("download", fn);
            a.innerHTML = "downloading...";
            D.body.appendChild(a);
            setTimeout(function () {
                a.click();
                D.body.removeChild(a);
                if (winMode === true) {
                    setTimeout(function () {
                        self.URL.revokeObjectURL(a.href);
                    }, 250);
                }
            }, 66);
            return true;
        }
        var f = D.createElement("iframe");
        D.body.appendChild(f);
        if (!winMode) {
            url = "data:" + url.replace(/^data:([\w\/\-\+]+)/, u);
        }
        f.src = url;
        setTimeout(function () {
            D.body.removeChild(f);
        }, 333);
    }
    if (navigator.msSaveBlob) {
        return navigator.msSaveBlob(blob, fn);
    }
    if (self.URL) {
        saver(self.URL.createObjectURL(blob), true);
    } else {
        if (typeof blob === "string" || blob.constructor === z) {
            try {
                return saver("data:" + m + ";base64," + self.btoa(blob));
            } catch (y) {
                return saver("data:" + m + "," + encodeURIComponent(blob));
            }
        }
        fr = new FileReader();
        fr.onload = function (e) {
            saver(this.result);
        };
        fr.readAsDataURL(blob);
    }
    return true;
}