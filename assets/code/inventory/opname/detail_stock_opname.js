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
    
    var stock_opname_id = $('#stock_opname_id').val();
    $("#datatable_detail_stock_opname").DataTable({        
		processing: true,
		serverSide: true,
		lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
		ajax: {
			"url": "transaction/Stock/datatable_detail_stock_opname/"+stock_opname_id, 
			"type": "POST"
		},
		columns: [				
			{"data": "id", className: "kt-font-dark text-center", width: "10px"},
            {"data": "code", className: "kt-font-dark text-center", width: "100px"},
            {"data": "name", className: "kt-font-dark"},
            {"data": "unit", className: "kt-font-dark text-center", width: "10px"},
			{"data": "stock", className: "kt-font-dark text-center", width: "100px"},
			{"data": "adjust", className: "kt-font-dark text-center", width: "100px"},
			{"data": "hpp", className: "kt-font-dark text-right", width: "100px", render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "total_hpp", className: "kt-font-dark text-right", width: "100px", render: $.fn.dataTable.render.number(',', '.', 0)}
		],     
		columnDefs: [           
            { 
                targets: -1, 
                orderable : false,
                render : function(val){
                    if(val < 0)
                    {
                        return `<span class="text-danger">`+ val +`</span>`;
                    }
                    else
                    {
                        return `<span class="text-success">`+ val +`</span>`;
                    }
                }
            },
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

	$('#synchronize_stok_opname_btn').on('click', function(){        
        $.ajax({
            url: 'inventory/Opname/synchronize_stok_opname',
            type: 'POST',
            dataType: 'JSON',
            data: {stock_opname_id:stock_opname_id},
            success: (data) => {
                if(data.status.code == 200)
                {
                    location.reload();
                } 
                else
                {
                    toastr.error(`${data.status.message}`);
                }
            },
            error: (err) => {
                toastr.error(`${err.responseText}`);
            }
        });        
	});
	
	$("#delete_stock_opname_btn").on('click', function(){
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
					url		: "inventory/Opname/delete",
					method	: "POST",
					dataType: "JSON",
					data: {
						stock_opname_id : stock_opname_id
					},
					success		: (data) => {						
						if(data.status.code	== 200) 
						{							
							window.location.replace("opname");
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

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}