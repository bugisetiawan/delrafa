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
			"url": "finance/Cash_ledger/bank", 
			"type": "POST",
			"data":function(data){
                data.account_id      = $('#account_id').val();
            }
		},
		columns: [
			{"data": "id", className:'text-dark text-center'},
			{"data": "cla_code", className:'text-dark text-center'},
			{"data": "date", className:'text-dark text-center'},
			{"data": "note", className:'text-dark'},
			{"data": "amount", className:'text-right'},
			{"data": "balance", className:'text-right'}
		],
		columnDefs: [
			{ 
                targets: [0,1,2,3,4,5],
                orderable : false
			},
            { 
                targets: 2, 
                render : function(val){                    
                    return format_date(val);
                }
			},
			{ 
                targets: -2, 
                render : function(val, type, row, meta){
					if(row['method'] == 1)
					{
						return '<span class="text-success">'+format_number(val)+' D</span>';
					}
					else
					{
						return '<span class="text-danger">'+format_number(val)+' K</span>';
					}
                }
			},
			{ 
                targets: -1, 
                render : function(val){                    
					if(Number(val) >= 0)
					{
						return '<span class="text-primary">'+format_number(val)+'</span>';
					}
					else
					{
						return '<span class="text-danger">('+format_number(val)+')</span>';
					}
                }
			}
        ],
		rowCallback: function(row, data, iDisplayIndex) {
			var info = this.fnPagingInfo();
			var page = info.iPage;
			var length = info.iLength;
			var index = page * length + (iDisplayIndex + 1);
			$('td:eq(0)', row).html(index);
		}
	});			

	$.getJSON("finance/Cash_ledger/get_cash_ledger_account/2", (data) => {
        var option = '<option value="" class="text-dark">- PILIH AKUN -</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i) {
                option += '<option value="' + data.response[i].id + '" class="text-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
            });
        } else {
            option = option;
        }        
        $("#account_id").html(option).select2();
    });

    $("#account_id").change(function() {
        if($(this).val() == "")
        {
            $('#account_id_notify').show('slow');
        }
        else
        {
            $('#account_id_notify').hide('slow');
        }
        table.ajax.reload();
	});

	$('.date').datepicker({
        format: "dd-mm-yyyy",		
		todayHighlight: true,
		clearBtn: true,
		autoclose: true,
		orientation: "bottom auto"
	});
	
	$('#mutation_transaction').hide();
	$('#transaction_type').change(function(){
		if($(this).val() == 2)
		{
			$('#from_cl_type, #from_account_id').prop( "required", true);
			$('#mutation_transaction').show();
		}
		else
		{
			$('#from_cl_type, #from_account_id').prop( "required", false);
			$('#mutation_transaction').hide();
		}
	});

	$.getJSON("finance/Cash_ledger/get_employee/"+$('#from_cl_type').val(), (data) => {	
		var option = '<option value="" class="kt-font-dark">- PILIH KARYAWAN -</option>';
		if(data.status.code == 200) {
			$.each(data.response, function(i) {
				option += '<option value="' + data.response[i].code + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
			});
		} else {
			option = option;
		}
		$("#from_account_id").html(option);
	});

	$('#from_cl_type').change(function(){
		if($(this).val() == 1 || $(this).val() == 2)
		{
			var option = '<option value="" class="kt-font-dark">- PILIH KARYAWAN -</option>';
			$.getJSON("finance/Cash_ledger/get_employee/"+$('#from_cl_type').val(), (data) => {	
				if (data.status.code == 200) {
					$.each(data.response, function(i) {
						option += '<option value="' + data.response[i].code + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
					});
				} else {
					option = option;
				}
				$("#from_account_id").html(option);
			})
		}		
		else if($(this).val() == 3)
		{
			var option = '<option value="" class="kt-font-dark">- PILIH AKUN/REK -</option>';
			$.getJSON("finance/Cash_ledger/get_bank_account", (data) => {
				if (data.status.code == 200) {
					$.each(data.response, function(i, item) {
						option += '<option value="' + data.response[i].id_ba + '" class="text-dark">' + data.response[i].code_b +' | '+ data.response[i].number_ba + ' | ' + data.response[i].name_ba + '</option>';
					});				
				} else {
					option = option;
				}			
				$("#from_account_id").html(option);
			}); 
		}
		else
		{			
			alert('Mohon maaf, terjadi kesalahan');
		}
	});

	$('.price').keyup(function() {
        $(this).val(format_number($(this).val()));
    });

	$('#create_data').on('submit', function(e) {
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
					$(".clear").val(null);
					$('#mutation_transaction').hide();
					$('#transaction_type').val(1).change();
					$('#add_balance_form').modal('hide');
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
	});

	$("#table_data").on('click', '.delete', function() {
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
					url		: "finance/Cash_ledger/delete",
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