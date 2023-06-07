$(document).ready(function(){
    var product_usage_id = $('#product_usage_id').val();

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

    $("#datatable").DataTable({        
		processing: true,
		serverSide: true,
		lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
		ajax: {
			"url": "inventory/Product_usage/datatable_detail_product_usage/"+product_usage_id, 
			"type": "POST"
		},
		columns: [
			{"data": "id", className: "text-dark text-center", width: "10px"},
            {"data": "code_p", className: "text-dark text-center", width: "100px"},
            {"data": "name_p", className:"text-dark"},
            {"data": "qty", className: "text-dark text-right", width: "10px"},
            {"data": "name_u", className: "text-dark text-left", width: "100px"},
			{"data": "price", className: "text-dark text-right", render: $.fn.dataTable.render.number(',', '.', 2)},
            {"data": "name_w", className: "text-dark text-center"},
			{"data": "total", className: "text-dark text-right", render: $.fn.dataTable.render.number(',', '.', 2)},
			{"data": "search_code_p", className: "kt-hidden"}
        ],
		rowCallback: function(row, data, iDisplayIndex) {
			var info = this.fnPagingInfo();
			var page = info.iPage;
			var length = info.iLength;
			var index = page * length + (iDisplayIndex + 1);
			$('td:eq(0)', row).html(index);
		}
    });

	var link_update_product_usage;
	$('#update_product_usage_btn').click(function(){
		link_update_product_usage = $(this).data('link');		
		$('#module_url').val('sales/invoice'); $('#action_module').val('update');
		var message = "<ul><li class='text-dark'>Penjualan <b>TUNAI</b> apabila sudah lebih dalam 30 Hari semenjak transaksi tidak dapat di melakukan perubahan</li><li class='text-dark'>Penjualan <b>KREDIT</b> apabila sudah terdapat pembayaran/pelunasan tidak dapat melakukan perubahan</li></ul>";
		$('#veryfy_message').html(message);
		$('#verify_module_password_modal').modal('show');		
	});
	
	$("#delete_product_usage_btn").on('click', function(){
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
					url		: "inventory/Product_usage/delete_product_usage",
					method	: "POST",
					dataType: "JSON",
					data: {
						product_usage_id : product_usage_id
					},
					success		: (data) => {						
						if(data.status.code	== 200) 
						{							
							window.location.replace("product_usage");
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

	$("#create_product_usage_do_btn").on('click', function(){
		swal.fire({
			title: 'Cetak DO?',
			text: "Yakin untuk melakukan Cetak DO?",
			type: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Ya',
			cancelButtonText: 'Tidak',
			reverseButtons: true
		}).then(function(result){
			if (result.value){
				$.ajax({
					url		: "inventory/Product_usage/create_product_usage_do/",
					method	: "POST",
					dataType: "JSON",
					data: {
						product_usage_id : product_usage_id
					},
					success	: (data) => {						
						if(data.status.code	== 200) 
						{	
							window.open('inventory/Product_usage/print_product_usage_do/'+data.product_usage_id, 'Print DO', 'left=300, top=100, width=800, height=500');
							location.reload();
						}
						else 
						{
							location.reload();
						}
					},
					error	: (err)	=> {
						alert(err.responseText);            
					}
				});			
			}
			else if (result.dismiss === 'cancel') {
				swal.fire(
					'Cetak DO Dibatalkan',
					'',
					'error'
				);				
			}
		});	
	});

	$('#cancel_product_usage_do_btn').click(function(){		
		$('#module_url').val('product_usage/cancel_product_usage_do'); $('#action_module').val('read');
		$('#verify_module_password_modal').modal('show');
	});

	$("#verify_module_password_form").on("submit", function(e){
		e.preventDefault();		
		var module_url = $('#module_url').val(); var action_module = $('#action_module').val();
		if(module_url == 'product_usage/cancel_product_usage_do' && action_module == 'read')
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
							if(result.value) {
								$.ajax({
									url		: "inventory/Product_usage/cancel_product_usage_do/",
									method	: "POST",
									dataType: "JSON",
									data: {
										product_usage_id : product_usage_id
									},
									success		: (data) => {						
										if(data.status.code	== 200) 
										{
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
							} 
							else if(result.dismiss === 'cancel') {
								swal.fire(
									'Batal DO Dibatalkan',
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

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
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