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
		format: "dd/mm/yyyy",		
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

	$.ajax({
		type: "POST",
		url: "report/Inventory_report/get_warehouse/",
		dataType: "JSON",
		success: function(data) {                                       
			$('#warehouse_id').html(data.option);
		}
	});

	$('#search').keyup(function(){
		total_inventory_value_report();
		table.ajax.reload();
	});

	$('#department_code, #subdepartment_code, #warehouse_id, #ppn').change(function(){
		total_inventory_value_report();
		table.ajax.reload();
	});

	total_inventory_value_report();

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
			"url"		: "report/Inventory_report/inventory_value/",
			"type"		: "POST",
			"data":function(data){
				data.search 			= $('#search').val();
				data.department_code 	= $('#department_code').val();
				data.subdepartment_code = $('#subdepartment_code').val();
				data.ppn                = $('#ppn').val();
                data.warehouse_id 		= $('#warehouse_id').val();
            }
		},
		columns: [				
			{"data": "id", className: "text-dark text-center", width: "10px"},			
			{"data": "code", className: "text-dark text-center", width: "100px"},
			{"data": "name", className: "text-dark"},
			{"data": "stock", className: 'text-primary text-right'},
			{"data": "unit", className: 'text-dark text-left', orderable:false},			
			{"data": "hpp", className: 'text-dark text-right', orderable:false, render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "total", className: 'text-primary text-right', orderable:false, render: $.fn.dataTable.render.number(',', '.', 0)}						
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
});

function total_inventory_value_report()
{
	$.ajax({
		type: "POST",
		url: "report/Inventory_report/total_inventory_value_report/",		
		dataType: "JSON",
		data: {
			search 			   : $('#search').val(),	
			department_code    : $('#department_code').val(),
			subdepartment_code : $('#subdepartment_code').val(),
			ppn                : $('#ppn'               ).val(),
			warehouse_id 	   : $('#warehouse_id').val()
		},
		success: function(data) {                    			
			$('#total_product').html(data['total_product']);
			$('#total_qty').html(data['total_qty']);	
			$('#total_grandtotal').html(data['grandtotal']);		
		}
	}); 
}