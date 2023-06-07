$(document).ready(function() {
	toastr.options = {
		closeButton: !1,
		debug: !1,
		newestOnTop: !1,
		progressBar: !0,
		positionClass: "toast-top-right",
		preventDuplicates: !0,
		showDuration: "300",
		hideDuration: "1000",
		timeOut: "3000",
		extendedTimeOut: "1000",
		showEasing: "swing",
		hideEasing: "linear",
		showMethod: "fadeIn",
		hideMethod: "fadeOut"
	};

	// Data Table Master Data	
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

	var table = $("#datatable").DataTable({
		processing: true,
		language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
		serverSide: true,		
		pageLength: 25,
        lengthMenu: [ [25, 50, 75, -1], [25, 50, 75, "All"] ],
		ajax: {
			"url": "finance/Cash_ledger/cash_account/", 
			"type": "POST"
		},
		columns: [
			{"data": "id", className: 'text-dark', width:'10px'},			
			{"data": "code", className: 'text-center text-dark', width:'150px'},
			{"data": "name", className: 'text-dark'},			
			{"data": "action", className: 'text-center text-dark ', width:'150px'}
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

    $('.uppercase').keyup(function() {
        $(this).val($(this).val().toUpperCase());
    });
    
    $('#create_data').on('submit', function(e) {
		let t = $(this);
		e.preventDefault();
		$.ajax({
			url: t.attr('action'),
			method: t.attr('method'),
			dataType	: "JSON",
			data: t.serialize(),
			success		: (data) => {				
				if(data.status.code	== 200) 
				{					
					toastr.success(data.status.message);					
					$("#name").focus();
					$('#update_form').modal('hide');
					table.ajax.reload();					
				} else 
				{
					toastr.error(data.status.message);
				}
			},
			error		: (err)	=> {
				toastr.error(err.responseText);            
			}
		})
	});

	$("#datatable").on('click', '.delete', function() {
		var myId = $(this).data('id');
		$.ajax({
            url: "finance/Cash_ledger/check_cash_account",
            type: "GET",
            dataType: "JSON",
            data: {
                id: myId
            },
            success: (data) => {
				if(data.status.code != 200) 
				{
                    toastr.error(data.status.message);
				} 
				else 
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
								url		: "finance/Cash_ledger/delete_cash_account",
								method	: "GET",
								dataType: "JSON",
								data: {
									id: myId
								},
								success		: (data) => {
									if(data.status.code	== 200) 
									{					
										toastr.success(data.status.message);
										$("#code").focus();
										table.ajax.reload();
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
								'Hapus Data Dibatalkan',
								'',
								'error'
							);
							$("#code").focus();
						}
					});
                }
            },
            error: (err) => {
				alert(err);
            }
		});
	});
});