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
	
	$("#purchase_order_datatable").DataTable({
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
			{"data": "id_po", className: "kt-font-dark text-center", width: "10px", orderable:false},
			{"data": "date", className: "kt-font-dark text-center", width: "150px"},
			{"data": "code_po", className: "kt-font-dark text-center", width: "100px"},
			{"data": "total_product", className: "kt-font-dark text-center", width: "100px"},						
			{"data": "grandtotal", className: 'kt-font-dark text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "supplier", className: 'kt-font-dark text-left'}
        ],
        columnDefs: [
            { 
				targets: 1, 
				orderable:false,
                render : function(val){
                    return format_date(val);
                }
			}
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
	
	$("#purchase_invoice_datatable").DataTable({
		searching: true,
		processing: true,
		language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
		serverSide: true,
		pageLength: 25,
        lengthMenu: [ [25, 50, 75, -1], [25, 50, 75, "All"] ],
		ajax: {
			"url": "transaction/Purchase/datatable_purchase_invoice/", 
			"type": "POST"
		},
		columns: [				
			{"data": "id", className: "kt-font-dark text-center", width: "10px", orderable:false},
			{"data": "date", className: "kt-font-dark text-center", width: "100px"},
			{"data": "code", className: "kt-font-dark text-center", width: "100px"},			
			{"data": "invoice", className: "kt-font-dark"},
			{"data": "payment", className: 'kt-font-dark text-center'},
			{"data": "due_date", className: 'kt-font-dark text-center'},
			{"data": "grandtotal", className: 'kt-font-primary text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "account_payable", className: 'kt-font-danger text-right', render: $.fn.dataTable.render.number(',', '.', 0)},			
			{"data": "name_s", className: 'kt-font-dark text-left'},
			{"data": "ppn", className: 'kt-font-dark text-center'},
			{"data": "payment_status", className: 'kt-font-dark text-center'},
			{"data": "search_code", className: 'kt-hidden'},
        ],
        columnDefs: [
			{ 
                targets: [0, 3, 4, -1, -2, -3, -4], 
                orderable: false
			},
            { 
                targets: 1, 
                render : function(val){
                    return format_date(val);
                }
			},
			{ 
                targets: 4, 
                render : function(val){
					if(val == 1)
					{
						return "TUNAI";
					}
					else if(val == 2)
					{
						return "KREDIT";
					}
                }
            },
			{ 
                targets: 5, 
                render : function(data, type, row, meta){
					if(row["payment_status"] == "1")
					{
						return "-";
					}
					else
					{						
						return format_date(data);
					}                    
                }
			},
			{
				targets: -3, 
                orderable : false,
                render : function(val){
                    if(val == 0)
                    {
                        return `<span class="text-danger">NON</span>`;
					}
					else if(val == 1)
                    {
                        return `<span class="text-primary">PPN</span>`;
                    }
                    else
                    {
                        return `<span class="text-success">FINAL</span>`;
                    }
                }
			},
			{ 
                targets: -2, 
                render : function(val){
					if(val == 1)
					{
						return "<div class='kt-font-bold kt-font-success'>LUNAS</div>";
					}
					else if(val == 2)
					{
						return "<div class='kt-font-bold kt-font-warning'>BELUM LUNAS</div>";
					}
					else
					{
						return "<div class='kt-font-bold kt-font-danger'>JATUH TEMPO</div>";
					}
                }
			}				
        ],
		order: [[1, 'DESC'], [2, 'DESC']],
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

