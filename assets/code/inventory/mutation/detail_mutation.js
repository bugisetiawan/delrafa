jQuery(document).ready(function() {
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

    var mutation_id = $('#mutation_id').val();
    $("#datatable_detail_mutation").DataTable({        
		processing: true,
		language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
		serverSide: true,
		lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
		ajax: {
			"url": "inventory/Mutation/detail/"+mutation_id,
			"type": "POST"
		},
		columns: [				
			{"data": "id", className: "text-dark text-center", width: "10px"},
            {"data": "code_p", className: "text-dark text-center", width: "100px"},
            {"data": "name_p", className: "text-dark"},
            {"data": "qty", className: "text-dark text-center", width: "10px"},
            {"data": "name_u", className: "text-dark text-center", width: "100px"},
            {"data": "name_fw", className: "text-dark text-center"},
            {"data": "name_tw", className: "text-dark text-center"}
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
	
	$("#delete_btn").on('click', function(){
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
					url		: "inventory/Mutation/delete",
					method	: "POST",
					dataType: "JSON",
					data: {
						mutation_id : mutation_id
					},
					success		: (data) => {						
						if(data.status.code	== 200) 
						{							
							location.replace("mutation");
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

    $("#create_do_btn").on('click', function(){
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
					url		: "inventory/Mutation/create_do/",
					method	: "POST",
					dataType: "JSON",
					data: {
						mutation_id : mutation_id
					},
					success		: (data) => {						
						if(data.status.code	== 200) 
						{	
							window.open('inventory/Mutation/print_do/'+data.mutation_id, 'Print DO', 'left=300, top=100, width=800, height=500');
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

	var link_cancel_mutation_do;
	$('#cancel_do_btn').click(function(){
		link_cancel_mutation_do = $(this).data('link');
		$('#module_url').val('mutation'); $('#action_module').val('delete');
		$('#verify_module_password_modal').modal('show');
	});

	$("#verify_module_password_form").on("submit", function(e){
		e.preventDefault();		
		var module_url = $('#module_url').val(); var action_module = $('#action_module').val();
		if(module_url == 'mutation' && action_module == 'delete')
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
									window.location.href = link_cancel_mutation_do;
								} 
								else
								{
									$('#verifypassword').val(null);					
									swal.fire("GAGAL", "Maaf, Verifikasi Password Gagal", "error");
								}			
							} 
							else if(result.dismiss === 'cancel') {
								swal.fire(
									'Hapus Data DO Dibatalkan',
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