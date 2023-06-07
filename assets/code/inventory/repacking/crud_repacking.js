$(document).ready(function(){
    $('#date').datepicker({
        format: "dd-mm-yyyy",
        todayHighlight: true,
		clearBtn: true,
		autoclose: true,
		orientation: "bottom auto"
    });

    // $.getJSON("master/Employee/get_employee", (data) => {
    //     var option = '<option value="" class="kt-font-dark">- PILIH KARYAWAN -</option>';
    //     if (data.status.code == 200) {
    //         $.each(data.response, function(i) {
    //             option += '<option value="' + data.response[i].code + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
    //         });
    //     } else {
    //         option = option;
    //     }
    //     $("#repacker").html(option).select2();
    // });
    
    $('.repeater').repeater({
        initEmpty: false,  
        isFirstItemUndeletable: true,
        hide: function () {
            $(this).remove();            
        }
    });

    $(".product_table").keypress(function(e){
		var key = e.which;
		if(key == 13)
		{			
			e.preventDefault();
			$('#add_product').click();
			$('.product_input:last').focus();
			$('.product_input:last').select();		   
		}
	});    

    $(".product_table").on("focus", ".product_input", function(){
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
                limit: 100,
                templates: {
                    empty: [
                        '<div class="empty-message" style="padding: 10px 15px; text-align: center; font-color:red;">',
                            'PRODUK TIDAK ADA',
                        '</div>'
                    ].join('\n'),
                    suggestion: Handlebars.compile('<div>{{barcode}}<br>{{code}}<br>{{name}}</div>')
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

    $(".product_table").on( "change", ".product_input", function(){        
        var id=Math.round(new Date().getTime() +(Math.random() * 100));
        $(this).attr('id', id);
        var product_code = $(this).attr('id')+2;
        var qty          = $(this).attr('id')+3;
        var unit         = $(this).attr('id')+4;
        var warehouse    = $(this).attr('id')+5;
        $(this).closest('tr').find('.product_code').attr('id', product_code);
        $(this).closest('tr').find('.qty').attr('id', qty);
        $(this).closest('tr').find('.unit').attr('id', unit);
        $(this).closest('tr').find('.warehouse').attr('id', warehouse);
        $('#' + qty).val(null);        
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
        $.ajax({
            type: "POST",
            url: "transaction/Stock/get_product_warehouse/",
            dataType: "JSON",
            success: function(data) {                                       
                $('#'+warehouse).html(data.option);
            }
        });
    });               

    $(".product_table").on("click", ".qty", function() {
        $(this).select();
    });
});