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
	
    var purchase_order_id = $('#purchase_order_id').val();
    $("#datatable").DataTable({        
		processing: true,
		language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
		serverSide: true,
		lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
		ajax: {
			"url": "transaction/purchase/datatable_detail_purchase_order/" + purchase_order_id, 
			"type": "POST"
		},
		columns: [				
			{"data": "id", className: "kt-font-dark text-center", width: "10px"},
            {"data": "code_p", className: "kt-font-dark text-center", width: "100px"},
            {"data": "name_p", className: "kt-font-dark"},
            {"data": "qty", className: "kt-font-dark text-right", width: "10px"},
            {"data": "buyprice", className: "kt-font-dark text-right", render: $.fn.dataTable.render.number(',', '.', 0)},
            {"data": "subtotal", className: "kt-font-dark text-right", render: $.fn.dataTable.render.number(',', '.', 0)}
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
});
