jQuery(document).ready(function() {
    $('#datatable').dataTable({
        searching : true,
        pageLength: 25,
        lengthMenu: [ [25, 50, 75, -1], [25, 50, 75, "All"] ],
        order: [[2, 'desc']],
        columnDefs: [ {
            "searchable": false,
            "orderable": false,
            "targets": [0]
        } ]
    });

    $('#btn_save').hide();
    $('.taken-all').click(function(){
        $('.taken').prop('checked', this.checked);
        if ($('.taken:checked').length > 0)
        {
            $('#btn_save').show('slow');
        }
        else
        {
            $('#btn_save').hide('slow');
        }
    });

    $('.taken').click(function(){
        if ($('.taken:checked').length > 0)
        {
            $('#btn_save').show('slow');
        }
        else
        {
            $('#btn_save').hide('slow');
        }
    });
});