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
			"url": "finance/Cash_ledger/cash_ledger_mutation", 
			"type": "POST",
			"data":function(data){
                data.account_id      = $('#option_account_id').val();
            }
		},
		columns: [
			{"data": "id", className:'text-dark text-center'},
			{"data": "cla_code", className:'text-dark text-center'},
			{"data": "date", className:'text-dark text-center'},
			{"data": "invoice"},
            {"data": "note", className:'text-dark'},
            {"data": "method", className:'text-dark text-center'},
			{"data": "amount", className:'text-right'}
		],
		columnDefs: [
			{ 
                targets: [0,1,2,3,4,5,6],
                orderable : false
			},
            { 
                targets: 2, 
                render : function(val){                    
                    return format_date(val);
                }
			},
			{ 
				targets: 3,
                render : function(data, type, row, meta){                    
                    if(row['information'] == "KAS&BANK MUTASI")
                    {
                        return `<a href="cash_ledger/mutation/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else
                    {
                        return `-`;
                    }                 
                }
            },
			{ 
                targets: -2, 
                render : function(val, type, row, meta){
					if(row['method'] == 1)
					{
						return 'MASUK';
					}
					else
					{
						return 'KELUAR';
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

	$.getJSON("finance/Cash_ledger/get_cash_ledger_account/1", (data) => {
        var option = '<option value="" class="text-dark">- SEMUA AKUN -</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i) {
                option += '<option value="' + data.response[i].id + '" class="text-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
            });
        } else {
            option = option;
        }        
        $("#option_account_id").html(option).select2();
    });

    $("#option_account_id").change(function() {
        if($(this).val() == "")
        {
            $('#option_account_id_notify').show('slow');
        }
        else
        {
            $('#option_account_id_notify').hide('slow');
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

	$.getJSON("finance/Cash_ledger/get_cash_ledger_account/"+$('#to_cl_type').val(), (data) => {	
		var option = '<option value="" class="kt-font-dark">- PILIH AKUN -</option>';
		if(data.status.code == 200) {
			$.each(data.response, function(i) {
				option += '<option value="' + data.response[i].id + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
			});
		} else {
			option = option;
		}
		$("#to_account_id").html(option);
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
		});
	});

	$('#to_cl_type').change(function(){
		var option = '<option value="" class="kt-font-dark">- PILIH AKUN -</option>';
		$.getJSON("finance/Cash_ledger/get_cash_ledger_account/"+$('#to_cl_type').val(), (data) => {	
			if (data.status.code == 200) {
				$.each(data.response, function(i) {
					option += '<option value="' + data.response[i].id + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
				});
			} else {
				option = option;
			}			
			$("#to_account_id").html(option);
		});
	});

	$('.price').keyup(function() {
        $(this).val(format_number($(this).val()));
    });

	$('#create_cash_ledger_mutation').on('submit', function(e) {
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
					$(".modal:visible").modal('hide');
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