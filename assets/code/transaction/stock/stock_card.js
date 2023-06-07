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
    
    var table = $("#datatable").DataTable({
        searching: true,
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
			"url"		: "transaction/Stock/stock_card/",
            "type"		: "POST"            
		},
		columns: [				
            {"data": "id_sc", className: "text-dark text-center", width:"10px"},
            {"data": "invoice", className: "text-primary text-left"},
            {"data": "code_p", className: "text-center"},
            {"data": "name_p", className: "text-dark text-left"},
            {"data": "qty", className: 'text-right'},
            {"data": "information", className: "text-dark text-center"},
			{"data": "method", className: 'text-center'},
            {"data": "stock", className: 'text-right'},
            {"data": "code_w", className: 'text-dark text-left'},
            {"data": "name_e", className: 'text-dark text-left'},
            {"data": "search_invoice", className: 'kt-hidden'},
            {"data": "search_code_p", className: 'kt-hidden'},
        ],
        columnDefs:[
            {
                "targets": [0, 1, 2, 3, 4, 5, 6 ,7],
                "orderable": false
            },
            { 
				targets: 1,
                render : function(data, type, row, meta){
                    if(row['type'] == 1)
                    {
                        return `<a href="purchase/invoice/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 2)
                    {
                        return `<a href="purchase/return/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 3)
                    {
                        return `<a href="pos/transaction/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 4)
                    {
                        return `<a href="sales/invoice/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 5)
                    {
                        return `<a href="sales/return/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 6)
                    {
                        return `<a href="stock/production/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 7)
                    {
                        return `<a href="stock/repacking/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 8)
                    {
                        return `<a href="stock/opname/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 9)
                    {
                        return `<a href="stock/mutation/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else
                    {
                        return `-`;
                    }                 
                }
			},
            { 
				targets: 4,
                render : function(data, type, row, meta){
                    if(row['method'] == 1)
                    {
                        return `<span class="text-success">`+data+`</span>`;
                    }   
                    else
                    {
                        return `<span class="text-danger">`+data+`</span>`;
                    }                 
                }
			},
            { 
				targets: -6,
                render : function(data, type, row, meta){
                    if(data == 1)
                    {
                        return `<span class="text-success">MASUK</span>`;
                    }   
                    else
                    {
                        return `<span class="text-danger">KELUAR</span>`;
                    }                 
                }
            },
            { 
				targets: -5,
                render : function(data, type, row, meta){
                    if(Number(data) > 0)
                    {
                        return `<span class="text-primary">`+data+`</span>`;
                    }   
                    else
                    {
                        return `<span class="text-danger">`+data+`</span>`;
                    }                 
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
		},
    });	
});

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}