var pEnum = 0;
var payment = {
  box: function(){
    viiBox.start();
	$.post('/index.php?go=balance&act=payment', function(d){
	  viiBox.win('payment', d);
	});
  },
  box_two: function(){
    viiBox.start();
	$.post('/index.php?go=balance&act=payment_2', function(d){
	  viiBox.win('payment_2', d);
	  $('#cost_balance').focus();
	});
  },
  operator: function(v){
	var check = $('#'+v).length;
	if(check){
	  $('#payment_oper').html($('#'+v).html()).attr('disabled', 0);
	} else {
	  $('#payment_oper').html('<option value="0"></option>').attr('disabled', 'disabled');
	}
	$('#payment_cost').html('<option value="0"></option>').attr('disabled', 'disabled');
	$('#smsblock').hide();
  },
  cost: function(v){
    var check = $('#cost_'+v).length;
	if(check){
	  $('#payment_cost').html($('#cost_'+v).html()).attr('disabled', 0);
	} else {
	  $('#payment_cost').html('<option value="0"></option>').attr('disabled', 'disabled');
	}
	$('#smsblock').hide();
  },
  number: function(v, t){
    var v = v.split('|');
    if(v[0] != 0){
	  $('#smsblock').show();
	  $('#smsnumber').text(v[0]);
	  if(v[1]) $('#smspref').text(v[1]);
	  else $('#smspref').text('');
	} else
	  $('#smsblock').hide();
  },
  update: function(){
    var pr = parseInt($('#cost_balance').val());
	if(!isNaN(pr)) $('#cost_balance').val(parseInt($('#cost_balance').val()));
	else $('#cost_balance').val('');
	var num = $('#cost_balance').val() * $('#cost').val();
	var res = ( $('#balance').val() - num );
	$('#num').text( res );
	if(!$('#cost_balance').val()) $('#num').text( $('#balance').val() );
	else if(res < 0) $('#num').text('недостаточно');
  },
  send: function(){
    var num = $('#cost_balance').val();
	var num_2 = $('#cost_balance').val() * $('#cost').val();
	var res = $('#balance').val() - num_2;
	var rub2 = $('#balance').val() - num_2;
	if(pEnum > 10){
	  alert('Я тебе голову сломаю!');
	  pEnum = 0;
	}
	if(res <= 0) res = 999999999999;
	if(num != 0 && $('#balance').val() >= res){
	  butloading('saverate', 50, 'disabled', '');
      $.post('/index.php?go=balance&act=ok_payment', {num: num}, function(d){
	    if(d == 1){
		  addAllErr('Пополните баланс для покупки.', 3300);
		  return false;
		}
	    $('#rub2').text(rub2);
	    $('#num2').text(parseInt($('#num2').text()) + parseInt(num));
        viiBox.clos('payment_2', 1);
      });	
	} else {
	  setErrorInputMsg('cost_balance');
	  pEnum++;
	}
  }
}