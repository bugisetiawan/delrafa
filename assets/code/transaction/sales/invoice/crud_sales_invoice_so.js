jQuery(document).ready(function() {       
    $('#date').datepicker({
        format: "dd-mm-yyyy",		
		todayHighlight: true,
		clearBtn: true,
		autoclose: true,
		orientation: "bottom auto"        
    });

    $('#credit_method').hide();    
    $('#payment').change(function(){
        if($(this).val() == 1)
        {
            $('#payment_due').val(0);
            $('#payment_due').prop( "readonly", true);
            $('#credit_method').hide();
        }        
        else
        {
            $('#payment_due').prop( "readonly", false);
            $('#credit_method').show();
        }
        dueday();
        calculate();
     });

    $('.repeater').repeater({
        isFirstItemUndeletable: false,
        hide: function () {
            $(this).remove();
            calculate();
        }
    });

    $("#product_table").on("click", ".qty", function() {
        $(this).select();
    });

    $("#product_table").on("keyup", ".qty", function() {
        var id = Math.round(new Date().getTime() +(Math.random() * 100));
        $(this).attr('id', id);
        price = $(this).attr('id')+2;
        total = $(this).attr('id')+3;
        $(this).closest('tr').find('.price').attr('id',price);
        $(this).closest('tr').find('.total').attr('id',total);

        var price_value = $('#'+price).val().replace(/\,/g, "");
        var subtotal = Number($('#'+id).val().replace(/\,/g, "")) * Number(price_value);
        $('#'+total).val(format_number(String(subtotal)));        
        calculate();
    });    

    $("#discount_p, #discount_rp, #down_payment").on("click",function() {
        $(this).select();
    });
    $(".qty, #discount_rp, #down_payment").on("keyup",function() {
        $(this).val(format_number($(this).val()));
    });

    $('#discount_amount').hide();
    $('#discount_method').change(function(){
        if($(this).val() == 1)
        {
            $('#discount_amount').hide();
            $('#discount_percent').show();
        }
        else
        {
            $('#discount_percent').hide();
            $('#discount_amount').show();            
        }
    });        
    
    $('#discount_p').keyup(function(){ 
        var discount_rp = (Number($(this).val().replace(/\,/g, "")) / 100)*Number($('#subtotal').val().replace(/\,/g, ""));
        $('#discount_rp').val(discount_rp);
        if(Number($(this).val().replace(/\,/g, "") >= 100))
        {
            $('#discount_p').val(0); $('#discount_rp').val(0);
            alert("Mohon Maaf, maksimal diskon adalah 100%. Silahkan isi ulang kembali, terima kasih.");
            $(this).select();
        }
        calculate();
    });
    
    $('#discount_rp').keyup(function(){
        var discount_p = (Number($(this).val().replace(/\,/g, "")) / Number($('#subtotal').val().replace(/\,/g, "")))*100;
        $('#discount_p').val(discount_p);
        if(Number($(this).val().replace(/\,/g, "")) >= Number($('#subtotal').val().replace(/\,/g, "")))
        {
            $('#discount_p').val(0); $('#discount_rp').val(0);
            alert("Mohon Maaf, maksimal diskon adalah 100%. Silahkan isi ulang kembali, terima kasih.");
            $(this).select();
        }
        calculate();
    });
    
    $('#down_payment').keyup(function(){
        calculate();
	});
});

function dueday()
{
    if($('#payment').val() == 1)
    {        
        $('#payment_due').val('0');
    }
    else
    {
        $.ajax({
            type: "POST", 
            url: 'transaction/Sales/get_payment_due',
            data: {
                customer_code : $('#customer_code').val(),
            }, 
            dataType: "json",
            beforeSend: function(e) {
                if(e && e.overrideMimeType) {
                    e.overrideMimeType("application/json;charset=UTF-8");
                }
            },
            success: function(data){ 
                $('#payment_due').val(data.dueday);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });        
    }
}

function calculate()
{
    var total_product = $(".qty");
    $('#total_product').val(total_product.length);
    
    var total_qty = 0;
    $('.qty').each(function(){
        total_qty += Number($(this).val().replace(/\,/g, ""));
    });
    $('#total_qty').val(total_qty);

    var subtotal = 0;
    $('.total').each(function() {
        subtotal += Number($(this).val().replace(/\,/g, ""));
    });
    $('#subtotal').val(format_number(String(subtotal)));

    var discount_rp = $('#discount_rp').val().replace(/\,/g, "");    
           
    var grandtotal = subtotal - discount_rp;

    if(grandtotal > 0)
    {
        if($('#payment').val() == 2)
        {
            $('#account_payable').val(format_number(String(Number($('#down_payment').val().replace(/\,/g, "")) - Number(grandtotal))));
            if(Number($('#down_payment').val().replace(/\,/g, "")) > Number(grandtotal))
            {
                $('#btn_save').attr('disabled', true);
            }
            else
            {
                $('#btn_save').attr('disabled', false);
            }
        }
        else
        {        
            $('#down_payment, #account_payable').val(0);
            $('#btn_save').attr('disabled', false);
        }        
    }
    else
    {
        $('#btn_save').attr('disabled', true);
    }
    

    $('#grandtotal').val(format_number(String(grandtotal)));
}


function format_number(angka, prefix){
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