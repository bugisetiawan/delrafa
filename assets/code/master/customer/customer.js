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
		bStateSave: true,
		fnStateSave: function (oSettings, oData) {
			localStorage.setItem('DataTables_' + window.location.pathname, JSON.stringify(oData));
		},
		fnStateLoad: function (oSettings) {
			var data = localStorage.getItem('DataTables_' + window.location.pathname);
			return JSON.parse(data);
		},
		responsive: true,
		processing: true,
		language: {            
			'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
		},
		serverSide: true,
		pageLength: 25,
		lengthMenu: [ [25, 50, 75, -1], [25, 50, 75, "All"] ],
		ajax: {
			"url": "master/Customer/", 
			"type": "POST",
			"data":function(data){
                data.status = $('#status').val();
            }
		},
		columns: [				
			{"data": "id", className: 'kt-font-dark text-center', width: '10px', orderable:false},
			{"data": "code", className: 'kt-font-dark text-center', width: '100px'},
			{"data": "name", className: 'kt-font-dark'},			
			{"data": "contact", className: 'kt-font-dark'},
			{"data": "telephone", className: 'kt-font-dark'},				
			{"data": "phone", className: 'kt-font-dark'},				
			{"data": "zone", className: 'kt-font-dark'},
			{"data": "pkp", className: 'kt-font-dark text-center'},
			{"data": "status", className: 'kt-font-dark text-center'}
		],
		columnDefs: [
			{ 
				targets: -2, 
				render : function(val){
					if(val == 1)
					{
						return `<span class="text-success"><i class="fa fa-check"></i></span>`;
					}
					else
					{
						return `<span class="text-danger"><i class="fa fa-times"></i></span>`;
					}
				}
			},
			{ 
				targets: -1, 
				render : function(val){
					if(val == 1)
					{
						return `<span class="text-success"><i class="fa fa-check"></i></span>`;
					}
					else
					{
						return `<span class="text-danger"><i class="fa fa-times"></i></span>`;
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