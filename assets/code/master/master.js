jQuery(document).ready(function() {
	// Data Table Master Data
	var pathArray = window.location.pathname.split('/');
	var table = pathArray[2];
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
		serverSide: true,
		lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
		ajax: {
			"url": "master/data/get/"+table, 
			"type": "POST"
		},
		columns: [
			{"data": "id"},
			{"data": "code"},
			{"data": "name"},			
			{"data": "view"}
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

	// Create & Update Data
	$('#createData, #updateData ').on('submit', function(e) {
		let t = $(this);
		e.preventDefault();
		$.ajax({
			url: t.attr('action'),
			method: t.attr('method'),
			dataType	: "JSON",
			data: t.serialize(),
			success		: (data) => {
				toastr.options = {
					closeButton: !1,
					debug: !1,
					newestOnTop: !1,
					progressBar: !0,
					positionClass: "toast-top-right",
					preventDuplicates: !0,
					showDuration: "300",
					hideDuration: "1000",
					timeOut: "3000",
					extendedTimeOut: "1000",
					showEasing: "swing",
					hideEasing: "linear",
					showMethod: "fadeIn",
					hideMethod: "fadeOut"
				};
				if(data.status.code	== 200) 
				{					
					toastr.success(data.status.message, "Berhasil Menambahkan Data");
					$("input").val("");
					table.ajax.reload();					
				} else 
				{
					toastr.error(data.status.message, 'Gagal Menambahkan Data');
				}
			},
			error		: (err)	=> {
				toastr.error(err.responseText, 'Gagal Menambahkan Data');            
			}
		})
	});
});