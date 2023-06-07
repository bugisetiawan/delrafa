jQuery(document).ready(function() {                
    $('#status').change(function(){
        if($(this).val() == 0)
        {
            $.ajax({
                type: "POST", 
                url: 'master/Product/check_stock',
                data: {
                    product_code : $('#product_code').val(),
                }, 
                dataType: "json",
                beforeSend: function(e) {
                    if(e && e.overrideMimeType) {
                        e.overrideMimeType("application/json;charset=UTF-8");
                    }
                },
                success: function(response){ 
                    if(response.result == 0){
                        $("#status_message").text("Mohon maaf, produk hanya bisa diskontinu jika stock sudah 0. Terima Kasih");
                        $('#status').val(1);
                    }                    
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
        }        
    });
});