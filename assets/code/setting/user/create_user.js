$(document).ready(function() {
	$.getJSON("setting/User/get_employee", (data) => {
        var option = '<option value="" class="form-control kt-font-dark">- PILIH PEGAWAI -</option>';
        if (data.status.code == 200) {
            $.each(data.response, function(i, item) {
                option += '<option value="' + data.response[i].code + '" class="form-control kt-font-dark">' + data.response[i].code + ' | '+data.response[i].name + '</option>';
            });
        } else {
            option = option;
        }
        $("#employee_code").html(option).select2();
    });
    
    $('#code').on('keyup', function(){        
        $(this).val($(this).val().toUpperCase().replace(/\s+/g, ''));
        var length = $(this).val().length;
        var validasiHuruf = /^[a-zA-Z ]+$/;
        if(length == 3 && $(this).val().match(validasiHuruf))
        {
            $.ajax({
                type: "POST", 
                url: 'setting/User/check_user/code',
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
                        $("#code_user_message").text("Mohon Maaf, Kode User sudah digunakan. Silahkan gunakan yang lain, terima kasih");
                        $('#btn_save').prop("disabled", true);
                    }
                    else
                    {                    
                        $("#code_user_message").text(null);
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
            $("#code_user_message").text("Mohon Maaf, Kode User wajib 3 huruf alphabet");
            $('#btn_save').prop("disabled", true);                        
        }        
    });

    $('#name').on('keyup', function(){
        $(this).val($(this).val().toUpperCase().replace(/\s+/g, ''));
        $.ajax({
            type: "POST", 
            url: 'setting/User/check_user/name',
            data: {
                name : $(this).val(),
            }, 
            dataType: "json",
            beforeSend: function(e) {
                if(e && e.overrideMimeType) {
                    e.overrideMimeType("application/json;charset=UTF-8");
                }
            },
            success: function(response){                 
                if(response.result == 1){
					$("#name_user_message").text("Mohon Maaf, Nama User sudah digunakan. Silahkan gunakan yang lain, terima kasih");
					$('#btn_save').prop("disabled", true);
                }
                else
                {                    
                    $("#name_user_message").text(null);
                    $('#btn_save').prop("disabled", false);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    });

    $('.time').timepicker({
        minuteStep: 1,
        defaultTime: '',
        showSeconds: false,
        showMeridian: false,
        snapToStep: true
    });
    
    $("#view-password").mousedown(function(){
        $("#password").attr('type','text');
    }).mouseup(function(){
        $("#password").attr('type','password');
    }).mouseout(function(){
        $("#password").attr('type','password');
    });

    $('#view-password-check').on('change', function(){
        if($(this).prop('checked') == true)
        {
            $("#password").attr('type','text');
        }
        else
        {
            $("#password").attr('type','password');
        }
    });
    
    $(".check-module").click(function(){
        var module_id = ($(this).data('module-id'));        
        var total = $('.module-'+module_id).length;
        var checked = $('input.module-'+module_id+':checkbox:checked').length;
        if(checked != total)
        {
            $(".check-all-"+module_id).prop('checked', false);
        }
        else
        {
            $(".check-all-"+module_id).prop('checked', true);
        }
    });

    $(".check-all-module").click(function(){
        var module_id = ($(this).data('module-id'));
        $('.module-'+module_id).not(this).prop('checked', this.checked);
    });    
});