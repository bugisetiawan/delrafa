jQuery(document).ready(function() {    	  
    $('#date').datepicker({
        format: "dd-mm-yyyy",
        todayHighlight: true,
		clearBtn: true,
		autoclose: true,
		orientation: "bottom auto"     
    });

    $.getJSON("transaction/Stock/get_employee", (data) => {
        var option = '<option value="" class="kt-font-dark">-- PILIH KARYAWAN --</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i) {
                option += '<option value="' + data.response[i].code + '" class="kt-font-dark">' +data.response[i].code+ ' | ' +data.response[i].name + '</option>';
            });
        } else {
            option = option;
        }
        $("#employee_code").html(option).select2();
	});
      
    $.getJSON("master/Warehouse/get_warehouse", (data) => {
        var option = '<option value="" class="kt-font-dark">-- PILIH GUDANG --</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i) {
                option += '<option value="' + data.response[i].id + '" class="kt-font-dark">' + data.response[i].code+ ' | '+ data.response[i].name + '</option>';
            });
        } else {
            option = option;
        }
        $("#warehouse_id").html(option).select2();
    });

    $('.repeater').repeater({
        initEmpty: false,  
        isFirstItemUndeletable: true,
        hide: function () {
            $(this).remove();
            calculate();
        }
    });
});