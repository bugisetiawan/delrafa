$(document).ready(function() {             
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

    var table = $("#datatable_out_stock_product").DataTable({
        searching : false,
        responsive: true,
        processing: true,
        serverSide: true,
        lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
        language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
        ajax: {
            "url": "General/out_stock_product", 
            "type": "POST",
            "data":function(data){
                data.search_product     = $('#search_product').val();
                data.department_code    = $('#department_code').val();
                data.subdepartment_code = $('#subdepartment_code').val();                
            }
        },
        columnDefs: [
            { 
                targets: [0],
                orderable : false
            },  
        ],
        columns: [				
            {"data": "id_p", className: 'text-dark text-center', width: '10px'},
            {"data": "code_p", className: 'text-dark text-center', width: '100px'},
            {"data": "name_p", className: 'text-dark text-left'},
            {"data": "total_stock", className: 'text-danger text-right', width: '80px'},
            {"data": "name_u", className: 'text-dark text-left', width: '80px'},
            {"data": "name_d", className: 'text-dark text-left', width: '100px'},
            {"data": "name_sd", className: 'text-dark text-left', width: '100px'}
        ],
        order: [[1, 'asc']],
        rowCallback: function(row, data, iDisplayIndex) {
            var info = this.fnPagingInfo();
            var page = info.iPage;
            var length = info.iLength;
            var index = page * length + (iDisplayIndex + 1);
            $('td:eq(0)', row).html(index);
        }
    });

    $.getJSON("master/department/get_department", (data) => {
        var option = '<option value="" class="kt-font-dark">- SEMUA DEPARTEMEN -</option>';
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
        table.ajax.reload();
    });

    $("#subdepartment_code").change(function() {
        table.ajax.reload();
    });

    $("#search_product").keypress(function(e){
		var key = e.which;
		if(key == 13)
		{			
            table.ajax.reload();			
		}
    });
});

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}