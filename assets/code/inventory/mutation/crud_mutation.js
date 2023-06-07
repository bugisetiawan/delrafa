jQuery(document).ready(function() {    	  
    $('#date').datepicker({
        format: "dd-mm-yyyy",
        todayHighlight: true,
		clearBtn: true,
		autoclose: true,
		orientation: "bottom auto"
    });

    $.getJSON("transaction/Sales/get_employee", (data) => {
        var option = '<option value="" class="kt-font-dark">-- PILIH KARYAWAN --</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i) {
                option += '<option value="' + data.response[i].code + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
            });
        } else {
            option = option;
        }
        $("#employee_code").html(option).select2();
	});
      
    $.getJSON("master/Warehouse/get_warehouse", (data) => {
        var option = '<option value="" class="kt-font-dark">-- PILIH GUDANG ASAL --</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i) {
                option += '<option value="' + data.response[i].id + '" class="kt-font-dark">' + data.response[i].code+ ' | '+ data.response[i].name + '</option>';
            });
        } else {
            option = option;
        }
        $("#from_warehouse_id").html(option).select2();
    });
    
    $("#from_warehouse_id").change(function() {
        var from_warehouse_id = $(this).val();
        $.ajax({
            url: "transaction/Stock/get_to_warehouse",
            type: "GET",
            dataType: 'JSON',
            data: {
                from_warehouse_id: from_warehouse_id
            },
            success: (data) => {
                var option = '<option value="">-- PILIH GUDANG TUJUAN --</option>';
                if (data.status.code == 200) {
                    $.each(data.response, function(i) {
                        option += '<option value="' + data.response[i].id + '">' + data.response[i].code +  ' | '+ data.response[i].name + '</option>';
                    });
                } else {
                    option = option;
                }
                $("#to_warehouse_id").html(option).select2();
            },
            error: (err) => {
                console.log(err);
                console.log(err.responseText);
            }
        });
    });
    
    $('.repeater').repeater({
        initEmpty: false,  
        isFirstItemUndeletable: true,
        hide: function () {
            $(this).remove();
            calculate();
        }
    });

    document.onkeydown = function (e){
		switch (e.keyCode){
			// Enter
			case 13:
                event.preventDefault();
                $('#add_product').click();
                $('.product_input:last').focus();
                $('.product_input:last').select();
        }
    };

    $("#product_table").on("focus", ".product_input", function(){
        var product = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            prefetch:"transaction/Stock/get_product",
            remote:{
                url:"transaction/Stock/get_product/%QUERY%/",
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
                limit: 50,
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
        var unit         = $(this).attr('id')+3;
        $(this).closest('tr').find('.product_code').attr('id', product_code);
        $(this).closest('tr').find('.unit').attr('id', unit);
        $.ajax({
            type: "POST",
            url: "transaction/Stock/get_unit/",
            dataType: "JSON",
            data: {
                code: $('#'+product_code).val()
            },
            success: function(data) {
                $('#'+unit).html(data.option);
            }
        });
        calculate();
    });

    $("#product_table").on("click", ".qty", function() {
        $(this).select();
    });

    $("#product_table").on("keyup", ".qty", function() {        
        calculate();
    });
});

function calculate()
{
    var total_product = $(".unit");
    $('#total_product').val(total_product.length);

    var total_qty = 0;
    $('.qty').each(function(){
        total_qty += Number($(this).val());
    });
    $('#total_qty').val(total_qty);
}