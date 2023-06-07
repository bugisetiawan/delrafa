<div class="kt-footer kt-grid__item kt-grid kt-grid--desktop kt-grid--ver-desktop" id="kt_footer">
    <div class="kt-footer__copyright kt-font-primary kt-font-bold">
        <p class="text-dark"><?php echo date('d-m-Y') ?> | <b><?php echo $this->session->userdata['company']->name; ?></b> | <?php echo $this->session->userdata('company')->address; ?></p>
    </div>   
</div>
</div>
</div>
</div>
<div id="kt_scrolltop" class="kt-scrolltop">
    <i class="fa fa-arrow-up"></i>
</div>
<script src="./assets/vendors/general/jquery/dist/jquery.js" type="text/javascript"></script>
<script src="./assets/vendors/general/popper.js/dist/umd/popper.js" type="text/javascript"></script>
<script src="./assets/vendors/general/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
<script src="./assets/vendors/general/js-cookie/src/js.cookie.js" type="text/javascript"></script>
<script src="./assets/vendors/general/moment/min/moment.min.js" type="text/javascript"></script>
<script src="./assets/vendors/general/tooltip.js/dist/umd/tooltip.min.js" type="text/javascript"></script>
<script src="./assets/vendors/general/perfect-scrollbar/dist/perfect-scrollbar.js" type="text/javascript"></script>
<script src="./assets/vendors/general/sticky-js/dist/sticky.min.js" type="text/javascript"></script>
<script src="./assets/vendors/general/wnumb/wNumb.js" type="text/javascript"></script>
<script src="./assets/vendors/general/jquery-form/dist/jquery.form.min.js" type="text/javascript"></script>
<script src="./assets/vendors/general/block-ui/jquery.blockUI.js" type="text/javascript"></script>
<script src="./assets/vendors/general/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="./assets/vendors/custom/js/vendors/bootstrap-datepicker.init.js" type="text/javascript"></script>
<script src="./assets/vendors/general/bootstrap-datetime-picker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="./assets/vendors/general/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
<script src="./assets/vendors/custom/js/vendors/bootstrap-timepicker.init.js" type="text/javascript"></script>
<script src="./assets/vendors/general/bootstrap-daterangepicker/daterangepicker.js" type="text/javascript"></script>
<script src="./assets/vendors/general/bootstrap-select/dist/js/bootstrap-select.js" type="text/javascript"></script>
<script src="./assets/vendors/general/select2/dist/js/select2.full.js" type="text/javascript"></script>
<script src="./assets/vendors/general/typeahead.js/dist/typeahead.bundle.js" type="text/javascript"></script>
<script src="./assets/vendors/general/handlebars/dist/handlebars.js" type="text/javascript"></script>
<script src="./assets/vendors/general/inputmask/dist/jquery.inputmask.bundle.js" type="text/javascript"></script>
<script src="./assets/vendors/general/inputmask/dist/inputmask/inputmask.date.extensions.js" type="text/javascript"></script>
<script src="./assets/vendors/general/inputmask/dist/inputmask/inputmask.numeric.extensions.js" type="text/javascript"></script>
<script src="./assets/vendors/general/summernote/dist/summernote.js" type="text/javascript"></script>
<script src="./assets/vendors/general/jquery-validation/dist/jquery.validate.js" type="text/javascript"></script>
<script src="./assets/vendors/general/jquery-validation/dist/additional-methods.js" type="text/javascript"></script>
<script src="./assets/vendors/custom/js/vendors/jquery-validation.init.js" type="text/javascript"></script>
<script src="./assets/vendors/general/toastr/build/toastr.min.js" type="text/javascript"></script>
<script src="./assets/vendors/general/raphael/raphael.js" type="text/javascript"></script>
<script src="./assets/vendors/general/morris.js/morris.js" type="text/javascript"></script>
<script src="./assets/vendors/general/chart.js/dist/Chart.bundle.js" type="text/javascript"></script>
<script src="./assets/vendors/custom/vendors/bootstrap-session-timeout/dist/bootstrap-session-timeout.min.js" type="text/javascript"></script>
<script src="./assets/vendors/custom/vendors/jquery-idletimer/idle-timer.min.js" type="text/javascript"></script>
<script src="./assets/vendors/general/waypoints/lib/jquery.waypoints.js" type="text/javascript"></script>
<script src="./assets/vendors/general/counterup/jquery.counterup.js" type="text/javascript"></script>
<script src="./assets/vendors/general/sweetalert2/dist/sweetalert2.min.js" type="text/javascript"></script>
<script src="./assets/vendors/custom/js/vendors/sweetalert2.init.js" type="text/javascript"></script>
<script src="./assets/vendors/general/jquery.repeater/src/lib.js" type="text/javascript"></script>
<script src="./assets/vendors/general/jquery.repeater/src/jquery.input.js" type="text/javascript"></script>
<script src="./assets/vendors/general/jquery.repeater/src/repeater.js" type="text/javascript"></script>
<script src="./assets/vendors/general/bootstrap-maxlength/src/bootstrap-maxlength.js" type="text/javascript"></script>
<script src="./assets/js/demo1/scripts.bundle.js" type="text/javascript"></script>
<script src="./assets/vendors/custom/datatables/datatables.bundle.js" type="text/javascript"></script>
<script>
	var KTAppOptions = {
    	"colors": {
    		"state": {
    			"brand": "#5d78ff",
    			"dark": "#000000", // #282a3c
    			"light": "#ffffff",
    			"primary": "#5867dd",
    			"success": "#34bfa3",
    			"info": "#36a3f7",
    			"warning": "#ffb822",
    			"danger": "#ff0000"
    		},
    		"base": {
    			"label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
    			"shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
    		}
    	}
    };

    var url = window.location;
	$('li.kt-menu__item a').filter(function(){
		(url == this.href) ? $(this).parent("li.kt-menu__item").addClass('kt-menu__item--active') : "";
	})
	
	toastr.options = {
		closeButton: 0,
		debug: 0,
		newestOnTop: 0,
		progressBar: !0,
		positionClass: "toast-top-right",
		preventDuplicates: !0,
		showDuration: "3000",
		hideDuration: "1000",
		timeOut: "3000",
		extendedTimeOut: "1000",
		showEasing: "swing",
		hideEasing: "linear",
		showMethod: "fadeIn",
		hideMethod: "fadeOut"
	};
	
	// $.sessionTimeout({
	// 	title: 'MOHON PERHATIAN!',
	// 	message: 'Aplikasi sedang tidak digunakan!',
	// 	keepAliveUrl: '<?php echo site_url(''); ?>',
	// 	keepAliveButton: 'Saya Masih Disini',
	// 	redirUrl: '<?php echo site_url('logout'); ?>',
	// 	logoutUrl: '<?php echo site_url('logout'); ?>',
	// 	logoutButton: 'Logout',
	// 	warnAfter: 7200000,
	// 	redirAfter: 7500000,
	// 	ignoreUserActivity: true,
	// 	countdownMessage: 'keluar dalam waktu {timer} detik.',
	// 	countdownBar: true
	// });
</script>
<?php if(isset($script)):?>
<?php foreach($script AS $script):?>
<script src="./assets/code/<?php echo $script; ?>" type="text/javascript"></script>
<?php endforeach; ?>
<?php endif; ?>
</body>
<!-- end::Body -->
</html>