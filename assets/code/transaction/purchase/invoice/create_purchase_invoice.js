$(document).ready(function(){
    calculate();

    $(document).keypress(function(event){
        if(event.keyCode == 13) 
            event.preventDefault();
    });

    $('#date').datepicker({
        format: "dd-mm-yyyy",
        todayHighlight: true,
		clearBtn: false,
		autoclose: true,
		orientation: "bottom auto"
    });

    $('#date').change(function(){
		if($(this).val() == "")
		{
			toastr.error('Tanggal wajib terisi');
			$(this).datepicker().datepicker('setDate', new Date());
		}
	});

    $('#invoice').keyup(function() {
        $(this).val($(this).val().toUpperCase().replace(/\s+/g, ''));
        $.ajax({
            type: "POST", 
            url: 'transaction/Purchase/check_invoice',
            data: {
                invoice : $(this).val(),
            }, 
            dataType: "json",
            beforeSend: function(e) {
                if(e && e.overrideMimeType) {
                    e.overrideMimeType("application/json;charset=UTF-8");
                }
            },
            success: function(response){                 
                if(response.result == 1){
					$("#invoice_message").text("Mohon Maaf, nomor refrensi sudah digunakan. Harap periksa kembali");
					$('#btn_save').prop("disabled", true);
                }
                else
                {                    
					$("#invoice_message").text(null);
                    calculate();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    });

    $.getJSON("transaction/Purchase/get_supplier", (data) => {
        var option = '<option value="" class="kt-font-dark">- PILIH SUPPLIER -</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i) {
                option += '<option value="'+data.response[i].code+'"class="kt-font-dark">'+data.response[i].code+' | '+data.response[i].name+'</option>';
            });
        } 
        else 
        {
            option = option;
		}   
		$("#supplier_code").html(option).select2();
	});

	$("#supplier_code").change(function(){
		if($(this).val() != "")
		{
            $('#payment').prop("disabled", false);
            $.ajax({
                type: "POST", 
                url: 'transaction/Purchase/check_supplier',
                data: {
                    supplier_code : $(this).val(),
                }, 
                dataType: "json",
                beforeSend: function(e) {
                    if(e && e.overrideMimeType) {
                        e.overrideMimeType("application/json;charset=UTF-8");
                    }
                },
                success: function(data){
                    if(data['ppn'] == 1)
                    {
                        $('#ppn').val(1).trigger('change');
                    }
                    else
                    {
                        $('#ppn').val(0).trigger('change');
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
            dueday();
		}	
		else
		{
            $('#payment').val(1).trigger('change'); $('#payment_due').val(0); $('#ppn').val(0).trigger('change');
            $('#payment').prop( "disabled", true);
        }	        		
        calculate();
    });	    
	       
    $('#credit_method, #dp_checklist_form').hide();
    $('#payment').change(function(){
		if($(this).val() == 1)
		{
            $('#payment_method').html('TUNAI');
            $('#from_cl_type').val(1).trigger('change');
			$('#payment_due').prop("readonly", true);
			$('#cash_ledger_form').show(); $('#credit_method, #dp_checklist_form, #downpayment_form').hide();
			$('.cash_ledger_input').prop("disabled", false); $('.cash_ledger_input').prop("required", true);
            $('#cash_balance, #down_payment').val(0); $("#dp_checklist").prop("checked", false);
		}
		else
		{
            $('#payment_method').html('KREDIT');
			$('#payment_due').prop("readonly", false);
			$('#cash_ledger_form, #dowpayment_form').hide(); $('#dp_checklist_form, #credit_method').show();
            $('.cash_ledger_input').prop("disabled", true); $('.cash_ledger_input').prop("required", false);
            $('#cash_balance, #down_payment').val(0);
        }        
		dueday();
		calculate();
    });

    $('#payment_due').click(function(){
        $(this).select();
	});

    $('#payment_due').on('change, keyup', function(){
        if(Number($(this).val()) < 0)
        {
            toastr.error('Jatuh Tempo tidak bisa minus');
			$(this).val(0);
        }
	});

	$('#include_tax_method, #tax_form').hide();
	$('#ppn').change(function(){
        $('#price_include_tax').prop('checked', false);
		if($(this).val() != 0)
		{
			$('#price_include_tax').prop('checked', false);
			$('#include_tax_method').show('slow');
            $('#tax_form').show('slow');
            $('#price_include_tax').trigger('click');
		}
		else
		{
			$('#include_tax_method').hide('slow');
			$('#tax_form').hide('slow');
		}
		calculate();
	});

	$('#price_include_tax').on('click', function(){
		if($(this).prop('checked') == true)
		{
			$('#tax_form').hide('slow');            
		}        
		else
		{
			$('#tax_form').show('slow');
		}
		calculate();
	});

    $('.repeater').repeater({
        initEmpty: false,  
        isFirstItemUndeletable: false,
        hide: function () {
            $(this).remove();
            calculate();
        }
    });

    $("#product_table").keypress(function(e){
		var key = e.which;
		if(key == 13)
		{			
			e.preventDefault();
			$('#add_product').click();
			$('.product_input:last').focus();
			$('.product_input:last').select();		   
		}
	});
        
    $("#product_table").on("focus", ".product_input", function(){
        var product = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            prefetch:"transaction/Purchase/get_product",
            remote:{
                url:"transaction/Purchase/get_product/%QUERY%/"+$('#ppn').val(),
                wildcard:"%QUERY%"
            }
        });        
        if($(this).data('autocomple_ready') != 'ok' ){            
            var id = Math.round(new Date().getTime() +(Math.random() * 100)); 
            $(this).attr('id',id);
            var product_code = $(this).attr('id')+2;
            $(this).closest('tr').find('.product_code').attr('id', product_code);
            $('#'+id).typeahead({
                hint: true,
                highlight: true,
                minLength: 1,
            },
            {
                name: 'product',
                display: 'name',
                source: product,
                limit: 100,
                templates: {
                    empty: [
                        '<div class="empty-message" style="padding: 10px 15px; text-align: center; font-color:red;">',
                            'PRODUK TIDAK DITEMUKAN',
                        '</div>'
                    ].join('\n'),
                    suggestion: Handlebars.compile('<div>{{code}}<br>{{name}}</div>')
                }                 
            });               
            $('#'+id).bind('typeahead:select', function(ev, suggestion) {                                
                $(this).closest('tr').find('.product_code').val(suggestion['code']);
                $('#'+id).trigger('change');
            });
            $('#'+id).bind('typeahead:change', function(ev, suggestion) {                
                $('#'+product_code).val(suggestion['code']);
            });
        };
        $(this).data('autocomple_ready','ok');                        
    });

    $("#product_table").on("change", ".product_input",function(){        
        var id = Math.round(new Date().getTime() +(Math.random() * 100)); $(this).attr('id', id);
        var product_code = $(this).attr('id')+2; $(this).closest('tr').find('.product_code').attr('id', product_code);
        var qty          = $(this).attr('id')+3; $(this).closest('tr').find('.qty').attr('id', qty);
        var unit         = $(this).attr('id')+4; $(this).closest('tr').find('.unit').attr('id', unit);
        var warehouse    = $(this).attr('id')+5; $(this).closest('tr').find('.warehouse').attr('id', warehouse);
        var buyprice     = $(this).attr('id')+6; $(this).closest('tr').find('.buyprice').attr('id', buyprice);
        var price        = $(this).attr('id')+7; $(this).closest('tr').find('.price').attr('id', price);
        var total        = $(this).attr('id')+8; $(this).closest('tr').find('.total').attr('id', total);
        $('#'+qty).val(0); $('#'+total).val(0);
        $.ajax({
            type: "POST",
            url: "transaction/Purchase/get_unit/",		
            dataType: "JSON",
            data: {
                code: $('#'+product_code).val()
            },
            success: function(data) {                    
                $('#'+unit).html(data.option); 
                $.ajax({
                    type: "POST", 
                    url: 'transaction/Purchase/get_buyprice', 
                    data: {
                        product_code  : $('#'+product_code).val(),
                        unit_id       : $('#'+unit).val()                        
                    },
                    dataType: "JSON",
                    success: function(data){
                        var buyprice_value = (data.buyprice != null) ? data.buyprice : 0;
                        $('#'+buyprice).val(format_amount(String(buyprice_value))); $('#'+price).val(format_amount(String(buyprice_value)));
                        $.ajax({
                            type: "POST",
                            url: "transaction/Purchase/get_warehouse/",
                            data: {
                                product_code  : $('#'+product_code).val(),
                                unit_id       : $('#'+unit).val()
                            },
                            dataType: "JSON",
                            success: function(data) {                                       
                                $('#'+warehouse).html(data.option);
                            }
                        });
                    },            
                });                                       
            }
        });        
        calculate();                 
    });

    $("#product_table").on("change", ".unit", function() {
        var id = Math.round(new Date().getTime() + (Math.random() * 100)); $(this).attr('id', id);                        
        var id_buyprice = $(this).attr('id')+2; $(this).closest('tr').find('.buyprice').attr('id', id_buyprice);
        var warehouse   = $(this).attr('id')+3; $(this).closest('tr').find('.warehouse').attr('id', warehouse);
        var price       = $(this).attr('id')+4; $(this).closest('tr').find('.price').attr('id', price);
        var total       = $(this).attr('id')+5; $(this).closest('tr').find('.total').attr('id', total);
        $.ajax({
            type: "POST", 
            url: 'transaction/Purchase/get_buyprice', 
            data: {
                product_code  : $(this).closest('tr').find('.product_code').val(),
                unit_id       : $(this).val(),
            }, 
            dataType: "JSON",
            success: function(data){ 
                var buyprice = (data.buyprice != null) ? data.buyprice : 0;
                $('#'+id_buyprice).val(format_amount(String(buyprice)));                
                calculate();
            }
        });
        $.ajax({
            type: "POST",
            url: "transaction/Purchase/get_warehouse/",
            data: {
                product_code  : $(this).closest('tr').find('.product_code').val(),
                unit_id       : $(this).val(),
            },
            dataType: "JSON",
            success: function(data) {                                       
                $('#'+warehouse).empty().html(data.option);
            }
        });                
    });

    $("#product_table").on("click", ".qty, .price, .disc_product", function() {
        $(this).select();
    });

    $("#product_table").on("keyup", ".qty, .price", function() {
        $(this).val(format_amount($(this).val()));
    });

    $("#product_table").on("keyup", ".qty", function() {
        var id = Math.round(new Date().getTime() +(Math.random() * 100)); $(this).attr('id', id);
        var price        = $(this).attr('id')+2;
        var disc_product = $(this).attr('id')+3;
        var total        = $(this).attr('id')+4;        
        $(this).closest('tr').find('.price').attr('id', price);
        $(this).closest('tr').find('.disc_product').attr('id', disc_product);
        $(this).closest('tr').find('.total').attr('id', total);
        var subtotal = Number($('#'+id).val().replace(/\,/g, ""))*Number($('#'+price).val().replace(/\,/g, ""));
        var disc_p = $('#'+disc_product).val().split('+');
        if(disc_p != null && disc_p != "" )
        {
            for(var i = 0; i < disc_p.length; i++)
            {      
                if(Number(disc_p[i]) >= 0 && Number(disc_p[i]) <= 100)          
                {
                    subtotal = subtotal - (Number(disc_p[i])/100*Number(subtotal));
                }
                else
                {
                    $('#'+id).val(null);
                    swal.fire('', 'Terjadi Kesalahan Input Pada Diskon', 'error');
                    break;
                }                
            }
            $('#'+total).val(format_amount(String(subtotal.toFixed(2))));
        }
        else
        {
            $('#'+total).val(format_amount(String(subtotal.toFixed(2))));
        }
        calculate();
    });

    $("#product_table").on("keyup", ".price", function() {
        var id = Math.round(new Date().getTime() +(Math.random() * 100)); $(this).attr('id', id);
        var qty          = $(this).attr('id')+2;
        var disc_product = $(this).attr('id')+3;
        var total        = $(this).attr('id')+4;
        $(this).closest('tr').find('.qty').attr('id', qty);
        $(this).closest('tr').find('.disc_product').attr('id', disc_product);
        $(this).closest('tr').find('.total').attr('id', total);
        var subtotal = Number($('#'+qty).val().replace(/\,/g, ""))*Number($('#'+id).val().replace(/\,/g, ""));
        var disc_p = $('#'+disc_product).val().split('+');
        if(disc_p != null && disc_p != "" )
        {
            for(var i = 0; i < disc_p.length; i++)
            {      
                if(Number(disc_p[i]) >= 0 && Number(disc_p[i]) <= 100)          
                {
                    subtotal = subtotal - (Number(disc_p[i])/100*Number(subtotal));
                }
                else
                {
                    $('#'+id).val(null);
                    swal.fire('', 'Terjadi Kesalahan Input Pada Diskon', 'error');
                    break;
                }
            }
            $('#'+total).val(format_amount(String(subtotal.toFixed(2))));
        }
        else
        {
            $('#'+total).val(format_amount(String(subtotal.toFixed(2))));
        }
        calculate();
    });
    
    $("#product_table").on("keyup", ".disc_product", function() {
        var id = Math.round(new Date().getTime() +(Math.random() * 100));
        $(this).attr('id', id);
        var qty = $(this).attr('id')+2;
        var price = $(this).attr('id')+3;
        var total = $(this).attr('id')+4;        
        $(this).closest('tr').find('.qty').attr('id', qty);
        $(this).closest('tr').find('.price').attr('id', price);
        $(this).closest('tr').find('.total').attr('id', total);        
        var subtotal = Number($('#'+qty).val().replace(/\,/g, ""))*Number($('#'+price).val().replace(/\,/g, ""));
        var disc_p = $('#'+id).val().split('+');
        if(disc_p != null && disc_p != "" )
        {
            for(var i = 0; i < disc_p.length; i++)
            {      
                if(Number(disc_p[i]) >= 0 && Number(disc_p[i]) <= 100)          
                {
                    subtotal = subtotal - (Number(disc_p[i])/100*Number(subtotal));
                }
                else
                {
                    $('#'+id).val(null);
                    swal.fire('', 'Terjadi Kesalahan Input Pada Diskon', 'error');
                    break;
                }                
            }
            $('#'+total).val(format_amount(String(subtotal.toFixed(2))));
        }
        else
        {
            $('#'+total).val(format_amount(String(subtotal.toFixed(2))));
        }                
        calculate();
	});
	
    $("#discount_p, #discount_rp, #down_payment, #delivery_cost").on("click",function() {
        $(this).select();
    });

    $("#discount_rp, #down_payment, #delivery_cost").on("keyup",function() {
        $(this).val(format_amount($(this).val()));
    });

    $('#discount_amount').hide();
    $('#discount_method').change(function(){
        if($(this).val() == 1)
        {
            $('#discount_amount').hide();
            $('#discount_percent').show();
        }
        else
        {
            $('#discount_percent').hide();
            $('#discount_amount').show();            
        }
    });

    $('#discount_p').keyup(function(){ 
        var discount_rp = (Number($(this).val().replace(/\,/g, "")) / 100)*Number($('#subtotal').val().replace(/\,/g, ""));
        $('#discount_rp').val(discount_rp);
        if(Number($(this).val().replace(/\,/g, "") >= 100))
        {
			$('#discount_p').val(0); $('#discount_rp').val(0);
            swal.fire('', 'Mohon Maaf, maksimal diskon adalah 100%. Silahkan periksa kembali, terima kasih.', 'error');
            $(this).select();
        }
        calculate();
    });
    
    $('#discount_rp').keyup(function(){
        var discount_p = (Number($(this).val().replace(/\,/g, "")) / Number($('#subtotal').val().replace(/\,/g, "")))*100;
        $('#discount_p').val(discount_p);
        if(Number($(this).val().replace(/\,/g, "")) >= Number($('#subtotal').val().replace(/\,/g, "")))
        {
			$('#discount_p').val(0); $('#discount_rp').val(0);
			swal.fire('', 'Mohon Maaf, maksimal diskon adalah 100%. Silahkan periksa kembali, terima kasih.', 'error');
            $(this).select();
        }
        calculate();
    });

    $("#delivery_cost").on("keyup",function(){
        calculate();
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
	
    $('#downpayment_form').hide();
	$('#dp_checklist').change(function(){        
		if($(this).prop('checked') == true)
		{
            $('#from_cl_type').val(1).trigger('change');
            $('#cash_ledger_form, #downpayment_form').show();
			$('.cash_ledger_input').prop("disabled", false); $('.cash_ledger_input').prop("required", true);
		}        
		else
		{			
            $('#cash_ledger_form, #downpayment_form').hide();
            $('#down_payment').val(0);
			$('.cash_ledger_input').prop("disabled", true); $('.cash_ledger_input').prop("required", false);
        }			
        calculate();
    });        
        
	$('#from_cl_type').change(function(){
        $('#cash_balance').val(format_amount(String(0)));
		var option = '<option value="" class="kt-font-dark">- PILIH AKUN -</option>';
			$.getJSON("finance/Cash_ledger/get_cash_ledger_account/"+$('#from_cl_type').val(), (data) => {
				if (data.status.code == 200) {
					$.each(data.response, function(i, item) {
						option += '<option value="' + data.response[i].id + '" class="text-dark">'+data.response[i].code+' | '+data.response[i].name+'</option>';
					});				
				} else {
					option = option;
				}			
				$("#from_account_id").html(option);
			}); 
    });

    $('#from_account_id').on('change',function(){
		$.ajax({
			type: "POST", 
			url: 'finance/Cash_ledger/get_last_balance',
			data: {
				cl_type: $('#from_cl_type').val(),
				account_id: $('#from_account_id').val()
			}, 
			dataType: "json",
			beforeSend: function(e) {
				if(e && e.overrideMimeType) {
					e.overrideMimeType("application/json;charset=UTF-8");
				}
			},
			success: function(response){
				$('#cash_balance').val(format_amount(String(response)));
				calculate();
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
			}
		});
    });

    $('#down_payment').keyup(function(){
        if(Number($(this).val().replace(/\,/g, "")) <= Number($('#cash_balance').val().replace(/\,/g, "")))
        {
            if(Number($(this).val().replace(/\,/g, "")) >= Number($('#grandtotal').val().replace(/\,/g, "")) && Number($('#grandtotal').val().replace(/\,/g, "")) != 0)
            {
                $(this).val(null);
                swal.fire('', 'Mohon Maaf, uang muka tidak bisa melebihi/sama dengan nilai grandtotal transaksi, terima kasih.', 'error');
                $(this).select();
            }
        }
        else
        {
            swal.fire("Mohon Maaf!", "Jumlah pembayaran tidak bisa dilakukan karena saldo tidak mencukupi, terima kasih", "error");
			$(this).val(null);
        }
        calculate();
    });
});

function dueday()
{
    if($('#payment').val() == 1)
    {        
        $('#payment_due').val(0);
    }
    else
    {
        $.ajax({
            type: "POST", 
            url: 'transaction/Purchase/get_payment_due',
            data: {
                supplier_code : $('#supplier_code').val(),
            }, 
            dataType: "json",
            beforeSend: function(e) {
                if(e && e.overrideMimeType) {
                    e.overrideMimeType("application/json;charset=UTF-8");
                }
            },
            success: function(data){
                if(data.dueday != null)
                {
                    $('#payment_due').val(data.dueday);
                }                 
                else
                {
                    $('#payment_due').val(0);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });        
    }
}

function calculate()
{
    var total_product = $(".qty");
    $('#total_product').val(total_product.length);
    
    var total_qty = 0;
    $('.qty').each(function(){
        total_qty += Number($(this).val().replace(/\,/g, ""));
    });
    $('#total_qty').val(total_qty);

    var subtotal = 0;
    $('.total').each(function() {
        subtotal += Number($(this).val().replace(/\,/g, ""));
    });
    $('#subtotal').val(format_amount(String(subtotal.toFixed(2))));

    var discount_rp = $('#discount_rp').val().replace(/\,/g, "");
    var ppn;
    if($('#ppn').val() != 0)
    {
        if($('#price_include_tax').prop('checked') == true)
        {
            ppn = 0;
        }    
        else
        {
            ppn = (subtotal - discount_rp)*0.11;
        }
    }
    else
    {
        ppn = 0;
    }    
    $('#total_tax').val(format_amount(String(ppn)));

    var delivery_cost = $('#delivery_cost').val().replace(/\,/g, "");
    var grandtotal = Number(subtotal)-Number(discount_rp)+Number(ppn)+Number(delivery_cost);
    var downpayment = $('#down_payment').val() ? $('#down_payment').val().replace(/\,/g, "") : 0;
    $('#account_payable').val(format_amount(String(Number(grandtotal) - Number(downpayment))));
    $('#grandtotal').val(format_amount(String(grandtotal.toFixed(2))));
    if(Number(total_product.length) > 0 && Number(total_qty) > 0)
    {
        if($('#payment').val() == 2)
        {
            if($('#dp_checklist').prop('checked') == true)
            {
                if(Number(downpayment) > 0)
                {
                    $('#btn_save').prop("disabled", false);
                }
                else
                {
                    $('#btn_save').prop("disabled", true);
                }   
            }
            else
            {
                $('#btn_save').prop("disabled", false);
            }                     
        }
        else
        {
			if(Number($('#cash_balance').val().replace(/\,/g, "")) >= grandtotal)
			{
                $('#balance_notify').hide();
				$('#btn_save').prop("disabled", false);
			}
			else
			{
                $('#balance_notify').show();
				$('#btn_save').prop("disabled", true);
			}            
        }
    }
    else
    {
        $('#btn_save').prop("disabled", true);
    }
    validate_qty_input();
}

function validate_qty_input()
{
    var found = 0;
    $('.qty').each(function(){
        if(Number($(this).val().replace(/\,/g, "")) <= Number(0))
        {
            found++;
        }
    });
    if(found == 0)
    {
        $('#qty_notify').hide();
        $('#btn_save').prop("disabled", false);
    }
    else
    {
        $('#qty_notify').show();
        $('#btn_save').prop("disabled", true);
    }
        
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