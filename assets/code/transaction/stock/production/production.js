$(document).ready(function() {
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
        responsive: true,
        processing: true,
        language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
        serverSide: true,
        pageLength: 25,
        lengthMenu: [ [25, 50, 75, -1], [25, 50, 75, "All"] ],                
        ajax: {
            "url": "transaction/Stock/production",
            "type": "POST"
        },
        columns: [				
            {"data": "id_pro", className: 'text-dark text-center', width: '10px'},
            {"data": "date_pro", className: 'text-dark text-center', width: '100px'},
            {"data": "code", className: 'text-dark text-center', width: '100px'},
            {"data": "name_p", className: 'text-dark text-left'},
            {"data": "name_w", className: 'text-dark text-center'},
            {"data": "status", className: 'text-dark text-center', width: '100px'},            
        ],
        columnDefs: [
            { 
                targets: 1, 
                render : function(val){
                    return format_date(val);
                }
            },
            { 
                targets: -1, 
                render : function(val){
                    if(val == 0)
                    {
                        return "DALAM PROSES";
                    }
                    else
                    {
                        return "SELESAI"
                    }
                }
            }
        ],
        order: [[1, 'asc']],
        rowCallback: function(row, data, iDisplayIndex) {
            var info = this.fnPagingInfo();
            var page = info.iPage;
            var length = info.iLength;
            var index = page * length + (iDisplayIndex + 1);
            $('td:eq(0)', row).html(index);
        }
    });	 

    var mutation_id = $('#mutation_id').val();
    $("#datatable_detail_mutation").DataTable({        
		processing: true,
		language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
		serverSide: true,
		lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
		ajax: {
			"url": "transaction/Stock/datatable_detail_mutation/" + mutation_id, 
			"type": "POST"
		},
		columns: [				
			{"data": "id", className: "kt-font-dark text-center", width: "10px"},
            {"data": "code_p", className: "kt-font-dark text-center", width: "100px"},
            {"data": "name_p", className: "kt-font-dark"},
            {"data": "qty", className: "kt-font-dark text-center", width: "10px"},
            {"data": "name_u", className: "kt-font-dark text-center", width: "100px"},
        ],      
		order: [[1, 'asc']],
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