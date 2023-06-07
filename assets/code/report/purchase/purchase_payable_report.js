$(document).ajaxStart(function(){    
    KTApp.block('#filter_portlet',{
		overlayColor: '#000000',
        type: 'v2',
        state: 'primary',
        message: '<span class="text-dark font-weight-bold">Mohon menunggu, sedang dalam proses...</span>'
    });
});

$(document).ajaxStop(function(){
    KTApp.unblock('#filter_portlet');
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
			"url"		: "report/Finance_report/purchase_payable/", 
			"type"		: "POST",			
            "data":function(data){
                data.from_date 		= $('#from_date').val();
                data.to_date 		= $('#to_date').val();				
				data.supplier_code 	= $('#supplier_code').val();
				data.ppn 	        = $('#ppn').val();
				data.payment_status = $('#payment_status').val();
            }
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
			{"data": "cheque_payable", className: 'text-danger text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "supplier", className: 'text-dark text-left'},
			{"data": "ppn", className: 'text-dark text-center'},		
			{"data": "payment_status", className: 'text-dark text-center'},
			{"data": "pay_action", className: 'text-dark text-center'},
			{"data": "search_invoice", className: 'kt-hidden'}
        ],
        columnDefs: [
			{ 
				targets: [0, 3, -2, -4],
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
						return `<span class="text-danger">TERTUNDA <b>`+Math.abs(val)+`</b> Hari</span>`;
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
				targets: -4, 
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
                targets: -3, 
                render : function(data, type, row, meta){
					if(data == 1)
					{
						return "<div class='kt-font-bold kt-font-success'>LUNAS</div>";
					}
					else if(data == 2)
					{
						if(Date.parse(row['due_date']) >= new Date())
						{
							return "<div class='kt-font-bold kt-font-warning'>BELUM LUNAS</div>";
						}
						else
						{
							return "<div class='kt-font-bold kt-font-danger'>JATUH TEMPO</div>";
						}						
					}					
                }
            },	
			{ 
                targets: -2, 
                render : function(data, type, row, meta){
				if(Number(row["account_payable"]) == 0)
					{
						return "-";
					}
					else
					{
						return data;
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
	
	$('.date').datepicker({
		format: "dd-mm-yyyy",		
		todayHighlight: true,
		clearBtn: true,
		autoclose: true,
		orientation: "bottom auto"       
	});
	
	$.getJSON("transaction/Purchase/get_supplier", (data) => {
		var option = '<option value="" class="kt-font-dark">- SEMUA SUPPLIER -</option>';
		if (data.status.code == 200) {
			$.each(data.response, function(i) {
				option += '<option value="' + data.response[i].code + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
			});
		} 
		else 
		{
			option = option;
		}   
		$("#supplier_code").html(option).select2();
	});

	$('#from_date, #to_date, #supplier_code, #payment, #payment_status').change(function(){		
		total_purchase_payable_report();
		table.ajax.reload();
	});

	total_purchase_payable_report();
});

function total_purchase_payable_report()
{
	$.ajax({
		type: "POST",
		url: "report/Finance_report/total_purchase_payable_report/",		
		dataType: "JSON",
		data: {
			from_date 		: $('#from_date').val(),
			to_date 		: $('#to_date').val(),
			supplier_code 	: $('#supplier_code').val(),
			ppn     	    : $('#ppn').val(),
			payment_status  : $('#payment_status').val()
		},
		success: function(data) {                    
			$('#total_grandtotal').html(data['grandtotal']);
			$('#total_account_payable').html(data['account_payable']);
		}
	}); 
}

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}


