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

$(document).ready(function() {
    $.fn.dataTableExt.oApi.fnPagingInfo = function(oSettings)
    {
        return{
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
        pageLength: 25,
        lengthMenu: [ [25, 50, 75, -1], [25, 50, 75, "All"] ],
        dom: "<'row'<'col-sm-12 col-md-6 text-left'l><'col-sm-12 col-md-6 text-right'f>>" +
			 "<'row'<'col-sm-12'tr>>" +
			 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
		buttons: [
			{
				extend: 'print',
				text: 'Print',				
				exportOptions: {
					columns: ':not(.notexport)'
				}
			},
			{
				extend: 'copyHtml5',
				text: 'Copy',				
				exportOptions: {
					columns: ':not(.notexport)'
				}
			},
			{
				extend: 'excelHtml5',
				text: 'Excel',				
				exportOptions: {
					columns: ':not(.notexport)'
				}
			}			
		],
        ajax: {
            "url": "General/product_sales_history",
            "type": "POST",
            "data":function(data){
                data.department_code    = $('#department_code').val();
                data.subdepartment_code = $('#subdepartment_code').val();
                data.customer_code      = $('#customer_code').val();
            }
        },
        columns: [				
            {"data": "id_p", className: 'text-dark text-center', width: '10px', orderable:false},
            {"data": "barcode_p", className: 'text-dark text-center', width: '100px'},
            {"data": "code_p", className: 'text-dark text-center', width: '100px'},
            {"data": "name_p", className: 'text-dark kt-font-bold'},
            {"data": "qty", className: 'text-dark text-right'},
            {"data": "code_u", className: 'text-dark text-left', orderable : false},
            {"data": "price", className: 'text-dark text-right'},
            {"data": "disc_product", className: 'text-dark text-right'},
            {"data": "total", className: 'text-dark text-right'},
            {"data": "action", className: 'text-dark text-center', width: '100px'}
        ],
        columnDefs: [
            { 
                targets: 1, 
                orderable : false,
                render : function(val){
                    if(val == "")
                    {
                        return `-`;
                    }
                    else
                    {
                        return val;
                    }
                }
            },
            { 
                targets: [-2, -4], 
                render : function(val){
                    if(val == null)
                    {
                        return 0;
                    }                    
                    else
                    {
                        return $.fn.dataTable.render.number(',', '.', 0).display(val);
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

    $("#search").keypress(function(e){
		var key = e.which;
		if(key == 13)
		{			
            table.ajax.reload();			
		}
    });    
    
    $.getJSON("master/department/get_department", (data) => {
        var option = '<option value="" class="text-dark">- SEMUA DEPARTEMEN -</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i, item) {
                option += '<option value="' + data.response[i].code + '" class="text-dark">' + data.response[i].name + '</option>';
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

    $.getJSON("transaction/Sales/get_customer", (data) => {
        var option = '<option value="" class="text-dark">- PILIH PELANGGAN -</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i) {
                option += '<option value="' + data.response[i].code + '" class="text-dark">' +data.response[i].code+ ' | ' +data.response[i].name + ' | ' +data.response[i].address + '</option>';
            });
        } else {
            option = option;
        }        
        $("#customer_code").html(option).select2();
    });
    
    $("#subdepartment_code, #customer_code").change(function() {
        if($('#customer_code').val() == "")
        {
            $('#customer_notify').show('slow');
        }
        else
        {
            $('#customer_notify').hide('slow');
        }
        table.ajax.reload();
    });

    $("#datatable").on('click', '.sellprice_history', function(){
        var product_code = $(this).data('code');        
        $("#sellprice-history-modal-body").html('');
        $.ajax({
            url: "transaction/Sales/get_sellprice_history",
            type: "POST",
            dataType: "JSON",
            data: {
                customer_code : $('#customer_code').val(),
                product_code  : product_code,
            },
            success: (data) => {                
                $("#sellprice-history-modal-body").html(data);
                $('#sellprice-history-modal').modal('show');
            },
            error: (err) => {
                console.log(err);
                console.log(err.responseText);
            }
        });        
    });
});