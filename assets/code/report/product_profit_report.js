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
                var option = '<option value="">- SEMUA SUB DEPARTEMEN -</option>';
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
		
	$('#from_date, #to_date, #department_code, #subdepartment_code').change(function(){
		total_product_profit_report();
		table.ajax.reload();
	});

	$("#search").keypress(function(e){
		var key = e.which;
		if(key == 13)
		{			
			table.ajax.reload();			
			total_product_profit_report();
		}
    });   
    
    $("#search").keyup(function(e){		
		if($(this).val() == "")
		{			
			table.ajax.reload();			
			total_product_profit_report();
		}
	});

	total_product_profit_report();

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
			"url"		: "report/Finance_report/product_profit/", 
			"type"		: "POST",
			"data":function(data){
				data.search 		= $('#search').val();
				data.department_code 	= $('#department_code').val();
				data.subdepartment_code = $('#subdepartment_code').val();
                data.from_date 		= $('#from_date').val();
				data.to_date 		= $('#to_date').val();				
            }
		},
		columns: [				
			{"data": "id", className: "text-dark text-center", width: "10px"},			
			{"data": "code", className: "text-dark text-center", width: "100px", orderable:false},
			{"data": "name", className: "text-dark"},            			
			{"data": "unit", className: 'text-dark text-left', width: "100px", orderable:false},			
			{"data": "qty_sales", className: 'text-success text-right', width: "50px"},
			{"data": "qty_sales_return", className: 'text-danger text-right', width: "50px"},
			{"data": "total_qty", className: 'text-primary text-right', width: "50px"},
			{"data": "total_sales", className: 'text-success text-right', width: "100px",render: $.fn.dataTable.render.number(',', '.', 0)},						
			{"data": "total_hpp", className: 'text-danger text-right', width: "100px", render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "profit", className: 'text-dark text-right'},
			{"data": "prosentase", className: 'text-dark text-right'}
		],
		columnDefs: [           
			{ 
                targets: -2, 
                render : function(val){
					if(val < 0)
					{
						return "<span class='text-danger'>"+$.fn.dataTable.render.number(',', '.', 2).display(val)+"<span>";
					}
					else
					{
						return "<span class='text-success'>"+$.fn.dataTable.render.number(',', '.', 2).display(val)+"<span>";
					}
                }
			},
			{ 
                targets: -1, 
                render : function(val){
					if(val < 0)
					{
						return "<span class='text-danger'>"+$.fn.dataTable.render.number(',', '.', 2).display(val)+"%<span>";
					}
					else
					{
						return "<span class='text-success'>"+$.fn.dataTable.render.number(',', '.', 2).display(val)+"%<span>";
					}
                }
			}
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
});

function total_product_profit_report()
{
	$.ajax({
		type: "POST",
		url: "report/Finance_report/total_product_profit_report/",		
		dataType: "JSON",
		data: {
			search 			   : $('#search').val(),	
			department_code    : $('#department_code').val(),
			subdepartment_code : $('#subdepartment_code').val(),
			from_date 		   : $('#from_date').val(),
			to_date 		   : $('#to_date').val()
		},
		success: function(data) {                    
			$('#total_sales').html(data['total_sales']);
			$('#total_sales_return').html(data['total_sales_return']);
			$('#total_hpp').html(data['total_hpp']);
			$('#total_profit').html(data['profit']);
		}		
	}); 
}


