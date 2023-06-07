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
	
	var method_option = [];
	$(".method").click(function(){				
		method_option = [];
		$(".method:checked").each(function(){
			method_option.push($(this).val());
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
			"url"		: "report/Finance_report/payment_of_receivable/", 
			"type"		: "POST",			
            "data":function(data){
                data.from_date 		= $('#from_date').val();
                data.to_date 		= $('#to_date').val();
				data.method 		= method_option;
				data.customer_code 	= $('#customer_code').val();
            }
		},
		columns: [				
			{"data": "id_por", className: "text-center text-dark", width: "10px"},
			{"data": "date", className: "text-center text-dark", width: "120px"},
			{"data": "code_por", className: "text-center text-dark", width: "100px"},
			{"data": "grandtotal", className: 'text-right text-primary', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "method", className: 'text-left text-dark'},
			{"data": "customer", className: 'text-left text-dark'},
			{"data": "search_code_por", className: 'kt-hidden'}
        ],
        columnDefs: [
            { 
                targets: 1, 
                render : function(val){
                    return format_date(val);
                }
			},
			{ 
                targets: -3, 
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
					if(jQuery.inArray('4', val) > -1)
					{
						description.push('DEPOSIT');
					}
					if(jQuery.inArray('5', val) > -1)
					{
						description.push('OPER CEK/GIRO');
					}
					return description;
                }
			}
        ],
		order: [[1, 'DESC']],
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

	$('#from_date, #to_date, #customer_code, .method').change(function(){
		table.ajax.reload();
	});
	
});

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}