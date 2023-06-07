$(document).ready(function(){    
	setInterval(clock, 1000);
	
    $.getJSON("transaction/Sales/get_customer", (data) => {
        var option;
        if (data.status.code == 200) {
            $.each(data.response, function(i) {
                option += '<option value="' + data.response[i].code + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
            });
        } else {
            option = option;
        }        
        $("#customer_code").html(option).val('CUST-00000').select2();
	});
	
	$(document).keydown(function(e){
		switch(e.keyCode) 
		{			
			case 113: // F2 (PRODUCT FOCUS)
				event.preventDefault(); $('#search_product').focus(); break;
			
			case 35: // END (PAYMENT BUTTON) // Cancel the default action, if needed			
				event.preventDefault(); $('#payment_btn').click(); $('#pay').focus().select(); break;
		}
	});

    $('#product_table').dataTable({
        searching : false,
        sort : false,
        responsive: true,
        processing: true,
        language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>',
            "zeroRecords": " "
        },
        paging : false,
        info : false,
        scrollY: '50vh',
        scrollCollapse: false,
    });
	
	$('#search_product').ready(function(){
        var product = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            prefetch:"pos/Cashier/get_product",
            remote:{
                url:"pos/Cashier/get_product/%QUERY%/",
                wildcard:"%QUERY%"
            }
        });        
		if( $(this).data('autocomple_ready') != 'ok' )
		{                        
            $('#search_product').typeahead({
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
                    suggestion: Handlebars.compile('<div class="text-center">{{name}}<br>{{price_1}}</div>')
                }
            });
            $('#search_product').bind('typeahead:select', function(ev, suggestion) {
				$('#search_product').typeahead('val', suggestion['code']);
			});
        };        
		$(this).data('autocomple_ready','ok');
	});

    $("#search_product").on('keyup', function(e){
        if(event.keyCode == 13) 
        {
			add_product($(this).val());
		}
    });

	$("#product_table").on("click", ".qty, .price, .discount_p", function() {
        $(this).select();
	});

    $("#product_table").on("keyup", ".qty, .price, .discount_p", function() {
        $(this).val(format_number($(this).val()));
    });

    $("#product_table").on("input", ".qty", function() {
		$(this).attr('id',Math.round(new Date().getTime() +(Math.random() * 100)));
		var qty   =$(this).attr('id')
		var price =$(this).attr('id')+1;
		var discount_p =$(this).attr('id')+2;
        var total =$(this).attr('id')+3;
		$(this).closest('tr').find('.price').attr('id', price);
		$(this).closest('tr').find('.discount_p').attr('id', discount_p);
        $(this).closest('tr').find('.total').attr('id', total);
		subtotal = Number($('#'+qty).val().replace(/\,/g, ""))*Number($('#'+price).val().replace(/\,/g, ""));
		var end = subtotal - (subtotal*Number($('#'+discount_p).val().replace(/\,/g, ""))/100);
		$('#'+total).val(format_number(String(end)));
		calculate();
    });

    $("#product_table").on("change",".unit",function() {
		let product_code = $(this).closest('tr').find('.product_code').val();
		var qty 		 = $(this).closest('tr').find('.qty');
		let unit_id		 = $(this).val();
		let price	     = $(this).closest('tr').find('.price');
		var total		 = $(this).closest('tr').find('.total');
		$.ajax({
			type: "POST", 
			url: 'pos/Cashier/get_sellprice', 
			data: {
				customer_code : $('#customer_code').val(),
				product_code  : product_code,
				unit_id       : unit_id
			}, 
			dataType: "json",
			beforeSend: function(e) {
				if(e && e.overrideMimeType) {
					e.overrideMimeType("application/json;charset=UTF-8");
				}
			},
			success: function(data){ 
				price.val(format_number(String(data.price)));
				var hitung=Number(data.price)*Number(qty.val().replace(/\,/g, "")); total.val(format_number(String(hitung)));
				calculate();							

			},
			error: function (xhr, ajaxOptions, thrownError) {				
				alert.log(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
			}
		});
    });

	$("#product_table").on("change", ".price", function() {
		$(this).attr('id',Math.round(new Date().getTime() +(Math.random() * 100)));
        var price  = $(this).attr('id')
        var product_code  = $(this).attr('id')+1;
		var qty    = $(this).attr('id')+2;
		var unit   = $(this).attr('id')+3;
        var total  = $(this).attr('id')+4;        
        $(this).closest('tr').find('.product_code').attr('id',product_code);
        $(this).closest('tr').find('.price').attr('id',price);
        $(this).closest('tr').find('.qty').attr('id',qty);
		$(this).closest('tr').find('.unit').attr('id',unit);
        $(this).closest('tr').find('.total').attr('id',total);
		$.ajax({
            type: "POST", 
            url: 'pos/Cashier/check_sellprice', 
            data: {
                product_code  : $('#'+product_code).val(),
				unit_id       : $('#'+unit).val(),
				price 		  : Number($('#'+price).val().replace(/\,/g, ""))
            }, 
            dataType: "JSON",
            success: function(data){ 
                var subtotal = 0;
				if(Number(data) != 0)
				{
					subtotal = Number($('#'+qty).val().replace(/\,/g, ""))*Number($('#'+price).val().replace(/\,/g, ""));
				}
				else
				{
                    alert("Mohon maaf, kesalahan harga jual, harap periksa kembali. Terima Kasih");
                    $('#'+price).val(0);
					$('#'+price).select();
				}
				$('#'+total).val(format_number(String(subtotal)));
				calculate();
            }
        });   
	});
	
	$("#product_table").on("input", ".discount_p", function() {
		$(this).attr('id',Math.round(new Date().getTime() +(Math.random() * 100)));
		var qty   =$(this).attr('id')
		var price =$(this).attr('id')+1;
		var discount =$(this).attr('id')+2;
		var total =$(this).attr('id')+3;
		$(this).closest('tr').find('.qty').attr('id', qty);
		$(this).closest('tr').find('.discount_p').attr('id', discount);
        $(this).closest('tr').find('.price').attr('id', price);
		$(this).closest('tr').find('.total').attr('id', total);
		var subtotal = Number($('#'+qty).val().replace(/\,/g, ""))*Number($('#'+price).val().replace(/\,/g, ""));
		var end = subtotal - (subtotal*Number($('#'+discount).val().replace(/\,/g, ""))/100);
		if(Number($('#'+discount).val().replace(/\,/g, "")) >= 0 && Number($('#'+discount).val().replace(/\,/g, "")) <= 100)
		{			
			$('#'+total).val(format_number(String(end)));
		}
		else
		{
			alert('Maaf, maksimal diskon adalah 100%');
			$('#'+discount).val(0).select();			
			$('#'+total).val(format_number(String(subtotal)));
		}		
		calculate();
	});
	
    $("#product_table").on("click", ".delete_product", function(){
		var row = this.parentNode.parentNode;
        row.parentNode.removeChild(row);	
        calculate();		
	});
	
	$('#payment').change(function(){
		if($(this).val() == 1 || $(this).val() == 2 )
		{
			$('#bank_form').show();
			$('#bank_id').hide();
			$.ajax({
				type: "GET", 
				url: 'pos/Cashier/get_bank',
				dataType: "json",
				beforeSend: function(e) {
					if(e && e.overrideMimeType) {
						e.overrideMimeType("application/json;charset=UTF-8");
					}
				},
				success: function(response){ 
					$('#bank_id').html(response.list_bank);
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
				}
			});
			$('#bank_id').show();
		}
		else
		{			
			$('#bank_form').hide();
		}
	});
	
	$('#pay').click(function(){
		$(this).select();
	});	

	$('#pay').keyup(function() {
		$(this).val(format_number(String($(this).val())));
		var kembalian = Number($(this).val().replace(/\,/g, "")) - Number($('#grandtotal').val());
		if(pay==0 || kembalian < 0)
		{
			$('#kembalian').val(0);
			$("#btn-save").attr("disabled", true);
		}
		else
		{			
			$('#kembalian').val(format_number(String(kembalian)));
			$("#btn-save").attr("disabled", false);			
		}		 
	});

	$(".btn-money").click(function(){
		var ammount = $(this).val();
		var pay = Number($('#pay').val().replace(/\,/g, "")) + Number(ammount); $('#pay').val(format_number(String(pay)));
		var kembalian = pay - Number($('#grandtotal').val());
		if(pay==0 || kembalian < 0)
		{
			$('#kembalian').val(0);
			$("#btn-save").attr("disabled", true);
		}
		else
		{
			$('#kembalian').val(format_number(String(kembalian)));
			$("#btn-save").attr("disabled", false);
		}
	});

	$("#delete-money").click(function(){
		var pay = 0; $('#pay').val(format_number(String(pay)));
		var kembalian = pay - Number($('#grandtotal').val());
		if(pay==0 || kembalian < 0)
		{
			$('#kembalian').val(0);
			$("#btn-save").attr("disabled", true);
		}
		else
		{
			$('#kembalian').val(format_number(String(kembalian)));
			$("#btn-save").attr("disabled", false);
		}
	});

	$('#payment_loading').hide();
	$('#btn-save').click(function(){
		$('#payment_loading').show(); this.disabled=true; this.form.submit();
	});		
});

