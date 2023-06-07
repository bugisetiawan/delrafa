$(document).ready(function() {	
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
		language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
		serverSide: true,
		pageLength: 25,
        lengthMenu: [ [25, 50, 75, -1], [25, 50, 75, "All"] ],
		ajax: {
			"url": "master/Depreciation/datatable/", 
			"type": "POST"
		},
		columns: [
			{"data": "id", className:'text-dark text-center', width:'10px'},
			{"data": "date", className:'text-dark text-center', width:'100px'},
			{"data": "name", className:'text-dark'},
			{"data": "price", className:'text-dark text-right', width:'100px', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "period", className:'text-dark text-right', width:'50px', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "value", className:'text-dark text-right', width:'100px', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "datediff", className:'text-dark text-right', width:'100px'},
			{"data": "view", className:'text-dark text-center', width:'120px'}
		],
		columnDefs: [
			{ 
                targets: 1, 
                render : function(val){
					return format_date(val);    
                }
			},
			{ 
                targets: -2, 
                render : function(data, type, row, meta){
					if(Number(data) >= Number(row["period"]))
					{
						return 0;
					}
					else
					{
						var result = Number(row["price"]) - Number((row["value"]*data));
						return format_number(String(result));
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
		}
	});			

	$('.date').datepicker({
        format: "dd-mm-yyyy",		
		todayHighlight: true,
		clearBtn: true,
		autoclose: true,
		orientation: "bottom auto"
	});
	
	$('#name').keyup(function() {
        $(this).val($(this).val().toUpperCase());
	});

	$('.ammount').keyup(function() {
        $(this).val(format_number($(this).val()));
    });

	$('#create_data, #update_data').on('submit', function(e) {
		let t = $(this);
		e.preventDefault();
		$.ajax({
			url: t.attr('action'),
			method: t.attr('method'),
			dataType	: "JSON",
			data: t.serialize(),
			success		: (data) => {				
				if(data.status.code	== 200) 
				{					
					toastr.success(data.status.message);
					$("input").val(null);
					$('#add_depreciation_form, #update_depreciation_form').modal('hide');
					table.ajax.reload();					
				} else 
				{
					toastr.error(data.status.message);
				}
			},
			error		: (err)	=> {
				toastr.error(err.responseText);            
			}
		})
	});
	
	$("#datatable").on('click', '.update', function() {
		var myId = $(this).data('id');
		$.ajax({
			type: "GET",
			url: "master/Depreciation/get_detail_depreciation/",
			dataType: "JSON",
			data: {
				id: myId
			},
			success: function(data) {				
				$.each(data, function() {
                    $('#e_depreciation_id').val(data.id);
                    $('#e_date').val(format_date(data.date));
                    $('#e_name').val(data.name);
                    $('#e_price').val(format_number(data.price));
                    $('#e_period').val(format_number(data.period));
					$('#update_depreciation_form').modal('show');
				});								
			}
		});							
	});
	
	$("#datatable").on('click', '.delete', function() {
		var myId = $(this).data('id');
		swal.fire({
			title: 'Hapus Data?',
			text: "Data yang dihapus sudah tidak dapat dikembalikan lagi",
			type: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Ya',
			cancelButtonText: 'Tidak',
			reverseButtons: true
		}).then(function(result){
			if (result.value) {
				$.ajax({
					url		: "master/Depreciation/delete",
					method	: "GET",
					dataType: "JSON",
					data: {
						id: myId
					},
					success		: (data) => {						
						if(data.status.code	== 200) 
						{					
							toastr.success(data.status.message);
							table.ajax.reload();
						}
						else 
						{
							toastr.error(data.status.message);
						}
					},
					error		: (err)	=> {
						toastr.error(err.responseText);            
					}
				})			
			} else if (result.dismiss === 'cancel') {
				swal.fire(
					'Hapus Data Dibatalkan',
					'',
					'error'
				);				
			}
		});
	});		
});

function format_date(date){	 
	day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}

function format_number(angka, prefix){
	var number_string = angka.replace(/[^.\d]/g, '').toString(),
	split   		= number_string.split('.'),
	sisa     		= split[0].length % 3,
	rupiah     		= split[0].substr(0, sisa),
	ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);
	if(ribuan){
		separator = sisa ? ',' : '';
		rupiah += separator + ribuan.join(',');
	}	
	rupiah = split[1] != undefined ? rupiah + '.' + split[1] : rupiah;
	return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
}