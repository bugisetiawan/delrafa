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
    $('#search').on('keyup', function(){        
        $(this).val($(this).val().toUpperCase());
    });

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
        searching: false,
        responsive: true,
        processing: true,
        language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
        serverSide: true,   
        pageLength: 25,
        lengthMenu: [ [25, 50, 75, -1], [25, 50, 75, "All"] ],
        dom: `<'row'<'col-sm-6 text-left'lf><'col-sm-6 text-right'B>>
			  <'row'<'col-sm-12'tr>>
			  <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'p>>`,
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
            "url": "master/Product",
            "type": "POST",
            "data":function(data){
                data.search             = $('#search').val();
                data.department_code    = $('#department_code').val();
                data.subdepartment_code = $('#subdepartment_code').val();
                data.input_order        = $('#input_order').val(); 
                data.product_type       = $('#product_type').val();
                data.ppn                = $('#ppn').val();
                data.status             = $('#status').val();     
                data.warehouse_id       = $('#warehouse_id').val();           
            }
        },
        columns: [				
            {"data": "id", className: 'text-dark text-center', width: '10px', orderable:false},
            {"data": "barcode", className: 'text-dark text-center', width: '100px'},
            {"data": "code", className: 'text-dark text-center', width: '100px'},
            {"data": "name", className: 'text-dark kt-font-bold'},
            {"data": "stock", className: 'text-dark text-right'},
            {"data": "unit", className: 'text-dark text-left', orderable : false},
            {"data": "sellprice", className: 'text-dark text-right'},
            {"data": "ppn", className: 'text-left', width: '10px'}
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
                targets: -1, 
                orderable : false,
                render : function(val){
                    if(val == 1)
                    {
                        return `<span class="text-primary">PPN</span>`;
                    }                    
                    else if(val == 2)
                    {
                        return `<span class="text-success">FINAL</span`;
                    }
                    else
                    {
                        return `<span class="text-danger">NON</span>`;
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

    $('a.toggle-vis').on('click', function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = table.column($(this).attr('data-column'));
 
        // Toggle the visibility
        column.visible(!column.visible());
    });
    
    $('#btn-refresh').click(function(){
		table.ajax.reload();
    });
    
    $("#btn-search").click(function(){
        $('#filter-modal').modal('hide');
        table.ajax.reload();
    }); 
    
    $("#search").keypress(function(e){
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
        table.ajax.reload();
    });

    $("#subdepartment_code").change(function() {
        table.ajax.reload();
    });

    $("#datatable").on('click', '.stock', function() {
        var product_code = $(this).data('id');
        $.ajax({
            url: "master/Product/detail_stock",
            type: "POST",
            dataType: "JSON",
            data: {
                product_code: product_code,
            },
            success: (data) => {                
                $("#stock-modal-body").html(data);
                $('#stock-modal').modal('show');
            },
            error: (err) => {
                console.log(err);
                console.log(err.responseText);
            }
        });        
    });

    $("#datatable").on('click', '.sellprice', function(){
        var product_code = $(this).data('id');
        $.ajax({
            url: "master/Product/detail_sellprice",
            type: "POST",
            dataType: "JSON",
            data: {
                product_code: product_code,
            },
            success: (data) => {                
                $("#sellprice-modal-body").html(data);
                $('#sellprice-modal').modal('show');
            },
            error: (err) => {
                console.log(err);
                console.log(err.responseText);
            }
        });        
    });
});