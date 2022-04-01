<form method="POST" action="" name="entryform">
    <div class="note_add_bg border_radius_5">
        <div class="videos_text">Заголовок</div>
        <input type="text" class="videos_input" style="width:767px" maxlength="140" id="title_n" value="{title}"/>
        <div class="input_hr"></div>
        <div class="videos_text">Текст</div>
        <div class="wysiwyg_bbpanel">
            <div onClick="setBold()" class="wysiwyg_icbold cursor_pointer border_radius_3"
                 onMouseOver="myhtml.title('1', 'Жирный', 'bb_bold_', '0')" id="bb_bold_1"></div>
            <div onClick="setItal()" class="wysiwyg_ici cursor_pointer border_radius_3"
                 onMouseOver="myhtml.title('1', 'Курсивный', 'bb_i_', '0')" id="bb_i_1"></div>
            <div onClick="setUnder()" class="wysiwyg_icunderline cursor_pointer border_radius_3"
                 onMouseOver="myhtml.title('1', 'Подчеркнутый', 'bb_underline_', '0')" id="bb_underline_1"></div>
            <div onClick="setLeft()" class="wysiwyg_icpleft cursor_pointer border_radius_3"
                 onMouseOver="myhtml.title('1', 'Выровнять по левому краю', 'bb_pleft_', '0')" id="bb_pleft_1"></div>
            <div onClick="setCenter()" class="wysiwyg_icpcenter cursor_pointer border_radius_3"
                 onMouseOver="myhtml.title('1', 'Выровнять по центру', 'bb_pcenter_', '0')" id="bb_pcenter_1"></div>
            <div onClick="setRight()" class="wysiwyg_icpright cursor_pointer border_radius_3"
                 onMouseOver="myhtml.title('1', 'Выровнять по правому краю', 'bb_pright_', '0')" id="bb_pright_1"></div>
            <div onClick="execCommandImitation('<blockquote>', '</blockquote><p>&nbsp;</p>')"
                 class="wysiwyg_icquote cursor_pointer border_radius_3"
                 onMouseOver="myhtml.title('1', 'Добавить цитату', 'bb_quote_', '0')" id="bb_quote_1"></div>
            <div class="wysiwyg_icphoto cursor_pointer border_radius_3" onClick="wall.attach_addphoto(false, false, 1)"
                 onMouseOver="myhtml.title('1', 'Добавить фотографию', 'bb_photo_', '0')" id="bb_photo_1"></div>
            <div class="wysiwyg_icvideo cursor_pointer border_radius_3" onClick="wall.attach_addvideo(false, false, 1)"
                 onMouseOver="myhtml.title('1', 'Добавить видеозапись', 'bb_video_', '0')" id="bb_video_1"></div>
            <div class="wysiwyg_iclink cursor_pointer border_radius_3" onClick="wysiwyg.linkBox()"
                 onMouseOver="myhtml.title('1', 'Добавить ссылку', 'bb_link_', '0')" id="bb_link_1"></div>
            <div class="clear"></div>
        </div>
        <iframe frameborder="no" src="#" id="text" name="text" class="videos_input wysiwyg_inpt"
                style="margin:0px;padding:0px;width:778px"></iframe>
        <div class="input_hr"></div>
        <div class="clear margin_top_10"></div>
        <div class="button_div fl_l">
            <button onClick="notes.editsave({note-id}); return false" id="notes_sending">Сохранить изменения</button>
        </div>
        <div class="clear"></div>
    </div>
</form>
<script type="text/javascript">
    var isGecko = navigator.userAgent.toLowerCase().indexOf("gecko") != -1;
    var iframe = (isGecko) ? document.getElementById("text") : frames["text"];
    var iWin = (isGecko) ? iframe.contentWindow : iframe.window;
    var iDoc = (isGecko) ? iframe.contentDocument : iframe.document;

    iHTML = "<html><head>\n";
    iHTML += "<style>\n";
    iHTML += "body, div, p, td{font-size:11px;font-family:tahoma;margin:0px;padding:0px}";
    iHTML += "body {margin:5px}";
    iHTML += "blockquote{padding:10px;background:#f5f5f5;border-left:10px solid #4274a4;margin:0px}";
    iHTML += "</style>\n";
    iHTML += "</html>";

    iDoc.open();
    iDoc.write(iHTML);
    iDoc.close();
    iDoc.body.innerHTML = "{text}";
    iWin.focus();

    if (!iDoc.designMode) alert("Визуальный режим редактирования не поддерживается Вашим браузером");
    else iDoc.designMode = (isGecko) ? "on" : "On";

    function setBold() {
        iWin.focus();
        iWin.document.execCommand("bold", null, "");
    }

    function setItal() {
        iWin.focus();
        iWin.document.execCommand("italic", null, "");
    }

    function setUnder() {
        iWin.focus();
        iWin.document.execCommand("underline", null, "");
    }

    function setLeft() {
        iWin.focus();
        iWin.document.execCommand("JustifyLeft", null, "");
    }

    function setRight() {
        iWin.focus();
        iWin.document.execCommand("JustifyRight", null, "");
    }

    function setCenter() {
        iWin.focus();
        iWin.document.execCommand("JustifyCenter", null, "");
    }

    function setHTML(start) {
        iWin.document.execCommand("insertHTML", null, start);
        iWin.focus();
    }

    function nodeList(parentNode, list, level) {
        var i, node, count;
        if (!list) list = new Array();
        level++;
        for (i = 0; i < parentNode.childNodes.length; i++) {
            node = parentNode.childNodes[i];
            if (node.nodeType != 1) continue;
            count = list.length;
            list[count] = new Array();
            list[count][0] = node;
            list[count][1] = level;
            nodeList(node, list, level);
        }
        return list;
    }

    function rgbNormal(color) {
        color = color.toString();
        var re = /rgb\((.*?)\)/i;
        if (re.test(color)) {
            compose = RegExp.$1.split(",");
            var hex = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F'];
            var result = "#";
            for (var i = 0; i < compose.length; i++) {
                rgb = parseInt(compose[i]);
                result += hex[parseInt(rgb / 16)] + hex[rgb % 16];
            }
            return result;
        } else
            return color;
    }

    function execCommandImitation(start, end) {
        var text = iDoc.body.innerHTML;
        if (text == '<br>') setHTML('<blockquote>Текст цитаты</blockquote><p>&nbsp;</p>');

        iDoc.execCommand("ForeColor", false, "#000009");

        var allNodes = nodeList(iDoc.body, false, 0);
        var maxLevel = 0;
        for (i = 0; i < allNodes.length; i++) {
            maxLevel = allNodes[i][1] > maxLevel ? allNodes[i][1] : maxLevel;
        }

        var node, newnode, color, parent;
        for (j = maxLevel; j >= 1; j--) {
            for (i = 0; i < allNodes.length; i++) {
                if (allNodes[i][1] != j) continue;
                node = allNodes[i][0];
                sname = node.nodeName.toLowerCase();
                color = node.color ? rgbNormal(node.color) : rgbNormal(node.style.color);
                if (color) color = color.toLowerCase();
                if (sname == "font" || sname == "span" && color == "#000009") {
                    try {
                        node.innerHTML = start + node.innerHTML + end;
                    } catch (e) {}
                    parent = node.parentNode;
                    while (node.childNodes.length > 0) parent.insertBefore(node.firstChild, node);
                    parent.removeChild(node);
                }
            }
        }
        iWin.focus();
    }
</script>