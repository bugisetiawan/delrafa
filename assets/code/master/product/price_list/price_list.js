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
        searching: false,
        processing: true,
        language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
        serverSide: true,
        // pageLength: 25,
        // lengthMenu: [ [25, 50, 75, -1], [25, 50, 75, "All"] ],
        paging : false,
        scrollY: '100vh',
        scrollCollapse: true,
        ajax: {
            "url": "master/Product/price_list/", 
            "type": "POST",
            "data":function(data){
                data.search_product     = $('#search_product').val();
                data.department_code    = $('#department_code').val();
                data.subdepartment_code = $('#subdepartment_code').val();                
            }
        },
        columns: [				            
            {"data": "id", className: "text-dark"},
            {"data": "code", className: "text-dark"},
            {"data": "name", className: "text-dark"},
            {"data": "stock", className: "text-dark text-right"},
            {"data": "unit", className: "text-dark text-"},
            {"data": "price_1", className: 'text-dark text-right'},
            {"data": "price_2", className: 'text-dark text-right'},
            {"data": "price_3", className: 'text-dark text-right'},
            {"data": "price_4", className: 'text-dark text-right'},
            {"data": "price_5", className: 'text-dark text-right'},
            {"data": "buyprice", className: 'text-dark text-right'},
            {"data": "hpp", className: 'text-dark text-right'},
        ],
        columnDefs: [
            { 
                targets: '_all',
                orderable: false
            },
            { 
                targets: [3], 
                render : function(val, type, row, meta){                    
                    if(val == null)
                    {
                        return 0;
                    }                    
                    else
                    {
                        return val;
                    }
                }
            },
            { 
                targets: [5, 6, 7, 8, 9], 
                render : function(val, type, row, meta){
                    var percent = (Number(row['buyprice']) > 0) ? (Number(val)-Number(row['buyprice']))/Number(row['buyprice'])*100 : 100;
                    var info_percent =  (percent > 0) ? '<span class="text-success">'+percent.toFixed(2)+'</span>' : '<span class="text-danger">'+percent.toFixed(2)+'</span>';
                    if(val == null)
                    {
                        return 0;
                    }                    
                    else
                    {
                        return $.fn.dataTable.render.number(',', '.', 2).display(val)+' / '+info_percent+'%';
                    }
                }
            },
            { 
                targets: [-1, -2], 
                render : function(val){
                    if(val == null)
                    {
                        return 0;
                    }                    
                    else
                    {
                        return $.fn.dataTable.render.number(',', '.', 2).display(val);
                    }
                }
            }
        ],
        rowCallback: function(row, data, iDisplayIndex) {
            var info = this.fnPagingInfo();
            var page = info.iPage;
            var length = info.iLength;
            var index = page * length + (iDisplayIndex + 1);
            $('td:eq(0)', row).html(index);
        }
    });

    $('#search_product').on('keyup', function() {
        $(this).val($(this).val().toUpperCase());
    });

    $("#search_product").keypress(function(e){
		var key = e.which;
		if(key == 13)
		{
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

    $("#department_code").on('change', function(){
        if($(this).val() != "")
        {
            $('#message').hide('slow');                        
        }
        else
        {
            $('#message').show('slow');            
        }
    });
});

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}