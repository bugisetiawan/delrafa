<!-- begin::Body -->
<body class="kt-offcanvas-panel--right kt-header--fixed kt-page--loading">
    <!-- begin:: Page -->
    <div class="kt-grid kt-grid--hor kt-grid--root">
    <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
    <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">
    <!-- begin:: Header -->
    <div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed">
        <!-- begin:: Header Menu -->
        <div class="kt-header-menu-wrapper" id="kt_header_menu_wrapper">
            <div id="kt_header_menu" class="kt-header-menu kt-header-menu--layout-default ">
                <ul class="kt-menu__nav ">
                    <li class="kt-menu__item">
                        <a href="<?php echo site_url(); ?>" class="btn btn-outline-info btn-square kt-font-bold">DASHBOARD</a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- end:: Header Menu -->
        <!-- begin:: Header Topbar -->
        <div class="kt-header__topbar">
            <div class="kt-header__topbar-item kt-header__topbar-item--user">
                <div class="kt-header__topbar-wrapper">
                    <div class="kt-header__topbar-user">
                        <span class="kt-header__topbar-welcome kt-hidden-mobile kt-font-primary kt-font-bold"><b><?php echo date('d-m-Y'); ?> | <span id="hour"></span>:<span id="minute"></span>:<span id="second"></span></b></span>
                    </div>
                </div>                
            </div>            
            <?php if($this->uri->segment(1) == "pos" && $this->uri->segment(2) == "cashier" && $this->uri->segment(3) == ""): ?>
            <!--begin: Quick panel toggler -->
            <div class="kt-header__topbar-item kt-header__topbar-item--quick-panel" data-toggle="kt-tooltip" title="Lainnya" data-placement="right">
                <span class="kt-header__topbar-icon" id="kt_quick_panel_toggler_btn">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect id="bound" x="0" y="0" width="24" height="24" />
                            <rect id="Rectangle-7" fill="#000000" x="4" y="4" width="7" height="7" rx="1.5" />
                            <path d="M5.5,13 L9.5,13 C10.3284271,13 11,13.6715729 11,14.5 L11,18.5 C11,19.3284271 10.3284271,20 9.5,20 L5.5,20 C4.67157288,20 4,19.3284271 4,18.5 L4,14.5 C4,13.6715729 4.67157288,13 5.5,13 Z M14.5,4 L18.5,4 C19.3284271,4 20,4.67157288 20,5.5 L20,9.5 C20,10.3284271 19.3284271,11 18.5,11 L14.5,11 C13.6715729,11 13,10.3284271 13,9.5 L13,5.5 C13,4.67157288 13.6715729,4 14.5,4 Z M14.5,13 L18.5,13 C19.3284271,13 20,13.6715729 20,14.5 L20,18.5 C20,19.3284271 19.3284271,20 18.5,20 L14.5,20 C13.6715729,20 13,19.3284271 13,18.5 L13,14.5 C13,13.6715729 13.6715729,13 14.5,13 Z" id="Combined-Shape" fill="#000000" opacity="0.3" />
                        </g>
                    </svg>
                </span>
            </div>
            <?php endif; ?>
            <!--end: Quick panel toggler -->            
            <!-- begin::Quick Panel -->
            <div id="kt_quick_panel" class="kt-quick-panel">
                <a href="#" class="kt-quick-panel__close" id="kt_quick_panel_close_btn"><i class="flaticon2-delete"></i></a>
                <div class="kt-quick-panel__nav">
                    <ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-brand  kt-notification-item-padding-x" role="tablist">
                        <!-- <li class="nav-item active">
                            <a class="nav-link active" data-toggle="tab" href="#kt_quick_panel_tab_notifications" role="tab">DAFTAR POS</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#kt_quick_panel_tab_logs" role="tab">DAFTAR COLLECT</a>
                        </li> -->
                        <li class="nav-item active">
                            <a class="nav-link active" data-toggle="tab" href="#kt_quick_panel_tab_settings" role="tab">LAINNYA</a>
                        </li>
                    </ul>
                </div>
                <div class="kt-quick-panel__content">
                    <div class="tab-content">
                        <div class="tab-pane fade kt-scroll" id="kt_quick_panel_tab_notifications" role="tabpanel">
                        </div>
                        <div class="tab-pane fade kt-scroll" id="kt_quick_panel_tab_logs" role="tabpanel">
                        </div>
                        <div class="tab-pane kt-quick-panel__content-padding-x fade kt-scroll show active" id="kt_quick_panel_tab_settings" role="tabpanel">                            
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label class="col-form-label text-dark kt-font-bold">DISKON</label>
                                </div>
                                <div class="col-md-6">
                                    <span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--success">
                                        <label>
                                            <input type="checkbox" id="feature_discount_p" name="feature_discount_p" value="1">
                                            <span></span>
                                        </label>
                                    </span>
                                </div>                                
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label class="col-form-label text-dark kt-font-bold">PELANGGAN BARU</label>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-sm btn-outline-success btn-square btn-block kt-font-bold" data-toggle="modal" data-target="#add_customer_modal">BUAT</button>
                                </div>                                
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label class="col-form-label text-dark kt-font-bold">COLLECT KASIR</label>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-sm btn-outline-warning btn-square btn-block kt-font-bold" data-toggle="modal" data-target="#collect_modal">COLLECT</button>
                                </div>                                
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label class="col-form-label text-dark kt-font-bold">PENUTUPAN KASIR</label>
                                </div>
                                <div class="col-md-6">                                    
                                    <button class="btn btn-sm btn-outline-danger btn-square btn-block kt-font-bold" id="close_cashier_btn">TUTUP KASIR</button>
                                </div>                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end::Quick Panel -->
            <!--begin: User Bar -->
            <div class="kt-header__topbar-item kt-header__topbar-item--user">
                <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="0px,0px">
                    <div class="kt-header__topbar-user">
                        <span class="kt-header__topbar-welcome kt-hidden-mobile kt-font-dark">Hallo,</span>
                        <span class="kt-header__topbar-username kt-hidden-mobile kt-font-dark"><strong><?php echo $this->session->userdata('name_e'); ?></strong></span>
                    </div>
                </div>
                <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-xl">
                    <!--begin: Head -->
                    <div class="kt-user-card kt-user-card--skin-light kt-notification-item-padding-x">
                        <div class="kt-user-card__name kt-font-dark">
                            <?php echo $this->session->userdata('name_e'); ?>
                        </div>
                        <div class="kt-user-card__badge">
                            <span class="btn btn-success btn-sm btn-bold btn-font-md"><?php echo $this->session->userdata('name_p'); ?></span>
                        </div>
                    </div>
                    <!--end: Head -->
                    <!--begin: Navigation -->
                    <div class="kt-notification">
                        <a href="<?php echo site_url('user/change_password/'.$this->global->encrypt($this->session->userdata('id_u'))); ?>" class="kt-notification__item">
                            <div class="kt-notification__item-icon">
                                <i class="fa fa-lock kt-font-success"></i>
                            </div>
                            <div class="kt-notification__item-details">
                                <div class="kt-notification__item-title kt-font-bold">
                                    Ganti Password
                                </div>                        
                            </div>
                        </a>
                        <div class="kt-notification__custom kt-space-between float-right">
                            <a href="<?php echo site_url('logout'); ?>" class="btn btn-label btn-label-brand btn-sm btn-bold">Logout</a>
                        </div>
                    </div>
                    <!--end: Navigation -->
                </div>
            </div>
            <!--end: User Bar -->
        </div>
        <!-- end:: Header Topbar -->
    </div>
    <!-- end:: Header -->