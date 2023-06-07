KTApp.block('#transaction_form',{
    overlayColor: '#000000',
    type: 'v2',
    state: 'primary',
    message: '<span class="text-dark font-weight-bold">Mohon menunggu, sedang dalam proses...</span>'
});

$(document).ready(function(){
    KTApp.unblock('#transaction_form');

    $('#date').datepicker({
        format: "dd-mm-yyyy",
        todayHighlight: true,
		clearBtn: false,
		autoclose: true,
		orientation: "bottom auto"
    });
    
    $(document).keypress(function(event){
        if(event.keyCode == 13) 
            event.preventDefault();
      });

    if($('#supplier_code').val() == "")
    {
        $.getJSON("transaction/Purchase/get_supplier", (data) => {
            var option = '<option value="" class="kt-font-dark">- PILIH SUPPLIER -</option>';
            if (data.status.code == 200) {
                $.each(data.response, function(i) {
                    option += '<option value="' + data.response[i].code + '" class="kt-font-dark" data-id="'+data.response[i].id+'">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
                });
            } 
            else 
            {
                option = option;
            }   
            $("#supplier_code").html(option).select2();
        });

        $("#supplier_code").on("change", function(){
            $('#supplier_id').val($(this).children("option:selected").data('id'));
        });
    }                

    $('.repeater').repeater({
        initEmpty: false,  
        isFirstItemUndeletable: false,
        hide: function () {
            $(this).remove();
            calculate();
        }
    });

    $("#transaction_table").keypress(function(e){
		var key = e.which;
		if(key == 13)
		{			
			e.preventDefault();
			$('#add_transacton').click();
			$('.transaction_input:last').focus();
			$('.transaction_input:last').select();		   
		}
	});
        
    $("#transaction_table").on("focus", ".transaction_input", function(){
        var transaction = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            prefetch:"finance/Payment/get_transaction/1/"+$('#supplier_code').val(),
            remote:{
                url:"finance/Payment/get_transaction/1/"+$('#supplier_code').val()+"/%QUERY%/",
                wildcard:"%QUERY%"
            }
        });        
        if($(this).data('autocomple_ready') != 'ok' ){            
            var id = Math.round(new Date().getTime() +(Math.random() * 100)); $(this).attr('id',id);
            var transaction_id = $(this).attr('id')+2;
            var transaction_date    = $(this).attr('id')+4; $(this).closest('tr').find('.transaction_date').attr('id', transaction_date);
            var transaction_invoice = $(this).attr('id')+3; $(this).closest('tr').find('.transaction_invoice').attr('id', transaction_invoice);        
            var transaction_grandtotal = $(this).attr('id')+5; $(this).closest('tr').find('.transaction_grandtotal').attr('id', transaction_grandtotal);
            var transaction_account_payable = $(this).attr('id')+6; $(this).closest('tr').find('.transaction_account_payable').attr('id', transaction_account_payable);
            $(this).closest('tr').find('.transaction_id').attr('id', transaction_id);
            $('#'+id).typeahead({
                hint: true,
                highlight: true,
                minLength: 1,
            },
            {
                name: 'transaction',
                display: 'code',
                source: transaction,
                limit: 100,
                templates: {
                    empty: [
                        '<div class="empty-message" style="padding: 10px 15px; text-align: center; font-color:red;">',
                            'TRANSAKSI TIDAK DITEMUKAN',
                        '</div>'
                    ].join('\n'),
                    suggestion: Handlebars.compile('<div>{{date}}<br>{{code}} | {{invoice}}<br>TOTAL TRANSAKSI: {{grandtotal}}<br>SISA TAGIHAN: {{account_payable}}</div>')
                }
            });
            $('#'+id).bind('typeahead:select', function(ev, suggestion) {
                $(this).closest('tr').find('.transaction_id').val(suggestion['id']);
                $(this).closest('tr').find('.transaction_date').val(suggestion['date']);
                $(this).closest('tr').find('.transaction_invoice').val(suggestion['invoice']);
                $(this).closest('tr').find('.transaction_grandtotal').val(suggestion['grandtotal']);
                $(this).closest('tr').find('.transaction_account_payable').val(suggestion['account_payable']);
                $(this).closest('tr').find('.transaction_pay').val(suggestion['account_payable']);
                calculate();
            });
        };
        $(this).data('autocomple_ready','ok');                        
    });

    $("#payment_table").keypress(function(e){
		var key = e.which;
		if(key == 13)
		{			
			e.preventDefault();
			$('#add_payment').click();
			$('.payment_method:last').focus();
			$('.payment_method:last').select();		   
		}
    });
    
    $.getJSON("finance/Cash_ledger/get_cash_ledger_account/"+$('#payment_method').val(), (data) => {	
		var option = '<option value="" class="kt-font-dark">- PILIH AKUN -</option>';
		if(data.status.code == 200) {
			$.each(data.response, function(i) {
				option += '<option value="' + data.response[i].id + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
			});
		} else {
			option = option;
		}
		$("#account_id").html(option);
    });
    
    $('#payment_table').on('change', '.payment_method',function(){
        $(this).closest('tr').find('.date').datepicker("destroy");
        if($(this).val() == 1)
        {
            $.getJSON("finance/Cash_ledger/get_cash_ledger_account/1", (data) => {	
                var option = '<option value="" class="kt-font-dark">- PILIH AKUN -</option>';
                if(data.status.code == 200) {
                    $.each(data.response, function(i) {
                        option += '<option value="' + data.response[i].id + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
                    });
                } else {
                    option = option;
                }
                $(this).closest('tr').find('.account_id').html(option);
            });
            $(this).closest('tr').find('.cheque_number').prop('readonly', true).prop('required', false).val(null);
            $(this).closest('tr').find('.cheque_open_date').prop('readonly', true).prop('required', false).val(null);
            $(this).closest('tr').find('.cheque_close_date').prop('readonly', true).prop('required', false).val(null);
        }
        else if($(this).val() == 2 || $(this).val() == 3)
        {
            $.getJSON("finance/Cash_ledger/get_cash_ledger_account/2", (data) => {	
                var option = '<option value="" class="kt-font-dark">- PILIH AKUN -</option>';
                if(data.status.code == 200) {
                    $.each(data.response, function(i) {
                        option += '<option value="' + data.response[i].id + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
                    });
                } else {
                    option = option;
                }
                $(this).closest('tr').find('.account_id').html(option);
            });
            if($(this).val() == 3)
            {
                $(this).closest('tr').find('.cheque_number').prop('readonly', false).prop('required', true).val(null);
                $(this).closest('tr').find('.cheque_open_date').prop('readonly', false).prop('required', true).val(null);
                $(this).closest('tr').find('.cheque_close_date').prop('readonly', false).prop('required', true).val(null);
                $(this).closest('tr').find('.date').datepicker({
                    format: "dd-mm-yyyy",
                    todayHighlight: true,
                    clearBtn: true,
                    autoclose: true,
                    orientation: "bottom auto"
                });
            }
            else
            {
                $(this).closest('tr').find('.cheque_number').prop('readonly', true).prop('required', false).val(null);
                $(this).closest('tr').find('.cheque_open_date').prop('readonly', true).prop('required', false).val(null);
                $(this).closest('tr').find('.cheque_close_date').prop('readonly', true).prop('required', false).val(null);
            }            
        }
        else if($(this).val() == 4)
        {
            $.getJSON("finance/Cash_ledger/get_cash_ledger_account/3/"+$('#supplier_id').val(), (data) => {
                if(data.status.code == 200) {
                    $.each(data.response, function(i) {
                        option += '<option value="' + data.response[i].id + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
                    });
                } else {
                    option = option;
                }
                $(this).closest('tr').find('.account_id').html(option);
            });            
            $(this).closest('tr').find('.cheque_number').prop('readonly', true).prop('required', false).val(null);
            $(this).closest('tr').find('.cheque_open_date').prop('readonly', true).prop('required', false).val(null);
            $(this).closest('tr').find('.cheque_close_date').prop('readonly', true).prop('required', false).val(null);
        }
        else
        {
            var option = '<option value="" class="kt-font-dark">- PILIH AKUN -</option>';
            $(this).closest('tr').find('.account_id').html(option);
            $(this).closest('tr').find('.cheque_number').prop('readonly', true).prop('required', false).val(null);
            $(this).closest('tr').find('.cheque_open_date').prop('readonly', true).prop('required', false).val(null);
            $(this).closest('tr').find('.cheque_close_date').prop('readonly', true).prop('required', false).val(null);
        }
    });

    $('#add_payment').on('click', function() {
        let html;
        let i = randomstring(32);
        html += '<tr id="'+i+'" class="table-additional empty">';
        html += '<td><select class="form-control payment_method" id="payment_method'+i+'" name="payment_method[]" required><option value="1">KAS</option><option value="2">TRANSFER</option><option value="3">CEK/GIRO</option><option value="4">DEPOSIT</option></td>';
        html += '<td><select class="form-control account_id" id="account_id'+i+'" name="account_id[]" required></select></td>';
        $.getJSON("finance/Cash_ledger/get_cash_ledger_account/1", (data) => {
            var option = '<option value="" class="kt-font-dark">- PILIH AKUN -</option>';
            if(data.status.code == 200) {
                $.each(data.response, function(i) {
                    option += '<option value="' + data.response[i].id + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
                });
            } else {
                option = option;
            }
            $("#account_id"+i).html(option);
        });
        html += '<td><input type="text" class="form-control uppercase cheque_number" name="cheque_number[]" readonly></td>';
        html += '<td><input type="text" class="form-control date cheque_open_date" name="cheque_open_date[]" readonly></td>';
        html += '<td><input type="text" class="form-control date cheque_close_date" name="cheque_close_date[]" readonly></td>';
        html += '<td><input type="text" class="form-control text-right amount payment_pay" name="payment_pay[]" placeholder="0" required></td>';        
        html += '<td><label class="col-form-label text-danger text-center delete_payment'+i+'"><i class="fa fa-times"></i></label></td>';
        $("#payment_table").append(html);
    });        

    $("#payment_table").on("click", ".table-additional", function() {
        let id = $(this).attr('id');
        // Delete
        $(".delete_payment"+id).on("click", function() {
            $('#'+id).remove();
        });
    });

    $("#transaction_table, #payment_table").on("click", ".amount", function() {
        $(this).select();
    });

    $("#transaction_table, #payment_table").on("keyup", ".amount", function() {
        $(this).val(format_amount($(this).val()));
        calculate();
    });

    $("#payment_table").on("keyup", ".uppercase", function() {
        $(this).val($(this).val().toUpperCase().replace(/\s+/g, ''));        
    });

    calculate();
});