function clock()
{
    var waktu = new Date();	
    function plus0(number)
    {
        number = number < 10 ? '0'+ number : number;
        return number;
    }
	document.getElementById("hour").innerHTML = plus0(waktu.getHours());
	document.getElementById("minute").innerHTML = plus0(waktu.getMinutes());
	document.getElementById("second").innerHTML = plus0(waktu.getSeconds());
}

function add_product(product_code)
{
	$.ajax({
		type: "POST", 
		url: 'pos/Cashier/scan_product', 
		data: {
			customer_code : $('#customer_code').val(),
			product_code  : product_code
		}, 
		dataType: "json",
		beforeSend: function(e) {
			if(e && e.overrideMimeType) {
				e.overrideMimeType("application/json;charset=UTF-8");
			}
		},
		success: function(data){ 
			if(data.status.code == 200)
			{
				let unit_id=Math.round(new Date().getTime() +(Math.random() * 100));
				if($('#feature_discount_p').prop('checked') == true)
				{
					var html = '<tr><td class="text-center text-dark">'+ data.data.code_p +'<input type="hidden" class="text-dark product_code" name="product_code[]" value="'+ data.data.code_p +'" required></td><td class="text-dark">'+ data.data.name_p +'</td><td><input type="text" class="form-control form-control-sm text-dark text-right qty" name="qty[]" value="1" required></td><td><select class="form-control text-left unit" name="unit_id[]" id="'+unit_id+'" required></select></td><td><input type="text" class="form-control form-control-sm text-dark text-right price" name="price[]" value="'+ format_number(String(data.data.price)) +'" required></td><td><input type="text" class="form-control form-control-sm text-dark text-right discount_p" name="discount_p[]" value="0"></td><td><input type="text" class="form-control form-control-sm text-dark text-right total" name="total[]" value="'+ format_number(String(data.data.price)) +'" readonly></td><td class="text-center"><a href="javascript:void(0);" class="text-danger text-center kt-font-bold delete_product"><i class="fa fa-times"></i></a></tr>';
				}
				else
				{
					var html = '<tr><td class="text-center text-dark">'+ data.data.code_p +'<input type="hidden" class="text-dark product_code" name="product_code[]" value="'+ data.data.code_p +'" required></td><td class="text-dark">'+ data.data.name_p +'</td><td><input type="text" class="form-control form-control-sm text-dark text-right qty" name="qty[]" value="1" required></td><td><select class="form-control text-left unit" name="unit_id[]" id="'+unit_id+'" required></select></td><td><input type="text" class="form-control form-control-sm text-dark text-right price" name="price[]" value="'+ format_number(String(data.data.price)) +'" required></td><td><input type="text" class="form-control form-control-sm text-dark text-right discount_p" name="discount_p[]" value="0" readonly></td><td><input type="text" class="form-control form-control-sm text-dark text-right total" name="total[]" value="'+ format_number(String(data.data.price)) +'" readonly></td><td class="text-center"><a href="javascript:void(0);" class="text-danger text-center kt-font-bold delete_product"><i class="fa fa-times"></i></a></tr>';
				}
				$(html).prependTo($('#product_table'));
				get_unit(data.data.code_p, unit_id);
				if(data.data.photo == null || data.data.photo == "")
				{
					$('#product_photo').attr('src', 'assets/media/system/products/nophoto.png');
				}
				else
				{
					$('#product_photo').attr('src', 'assets/media/system/products/'+data.data.photo);
				}				
				calculate();
				$('#search_product').typeahead('val',null).focus();
			}
			else
			{
				alert('Mohon maaf, produk tidak ditemukan');
				$('#product_photo').attr('src', '#');
				calculate();
				$('#search_product').typeahead('val',null).focus();
			}
		},
		error: function (xhr, ajaxOptions, thrownError,response) {
			alert(xhr.responseText);
		}
	});
}

