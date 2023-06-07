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
    
	$("#datatable").DataTable({
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
			"url": "transaction/Purchase/purchase_return/", 
			"type": "POST",
			"data":function(data){
                data.do_status 		= $('#do_status').val();
            }
		},
		columns: [				
			{"data": "id", className: "text-dark text-center", width: "10px"},
			{"data": "date", className: "text-dark text-center"},			
			{"data": "code", className: "text-dark text-center"},
			{"data": "method", className: "text-dark text-center"},
			{"data": "invoice", className: "text-dark"},
			{"data": "total_return", className: 'text-primary text-right', render: $.fn.dataTable.render.number(',', '.', 0)},				
			{"data": "name_s", className: 'text-dark text-left'},
			{"data": "do_status", className: 'text-dark text-center'}
        ],
        columnDefs: [
            { 
                targets: 1, 
                render : function(val){                
                    return format_date(val);
                }
			},
			{ 
                targets: 3, 
                render : function(val){                
					if(val == 1)
					{
						return 'TIDAK POTONG PEMBELIAN';
					}
					else if(val == 2)
					{
						return 'POTONG PEMBELIAN';
					}
					else
					{
						return '-';
					}
                }
			},
			{ 
                targets: 4, 
                render : function(val){                
					if(val != null)
					{
						return val;
					}					
					else
					{
						return '<span class="text-center">-<span>';
					}
                }
			},
			{ 
                targets: -1, 
                render : function(val){
					if(val == 1)
					{
						return "<div class='kt-font-bold text-success'><i class='fa fa-check'></i></div>";
					}
					else
					{
						return "<div class='kt-font-bold text-danger'><i class='fa fa-times'></i></div>";
					}
                }
            },
        ],
		order: [[0, 'desc']],
		rowCallback: function(row, data, iDisplayIndex) {
			var info = this.fnPagingInfo();
			var page = info.iPage;
			var length = info.iLength;
			var index = page * length + (iDisplayIndex + 1);
			$('td:eq(0)', row).html(index);
		}
	});
});

function format_date(date){	 
    day = date.split("-")[2]; month = date.split("-")[1]; year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}
