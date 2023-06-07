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
	
	var purchase_invoice_id = $('#purchase_invoice_id').val();
	
	var link_update_purchase_invoice;
	$('#update_purchase_invoice_btn').click(function(){
		link_update_purchase_invoice = $(this).data('link');
		$('#verifypassword').val(null); $('#module_url').val('purchase/invoice'); $('#action_module').val('U');
		// var message = "<ul><li class='text-dark'>Pembelian <b>TUNAI</b> apabila sudah lebih dalam 30 Hari semenjak transaksi tidak dapat di melakukan perubahan</li><li class='text-dark'>Pembelian <b>KREDIT</b> apabila sudah terdapat pembayaran/pelunasan tidak dapat melakukan perubahan</li></ul>";
		// $('#veryfy_message').html(message);
		$('#verify_module_password_modal').modal('show');
	});
	
	$("#delete_purchase_invoice_btn").on('click', function(){		
		$.ajax({
			url: "Auth/verify_user_password",
			type: "POST",
			dataType: "JSON",
			data:{
				'module_url' : 'purchase/invoice',
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
						if (result.value) {
							$.ajax({
								url		: "transaction/Purchase/delete_purchase_invoice",
								method	: "POST",
								dataType: "JSON",
								data: {
									purchase_invoice_id: purchase_invoice_id						
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
					$('#verifypassword').val(null); $('#module_url').val('purchase/invoice'); $('#action_module').val('D');
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
		if(module_url == 'purchase/invoice' && action_module == 'U')
		{
			$.ajax({
				url: "Auth/verify_module_password",
				type: "POST",
				dataType: "JSON",
				data: $(this).serialize(),
				success: (data) => {
					if (data.status.code == 200)
					{
						toastr.success('Password Terverifikasi');
						window.location.href = link_update_purchase_invoice;
					} 
					else
					{
						$('#verifypassword').val(null);
						toastr.error('Verifikasi Password Gagal');
					}
				},
				error: (err) => {
					alert(err.responseText);
				}
			});
		}
		else if(module_url == 'purchase/invoice' && action_module == 'D')
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
									url		: "transaction/Purchase/delete_purchase_invoice",
									method	: "POST",
									dataType: "JSON",
									data: {
										purchase_invoice_id: purchase_invoice_id						
									},
									success		: (data) => {						
										if(data.status.code	== 200) 
										{							
											window.location.replace("purchase");
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

    $("#datatable_detail_purchase").DataTable({        
		responsive: true,
		processing: true,
		language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
		serverSide: true,
		lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
		ajax: {
			"url": "transaction/Purchase/datatable_detail_purchase_invoice/"+purchase_invoice_id, 
			"type": "POST"
		},
		columns: [				
			{"data": "id", className: "text-dark text-center", width: "10px"},
            {"data": "code_p", className: "text-dark text-center", width: "100px"},
            {"data": "name_p", className: "text-dark"},
            {"data": "qty", className: "text-dark text-right", width: "10px"},
            {"data": "name_u", className: "text-dark text-left", width: "100px"},
			{"data": "price", className: "text-dark text-right", render: $.fn.dataTable.render.number(',', '.', 2)},
			{"data": "disc_product", className: "text-dark text-right"},
            {"data": "name_w", className: "text-dark text-center"},
			{"data": "total", className: "text-dark text-right", render: $.fn.dataTable.render.number(',', '.', 2)},
			{"data": "search_code_p", className: "kt-hidden"},
        ],      
		order: [[0, 'asc']],
		rowCallback: function(row, data, iDisplayIndex) {
			var info = this.fnPagingInfo();
			var page = info.iPage;
			var length = info.iLength;
			var index = page * length + (iDisplayIndex + 1);
			$('td:eq(0)', row).html(index);
		}
	});	

	// var payment_option = [];
	// $(".payment").click(function(){				
	// 	payment_option = [];
	// 	$(".payment:checked").each(function(){
	// 		payment_option.push($(this).val());
	// 	});
	// });

	var datatable_payment = $("#datatable_payment").DataTable({				
		responsive: true,
		processing: true,
		language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
		serverSide: true,
		pageLength: 25,
        lengthMenu: [ [25, 50, 75, -1], [25, 50, 75, "All"] ],
		dom: "<'row'<'col-sm-12 col-md-6 text-left'l><'col-sm-12 col-md-6 text-right'f>>" +
			 "<'row'<'col-sm-12'tr>>" +
			 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
		buttons: [
			{
				extend: 'print',
				text: 'Print',				
				exportOptions: {
					columns: ':not(.notexport)'
				}
			},
			{
				extend: 'copyHtml5',
				text: 'Copy',				
				exportOptions: {
					columns: ':not(.notexport)'
				}
			},
			{
				extend: 'excelHtml5',
				text: 'Excel',				
				exportOptions: {
					columns: ':not(.notexport)'
				}
			}			
		],
		ajax: {
			"url"  : "transaction/Purchase/datatable_detail_purchase_invoice_payment/"+purchase_invoice_id,
			"type" : "POST",
		},
		columns: [
			{"data": "id", className: "text-dark text-center", width: "10px"},
			{"data": "date", className: "text-dark text-center", width: "120px"},
			{"data": "code_pod", className: "text-dark"},
			{"data": "amount", className: 'text-primary text-right', render: $.fn.dataTable.render.number(',', '.', 2)},
			{"data": "search_code_pod", className: 'kt-hidden'}
        ],
        columnDefs: [
			{ 
                targets: [0, 1, 2, 3, 4], 
                orderable:false
			},
            { 
                targets: 1, 
                render : function(val){
                    return format_date(val);
                }
			}						
        ],
		rowCallback: function(row, data, iDisplayIndex) {
			var info = this.fnPagingInfo();
			var page = info.iPage;
			var length = info.iLength;
			var index = page * length + (iDisplayIndex + 1);
			$('td:eq(0)', row).html(index);			 		
		},
	});
	
	$('.payment').change(function(){
		datatable_payment.ajax.reload();
	});

	// PURCHASE RETURN
	var purchase_return_datatable = $("#purchase_return_datatable").DataTable({
		responsive: true,
		processing: true,
		language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
		serverSide: true,
		pageLength: 25,
        lengthMenu: [ [25, 50, 75, -1], [25, 50, 75, "All"] ],
		dom: "<'row'<'col-sm-12 col-md-6 text-left'l><'col-sm-12 col-md-6 text-right'f>>" +
			 "<'row'<'col-sm-12'tr>>" +
			 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
		buttons: [
			{
				extend: 'print',
				text: 'Print',				
				exportOptions: {
					columns: ':not(.notexport)'
				}
			},
			{
				extend: 'copyHtml5',
				text: 'Copy',				
				exportOptions: {
					columns: ':not(.notexport)'
				}
			},
			{
				extend: 'excelHtml5',
				text: 'Excel',				
				exportOptions: {
					columns: ':not(.notexport)'
				}
			}			
		],
		ajax: {
			"url": "transaction/Purchase/datatable_detail_purchase_invoice_purchase_return/"+purchase_invoice_id, 
			"type": "POST"
		},
		columns: [				
			{"data": "id_pr", className: "text-dark text-center", width: "10px"},
			{"data": "date", className: "text-dark text-center"},
			{"data": "code_pr", className: "text-dark text-center"},
			{"data": "total_return", className: 'text-primary text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "search_code", className: "kt-hidden"}
        ],
        columnDefs: [
			{ 
                targets: [0,1,2,3,4], 
                orderable:false
			},
            { 
                targets: 1, 
                render : function(val){
                    return format_date(val);
                }
			}
        ],
		rowCallback: function(row, data, iDisplayIndex) {
			var info = this.fnPagingInfo();
			var page = info.iPage;
			var length = info.iLength;
			var index = page * length + (iDisplayIndex + 1);
			$('td:eq(0)', row).html(index);
		}
	});

	// PURCHASE TAX INVOICE
	var purchase_tax_invoice_datatable = $("#purchase_tax_invoice_datatable").DataTable({
		responsive: true,
		processing: true,
		language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
		serverSide: true,
		pageLength: 25,
        lengthMenu: [ [25, 50, 75, -1], [25, 50, 75, "All"] ],
		dom: "<'row'<'col-sm-12 col-md-6 text-left'l><'col-sm-12 col-md-6 text-right'f>>" +
			 "<'row'<'col-sm-12'tr>>" +
			 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
		buttons: [
			{
				extend: 'print',
				text: 'Print',				
				exportOptions: {
					columns: ':not(.notexport)'
				}
			},
			{
				extend: 'copyHtml5',
				text: 'Copy',				
				exportOptions: {
					columns: ':not(.notexport)'
				}
			},
			{
				extend: 'excelHtml5',
				text: 'Excel',				
				exportOptions: {
					columns: ':not(.notexport)'
				}
			}			
		],
		ajax: {
			"url": "transaction/Purchase/datatable_detail_purchase_invoice_tax_invoice/"+purchase_invoice_id, 
			"type": "POST"
		},
		columns: [				
			{"data": "id", className: "text-dark text-center", width: "10px"},
			{"data": "date", className: "text-dark text-center"},
			{"data": "number", className: "text-dark text-center"},
			{"data": "dpp", className: 'text-dark text-right', render: $.fn.dataTable.render.number(',', '.', 2)},
			{"data": "ppn", className: 'text-dark text-right', render: $.fn.dataTable.render.number(',', '.', 2)},
			{"data": "grandtotal", className: 'text-primary text-right', render: $.fn.dataTable.render.number(',', '.', 2)},
			{"data": "action", className: "text-dark text-center"},
			{"data": "search_number", className: "kt-hidden"}
        ],
        columnDefs: [
			{ 
                targets: [0,1,2,3,4,5,6], 
                orderable:false
			},
            { 
                targets: 1, 
                render : function(val){
                    return format_date(val);
                }
			}
        ],
		rowCallback: function(row, data, iDisplayIndex) {
			var info = this.fnPagingInfo();
			var page = info.iPage;
			var length = info.iLength;
			var index = page * length + (iDisplayIndex + 1);
			$('td:eq(0)', row).html(index);
		}
	});

	$('.date').datepicker({
        format: "dd-mm-yyyy",		
		todayHighlight: true,
		clearBtn: true,
		autoclose: true,
		orientation: "bottom auto"
	});

	$('.ammount').keyup(function() {
        $(this).val(format_amount($(this).val()));
	});
		
	$('#create_data, #update_data').on('submit', function(e) {
		let t = $(this);
		e.preventDefault();
		$.ajax({
			url: t.attr('action'),
			method: t.attr('method'),
			dataType	: "JSON",
			data: {
				purchase_invoice_id : $('#purchase_invoice_id').val(),
				date : $('#tax_invoice_date').val(),
				number : $('#tax_invoice_number').val(),
				dpp :$('#tax_invoice_dpp').val(),
				ppn :$('#tax_invoice_ppn').val()
			},				
			success		: (data) => {				
				if(data.status.code	== 200) 
				{					
					$('#add_purchase_tax_invoice_modal').modal('hide');
					toastr.success(data.status.message);
					purchase_tax_invoice_datatable.ajax.reload();					
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
		
	$("#purchase_tax_invoice_datatable").on('click', '.delete', function() {
		var myId = $(this).data('id');
		swal.fire({
			title: 'HAPUS DATA?',
			text: "Data yang dihapus tidak dapat dikembalikan!",
			type: 'warning',
			showCancelButton: true,
			confirmButtonText: 'HAPUS',
			cancelButtonText: 'BATAL',
			reverseButtons: true
		}).then(function(result){
			if (result.value) {
				$.ajax({
					url		: "transaction/Purchase/delete_purchase_tax_invoice",
					method	: "POST",
					dataType: "JSON",
					data: {
						id: myId
					},
					success		: (data) => {						
						if(data.status.code	== 200) 
						{					
							toastr.success(data.status.message);
							purchase_tax_invoice_datatable.ajax.reload();
						} else 
						{
							toastr.error(data.status.message);
						}
					},
					error		: (err)	=> {
						toastr.error(err.responseText);            
					}
				})			
			} else if (result.dismiss === 'cancel') {
				swal.fire(
					'HAPUS DATA DIBATALKAN',
					'',
					'error'
				);
			}
		});
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