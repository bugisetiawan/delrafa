$(document).ready(function() {    
    $('.time').timepicker({
        minuteStep: 1,
        defaultTime: '',
        showSeconds: false,
        showMeridian: false,
        snapToStep: true
    });

	$("#delete").on('click', function() {
		var myId = $(this).data('id');
		swal.fire({
			title: 'Hapus Data?',
			text: "Data yang dihapus sudah tidak dapat dikembalikan lagi",
			type: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Ya',
			cancelButtonText: 'Tidak',
			reverseButtons: true
		}).then(function(result){
			if (result.value) {
				$.ajax({
					url		: "setting/User/delete",
					method	: "GET",
					dataType: "JSON",
					data: {
						id: myId
					},
					success		: (data) => {
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
						if(data.status.code	== 200) 
						{
							window.location.replace('setting');				
						} 
						else 
						{
							toastr.error(data.status.message);
						}
					},
					error		: (err)	=> {
						toastr.error(err.responseText);            
					}
				})			
			} else if (result.dismiss === 'cancel') {
				swal.fire(
					'Hapus Data Dibatalkan',
					'',
					'error'
				);
				$("#code").focus();
			}
		});
	});
});