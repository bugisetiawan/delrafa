$(document).ready(function() {                	
	$.getJSON("transaction/Sales/get_employee", (data) => {
        var option = '<option value="" class="text-dark">- SEMUA SALES -</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i) {
                option += '<option value="' + data.response[i].code + '" class="text-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
            });
        } else {
            option = option;
        }
        $("#sales_code").html(option).select2();
    });
	
	$.getJSON("transaction/Sales/get_customer", (data) => {
        var option = '<option value="" class="text-dark">- SEMUA PELANGGAN -</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i) {
                option += '<option value="' + data.response[i].code + '" class="text-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
            });
        } else {
            option = option;
        }        
        $("#customer_code").html(option).select2();
	});

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

    var table = $("#datatable_sales_invoice").DataTable({
        responsive: true,
		processing: true,
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
			"url"		: "General/due_sales_invoice/", 
			"type"		: "POST",
			"data":function(data){
				data.customer_code 	= $('#customer_code').val();
				data.sales_code 	= $('#sales_code').val();
            }
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

	$('#customer_code, #sales_code').change(function(){
		table.ajax.reload();
	});
});

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}