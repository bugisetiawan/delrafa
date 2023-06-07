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
        serverSide: true,
        lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
        ajax: {
            "url": "pos/Transaction/datatable", 
            "type": "POST"
        },
        columns: [				
            {"data": "id", className: "text-dark text-center", width: "10px"},
            {"data": "date", className: "text-dark text-center", width: "120px"},
            {"data": "time", className: "text-dark text-center", width: "100px"},
            {"data": "invoice", className: "text-dark"},
            {"data": "total_product", className: "text-dark text-center", width: "100px"},
            {"data": "grandtotal", className: "text-dark text-right", width: "100px", render: $.fn.dataTable.render.number(',', '.', 0)},
            {"data": "name_e", className: "text-dark text-center", width: "200px"}
        ],       
        columnDefs: [
            { 
                targets: 1, 
                render : function(val){
                    let current_datetime = new Date(val);
                    let formatted_date = current_datetime.getDate() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getFullYear();
                    return formatted_date;
                }
            }            
        ], 
        order: [[0, 'asc']],
        rowCallback: function(row, data, iDisplayIndex) {
            var info = this.fnPagingInfo();
            var page = info.iPage;
            var length = info.iLength;
            var index = page * length + (iDisplayIndex + 1);
            $('td:eq(0)', row).html(index);
        }
    });
});