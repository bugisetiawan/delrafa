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

	$.getJSON("master/department/get_department", (data) => {
        var option = '<option value="" class="kt-font-dark">- SEMUA DEPARTEMEN -</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i, item) {
                option += '<option value="' + data.response[i].code + '" class="kt-font-dark">' + data.response[i].name + '</option>';
            });
        } else {
            option = option;
        }
        $("#department_code").html(option);
	});	
    
    $("#department_code").change(function() {
        code_depart = $(this).val();        
        $.ajax({
            url: "master/department/get_sub",
            type: "GET",
            dataType: 'JSON',
            data: {
                code_depart: $(this).val()
            },
            success: (data) => {
                var option = '<option value="">- SEMUA SUBDEPARTEMEN -</option>';
                if (data.status.code == 200) {
                    $.each(data.response, function(i, item) {
                        option += '<option value="' + data.response[i].code + '">' + data.response[i].name + '</option>';
                    });
                } else {
                    option = option;
                }
                $("#subdepartment_code").html(option);
            },
            error: (err) => {
                console.log(err);
                console.log(err.responseText);
            }
        });                
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
		searching: false,
		responsive: true,
		processing: true,
		language: {            
			'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>',
			'emptyTable': '<span class="text-danger">Tidak Ada Data Yang Tersedia</span>'
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
			"url"		: "report/Sales_report/product_sales/",
			"type"		: "POST",
			"data":function(data){
				data.from_date 		= $('#from_date').val();
				data.to_date 		= $('#to_date').val();				
				data.department_code 	= $('#department_code').val();
				data.subdepartment_code = $('#subdepartment_code').val();                
				data.customer_code  = $('#customer_code').val();
				data.sales_code     = $('#sales_code').val();
				data.ppn 		    = $('#ppn').val();
				data.search 		= $('#search').val();				
            }
		},
		columns: [				
			{"data": "id", className: "text-dark text-center", width: "10px", orderable:false},			
			{"data": "code", className: "text-dark text-center", width: "100px", orderable:false},
            {"data": "name", className: "text-dark"},            			
			{"data": "total_qty", className: 'text-dark text-right', width: "50px"},
			{"data": "unit", className: 'text-dark text-left', width: "50px", orderable:false},			
			{"data": "total_sales", className: 'text-primary text-right',width: "100px", render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "average_sales", className: 'text-dark text-right', width: "150px", render: $.fn.dataTable.render.number(',', '.', 0)}						
		],
		order: [[2, 'asc']],
		rowCallback: function(row, data, iDisplayIndex) {
			var info = this.fnPagingInfo();
			var page = info.iPage;
			var length = info.iLength;
			var index = page * length + (iDisplayIndex + 1);
			$('td:eq(0)', row).html(index);			 		
		},
	});	
	
	$('#from_date, #to_date').change(function(){
		if($(this).val() == "")
		{
			swal.fire("Mohon Maaf!", "Tanggal tidak boleh kosong, terima kasih", "error");
			$(this).datepicker().datepicker('setDate', new Date());
		}
	});

	$('#from_date, #to_date, #department_code, #subdepartment_code, #customer_code, #sales_code, #ppn, #view_type').change(function(){
		get_total_product_sales_report(); chart_product_sales();
		table.ajax.reload();
	});

	$("#search").keypress(function(e){
		var key = e.which;
		if(key == 13)
		{			
			get_total_product_sales_report(); chart_product_sales();
			table.ajax.reload();
		}
	});

	get_total_product_sales_report(); chart_product_sales();
});

function get_total_product_sales_report()
{
	$.ajax({
		type: "POST",
		url: "report/Sales_report/get_total_product_sales_report/",		
		dataType: "JSON",
		data: {
			from_date 	: $('#from_date').val(),
			to_date 	: $('#to_date').val(),				
			department_code  : $('#department_code').val(),
			subdepartment_code : $('#subdepartment_code').val(),						
			customer_code  	   : $('#customer_code').val(),
			sales_code  	   : $('#sales_code').val(),
			ppn 		: $('#ppn').val(),
			search 		: $('#search').val()
		},
		success: function(data) {                    			
			$('#total_product').html(data['total_product']);
			$('#total_qty').html(data['total_qty']);
			$('#total_grandtotal').html(data['grandtotal']);
		}
	}); 
}

function chart_product_sales()
{
    $.ajax({
        type: "POST",
        url: "report/Sales_report/chart_product_sales/",		
        dataType: "JSON",
        data: {
            view_type : $('#view_type').val(),
			from_date : $('#from_date').val(),
			to_date : $('#to_date').val(),
            department_code   : $('#department_code').val(),
            subdepartment_code   : $('#subdepartment_code').val(),
			customer_code : $('#customer_code').val(),
			sales_code     : $('#sales_code').val(),
			search : $('#search').val()
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
					ykeys: ['grandtotal', 'total_qty'],
					labels: ['Total', 'Qty'],
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