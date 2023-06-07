jQuery(document).ready(function() {
    $('.repeater').repeater({
        initEmpty: false,  
        isFirstItemUndeletable: false,
        show: function () {
            $(this).slideDown();
		}
    });

    $('.adjust').click(function(){
        $(this).select();
    });
    $("#product_table").on("keyup", ".adjust", function() {
        var id = Math.round(new Date().getTime() +(Math.random() * 100));
        $(this).attr('id', id);
        stock = $(this).attr('id')+2;
        end_stock = $(this).attr('id')+3;
        $(this).closest('tr').find('.stock').attr('id',stock);
        $(this).closest('tr').find('.end_stock').attr('id',end_stock);

        var total = Number($('#'+stock).val()) + Number($('#'+id).val());
        $('#'+end_stock).val(total);
    });
});