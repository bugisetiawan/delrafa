$(document).ready(function(){
    $('#date').datepicker({
        format: "dd-mm-yyyy",
        todayHighlight: true,
		clearBtn: true,
		autoclose: true,
		orientation: "bottom auto"
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
            prefetch:"finance/Accounting/get_account",
            remote:{
                url:"finance/Accounting/get_account/%QUERY%/",
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

    $('#journal_notify').hide();
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
    var total_account = Number($(".debit").length);

    var total_debit = 0;
    $('.debit').each(function(){
        total_debit += Number($(this).val().replace(/\,/g, ""));
    });
    $('#total_debit').val(total_debit);

    var total_credit = 0;
    $('.credit').each(function(){
        total_credit += Number($(this).val().replace(/\,/g, ""));
    });
    $('#total_credit').val(total_credit);

    if(total_account >= 2)
    {
        if(total_debit > 0 && total_credit > 0)
        {
            if(total_debit == total_credit)
            {
                $('#btn_save').prop('disabled', false);
                $('#journal_notify').hide();
            }
            else
            {
                $('#btn_save').prop('disabled', true);
                var message = "MOHON PERHATIAN! JUMLAH TOTAL DEBIT DAN KREDIT TERDAPAT SELISIH "+format_amount(String(Math.abs(total_debit-total_credit)));
                $('#journal_notify').html(message); $('#journal_notify').show();
            }
        }
        else
        {
            $('#btn_save').prop('disabled', true);
        }
    }
    else
    {
        var message = "MOHON PERHATIAN! MINIMAL 2 AKUN UNTUK MELAKUKAN JURNAL";
        $('#journal_notify').html(message); $('#journal_notify').show();
        $('#btn_save').prop('disabled', true);
    }    
}