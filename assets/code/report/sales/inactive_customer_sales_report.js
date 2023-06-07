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

	$.getJSON("master/Zone/get_zone", (data) => {
		for(var i=0; i<data.response.length; i++){
			$("#zone_id").append($('<option>', {value: data.response[i].id, text: data.response[i].code}));				
		}		
		$('#zone_id').select2();
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
		processing: true,
		responsive: true,
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
			"url"		: "report/Sales_report/inactive_customer_sales/", 
			"type"		: "POST",			
            "data":function(data){
                data.from_date	= $('#from_date').val();
				data.to_date	= $('#to_date').val();
				data.zone_id	= $('#zone_id').val();
            }
		},
		columns:[
			{"data": "id", className: "kt-font-dark text-center", width: "10px"},
			{"data": "code", className: "kt-font-dark text-center", width: "100px"},
            {"data": "name", className: "kt-font-dark text-left"},
			{"data": "address", className: 'kt-font-dark text-left'},
			{"data": "contact", className: 'kt-font-dark text-left'},
			{"data": "telephone", className: 'kt-font-dark text-left'},
			{"data": "phone", className: 'kt-font-dark text-left'},
			{"data": "search_code", className: 'kt-hidden'}
        ],        
		order: [[1, 'ASC']],
		rowCallback: function(row, data, iDisplayIndex) {
			var info = this.fnPagingInfo();
			var page = info.iPage;
			var length = info.iLength;
			var index = page * length + (iDisplayIndex + 1);
			$('td:eq(0)', row).html(index);			 		
		},
	});			
	
	$('#from_date, #to_date, #zone_id').change(function(){
		table.ajax.reload();
	});	
});

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}