function get_unit(product_code, unit_id)
{
    var unit = document.getElementById(unit_id);
	$(unit).hide();
	$.ajax({
		type: "POST", 
		url: 'pos/Cashier/get_unit',
		data: {
			product_code : product_code,
		}, 
		dataType: "json",
		beforeSend: function(e) {
			if(e && e.overrideMimeType) {
				e.overrideMimeType("application/json;charset=UTF-8");
			}
		},
		success: function(data){ 
			$(unit).html(data.option);
		},
		error: function (xhr, ajaxOptions, thrownError) {
			// alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
			console.log(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
		}
	});
	$(unit).show();
}

function calculate()
{   
	$('#pay').val(0); $('#kembalian').val(0); $("#btn-save").attr("disabled", true);
	$('#view_total_product').html($('.product_code').length);

	var total_qty = 0;
	$('.qty').each(function() {
		total_qty += Number($(this).val().replace(/\,/g, ""));
    });
	$('#total_qty').val(total_qty);

	var sum = 0;
	$('.total').each(function() {
		sum += Number($(this).val().replace(/\,/g, ""));
    });
	
	$('#grandtotal').val(sum);$('#custom-money').val(sum);$('#custom-money').html(sum);
    $('#view_grandtotal').html(format_number(String(sum)));
    if(sum != 0)
    {
        $("#payment_btn").attr("disabled", false);
    }
    else
    {
        $("#payment_btn").attr("disabled", true);
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


