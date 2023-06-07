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
			"url"		: "report/Sales_report/pos/", 
			"type"		: "POST",			
            "data":function(data){
                data.from_date 		= $('#from_date').val();
				data.to_date 		= $('#to_date').val();
				data.payment 		= $('#payment').val();
				data.customer_code 	= $('#customer_code').val();
				data.cashier_code 	= $('#cashier_code').val();
            }
		},
		columns: [				
			{"data": "id", className: "text-dark text-center", width: "10px"},
			{"data": "date", className: "text-dark text-center", width: "100px"},
			{"data": "time", className: "text-dark text-center", width: "100px"},
            {"data": "invoice", className: "text-dark text-center"},
			{"data": "payment", className: 'text-dark text-center'},
			{"data": "total_product", className: 'text-dark text-center'},
			{"data": "total_qty", className: 'text-dark text-center'},
			{"data": "grandtotal", className: 'text-primary text-right', render: $.fn.dataTable.render.number(',', '.', 0)},			
			{"data": "name_c", className: 'text-dark text-left'},
			{"data": "name_e", className: 'text-dark text-left'},
			{"data": "search_invoice", className: 'kt-hidden'},
        ],
        columnDefs: [
            { 
                targets: 1, 
                render : function(val){                    
                    return format_date(val);
                }
			},			
			{ 
                targets: 4, 
                render : function(val){
					if(val == 0)
					{
						return "TUNAI";
					}
					else if(val == 1)
					{
						return "DEBIT";
					}
					else
					{
						return "KREDIT";
					}
                }
			},			
        ],
		order: [[3, 'desc']],
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

	$.getJSON("report/Sales_report/get_cashier", (data) => {
		var option = '<option value="" class="kt-font-dark">- SEMUA KASIR -</option>';
		if (data.status.code == 200) {
			$.each(data.response, function(i) {
				option += '<option value="' + data.response[i].code + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
			});
		} 
		else 
		{
			option = option;
		}   
		$("#cashier_code").html(option).select2();
	});

	$('#from_date, #to_date, #payment, #customer_code, #cashier_code').change(function(){		
		get_total_pos_report();
		table.ajax.reload();
	});

	get_total_pos_report();

	$('#print_pos_report').click(function(){		
		$('#filter_form').attr('target', '_blank').attr('action', 'report/Sales_report/print_pos_report').submit();
	});

	$('#print_pos_daily_report').click(function(){
		$('#filter_form').attr('target', '_blank').attr('action', 'report/Sales_report/print_pos_daily_report').submit();
	});

	$('#print_pos_detail_report').click(function(){		
		$('#filter_form').attr('target', '_blank').attr('action', 'report/Sales_report/print_pos_detail_report').submit();
	});
});

function get_total_pos_report()
{
	$.ajax({
		type: "POST",
		url: "report/Sales_report/get_total_pos_report/",		
		dataType: "JSON",
		data: {
			from_date 		: $('#from_date').val(),
			to_date 		: $('#to_date').val(),						
			payment 		: $('#payment').val(),
			customer_code 	: $('#customer_code').val(),
			cashier_code 	: $('#cashier_code').val()
		},
		success: function(data) {                    
			$('#total_grandtotal').html(data['total_grandtotal']);
			$('#total_transaction').html(data['total_transaction']);
		}
	}); 
}

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}

