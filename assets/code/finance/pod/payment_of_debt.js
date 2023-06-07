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
			"url"		: "finance/Payment/payment_of_debt/",
			"type"		: "POST"
		},
		columns: [
			{"data": "id_pod", className: "text-dark text-center", width: "10px"},
			{"data": "date", className: "text-dark text-center", width: "120px"},
			{"data": "code_pod", className: "text-dark text-center", width: "100px"},
			{"data": "grandtotal", className: 'text-primary text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "method", className: 'text-dark text-left'},
			{"data": "supplier", className: 'text-dark text-left'},
			{"data": "search_code_pod", className: 'kt-hidden'}
        ],
        columnDefs: [
            { 
                targets: 1, 
                render : function(val){
                    return format_date(val);
                }
			},
			{ 
                targets: -3, 
                render : function(val){
					var description = [];
					if(jQuery.inArray('1', val) > -1)
					{
						description.push('TUNAI');
					}
					if(jQuery.inArray('2', val) > -1)
					{
						description.push('TRANSFER');
					}
					if(jQuery.inArray('3', val) > -1)
					{
						description.push('CEK/GIRO');
					}
					if(jQuery.inArray('4', val) > -1)
					{
						description.push('DEPOSIT');
					}
					if(jQuery.inArray('5', val) > -1)
					{
						description.push('OPER CEK/GIRO');
					}
					return description;
                }
			},			
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
	
});

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}