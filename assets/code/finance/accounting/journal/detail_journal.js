$(document).ready(function(){
    $("#btn_delete_journal").on('click', function(){
        var journal_id = $(this).data('id');
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
					url		: "finance/Accounting/delete_journal",
					method	: "POST",
					dataType: "JSON",
					data: {
						journal_id : journal_id
					},
					success		: (data) => {
						if(data.status.code	== 200) 
						{							
							window.location.replace("journal");
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
	}); 
});