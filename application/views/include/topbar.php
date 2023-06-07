<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">
<!-- begin:: Header -->
<div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed ">
    <!-- begin:: Header Menu -->
    <button class="kt-header-menu-wrapper-close" id="kt_header_menu_mobile_close_btn"><i class="la la-close"></i></button>
    <div class="kt-header-menu-wrapper" id="kt_header_menu_wrapper">
		<div id="kt_header_menu" class="kt-header-menu kt-header-menu-mobile  kt-header-menu--layout-default ">
			<ul class="kt-menu__nav ">
                <li class="kt-menu__item">
					<a href="<?php echo site_url('dashboard'); ?>" class="btn btn-outline-primary btn-sm"><span class="kt-menu__link-text"><i class="fa fa-home"></i> HALAMAN UTAMA</span></a>
                </li>
                <?php if($this->system->check_access('product', 'read')) { ?>
                <li class="kt-menu__item">                
                    <?php  $access_user_id = [1, 3, 14, 17];
		            if(in_array($this->session->userdata('id_u'), $access_user_id)): ?>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm btn-wide dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-cube"></i>
                            <span class="d-none d-sm-inline">PRODUK</span>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                            <a class="dropdown-item text-dark" href="<?php echo site_url('product'); ?>">- Master Produk</a>
                            <a class="dropdown-item text-dark" href="<?php echo site_url('product/price_list/'); ?>">- Daftar Harga (Jual,Beli, & HPP)</a>
                        </div>
                    </div>
                    <?php else: ?>
                    <a href="<?php echo site_url('product'); ?>" class="btn btn-outline-primary btn-sm btn-wide"><span class="kt-menu__link-text"><i class="fa fa-cube"></i> PRODUK</span></a>
                    <?php endif; ?>
                </li>
                <?php } if($this->system->check_access('purchase/order', 'read') || $this->system->check_access('purchase/invoice', 'read')) { ?>
				<li class="kt-menu__item">
					<a href="<?php echo site_url('purchase/invoice'); ?>" class="btn btn-outline-primary btn-sm btn-wide"><span class="kt-menu__link-text"><i class="fa fa-shopping-cart"></i> PEMBELIAN</span></a>
                </li>
                <?php } if($this->system->check_access('sales/order', 'read') || $this->system->check_access('sales/order/taking', 'read') || $this->system->check_access('sales/invoice', 'read')) { ?>
				<li class="kt-menu__item">
					<a href="<?php echo site_url('sales/invoice'); ?>" class="btn btn-outline-primary btn-sm btn-wide"><span class="kt-menu__link-text"><i class="fa fa-dolly"></i> PENJUALAN</span></a>
                </li>            
                <?php } if($this->system->check_access('report', 'read')) { ?>    
                <li class="kt-menu__item">
					<a href="<?php echo site_url('report'); ?>" class="btn btn-outline-primary btn-sm btn-wide"><span class="kt-menu__link-text"><i class="fa fa-book"></i> LAPORAN</span></a>
                </li>
                <?php }?>
			</ul>
		</div>
	</div>
    <!-- end:: Header Menu -->    
    <!-- begin:: Header Topbar -->
    <div class="kt-header__topbar">        
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
                            <i class="fa fa-lock kt-font-primary"></i>
                        </div>
                        <div class="kt-notification__item-details">
                            <div class="kt-notification__item-title kt-font-bold">
                                Perbarui Kata Sandi
                            </div>                        
                        </div>
                    </a>
                    <?php if($this->session->userdata('name_p') == "LOGIN AS"): ?>
                        <a href="javascript:void(0);" class="kt-notification__item">
                            <div class="kt-notification__item-icon">
                                <i class="fa fa-arrow-left kt-font-danger"></i>
                            </div>
                            <div class="kt-notification__item-details">
                                <div class="kt-notification__item-title kt-font-bold">
                                    <form action="<?php echo site_url('setting/User/login_as/1'); ?>" method="POST">
                                        <button type="submit" class="btn btn-sm btn-danger">KEMBALI SEBAGAI WEB DEVELOPER</button>
                                    </form>    
                                </div>                        
                            </div>
                        </a>                                
                    <?php endif; ?>
                    <div class="kt-notification__custom kt-space-between float-right">
                        <a href="<?php echo site_url('logout'); ?>" class="btn btn-label btn-label-brand btn-sm btn-bold">Keluar</a>
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