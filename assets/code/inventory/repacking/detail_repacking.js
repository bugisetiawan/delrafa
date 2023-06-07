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
    
	var repacking_id = $('#repacking_id').val();
	
	$("#datatable_to_product").DataTable({
		respnsive: true,
		processing: true,
		language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
		serverSide: true,
		lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
		ajax: {
			"url": "inventory/Repacking/datatable_detail_repacking/"+repacking_id, 
			"type"		: "POST"
		},
		columns: [				
			{"data": "id_p", className: "text-dark text-center", width: "10px"},
			{"data": "code_p", className: "text-dark text-center", width: "100px"},
            {"data": "name_p", className: "text-dark text-left"},
            {"data": "qty", className: "text-dark text-right", width:"50px"},
            {"data": "name_u", className: "text-dark text-left", width: "50px"},            
			{"data": "name_w", className: 'text-dark text-left', width: "100px"},			
		],    		   
		order: [[1, 'desc']],
		rowCallback: function(row, data, iDisplayIndex) {
			var info = this.fnPagingInfo();
			var page = info.iPage;
			var length = info.iLength;
			var index = page * length + (iDisplayIndex + 1);
			$('td:eq(0)', row).html(index);			 		
		},
	});

	$("#delete_repacking_btn").on('click', function(){
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
					url		: "inventory/Repacking/delete_repacking",
					method	: "POST",
					dataType: "JSON",
					data: {
						repacking_id : repacking_id
					},
					success		: (data) => {						
						if(data.status.code	== 200) 
						{							
							window.location.replace("repacking");
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