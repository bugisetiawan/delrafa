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
    
    $("#datatable_log_login_user").DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
        language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
        ajax: {
            "url": "Dashboard/get_log_user_login", 
            "type": "POST"
        },
        columns: [				
            {"data": "id_u", className: 'text-dark text-center'},            
            {"data": "name_e", className: 'text-primary'},
            {"data": "last_login", className: 'text-dark text-left'}
        ],
        columnDefs: [
            { 
                targets: -1, 
                render : function(val){
                    if(val != null)
                    {
                        return val;
                    }                    
                    else
                    {
                        return "BELUM LOGIN";
                    }
                }
            }            
        ],
        rowCallback: function(row, data, iDisplayIndex) {
            var info = this.fnPagingInfo();
            var page = info.iPage;
            var length = info.iLength;
            var index = page * length + (iDisplayIndex + 1);
            $('td:eq(0)', row).html(index);
        }
    });

    $("#datatable_log_activity_user").DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
        language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
        ajax: {
            "url": "Dashboard/get_log_user_activity", 
            "type": "POST"
        },
        columns: [				
            {"data": "id_a", className: 'text-dark text-center', width: '10px'},
            {"data": "information", className: 'text-dark'},
            {"data": "name_e", className: 'text-dark text-left'},
            {"data": "created", className: 'text-dark text-left', width: '150px'}
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
    
    $.ajax({
		type: "POST",
		url: "Dashboard/get_total_master_data/",		
		dataType: "JSON",
		success: function(data) {                    
            $('#total_product').html(data['total_product']);
            $('#total_supplier').html(data['total_supplier']);
            $('#total_customer').html(data['total_customer']);			
            $('#total_employee').html(data['total_employee']);
		}
    });

    $("#datatable_purchase_invoice").DataTable({
        searching: false,
        responsive: true,		
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
			"url"		: "Dashboard/datatable_purchase_invoice/", 
			"type"		: "POST",			            
		},
		columns: [				
			{"data": "id", className: "text-dark text-center", width: "10px"},
			{"data": "date", className: "text-dark text-center", width: "100px"},
			{"data": "code", className: "text-dark text-center", width: "100px"},
            {"data": "invoice", className: "text-dark text-left"},			
			{"data": "due_date", className: 'text-dark text-center'},
			{"data": "remaining_time", className: 'text-dark text-right'},
			{"data": "grandtotal", className: 'text-primary text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "account_payable", className: 'text-danger text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "supplier", className: 'text-dark text-left'},
			{"data": "ppn", className: 'text-dark text-center'},		
			{"data": "payment_status", className: 'text-dark text-center'}
        ],
        columnDefs: [
			{ 
				targets: [0, 3, -1, -3],
                orderable: false
			},
            { 
				targets: 1,
                render : function(val){
                    return format_date(val);
                }
			},
			{ 
				targets: 5,
                render : function(val){
					if(val <= 0)
					{
						return `<span class="text-danger">`+val+` Hari</span>`;
					}
					else
					{
						return `<span class="text-dark">`+val+` Hari</span>`;
					}
                }
			},
			{ 
                targets: 4, 
                render : function(data, type, row, meta){
					if(row["payment"] == "1")
					{
						return "-";
					}
					else
					{
						if(row["payment_status"] == "1")
						{
							return "-";
						}
						else
						{
							return format_date(row['due_date']);
						}						
					}                    
                }
			},		
			{
				targets: -2, 
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
                targets: -1, 
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
		order: [[1, 'desc'], [2, 'desc']],
		rowCallback: function(row, data, iDisplayIndex) {
			var info = this.fnPagingInfo();
			var page = info.iPage;
			var length = info.iLength;
			var index = page * length + (iDisplayIndex + 1);
			$('td:eq(0)', row).html(index);			 		
		},
    });	
    
    $("#datatable_sales_invoice").DataTable({
        searching: false,
        responsive: true,
		processing: true,
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
			"url"		: "Dashboard/datatable_sales_invoice/", 
			"type"		: "POST"
		},
		columns: [
			{"data": "id", className: "text-dark text-center", width: "10px"},
			{"data": "date", className: "text-dark text-center", width: "120px"},
            {"data": "invoice", className: "text-dark text-left"},			
			{"data": "due_date", className: 'text-dark text-center'},
			{"data": "remaining_time", className: 'text-dark text-right'},
			{"data": "grandtotal", className: 'text-primary text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "account_payable", className: 'text-danger text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "name_c", className: 'text-dark text-left'},
			{"data": "name_s", className: 'text-dark text-left'},			
			{"data": "payment_status", className: 'text-dark text-center'}
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
                render : function(data, type, row, meta){
					if(row["payment"] == "1")
					{
						return "-";
					}
					else
					{
						if(row["payment_status"] == "1")
						{
							return "-";
						}
						else
						{
							return format_date(row['due_date']);
						}						
					}                    
                }
			},
			{ 
				targets: 4,
                render : function(val){
					if(val <= 0)
					{
						return `<span class="text-danger">`+val+` Hari</span>`;
					}
					else
					{
						return `<span class="text-dark">`+val+` Hari</span>`;
					}
                }
			},
			{ 
                targets: -1, 
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
		order: [[1, 'desc']],
		rowCallback: function(row, data, iDisplayIndex) {
			var info = this.fnPagingInfo();
			var page = info.iPage;
			var length = info.iLength;
			var index = page * length + (iDisplayIndex + 1);
			$('td:eq(0)', row).html(index);			 		
		},
	});	
	
	$("#datatable_cheque_debt").DataTable({				
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
			"url"		: "Dashboard/cheque_of_debt/",
			"type"		: "POST",
		},
		columns: [
			{"data": "id", className: "text-dark text-center", width: "10px"},
			{"data": "date", className: "text-dark text-center", width: "120px"},
			{"data": "code_pod", className: "text-dark text-center"},
			{"data": "cheque_number", className: "text-dark text-center"},
			{"data": "cheque", className: 'text-primary text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "search_code_pod", className: 'kt-hidden'},			
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
		},
	});
	
	$("#datatable_cheque_receivable").DataTable({
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
			"url"		: "Dashboard/cheque_of_receivable/", 
			"type"		: "POST",
		},
		columns: [
			{"data": "id", className: "text-dark text-center", width: "10px"},
			{"data": "date", className: "text-dark text-center", width: "120px"},
			{"data": "code_por", className: "text-dark text-center"},
			{"data": "cheque_number", className: "text-dark text-center"},
			{"data": "cheque", className: 'text-primary text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "search_code_por", className: 'kt-hidden'},			
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
		},
	});
});

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}