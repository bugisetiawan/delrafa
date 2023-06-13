$(document).ready(function(){
	$.fn.dataTableExt.oApi.fnPagingInfo = function(oSettings)
	{
		return {
			"iStart": oSettings._iDisplayStart,
			"iEnd": oSettings.fnDisplayEnd(),
			"iLength": oSettings._iDisplayLength,
			"iTotal": oSettings.fnRecordsTotal(),
			"iFilteredTotal": oSettings.fnRecordsDisplay(),
			"iPage": Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
			"iTotalPages": Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
		};
	};

	var pod_id = $('#pod_id').val()

	var link_update_payment_of_debt;
	$('#update_pod_btn').click(function(){
		link_update_payment_of_debt = $(this).data('link');
		$('#verifypassword').val(null); $('#module_url').val('payment/debt'); $('#action_module').val('U');
		$('#verify_module_password_modal').modal('show');
	});

	$("#delete_pod_btn").on('click', function(){
		$('#verifypassword').val(null); $('#module_url').val('payment/debt'); $('#action_module').val('D');
		var message = "<p>Yakin untuk menghapus data? Seluruh transaksi yang terkait akan mengalami perubahan</p>";
		$('#veryfy_message').html(message);
		$('#verify_module_password_modal').modal('show');
	});

	$("#verify_module_password_form").on("submit", function(e){
		e.preventDefault();		
		var module_url = $('#module_url').val(); var action_module = $('#action_module').val();
		if(module_url == 'payment/debt' && action_module == 'U')
		{		
			$.ajax({
				url: "Auth/verify_module_password",
				type: "POST",
				dataType: "JSON",
				data: $(this).serialize(),
				success: (data) => {
					if(data.status.code == 200)
					{
						swal.fire("BERHASIL", "Password Terverifikasi", "success");
						window.location.href = link_update_payment_of_debt;
					} 
					else
					{
						$('#verifypassword').val(null);
						swal.fire("GAGAL", "Maaf, Verifikasi Password Gagal", "error");
					}
				},
				error: (err) => {
					alert(err.responseText);
				}
			});
		}
		else if(module_url == 'payment/debt' && action_module == 'D')
		{
			$.ajax({
				url: "Auth/verify_module_password",
				type: "POST",
				dataType: "JSON",
				data: $(this).serialize(),
				success: (data) => {
					if(data.status.code == 200) 
					{
						swal.fire({
							title: 'Hapus Data?',
							text: "Data yang dihapus sudah tidak dapat dikembalikan lagi",
							type: 'warning',
							showCancelButton: true,
							confirmButtonText: 'Ya',
							cancelButtonText: 'Tidak',
							reverseButtons: true
						}).then(function(result){
							if (result.value) {
								$.ajax({
									url		: "finance/Payment/delete_payment_of_debt",
									method	: "POST",
									dataType: "JSON",
									data: {
										pod_id: pod_id						
									},
									success		: (data) => {						
										if(data.status.code	== 200) 
										{							
											window.location.replace("purchase/invoice");
										}
										else 
										{
											console.log(data.status.message);
										}
									},
									error		: (err)	=> {
										console.log(err.responseText);            
									}
								})			
							} else if (result.dismiss === 'cancel') {
								swal.fire(
									'Hapus Data Dibatalkan',
									'',
									'error'
								);				
							}
						});						
					} 
					else
					{
						$('#verifypassword').val(null);					
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
			alert('Verifikasi Gagal');
		}        
	});

	$('.cheque_acquittance_btn').on('click', function(e){
		$('#pl_detail_id').val($(this).data('id'));
		$('#cheque_number_title').html($(this).data('chequenumbertitle'));
	});	
		
	$('.date').datepicker({
        format: "dd-mm-yyyy",
		todayHighlight: true,
		clearBtn: false,
		autoclose: true,
		orientation: "bottom auto"
    });		
	
	$('.date').change(function(){
		if($(this).val() == "")
		{
			toastr.error('Tanggal wajib terisi');
			$(this).datepicker().datepicker('setDate', new Date());
		}
	});

	$('#cheque_acquittance_form').on('submit', function(e) {
		let t = $(this);
		e.preventDefault();
		$.ajax({
			url		 : t.attr('action'),
			method	 : t.attr('method'),
			dataType : "JSON",
			data	 : t.serialize(),
			success		: (data) => {				
				if(data.status.code	== 200) 
				{
					location.reload();
				} 
				else 
				{
					toastr.error(data.status.message);
				}
			},
			error		: (err)	=> {
				toastr.error(err.responseText);            
			}
		})
	});
});
