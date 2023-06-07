$(document).ajaxStart(function(){
    $('#btn-search-product').prop('disabled', true);
    $('#loading').show();    
});
$(document).ajaxStop(function(){
    $('#btn-search-product').prop('disabled', false);
    $('#loading').hide();
});

$(document).ready(function() {       
    $('#date').datepicker({
        format: "dd-mm-yyyy",
        todayHighlight: true,
		clearBtn: true,
		autoclose: true,
		orientation: "bottom auto"
    });

    $.getJSON("master/Warehouse/get_warehouse", (data) => {
        var option = '<option value="" class="kt-font-dark">-- PILIH GUDANG --</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i) {
                option += '<option value="' + data.response[i].id + '" class="kt-font-dark">' + data.response[i].name + '</option>';
            });
        } else {
            option = option;
        }
        $("#warehouse_id").html(option).select2();
    });

    $.getJSON("master/Employee/get_employee", (data) => {
        var option = '<option value="" class="kt-font-dark">-- PILIH PETUGAS --</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i) {
                option += '<option value="' + data.response[i].code + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
            });
        } else {
            option = option;
        }
        $("#employee_code").html(option).select2();
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

    table.on( 'draw', function () {
        calculate();
    } );

    $('#confirm-button').hide();
    $('.choose-all').click(function(){
        $('.choose').prop('checked', this.checked);
        calculate();
    });

    $("#datatable").on("click", ".choose", function(){        
        calculate();
    });    
});

function calculate() 
{
    var total_product = $('.choose:checked').length;
    if (total_product > 0)
    {
        $('#btn-search-product').prop('disabled', true);
        $('#confirm-button').show('slow');        
    }
    else
    {
        $('#btn-search-product').prop('disabled', false);
        $('#confirm-button').hide('slow');
    } 
    $('#total_product').val(total_product);
}