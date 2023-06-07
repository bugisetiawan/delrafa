$(document).ajaxStart(function(){
    KTApp.blockPage({
		overlayColor: '#000000',
        type: 'v2',
        state: 'primary',
        message: '<span class="text-dark font-weight-bold">Mohon menunggu, sedang dalam proses...</span>'
    });
    $('#btn-search-product').prop('disabled', true);    
});

$(document).ajaxStop(function(){
    KTApp.unblockPage();
    $('#btn-search-product').prop('disabled', false);
});

$(document).ready(function(){
    $('#date').datepicker({
        format: "dd-mm-yyyy",
        todayHighlight: true,
		clearBtn: true,
		autoclose: true,
		orientation: "bottom auto"
    });

    $.getJSON("transaction/Purchase/get_supplier", (data) => {
        var option = '<option value="" class="kt-font-dark">- PILIH SUPPLIER -</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i) {
                option += '<option value="' + data.response[i].code + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
            });
        } 
        else 
        {
            option = option;
        }
        var value = $('#supplier_code_update').val();
        $('#supplier_code').val(value);
			if (value==null) 
			{				
				$("#supplier_code").html(option).select2();
			}		
			else
			{
                $("#supplier_code").html(option).select2();
                $('#supplier_code').html(option).val(value).select2();                
			}
    });	
    
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
        searching: true,
        responsive: true,
        processing: true,
        language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },        
        paging : false,
        scrollY: '100vh',
        scrollCollapse: true,
        info: false,
        columnDefs: [
            { 
                targets: [0, 1, 6, 8, -1, -2],
                orderable : false
            }
        ],
        order: [[2, 'asc']],
        rowCallback: function(row, data, iDisplayIndex) {
			var info = this.fnPagingInfo();
			var page = info.iPage;
			var length = info.iLength;
			var index = page * length + (iDisplayIndex + 1);
			$('td:eq(0)', row).html(index);
		}
    });

    table.on( 'draw', function () {
        calculate();
    } );

    $('#confirm-button').hide();
    $('#supplier_code').change(function(){
        $('.choose').prop('checked', false);
        calculate();
        $('#confirm-button').hide();
    });

    $('.choose-all').click(function(){
        $('.choose').prop('checked', this.checked);
        if ($('.choose:checked').length > 0)
        {
            $('#confirm-button').show('slow');
        }
        else
        {
            $('#confirm-button').hide('slow');
        }
        calculate();
    });

    $("#datatable").on("click", ".choose", function(){
        if ($('.choose:checked').length > 0)
        {
            $('#confirm-button').show('slow');
        }
        else
        {
            $('#confirm-button').hide('slow');
        }
        calculate();
    });

    $("#datatable").on("click", ".qty", function() {
        $(this).select();
    });

    $("#datatable").on("keyup", ".qty", function() {
        var id = Math.round(new Date().getTime() +(Math.random() * 100));
        $(this).attr('id', id);
        price = $(this).attr('id')+2;
        total = $(this).attr('id')+3;
        $(this).closest('tr').find('.price').attr('id',price);
        $(this).closest('tr').find('.total').attr('id',total);

        var price_value = $('#'+price).val().replace(/\,/g, "");
        var subtotal = Number($('#'+id).val().replace(/\,/g, "")) * Number(price_value);
        $('#'+total).val(format_number(String(subtotal)));
        calculate();
    });    
});

function calculate()
{
    $('#total_product').val($('.choose:checked').length);
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

