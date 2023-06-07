$(document).ready(function(){
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
                url:"transaction/Purchase/get_product/%QUERY%/",
                wildcard:"%QUERY%"
            }
        });        
        if( $(this).data('autocomple_ready') != 'ok' ){            
            var id = Math.round(new Date().getTime() +(Math.random() * 100));
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
            });               
            $('#'+id).bind('typeahead:change', function(ev, suggestion) {
                $('#'+product_code).val(suggestion['code']);
            });
        };        
        $(this).data('autocomple_ready','ok');                        
    });

    $("#product_table").on( "change", ".product_input", function(){        
        var id=Math.round(new Date().getTime()+(Math.random() * 100));
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
        var id=Math.round(new Date().getTime() + (Math.random() * 100));
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
            }
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

    var grandtotal = 0;
    $('.total').each(function() {
        grandtotal += Number($(this).val().replace(/\,/g, ""));
    });    

    if(grandtotal > 0)
    {
        if($('#payment').val() == 2)
        {
            $('#account_payable').val(format_number(String(Number($('#down_payment').val().replace(/\,/g, "")) - Number(grandtotal))));
            if(Number($('#down_payment').val().replace(/\,/g, "")) > Number(grandtotal))
            {
                $('#btn_save').attr('disabled', true);
            }
            else
            {
                $('#btn_save').attr('disabled', false);
            }
        }
        else
        {        
            $('#down_payment, #account_payable').val(0);
            $('#btn_save').attr('disabled', false);
        }        
    }
    else
    {
        $('#btn_save').attr('disabled', true);
    }
    $('#grandtotal').val(format_number(String(grandtotal)));
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