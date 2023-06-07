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
	
	$.getJSON("report/Inventory_report/user_product_usage", (data) => {
        var option = '<option value="" class="text-dark">- SEMUA USER -</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i) {
                option += '<option value="' + data.response[i].code + '" class="text-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
            });
        } else {
            option = option;
        }
        $("#user_code").html(option).select2();
	});
	
    $('#print_product_usage_detail_report').click(function(){		
		$('#filter_form').attr('target', '_blank').attr('action', 'report/Inventory_report/print_product_usage_detail_report').submit();
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
			"url"		: "report/Inventory_report/product_usage/",
			"type"		: "POST",
			"data":function(data){
				data.from_date 	   = $('#from_date').val();
				data.to_date 	   = $('#to_date').val();
				data.user_code 	   = $('#user_code').val();
            }
		},
		columns: [				
            {"data": "id", className: 'text-dark text-center', width: '10px'},
			{"data": "date", className: 'text-dark text-center', width: '100px'},
			{"data": "code", className: 'text-dark text-center', width: '100px'},
			{"data": "grandtotal", className: 'text-primary text-right',  render: $.fn.dataTable.render.number(',', '.', 2)},
			{"data": "name_u", className: 'text-dark text-left'},
			{"data": "search_code", className: 'kt-hidden'}
		],    
		columnDefs: [
            {
                targets: 1, 
                render : function(val){
					return format_date(val);
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

	$('#from_date, #to_date, #user_code').change(function(){
        total_product_usage();
		table.ajax.reload();
    });
    
    total_product_usage();
});

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}

function total_product_usage()
{
	$.ajax({
		type: "POST",
		url: "report/Inventory_report/total_product_usage/",
		dataType: "JSON",
		data: {
			from_date 		: $('#from_date').val(),
			to_date 		: $('#to_date').val(),
			user_code 		: $('#user_code').val()
		},
		success: function(data) {
			$('#grandtotal').html(data['grandtotal']);
		}
	}); 
}