var invite = {
	start: function(){
		var inv_email = $('#inv_email').val();
		var inv_name = $('#inv_name').val();
		var inv_lastname = $('#inv_lastname').val();
		var inv_sec_code = $('#inv_sec_code').val();
		if(isValidName(inv_name) && inv_name.length >= 2){
		if(isValidName(inv_lastname)){
		if(isValidEmailAddress(inv_email)){
			if(inv_sec_code.length == 5){
				butloading('inv_butt', '105', 'disabled', '');
				$.ajax({
					type: "POST",
					url: "/index.php?go=invite&act=start",
					data: {email: inv_email, name: inv_name, lastname: inv_lastname, sec_code: inv_sec_code},
					success: function(d){
						butloading('inv_butt', '105', 'enabled', 'Выслать приглашение');
						if(d == 1){
							$('#err_inv').html('Код безопасности не соответствует отображённому').show();
							setErrorInputMsg('inv_sec_code');
						} else if(d == 2){
							$('#err_inv').html('Неправильно введены данные').show();
						} else if(d == 3){
							$('#err_inv').html('Пользователь с таким E-Mail адресом уже зарегистрирован.').show();
							setErrorInputMsg('inv_email');
						} else if(d == 4){
							$('#err_inv').html('Пользователю с таким E-Mail адресом уже было отправлено приглашение.').show();
							setErrorInputMsg('inv_email');
						} else if(d){
							window.location.href = d;
						} else {
							$('#setup_inv').hide();
							$('#ok_inv').show();
							setTimeout(function(){
                            window.location.href = '/invite';
                            setTimeout(arguments.callee, 10000);
                            }, 10000);
						}
					}
				});
				
			} else {
				$('#err_inv').html('Код безопасности не соответствует отображённому').show();
				setErrorInputMsg('inv_sec_code');
			}
		} else {
			$('#err_inv').html('Неправильный email адрес').show();
			setErrorInputMsg('inv_email');
		}
		} else {
			$('#err_inv').html('Специальные символы и пробелы запрещены.').show();
			setErrorInputMsg('inv_lastname');
		}
		} else {
			$('#err_inv').html('Специальные символы и пробелы запрещены.').show();
			setErrorInputMsg('inv_name');
		}
	},
	finish: function(h){
		var inv_pass1 = $('#inv_pass1').val();
		var inv_pass2 = $('#inv_pass2').val();
				if(inv_pass1 != 0 && inv_pass1.length >= 6){
					if(inv_pass1 == inv_pass2){
						$('#err_inv2').hide();
						butloading('inv_fini', '140', 'disabled', '');
						$.post('/index.php?go=invite&act=finish', {inv_pass1: inv_pass1, inv_pass2: inv_pass2, hash: h}, function(d){
							if(d == 1){
								$('#err_inv2').html('Такой ссылки приглашения не существует.').show();
							} else if(d == 2){
								$('#err_inv2').html('Ошибка регистрации.').show();
							} else {
								window.location.href = '/editmypage';
							}
						});
					} else {
						$('#err_inv2').html('Оба введенных пароля должны быть идентичны.').show();
						setErrorInputMsg('inv_pass2');
					}
				} else {
					$('#err_inv2').html('Длина пароля должна быть не менее 6 символов.').show();
					setErrorInputMsg('inv_pass1');
				}
	}
}

function updateCode(){
	var rndval = new Date().getTime(); 
	$('#sec_code').html('<img src="/antibot/antibot.php?rndval=' + rndval + '" alt="" title="Показать другой код" width="120" height="50" />');
}

function isValidEmailAddress(emailAddress) {
 	var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
 	return pattern.test(emailAddress);
}

function isValidName(xname){
	var pattern = new RegExp(/^[a-zA-Zа-яА-Я]+$/);
 	return pattern.test(xname);
}