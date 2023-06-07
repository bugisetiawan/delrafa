$(document).ajaxStart(function(){    
    KTApp.block('#filter_form',{
		overlayColor: '#000000',
        type: 'v2',
        state: 'primary',
        message: '<span class="text-dark font-weight-bold">Mohon menunggu, sedang dalam proses...</span>'
    });
});

$(document).ajaxStop(function(){
    KTApp.unblock('#filter_form');
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
        searching: true,
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
			"url"	: "report/Stock_report/stock_card/",
            "type"	: "POST",
            "data"  :function(data){
                data.from_date 		= $('#from_date').val();
                data.to_date 		= $('#to_date').val();
				data.department_code    = $('#department_code').val();
				data.subdepartment_code = $('#subdepartment_code').val();
				data.transaction_type 	= $('#transaction_type').val();
				data.warehouse_id   = $('#warehouse_id').val();
            }
		},
		columns: [				
            {"data": "id_sc", className: "text-dark text-center", width:"10px"},
            {"data": "invoice", className: "text-primary text-left"},
            {"data": "code_p", className: "text-center"},
            {"data": "name_p", className: "text-dark text-left"},
            {"data": "qty", className: 'text-right'},
            {"data": "information", className: "text-dark text-center"},
			{"data": "method", className: 'text-center'},
            {"data": "stock", className: 'text-right'},
            {"data": "name_w", className: 'text-dark text-left'},            
            {"data": "search_invoice", className: 'kt-hidden'},
            {"data": "search_code_p", className: 'kt-hidden'},
        ],
        columnDefs:[
            {
                "targets": [0, 1, 2, 3, 4, 5, 6],
                "orderable": false
            },
            { 
				targets: 1,
                render : function(data, type, row, meta){
                    if(row['type'] == 1)
                    {
                        return `<a href="purchase/invoice/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 2)
                    {
                        return `<a href="purchase/return/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 3)
                    {
                        return `<a href="pos/transaction/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 4)
                    {
                        return `<a href="sales/invoice/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 5)
                    {
                        return `<a href="sales/return/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 6)
                    {
                        return `<a href="stock/production/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 7)
                    {
                        return `<a href="stock/repacking/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 8)
                    {
                        return `<a href="stock/opname/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 9)
                    {
                        return `<a href="stock/mutation/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else
                    {
                        return `-`;
                    }                 
                }
			},
            { 
				targets: 4,
                render : function(data, type, row, meta){
                    if(row['method'] == 1)
                    {
                        return `<span class="text-success">`+data+`</span>`;
                    }   
                    else
                    {
                        return `<span class="text-danger">`+data+`</span>`;
                    }                 
                }
			},
            { 
				targets: -5,
                render : function(data, type, row, meta){
                    if(data == 1)
                    {
                        return `<span class="text-success">MASUK</span>`;
                    }   
                    else
                    {
                        return `<span class="text-danger">KELUAR</span>`;
                    }                 
                }
            },
            { 
				targets: -4,
                render : function(data, type, row, meta){
                    if(Number(data) > 0)
                    {
                        return `<span class="text-primary">`+data+`</span>`;
                    }   
                    else
                    {
                        return `<span class="text-danger">`+data+`</span>`;
                    }                 
                }
			}
        ],
		order: [[0, 'desc']],
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
        var option = '<option value="" class="text-dark">- SEMUA DEPARTEMEN -</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i, item) {
                option += '<option value="' + data.response[i].code + '" class="text-dark">' + data.response[i].name + '</option>';
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
                alert(err);
                alert(err.responseText);
            }
        });                
    });
    
    $.ajax({
		type: "POST",
		url: "report/Stock_report/get_warehouse/",
		dataType: "JSON",
		success: function(data) {                                       
			$('#warehouse_id').html(data.option);
		}
    });
    
    $('#from_date, #to_date, #department_code, #subdepartment_code, #transaction_type, #warehouse_id').change(function(){
		table.ajax.reload();
	});
});

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}