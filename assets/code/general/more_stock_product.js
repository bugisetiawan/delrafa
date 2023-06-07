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

    $("#datatable_more_stock_product").DataTable({
        searching : false,
        responsive: true,
        processing: true,
        serverSide: true,
        lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
        language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
        ajax: {
            "url": "General/more_stock_product", 
            "type": "POST"
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
            {"data": "total_stock", className: 'text-primary text-right', width: '80px'},
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
});

function format_date(date){	 
    day = date.split("-")[2];
    month = date.split("-")[1];
    year = date.split("-")[0]; 
    return day+"-"+month+"-"+ year;
}