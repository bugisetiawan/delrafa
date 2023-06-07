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
	
	var payment_option = [];
	$(".payment").click(function(){				
		payment_option = [];
		$(".payment:checked").each(function(){
			payment_option.push($(this).val());
		});
	});

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
			"url"		: "report/Finance_report/cheque_of_receivable/", 
			"type"		: "POST",			
            "data":function(data){
                data.from_date 		= $('#from_date').val();
                data.to_date 		= $('#to_date').val();
				data.customer_code 	= $('#customer_code').val();
            }
		},
		columns: [				
			{"data": "id", className: "text-dark text-center", width: "10px"},
			{"data": "date", className: "text-dark text-center", width: "120px"},
			{"data": "code", className: "text-dark text-center", width: "100px"},
            {"data": "invoice", className: "text-dark text-center", width: "100px"},			
			{"data": "grandtotal", className: 'text-primary text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "payment", className: 'text-dark text-left'},
            {"data": "customer", className: 'text-dark text-left'},
            {"data": "cheque_status", className: 'text-dark text-center'},
			{"data": "search_code_por", className: 'kt-hidden'},
			{"data": "search_invoice", className: 'kt-hidden'}
        ],
        columnDefs: [
            { 
                targets: 1, 
                render : function(val){
                    return format_date(val);
                }
			},
			{ 
                targets: 5, 
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
					return description;
                }
			},
			{ 
                targets: -3, 
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
						return "<div class='kt-font-bold kt-font-danger'>-</div>";
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

	$('.date').datepicker({
		format: "dd-mm-yyyy",		
		todayHighlight: true,
		clearBtn: true,
		autoclose: true,
		orientation: "bottom auto"
	});
	
	$.getJSON("transaction/Sales/get_customer", (data) => {
		var option = '<option value="" class="kt-font-dark">- SEMUA PELANGGAN -</option>';
		if (data.status.code == 200) {
			$.each(data.response, function(i) {
				option += '<option value="' + data.response[i].code + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
			});
		} 
		else 
		{
			option = option;
		}   
		$("#customer_code").html(option).select2();
	});

	$('#from_date, #to_date, .payment, #customer_code').change(function(){
		table.ajax.reload();
	});
	
});

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}