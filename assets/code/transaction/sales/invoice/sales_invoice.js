$(document).ready(function(){
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
    
    $("#datatable").DataTable({
		searching: true,
		processing: true,
		language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
		scrollX: true,
		serverSide: true,
		pageLength: 25,
        lengthMenu: [ [25, 50, 75, -1], [25, 50, 75, "All"] ],
		ajax: {
			"url": "transaction/Sales/sales_invoice/", 
			"type": "POST",
			"data":function(data){
                data.do_status 		= $('#do_status').val();
            }
		},
		columns: [				
			{"data": "id", className: "text-dark"},
            {"data": "date", className: "text-dark text-center"},
            {"data": "invoice", className: "text-dark text-center"},
			{"data": "payment", className: "text-dark text-center"},
			{"data": "due_date", className: "text-dark text-center"},            
			{"data": "grandtotal", className: 'text-primary text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
			{"data": "account_payable", className: 'text-danger text-right', render: $.fn.dataTable.render.number(',', '.', 0)},
            {"data": "name_c", className: "text-dark"},
			{"data": "name_s", className: "text-dark"},
			{"data": "information", className: "text-dark"},
			{"data": "ppn", className: "text-dark text-center"},
			{"data": "payment_status", className: "text-dark text-center"},
			{"data": "do_status", className: "text-dark text-center"},
			{"data": "search_invoice", className: "kt-hidden"}
        ],
        columnDefs: [
            { 
                targets: 1, 
                render : function(data){
                    return format_date(data);
                }
			},
			{ 
                targets: 3, 
                render : function(data){
					if(data == 1)
					{
						return "TUNAI";
					}
					else if(data == 2)
					{
						return "KREDIT";
					}
                }
            },
			{ 
                targets: 4, 
                render : function(data, type, row, meta){
					if(row["payment_status"] == "1")
					{
						return "-";
					}
					else
					{						
						return format_date(data);
					}                    
                }
			},
			{
				targets: -4, 
                orderable : false,
                render : function(data){
                    if(data == 0)
                    {
                        return `<span class="text-danger">NON</span>`;
					}
					else if(data == 1)
                    {
                        return `<span class="text-primary">PPN</span>`;
                    }
                    else
                    {
                        return `<span class="text-success">FINAL</span>`;
                    }
                }
			},
			{ 
                targets: -3, 
                render : function(data){
					if(data == 1)
					{
						return "<div class='kt-font-bold text-success'>LUNAS</div>";
					}
					else if(data == 2)
					{
						return "<div class='kt-font-bold kt-font-warning'>BELUM LUNAS</div>";
					}
					else
					{
						return "<div class='kt-font-bold text-danger'>JATUH TEMPO</div>";
					}
                }
            },
			{ 
                targets: -2, 
                render : function(data){
					if(data == 1)
					{
						return "<div class='kt-font-bold text-success'><i class='fa fa-check'></i></div>";
					}
					else
					{
						return "<div class='kt-font-bold text-danger'><i class='fa fa-times'></i></div>";
					}
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

	$('a.toggle-vis').on('click', function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = table.column($(this).attr('data-column'));
 
        // Toggle the visibility
        column.visible(!column.visible());
    });
});

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}