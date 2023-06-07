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
    
    $("#datatable").DataTable({
        responsive: true,
        processing: true,
        language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
        serverSide: true,
        pageLength: 25,
        lengthMenu: [ [25, 50, 75, -1], [25, 50, 75, "All"] ],                
        ajax: {
            "url": "inventory/Mutation",
            "type": "POST",
            "data":function(data){
                data.do_status 		= $('#do_status').val();
            }
        },
        columns: [				
            {"data": "id", className: 'text-dark text-center', width: '10px'},
            {"data": "date", className: 'text-dark text-center', width: '100px'},
            {"data": "code", className: 'text-center', width: '100px'},
            {"data": "total_product", className: 'text-dark text-center', width: '100px'},
            {"data": "checker", className: 'text-dark text-center'},
            {"data": "operator", className: 'text-dark text-center'},
            {"data": "do_status", className: 'text-dark text-center'},
            {"data": "search_code", className: 'kt-hidden'}
        ],
        columnDefs: [
            { 
                targets: 1, 
                render : function(val){
                    return format_date(val);
                }
            },
            { 
                targets: -2, 
                render : function(val){
					if(val == 1)
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
        order: [[1, 'desc'], [2, 'desc']],
        rowCallback: function(row, data, iDisplayIndex) {
            var info = this.fnPagingInfo();
            var page = info.iPage;
            var length = info.iLength;
            var index = page * length + (iDisplayIndex + 1);
            $('td:eq(0)', row).html(index);
        }
    });
});

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}