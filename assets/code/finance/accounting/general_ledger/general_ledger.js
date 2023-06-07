$(document).ajaxStart(function(){    
    KTApp.blockPage({
		overlayColor: '#000000',
        type: 'v2',
        state: 'primary',
        message: '<span class="text-dark font-weight-bold">Mohon menunggu, sedang dalam proses...</span>'
    });
});

$(document).ajaxStop(function(){
    KTApp.unblockPage();
});

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
		dom: "<'row'<'col-sm-12 col-md-6 text-left'l><'col-sm-12 col-md-6 text-right'fB>>" +
			 "<'row'<'col-sm-12'tr>>" +
			 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
		buttons: [
			{
				extend: 'print',
				text: 'Print',				
				exportOptions: {
					columns: ':not(.notexport)'
				}
			},
			{
				extend: 'copyHtml5',
				text: 'Copy',				
				exportOptions: {
					columns: ':not(.notexport)'
				}
			},
			{
				extend: 'excelHtml5',
				text: 'Excel',				
				exportOptions: {
					columns: ':not(.notexport)'
				}
			}			
		],
		ajax: {
			"url": "finance/Accounting/general_ledger", 
            "type": "POST",
            "data":function(data){
                data.coa_account_code      = $('#coa_account_code').val();
            }
		},
		columns: [
			{"data": "id", className:'text-dark text-center'},
            {"data": "code", className:'text-dark text-left'},
			{"data": "date", className:'text-dark text-center'},
            {"data": "note", className:'text-dark'},
            {"data": "debit", className:'text-right text-dark'},
			{"data": "credit", className:'text-right text-dark'},
			{"data": "balance", className:'text-right'}
		],
		columnDefs: [
			{ 
                targets: [0,1,2,3,4,5,6],
                orderable : false
            },
            { 
                targets: 1, 
                render : function(val, type, row, meta){
                    return row['code']+' | '+row['name'];
                }
			},
            { 
                targets: 2, 
                render : function(val){                    
                    return format_date(val);
                }
			},
			{ 
                targets: [-3, -2], 
                render : function(val, type, row, meta){										
                    return format_number(val);
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
    
    $.getJSON("finance/Accounting/get_coa_account", (data) => {
        var option = '<option value="" class="text-dark">- PILIH AKUN -</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i) {
                option += '<option value="' + data.response[i].code + '" class="text-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
            });
        } else {
            option = option;
        }        
        $("#coa_account_code").html(option).select2();
    });

    $("#coa_account_code").change(function() {
        if($(this).val() == "")
        {
            $('#coa_account_code_notify').show('slow');
        }
        else
        {
            $('#coa_account_code_notify').hide('slow');
        }
        table.ajax.reload();
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