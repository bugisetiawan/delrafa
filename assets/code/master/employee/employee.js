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
		bStateSave: true,
        fnStateSave: function (oSettings, oData) {
            localStorage.setItem('DataTables_' + window.location.pathname, JSON.stringify(oData));
        },
        fnStateLoad: function (oSettings) {
            var data = localStorage.getItem('DataTables_' + window.location.pathname);
            return JSON.parse(data);
        },
		processing: true,
		language: {            
			'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
		},
		serverSide: true,
		pageLength: 25,
		lengthMenu: [ [25, 50, 75, -1], [25, 50, 75, "All"] ],
		ajax: {
			"url": "master/Employee/datatable/", 
			"type": "POST"
		},
		columns: [				
			{"data": "id", className: "kt-font-dark text-center", width: "10px"},
			{"data": "code", className: "kt-font-dark text-center", width: "100px"},
			{"data": "name", className: "kt-font-dark"},
			{"data": "position", className: "kt-font-dark text-center", width: "100px"},
			{"data": "status", className: "kt-font-dark text-center", width: "150px"},				
		],
		columnDefs:[
            { 
                targets: -1, 
                orderable : false,
                render : function(val){
                    if(val == 0)
                    {
                        return `<span class="text-danger">TIDAK AKTIF</span>`;
                    }
                    else
                    {
                        return `<span class="text-success">AKTIF</span>`;
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
});