$(document).ready(function(){		    
    $('#name, #address, #contact').keyup(function() {
        $(this).val($(this).val().toUpperCase());
    });					
            
    $.getJSON("master/province/get_province", (data) => {
        for(var i=0; i<data.response.length; i++){
            $("#province").append($('<option>', {value: data.response[i].id, text: data.response[i].name}));				
        }

        var value = $('#province_id').val();		
        $('#province').val(value);
        if (value==null) 
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

                var value = $('#city_id').val();
                if (value==null) 
                {				
                    $("#city").html(option).select2();
                }		
                else
                {
                    $("#city").html(option).val(value).select2();
                }																	
            }
        });
    });
});