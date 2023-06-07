$(document).ready(function(){
    toastr.options = {
		closeButton: !1,
		debug: !1,
		newestOnTop: !1,
		progressBar: !0,
		positionClass: "toast-top-right",
		preventDuplicates: !0,
		showDuration: "3000",
		hideDuration: "1000",
		timeOut: "3000",
		extendedTimeOut: "1000",
		showEasing: "swing",
		hideEasing: "linear",
		showMethod: "fadeIn",
		hideMethod: "fadeOut"
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
    
    var table = $("#datatable").DataTable({
        searching: true,
        responsive: true,
        processing: true,
        language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
        serverSide: true,
        paging : false,
        scrollY: '100vh',
        scrollCollapse: true,
        info: false,
        ajax: {
            "url": "transaction/Sales/sales_billing",
            "type": "POST"
        },
        columns: [				
            {"data": "id", className: 'text-dark text-center', width: '10px'},
            {"data": "date", className: 'kt-font-dark text-center'},
            {"data": "code", className: 'text-dark text-center'}
        ],        
        columnDefs: [            
            { 
				targets: 1,
                render : function(val){
                    return format_date(val);
                }
			},
        ],
        order: [[2, 'asc']],
        rowCallback: function(row, data, iDisplayIndex) {
			var info = this.fnPagingInfo();
			var page = info.iPage;
			var length = info.iLength;
			var index = page * length + (iDisplayIndex + 1);
			$('td:eq(0)', row).html(index);
		}
    });

    $('#date').change(function(){
		table.ajax.reload();
	});	
}); 

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}