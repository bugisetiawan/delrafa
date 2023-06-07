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
	
	$.getJSON("report/Purchase_report/get_supplier_purchase_invoice_report", (data) => {
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
	
	$('#print_purchase_invoice_report').click(function(){		
		$('#filter_form').attr('target', '_blank').attr('action', 'report/Purchase_report/print_purchase_invoice_report').submit();
	});

	$('#print_purchase_invoice_daily_report').click(function(){
		$('#filter_form').attr('target', '_blank').attr('action', 'report/Purchase_report/print_purchase_invoice_daily_report').submit();
	});

	$('#print_purchase_invoice_detail_report').click(function(){		
		$('#filter_form').attr('target', '_blank').attr('action', 'report/Purchase_report/print_purchase_invoice_detail_report').submit();
	});

	total_purchase_invoice_report(); chart_purchase_invoice();

	function newexportaction(e, dt, button, config) {
		var self = this;
		var oldStart = dt.settings()[0]._iDisplayStart;
		dt.one('preXhr', function (e, s, data) {
			// Just this once, load all data from the server...
			data.start = dt.settings()[0]._iDisplayStart;
			data.page = Math.ceil(dt.settings()[0]._iDisplayStart / dt.settings()[0]._iDisplayLength);
			dt.one('preDraw', function (e, settings) {
				// Call the original action function
				if (button[0].className.indexOf('buttons-copy') >= 0) {
					$.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
				} else if (button[0].className.indexOf('buttons-excel') >= 0) {
					$.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
						$.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
						$.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
				} else if (button[0].className.indexOf('buttons-csv') >= 0) {
					$.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
						$.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config) :
						$.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
				} else if (button[0].className.indexOf('buttons-pdf') >= 0) {
					$.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
						$.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config) :
						$.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
				} else if (button[0].className.indexOf('buttons-print') >= 0) {
					$.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
				}
				dt.one('preXhr', function (e, s, data) {
					// DataTables thinks the first item displayed is index 0, but we're not drawing that.
					// Set the property to what it was before exporting.
					settings._iDisplayStart = oldStart;
					data.start = oldStart;
				});
				// Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
				setTimeout(dt.ajax.reload, 0);
				// Prevent rendering of the full data to the DOM
				return false;
			});
		});
		// Requery the server with the new one-time export settings
		dt.ajax.reload();
	};

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
	
	var table = $("#purchase_invoice_datatable").DataTable({
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
			"url"		: "report/Purchase_report/purchase_invoice/", 
			"type"		: "POST",			
            "data":function(data){
                data.from_date 		= $('#from_date').val();
                data.to_date 		= $('#to_date').val();
				data.payment 		= $('#payment').val();
				data.supplier_code 	= $('#supplier_code').val();
				data.ppn 	        = $('#ppn').val();
				data.payment_status = $('#payment_status').val();
				data.search_product	= $('#search_product').val();
            }
		},
		columns: [				
			{"data": "id", className: "text-dark text-center", width: "10px"},
			{"data": "date", className: "text-dark text-center"},
			{"data": "code", className: "text-dark text-center"},
            {"data": "invoice", className: "text-dark text-left"},
			{"data": "payment", className: 'text-dark text-center'},
			{"data": "due_date", className: 'text-dark text-center'},
			{"data": "grandtotal", className: 'kt-font-primary text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "account_payable", className: 'kt-font-danger text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "supplier", className: 'text-dark text-left'},
			{"data": "ppn", className: 'text-dark text-center'},		
			{"data": "payment_status", className: 'text-dark text-center'},
			{"data": "search_invoice", className: 'kt-hidden'}
        ],
        columnDefs: [
			{ 
				targets: [0, 3, 4, -1, -3],
                orderable: false
			},
            { 
				targets: 1,
                render : function(val){
                    return format_date(val);
                }
			},
			{ 
                targets: 4, 
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
                targets: 5, 
                render : function(data, type, row, meta){
					if(row["payment_status"] == "1")
					{
						return "-";
					}
					else
					{						
						return format_date(data);
					}					
                }
			},
			{
				targets: -3, 
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
                targets: -2, 
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
	
	$('#btn_refresh').on('click', function(){		
		total_purchase_invoice_report(); 
		chart_purchase_invoice();
		table.ajax.reload();
	});

	$('#from_date, #to_date').on('change', function(){
		if($(this).val() == "")
		{
			swal.fire("Mohon Maaf!", "Tanggal tidak boleh kosong, terima kasih", "error");
			$(this).datepicker().datepicker('setDate', new Date());
		}
		total_purchase_invoice_report(); 
		chart_purchase_invoice();
		table.ajax.reload();
	});

	$('#from_date, #to_date, #supplier_code, #payment, #ppn, #payment_status, #view_type').on('change', function(){		
		total_purchase_invoice_report(); 
		chart_purchase_invoice();
		table.ajax.reload();
	});

	$("#search_product").keypress(function(e){
		var key = e.which;
		if(key == 13)
		{			
			total_purchase_invoice_report(); 
			chart_purchase_invoice();
			table.ajax.reload();
		}
	});
});

function total_purchase_invoice_report()
{
	$.ajax({
		type: "POST",
		url: "report/Purchase_report/total_purchase_invoice_report/",		
		dataType: "JSON",
		data: {
			from_date 		: $('#from_date').val(),
			to_date 		: $('#to_date').val(),
			payment 		: $('#payment').val(),
			supplier_code 	: $('#supplier_code').val(),
			ppn 	        : $('#ppn').val(),
			payment_status  : $('#payment_status').val(),
			search_product	: $('#search_product').val()
		},
		success: function(data) {                    
			$('#total_grandtotal').html(data['grandtotal']);
			$('#total_account_payable').html(data['account_payable']);
		}
	}); 
}

function chart_purchase_invoice()
{
    $.ajax({
        type: "POST",
        url: "report/Purchase_report/chart_purchase_invoice/",		
        dataType: "JSON",
        data: {
            view_type : $('#view_type').val(),
            from_date : $('#from_date').val(),
            to_date   : $('#to_date').val(),
            payment   : $('#payment').val(),
			supplier_code : $('#supplier_code').val(),
			ppn 	        : $('#ppn').val(),
			payment_status : $('#payment_status').val(),
			search_product : $('#search_product').val()
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