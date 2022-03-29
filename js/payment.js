/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

var pEnum = 0;
var payment = {
    box: function () {
        // viiBox.start();
        $.post('/balance/payment', function (d) {
            Box.Show('payment', 400, lang_payment_set, d, lang_box_cancel);
        });
    },
    box_two: function () {
        $.post('/balance/payment_2', function (d) {
            Box.Show('payment2', 400, 'payment_2', d, lang_box_cancel);
            $('#cost_balance').focus();
        });
    },
    operator: function (v) {
        var check = $('#' + v).length;
        if (check) {
            $('#payment_oper').html($('#' + v).html()).attr('disabled', 0);
        } else {
            $('#payment_oper').html('<option value="0"></option>').attr('disabled', 'disabled');
        }
        $('#payment_cost').html('<option value="0"></option>').attr('disabled', 'disabled');
        $('#smsblock').hide();
    },
    cost: function (v) {
        var check = $('#cost_' + v).length;
        if (check) {
            $('#payment_cost').html($('#cost_' + v).html()).attr('disabled', 0);
        } else {
            $('#payment_cost').html('<option value="0"></option>').attr('disabled', 'disabled');
        }
        $('#smsblock').hide();
    },
    number: function (v, t) {
        var v = v.split('|');
        if (v[0] != 0) {
            $('#smsblock').show();
            $('#smsnumber').text(v[0]);
            if (v[1]) $('#smspref').text(v[1]);
            else $('#smspref').text('');
        } else
            $('#smsblock').hide();
    },
    update: function () {
        var pr = parseInt($('#cost_balance').val());
        if (!isNaN(pr)) $('#cost_balance').val(parseInt($('#cost_balance').val()));
        else $('#cost_balance').val('');
        var num = $('#cost_balance').val() * $('#cost').val();
        var res = ($('#balance').val() - num);
        $('#num').text(res);
        if (!$('#cost_balance').val()) $('#num').text($('#balance').val());
        else if (res < 0) $('#num').text('недостаточно');
    },
    send: function () {
        var num = $('#cost_balance').val();
        var num_2 = $('#cost_balance').val() * $('#cost').val();
        var res = $('#balance').val() - num_2;
        var rub2 = $('#balance').val() - num_2;
        if (pEnum > 10) {
            // alert('ttt');
            console.log('ttt');
            pEnum = 0;
        }
        if (res <= 0) res = 999999999999;
        if (num !== 0 && $('#balance').val() >= res) {
            butloading('saverate', 50, 'disabled', '');
            $.post('/balance/ok_payment', {num: num}, function (d) {
                if (d === 1) {
                    Page.addAllErr('Пополните баланс для покупки.', 3300);
                    return false;
                }
                $('#rub2').text(rub2);
                $('#num2').text(parseInt($('#num2').text()) + parseInt(num));
                Box.Close('payment2', false);
            });
        } else {
            setErrorInputMsg('cost_balance');
            pEnum++;
        }
    }
}