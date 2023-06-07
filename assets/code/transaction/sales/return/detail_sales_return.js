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
	
    var sales_return_id = $('#sales_return_id').val();
    $("#datatable_detail").DataTable({        
		processing: true,
		serverSide: true,
		lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"] ],
		ajax: {
			"url": "transaction/Sales/datatable_detail_sales_return/"+sales_return_id, 
			"type": "POST"
		},
		columns: [				
			{"data": "id", className: "text-dark text-center", width: "10px"},
            {"data": "code_p", className: "text-dark text-center", width: "100px"},
			{"data": "name_p", className: "text-dark text-left"},
			{"data": "qty", className: "text-dark text-right", width: "10px"},
			{"data": "name_u", className: "text-dark text-left", width: "100px"},			
			{"data": "price", className: "text-dark text-right", render: $.fn.dataTable.render.number(',', '.', 0)},            
			{"data": "name_w", className: "text-dark text-center"},
			{"data": "total", className: "text-dark text-right", render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "information", className: "text-dark"},
		], 
		columnDefs: [            
			{ 
                targets: -1, 
                render : function(val){
					if(val == "")
					{
						return "-";
					}
					else
					{
						return val;
					}                    
                }
			}			
        ],     
		order: [[1, 'asc']],
		rowCallback: function(row, data, iDisplayIndex) {
			var info = this.fnPagingInfo();
			var page = info.iPage;
			var length = info.iLength;
			var index = page * length + (iDisplayIndex + 1);
			$('td:eq(0)', row).html(index);
		}
	});    

	if($("#method").val() == "TIDAK POTONG FAKTUR")
	{
		$('.choose_invoice').hide();
	}	
	else
	{            
		$('.choose_invoice').show();
	}				

	$("#create_sales_return_do_btn").on('click', function(){
		swal.fire({
			title: 'Cetak DO?',
			text: "Yakin untuk melakukan Cetak DO?",
			type: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Ya',
			cancelButtonText: 'Tidak',
			reverseButtons: true
		}).then(function(result){
			if (result.value) {
				$.ajax({
					url		: "transaction/Sales/create_sales_return_do/",
					method	: "POST",
					dataType: "JSON",
					data: {
						sales_return_id : sales_return_id
					},
					success		: (data) => {						
						if(data.status.code	== 200) 
						{	
							window.open('transaction/Sales/print_sales_return_do/'+data.sales_return_id, 'Print DO', 'left=300, top=100, width=800, height=500');
							location.reload();							
						}
						else 
						{
							location.reload();
						}
					},
					error		: (err)	=> {
						alert(err.responseText);            
					}
				})			
			} else if (result.dismiss === 'cancel') {
				swal.fire(
					'Cetak DO Dibatalkan',
					'',
					'error'
				);				
			}
		});	
	});

	var link_cancel_sales_return_do;
	$('#cancel_sales_return_do_btn').click(function(){
		link_cancel_sales_return_do = $(this).data('link');
		$('#module_url').val('sales/return'); $('#action_module').val('delete');
		$('#verify_module_password_modal').modal('show');
	});

	$("#verify_module_password_form").on("submit", function(e){
		e.preventDefault();		
		var module_url = $('#module_url').val(); var action_module = $('#action_module').val();
		if(module_url == 'sales/return' && action_module == 'update')
		{
			$.ajax({
				url: "Auth/verify_module_password",
				type: "POST",
				dataType: "JSON",
				data: $(this).serialize(),
				success: (data) => {
					if (data.status.code == 200) 
					{
						swal.fire("BERHASIL", "Password Terverifikasi", "success");
						window.location.href = link_update_sales_invoice;
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
		else if(module_url == 'sales/return' && action_module == 'delete') //CANCEL SALES INVOICE DO
		{
			$.ajax({
				url: "Auth/verify_module_password",
				type: "POST",
				dataType: "JSON",
				data: $(this).serialize(),
				success: (data) => {
					if (data.status.code == 200) 
					{
						swal.fire("BERHASIL", "Password Terverifikasi", "success");
						swal.fire({
							title: 'Batal DO?',
							text: "Yakin untuk membatalkan?",
							type: 'warning',
							showCancelButton: true,
							confirmButtonText: 'Ya',
							cancelButtonText: 'Tidak',
							reverseButtons: true
						}).then(function(result){
							if (result.value) {
								if(data.status.code == 200) 
								{									
									window.location.href = link_cancel_sales_return_do;
								} 
								else
								{
									$('#verifypassword').val(null);					
									swal.fire("GAGAL", "Maaf, Verifikasi Password Gagal", "error");
								}			
							} 
							else if(result.dismiss === 'cancel') {
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

	$("#delete_sales_return_btn").on('click', function(){
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
					url		: "transaction/Sales/delete_sales_return",
					method	: "POST",
					dataType: "JSON",
					data: {
						sales_return_id : sales_return_id
					},
					success		: (data) => {						
						if(data.status.code	== 200) 
						{							
							window.location.replace("sales/return");
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