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

$(document).ready(function(){
	$('.date').datepicker({
		format: "dd-mm-yyyy",		
		todayHighlight: true,
		clearBtn: false,
		autoclose: true,
		orientation: "bottom auto"
	});		

    balance_sheet();

    $('#date').on('change', function(){
        balance_sheet();
	});
	
	$("#check_unbalance_gl_btn").on('click', function() {
		window.open("report/Finance_report/check_unbalance_general_ledger/", "Check Unbalance GL", "left=300, top=100, width=1080, height=500");
	});
	
	$('#balance_sheet_refresh_btn').on('click', function(){
        $.ajax({
            url: 'report/Finance_report/recalculate_general_ledger',
            type: 'POST',
            dataType: 'JSON',
			beforeSend: function(){
				KTApp.blockPage({
					overlayColor: '#000000',
					type: 'v2',
					state: 'primary',
					message: '<span class="text-dark font-weight-bold">Mohon menunggu, sedang dalam proses. Jangan tutup halaman ini...</span>'
				});
			},
            success: (data) => {
                if(data.status.code == 200)
                {
                    location.reload();
                } 
                else
                {
                    toastr.error(`${data.status.message}`);
                }
            },
            error: (err) => {
                toastr.error(`${err.responseText}`);
            }
        });        
    });
});

function balance_sheet()
{
	$.ajax({
		type: "POST",
		url: "report/Finance_report/balance_sheet/",		
		dataType: "JSON",
		data: {
			date : $('#date').val()
		},
		success: function(data){
			// ASET
			$('.aset').remove();            
			$(data['aset']).insertAfter($('#left_table tr#aset_header'));
			$('#total_aset').html(data['total_aset']);
			// KEWAJIBAN & EKUITAS
			$('.kewajiban').remove();            
			$(data['kewajiban']).insertAfter($('#right_table tr#kewajiban_header'));
			$('.ekuitas').remove();            
			$(data['ekuitas']).insertAfter($('#right_table tr#ekuitas_header'));
			$('#total_kewajiban_ekuitas').html(data['total_kewajiban_ekuitas']);
		}
	}); 
}