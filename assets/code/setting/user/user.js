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

	var table = $("#datatable").DataTable({
		responsive: true,
		processing: true,
		language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
		serverSide: true,
		lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
		ajax: {
			"url": "setting/User", 
			"type": "POST"
		},
		columns: [
			{"data": "id_u", className: 'text-center text-dark', width: '10px'},
			{"data": "code_u", className: 'text-center text-dark', width: '100px'},
			{"data": "name_e", className: 'text-dark'},
			{"data": "active", className: 'text-center text-dark', width: '10px'},			
		],
		columnDefs: [
            { 
                targets: -1, 
                render : function(val){
                    if(val == 0)
                    {
                        return `<i class="fa fa-times text-danger"></i>`;
                    }
                    else
                    {
                        return `<i class="fa fa-check text-success"></i>`;
                    }
                }
            },            
        ],
		order: [[0, 'asc']],
		rowCallback: function(row, data, iDisplayIndex) {
			var info = this.fnPagingInfo();
			var page = info.iPage;
			var length = info.iLength;
			var index = page * length + (iDisplayIndex + 1);
			$('td:eq(0)', row).html(index);
		}
	});
});