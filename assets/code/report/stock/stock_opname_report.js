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
		responsive: true,
		processing: true,
		language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
		serverSide: true,
		pageLength: 25,
        lengthMenu: [ [25, 50, 75, -1], [25, 50, 75, "All"] ],
		dom: "<'row'<'col-sm-12 col-md-6 text-left'l><'col-sm-12 col-md-6 text-right'fB>>" +
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
			"url"		: "report/Stock_report/stock_opname/", 
			"type"		: "POST",
			"data":function(data){
				data.from_date 	  = $('#from_date').val();
				data.to_date 	  = $('#to_date').val();
				data.warehouse_id = $('#warehouse_id').val();
				data.status       = $('#status').val();
            }
		},
		columns: [				
			{"data": "id", className: "text-dark text-center", width: "10px"},
			{"data": "date", className: "text-dark text-center", width: "100px"},
			{"data": "code", className: "text-dark text-center", width: "100px"},
			{"data": "total_product", className: 'text-dark text-center'},
			{"data": "checker", className: 'text-dark text-left'},
			{"data": "operator", className: 'text-dark text-left'},			
			{"data": "warehouse", className: 'text-dark text-left'},
			{"data": "status", className: 'text-dark text-center'},
			{"data": "search_code", className: 'kt-hidden'}
		],    
		columnDefs: [
            { 
                targets: 1, 
                render : function(val){
					return format_date(val);
                }
			},	
			{ 
                targets: -2, 
                render : function(val){
					if(val == "1")
					{
						return "<b class='text-success'>SELESAI</b>";
					}
					else
					{
						return "<b class='text-warning'>DALAM PROSES</b>";
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
		},
	});	
	
	$('.date').datepicker({
		format: "dd-mm-yyyy",		
		todayHighlight: true,
		clearBtn: true,
		autoclose: true,
		orientation: "bottom auto"
	});
	
	$.ajax({
		type: "POST",
		url: "report/Stock_report/get_warehouse/",
		dataType: "JSON",
		success: function(data) {                                       
			$('#warehouse_id').html(data.option);
		}
	});
	
	$('#from_date, #to_date, #warehouse_id, #status').change(function(){
		table.ajax.reload();
	});


});

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}


