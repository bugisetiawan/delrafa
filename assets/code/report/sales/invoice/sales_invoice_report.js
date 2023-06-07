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
	$('.date').datepicker({
		format: "dd-mm-yyyy",		
		todayHighlight: true,
		clearBtn: false,
		autoclose: true,
		orientation: "bottom auto"
	});

	$('#from_date, #to_date').change(function(){
		if($(this).val() == "")
		{
			toastr.error('Tanggal wajib terisi');
			$(this).datepicker().datepicker('setDate', new Date());
		}
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

	$('#print_sales_invoice_report').click(function(){		
		$('#filter_form').attr('target', '_blank').attr('action', 'report/Sales_report/print_sales_invoice_report').submit();
	});

	$('#print_sales_invoice_daily_report').click(function(){
		$('#filter_form').attr('target', '_blank').attr('action', 'report/Sales_report/print_sales_invoice_daily_report').submit();
	});

	$('#print_sales_invoice_detail_report').click(function(){		
		$('#filter_form').attr('target', '_blank').attr('action', 'report/Sales_report/print_sales_invoice_detail_report').submit();
	});

	get_total_sales_invoice(); chart_sales_invoice();

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
			'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>',
			'emptyTable': '<span class="text-danger">Tidak Ada Data Yang Tersedia</span>'
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
			"url"		: "report/Sales_report/sales_invoice/", 
			"type"		: "POST",			
            "data":function(data){
                data.from_date 		= $('#from_date').val();
				data.to_date 		= $('#to_date').val();
				data.payment 		= $('#payment').val();
				data.customer_code 	= $('#customer_code').val();
				data.sales_code 	= $('#sales_code').val();
				data.payment_status = $('#payment_status').val();
            }
		},
		columns:[
			{"data": "id", className: "text-dark text-center", width: "10px"},
			{"data": "date", className: "text-dark text-center"},
            {"data": "invoice", className: "text-dark text-center"},
			{"data": "payment", className: 'text-dark text-center'},
			{"data": "due_date", className: 'text-dark text-center'},
			{"data": "grandtotal", className: 'text-primary text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "account_payable", className: 'text-danger text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "name_c", className: 'text-dark text-left'},
			{"data": "name_s", className: 'text-dark text-left'},			
			{"data": "information", className: 'text-dark text-left'},
			{"data": "payment_status", className: 'text-dark text-center'},
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
                targets: 3, 
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

	$('#from_date, #to_date, #payment, #customer_code, #sales_code, #payment_status, #view_type').change(function(){		
		get_total_sales_invoice(); chart_sales_invoice();
		table.ajax.reload();
	});	
});

function get_total_sales_invoice()
{
	$.ajax({
		type: "POST",
		url: "report/Sales_report/get_total_sales_invoice_report/",		
		dataType: "JSON",
		data: {
			from_date 		: $('#from_date').val(),
			to_date 		: $('#to_date').val(),
			payment 	    : $('#payment').val(),
			customer_code 	: $('#customer_code').val(),
			sales_code 	    : $('#sales_code').val(),
			payment_status 	: $('#payment_status').val()
		},
		success: function(data) {                    
			$('#total_account_payable').html(data['account_payable']);
			$('#total_grandtotal').html(data['grandtotal']);
		}
	}); 
}

function chart_sales_invoice()
{
    $.ajax({
        type: "POST",
        url: "report/Sales_report/chart_sales_invoice/",		
        dataType: "JSON",
        data: {
            view_type : $('#view_type').val(),
            from_date : $('#from_date').val(),
            to_date   : $('#to_date').val(),
            payment   : $('#payment').val(),
			customer_code : $('#customer_code').val(),
			sales_code     : $('#sales_code').val(),
			payment_status : $('#payment_status').val()
        },
        success: function(data) {
			$("#chart").empty();
			if(data.length != 0)			
			{
				new Morris.Line({                
					element: 'chart',
					hideHover: 'auto',
					parseTime: false,
					data: data,
					xkey: 'time',
					xLabelAngle: 45,
					ykeys: ['grandtotal'],
					labels: ['Total'],
				});
			}		
			else
			{
				$('#chart').html('<div class="text-center"><span class="text-danger">Tidak Ada Data Yang Tersedia</span></div>');
			}	
        },
        error		: (err)	=> {
            toastr.error(err.responseText);            
        }
    });
}

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}

