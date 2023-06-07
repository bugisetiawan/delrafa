$(document).ready(function(){
    $('#date').datepicker({
        format: "dd-mm-yyyy",
        todayHighlight: true,
		clearBtn: false,
		autoclose: true,
		orientation: "bottom auto"
    });
    
    $('#information').on('keyup', function() {
        $(this).val($(this).val().toUpperCase());
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
			$('#add_product').click(); $('.product_input:last').focus().select();
		}
	});

    $("#product_table").on("focus", ".product_input", function(){
        var product = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            prefetch:"inventory/Product_usage/get_product",
            remote:{
                url:"inventory/Product_usage/get_product/%QUERY%/0",
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

    $("#product_table").on("change", ".product_input", function(){
        var id=Math.round(new Date().getTime()+(Math.random() * 100)); $(this).attr('id', id);
        var product_code = $(this).attr('id')+2; $(this).closest('tr').find('.product_code').attr('id', product_code);
        var qty          = $(this).attr('id')+3; $(this).closest('tr').find('.qty').attr('id', qty);
        var unit         = $(this).attr('id')+4; $(this).closest('tr').find('.unit').attr('id', unit);
        var price        = $(this).attr('id')+5; $(this).closest('tr').find('.price').attr('id', price);
        var warehouse    = $(this).attr('id')+7; $(this).closest('tr').find('.warehouse').attr('id', warehouse);
        var total        = $(this).attr('id')+8; $(this).closest('tr').find('.total').attr('id', total);
        var delete_product = $(this).attr('id')+9; $(this).closest('tr').find('.delete_product').attr('id', delete_product);
        $('#'+qty).val(0); $('#'+total).val(0);
        $.ajax({
            type: "POST",
            url: "transaction/Sales/get_unit",		
            dataType: "JSON",
            data: {
                code: $('#'+product_code).val()
            },
            success: function(data) {                    
                $('#'+unit).html(data.option); 
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
            }
        });
        calculate();
	});        
	
    $("#product_table").on("keyup", ".qty", function(){
        $(this).val(format_amount($(this).val()));
        calculate();
    });

    $("#product_table").on("change", ".unit", function(){
        var id=Math.round(new Date().getTime() + (Math.random() * 100)); $(this).attr('id', id);
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

    $("#btn_save").on("click", function(){
        if(validate_input())
        {
            $("#create_form").submit();
        }
        else
        {
            toastr.error('Harap periksa kembali, ada data wajib yang belum terisi. Terima kasih');
        }
    });
});

function calculate()
{
    var total_product = $(".qty");
    $('#total_product').val(total_product.length);
    
    var total_qty = 0;
    $('.qty').each(function(){
        total_qty += Number($(this).val().replace(/\,/g, ""));
    });
    $('#total_qty').val(total_qty);
    
    if(Number(total_product.length) > 0 && Number(total_qty) > 0)
    {
		$('#btn_save').prop("disabled", false);        
    }
    else
    {
        $('#btn_save').prop("disabled", true);
    }
}

function validate_input()
{
    var fault = 0;
    $('input[required], textarea[required], option[required]', '#create_form').each(function(){
        if(this.value.trim() == ''){
            fault++;
        }
	});	
    $('.qty').each(function(){
		if(Number($(this).val().replace(/\,/g, "")) <= 0)
		{
			fault++;
		}
    });
    $('.unit').each(function(){        
		if(Number($(this).val()) <= 0)
		{
			fault++;
		}
    });
    $('.warehouse').each(function(){
		if(Number($(this).val()) <= 0)
		{
			fault++;
		}
    });
    return (fault == 0) ? true : false;
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