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

    var sales_invoice_id = $('#sales_invoice_id').val();
    $("#datatable_detail_sales_invoice").DataTable({        
		processing: true,
		serverSide: true,
		lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
		ajax: {
			"url": "transaction/Sales/datatable_detail_sales_invoice/"+sales_invoice_id, 
			"type": "POST"
		},
		columns: [				
			{"data": "id", className: "text-dark text-center", width: "10px"},
            {"data": "code_p", className: "text-dark text-center", width: "100px"},
            {"data": "name_p", className:"text-dark"},
            {"data": "qty", className: "text-dark text-right", width: "10px"},
            {"data": "name_u", className: "text-dark text-left", width: "100px"},
			{"data": "price", className: "text-dark text-right"},
			{"data": "disc_product", className: "text-dark text-right"},
            {"data": "name_w", className: "text-dark text-center"},
			{"data": "total", className: "text-dark text-right", render: $.fn.dataTable.render.number(',', '.', 2)},
			{"data": "search_code_p", className: "kt-hidden"},
		],
		columnDefs: [
			{
                targets: 5,
                render : function(data, type, row, meta){
					var price_class;
					if(Number(row['price']) > Number(row['price_5']))
					{
						if(Number(row['price']) > Number(row['price_4']))
						{
							if(Number(row['price']) > Number(row['price_3']))
							{
								if(Number(row['price']) > Number(row['price_2']))
								{
									price_class=1;
								}
								else
								{
									price_class=2;						
								}
							}
							else
							{
								price_class=3;						
							}							
						}
						else
						{
							price_class=4;						
						}						
					}
					else
					{
						price_class=5;						
					}
					return format_amount(row['price'])+' ('+price_class+')';
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
	
	

	var link_update_sales_invoice;
	$('#update_sales_invoice_btn').click(function(){
		link_update_sales_invoice = $(this).data('link');		
		$('#module_url').val('sales/invoice'); $('#action_module').val('update');
		var message = "<ul><li class='text-dark'>Penjualan <b>TUNAI</b> apabila sudah lebih dalam 30 Hari semenjak transaksi tidak dapat di melakukan perubahan</li><li class='text-dark'>Penjualan <b>KREDIT</b> apabila sudah terdapat pembayaran/pelunasan tidak dapat melakukan perubahan</li></ul>";
		$('#veryfy_message').html(message);
		$('#verify_module_password_modal').modal('show');		
	});
	
	$("#delete_sales_invoice_btn").on('click', function(){
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
					url		: "transaction/Sales/delete_sales_invoice",
					method	: "POST",
					dataType: "JSON",
					data: {
						sales_invoice_id : sales_invoice_id
					},
					success		: (data) => {						
						if(data.status.code	== 200) 
						{							
							window.location.replace("sales/invoice");
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

	$("#create_sales_invoice_do_btn").on('click', function(){
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
					url		: "transaction/Sales/create_sales_invoice_do/",
					method	: "POST",
					dataType: "JSON",
					data: {
						sales_invoice_id : sales_invoice_id
					},
					beforeSend: function(){
						KTApp.blockPage({
							overlayColor: '#000000',
							type: 'v2',
							state: 'primary',
							message: '<span class="text-dark font-weight-bold">Mohon menunggu, sedang dalam proses...</span>'
						});
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
			} else if (result.dismiss === 'cancel') {
				swal.fire(
					'Cetak DO Dibatalkan',
					'',
					'error'
				);				
			}
		});	
	});

	var link_cancel_sales_invoice_do;
	$('#cancel_sales_invoice_do_btn').click(function(){
		link_cancel_sales_invoice_do = $(this).data('link');
		$('#module_url').val('sales/invoice/cancel_sales_invoice_do'); $('#action_module').val('read');
		$('#verify_module_password_modal').modal('show');
	});

	$("#verify_module_password_form").on("submit", function(e){
		e.preventDefault();		
		var module_url = $('#module_url').val(); var action_module = $('#action_module').val();
		if(module_url == 'sales/invoice' && action_module == 'update')
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
		else if(module_url == 'sales/invoice/cancel_sales_invoice_do' && action_module == 'read') //CANCEL SALES INVOICE DO
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
								$('.modal').modal('hide');
								$.ajax({
									url		: "transaction/Sales/cancel_sales_invoice_do/",
									method	: "POST",
									dataType: "JSON",
									data: {
										sales_invoice_id : sales_invoice_id
									},
									beforeSend: function(){
										KTApp.blockPage({
											overlayColor: '#000000',
											type: 'v2',
											state: 'primary',
											message: '<span class="text-dark font-weight-bold">Mohon menunggu, sedang dalam proses...</span>'
										});
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
	
	// var payment_option = [];
	// $(".payment").click(function(){				
	// 	payment_option = [];
	// 	$(".payment:checked").each(function(){
	// 		payment_option.push($(this).val());
	// 	});
	// });	

	var payment_datatable = $("#payment_datatable").DataTable({				
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
			"url"  : "transaction/Sales/datatable_detail_sales_invoice_payment/"+sales_invoice_id,
			"type" : "POST",			
            // "data" :function(data){
			// 	data.payment = payment_option;				
            // }
		},
		columns: [
			{"data": "id", className: "text-dark text-center", width: "10px"},
			{"data": "date", className: "text-dark text-center", width: "120px"},
			{"data": "code_por", className: "text-dark"},
			{"data": "amount", className: 'text-primary text-right', render: $.fn.dataTable.render.number(',', '.', 2)},
			{"data": "search_code_por", className: 'kt-hidden'}
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
		payment_datatable.ajax.reload();
	});

	var sales_return_datatable = $("#sales_return_datatable").DataTable({
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
			"url": "transaction/Sales/datatable_detail_sales_invoice_sales_return/"+sales_invoice_id, 
			"type": "POST"
		},
		columns: [				
			{"data": "id_sr", className: "text-dark text-center", width: "10px"},
			{"data": "date", className: "text-dark text-center"},
			{"data": "code_sr", className: "text-dark text-center"},
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

	// SALES TAX INVOICE
	var sales_tax_invoice_datatable = $("#sales_tax_invoice_datatable").DataTable({
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
			"url": "transaction/Sales/datatable_detail_sales_invoice_tax_invoice/"+sales_invoice_id, 
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
				sales_invoice_id : $('#sales_invoice_id').val(),
				date : $('#tax_invoice_date').val(),
				number : $('#tax_invoice_number').val(),
				dpp :$('#tax_invoice_dpp').val(),
				ppn :$('#tax_invoice_ppn').val()
			},				
			success		: (data) => {				
				if(data.status.code	== 200) 
				{					
					$('#add_sales_tax_invoice_modal').modal('hide');
					toastr.success(data.status.message);
					sales_tax_invoice_datatable.ajax.reload();					
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
		
	$("#sales_tax_invoice_datatable").on('click', '.delete', function() {
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
					url		: "transaction/Sales/delete_sales_tax_invoice",
					method	: "POST",
					dataType: "JSON",
					data: {
						id: myId
					},
					success		: (data) => {						
						if(data.status.code	== 200) 
						{					
							toastr.success(data.status.message);
							sales_tax_invoice_datatable.ajax.reload();
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