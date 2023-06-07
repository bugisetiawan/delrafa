$(document).ready(function() {             
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

    var pos_id = $('#pos_id').val();
    $("#datatable_detail_pos").DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
        ajax: {
            "url": "pos/Transaction/datatable_detail_transaction/" + pos_id, 
            "type": "POST"
        },
        columns: [				
            {"data": "id", className: "text-dark text-center", width: "10px"},
            {"data": "code_p", className: "text-dark text-center", width: "100px"},
            {"data": "name_p", className: "text-dark"},
            {"data": "qty", className: "text-dark text-center", width: "10px"},
            {"data": "name_u", className: "text-dark text-center", width: "100px"},            
            {"data": "sellprice", className: "text-dark text-right", width: "100px", render: $.fn.dataTable.render.number(',', '.', 0)},
            {"data": "total", className: "text-dark text-right", width: "100px", render: $.fn.dataTable.render.number(',', '.', 0)},
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

    var link_update_transaction;
	$('#update_transaction_btn').click(function(){
		link_update_transaction = $(this).data('link');
		$('#module_url').val('pos'); $('#action_module').val('update');
		$('#verify_module_password_modal').modal('show');
	});
	
	$('#delete_transaction_btn').click(function(){		
		$('#module_url').val('pos'); $('#action_module').val('delete');
		$('#verify_module_password_modal').modal('show');
    });
    
    $("#verify_module_password_form").on("submit", function(e){
		e.preventDefault();		
		var module_url = $('#module_url').val(); var action_module = $('#action_module').val();
		if(module_url == 'pos' && action_module == 'update')
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
						window.location.href = link_update_transaction;
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
		else if(module_url == 'pos' && action_module == 'delete')
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
									url		: "pos/Transaction/delete_transaction",
									method	: "POST",
									dataType: "JSON",
									data: {
										pos_id : pos_id
									},
									success		: (data) => {						
										if(data.status.code	== 200) 
										{							
											window.location.replace("pos/transaction");
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