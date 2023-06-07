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
    
	// $("#datatable_so").DataTable({
	// 	searching: true,
	// 	processing: true,
	// 	language: {            
    //         'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
    //     },
	// 	serverSide: true,
	// 	pageLength: 25,
    //     lengthMenu: [ [25, 50, 75, -1], [25, 50, 75, "All"] ],
	// 	ajax: {
	// 		"url": "transaction/Sales/datatable_sales_order/", 
	// 		"type": "POST"
	// 	},
	// 	columns: [				
	// 		{"data": "id", className: "text-dark text-center", width: "10px"},
    //         {"data": "date", className: "text-dark text-center", width: "120px"},
	// 		{"data": "invoice", className: "text-dark text-center"},
	// 		{"data": "taking", className: "text-dark text-center"},
	// 		{"data": "grandtotal", className: 'text-dark text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
    //         {"data": "name_c", className: "text-dark text-center", width: "150px"},
	// 		{"data": "name_s", className: "text-dark text-center", width: "150px"},			
	// 		{"data": "status_so", className: "text-dark text-center"}
    //     ],
    //     columnDefs: [
    //         { 
    //             targets: 1, 
    //             render : function(val){
    //                 return format_date(val);
    //             }
	// 		},
	// 		{ 
    //             targets: 3, 
    //             render : function(val){
	// 				if(val == 1)
	// 				{
	// 					return "LANGSUNG";
	// 				}
	// 				else
	// 				{
	// 					return "PENGIRIMAN";
	// 				}
    //             }
	// 		},
	// 		{ 
    //             targets: -1, 
    //             render : function(val){
	// 				if(val == 1)
	// 				{
	// 					return "<span class='kt-font-bold kt-font-danger'>BELUM ADA</span>";
	// 				}
	// 				else
	// 				{
	// 					return "<span class='kt-font-bold kt-font-success'>SUDAH ADA</span>";
	// 				}
    //             }
	// 		}

    //     ],
	// 	order: [[0, 'desc']],
	// 	rowCallback: function(row, data, iDisplayIndex) {
	// 		var info = this.fnPagingInfo();
	// 		var page = info.iPage;
	// 		var length = info.iLength;
	// 		var index = page * length + (iDisplayIndex + 1);
	// 		$('td:eq(0)', row).html(index);
	// 	}
    // });	
    
    // $("#datatable_sot").DataTable({
	// 	searching: true,
	// 	processing: true,
	// 	language: {            
    //         'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
    //     },
	// 	serverSide: true,
	// 	pageLength: 25,
    //     lengthMenu: [ [25, 50, 75, -1], [25, 50, 75, "All"] ],
	// 	ajax: {
	// 		"url": "transaction/Sales/datatable_sales_order_taking/", 
	// 		"type": "POST"
	// 	},
	// 	columns: [				
	// 		{"data": "id", className: "text-dark text-center", width: "10px"},
    //         {"data": "date", className: "text-dark text-center", width: "120px"},
	// 		{"data": "code", className: "text-dark text-center"},
	// 		{"data": "total_so", className: "text-dark text-center"}
    //     ],
    //     columnDefs: [
    //         { 
    //             targets: 1, 
    //             render : function(val){
    //                 return format_date(val);
    //             }
	// 		},
    //     ],
	// 	order: [[0, 'desc']],
	// 	rowCallback: function(row, data, iDisplayIndex) {
	// 		var info = this.fnPagingInfo();
	// 		var page = info.iPage;
	// 		var length = info.iLength;
	// 		var index = page * length + (iDisplayIndex + 1);
	// 		$('td:eq(0)', row).html(index);
	// 	}
    // });
    
    $("#datatable_si").DataTable({
		searching: true,
		processing: true,
		language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
		serverSide: true,
		pageLength: 25,
        lengthMenu: [ [25, 50, 75, -1], [25, 50, 75, "All"] ],
		ajax: {
			"url": "transaction/Sales/datatable_sales_invoice/", 
			"type": "POST",
			"data":function(data){
                data.do_status 		= $('#do_status').val();
            }
		},
		columns: [				
			{"data": "id", className: "text-dark text-center", width: "10px"},
            {"data": "date", className: "text-dark text-center", width: "150px"},
            {"data": "invoice", className: "text-dark text-center"},            
			{"data": "total_price", className: 'text-dark text-right', render: $.fn.dataTable.render.number(',', '.', 0)},							
            {"data": "name_c", className: "text-dark", width: "150px"},
			{"data": "name_s", className: "text-dark", width: "150px"},
			{"data": "information", className: "text-dark text-left"},
			{"data": "status", className: "text-dark text-center"},
			{"data": "do_status", className: "text-dark text-center"},
			{"data": "search_invoice", className: "kt-hidden"}
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
			{ 
                targets: -3, 
                render : function(val){
					if(val == 1)
					{
						return "<div class='kt-font-bold text-success'>LUNAS</div>";
					}
					else if(val == 2)
					{
						return "<div class='kt-font-bold kt-font-warning'>BELUM LUNAS</div>";
					}
					else
					{
						return "<div class='kt-font-bold text-danger'>JATUH TEMPO</div>";
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
});

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}