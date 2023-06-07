$(document).ajaxStart(function(){    
    KTApp.blockPage({
		overlayColor: '#000000',
        type: 'v2',
        state: 'primary',
        message: '<span class="text-dark font-weight-bold">Mohon menunggu, sedang dalam proses...</span>'
    });
});

$(document).ajaxStop(function(){
    KTApp.unblockPage();
});

$(document).ready(function(){
	$('#print_sales_profit_detail_report').click(function(){		
		$('#filter_form').attr('target', '_blank').attr('action', 'report/Finance_report/print_sales_profit_detail_report').submit();
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
	
	var table_sales_invoice = $("#datatable_sales_invoice").DataTable({
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
			"url"		: "report/Finance_report/sales_profit/", 
			"type"		: "POST",			
            "data":function(data){
				data.datatable_type = 'datatable_sales_invoice';
                data.from_date 		= $('#from_date').val();
				data.to_date 		= $('#to_date').val();
				data.customer_code 	= $('#customer_code').val();
				data.sales_code 	= $('#sales_code').val();				
            }
		},
		columns:[
			{"data": "id", className: "text-dark text-center", width: "10px"},
			{"data": "date", className: "text-dark text-center", width: "100px"},
            {"data": "invoice", className: "text-dark text-left", width: "150px"},			
			{"data": "grandtotal", className: 'text-success text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "total_hpp", className: 'text-danger text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "grandtotal", className: 'text-dark text-right'},
			{"data": "grandtotal", className: 'text-dark text-right'},			
			{"data": "name_c", className: "text-dark"},
			{"data": "name_s", className: "text-dark"},			
			{"data": "search_invoice", className: "kt-hidden"}
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
                render : function(data, type, row, meta){
					var result = Number(row['grandtotal']) - Number(row['total_hpp']);
					if(result <= 0)
					{
						return "<span class='text-danger'>"+$.fn.dataTable.render.number(',', '.', 0).display(result)+"<span>";
					}					
					else
					{
						return "<span class='text-primary'>"+$.fn.dataTable.render.number(',', '.', 0).display(result)+"<span>";
					}
                }
			},			
			{ 
                targets: 6, 
                render : function(data, type, row, meta){
					var pembagi = row['total_hpp'] != 0 ? row['total_hpp'] : 1;
					var result = ((Number(row['grandtotal']) - Number(row['total_hpp']))/Number(pembagi))*100;
					if(result <= 0)
					{
						return "<span class='text-danger'>"+$.fn.dataTable.render.number(',', '.', 2).display(result)+"%<span>"+' '+row['action'];
					}					
					else
					{
						return "<span class='text-primary'>"+$.fn.dataTable.render.number(',', '.', 2).display(result)+"%<span>"+' '+row['action'];
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

	$("#datatable_sales_invoice").on('click', '.view_detail', function() {
		var url= $(this).data('url');
		window.open(url, "Detail Profitabilitas Penjualan", "left=300, top=100, width=1080, height=500");
	});	
	
	var table_sales_return = $("#datatable_sales_return").DataTable({		
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
			"url": "report/Finance_report/sales_profit/", 
			"type": "POST",
			"data":function(data){
				data.datatable_type = 'datatable_sales_return';
                data.from_date 		= $('#from_date').val();
                data.to_date 		= $('#to_date').val();
				data.customer_code 	= $('#customer_code').val();
				data.sales_code 	= $('#sales_code').val();
            }
		},
		columns: [				
			{"data": "id_sr", className: "text-dark text-center", width: "10px"},
			{"data": "date", className: "text-dark text-center", width: "100px"},
			{"data": "code_sr", className: "text-dark text-center", width: "100px"},			
			{"data": "invoice", className: "text-dark text-left"},                        
            {"data": "total_product", className: "text-dark text-center", width: "100px"},
			{"data": "total_return", className: 'text-primary text-right', width: "100px", render: $.fn.dataTable.render.number(',', '.', 0)},				
			{"data": "name_c", className: "text-dark"}
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
					if(val == null)
					{
						return "-";
					}
					else
					{
						return val;
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
		}
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

	$('#from_date, #to_date, #customer_code, #sales_code').change(function(){		
		table_sales_invoice.ajax.reload();
		table_sales_return.ajax.reload();
		get_total_sales_profit_report();
	});

	get_total_sales_profit_report();
});

function get_total_sales_profit_report()
{
	$.ajax({
		type: "POST",
		url: "report/Finance_report/get_total_sales_profit_report/",		
		dataType: "JSON",
		data: {						
			from_date 	 : $('#from_date').val(),
			to_date 	 : $('#to_date').val(),
			customer_code: $('#customer_code').val(),
			sales_code 	 : $('#sales_code').val()
		},
		success: function(data) {                    
			$('#total_sales').html(data['total_sales']);
			$('#total_sales_return').html(data['total_sales_return']);
			$('#total_hpp').html(data['total_hpp']);
			$('#total_profit').html(data['profit']);
		}		
	}); 
}

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}