function calculate()
{
    var total_transaction_pay = 0;
    $('.transaction_pay').each(function(){
        total_transaction_pay += Number($(this).val().replace(/\,/g, ""));
    });
    total_transaction_pay += Number($('#cost').val().replace(/\,/g, ""));
    $('#total_transaction_pay').val(format_amount(String(total_transaction_pay)));

    var total_payment_pay = 0;
    $('.payment_pay').each(function(){
        total_payment_pay += Number($(this).val().replace(/\,/g, ""));
    });
    $('#total_payment_pay').val(format_amount(String(total_payment_pay)));

    if(total_transaction_pay > 0 && total_payment_pay > 0)
    {
        if(total_transaction_pay == total_payment_pay)
        {
            $('#btn-save').prop('disabled', false);
            $('#notify').hide();
        }
        else
        {
            $('#btn-save').prop('disabled', true);
            var message = "MOHON PERHATIAN! JUMLAH TRANSAKSI DAN PEMBAYARAN TERDAPAT SELISIH "+format_amount(String(Math.abs(total_transaction_pay-total_payment_pay)));
            $('#notify').html(message); $('#notify').show();
        }
    }
    else
    {
        $('#btn-save').prop('disabled', true);
        $('#notify').hide();
    }
}

function randomstring(L) {
    var s = '';
    var randomchar = function() {
        var n = Math.floor(Math.random() * 62);
        if (n < 10) return n; //1-10
        if (n < 36) return String.fromCharCode(n + 55); //A-Z
        return String.fromCharCode(n + 61); //a-z
    }
    while (s.length < L) s += randomchar();
    return s;
}

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