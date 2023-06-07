$(document).ready(function() {       
    $('#date').datepicker({        
		format: "dd-mm-yyyy",		
		todayHighlight: true,
		clearBtn: true,
		autoclose: true,
		orientation: "bottom auto"	       
    });    

    $.getJSON("transaction/Sales/get_employee", (data) => {
        var option
        if (data.status.code == 200) {
            $.each(data.response, function(i) {
                option += '<option value="' + data.response[i].code + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
            });
        } else {
            option = option;
        }        
        var value = ($('#sales_code_update').val() != null) ? $('#sales_code_update').val() : null;
        $('#sales_code').html(option).val(value).select2();
    });

    $.getJSON("transaction/Sales/get_customer", (data) => {
        var option;
        if (data.status.code == 200) {
            $.each(data.response, function(i) {
                option += '<option value="' + data.response[i].code + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
            });
        } else {
            option = option;
        }        
        var value = ($('#customer_code_update').val() != null) ? $('#customer_code_update').val() : null;
        $('#customer_code').html(option).val(value).select2();
    });

    $('#delivery').hide();
    $("#customer_code").change(function(){
		if($(this).val() != "")
		{
            $('#taking').prop( "disabled", false);
            if($('#taking').val() == 1)
            {
                $('#delivery').hide('slow');
                $('#delivery_address').prop( "required", false);
                $('#delivery_address').val(null);
            }
            else
            {
                $('#delivery').show('slow');
                $('#delivery_address').prop( "required", true);
                $.ajax({
                    type: "POST", 
                    url: 'transaction/Sales/check_customer',
                    data: {
                        customer_code : $('#customer_code').val(),
                    }, 
                    dataType: "json",
                    beforeSend: function(e) {
                        if(e && e.overrideMimeType) {
                            e.overrideMimeType("application/json;charset=UTF-8");
                        }
                    },
                    success: function(data){
                        $('#delivery_address').val(data['address']);
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            }
		}	
		else
		{
            $('#taking').prop( "disabled", true);
        } 
    });

    var taking_method_update = ($('#taking_method_update').val() != null) ? $('#taking_method_update').val() : null;
    if(taking_method_update != null)
    {
        $('#taking').val(taking_method_update);
        if(taking_method_update == 1)
        {
            $('#delivery').hide('slow');
        }
        else
        {
            $('#delivery').show('slow');
        }
    }   

    $('#taking').change(function(){
        if($(this).val() == 1)
        {
            $('#delivery').hide('slow');
            $('#delivery_address').prop( "required", false);
            $('#delivery_address').val(null);
        }
        else
        {
            $('#delivery').show('slow');
            $('#delivery_address').prop( "required", true);
            $.ajax({
                type: "POST", 
                url: 'transaction/Sales/check_customer',
                data: {
                    customer_code : $('#customer_code').val(),
                }, 
                dataType: "json",
                beforeSend: function(e) {
                    if(e && e.overrideMimeType) {
                        e.overrideMimeType("application/json;charset=UTF-8");
                    }
                },
                success: function(data){
                    $('#delivery_address').val(data['address']);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
        }
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
            prefetch:"transaction/Sales/get_product",
            remote:{
                url:"transaction/Sales/get_product/%QUERY%/",
                wildcard:"%QUERY%"
            }
        });        
        if( $(this).data('autocomple_ready') != 'ok' ){            
            var id = Math.round(new Date().getTime() +(Math.random() * 100))
            $(this).attr('id',id);
            var product_code =$(this).attr('id')+1;
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
        var product_code = $(this).attr('id')+2;
        var qty          = $(this).attr('id')+3;
        var unit         = $(this).attr('id')+4;
        var price        = $(this).attr('id')+5;
        var warehouse    = $(this).attr('id')+6;                
        var total        = $(this).attr('id')+7;
        $(this).closest('tr').find('.product_code').attr('id', product_code);
        $(this).closest('tr').find('.qty').attr('id', qty);
        $(this).closest('tr').find('.unit').attr('id', unit);
        $(this).closest('tr').find('.price').attr('id', price);
        $(this).closest('tr').find('.warehouse').attr('id', warehouse);        
        $(this).closest('tr').find('.total').attr('id', total);        
        $('#'+qty).val(0);$('#'+total).val(0);
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
                    url: 'transaction/Sales/get_sellprice', 
                    data: {
                        product_code  : $('#'+product_code).val(),
                        unit_id       : $('#'+unit).val(),
                        customer_code : $('#customer_code').val()
                    },
                    dataType: "JSON",
                    success: function(data){
                        var sellprice = (data.sellprice != null) ? data.sellprice : 0;
                        $('#'+price).val(format_number(String(sellprice)));
                        $.ajax({
                            type: "POST",
                            url: "transaction/Sales/get_warehouse/",
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
        var id=Math.round(new Date().getTime() +(Math.random() * 100));
        $(this).attr('id', id);                
        var qty         = $(this).attr('id')+2;
        var price       = $(this).attr('id')+3;        
        var warehouse   = $(this).attr('id')+4;
        var total       = $(this).attr('id')+5;
        $(this).closest('tr').find('.qty').attr('id', qty);
        $(this).closest('tr').find('.price').attr('id', price);
        $(this).closest('tr').find('.warehouse').attr('id', warehouse);
        $(this).closest('tr').find('.total').attr('id', total);
        $.ajax({
            type: "POST", 
            url: 'transaction/Sales/get_sellprice', 
            data: {
                product_code  : $(this).closest('tr').find('.product_code').val(),
                unit_id       : $(this).val(),
                customer_code : $('#customer_code').val()
            }, 
            dataType: "JSON",            
            success: function(data){ 
                var sellprice = (data.sellprice != null) ? data.sellprice : 0;
                $('#'+price).val(format_number(String(sellprice)));
                var total_value = Number($('#'+qty).val()) * Number(sellprice);
                $('#'+total).val(format_number(String(total_value)));
                calculate();
            },            
        });
        $.ajax({
            type: "POST",
            url: "transaction/Sales/get_warehouse/",
            data: {
                product_code  : $(this).closest('tr').find('.product_code').val(),
                unit_id       : $(this).val(),
            },
            dataType: "JSON",
            success: function(data) {                                       
                $('#'+warehouse).html(data.option);                        
            }
        });
    });

    $("#product_table").on("click", ".qty, .price", function() {
        $(this).select();
    });
    
    $("#product_table").on("keyup", ".qty, .price", function() {
        $(this).val(format_number($(this).val()));
    });

    $("#product_table").on("keyup", ".qty", function() {
        var id = Math.round(new Date().getTime() +(Math.random() * 100));
        $(this).attr('id', id);
        price = $(this).attr('id')+2;
        total = $(this).attr('id')+3;
        $(this).closest('tr').find('.price').attr('id',price);
        $(this).closest('tr').find('.total').attr('id',total);

        var price_value = $('#'+price).val().replace(/\,/g, "");
        var subtotal = Number($('#'+id).val().replace(/\,/g, "")) * Number(price_value);
        $('#'+total).val(format_number(String(subtotal)));        
        calculate();
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
        $('#'+total).val(format_number(String(subtotal)));        
        calculate();
    });

    $("#discount_p, #discount_rp").on("click",function() {
        $(this).select();
    });
    $("#discount_rp").on("keyup",function() {
        $(this).val(format_number($(this).val()));
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
            alert("Mohon Maaf, maksimal diskon adalah 100%. Silahkan isi ulang kembali, terima kasih.");
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
            alert("Mohon Maaf, maksimal diskon adalah 100%. Silahkan isi ulang kembali, terima kasih.");
            $(this).select();
        }
        calculate();
    });
});

function calculate()
{
    var total_product = $(".qty");
    $('#total_product').val(total_product.length-1);
    
    var total_qty = 0;
    $('.qty').each(function(){
        total_qty += Number($(this).val().replace(/\,/g, ""));
    });
    $('#total_qty').val(total_qty);

    var subtotal = 0;
    $('.total').each(function() {
        subtotal += Number($(this).val().replace(/\,/g, ""));
    });
    $('#subtotal').val(format_number(String(subtotal)));

    var grandtotal = Number(subtotal) - Number($('#discount_rp').val().replace(/\,/g, ""));   

    $('#grandtotal').val(format_number(String(grandtotal)));
    if(grandtotal > 0)
    {
        $('#btn_save').attr('disabled', false);
    }
    else
    {
        $('#btn_save').attr('disabled', true);
    }
}


function format_number(angka, prefix){
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