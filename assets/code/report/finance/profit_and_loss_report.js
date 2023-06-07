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
		clearBtn: true,
		autoclose: true,
		orientation: "bottom auto"
    });			
    
    $('#from_date, #to_date').change(function(){		
		profit_and_loss_report();		
	});

	profit_and_loss_report();
});

function profit_and_loss_report()
{
	$.ajax({
		type: "POST",
		url: "report/Finance_report/profit_and_loss/",		
		dataType: "JSON",
		data: {
			from_date 		: $('#from_date').val(),
			to_date 		: $('#to_date').val()
		},
		success: function(data) {
            $('#periode_from').html($('#from_date').val());
            $('#periode_to').html($('#to_date').val());
            $('#total_sales').html(data['total_sales']);
            $('#total_sales_return').html(data['total_sales_return']);
            $('#total_other_income').html(data['total_other_income']);
            $('#net_sales').html(data['net_sales']);
            $('#total_hpp').html(data['total_hpp']);
            $('#total_net_hpp').html(data['total_hpp']);
            $('#gross_profit').html(data['gross_profit']);
            $('.list_of_expense').remove();            
            $(data['list_of_expense']).insertAfter($('#profit_and_lost_table tr#expense_header'));
            $('#total_expense').html(data['total_expense']);
            $('#total_stock_opname').html(data['total_stock_opname']);
            $('#net_profit').html(data['net_profit']);
		}
	}); 
}

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}