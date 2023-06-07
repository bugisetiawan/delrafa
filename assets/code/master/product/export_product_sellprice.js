$(document).ready(function() {
    $(document).keypress(function(e){
		var key = e.which;
		if(key == 13)
		{			
			e.preventDefault();
		}
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
        if($(this).val() != "")
        {
            $('#message').hide('slow');                        
        }
        else
        {
            $('#message').show('slow');            
        }  
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

    $('#confirm-button').hide();
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
        serverSide: true,
        paging : false,
        scrollY: '100vh',
        scrollCollapse: true,
        info: false,
        ajax: {
            "url": "master/Product/datatable_export_product_sellprice",
            "type": "POST",
            "data":function(data){
                data.search_product = $('#search_product').val();
                data.department_code = $('#department_code').val();
                data.subdepartment_code = $('#subdepartment_code').val();
            }
        },
        columns: [				
            {"data": "id", className: 'kt-font-dark text-center', width: '10px'},
            {"data": "choose", className: 'kt-font-dark text-center', width: '10px'},
            {"data": "code", className: 'kt-font-dark text-center', width: '100px'},
            {"data": "name_p", className: 'kt-font-dark kt-font-bold'},            
            {"data": "name_d", className: 'kt-font-dark'},
            {"data": "name_sd", className: 'kt-font-dark'},
        ],        
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

    $('#search_product').keypress(function(e){
		var key = e.which;
		if(key == 13)
		{			
			e.preventDefault();
            if($(this).val() != "")
            {
                $('#message').hide('slow');                        
            }
            else
            {
                $('#message').show('slow');            
            }
            table.ajax.reload();
		}
	});

    $('#department_code, #subdepartment_code').change(function(){
        table.ajax.reload();
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
        $('#total_product').val($('.choose:checked').length);
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
        $('#total_product').val($('.choose:checked').length);
    });   
});