$(document).ready(function(){
	$('#feature_discount_p').click(function(){
		if($('#feature_discount_p').prop('checked') == true)
		{
			$('#discount_p_modal').modal('show');
		}
		else
		{
			$('.discount_p').each(function() 
			{
				$(this).prop('readonly', true);
				$(this).val(0);
				recalculatediscount();
			});
		}
	});

	$('#cancel_discount_p_modal').click(function(){
		$('#feature_discount_p').prop('checked', false);
		$('.discount_p').each(function() 
		{
			$(this).prop('readonly', true);
			$(this).val(0);
		});
	});

	$("#discount_p_form").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: "pos/Cashier/verify_discount_p_password",
            type: "POST",
            dataType: "JSON",
            data: $(this).serialize(),
            success: (data) => {
				if (data.status.code == 200) 
				{
					$('.verifypassword').val(null);
					$('#discount_p_modal').modal('hide');
					$('.discount_p').each(function() 
					{
						$(this).prop('readonly', false);
						$(this).val(0);
					});
					recalculatediscount();
					swal.fire("BERHASIL", "Password Terverifikasi", "success");
				} 
				else
				{
					$('#feature_discount_p').prop('checked', false);
					$('.verifypassword').val(null);
					$('#discount_p_modal').modal('hide');					
					recalculatediscount();
					swal.fire("GAGAL", "Maaf, Verifikasi Password Gagal", "error");
                }
            },
            error: (err) => {
                alert(err.responseText);
            }
        });
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
        $("#employee_code").html(option).select();
	});
	
	$('#name_customer').keyup(function(){
		$(this).val($(this).val().toUpperCase());
		if($(this).val() != null && $(this).val() != "")
		{
			$("#btn-add-customer").attr("disabled", false);
		}
		else
		{
			$("#btn-add-customer").attr("disabled", true);
		}
	});

	$("#add_customer_form").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: "pos/Cashier/add_customer",
            type: "POST",
            dataType: "JSON",
            data: $(this).serialize(),
            success: (data) => {
				if (data.status.code == 200) 
				{
					$('#name_customer').val(null);
					$('#price_Class').val(1);
					$('#add_customer_modal').modal('hide');
					swal.fire("BERHASIL", "Pelanggan berhasil disimpan", "success");
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
				} 
				else
				{
                    alert(data.status.message);
                }
            },
            error: (err) => {
                alert(err.responseText);
            }
        });
	});
	
	$('#collect_amount').keyup(function(){
		$(this).val(String(format_number($(this).val())));
		if(Number($(this).val().replace(/\,/g, "")) > 0)
		{
			$("#btn-collect").attr("disabled", false);
		}
		else
		{
			$("#btn-collect").attr("disabled", true);
		}
	});

	$("#collect_form").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: "pos/Cashier/collect",
            type: "POST",
            dataType: "JSON",
            data: $(this).serialize(),
            success: (data) => {
				if (data.status.code == 200) 
				{
					$('#collect_amount').val(null);
					$('#collect_modal').modal('hide');
					swal.fire("BERHASIL", "Collect Berhasil", "success");
					window.open("pos/cashier/print_collect/"+data.collect_id, "Print Collect", "left=300, top=100, width=800, height=500");
				} 
				else
				{
                    alert(data.status.message);
                }
            },
            error: (err) => {
                alert(err.responseText);
            }
        });
	});

	$('#close_cashier_btn').click(function(){
		$('#module_url').val('close_cashier'); $('#action_module').val('read');
		$('#verify_module_password_modal').modal('show');
	});

	$('#close_cashier_automatic_btn').click(function(){
		$('#module_url').val('close_cashier'); $('#action_module').val('read');
		$('#verify_module_password_modal').modal('show');
	});

	$("#verify_module_password_form").on("submit", function(e){
		e.preventDefault();		
		var module_url = $('#module_url').val(); var action_module = $('#action_module').val();		
		if(module_url == 'close_cashier' && action_module == 'read')
		{
			$.ajax({
				url: "pos/Cashier/verify_close_cashier_password",
				type: "POST",
				dataType: "JSON",
				data: $(this).serialize(),
				success: (data) => {
					if(data.status.code == 200) 
					{
						$('.verifypassword').val(null);
						$('#verify_module_password_form').modal('hide');
						swal.fire("BERHASIL", "Password Terverifikasi", "success");
						$('#close_code_e').val(data.status.code_e);
						$('#close_modal').modal('show');
					} 
					else
					{
						$('.verifypassword').val(null);	
						$('#verify_module_password_form').modal('hide');
						$('#close_code_e').val(null);				
						swal.fire("GAGAL", "Maaf, Verifikasi Password Gagal", "error");
					}
				},
				error: (err) => {
					alert(err.responseText);
				}
			});
		}
		else
		{
			swal.fire("GAGAL", "Maaf, Verifikasi Password Gagal", "error");
		}        
	});
});

function recalculatediscount()
{
	$('.discount_p').each(function() 
	{
		$(this).prop('readonly', true);
		$(this).val(0);
		$(this).attr('id',Math.round(new Date().getTime() +(Math.random() * 100)));
		var qty   =$(this).attr('id')+1
		var price =$(this).attr('id')+2;		
		var total =$(this).attr('id')+3;
		$(this).closest('tr').find('.qty').attr('id', qty);		
        $(this).closest('tr').find('.price').attr('id', price);
		$(this).closest('tr').find('.total').attr('id', total);
		var subtotal = Number($('#'+qty).val().replace(/\,/g, ""))*Number($('#'+price).val().replace(/\,/g, ""));
		$('#'+total).val(format_number(String(subtotal)));		
	});							
	calculate();
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


