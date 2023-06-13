$(document).ready(function(){	
	$('#code').on('keyup', function(){        
        $(this).val($(this).val().toUpperCase().replace(/\s+/g, ''));        
        var length = $(this).val().length;
        if(length == 5)
        {
            $.ajax({
                type: "POST", 
                url: 'master/Customer/check_code/',
                data: {
                    code : $(this).val(),
                }, 
                dataType: "json",
                beforeSend: function(e) {
                    if(e && e.overrideMimeType) {
                        e.overrideMimeType("application/json;charset=UTF-8");
                    }
                },
                success: function(response){                 
                    if(response.result == 1){
                        $("#code_customer_message").text("Mohon Maaf, Kode Pelanggan sudah digunakan. Silahkan gunakan yang lain, terima kasih");
                        $('#btn_save').prop("disabled", true);
                    }
                    else
                    {                    
                        $("#code_customer_message").text(null);
                        $('#btn_save').prop("disabled", false);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
        }
        else
        {            
            $("#code_customer_message").text("Mohon Maaf, Kode Pelanggan wajib 5 angka");
            $('#btn_save').prop("disabled", true);                        
        }        
    });
				
	jQuery('#name, #address, #contact').keyup(function() {
		$(this).val($(this).val().toUpperCase());
	});

	$("#pkp").change(function(){
		if($("input[name=pkp]").is(":checked"))
		{
			$('#npwp').attr('required', 'true');
		}
		else
		{
			$('#npwp').removeAttr('required');
		}			
		});
			
	$.getJSON("master/Zone/get_zone", (data) => {
		for(var i=0; i<data.response.length; i++){
			$("#zone").append($('<option>', {value: data.response[i].id, text: data.response[i].code}));				
		}

		var value = $('#zone_id').val();		
		$('#zone').val(value);
		if (value == null) 
		{				
			$('#zone').select2();		  
		}		
		else
		{
			$('#zone').select2().trigger('change');	
		}						  
	});

	$.getJSON("master/Province/get_province", (data) => {
		for(var i=0; i<data.response.length; i++){
			$("#province").append($('<option>', {value: data.response[i].id, text: data.response[i].name}));
		}
		var province_id = $('#province_id').val();
		$('#province').val(province_id);
		if (province_id == null || province_id == "") 
		{				
			$('#province').select2();		  
		}		
		else
		{
			$('#province').select2().trigger('change');	
		}
	});

	$("#province").change(function(){			
		var province_id = $(this).val();			
		$.ajax({
			url			: "master/city/get_city",
			type		: "GET",
			dataType	: "JSON",
			data		: {province_id:province_id},
			success		: (data) => {
				var option	= '';
				for(var i=0; i<data.response.length; i++){
					option	+=	'<option value="'+data.response[i].id+'">'+data.response[i].name+'</option>';
				}

				var city_id = $('#city_id').val();
				if (city_id == null || city_id =="") 
				{
					$("#city").html(option).select2();
				}
				else
				{
					$("#city").html(option).val(city_id).select2();
				}
			}
		});
	});	

	$('#credit, #dueday').on('click', function() {
		this.select();
	});

	$('#credit').on('keyup blur', function() {		
		if(this.value == null || this.value == "")
		{
			this.value = format_number("0");
		}
		else
		{
			this.value = format_number(this.value);
		}
	});
});

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