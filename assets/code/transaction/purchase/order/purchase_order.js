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
		searching: true,
		processing: true,
		language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
		serverSide: true,
		pageLength: 25,
        lengthMenu: [ [25, 50, 75, -1], [25, 50, 75, "All"] ],
		ajax: {
			"url": "transaction/Purchase/datatable_purchase_order/", 
			"type": "POST"
		},
		columns: [
			{"data": "id_po", className: "kt-font-dark text-center", width: "10px"},
			{"data": "date", className: "kt-font-dark text-center", width: "150px"},
			{"data": "code_po", className: "kt-font-dark text-center", width: "100px"},
			{"data": "total_product", className: "kt-font-dark text-center", width: "100px"},						
			{"data": "grandtotal", className: 'kt-font-dark text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "supplier", className: 'kt-font-dark text-center'}
        ],
        columnDefs: [
            { 
                targets: 1, 
                render : function(val){
                    return format_date(val);
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
});

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}

