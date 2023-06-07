$(document).ready(function(){
    var cash_ledger_id = $('#cash_ledger_id').val();

    $("#delete_cash_ledger_mutation_btn").on('click', function(){		
		$.ajax({
			url: "Auth/verify_user_password",
			type: "POST",
			dataType: "JSON",
			data:{
				'module_url' : 'cash_ledger/mutation',
				'action' : 'delete'
			},
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
						if(result.value) {
							$.ajax({
								url		: "finance/Cash_ledger/delete_cash_ledger_mutation",
								method	: "POST",
								dataType: "JSON",
								data: {
									id: cash_ledger_id						
								},
								success		: (data) => {						
									if(data.status.code	== 200) 
									{							
										window.location.replace("cash_ledger/mutation");
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
					$('#verifypassword').val(null); $('#module_url').val('cash_ledger/in_out'); $('#action_module').val('delete');
					var message = "<p>Yakin untuk menghapus data? Seluruh transaksi yang terkait akan mengalami perubahan</p>";
					$('#veryfy_message').html(message);
					$('#verify_module_password_modal').modal('show');
				}
			},
			error: (err) => {
				alert(err.responseText);
			}
		});
	});

	$("#verify_module_password_form").on("submit", function(e){
		e.preventDefault();		
		var module_url = $('#module_url').val(); var action_module = $('#action_module').val();		
		if(module_url == 'cash_ledger/in_out' && action_module == 'delete')
		{
			$.ajax({
				url: "Auth/verify_module_password",
				type: "POST",
				dataType: "JSON",
				data: $(this).serialize(),
				success: (data) => {
					if (data.status.code == 200) 
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
									url		: "finance/Cash_ledger/delete_cash_ledger_mutation",
									method	: "POST",
									dataType: "JSON",
									data: {
										id : cash_ledger_id						
									},
									success		: (data) => {						
										if(data.status.code	== 200) 
										{							
											window.location.replace("cash_ledger/mutation");
										}
										else 
										{
											window.location.reload();
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
});