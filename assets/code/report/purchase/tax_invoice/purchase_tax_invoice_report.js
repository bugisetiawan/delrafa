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
	
	var table = $("#purchase_tax_invoice_datatable").DataTable({
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
			"url"		: "report/Purchase_report/purchase_tax_invoice/", 
			"type"		: "POST",			
            "data":function(data){
                data.from_date 		= $('#from_date').val();
                data.to_date 		= $('#to_date').val();
				data.supplier_code 	= $('#supplier_code').val();
				data.tax_invoice_status = $('#tax_invoice_status').val();
				data.is_used 	    = $('#is_used').val();
            }
		},
		columns: [				
			{"data": "id", className: "text-dark text-center", width: "10px"},
			{"data": "date", className: "text-dark text-center", width: "100px"},
			{"data": "code", className: "text-dark text-center", width: "100px"},
            {"data": "invoice", className: "text-dark text-left"},				
			{"data": "tax_invoice_date", className: 'text-dark text-center', width: "100px"},
			{"data": "tax_invoice_number", className: 'text-dark text-center'},
			{"data": "tax_invoice_dpp", className: 'text-primary text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "tax_invoice_ppn", className: 'text-primary text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "total_tax_invoice", className: 'text-primary text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "is_used", className: 'text-dark text-center'},
			{"data": "supplier", className: 'text-dark text-left'},
			{"data": "search_code", className: 'kt-hidden'},
			{"data": "search_invoice", className: 'kt-hidden'}
        ],
        columnDefs: [
			{ 
				targets: [0],
                orderable: false
			},
            { 
				targets: [1, 4],
                render : function(val){
					if(val != null)
					{
						return format_date(val);
					}
					else
					{
						return "-";
					}					
                }
			},
			{ 
				targets: [-4],
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
		order: [[1, 'DESC'], [2, 'DESC']],
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
	
	$.getJSON("report/Purchase_report/get_supplier_purchase_tax_invoice_report", (data) => {
		var option = '<option value="">- SEMUA SUPPLIER -</option>';
		if (data.status.code == 200) {
			$.each(data.response, function(i) {
				option += '<option value="'+data.response[i].code+'">'+ data.response[i].code + ' | '+ data.response[i].name + '</option>';
			});
		} 
		else 
		{
			option = option;
		}
		$("#supplier_code").html(option).select2();
	});

	$('#from_date, #to_date, #supplier_code, #tax_invoice_status, #is_used').change(function(){
		table.ajax.reload();
	});
});

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}
