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
			"url"		: "report/Finance_report/expense/", 
			"type"		: "POST",			
            "data":function(data){
                data.from_date 		= $('#from_date').val();
				data.to_date 		= $('#to_date').val();	
				data.cost_id 		= $('#cost_id').val();
				data.employee_code 	= $('#employee_code').val();
            }
		},
		columns: [				
			{"data": "id", className:'text-dark text-center', width:'10px'},		
			{"data": "date", className:'text-dark text-center', width:'100px'},			
            {"data": "name_c", className:'text-dark'},
            {"data": "code", className:'text-dark'},
			{"data": "information", className:'text-dark'},
			{"data": "amount", className:'text-primary text-right', width:'100px', render: $.fn.dataTable.render.number(',', '.', 0)},			
			{"data": "name_e", className:'text-dark text-left'},
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
					if(val == "")
					{
						return "-";
					}
					else if(val)
					{
						return val;
					}
                }
            },		
			{ 
                targets: 4, 
                render : function(val){
					if(val == "")
					{
						return "-";
					}
					else if(val)
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
		},
	});	
	
	$('.date').datepicker({
		format: "dd-mm-yyyy",		
		todayHighlight: true,
		clearBtn: true,
		autoclose: true,
		orientation: "bottom auto"
	});
	
	$.getJSON("report/Finance_report/get_expense_cost_report", (data) => {
		var option = '<option value="" class="kt-font-dark">- SEMUA BIAYA -</option>';
		if (data.status.code == 200) {
			$.each(data.response, function(i) {
				option += '<option value="' + data.response[i].id + '" class="kt-font-dark">' +data.response[i].id+ ' | ' +data.response[i].name + '</option>';
			});
		} 
		else 
		{
			option = option;
		}   
		$("#cost_id").html(option).select2();
	});

	$.getJSON("report/Finance_report/get_expense_employee_report", (data) => {
		var option = '<option value="" class="kt-font-dark">- SEMUA KARYAWAN -</option>';
		if (data.status.code == 200) {
			$.each(data.response, function(i) {
				option += '<option value="' + data.response[i].code + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
			});
		} 
		else 
		{
			option = option;
		}   
		$("#employee_code").html(option).select2();
	});

	$('#from_date, #to_date, #cost_id, #employee_code').change(function(){		
		get_total_expense_report();
		table.ajax.reload();
	});

	get_total_expense_report();
});

function get_total_expense_report()
{
	$.ajax({
		type: "POST",
		url: "report/Finance_report/get_total_expense_report/",		
		dataType: "JSON",
		data: {
			from_date 		: $('#from_date').val(),
			to_date 		: $('#to_date').val(),			
			cost_id 		: $('#cost_id').val(),
			employee_code 	: $('#employee_code').val()
		},
		success: function(data) {                    
			$('#total_grandtotal').html(data['grandtotal']);			
		}
	}); 
}

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}