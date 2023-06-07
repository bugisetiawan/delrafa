$(document).ajaxStart(function(){    
    KTApp.blockPage({
		overlayColor: '#000000',
        type: 'v2',
        state: 'primary',
        message: '<span class="text-dark font-weight-bold">Mohon menunggu, sedang dalam proses...</span>'
    });
});

$(document).ajaxStop(function(){
    KTApp.unblockPage();
});

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
	
	var table = $("#datatable").DataTable({
		searching: false,
		processing: true,
		language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
		serverSide: true,
		pageLength: 25,
        lengthMenu: [ [25, 50, 75, -1], [25, 50, 75, "All"] ],
		dom: `<'row'<'col-sm-6 text-left'lf><'col-sm-6 text-right'B>>
			<'row'<'col-sm-12'tr>>
			<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'p>>`,
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
			"url"		: "report/Finance_report/cashier/", 
			"type"		: "POST",			
            "data":function(data){
                data.from_date 		= $('#from_date').val();
				data.to_date 		= $('#to_date').val();				
				data.cashier_code	= $('#cashier_code').val();								
            }
		},
		columns: [				
			{"data": "id", className: "text-dark text-center", width: "10px"},
			{"data": "date", className: "text-dark text-center", width: "100px"},
			{"data": "open_time", className: "text-dark text-center", width: "100px"},
			{"data": "close_time", className: "text-dark text-center", width: "100px"},            
			{"data": "income", className: 'text-success text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "outcome", className: 'text-danger text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "grandtotal", className: 'text-primary text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "name_e", className: 'text-dark text-left'},
			{"data": "action", className: "text-dark text-center", width: "10px"}
        ],
        columnDefs: [
            { 
                targets: 1, 
                render : function(val){                    
                    return format_date(val);
                }
			},
			{ 
                targets: -1, 
                render : function(val, type, row, meta){
					if(row['status_c'] == 0)
					{
						return '<a class="text-danger kt-link text-center close_cashier_btn" data-id="'+row['code_e']+'"><i class="fa fa-door-closed"></i></a>';
					}
					else
					{
						return val;			
					}                    
                }			
			}						
        ],
		order: [[1, 'desc']],
		rowCallback: function(row, data, iDisplayIndex) {
			var info = this.fnPagingInfo();
			var page = info.iPage;
			var length = info.iLength;
			var index = page * length + (iDisplayIndex + 1);
			$('td:eq(0)', row).html(index);			 		
		}		
	});	

	$("#datatable").on('click', '.close_cashier_btn', function() {
		$('#close_cashier_code').val($(this).data('id'));
		swal.fire({
			title: 'TUTUP KASIR?',
			text: "Anda yakin ingin menutup kasir?",
			type: 'warning',
			showCancelButton: true,
			confirmButtonText: 'TUTUP',
			cancelButtonText: 'BATAL',
			reverseButtons: true
		}).then(function(result){
			if (result.value){
				$('#verify_close_cashier_modal').modal('show');
			} else if (result.dismiss === 'cancel') {
				swal.fire(
					'TUTUP KASIR DIBATALKAN',
					'',
					'error'
				);
			}
		});
	});

	$("#verify_close_cashier_form").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: "pos/Cashier/verify_close_cashier_password",
            type: "POST",
            dataType: "JSON",
            data: $(this).serialize(),
            success: (data) => {
				if (data.status.code == 200) 
				{										
					$('#verifpasswordcashierclose').val(null);
					$('#verify_close_cashier_modal').modal('hide');
					alert('Verifikasi Password Berhasil');
					$.ajax({
						url		: "report/Finance_report/close_cashier_report/",
						method	: "POST",
						dataType: "JSON",
						data: {
							code_e 		 : $('#close_cashier_code').val(),
							close_code_e : data.status.code_e
						},
						success		: (data) => {						
							if(data.status.code	== 200) 
							{					
								toastr.success(data.status.message);							
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
				} 
				else
				{					
					$('#verifpasswordcashierclose').val(null);
					$('#verify_close_cashier_modal').modal('hide');
					alert('Verifikasi Password Gagal');
                }
            },
            error: (err) => {
                alert(err.responseText);
            }
        });
	});
	
	$('.date').datepicker({
		format: "dd-mm-yyyy",		
		todayHighlight: true,
		clearBtn: true,
		autoclose: true,
		orientation: "bottom auto"       
	});
	
	$.getJSON("report/Finance_report/get_cashier", (data) => {
		var option = '<option value="" class="kt-font-dark">- SEMUA KASIR -</option>';
		if (data.status.code == 200) {
			$.each(data.response, function(i) {
				option += '<option value="' + data.response[i].code + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
			});
		} 
		else 
		{
			option = option;
		}   
		$("#cashier_code").html(option).select2();
	});

	$('#from_date, #to_date, #cashier_code').change(function(){				
		table.ajax.reload();
	});
});

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}


