$(document).ajaxStart(function(){
    $('#btn-search-product').prop('disabled', true);
    $('#loading').show();    
});
$(document).ajaxStop(function(){
    $('#btn-search-product').prop('disabled', false);
    $('#loading').hide();
});

$(document).ready(function(){
    $('#name').keyup(function() {
        $(this).val($(this).val().toUpperCase());
    });

    $('.date').datepicker({
        format: "dd-mm-yyyy",
        todayHighlight: true,
		clearBtn: true,
		autoclose: true,
		orientation: "bottom auto"
    });

    $('.time').timepicker({
        minuteStep: 1,
        defaultTime: '',
        showSeconds: true,
        showMeridian: false,
        snapToStep: true
    });

    $.getJSON("master/department/get_department", (data) => {
        var option = '<option value="" class="kt-font-dark">-- SEMUA DEPARTEMEN --</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i, item) {
                option += '<option value="' + data.response[i].code + '" class="kt-font-dark">' + data.response[i].name + '</option>';
            });
        } else {
            option = option;
        }        
        $("#department_code").html(option).select2();
    });	    

    var code_depart = '';
    $("#department_code").change(function() {
        code_depart = $(this).val();        
        $.ajax({
            url: "master/department/get_sub",
            type: "GET",
            dataType: 'JSON',
            data: {
                code_depart: code_depart
            },
            success: (data) => {
                var option = '<option value="">-- SEMUA SUB DEPARTEMEN --</option>';
                if (data.status.code == 200) {
                    $.each(data.response, function(i, item) {
                        option += '<option value="' + data.response[i].code + '">' + data.response[i].name + '</option>';
                    });
                } else {
                    option = option;
                }
                $("#subdepartment_code").html(option).select2();
            },
            error: (err) => {
                console.log(err);
                console.log(err.responseText);
            }
        });
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
                targets: 1,
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

    $('#confirm-button').hide();
    $('#department_code, #subdepartment_code').change(function(){
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