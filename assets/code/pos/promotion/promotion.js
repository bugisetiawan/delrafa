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
    
    $("#datatable").DataTable({
		searching: true,
		processing: true,
		language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
		serverSide: true,
		pageLength: 25,
        lengthMenu: [ [25, 50, 75, -1], [25, 50, 75, "All"] ],
		ajax: {
			"url": "pos/Promotion/datatable_promotion/", 
			"type": "POST"
		},
		columns: [
			{"data": "id", className: "kt-font-dark text-center", width: "10px"},
			{"data": "name", className: "kt-font-dark text-left"},
			{"data": "start_date", className: "kt-font-dark text-center", width: "100px"},
            {"data": "start_time", className: "kt-font-dark text-center", width: "100px"},
            {"data": "end_date", className: "kt-font-dark text-center", width: "100px"},
            {"data": "end_time", className: "kt-font-dark text-center", width: "100px"}
        ],
        columnDefs: [
            { 
				targets: [2, -2], 
				orderable:false,
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
		}
	});
});

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}