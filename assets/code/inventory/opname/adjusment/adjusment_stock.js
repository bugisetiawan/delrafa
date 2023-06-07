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
		lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
		ajax: {
			"url"		: "transaction/Stock/datatable_adjusment_stock/", 
			"type"		: "POST",
			"data":function(data){
				data.from_date 	  = $('#from_date').val();
				data.to_date 	  = $('#to_date').val();
				data.warehouse_id = $('#warehouse_id').val();
            }
		},
		columns: [				
			{"data": "id", className: "text-center", width: "10px"},
			{"data": "date", className: "text-center", width: "100px"},
			{"data": "code", className: "text-center", width: "100px"},
			{"data": "total_product", className: 'text-center'},
			{"data": "checker"},
			{"data": "operator"},			
			{"data": "warehouse", className: 'text-center'},
			{"data": "status", className: 'text-center'}
		],    
		columnDefs: [
            { 
                targets: 1, 
                render : function(val){
					return change_date(val);
                }
			},	
			{ 
                targets: -1, 
                render : function(val){
					if(val == "1")
					{
						return "<b class='text-succes'>SUDAH DI ADJUST</b>";
					}
					else
					{
						return "<b class='text-warning'>DALAM PROSES</b>";
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
		format: "dd/mm/yyyy",		
		todayHighlight: true,
		clearBtn: true,
		autoclose: true,
		orientation: "bottom auto"       
	});		
	
	$.getJSON("transaction/Stock/get_warehouse", (data) => {
        var option = '<option value="" class="kt-font-dark">-- Pilih Gudang --</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i) {
                option += '<option value="' + data.response[i].id + '" class="kt-font-dark">' + data.response[i].name + '</option>';
            });
        } else {
            option = option;
        }
        $("#warehouse_id").html(option).select2();
	});
	
	$.getJSON("transaction/Stock/get_employee", (data) => {
        var option = '<option value="" class="kt-font-dark">-- Pilih Karyawan --</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i) {
                option += '<option value="' + data.response[i].code + '" class="kt-font-dark">' + data.response[i].code +' | '+ data.response[i].name + '</option>';
            });
        } else {
            option = option;
        }
        $("#checker").html(option).select2();
    });
	
	$('#from_date, #to_date, #warehouse_id').change(function(){
		table.ajax.reload();
	});    
});

function change_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}