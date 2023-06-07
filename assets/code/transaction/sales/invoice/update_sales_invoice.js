$(document).ready(function(){
    $('#date').datepicker({
        format: "dd-mm-yyyy",
        todayHighlight: true,
		clearBtn: true,
		autoclose: true,
		orientation: "bottom auto"
    });

    $.getJSON("transaction/Sales/get_employee", (data) => {
        var option;
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

	$("#customer_code").change(function(){
		if($(this).val()!="")
		{
			$('#payment').prop("disabled", false);
            dueday();
		}	
		else
		{
            $('#payment').val(1); $('#payment_due').val(0); $('#payment').prop( "disabled", true);
        }	
        $.ajax({
            type: "POST", 
            url: 'transaction/Sales/check_customer',
            data: {
                customer_code : $(this).val(),
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
        calculate();
    });        

    if($('#payment').val() == 1)
    {
        $('#payment_method').html('TUNAI');
        $('#payment_due').prop("readonly", true);
        $('#cash_ledger_form').show(); $('#credit_method, #dp_checklist_form, #downpayment_form').hide();
        $('.cash_ledger_input').prop("disabled", false); $('.cash_ledger_input').prop("required", true);
        $('#down_payment').val(0); $("#dp_checklist").prop("checked", false);
    }
    else
    {
        $('#payment_method').html('KREDIT');
        $('#payment_due').prop("readonly", false);
        if($('#dp_checklist').prop('checked') != true)
        {            
			$('#cash_ledger_form, #downpayment_form').hide();
            $('#down_payment').val(0);
			$('.cash_ledger_input').prop("disabled", true); $('.cash_ledger_input').prop("required", false);
        }
        else
        {            
            $('#cash_ledger_form, #downpayment_form').show();
			$('.cash_ledger_input').prop("disabled", false); $('.cash_ledger_input').prop("required", true);
        }
    }

    $('#payment').change(function(){
		if($(this).val() == 1)
		{
            $('#payment_method').html('TUNAI');
            $('#from_cl_type').val(1).trigger('change');
			$('#payment_due').prop("readonly", true);
			$('#cash_ledger_form').show(); $('#credit_method, #dp_checklist_form, #downpayment_form').hide();
			$('.cash_ledger_input').prop("disabled", false); $('.cash_ledger_input').prop("required", true);
            $('#down_payment').val(0); $("#dp_checklist").prop("checked", false);
		}
		else
		{
            $('#payment_method').html('KREDIT');
			$('#payment_due').prop("readonly", false);
			$('#cash_ledger_form, #dowpayment_form').hide(); $('#dp_checklist_form, #credit_method').show();
            $('.cash_ledger_input').prop("disabled", true); $('.cash_ledger_input').prop("required", false);
            $('#down_payment').val(0);
        }        
		dueday();
		calculate();
    });

    $('#payment_due').click(function(){
        $(this).select();
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
                        // var sellprice = (data.sellprice != null) ? data.sellprice : 0;
                        // $('#'+price).val(format_amount(String(sellprice)));
                        // $('#'+price).html(data.option).select2({
						// 	tags: true,
						// 	createTag: function (params) {
						// 		var term = String(format_amount(params.term));
						// 			return {
						// 				id: term,
						// 				text: term,
						// 				newTag: true // add additional parameters
						// 			}
						// 		}
                        // });
                        $.ajax({
                            type: "POST", 
                            url: 'transaction/Sales/get_hpp_product', 
                            data: {
                                product_code  : $('#'+product_code).val(),
                                unit_id       : $('#'+unit).val()
                            }, 
                            dataType: "JSON",
                            success: function(data){
                                if(Number($('#'+price).val()) <= Number(data.hpp))
                                {
                                    var message = "<p class='text-dark'>Harga Jual lebih rendah/sama dengan harga pokok, segera melakukan perubahan harga jual. Apabila tetap ingin melanjutkan silahkan lakukan verifikasi, terima kasih</p>";
                                    $('#veryfy_message').html(message);
                                    $('#module_url').val('sales/invoice'); $('#action_module').val('create');
                                    $('#verify_module_password_modal').modal('show'); $('#verifypassword').focus();
                                    $("#verify_module_password_form").on("submit", function(e){
                                        e.preventDefault();		
                                        var module_url = $('#module_url').val(); var action_module = $('#action_module').val();
                                        if(module_url == 'sales/invoice' && action_module == 'create')
                                        {
                                            $.ajax({
                                                url: "Auth/verify_module_password",
                                                type: "POST",
                                                dataType: "JSON",
                                                data: $(this).serialize(),
                                                success: (data) => {
                                                    if (data.status.code == 200) 
                                                    {
                                                        $('#verify_module_password_modal').modal('hide'); $('#verifypassword').val(null);
                                                        $('#'+qty).focus(); calculate();
                                                        toastr.success('Password Terverifikasi');
                                                    }
                                                    else
                                                    {	
                                                        $('#'+delete_product).trigger('click');
                                                        $('#verify_module_password_modal').modal('hide'); $('#verifypassword').val(null);
                                                        toastr.error('Verifikasi Gagal');
                                                    }
                                                },
                                                error: (err) => {
                                                    alert(err.responseText);
                                                }
                                            }); 
                                        }
                                        else
                                        {
                                            calculate();
                                            $('#verify_module_password_modal').modal('hide'); $('#verifypassword').val(null);
                                            toastr.error('Verifikasi Gagal');
                                        }
                                    });
                                }
                            }
                        });
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
        var id=Math.round(new Date().getTime() + (Math.random() * 100)); $(this).attr('id', id);                
        var qty         = $(this).attr('id')+2;
        var price       = $(this).attr('id')+3;
        var disc_product = $(this).attr('id')+4;
        var warehouse   = $(this).attr('id')+5;        
        var total       = $(this).attr('id')+6;
        $(this).closest('tr').find('.qty').attr('id', qty);
        $(this).closest('tr').find('.price').attr('id', price);
        $(this).closest('tr').find('.disc_product').attr('id', disc_product);
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
            success: function(data) {
                $('#'+price).html(data.option).select2({
                    tags: true
                });
                var subtotal = Number($('#'+qty).val().replace(/\,/g, ""))*$('#'+price).val();
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

    $("#product_table").on("click", ".update_price", function(){
        $(this).select2({
            tags: true,
            createTag: function (params){
                var term = String(format_amount(params.term));
                    return {
                        id: term,
                        text: term,
                        newTag: true // add additional parameters
                    }
                }
        });
    });
    $("#product_table").on("change", ".price", function() {
		var id = Math.round(new Date().getTime() +(Math.random() * 100)); $(this).attr('id', id);
		var product_code = $(this).attr('id')+2;
		var unit 	=  $(this).attr('id')+3;
        var qty          = $(this).attr('id')+4;
        var disc_product = $(this).attr('id')+5;
		var total        = $(this).attr('id')+6;				
		$(this).closest('tr').find('.product_code').attr('id', product_code);
		$(this).closest('tr').find('.unit').attr('id', unit);
        $(this).closest('tr').find('.qty').attr('id', qty);
        $(this).closest('tr').find('.disc_product').attr('id', disc_product);
        $(this).closest('tr').find('.total').attr('id', total);
        var subtotal;
        if($('#'+id).children("option:selected").hasClass("H5"))
        {
            $('#module_url').val('sales/invoice'); $('#action_module').val('create');
            $('#verify_module_password_modal').modal('show'); $('#verifypassword').focus();
            $("#verify_module_password_form").on("submit", function(e){
                e.preventDefault();		
                var module_url = $('#module_url').val(); var action_module = $('#action_module').val();
                if(module_url == 'sales/invoice' && action_module == 'create')
                {
                    $.ajax({
                        url: "Auth/verify_module_password",
                        type: "POST",
                        dataType: "JSON",
                        data: $(this).serialize(),
                        success: (data) => {
                            if (data.status.code == 200) 
                            {
								subtotal = Number($('#'+qty).val().replace(/\,/g, ""))*Number($('#'+id).val().replace(/\,/g, ""));
								$('#verify_module_password_modal').modal('hide'); $('#verifypassword').val(null);
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
											$('#'+disc_product).val(0);
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
                                toastr.success('Password Terverifikasi');
                            }
                            else
                            {		
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
										// var sellprice = (data.sellprice != null) ? data.sellprice : 0;
										// $('#'+price).val(format_amount(String(sellprice)));
										$('#'+id).select2('destroy');
										$('#'+id).html(data.option).select2({
											tags: true
										});
										subtotal = Number($('#'+qty).val().replace(/\,/g, ""))*$('#'+id).val();
										var disc_p = $('#'+disc_product).val().split('+');
										if(disc_p != null && disc_p != "" )
										{
											for(var i = 0; i < disc_p.length; i++)
											{      
												subtotal = subtotal - (Number(disc_p[i])/100*Number(subtotal));
											}
											$('#'+total).val(format_amount(String(subtotal.toFixed(2))));
										}
										else
										{
											$('#'+total).val(format_amount(String(subtotal.toFixed(2))));
										}
										$('#verify_module_password_modal').modal('hide'); $('#verifypassword').val(null);
		                                swal.fire("GAGAL", "Maaf, Verifikasi Password Gagal", "error");
										calculate();
									}
								});																
                            }
                        },
                        error: (err) => {
                            alert(err.responseText);
                        }
                    }); 
                }
                else
                {
					calculate();
					$('#verify_module_password_modal').modal('hide'); $('#verifypassword').val(null);
                    alert('Verifikasi Gagal');
                }
            });                                   
        }
        else
        {
			$.ajax({
				type: "POST", 
				url: 'transaction/Sales/get_hpp_product', 
				data: {
					product_code  : $('#'+product_code).val(),
					unit_id       : $('#'+unit).val()
				}, 
				dataType: "JSON",
				success: function(data){ 
					if(Number($('#'+id).val().replace(/\,/g, "")) <= Number(data.hpp))
					{
						$('#module_url').val('sales/invoice'); $('#action_module').val('create');
						$('#verify_module_password_modal').modal('show'); $('#verifypassword').focus();
						$("#verify_module_password_form").on("submit", function(e){
							e.preventDefault();		
							var module_url = $('#module_url').val(); var action_module = $('#action_module').val();
							if(module_url == 'sales/invoice' && action_module == 'create')
							{
								$.ajax({
									url: "Auth/verify_module_password",
									type: "POST",
									dataType: "JSON",
									data: $(this).serialize(),
									success: (data) => {
										if (data.status.code == 200) 
										{
											subtotal = Number($('#'+qty).val().replace(/\,/g, ""))*Number($('#'+id).val().replace(/\,/g, ""));
											$('#verify_module_password_modal').modal('hide'); $('#verifypassword').val(null);
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
														$('#'+disc_product).val(0);
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
											toastr.success('Password Terverifikasi');										
										}
										else
										{		
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
													// var sellprice = (data.sellprice != null) ? data.sellprice : 0;
													// $('#'+price).val(format_amount(String(sellprice)));
													$('#'+id).select2('destroy');
													$('#'+id).html(data.option).select2({
														tags: true
													});
													subtotal = Number($('#'+qty).val().replace(/\,/g, ""))*$('#'+id).val();
													var disc_p = $('#'+disc_product).val().split('+');
													if(disc_p != null && disc_p != "" )
													{
														for(var i = 0; i < disc_p.length; i++)
														{      
															subtotal = subtotal - (Number(disc_p[i])/100*Number(subtotal));
														}
														$('#'+total).val(format_amount(String(subtotal.toFixed(2))));
													}
													else
													{
														$('#'+total).val(format_amount(String(subtotal.toFixed(2))));
													}
													$('#verify_module_password_modal').modal('hide'); $('#verifypassword').val(null);
													swal.fire("GAGAL", "Maaf, Verifikasi Password Gagal", "error");
													calculate();
												}
											});																
										}
									},
									error: (err) => {
										alert(err.responseText);
									}
								}); 
							}
							else
							{
								calculate();
								$('#verify_module_password_modal').modal('hide'); $('#verifypassword').val(null);
								alert('Verifikasi Gagal');
							}
						});
					}
					else
					{
						subtotal = Number($('#'+qty).val().replace(/\,/g, ""))*Number($('#'+id).val().replace(/\,/g, ""));
						var disc_p = $('#'+disc_product).val().split('+');
						if(disc_p != null && disc_p != "" )
						{
							for(var i = 0; i < disc_p.length; i++)
							{      
								subtotal = subtotal - (Number(disc_p[i])/100*Number(subtotal));
							}
							$('#'+total).val(format_amount(String(subtotal.toFixed(2))));
						}
						else
						{
							$('#'+total).val(format_amount(String(subtotal.toFixed(2))));
						}
						calculate();
					}					
				}
			});			
		}
    });

    // $("#product_table").on("keyup", ".price", function() {
    //     var id = Math.round(new Date().getTime() +(Math.random() * 100)); $(this).attr('id', id);
    //     var qty          = $(this).attr('id')+2;
    //     var disc_product = $(this).attr('id')+3;
    //     var total        = $(this).attr('id')+4;
    //     $(this).closest('tr').find('.qty').attr('id', qty);
    //     $(this).closest('tr').find('.disc_product').attr('id', disc_product);
    //     $(this).closest('tr').find('.total').attr('id', total);
    //     var subtotal = Number($('#'+qty).val().replace(/\,/g, ""))*Number($('#'+id).val().replace(/\,/g, ""));
    //     var disc_p = $('#'+disc_product).val().split('+');
    //     if(disc_p != null && disc_p != "" )
    //     {
    //         for(var i = 0; i < disc_p.length; i++)
    //         {      
    //             if(Number(disc_p[i]) >= 0 && Number(disc_p[i]) <= 100)          
    //             {
    //                 subtotal = subtotal - (Number(disc_p[i])/100*Number(subtotal));
    //             }
    //             else
    //             {
    //                 $('#'+id).val(null);
    //                 swal.fire('', 'Terjadi Kesalahan Input Pada Diskon', 'error');
    //                 break;
    //             }
    //         }
    //         $('#'+total).val(format_amount(String(subtotal.toFixed(2))));
    //     }
    //     else
    //     {
    //         $('#'+total).val(format_amount(String(subtotal.toFixed(2))));
    //     }
    //     calculate();
    // });
    
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

    $("#discount_p, #discount_rp, #down_payment").on("click",function() {
        $(this).select();
    });
    $("#discount_rp, #down_payment").on("keyup",function() {
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
    
    $.getJSON("finance/Cash_ledger/get_cash_ledger_account/"+$('#from_cl_type').val(), (data) => {	
        var option = '<option value="" class="kt-font-dark">- PILIH AKUN -</option>';
        if(data.status.code == 200) {
            $.each(data.response, function(i) {
                option += '<option value="' + data.response[i].id + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
            });
        } else {
            option = option;
        }
        var value = ($('#account_id').val() != null) ? $('#account_id').val() : null;
        $('#from_account_id').html(option).val(value);
    });
    
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

    $("#down_payment").on("keyup",function() {
        calculate();
    });

    $('#account_payable_message').hide();
});

function dueday()
{
    if($('#payment').val() == 1)
    {        
        $('#payment_due').val('0');
    }
    else
    {
        $.ajax({
            type: "POST", 
            url: 'transaction/Sales/get_payment_due',
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
                $('#payment_due').val(data.dueday);
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
    $('#subtotal').val(format_amount(String(subtotal)));

    var discount_rp = $('#discount_rp').val().replace(/\,/g, "");
    var grandtotal = subtotal - discount_rp;
    var downpayment = $('#down_payment').val() ? $('#down_payment').val().replace(/\,/g, "") : 0;
    $('#account_payable').val(format_amount(String(Number(grandtotal) - Number(downpayment))));
    $('#grandtotal').val(format_amount(String(grandtotal)));
    if(Number(total_product.length) > 0 && Number(total_qty) > 0)
    {
        if($('#payment').val() == 2 && $('#dp_checklist').prop('checked') == true)
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