$(document).ready(function() {    
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
          
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

    $("#refresh").click(function(){
        location.reload();
    });        
    
    $("#datatable_multi_price").DataTable({
        responsive: true,        
        lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ]
    });

    $("#datatable_multi_unit").DataTable({
        responsive: true,        
        lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ]        
    });

    $("#datatable_product_location").DataTable({
        responsive: true,        
        lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ]
    });    
    
    $("#check_stock_card_movement_product_btn").on('click', function() {
		var id= $(this).data('id');
		window.open("master/Product/check_unbalance_stock_card_movement_product/"+id, "Cek Kartu & Pergerakan Stok Produk", "left=300, top=100, width=1080, height=500");
    });

    $('#validate_stock_card_movement_product_btn').on('click', function(){
        let id = $(this).data('id');
        $.ajax({
            url: 'master/Product/validate_stock_card_movement_product',
            type: 'POST',
            dataType: 'JSON',
            data: {product_id:id},
            beforeSend: function(){
                KTApp.blockPage({
                    overlayColor: '#000000',
                    type: 'v2',
                    state: 'primary',
                    message: '<span class="text-dark font-weight-bold">Mohon menunggu, sedang dalam proses...</span>'
                });
            },
            success: (data) => {
                if(data.status.code == 200)
                {
                    location.reload();
                } 
                else
                {
                    toastr.error(`${data.status.message}`);
                }
            },
            error: (err) => {
                toastr.error(`${err.responseText}`);
            }
        });        
    });

    $('#sort_inventory_product_btn').on('click', function(){
        let id = $(this).data('id');
        $.ajax({
            url: 'master/Product/sort_inventory_product',
            type: 'POST',
            dataType: 'JSON',
            data: {product_id:id},
            success: (data) => {
                if(data.status.code == 200){
                    location.reload();
                } 
                else{
                    toastr.error(`${data.status.message}`);
                }
            },
            error: (err) => {
                toastr.error(`${err.responseText}`);
            }
        });        
    }); 

    $('#recalculate_inventory_product_btn').on('click', function(){
        let id = $(this).data('id');
        $.ajax({
            url: 'master/Product/recalculate_inventory_product',
            type: 'POST',
            dataType: 'JSON',
            data: {product_id:id},
            beforeSend: function(){
                KTApp.blockPage({
                    overlayColor: '#000000',
                    type: 'v2',
                    state: 'primary',
                    message: '<span class="text-dark font-weight-bold">Mohon menunggu, sedang dalam proses...</span>'
                });
            },
            success: (data) => {
                if(data.status.code == 200){
                    location.reload();
                } 
                else{
                    toastr.error(`${data.status.message}`);
                }
            },
            error: (err) => {
                toastr.error(`${err.responseText}`);
            }
        });        
    });          

    $('#recalculate_hpp_product_btn').on('click', function(){
        let id = $(this).data('id');
        $.ajax({
            url: 'master/Product/recalculate_hpp_product',
            type: 'POST',
            dataType: 'JSON',
            data: {product_id:id},
            beforeSend: function(){
                KTApp.blockPage({
                    overlayColor: '#000000',
                    type: 'v2',
                    state: 'primary',
                    message: '<span class="text-dark font-weight-bold">Mohon menunggu, sedang dalam proses...</span>'
                });
            },
            success: (data) => {
                if(data.status.code == 200)
                {
                    location.reload();
                } 
                else
                {
                    toastr.error(`${data.status.message}`);
                }
            },
            error: (err) => {
                toastr.error(`${err.responseText}`);
            }
        });        
    });

    var product_id = $('#product_id').val();
    var datatable_stock_card = $("#datatable_stock_card").DataTable({
		searching: false,		
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
			"url"		: "master/Product/datatable_stock_card/"+product_id,
            "type"		: "POST",
            "data"      :function(data){
                data.warehouse_id_sc  = $('#warehouse_id_sc').val();
                data.transaction_type = $('#transaction_type').val();
            }
		},
		columns: [				
            {"data": "id_sc", className: "text-dark text-center"},
            {"data": "date", className: "text-dark text-center"},
            {"data": "invoice", className: "text-primary text-left"},
            {"data": "information", className: "text-dark text-left"},
            {"data": "qty", className: 'text-right'},
            {"data": "stock", className: 'text-right'},
            {"data": "code_w", className: 'text-dark text-left'}
        ],
        columnDefs:[
            {
                "targets": [0, 1, 2, 3, 4, 5, 6],
                "orderable": false
            },
            { 
				targets: 2,
                render : function(data, type, row, meta){
                    if(row['type'] == 1)
                    {
                        return `<a href="purchase/invoice/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 2)
                    {
                        return `<a href="purchase/return/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 3)
                    {
                        return `<a href="pos/transaction/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 4)
                    {
                        return `<a href="sales/invoice/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 5)
                    {
                        return `<a href="sales/return/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 6)
                    {
                        return `<a href="production/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 7)
                    {
                        return `<a href="repacking/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 8)
                    {
                        return `<a href="opname/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 9)
                    {
                        return `<a href="mutation/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else
                    {
                        return `-`;
                    }                 
                }
            },
            { 
				targets: 3,
                render : function(data, type, row, meta){
                    return row['information']+` | `+row['note'];
                }
			},
            { 
				targets: -3,
                render : function(data, type, row, meta){
                    if(row['method'] == 1)
                    {
                        return `<span class="text-success">`+data+`</span>`;
                    }   
                    else
                    {
                        return `<span class="text-danger">`+data+`</span>`;
                    }                 
                }
			},        
            { 
				targets: -2,
                render : function(data, type, row, meta){
                    if(Number(data) > 0)
                    {
                        return `<span class="text-primary">`+data+`</span>`;
                    }   
                    else
                    {
                        return `<span class="text-danger">`+data+`</span>`;
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
		},
    });
    
    $('#warehouse_id_sc, #transaction_type').change(function(){
		datatable_stock_card.ajax.reload();
    });
    
    // STOCK MOVEMENT
    var datatable_stock_movement = $("#datatable_stock_movement").DataTable({
		searching: false,		
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
			"url"		: "master/Product/datatable_stock_movement/"+product_id,
            "type"		: "POST",
            "data"      :function(data){
                data.transaction_type = $('#transaction_movement_type').val();
            }
		},
		columns: [				
            {"data": "id_sc", className: "text-dark text-center"},
            {"data": "date", className: "text-dark text-center"},
            {"data": "invoice", className: "text-primary text-left"},
            {"data": "information", className: "text-dark text-left"},
            {"data": "qty", className: 'text-right'},
            {"data": "stock", className: 'text-right'},
            {"data": "price", className: 'text-dark text-right', render: $.fn.dataTable.render.number(',', '.', 2)},
            {"data": "hpp", className: 'text-dark text-right', render: $.fn.dataTable.render.number(',', '.', 2)},
        ],
        columnDefs:[
            {
                "targets": [0, 1, 2, 3, 4, 5],
                "orderable": false
            },
            { 
				targets: 2,
                render : function(data, type, row, meta){
                    if(row['type'] == 1)
                    {
                        return `<a href="purchase/invoice/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 2)
                    {
                        return `<a href="purchase/return/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 3)
                    {
                        return `<a href="pos/transaction/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 4)
                    {
                        return `<a href="sales/invoice/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 5)
                    {
                        return `<a href="sales/return/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 6)
                    {
                        return `<a href="production/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 7)
                    {
                        return `<a href="repacking/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 8)
                    {
                        return `<a href="opname/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else if(row['type'] == 9)
                    {
                        return `<a href="mutation/detail/`+row['transaction_id']+`" class="kt-link kt-font-bold">`+data+`</a>`;
                    }
                    else
                    {
                        return `-`;
                    }                 
                }
            },
            { 
				targets: 3,
                render : function(data, type, row, meta){
                    return row['information']+` | `+row['note'];
                }
			},
            { 
				targets: -4,
                render : function(data, type, row, meta){
                    if(row['method'] == 1)
                    {
                        return `<span class="text-success">`+data+`</span>`;
                    }   
                    else
                    {
                        return `<span class="text-danger">`+data+`</span>`;
                    }                 
                }
			},        
            { 
				targets: -3,
                render : function(data, type, row, meta){
                    if(Number(data) > 0)
                    {
                        return `<span class="text-primary">`+data+`</span>`;
                    }   
                    else
                    {
                        return `<span class="text-danger">`+data+`</span>`;
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
		},
    });

    $('#transaction_movement_type').change(function(){
		datatable_stock_movement.ajax.reload();
    });

    $("#datatable_list_of_purchase_invoice").DataTable({        
		responsive: true,
		processing: true,
		language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
		serverSide: true,
		lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
		ajax: {
			"url": "master/Product/datatable_list_of_purchase_invoice/"+product_id, 
			"type": "POST"
		},
		columns: [				
			{"data": "id", className: "text-dark text-center", width: "10px"},
            {"data": "date", className: "text-dark text-center", width: "100px"},
            {"data": "code_pi", className: "text-dark", width: "100px"},
            {"data": "qty", className: "text-dark text-right", render: $.fn.dataTable.render.number(',', '.', 2)},
            {"data": "name_u", className: "text-dark", width: "100px"},
            {"data": "price", className: "text-dark text-right", render: $.fn.dataTable.render.number(',', '.', 2)},
            {"data": "disc_product", className: "text-dark text-right", width: "100px"},
            {"data": "total", className: "text-dark text-right", render: $.fn.dataTable.render.number(',', '.', 2)},
            {"data": "name_s", className: "text-dark"}
        ],      
        columnDefs: [			
            { 
                targets: 1, 
                render : function(val){
                    return format_date(val);
                }
			}
        ],
		order: [[1, 'desc']],
		rowCallback: function(row, data, iDisplayIndex) {
			var info = this.fnPagingInfo();
			var page = info.iPage;
			var length = info.iLength;
			var index = page * length + (iDisplayIndex + 1);
			$('td:eq(0)', row).html(index);
		}
	});

    $("#datatable_list_of_sales_invoice").DataTable({        
		responsive: true,
		processing: true,
		language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
		serverSide: true,
		lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
		ajax: {
			"url": "master/Product/datatable_list_of_sales_invoice/"+product_id, 
			"type": "POST"
		},
		columns: [				
			{"data": "id", className: "text-dark text-center", width: "10px"},
            {"data": "date", className: "text-dark text-center", width: "100px"},
            {"data": "code_si", className: "text-dark", width: "100px"},
            {"data": "qty", className: "text-dark text-right", render: $.fn.dataTable.render.number(',', '.', 2)},
            {"data": "name_u", className: "text-dark", width: "100px"},
            {"data": "price", className: "text-dark text-right", render: $.fn.dataTable.render.number(',', '.', 2)},
            {"data": "disc_product", className: "text-dark text-right", width: "100px"},
            {"data": "total", className: "text-dark text-right", render: $.fn.dataTable.render.number(',', '.', 2)},
            {"data": "name_c", className: "text-dark"}
        ],      
        columnDefs: [			
            { 
                targets: 1, 
                render : function(val){
                    return format_date(val);
                }
			}
        ],
		order: [[1, 'desc']],
		rowCallback: function(row, data, iDisplayIndex) {
			var info = this.fnPagingInfo();
			var page = info.iPage;
			var length = info.iLength;
			var index = page * length + (iDisplayIndex + 1);
			$('td:eq(0)', row).html(index);
		}
	});

    // Delete Product
    $('#delete').on('click', function(){
        let id = $(this).data('id');
        $.ajax({
            url: 'master/Product/verify_delete_product',
            type: 'POST',
            dataType: 'JSON',
            data: {product_code:id},
            success: (data) => {
                if(data.status.code == 200){
                    swal.fire({
                        title: "Hapus Data?",
                        text: "Data yang dihapus sudah tidak dapat dikembalikan lagi",
                        type: "warning",
                        showCancelButton: !0,
                        confirmButtonText: "Ya",
                        cancelButtonText: "Tidak",
                        reverseButtons: !0
                    }).then(function(e) {
                        if(e.value) {
                            $.ajax({
                                url: 'product/delete',
                                type: 'GET',
                                dataType: 'JSON',
                                data: {code:id},
                                success: (data) => {
                                    if(data.status.code == 200) {
                                        toastr.success(`${data.status.message}`);
                                        location.replace('product');                            
                                    } else {
                                        toastr.error(`${data.status.message}`);
                                    }
                                },
                                error: (err) => {
                                    toastr.error(`${err.responseText}`);
                                }
                            });
                        } else {
                            swal.fire("Cancelled", "Data Tidak Dihapus", "error");
                        }            
                    })
                } else {
                    toastr.error(`${data.status.message}`);
                }
            },
            error: (err) => {
                toastr.error(`${err.responseText}`);
            }
        });        
    });
    
    $('.price').keyup(function(){
        $(this).val(format_amount($(this).val()));        
    }); 
    
    $('.price').click(function() {
        $(this).select();
    });

    $("#update_buyprice_btn").on('click', function() {
		var product_code = $(this).data('id');
		$.ajax({
            url: "master/Product/get_detail_buyprice_hpp",
            type: "POST",
            dataType: "JSON",
            data: {
                product_code : product_code
            },
            success: (data) => {
				$.each(data, function() {
                    $('#update_buyprice_form').modal('show');
                    $('#buyprice_product').val(format_amount(data.buyprice));
                });                 
            },
            error: (err) => {
                alert(err);
            }
		});		
    });

    $("#update_hpp_btn").on('click', function() {
		var product_code = $(this).data('id');
		$.ajax({
            url: "master/Product/get_detail_buyprice_hpp",
            type: "POST",
            dataType: "JSON",
            data: {
                product_code : product_code
            },
            success: (data) => {
				$.each(data, function() {
                    $('#update_hpp_form').modal('show');
                    $('#hpp_product').val(format_amount(data.hpp));
                });                 
            },
            error: (err) => {
                alert(err);
            }
		});		
    });

    $('#price_alert, #e_price_alert').hide();

    $("#add_multi_price_data").on("keyup", '.price', function(){
        var id      = Number($(this).attr('id').substring(6));
		var prev_id = id-1;
        var prev    = "price_"+prev_id;
        var price   = Number($(this).val().replace(/\,/g, ""));
        var buyprice;
        if( price == "")
        {
            $('#price_alert').hide();
            $('#save-multiprice').prop( "disabled", false);
        }
        else
        {
            $.ajax({
                type: "POST", 
                url: 'master/Product/get_product_unit_value',
                data: {
                    product_code : $('#product_code').val(),
                    unit_id      : $('#unit_id').val()
                }, 
                dataType: "json",
                beforeSend: function(e) {
                    if(e && e.overrideMimeType) {
                        e.overrideMimeType("application/json;charset=UTF-8");
                    }
                },
                success: function(response){
                    buyprice = Number($('#buyprice').val())*Number(response.value);
                    if(Number(price) <= Number(buyprice) )
                    {            
                        $("#price_message").text("Mohon Maaf, Harga Jual harus lebih tinggi daripada Harga Beli, terima kasih");
                        $('#price_alert').show();
                        $('#save-multiprice').prop( "disabled", true);
                    }
                    else
                    {
                        if(prev_id != 0)
                        {
                            if(Number(price) > Number($('#'+prev).val().replace(/\,/g, "")))
                            {
                                $("#price_message").text("Mohon Maaf, Harga Jual "+id+" harus lebih rendah daripada Harga Jual "+ prev_id+ ". Terima Kasih");
                                $('#price_alert').show();
                                $(this).val("");
                            }
                            else
                            {
                                $('#price_alert').hide();
                                $('#save-multiprice').prop( "disabled", false);
                            }
                        } 
                        else
                        {
                            $('#price_alert').hide();
                            $('#save-multiprice').prop( "disabled", false);
                        }               
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
        }        
    });

    $("#update_multi_price_data").on("keyup", '.price', function(){
		var id      = Number($(this).attr('id').substring(8));
		var prev_id = id-1;
        var prev    = "e_price_"+prev_id;
        var buyprice = Number($('#buyprice').val())*Number($('#e_unit_value').val());
        if($(this).val() == "")
        {
            $('#e_price_alert').hide();
            $('#save-update-multiprice').prop( "disabled", false);
        }
        else
        {
            if(Number($(this).val().replace(/\,/g, "")) <= Number(buyprice) )
            {            
                $("#e_price_message").text("Mohon Maaf, Harga Jual harus lebih tinggi daripada Harga Beli, terima kasih");
                $('#e_price_alert').show();
                $('#save-update-multiprice').prop( "disabled", true);
            }
            else
            {
                if(prev_id != 0)
                {
                    if(Number($(this).val().replace(/\,/g, "")) > Number($('#'+prev).val().replace(/\,/g, "")))
                    {				
                        $("#e_price_message").text("Mohon Maaf, Harga Jual "+id+" harus lebih rendah daripada Harga Jual "+ prev_id+ ". Terima Kasih");
                        $('#e_price_alert').show();
                        $(this).val("");
                    }
                    else
                    {
                        $('#e_price_alert').hide();
                        $('#save-update-multiprice').prop( "disabled", false);
                    }
                } 
                else
                {
                    $('#e_price_alert').hide();
                    $('#save-update-multiprice').prop( "disabled", false);                    
                }               
            }
        }		
        var i;
		for (i = $(this).attr('id').substring(8); i <= 5; i++) 
		{						
			if(i != 5)
			{
				var next_id   = Number(i)+1;
				var next 	  = "e_price_"+next_id;				
                $('#'+next).val("");				
			}			
		}	
    });

    $('#update_hpp_data, #add_multi_price_data, #update_multi_price_data, #add_multi_unit_data, #update_multi_unit_data, #add_product_location_data, #update_product_location_data').on('submit', function(e) {
		let t = $(this);
		e.preventDefault();
		$.ajax({
			url: t.attr('action'),
			method: t.attr('method'),
			dataType	: "JSON",
			data: t.serialize(),
			success		: (data) => {				
				if(data.status.code	== 200) 
				{					
					location.reload();					
                } 
                else 
				{
					toastr.error(data.status.message);
				}
			},
			error		: (err)	=> {
				toastr.error(err.responseText);            
			}
		})
    });
    
    $(".update_price").on('click', function() {
		var myId = $(this).data('id');
		$.ajax({
            url: "master/Product/get_detail_sellprice",
            type: "GET",
            dataType: "JSON",
            data: {
                id: myId
            },
            success: (data) => {
				$.each(data, function() {
                    $('#update_price_form').modal('show');
                    $('#e_unit_id').val(data.id_u);
                    $('#unit_name').val(data.name_u);
                    $('#e_unit_value').val(data.value);
                    $('#e_price_1').val(format_amount(data.price_1));
                    $('#e_price_2').val(format_amount(data.price_2));
                    $('#e_price_3').val(format_amount(data.price_3));
                    $('#e_price_4').val(format_amount(data.price_4));
                    $('#e_price_5').val(format_amount(data.price_5));
                });                 
            },
            error: (err) => {
                alert(err);
            }
		});		
    });

    $('.delete_multi_price').on('click', function() {
        let id = $(this).data('id');        
        swal.fire({
            title: "Hapus Data?",
            text: "Data yang dihapus sudah tidak dapat dikembalikan lagi",
            type: "warning",
            showCancelButton: !0,
            confirmButtonText: "Ya",
            cancelButtonText: "Tidak",
            reverseButtons: !0
        }).then(function(e) {
            if(e.value) {
                $.ajax({
                    url: 'master/Product/delete_multi_price',
                    type: 'GET',
                    dataType: 'JSON',
                    data: { id : id },
                    success: (data) => {
                        if(data.status.code == 200) {                            
                            location.reload();
                        } 
                        else 
                        {                            
                            toastr.error(data.status.message);
                        }
                    },
                    error: (err) => {
                        toastr.error(data.status.message);
                    }
                });
            } else {
                swal.fire("Cancelled", "Data Tidak Dihapus", "error");
            }            
        })
    });

    $(".update_multi_unit").on('click', function() {
		var myId = $(this).data('id');
		$.ajax({
            url: "master/Product/get_detail_multi_unit",
            type: "GET",
            dataType: "JSON",
            data: {
                id: myId
            },
            success: (data) => {
                console.log(data);
				$.each(data, function() {
                    $('#e_id_mu').val(data.id_mu);
                    $('#name_mu').val(data.name_u);
                    $('#e_value').val(data.value);
                    $('#update_unit_form').modal('show');
                });                 
            },
            error: (err) => {
                console.log(err);
            }
		});		
    });

    $('.delete_multi_unit').on('click', function() {
        let id = $(this).data('id');        
        swal.fire({
            title: "Hapus Data?",
            text: "Data yang dihapus sudah tidak dapat dikembalikan lagi",
            type: "warning",
            showCancelButton: !0,
            confirmButtonText: "Ya",
            cancelButtonText: "Tidak",
            reverseButtons: !0
        }).then(function(e) {
            if(e.value) {
                $.ajax({
                    url: 'master/Product/delete_multi_unit',
                    type: 'GET',
                    dataType: 'JSON',
                    data: {id : id},
                    success: (data) => {
                        if(data.status.code == 200) {                            
                            location.reload();
                        } 
                        else 
                        {
                            console.log(`${data.status.message}`);                            
                        }
                    },
                    error: (err) => {
                        console.log(`${err.responseText}`);                        
                    }
                });
            } else {
                swal.fire("Cancelled", "Data Tidak Dihapus", "error");
            }            
        })
    });

    $(".update_product_location").on('click', function() {
		var myId = $(this).data('id');
		$.ajax({
            url: "master/Product/get_detail_product_location",
            type: "GET",
            dataType: "JSON",
            data: {
                id: myId
            },
            success: (data) => {
                console.log(data);
				$.each(data, function() {
                    $('#update_product_location_form').modal('show');                    
                    $('#e_id_pl').val(data.id_pl);
                    $('#name_w').val(data.name_w);
                    $('#e_location').val(data.location);                    
                });                 
            },
            error: (err) => {
                console.log(err);
            }
		});		
    });

    $('.delete_product_location').on('click', function() {
        let id = $(this).data('id');        
        swal.fire({
            title: "Hapus Data?",
            text: "Data yang dihapus sudah tidak dapat dikembalikan lagi",
            type: "warning",
            showCancelButton: !0,
            confirmButtonText: "Ya",
            cancelButtonText: "Tidak",
            reverseButtons: !0
        }).then(function(e) {
            if(e.value) {
                $.ajax({
                    url: 'master/Product/delete_product_location',
                    type: 'GET',
                    dataType: 'JSON',
                    data: {id : id},
                    success: (data) => {
                        if(data.status.code == 200) {                            
                            location.reload();
                        } 
                        else 
                        {
                            console.log(`${data.status.message}`);                            
                        }
                    },
                    error: (err) => {
                        console.log(`${err.responseText}`);                        
                    }
                });
            } else {
                swal.fire("Cancelled", "Data Tidak Dihapus", "error");
            }            
        })
    });        
});

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}

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