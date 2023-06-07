$(document).ready(function(){		
	toastr.options = {
		closeButton: !1,
		debug: !1,
		newestOnTop: !1,
		progressBar: !0,
		positionClass: "toast-top-right",
		preventDuplicates: !0,
		showDuration: "300",
		hideDuration: "1000",
		timeOut: "3000",
		extendedTimeOut: "1000",
		showEasing: "swing",
		hideEasing: "linear",
		showMethod: "fadeIn",
		hideMethod: "fadeOut"
	};

	$('#nik').maxlength({
		warningClass: "kt-badge kt-badge--warning kt-badge--rounded kt-badge--inline",
		limitReachedClass: "kt-badge kt-badge--success kt-badge--rounded kt-badge--inline"
	});

	$('#name, #address').keyup(function() {
		$(this).val($(this).val().toUpperCase());
	});
	
	$.getJSON("master/religion/get_religion", (data) => {
		for(var i=0; i<data.response.length; i++){
			$("#religion").append($('<option>', {value: data.response[i].id, text: data.response[i].name}));
		}
		var value = $('#religion_id').val();		
		$('#religion').val(value);
		if (value==null) 
		{				
			$('#religion').select2();
		}		
		else
		{
			$('#religion').select2();
		}
	});

	$.getJSON("master/position/get_position", (data) => {
		for(var i=0; i<data.response.length; i++){
			$("#position").append($('<option>', {value: data.response[i].id, text: data.response[i].name}));
		}
		var value = $('#position_id').val();		
		$('#position').val(value);
		if (value==null) 
		{				
			$('#position').select2();		  
		}		
		else
		{
			$('#position').select2().trigger('change');	
		}
	});
	
	$.getJSON("master/education/get_education", (data) => {
		for(var i=0; i<data.response.length; i++){
			$("#education").append($('<option>', {value: data.response[i].id, text: data.response[i].code}));
		}
		var value = $('#education_id').val();		
		$('#education').val(value);
		if (value==null) 
		{				
			$('#education').select2();		  
		}		
		else
		{
			$('#education').select2().trigger('change');	
		}
	});

	$.getJSON("master/city/get_all_city", (data) => {
		for(var i=0; i<data.response.length; i++){
			$("#born").append($('<option>', {value: data.response[i].id, text: data.response[i].name}));
		}
		var value = $('#born_id').val();		
		$('#born').val(value);
		if (value==null) 
		{				
			$('#born').select2();		  
		}		
		else
		{
			$('#born').select2().trigger('change');	
		}
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

	$('.date').datepicker({
        format: "dd-mm-yyyy",
        todayHighlight: true,
		clearBtn: true,
		autoclose: true,
		orientation: "bottom auto"
    });
});

function previewGambar(input){
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {
			$('#preview').attr('src', e.target.result);
		}
		reader.readAsDataURL(input.files[0]);
	}
}

$("#photo").change(function(){
	previewGambar(this);
});