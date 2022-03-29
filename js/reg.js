/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

//REG
var reg = {
    rules: function(){
        Box.Page('/register/rules', '', 'reg1', 600, 'Пользовательское соглашение', 'Отмена', 'Далее', 'reg.step2()', 350, 1, 1, 1, 0, 0);
    },
    step1: function () {
        var name = $('#name').val();
        var lastname = $('#lastname').val();
        if (name != 0) {
            if (isValidName(name)) {
                if (lastname != 0) {
                    if (isValidName(lastname)) {
                        $('#step1').hide();
                        $('#step2').show();
                        $('#reg_lnk').attr('onClick', '');
                    } else {
                        setErrorInputMsg('lastname');
                        $('#err').show().html(lang_nosymbol);
                    }
                } else {
                    setErrorInputMsg('lastname');
                    $('#err').show().html(lang_empty);
                }
            } else {
                setErrorInputMsg('name');
                $('#err').show().html(lang_nosymbol);
            }
        } else {
            setErrorInputMsg('name');
            $('#err').show().html(lang_empty);
        }
    },
    step2: function(){
        var letsok = $('#letsok:checked').length;
        if(letsok){
            Box.Page('/register/step2', '', 'reg2', 500, 'Моментальная регистрация', 'Отмена', 'Зарегистрироваться', 'reg.step3()', 350, 0, 0, 0, 0, 0);
        } else
            Box.Info('reg_err', 'Ошибка регистрации', 'Вы не приняли пользовательское соглашение.');
    },
    step3: function(){
        var reg_email = $('#reg_email').val();
        var reg_sec_code = $('#reg_sec_code').val();
        if(isValidEmailAddress(reg_email)){
            if(reg_sec_code.length == 5){
                butloading('box_butt_create', '105', 'disabled', '');
                $.ajax({
                    type: "POST",
                    url: "/register/step3",
                    data: {email: reg_email, sec_code: reg_sec_code},
                    success: function(d){
                        butloading('box_butt_create', '105', 'enabled', 'Зарегистрироваться');
                        if(d == 1){
                            $('#err_reg_2').html('Код безопасности не соответствует отображённому').show();
                            setErrorInputMsg('reg_sec_code');
                        } else if(d == 2){
                            $('#err_reg_2').html('Неправильный email адрес').show();
                            setErrorInputMsg('reg_email');
                        } else if(d == 3){
                            $('#err_reg_2').html('Пользователь с таким E-Mail адресом уже зарегистрирован.').show();
                            setErrorInputMsg('reg_email');
                        } else if(d == 4){
                            $('#err_reg_2').html('Регистрация E-mail адресов в этой зоне запрещена.').show();
                            setErrorInputMsg('reg_email');
                        } else if(d){
                            window.location.href = d;
                        } else {
                            $('#reg_box_step2, #box_but').hide();
                            $('#ok_reg').show();
                            $('.box_conetnt').css('height', '225px');
                            $('#box_but_close').text('Закрыть');
                        }
                    }
                });

            } else {
                $('#err_reg_2').html('Код безопасности не соответствует отображённому').show();
                setErrorInputMsg('reg_sec_code');
            }
        } else {
            $('#err_reg_2').html('Неправильный email адрес').show();
            setErrorInputMsg('reg_email');
        }
    },
    finish: function () {
        var email = $('#email').val();
        var new_pass = $('#new_pass').val();
        var new_pass2 = $('#new_pass2').val();
        var rndval = new Date().getTime();
        if (email != 0 && isValidEmailAddress(email)) {
            if (new_pass != 0 && new_pass.length >= 6) {
                if (new_pass == new_pass2) {
                    Box.Show('sec_code', 280, 'Введите код с картинки:', '<div style="padding:20px;text-align:center"><div class="cursor_pointer" onClick="updateCode(); return false"><div id="sec_code"><img src="/security/img?rndval=' + rndval + '" alt="" title="Показать другой код" width="120" height="50" /></div></div><div id="code_loading"><input type="text" id="val_sec_code" class="inpst" maxlength="6" style="margin-top:10px;width:110px" /></div></div>', lang_box_cancel, 'Отправить', 'checkCode(); return false;');
                    $('#val_sec_code').focus();
                } else {
                    setErrorInputMsg('new_pass2');
                    $('#err2').show().html('Оба введенных пароля должны быть идентичны.');
                }
            } else {
                setErrorInputMsg('new_pass');
                $('#err2').show().html('Длина пароля должна быть не менее 6 символов.');
            }
        } else {
            setErrorInputMsg('email');
            $('#err2').show().html(lang_bad_email);
        }
    },
    end: function(h){
        var reg_name = $('#reg_name').val();
        var reg_lastname = $('#reg_lastname').val();
        var reg_pass1 = $('#reg_pass1').val();
        var reg_pass2 = $('#reg_pass2').val();
        if(isValidName(reg_name) && reg_name.length >= 2){
            if(isValidName(reg_lastname)){
                if(reg_pass1 != 0 && reg_pass1.length >= 6){
                    if(reg_pass1 == reg_pass2){
                        $('#err_reg_3').hide();
                        butloading('reg_fini', '140', 'disabled', '');
                        $.post('/register/finish', {reg_name: reg_name, reg_lastname: reg_lastname, reg_pass1: reg_pass1, reg_pass2: reg_pass2, hash: h}, function(d){
                            if(d == 1){
                                $('#err_reg_3').html('Эта ссылка на регистрацию устарела. Пройдите процесс получения ссылки еще раз.').show();
                            } else if(d == 2){
                                $('#err_reg_3').html('Ошибка регистрации.').show();
                            } else {
                                window.location.href = '/editmypage';
                            }
                        });
                    } else {
                        $('#err_reg_3').html('Оба введенных пароля должны быть идентичны.').show();
                        setErrorInputMsg('reg_pass2');
                    }
                } else {
                    $('#err_reg_3').html('Длина пароля должна быть не менее 6 символов.').show();
                    setErrorInputMsg('reg_pass1');
                }
            } else {
                $('#err_reg_3').html(lang_nosymbol).show();
                setErrorInputMsg('reg_lastname');
            }
        } else {
            $('#err_reg_3').html(lang_nosymbol).show();
            setErrorInputMsg('reg_name');
        }
    },
    send: function (sec_code) {
        var email = $('#email').val();
        var new_pass = $('#new_pass').val();
        var new_pass2 = $('#new_pass2').val();
        var name = $('#name').val();
        var lastname = $('#lastname').val();
        var val_sec_code = $("#val_sec_code").val();
        var sex = $("#sex").val();
        var day = $("#day").val();
        var month = $("#month").val();
        var year = $("#year").val();
        var country = $("#country").val();
        var city = $("#select_city").val();
        $.post('/register/send', {
            name: name,
            lastname: lastname,
            email: email,
            sex: sex,
            day: day,
            month: month,
            year: year,
            country: country,
            city: city,
            password_first: new_pass,
            password_second: new_pass2,
            sec_code: sec_code
        }, function (d) {
            if (d.status == 1) {
                window.location = '/u' + d.user_id + 'after';
            } else if (d.status == 4) {
                $('#err2').show().html('Пользователь с таким E-Mail адресом уже зарегистрирован.');
                Box.Close('sec_code');
            } else if (d.status == 9) {
                $('#err2').show().html('Не верные данные.');
                Box.Close('sec_code');
            } else if (d.status == 9) {
                $('#err2').show().html('Ошибка доступа.');
                Box.Close('sec_code');
            } else {
                Box.Info('boxerr', 'Ошибка', 'Неизвестная ошибка', 300);
                Box.Close('sec_code');
            }
        });
    },
    box: function () {
        $('.js_titleRemove').remove();
        $.post('/login', function (d) {
            Box.Show('login_box', 400, 'Войти', d, lang_box_cancel);
        });
    },
    finish: function(h){
        var reg_name = $('#reg_name').val();
        var reg_lastname = $('#reg_lastname').val();
        var reg_pass1 = $('#reg_pass1').val();
        var reg_pass2 = $('#reg_pass2').val();
        if(isValidName(reg_name) && reg_name.length >= 2){
            if(isValidName(reg_lastname)){
                if(reg_pass1 != 0 && reg_pass1.length >= 6){
                    if(reg_pass1 == reg_pass2){
                        $('#err_reg_3').hide();
                        butloading('reg_fini', '140', 'disabled', '');
                        $.post('/index.php?go=register&act=finish', {reg_name: reg_name, reg_lastname: reg_lastname, reg_pass1: reg_pass1, reg_pass2: reg_pass2, hash: h}, function(d){
                            if(d == 1){
                                $('#err_reg_3').html('Эта ссылка на регистрацию устарела. Пройдите процесс получения ссылки еще раз.').show();
                            } else if(d == 2){
                                $('#err_reg_3').html('Ошибка регистрации.').show();
                            } else {
                                window.location.href = '/editmypage';
                            }
                        });
                    } else {
                        $('#err_reg_3').html('Оба введенных пароля должны быть идентичны.').show();
                        setErrorInputMsg('reg_pass2');
                    }
                } else {
                    $('#err_reg_3').html('Длина пароля должна быть не менее 6 символов.').show();
                    setErrorInputMsg('reg_pass1');
                }
            } else {
                $('#err_reg_3').html(lang_nosymbol).show();
                setErrorInputMsg('reg_lastname');
            }
        } else {
            $('#err_reg_3').html(lang_nosymbol).show();
            setErrorInputMsg('reg_name');
        }
    }
}
//RESTORE
var restore = {
    next: function () {
        var email = $('#email').val();
        if (email != 0 && email != 'Ваш электронный адрес' && isValidEmailAddress(email)) {
            butloading('send', '32', 'disabled', '');
            $.post('/restore/next', {email: email}, function (data) {
                if (data.status == 8) {
                    $('#err').show().html('Пользователь <b>' + email + '</b> не найден.<br />Пожалуйста, убедитесь, что правильно ввели e-mail.');
                } else if (data.status == 0) {
                    $('#err').show().html('Неизвестная ошибка.');
                } else {
                    $('#step1').hide();
                    $('#step2').show();
                    $('#c_src').attr('src', data.user_photo);
                    $('#c_name').html('<b>' + data.user_name + '</b>');
                }
            });
            butloading('send', '32', 'enabled', 'Далее');
        } else
            setErrorInputMsg('email');
    },
    send: function () {
        var email = $('#email').val();
        butloading('send2', '129', 'disabled', '');
        $.post('/restore/send', {email: email}, function (d) {
            $('#step2').hide();
            $('#step3').show();
        });
    },
    finish: function () {
        var new_pass = $('#new_pass').val();
        var new_pass2 = $('#new_pass2').val();
        var hash = $('#hash').val();
        if (new_pass != 0 && new_pass != 'Новый пароль') {
            if (new_pass2 != 0 && new_pass2 != 'Повторите еще раз новый пароль') {
                if (new_pass == new_pass2) {
                    if (new_pass.length >= 6) {
                        $('#err').hide();
                        butloading('send', '43', 'disabled', '');
                        $.post('/restore/finish', {
                            new_pass: new_pass,
                            new_pass2: new_pass2,
                            hash: hash
                        }, function (d) {
                            $('#step1').hide();
                            $('#step2').show();
                        });
                    } else
                        $('#err').show().html('Длина пароля должна быть не менее 6 символов.');
                } else
                    $('#err').show().html('Оба введенных пароля должны быть идентичны.');
            } else
                setErrorInputMsg('new_pass2');
        } else
            setErrorInputMsg('new_pass');
    }
}

function isValidName(xname) {
    var pattern = new RegExp(/^[a-zA-Zа-яА-Я]+$/);
    return pattern.test(xname);
}

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
    return pattern.test(emailAddress);
}

function updateCode() {
    var rndval = new Date().getTime();
    $('#sec_code').html('<img src="/security/img?rndval=' + rndval + '" alt="" title="Показать другой код" width="120" height="50" />');
}

function checkCode() {
    var val_sec_code = $("#val_sec_code").val();
    $('#code_loading').html('<img src="' + template_dir + '/images/loading_mini.gif" style="margin-top:21px" />');
    $.get('/security/code?user_code=' + val_sec_code, function (data) {
        if (data.status == '1') {
            reg.send(val_sec_code);
        } else {
            updateCode();
            $('#code_loading').html('<input type="text" id="val_sec_code" class="inpst" maxlength="6" style="margin-top:10px;width:110px" />');
            $('#val_sec_code').val('');
            $('#val_sec_code').focus();
        }
    });
}