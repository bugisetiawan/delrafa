$(document).ready(function(){
    $('#date').datepicker({
        format: "dd-mm-yyyy",
        todayHighlight: true,
		clearBtn: true,
		autoclose: true,
		orientation: "bottom auto"
    });                	

    $.getJSON("finance/Cash_ledger/get_cash_ledger_account/"+$('#cl_type').val(), (data) => {	
		var option = '<option value="" class="kt-font-dark">- PILIH AKUN -</option>';
		if(data.status.code == 200) {
			$.each(data.response, function(i) {
				option += '<option value="' + data.response[i].id + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
			});
		} else {
			option = option;
		}
		$("#account_id").html(option);
    });

    $('#cl_type').change(function(){
		var option = '<option value="" class="kt-font-dark">- PILIH AKUN -</option>';
		$.getJSON("finance/Cash_ledger/get_cash_ledger_account/"+$('#cl_type').val(), (data) => {	
			if (data.status.code == 200) {
				$.each(data.response, function(i) {
					option += '<option value="' + data.response[i].id + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
				});
			} else {
				option = option;
			}			
			$("#account_id").html(option);
		});
	});
    
    $('.repeater').repeater({
        initEmpty: false,  
        isFirstItemUndeletable: false,
        hide: function () {
            $(this).remove();
            calculate();
        }
    });

    $("#account_table").keypress(function(e){
		var key = e.which;
		if(key == 13)
		{			
			e.preventDefault();
			$('#add_account').click(); $('.account_input:last').focus().select();
		}
	});

    $("#account_table").on("focus", ".account_input", function(){
        var account = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            prefetch:"finance/Cash_ledger/get_account",
            remote:{
                url:"finance/Cash_ledger/get_account/%QUERY%/",
                wildcard:"%QUERY%"
            }
        });        
        if( $(this).data('autocomple_ready') != 'ok' ){            
            var id = Math.round(new Date().getTime() +(Math.random() * 100)); $(this).attr('id',id);
            var coa_account_code = $(this).attr('id')+1; $(this).closest('tr').find('.coa_account_code').attr('id', coa_account_code);
            $('#'+id).typeahead({
                hint: true,
                highlight: true,
                minLength: 1,
            },
            {
                name: 'account',
                display: 'name',
                source: account,
                limit: 100,
                templates: {
                    empty: [
                        '<div class="empty-message" style="padding: 10px 15px; text-align: center; font-color:red;">',
                            'AKUN TIDAK DITEMUKAN',
                        '</div>'
                    ].join('\n'),
                    suggestion: Handlebars.compile('<div>{{code}}<br>{{name}}</div>')
                }                 
            });               
            $('#'+id).bind('typeahead:select', function(ev, suggestion) {                
                $(this).closest('tr').find('.coa_account_code').val(suggestion['code']);
                $('#'+id).trigger('change');
            });               
            $('#'+id).bind('typeahead:change', function(ev, suggestion) {
                $('#'+coa_account_code).val(suggestion['code']);
            });
        };        
        $(this).data('autocomple_ready','ok');
    });

    $("#account_table").on("change", ".account_input", function(){
        var id=Math.round(new Date().getTime()+(Math.random() * 100)); $(this).attr('id', id);
        var coa_account_code = $(this).attr('id')+1; $(this).closest('tr').find('.coa_account_code').attr('id', coa_account_code);
        var debit = $(this).attr('id')+2; $(this).closest('tr').find('.debit').attr('id', debit);
        var credit = $(this).attr('id')+3; $(this).closest('tr').find('.credit').attr('id', credit);
        $('#'+debit).val(0); $('#'+credit).val(0);
        calculate();
    });    

    $("#account_table").on("click", ".amount", function() {
        $(this).select();
    });

    $("#account_table").on("keyup", ".amount", function() {
        $(this).val(format_amount($(this).val()));
        calculate();
    });
});

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

function calculate()
{
    var grandtotal = 0;
    $('.amount').each(function(){
        grandtotal += Number($(this).val().replace(/\,/g, ""));
    });
    $('#grandtotal').val(grandtotal);

    if(grandtotal > 0)
    {
        $('#btn_save').prop('disabled', false);
    }
    else
    {
        $('#btn_save').prop('disabled', true);
    }
}