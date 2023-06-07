$(document).ready(function(){
    $('#date').datepicker({
        format: "dd-mm-yyyy",
        todayHighlight: true,
		clearBtn: true,
		autoclose: true,
		orientation: "bottom auto"
    });                	

    $.getJSON("finance/Cash_ledger/get_cash_ledger_account/"+$('#from_cl_type').val(), (data) => {	
		var option = '<option value="" class="kt-font-dark">- PILIH AKUN -</option>';
		if(data.status.code == 200) {
			$.each(data.response, function(i) {
				option += '<option value="' + data.response[i].id + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
			});
		} else {
			option = option;
		}
		$("#from_account_id").html(option);
    });

    $('#from_cl_type').change(function(){
		var option = '<option value="" class="kt-font-dark">- PILIH AKUN -</option>';
		$.getJSON("finance/Cash_ledger/get_cash_ledger_account/"+$('#from_cl_type').val(), (data) => {	
			if (data.status.code == 200) {
				$.each(data.response, function(i) {
					option += '<option value="' + data.response[i].id + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
				});
			} else {
				option = option;
			}			
			$("#from_account_id").html(option);
		});
    });
    
    $.getJSON("finance/Cash_ledger/get_cash_ledger_account/"+$('#to_cl_type').val(), (data) => {	
		var option = '<option value="" class="kt-font-dark">- PILIH AKUN -</option>';
		if(data.status.code == 200) {
			$.each(data.response, function(i) {
				option += '<option value="' + data.response[i].id + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
			});
		} else {
			option = option;
		}
		$("#to_account_id").html(option);
    });

    $('#to_cl_type').change(function(){
		var option = '<option value="" class="kt-font-dark">- PILIH AKUN -</option>';
		$.getJSON("finance/Cash_ledger/get_cash_ledger_account/"+$('#to_cl_type').val(), (data) => {	
			if (data.status.code == 200) {
				$.each(data.response, function(i) {
					option += '<option value="' + data.response[i].id + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
				});
			} else {
				option = option;
			}			
			$("#to_account_id").html(option);
		});
    });
    
    $('#amount').keyup(function() {
        $(this).val(format_amount($(this).val()));
        calculate();
    });
});

function format_amount(angka, prefix){
	var number_string = angka.replace(/[^.\d]/g, '').toString(),
	split   		= number_string.split('.'),
	sisa     		= split[0].length % 3,
	rupiah     		= split[0].substr(0, sisa),
	ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);
	if(ribuan){
		separator = sisa ? ',' : '';
		rupiah += separator + ribuan.join(',');
	}	
	rupiah = split[1] != undefined ? rupiah + '.' + split[1] : rupiah;
	return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
}

function calculate()
{        
    if(Number($('#amount').val().replace(/\,/g, "")) > 0)
    {
        $('#btn_save').prop('disabled', false);
    }
    else
    {
        $('#btn_save').prop('disabled', true);
    }
}