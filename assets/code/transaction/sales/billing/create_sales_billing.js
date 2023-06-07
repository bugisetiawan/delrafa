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
    
    $('.date').datepicker({
        format: "dd-mm-yyyy",
        todayHighlight: true,
		clearBtn: false,
		autoclose: true,
		orientation: "bottom auto"
    });

    $('.date').change(function(){
		if($(this).val() == "")
		{
			toastr.error('Tanggal wajib terisi');
			$(this).datepicker().datepicker('setDate', new Date());
		}
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
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
        serverSide: true,
        paging : false,
        scrollY: '100vh',
        scrollCollapse: true,
        info: false,
        ajax: {
            "url": "transaction/Sales/create_sales_billing",
            "type": "POST",
            "data":function(data){
                data.sales_code = $('#sales_code').val();
            }
        },
        columns: [				
            {"data": "id", className: 'text-dark text-center', width: '10px'},
            {"data": "choose", className: 'kt-font-dark text-center', width: '10px'},
            {"data": "name_c", className: 'text-dark kt-font-bold'},
            {"data": "date", className: 'text-dark text-center'},
            {"data": "invoice", className: 'text-dark text-left', width: '100px'},
            {"data": "due_date", className: 'text-dark text-center'},                        
            {"data": "account_payable", className: 'text-primary text-right', render: $.fn.dataTable.render.number(',', '.', 2)},
        ],        
        columnDefs: [
            { 
                targets: 1,
                orderable : false
            },
            { 
				targets: [3, 5],
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

    $('#sales_code').change(function(){
		table.ajax.reload();
	});	
}); 

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}