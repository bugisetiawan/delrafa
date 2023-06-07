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
		processing: true,
		language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
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
			"url"		: "report/Purchase_report/product_purchase_return/", 
			"type"		: "POST",
			"data":function(data){				
                data.from_date 		= $('#from_date').val();
				data.to_date 		= $('#to_date').val();
				data.department_code = $('#department_code').val();
				data.subdepartment_code = $('#subdepartment_code').val();
				data.supplier_code = $('#supplier_code').val();
				data.ppn = $('#ppn').val();
				data.search 		= $('#search').val();
            }
		},
		columns: [				
			{"data": "id", className: "text-dark text-center", width: "10px"},			
			{"data": "code", className: "text-dark text-center", width: "100px"},
            {"data": "name", className: 'text-dark'},            			
			{"data": "total_qty", className: 'text-dark text-right', width: "50px", orderable:false},
			{"data": "unit", className: 'text-dark text-left', width: "50px",orderable:false},			
			{"data": "total_purchase_return", className: 'text-primary text-right', width: "100px", orderable:false, render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "average_purchase_return", className: 'text-dark text-right', width: "150px", orderable:false, render: $.fn.dataTable.render.number(',', '.', 0)}						
		],
		order: [[1, 'asc']],
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

	$.getJSON("master/department/get_department", (data) => {
        var option = '<option value="" class="kt-font-dark">- SEMUA DEPARTEMEN -</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i, item) {
                option += '<option value="' + data.response[i].code + '" class="kt-font-dark">' + data.response[i].name + '</option>';
            });
        } else {
            option = option;
        }
        $("#department_code").html(option).select2();
    });	

    $("#department_code").change(function() {        
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
                $("#subdepartment_code").html(option).select2();
            },
            error: (err) => {
                console.log(err);
                console.log(err.responseText);
            }
        });                
	});

	$.getJSON("report/Purchase_report/get_supplier_purchase_return_report", (data) => {
		var option = '<option value="" class="text-dark">- SEMUA SUPPLIER -</option>';
		if (data.status.code == 200) {
			$.each(data.response, function(i) {
				option += '<option value="' + data.response[i].code + '" class="text-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
			});
		} 
		else 
		{
			option = option;
		}   
		$("#supplier_code").html(option).select2();
	});
		
	$('#from_date, #to_date, #department_code, #subdepartment_code, #supplier_code, #ppn').change(function(){
		get_total_product_purchase_return_report();
		table.ajax.reload();
	});	
	
	$("#search").keypress(function(e){
		var key = e.which;
		if(key == 13)
		{			
			table.ajax.reload();			
			get_total_product_purchase_return_report();
		}
	});
	
	get_total_product_purchase_return_report();
});

function get_total_product_purchase_return_report()
{
	$.ajax({
		type: "POST",
		url: "report/Purchase_report/get_total_product_purchase_return_report/",		
		dataType: "JSON",
		data: {			
			from_date 	: $('#from_date').val(),
			to_date 	: $('#to_date').val(),
			department_code    : $('#department_code').val(),
			subdepartment_code : $('#subdepartment_code').val(),
			supplier_code      : $('#supplier_code').val(),
			ppn         : $('#ppn').val(),
			search 		: $('#search').val()
		},
		success: function(data) {
			$('#total_qty').html(data['total_qty']);
			$('#total_product').html(data['total_product']);
			$('#total_grandtotal').html(data['grandtotal']);
		}
	}); 
}


