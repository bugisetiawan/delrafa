jQuery(document).ready(function() {	
	var dbtable = "warehouse";

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
			"url": "master/data/datatable/"+dbtable, 
			"type": "POST"
		},
		columns: [
			{"data": "id", className: 'kt-font-dark'},		
			{"data": "code", className: 'kt-font-dark'},			
			{"data": "name", className: 'kt-font-dark'},			
			{"data": "view", className: 'kt-font-dark'}
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

	jQuery('#code, #editCode, #name, #editName').keyup(function() {
        $(this).val($(this).val().toUpperCase());
	});
	
	$('#create_data, #update_data').on('submit', function(e) {
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
					$("input").val("");
					$("#code").focus();
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
	
	$("#table_data").on('click', '.update', function() {
		var myId = $(this).data('id');
		$.ajax({
			type: "GET",
			url: "master/data/get_detail/"+dbtable,		
			dataType: "JSON",
			data: {
				id: myId
			},
			success: function(data) {
				console.log(data);
				$.each(data, function(id, code, name) {
					$('#update_form').modal('show');
					$('#editId').val(data.id);					
					$('#editCode').val(data.code);					
					$('#editName').val(data.name);					
				});
			}
		});
		return false;
	});
	
	$("#table_data").on('click', '.delete', function() {
		var myId = $(this).data('id');
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
					url		: "master/"+dbtable+"/delete/",
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
			} else if (result.dismiss === 'cancel') {
				swal.fire(
					'Hapus Data Dibatalkan',
					'',
					'error'
				);
				$("#code").focus();
			}
		});
	});

	$("#default_warehouse").click(function(){
        $.getJSON("master/warehouse/get_warehouse", (data) => {        
			var option = '<option value="">--- Pilih Gudang ---</option>';
			var warehouse_id;
            if (data.status.code == 200) {
                $.each(data.response, function(i, item) {
					option += '<option value="' + data.response[i].id + '">' + data.response[i].name + '</option>';
					if(data.response[i].default == 1)
					{
						warehouse_id = data.response[i].id;
					}
				});				
            } else {
                option = option;
			}			
			$("#warehouse_id").html(option);
			$('#warehouse_id').html(option).val(warehouse_id);
        });
	});

	$('#default_warehouse_data').on('submit', function(e) {
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
					$('#default_warehouse_form').modal('hide');
					$("#code").focus();					
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
});