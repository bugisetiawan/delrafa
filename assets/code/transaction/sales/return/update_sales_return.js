$(document).ready(function(){
    $('#date').datepicker({
        format: "dd-mm-yyyy",
        todayHighlight: true,
		clearBtn: true,
		autoclose: true,
		orientation: "bottom auto"
    });   

    $.getJSON("transaction/Sales/get_customer", (data) => {
        var option = '<option value="" class="kt-font-dark">- PILIH PELANGGAN -</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i) {
                option += '<option value="' + data.response[i].code + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
            });
        } else {
            option = option;
        }        
        var value = ($('#customer_code_update').val() != null) ? $('#customer_code_update').val() : null;
        $('#customer_code').val(value);				
        $('#customer_code').html(option).val(value).select2();
    });

    $("#customer_code").change(function(){
		if($(this).val()!="")
		{
            $('#method').prop( "disabled", false); 
            $.ajax({
                type: "POST", 
                url: 'transaction/Sales/check_customer',
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
            if($('#method').val() == 2)
            {
                $.getJSON("transaction/Sales/get_invoice_return/"+$('#customer_code').val(), (data) => {
                    var option = '<option value="" class="kt-font-dark">- PILIH PENJUALAN -</option>';
                    if (data.status.code == 200) {
                        $.each(data.response, function(i) {
                            option += '<option value="' + data.response[i].id + '" class="kt-font-dark">' + format_date(data.response[i].date) + ' | '+ data.response[i].invoice + ' | '+ String(format_amount(data.response[i].account_payable)) + '</option>';
                        });
                    } 
                    else 
                    {
                        option = option;
                    }
                    $('#sales_invoice_id').html(option).select2();
                });
            }           
		}	
		else
		{
            $('#method').val(1).trigger('change');
            $('#method').prop( "disabled", true);            
        }
    });

    if($('#method').val() == 1)
    {
        $('#cash_ledger_form').show(); $('.cash_ledger_input').prop("disabled", false); $('.cash_ledger_input').prop("required", true);
        $('#sales_invoice_id, #account_payable, #grandtotal').attr("required", false);
        $('.choose_invoice').hide();
    }
    else
    {
        $.getJSON("transaction/Sales/get_invoice_return/"+$('#customer_code_update').val(), (data) => {
            var option = '<option value="" class="kt-font-dark">- PILIH PENJUALAN -</option>';
            if (data.status.code == 200) {
                $.each(data.response, function(i) {
                    option += '<option value="' + data.response[i].id + '" class="kt-font-dark">' + format_date(data.response[i].date) + ' | '+ data.response[i].invoice + ' | '+ String(format_amount(data.response[i].account_payable)) + '</option>';
                });
            } 
            else 
            {
                option = option;
            }
            var value = ($('#sales_invoice_id_update').val() != null) ? $('#sales_invoice_id_update').val() : null;
            $('#sales_invoice_id').val(value);				
            $('#sales_invoice_id').html(option).val(value).select2().trigger('change');
        });
        $('#cash_ledger_form').hide(); $('.cash_ledger_input').prop("disabled", true); $('.cash_ledger_input').prop("required", false);
        $('#sales_invoice_id, #account_payable, #grandtotal').attr("required", true);
        $('.choose_invoice').show();
    }

    $("#method").change(function(){
		if($(this).val() == 1)
		{
            $('#cash_ledger_form').show(); $('.cash_ledger_input').prop("disabled", false); $('.cash_ledger_input').prop("required", true);
            $('#sales_invoice_id, #account_payable, #grandtotal').attr("required", false);
			$('.choose_invoice').hide();
		}	
		else
		{
            $.getJSON("transaction/Sales/get_invoice_return/"+$('#customer_code').val(), (data) => {
                var option = '<option value="" class="kt-font-dark">- PILIH PENJUALAN -</option>';
                if (data.status.code == 200) {
                    $.each(data.response, function(i) {
                        option += '<option value="' + data.response[i].id + '" class="kt-font-dark">' + format_date(data.response[i].date) + ' | '+ data.response[i].invoice + ' | '+ String(format_amount(data.response[i].account_payable)) + '</option>';
                    });
                } 
                else 
                {
                    option = option;
                }
                $('#sales_invoice_id').html(option).select2();
            });
            $('#cash_ledger_form').hide(); $('.cash_ledger_input').prop("disabled", true); $('.cash_ledger_input').prop("required", false);
            $('#sales_invoice_id, #account_payable, #grandtotal').attr("required", true);
			$('.choose_invoice').show();
		}	
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
        $.getJSON("finance/Cash_ledger/get_cash_ledger_account/"+$(this).val(), (data) => {	
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
        var customer_code = $('#customer_code').val();
        var ppn = $('#ppn').val();
        var product = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            prefetch:"transaction/Sales/get",
            remote:{
                url:"transaction/Sales/get_product_return/%QUERY%/"+customer_code+"/"+ppn,
                wildcard:"%QUERY%"
            }
        });  

        if( $(this).data('autocomple_ready') != 'ok' ){
            var id = Math.round(new Date().getTime() +(Math.random() * 100))
            $(this).attr('id',id);
            var product_code =$(this).attr('id')+2;
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
                            'PRODUK TIDAK ADA',
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
    
    $("#product_table").on( "change", ".product_input", function(){        
        var id=Math.round(new Date().getTime() +(Math.random() * 100));
        $(this).attr('id', id);
        var product_code = $(this).attr('id')+2; $(this).closest('tr').find('.product_code').attr('id', product_code);
        var qty          = $(this).attr('id')+3; $(this).closest('tr').find('.qty').attr('id', qty);
        var unit         = $(this).attr('id')+4; $(this).closest('tr').find('.unit').attr('id', unit);
        var warehouse    = $(this).attr('id')+5; $(this).closest('tr').find('.warehouse').attr('id', warehouse);
        var buyprice     = $(this).attr('id')+6; $(this).closest('tr').find('.price').attr('id', buyprice);
        var total        = $(this).attr('id')+7; $(this).closest('tr').find('.total').attr('id', total);
        $('#'+qty).val(0); $('#'+total).val(0);
        $.ajax({
            type: "POST",
            url: "transaction/Sales/get_unit/",		
            dataType: "JSON",
            data: {
                code: $('#'+product_code).val()
            },
            success: function(data) {                    
                $('#'+unit).html(data.option); 
                $.ajax({
                    type: "POST", 
                    url: 'transaction/Sales/get_sellprice_return', 
                    data: {
                        product_code  : $('#'+product_code).val(),
                        unit_id       : $('#'+unit).val(),
                        supplier_code : $('#customer_code').val()
                    },
                    dataType: "JSON",
                    success: function(data){
                        var buyprice_value = (data.buyprice != null) ? data.buyprice : 0;
                        $('#'+buyprice).val(format_amount(String(buyprice_value)));                                                
                        $.ajax({
                            type: "POST",
                            url: "transaction/Sales/get_warehouse_return/",
                            dataType: "JSON",
                            data: {
                                product_code    : $('#'+product_code).val(),
                                unit_id         : $('#'+unit).val()
                            },
                            success: function(data) {                                       
                                $('#'+warehouse).html(data.option);
                            }
                        }); 
                        calculate();
                    }
                });                                       
            }
        });                               
    });

    $("#product_table").on("click", ".qty, .price", function() {
        $(this).select();
    });

    $("#product_table").on("keyup", ".qty, .price", function() {
        $(this).val(format_amount($(this).val()));
    });

    $("#product_table").on("keyup", ".qty", function() {
        var id = Math.round(new Date().getTime() +(Math.random() * 100));
        $(this).attr('id', id);
        price = $(this).attr('id')+2;
        total = $(this).attr('id')+3;
        $(this).closest('tr').find('.price').attr('id',price);
        $(this).closest('tr').find('.total').attr('id',total);

        var price_value = $('#'+price).val().replace(/\,/g, "");
        var subtotal = Number($('#'+id).val()) * Number(price_value);
        $('#'+total).val(format_amount(String(subtotal)));        
        calculate();
    });

    $("#product_table").on("change", ".unit", function() {
        var id=Math.round(new Date().getTime() +(Math.random() * 100));
        $(this).attr('id', id);                
        var product_code = $(this).attr('id')+2;
        var qty          = $(this).attr('id')+3;
        var warehouse    = $(this).attr('id')+4;  
        var price        = $(this).attr('id')+5;        
        var total        = $(this).attr('id')+6;                
        $(this).closest('tr').find('.product_code').attr('id', product_code)
        $(this).closest('tr').find('.qty').attr('id', qty);
        $(this).closest('tr').find('.warehouse').attr('id', warehouse);
        $(this).closest('tr').find('.price').attr('id', price);
        $(this).closest('tr').find('.total').attr('id', total);
        $.ajax({
            type: "POST", 
            url: 'transaction/Sales/get_buyprice_return', 
            data: {
                product_code  : $('#'+product_code).val(),
                unit_id       : $('#'+id).val(),
                supplier_code : $('#customer_code').val()
            }, 
            dataType: "JSON",            
            success: function(data){ 
                var buyprice = (data.buyprice != null) ? data.buyprice : 0;                               
                $('#'+price).val(format_amount(String(buyprice)));
                var total_value = Number($('#'+qty).val()) * Number(buyprice);
                $('#'+total).val(format_amount(String(total_value)));    
                calculate();
            }
        });   
        $.ajax({
            type: "POST",
            url: "transaction/Sales/get_warehouse/",
            dataType: "JSON",
            data: {
                product_code    : $('#'+product_code).val(),
                unit_id         : $('#'+id).val()
            },
            success: function(data) {                                                       
                $('#'+warehouse).empty().html(data.option);
            }
        });                                           
    });
    
    $("#product_table").on("keyup", ".price", function() {
        var id = Math.round(new Date().getTime() +(Math.random() * 100));
        $(this).attr('id', id);
        qty = $(this).attr('id')+2;
        total = $(this).attr('id')+3;
        $(this).closest('tr').find('.qty').attr('id',qty);
        $(this).closest('tr').find('.total').attr('id',total);

        var price_value = $(this).val().replace(/\,/g, "");
        var subtotal = Number($('#'+qty).val().replace(/\,/g, "")) * Number(price_value);        
        $('#'+total).val(format_amount(String(subtotal.toFixed(2))));
        calculate();
    });

    $("#product_table").on('click', '.sellprice_history', function(){
        var id = Math.round(new Date().getTime() +(Math.random() * 100)); $(this).attr('id', id);
        var product_code = $(this).attr('id')+2;
        $(this).closest('tr').find('.product_code').attr('id', product_code);
        $("#sellprice-history-modal-body").html('');
        $.ajax({
            url: "transaction/Sales/get_sellprice_history",
            type: "POST",
            dataType: "JSON",
            data: {
                customer_code : $('#customer_code').val(),
                product_code  : $('#'+product_code).val(),
            },
            success: (data) => {                
                $("#sellprice-history-modal-body").html(data);
                $('#sellprice-history-modal').modal('show');
            },
            error: (err) => {
                console.log(err);
                console.log(err.responseText);
            }
        });        
    });

    $('#sales_invoice_id').click(function(){
        var supplier_code = $('#customer_code').val();
        $.getJSON("transaction/Sales/get_invoice_return/"+supplier_code, (data) => {
            var option = '<option value="" class="kt-font-dark">- PILIH PENJUALAN -</option>';
            if (data.status.code == 200) {
                $.each(data.response, function(i) {
                    option += '<option value="' + data.response[i].id + '" class="kt-font-dark">' + format_date(data.response[i].date) + ' | '+ data.response[i].invoice + ' | '+ String(format_amount(data.response[i].account_payable)) + '</option>';
                });
            } 
            else 
            {
                option = option;
            }
            $(this).html(option).select2();
        });                
    });

    $('#sales_invoice_id').change(function(){
        $.ajax({
            type: "POST", 
            url: 'transaction/Sales/get_account_payable', 
            data: {                    
                id : $(this).val(),                    
            }, 
            dataType: "JSON",            
            success: function(data){                     
                var total_return = $('#total_return').val().replace(/\,/g, "");
                if(Number(data.account_payable) >= Number(total_return))
                {
                    $('#account_payable').val(format_amount(String(data.account_payable)));                    
                }
                else
                {                        
                    alert('Mohon Maaf, Nilai Faktur lebih kecil daripada Total Retur. Harap pilih faktur yang lain. Terima Kasih');
                    $(this).val("");
                    $('#account_payable').val(null);
                }   
                calculate();
            },            
        });
    });
});

function calculate()
{
    var total_product = $(".qty");
    $('#total_product').val(total_product.length);
    
    var total_qty = 0;
    $('.qty').each(function(){
        total_qty += Number($(this).val());
    });
    $('#total_qty').val(total_qty);    

    var total_return = 0;
    $('.total').each(function() {
        total_return += Number($(this).val().replace(/\,/g, ""));
    });
    $('#total_return').val(format_amount(String(total_return)));

    if($('#method').val() == 2)
    {
        var grandtotal = Number($('#account_payable').val().replace(/\,/g, "")) - Number(total_return);
        $('#grandtotal').val(format_amount(String(grandtotal)));

        if(Number($('#account_payable').val().replace(/\,/g, "")) >= Number(total_return))
        {
            $('#btn_save').attr('disabled', false);
        }
        else
        {
            $('#btn_save').attr('disabled', true);            
        }
    }
}

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
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