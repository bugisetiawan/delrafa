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

	total();

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
			"url": "finance/Expense/", 
			"type": "POST"
		},
		columns: [
			{"data": "id", className:'text-dark text-center'},		
			{"data": "date", className:'text-dark text-center'},
			{"data": "code", className:'text-dark'},
			{"data": "name_c", className:'text-dark'},			
			{"data": "amount", className:'text-dark text-right', render: $.fn.dataTable.render.number(',', '.', 0)},			
			{"data": "information", className:'text-dark'},
			{"data": "view", className:'text-dark text-center'}
		],
		columnDefs: [
            { 
                targets: 1, 
                render : function(val){                    
                    return format_date(val);
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
	
	$("#add_expense").click(function(){
        $.getJSON("finance/Expense/get_cost", (data) => {        
			var option = '<option value="">- PILIH BIAYA -</option>';			
            if (data.status.code == 200) {
                $.each(data.response, function(i, item) {
					option += '<option value="' + data.response[i].id + '">' + data.response[i].name + '</option>';					
				});				
            } else {
                option = option;
			}			
			$("#cost_id").html(option);			
        });    
	});

	$.getJSON("finance/Cash_ledger/get_cash_ledger_account/"+$('#from_cl_type').val(), (data) => {	
		var option = '<option value="" class="kt-font-dark">- PILIH AKUN -</option>';
		if(data.status.code == 200) {
			$.each(data.response, function(i) {
				option += '<option value="' + data.response[i].id + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
			});
		} else {
			option = option;
		}
		$("#from_account_id").html(option);
	});

	$('#from_cl_type').change(function(){
		var option = '<option value="" class="kt-font-dark">- PILIH AKUN -</option>';
		$.getJSON("finance/Cash_ledger/get_cash_ledger_account/"+$('#from_cl_type').val(), (data) => {	
			if (data.status.code == 200) {
				$.each(data.response, function(i) {
					option += '<option value="' + data.response[i].id + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
				});
			} else {
				option = option;
			}
			$("#from_account_id").html(option);
		})
	});

	$('.date').datepicker({
        format: "dd-mm-yyyy",		
		todayHighlight: true,
		clearBtn: true,
		autoclose: true,
		orientation: "bottom auto"
	});
	
	$('#invoice').keyup(function() {
        $(this).val($(this).val().toUpperCase());
	});

	$('.amount').click(function() {
        $(this).select();
	});

	$('.amount').keyup(function() {
        $(this).val(format_amount($(this).val()));
	});		
	
	$('#from_account_id').on('change',function(){
		$.ajax({
			type: "POST", 
			url: 'finance/Cash_ledger/get_last_balance',
			data: {
				cl_type: $('#from_cl_type').val(),
				account_id: $('#from_account_id').val()
			}, 
			dataType: "json",
			beforeSend: function(e) {
				if(e && e.overrideMimeType) {
					e.overrideMimeType("application/json;charset=UTF-8");
				}
			},
			success: function(response){
				$('#cash_ledger_balance').val(response);
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
			}
		});
	});

	$('#amount').on('keyup',function(){
		$(this).val($(this).val().replace(/\s+/g, ''));		
		if(Number($(this).val().replace(/\,/g, "")) > Number($('#cash_ledger_balance').val().replace(/\,/g, "")))
		{
			swal.fire("Mohon Maaf!", "Pengeluaran biaya tidak bisa dilakukan karena saldo tidak mencukupi, terima kasih", "error");
			$(this).val(null);
		}
	});

	$('#e_from_account_id').on('change',function(){
		$.ajax({
			type: "POST", 
			url: 'finance/Cash_ledger/get_last_balance',
			data: {
				cl_type: $('#e_from_cl_type').val(),
				account_id: $('#e_from_account_id').val()
			}, 
			dataType: "json",
			beforeSend: function(e) {
				if(e && e.overrideMimeType) {
					e.overrideMimeType("application/json;charset=UTF-8");
				}
			},
			success: function(response){
				$('#cash_ledger_balance').val(response);
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
			}
		});
	});

	$('#e_amount').on('keyup',function(){
		$(this).val($(this).val().replace(/\s+/g, ''));		
		if(Number($(this).val().replace(/\,/g, "")) > Number($('#cash_ledger_balance').val().replace(/\,/g, "")))
		{
			swal.fire("Mohon Maaf!", "Pengeluaran biaya tidak bisa dilakukan karena saldo tidak mencukupi, terima kasih", "error");
			$(this).val(null);
		}
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
					$("input, textarea").val(null);
					$('#add_expense_form, #update_expense_form').modal('hide');
					table.ajax.reload(); total();
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
	
	$("#table_data").on('click', '.update', function() {
		var myId = $(this).data('id');
		$.ajax({
			type: "GET",
			url: "finance/Expense/get_detail_expense/",		
			dataType: "JSON",
			data: {
				id: myId
			},
			success: function(data) {				
				$.each(data, function() {					
					$('#e_expense_id').val(data.expense.id);
					$('#e_date').val(format_date(data.expense.date));
					$('#e_amount').val(format_amount(data.expense.amount));
					$('#e_cost_id').val(data.expense.cost_id);
					var cost_id = data.expense.cost_id;
					$('#e_invoice').val(data.expense.invoice);
					$('#e_information').val(data.expense.information);
					var from_cl_type = data.cash_ledger.cl_type;
					var from_account_id = data.cash_ledger.account_id;
					$.getJSON("finance/Expense/get_cost", (data) => {
						var option = '<option value="">--- Pilih Biaya ---</option>';
						if (data.status.code == 200) {
							$.each(data.response, function(i, item) {
								option += '<option value="' + data.response[i].id + '">' + data.response[i].name + '</option>';							
							});				
						} else {
							option = option;
						}			
						$("#e_cost_id").html(option);
						$("#e_cost_id").html(option).val(cost_id);
					});
					$('#e_from_cl_type').val(from_cl_type);
					$.getJSON("finance/Cash_ledger/get_employee_all/"+$('#from_cl_type').val(), (data) => {	
						var option = '<option value="" class="kt-font-dark">- PILIH AKUN -</option>';
						if(data.status.code == 200) {
							$.each(data.response, function(i) {
								option += '<option value="' + data.response[i].code + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
							});
						} else {
							option = option;
						}
						$("#e_from_account_id").html(option).val(from_account_id);
					});
					$('#update_expense_form').modal('show');
				});								
			}
		});							
	});
	
	$('#e_from_cl_type').change(function(){
		if($(this).val() == 1 || $(this).val() == 2)
		{
			var option = '<option value="" class="kt-font-dark">- PILIH AKUN -</option>';
			$.getJSON("finance/Cash_ledger/get_employee_all/"+$('#e_from_cl_type').val(), (data) => {	
				if (data.status.code == 200) {
					$.each(data.response, function(i) {
						option += '<option value="' + data.response[i].code + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
					});
				} else {
					option = option;
				}
				$("#e_from_account_id").html(option);
			})
		}		
		else if($(this).val() == 3)
		{
			var option = '<option value="" class="kt-font-dark">- PILIH AKUN -</option>';
			$.getJSON("finance/Cash_ledger/get_bank_account_all", (data) => {
				if (data.status.code == 200) {
					$.each(data.response, function(i, item) {
						option += '<option value="' + data.response[i].id_ba + '" class="text-dark">' + data.response[i].code_b +' | '+ data.response[i].number_ba + ' | ' + data.response[i].name_ba + '</option>';
					});				
				} else {
					option = option;
				}			
				$("#e_from_account_id").html(option);
			}); 
		}
		else
		{			
			alert('Mohon maaf, terjadi kesalahan');
		}
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
					url		: "finance/Expense/delete",
					method	: "GET",
					dataType: "JSON",
					data: {
						id: myId
					},
					success		: (data) => {						
						if(data.status.code	== 200) 
						{					
							toastr.success(data.status.message);
							$("#code").focus();
							table.ajax.reload(); total();
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

function format_amount(angka, prefix){
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

function total()
{
	$.ajax({
		type: "POST",
		url: "finance/Expense/total/",		
		dataType: "JSON",		
		success: function(data) {
			$('#total_grandtotal').html(data['grandtotal']);
		}
	}); 
}