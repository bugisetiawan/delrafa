$(document).ajaxStart(function(){    
    KTApp.block('#filter_portlet',{
		overlayColor: '#000000',
        type: 'v2',
        state: 'primary',
        message: '<span class="text-dark font-weight-bold">Mohon menunggu, sedang dalam proses...</span>'
    });
});

$(document).ajaxStop(function(){
    KTApp.unblock('#filter_portlet');
});

$(document).ready(function() {	
	$('.date').datepicker({
		format: "dd-mm-yyyy",		
		todayHighlight: true,
		clearBtn: false,
		autoclose: true,
		orientation: "bottom auto"
	});

	$.getJSON("finance/Cash_ledger/get_cash_ledger_account/4", (data) => {
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
	
	$('#print_cash_report').click(function(){		
		if($('#cl_type').val() != "" && $('#account_id').val() != "")
		{
			$('#filter_form').attr('target', '_blank').attr('action', 'report/Finance_report/print_cash_report').submit();
		}
		else
		{
			swal.fire(
				'Mohon Maaf',
				'Pilih salah satu akun terlebih dahulu, terima kasih',
				'error'
			);
		}		
	});

	total_customer_deposit();

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
			"url": "report/Finance_report/customer_deposit", 
			"type": "POST",
			"data":function(data){
				data.from_date  = $('#from_date').val();
				data.to_date    = $('#to_date').val();
                data.account_id = $('#account_id').val();
            }
		},
		columns: [
			{"data": "id", className:'text-dark text-center'},
			{"data": "cla_code", className:'text-dark text-center'},			
			{"data": "date", className:'text-dark text-center'},
			{"data": "invoice"},
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
				targets: 3,
                render : function(data, type, row, meta){
                    if(row['information'] == "PEMBAYARAN PEMBELIAN")
                    {
                        return `<a href="payment/debt/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['information'] == "PEMBAYARAN PENJUALAN")
                    {
                        return `<a href="payment/receivable/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['information'] == "KAS&BANK MASUK/KELUAR")
                    {
                        return `<a href="cash_ledger/in_out/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['information'] == "KAS&BANK MUTASI")
                    {
                        return `<a href="cash_ledger/mutation/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['information'] == 5)
                    {
                        return `<a href="sales/return/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['information'] == 6)
                    {
                        return `<a href="stock/production/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['information'] == 7)
                    {
                        return `<a href="stock/repacking/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['information'] == 8)
                    {
                        return `<a href="stock/opname/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['information'] == 9)
                    {
                        return `<a href="stock/mutation/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
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
						return '<span class="text-success">'+format_amount(val)+' D</span>';
					}
					else
					{
						return '<span class="text-danger">'+format_amount(val)+' K</span>';
					}
                }
			},
			{ 
                targets: -1, 
                render : function(val){                    
					if(Number(val) >= 0)
					{
						return '<span class="text-primary">'+format_amount(val)+'</span>';
					}
					else
					{
						return '<span class="text-danger">('+format_amount(val)+')</span>';
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

	$('#from_date, #to_date').on('change', function(){
		if($(this).val() == "")
		{
			swal.fire("Mohon Maaf!", "Tanggal tidak boleh kosong, terima kasih", "error");
			$(this).datepicker().datepicker('setDate', new Date());
		}
		total_customer_deposit(); table.ajax.reload();
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
        total_customer_deposit(); table.ajax.reload();
    });
});

function total_customer_deposit()
{
	$.ajax({
		type: "POST",
		url: "report/Finance_report/total_customer_deposit/",
		dataType: "JSON",
		data: {
			from_date 		: $('#from_date').val(),
			to_date 		: $('#to_date').val(),
			account_id 	    : $('#account_id').val()
		},
		success: function(data) {                    
			$('#last_balance').html(data['last_balance']);
			$('#total_debit').html(data['total_debit']);
			$('#total_credit').html(data['total_credit']);
			$('#end_balance').html(data['end_balance']);
		}
	}); 
}

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