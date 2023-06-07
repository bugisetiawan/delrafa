<!DOCTYPE html>
<html lang="en">    
    <head>        
        <base href="<?php echo base_url('/'); ?>">
        <meta charset="utf-8" />
        <title>TRUST System | Login</title>
        <meta name="description" content="Login Page">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">        
        <style>
        @font-face {
            font-family: 'Poppins';
            src: url(<?php echo base_url('/assets/font/Poppins-Regular.ttf') ?>);
        }
        </style>
        <link href="./assets/css/demo1/pages/general/login/login-5.css" rel="stylesheet" type="text/css" />        
        <link href="./assets/vendors/general/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" type="text/css" />        
        <link href="./assets/vendors/general/socicon/css/socicon.css" rel="stylesheet" type="text/css" />
        <link href="./assets/vendors/custom/vendors/line-awesome/css/line-awesome.css" rel="stylesheet" type="text/css" />
        <link href="./assets/vendors/custom/vendors/flaticon/flaticon.css" rel="stylesheet" type="text/css" />
        <link href="./assets/vendors/custom/vendors/flaticon2/flaticon.css" rel="stylesheet" type="text/css" />
        <link href="./assets/vendors/general/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />        
        <link href="./assets/css/demo1/style.bundle.css" rel="stylesheet" type="text/css" />
        <link href="./assets/css/demo1/skins/header/base/light.css" rel="stylesheet" type="text/css" />
        <link href="./assets/css/demo1/skins/header/menu/light.css" rel="stylesheet" type="text/css" />
        <link href="./assets/css/demo1/skins/brand/dark.css" rel="stylesheet" type="text/css" />
        <link href="./assets/css/demo1/skins/aside/dark.css" rel="stylesheet" type="text/css" />        
        <link rel="shortcut icon" href="./assets/media/logos/favicon.png"/>
    </head>    
    <body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--fixed kt-subheader--enabled kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">
        <div class="kt-grid kt-grid--ver kt-grid--root">
            <div class="kt-grid kt-grid--hor kt-grid--root  kt-login kt-login--v5 kt-login--signin" id="kt_login">
                <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--desktop kt-grid--ver-desktop kt-grid--hor-tablet-and-mobile" style="background-image: url(./assets/media//bg/bg-3.jpg);">
                    <div class="kt-login__left">
                        <div class="kt-login__wrapper">
                            <div class="kt-login__content">
                                <a class="kt-login__logo" href="javascript:void(0);">
                                	<img src="./trust_logo_dark.png" style="width: 100%;">
                                </a>   
                                <span class="kt-login__desc kt-font-dark">
									Let's TRUST the System! <?php echo date('Y'); ?>
								</span>                             
                            </div>
                        </div>
                    </div>
                    <div class="kt-login__divider">
                        <div></div>
                    </div>
                    <div class="kt-login__right">
                        <div class="kt-login__wrapper">
                            <div class="kt-login__signin">
                                <div class="kt-login__head">
                                    <h1 class="kt-font-dark"><b><?php echo $company->name; ?></b></h1>
                                    <p class="kt-font-dark"><?php echo $company->address; ?></p class="kt-font-dark">
                                </div>
                                <hr>
                                <?php if($this->session->flashdata('success')) :?>
                                    <div class="alert alert-success fade show" role="alert">
                                        <div class="alert-icon"><i class="flaticon2-checkmark"></i></div>
                                        <div class="alert-text"><?php echo $this->session->flashdata('success'); ?></div>
                                        <div class="alert-close">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true"><i class="la la-close"></i></span>
                                            </button>
                                        </div>
                                    </div>
                                    <?php elseif($this->session->flashdata('error')): ?>
                                    <div class="alert alert-danger fade show" role="alert">
                                        <div class="alert-icon"><i class="flaticon-warning"></i></div>
                                        <div class="alert-text"><?php echo $this->session->flashdata('error'); ?></div>
                                        <div class="alert-close">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true"><i class="la la-close"></i></span>
                                            </button>
                                        </div>
                                    </div>
                                <?php endif;?>  
                                <div class="kt-login__form">
                                <?php echo form_open('', array('id' => 'login_form' ,'autocomplete' => 'off')); ?>
                                        <div class="form-group">                                            
                                            <?php 
                                                $data = array('type' => 'text', 'class' => 'form-control form-control-lg','placeholder' => 'Nama User...', 'name' => 'name', 'id' => 'name', 'value' => set_value('name'), 'required' => 'true', 'autofocus' => 'true'); 
                                                echo form_input($data);
                                                echo form_error('name');
                                            ?>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <?php 
                                                    $data = array('type' => 'password', 'class' => 'form-control form-control-lg','placeholder' => 'Kata Sandi...', 'name' => 'password', 'id' => 'password', 'value' => set_value('password'), 'required' => 'true', 'autocomplete' => 'off');
                                                    echo form_input($data);
                                                ?> 
                                                <div class="input-group-append"><span class="input-group-text" id="view-password"><i class="fa fa-eye"></i></span></div>
                                            </div>     
                                            <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
                                                <input type="checkbox" id="view-password-check">Lihat Kata Sandi<span></span>
                                            </label>                                       
                                        </div>                                        
                                        <div class="kt-login__actions">                                            
                                            <?php echo form_submit('submit', 'LOGIN', array('class' => 'btn btn-pill  btn-success btn-elevate btn-elevate-air')); ?>
                                        </div>
                                    </form>
                                </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
        <script>
            var KTAppOptions = {
            	"colors": {
            		"state": {
            			"brand": "#5d78ff",
            			"dark": "#282a3c",
            			"light": "#ffffff",
            			"primary": "#5867dd",
            			"success": "#34bfa3",
            			"info": "#36a3f7",
            			"warning": "#ffb822",
            			"danger": "#fd3995"
            		},
            		"base": {
            			"label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
            			"shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
            		}
            	}
            };
        </script>
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
        <script src="./assets/vendors/general/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.js" type="text/javascript"></script>
        <script src="./assets/vendors/general/bootstrap-maxlength/src/bootstrap-maxlength.js" type="text/javascript"></script>
        <script src="./assets/vendors/custom/vendors/bootstrap-multiselectsplitter/bootstrap-multiselectsplitter.min.js" type="text/javascript"></script>
        <script src="./assets/vendors/general/bootstrap-select/dist/js/bootstrap-select.js" type="text/javascript"></script>
        <script src="./assets/vendors/general/bootstrap-switch/dist/js/bootstrap-switch.js" type="text/javascript"></script>
        <script src="./assets/vendors/custom/js/vendors/bootstrap-switch.init.js" type="text/javascript"></script>
        <script src="./assets/vendors/general/select2/dist/js/select2.full.js" type="text/javascript"></script>
        <script src="./assets/vendors/general/ion-rangeslider/js/ion.rangeSlider.js" type="text/javascript"></script>
        <script src="./assets/vendors/general/typeahead.js/dist/typeahead.bundle.js" type="text/javascript"></script>
        <script src="./assets/vendors/general/handlebars/dist/handlebars.js" type="text/javascript"></script>
        <script src="./assets/vendors/general/inputmask/dist/jquery.inputmask.bundle.js" type="text/javascript"></script>
        <script src="./assets/vendors/general/inputmask/dist/inputmask/inputmask.date.extensions.js" type="text/javascript"></script>
        <script src="./assets/vendors/general/inputmask/dist/inputmask/inputmask.numeric.extensions.js" type="text/javascript"></script>
        <script src="./assets/vendors/general/nouislider/distribute/nouislider.js" type="text/javascript"></script>
        <script src="./assets/vendors/general/owl.carousel/dist/owl.carousel.js" type="text/javascript"></script>
        <script src="./assets/vendors/general/autosize/dist/autosize.js" type="text/javascript"></script>
        <script src="./assets/vendors/general/clipboard/dist/clipboard.min.js" type="text/javascript"></script>
        <script src="./assets/vendors/general/dropzone/dist/dropzone.js" type="text/javascript"></script>
        <script src="./assets/vendors/general/summernote/dist/summernote.js" type="text/javascript"></script>
        <script src="./assets/vendors/general/markdown/lib/markdown.js" type="text/javascript"></script>
        <script src="./assets/vendors/general/bootstrap-markdown/js/bootstrap-markdown.js" type="text/javascript"></script>
        <script src="./assets/vendors/custom/js/vendors/bootstrap-markdown.init.js" type="text/javascript"></script>
        <script src="./assets/vendors/general/bootstrap-notify/bootstrap-notify.min.js" type="text/javascript"></script>
        <script src="./assets/vendors/custom/js/vendors/bootstrap-notify.init.js" type="text/javascript"></script>
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
        <script src="./assets/vendors/general/es6-promise-polyfill/promise.min.js" type="text/javascript"></script>
        <script src="./assets/vendors/general/sweetalert2/dist/sweetalert2.min.js" type="text/javascript"></script>
        <script src="./assets/vendors/custom/js/vendors/sweetalert2.init.js" type="text/javascript"></script>
        <script src="./assets/vendors/general/jquery.repeater/src/lib.js" type="text/javascript"></script>
        <script src="./assets/vendors/general/jquery.repeater/src/jquery.input.js" type="text/javascript"></script>
        <script src="./assets/vendors/general/jquery.repeater/src/repeater.js" type="text/javascript"></script>
        <script src="./assets/vendors/general/dompurify/dist/purify.js" type="text/javascript"></script>
        <script src="./assets/js/demo1/scripts.bundle.js" type="text/javascript"></script>
        <script src="./assets/js/demo1/pages/login/login-general.js" type="text/javascript"></script>
        <script>            
            KTApp.block('#login_form',{
                overlayColor: '#000000',
                type: 'v2',
                state: 'primary',
                message: '<span class="text-dark font-weight-bold">Mohon menunggu, sedang dalam proses...</span>'
            });

            $(document).ready(function(){
                KTApp.unblock('#login_form');

                $('#name').on('keyup', function(){
                    $(this).val($(this).val().toUpperCase().replace(/\s+/g, ''));
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
            });                      
        </script>
    </body>
</html>