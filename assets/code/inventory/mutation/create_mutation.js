$(document).ready(function() {    	  
    $('#date').datepicker({
        format: "dd-mm-yyyy",
        todayHighlight: true,
		clearBtn: true,
		autoclose: true,
		orientation: "bottom auto"
    });

    $.getJSON("transaction/Sales/get_employee", (data) => {
        var option = '<option value="" class="kt-font-dark">- PILIH PETUGAS -</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i) {
                option += '<option value="' + data.response[i].code + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
            });
        } else {
            option = option;
        }
        $("#checker_code").html(option).select2();
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
        var product_code = $(this).attr('id')+2;
        var qty          = $(this).attr('id')+3;
        var unit         = $(this).attr('id')+4;                
        var from_warehouse  = $(this).attr('id')+5;
        var to_warehouse    = $(this).attr('id')+6;
        $(this).closest('tr').find('.product_code').attr('id', product_code);
        $(this).closest('tr').find('.qty').attr('id', qty);
        $(this).closest('tr').find('.unit').attr('id', unit);
        $(this).closest('tr').find('.from_warehouse').attr('id', from_warehouse);
        $(this).closest('tr').find('.to_warehouse').attr('id', to_warehouse);
        $('#'+qty).val(0);
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
                        $('#'+from_warehouse).html(data.option);
                        $.ajax({
                            type: "POST",
                            url: "inventory/Mutation/get_to_warehouse_mutation/",
                            data: {
                                product_code  : $('#'+product_code).val(),
                                unit_id       : $('#'+unit).val(),
                                from_warehouse_id : $('#'+from_warehouse).val()
                            },
                            dataType: "JSON",
                            success: function(data) {
                                $('#'+to_warehouse).html(data.option);
                            }
                        });                        
                    }
                });                                    
            }
        });
        calculate();
    });

    $("#product_table").on("click", ".qty", function() {
        $(this).select();
    });

    $("#product_table").on("keyup", ".qty", function() {
        $(this).val(format_number($(this).val()));
        calculate();
    });

    $("#product_table").on("change", ".unit", function() {
        var id=Math.round(new Date().getTime() + (Math.random() * 100)); $(this).attr('id', id);                
        var product_code = $(this).attr('id')+2;
        var from_warehouse = $(this).attr('id')+3;
        var to_warehouse   = $(this).attr('id')+4;
        $(this).closest('tr').find('.product_code').attr('id', product_code);
        $(this).closest('tr').find('.from_warehouse').attr('id', from_warehouse);
        $(this).closest('tr').find('.to_warehouse').attr('id', to_warehouse);
        $.ajax({
            type: "POST",
            url: "transaction/Sales/get_warehouse/",
            data: {
                product_code  : $('#'+product_code).val(),
                unit_id       : $('#'+id).val()
            },
            dataType: "JSON",
            success: function(data) {                                       
                $('#'+from_warehouse).html(data.option);
                $.ajax({
                    type: "POST",
                    url: "inventory/Mutation/get_to_warehouse_mutation/",
                    data: {
                        product_code  : $('#'+product_code).val(),
                        unit_id       : $('#'+id).val(),
                        from_warehouse_id : $('#'+from_warehouse).val()
                    },
                    dataType: "JSON",
                    success: function(data) {
                        $('#'+to_warehouse).html(data.option);
                    }
                });
            }
        });
        calculate();
    });    

    $("#product_table").on("change", ".from_warehouse", function() {
        var id=Math.round(new Date().getTime() + (Math.random() * 100)); $(this).attr('id', id);                
        var product_code = $(this).attr('id')+2;
        var unit = $(this).attr('id')+3;
        var to_warehouse   = $(this).attr('id')+4;
        $(this).closest('tr').find('.product_code').attr('id', product_code);
        $(this).closest('tr').find('.unit').attr('id', unit);
        $(this).closest('tr').find('.to_warehouse').attr('id', to_warehouse);
        $.ajax({
            type: "POST",
            url: "inventory/Mutation/get_to_warehouse_mutation/",
            data: {
                product_code  : $('#'+product_code).val(),
                unit_id       : $('#'+unit).val(),
                from_warehouse_id : $('#'+id).val()
            },
            dataType: "JSON",
            success: function(data) {
                $('#'+to_warehouse).html(data.option);
            }
        });
        calculate();
    });

    $("#btn_save").on("click", function(){
        if(validate_input())
        {
            $("#create_form").submit();
        }
        else
        {
            swal.fire('Harap periksa kembali, ada data wajib yang belum terisi. Terima kasih');
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

    if(Number(total_qty) > 0)
    {   
        $('#btn_save').attr('disabled', false);             
    }
    else
    {
        $('#btn_save').attr('disabled', true);
    }
}

function validate_input()
{
    var res = 0;
    $(':input[required], option[required]', '#create_form' ).each(function(){        
        if(this.value.trim() == ''){
            res++;
        }                
    });
    if(res == 0)
    {
        return true;
    }
    else
    {
        return false;
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