$(document).ready(function() {                
    $('#barcode, #name, #productid').keyup(function() {
        $(this).val($(this).val().toUpperCase());   
	});  
	
	$("#refresh_department").click(function(){
        $.getJSON("master/department/get_department", (data) => {
			var option = '<option value="" class="kt-font-dark">- PILIH DEPARTEMEN -</option>';
			if (data.status.code == 200) {
				$.each(data.response, function(i, item) {
					option += '<option value="' + data.response[i].code + '" class="kt-font-dark">' + data.response[i].name + '</option>';
				});
			} else {
				option = option;
			}
            $("#department_code").html(option).select2();
            $("#product_code").html("<small>Silahkan Isi Departemen dan Sub Departemen</small>");
		});
	});

    $.getJSON("master/department/get_department", (data) => {
        var option = '<option value="" class="kt-font-dark">- PILIH DEPARTEMEN -</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i, item) {
                option += '<option value="' + data.response[i].code + '" class="kt-font-dark">' + data.response[i].name + '</option>';
            });
        } else {
            option = option;
        }
        var value = $('#department_code_update').val();
        if (value == null) 
        {                      
            $("#department_code").html(option).select2();
        }		
        else
        {             
            $("#department_code").html(option).val(value).select2().trigger('change');
        }	
        
	});	
	
    $("#department_code").change(function() {
        var code_depart = $(this).val();        
        $.ajax({
            url: "master/department/get_sub",
            type: "GET",
            dataType: 'JSON',
            data: {
                code_depart: code_depart
            },
            success: (data) => {
                var option = '<option value="">- PILIH SUBDEPARTEMEN -</option>';
                if (data.status.code == 200) {
                    $.each(data.response, function(i, item) {
                        option += '<option value="' + data.response[i].code + '">' + data.response[i].name + '</option>';
                    });
                } else {
                    option = option;
                }
                var value = $('#subdepartment_code_update').val();
                if (value == null)
                {
                    $("#subdepartment_code").html(option).select2();
                }
                else
                {
                    $("#subdepartment_code").html(option).val(value).select2().trigger('change');
                }
            },
            error: (err) => {
                alert(err);
                alert(err.responseText);
            }
        });
    });

    $("#refresh_unit").click(function(){
        $.getJSON("master/unit/get_unit", (data) => {        
            var option = '<option value="">- PILIH SATUAN -</option>';
            if (data.status.code == 200) {
                $.each(data.response, function(i, item) {
                    option += '<option value="' + data.response[i].id + '">' + data.response[i].name + '</option>';
                });
            } else {
                option = option;
            }
            $(".unit_select").html(option).select2();
        });    
	});

    $.getJSON("master/unit/get_unit", (data) => {        
        var option = '<option value="">- PILIH SATUAN -</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i, item) {
                option += '<option value="' + data.response[i].id + '">' + data.response[i].name + '</option>';
            });

            var value = $('#unit_id_update').val();
			$('#unit_id').val(value);
			if (value==null) 
			{				
				$('#unit_id').html(option).select2();
			}		
			else
			{
                $(".unit_select").html(option).select2();
                $('#unit_id').html(option).val(value).select2();
                
			}            
        } 
        else 
        {
            option = option;
        }        
    });    

    $("#minimal, #maximal, #weight, #commission_sales").click(function(){
        $(this).select();
    });

    $("#photo").change(function(){
        previewPhoto(this);
    });

    if($("#product_type").val() == 1)
    {
        $('#bundle_form').hide();
    }
    else
    {
        $('#bundle_form').show();    
    }    
    $("#product_type").change(function(){
        if($(this).val() == 1)
        {
            $('#bundle_form').hide();
            $('.product_input, .product_code, .qty, .unit').prop( "required", false);
        }
        else
        {
            $('#bundle_form').show();            
            $('.product_input, .product_code, .qty, .unit').prop( "required", true);
        }
    });    

    $('.repeater').repeater({
        initEmpty: false,  
        isFirstItemUndeletable: true,
        hide: function () {
            $(this).remove();
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
            var id = Math.round(new Date().getTime() +(Math.random() * 100))
            $(this).attr('id',id); $('#'+id).prop( "required", true);
            var product_code =$(this).attr('id')+2;
            $(this).closest('tr').find('.product_code').attr('id', product_code); $('#'+product_code).prop( "required", true);
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
                    suggestion: Handlebars.compile('<div>{{barcode}}<br>{{code}}<br>{{name}}</div>')
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
        $(this).closest('tr').find('.product_code').attr('id', product_code);
        $(this).closest('tr').find('.qty').attr('id', qty);
        $(this).closest('tr').find('.unit').attr('id', unit); 
        $('#'+qty).prop( "required", true); $('#'+unit).prop( "required", true);       
        $('#' + qty).val(null);        
        $.ajax({
            type: "POST",
            url: "transaction/Purchase/get_unit/",		
            dataType: "JSON",
            data: {
                code: $('#'+product_code).val()
            },
            success: function(data) {                    
                $('#'+unit).html(data.option);                                                       
            }
        });
    });

    $('.price').on('keyup',function(){
        $(this).val(format_amount($(this).val()));        
    }); 
    
    $('.price').on('click', function() {
        $(this).select();
    });

});

function previewPhoto(input){
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {
			$('#preview').attr('src', e.target.result);
		}
		reader.readAsDataURL(input.files[0]);
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