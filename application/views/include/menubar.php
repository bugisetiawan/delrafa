<!-- begin::Body -->
<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--fixed kt-subheader--enabled kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-aside--minimize kt-footer--fixed">
    <!-- begin:: Page -->
    <!-- begin:: Header Mobile -->
    <div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">
        <div class="kt-header-mobile__logo">
            <a href="<?php echo site_url('dashboard'); ?>">
                <h3 class="text-light font-weight-bold">TRUST System</h3>
            </a>
        </div>
        <div class="kt-header-mobile__toolbar">
            <button class="kt-header-mobile__toggler kt-header-mobile__toggler--left" id="kt_aside_mobile_toggler"><span></span></button>            
        </div>
    </div>
    <!-- end:: Header Mobile -->
    <div class="kt-grid kt-grid--hor kt-grid--root">
    <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
    <!-- begin:: Aside -->
    <button class="kt-aside-close " id="kt_aside_close_btn"><i class="la la-close"></i></button>
    <div class="kt-aside  kt-aside--fixed  kt-grid__item kt-grid kt-grid--desktop kt-grid--hor-desktop" id="kt_aside">
        <!-- begin:: Aside -->
        <div class="kt-aside__brand kt-grid__item " id="kt_aside_brand">
            <div class="kt-aside__brand-logo">
                <a href="<?php echo site_url('dashboard'); ?>">
                <img width="100%" alt="Logo" src="./trust_logo_light.png" />
                </a>
            </div>
            <div class="kt-aside__brand-tools">
                <button class="kt-aside__brand-aside-toggler" id="kt_aside_toggler">
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <polygon id="Shape" points="0 0 24 0 24 24 0 24" />
                                <path d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z" id="Path-94" fill="#000000" fill-rule="nonzero" transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999) " />
                                <path d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z" id="Path-94" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999) " />
                            </g>
                        </svg>
                    </span>
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <polygon id="Shape" points="0 0 24 0 24 24 0 24" />
                                <path d="M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z" id="Path-94" fill="#000000" fill-rule="nonzero" />
                                <path d="M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z" id="Path-94" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999) " />
                            </g>
                        </svg>
                    </span>
                </button>
            </div>
        </div>
        <!-- end:: Aside -->
        <!-- begin:: Aside Menu -->
        <?php $this->load->model('setting/User_model', 'user'); ?>
        <div class="kt-aside-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_aside_menu_wrapper">
            <div id="kt_aside_menu" class="kt-aside-menu " data-ktmenu-vertical="1" data-ktmenu-scroll="1" data-ktmenu-dropdown-timeout="500">
                <ul class="kt-menu__nav ">
                    <li class="kt-menu__item" aria-haspopup="true">
                        <a href="<?php echo site_url('dashboard'); ?>" class="kt-menu__link ">
                        <span class="kt-menu__link-icon">
                        <i class="fa fa-home"></i>
                        </span>
                        <span class="kt-menu__link-text">Halaman Utama</span>
                        </a>
                    </li>
                    <li class="kt-menu__section ">
                        <h4 class="kt-menu__section-text">UMUM</h4>
                        <i class="kt-menu__section-icon flaticon-more-v2"></i>
                    </li>
                    <li class="kt-menu__item" aria-haspopup="true">
                        <a href="<?php echo site_url('general/product_list'); ?>" class="kt-menu__link ">
                        <span class="kt-menu__link-icon">
                        <i class="fa fa-list"></i>
                        </span>
                        <span class="kt-menu__link-text">Daftar Produk</span>
                        </a>
                    </li>                    
                    <li class="kt-menu__item" aria-haspopup="true">
                        <a href="<?php echo site_url('general/product_sales_history'); ?>" class="kt-menu__link ">
                        <span class="kt-menu__link-icon">
                        <i class="fa fa-history"></i>
                        </span>
                        <span class="kt-menu__link-text">Riwayat Penjualan Produk</span>
                        </a>
                    </li>
                    <!-- <li class="kt-menu__item" aria-haspopup="true">
                        <a href="<?php echo site_url('general/cancel_do'); ?>" class="kt-menu__link ">
                        <span class="kt-menu__link-icon">
                        <i class="fa fa-times"></i>
                        </span>
                        <span class="kt-menu__link-text">Pembatalan DO</span>
                        </a>
                    </li> -->
                    <?php ?>
					<?php $modules = $this->crud->get_where('module', ['category' => 1])->result_array(); $res = 0;
					foreach($modules AS $module)
					{
						if($this->system->check_access($module['url'], 'A'))
						{
							$res++;
						}
					}
					if($res > 0): ?>
                    <li class="kt-menu__section ">
                        <h4 class="kt-menu__section-text">MASTER DATA</h4>
                        <i class="kt-menu__section-icon flaticon-more-v2"></i>
                    </li>
					<?php endif; ?>
                    <?php $categories = ['product', 'department', 'unit', 'warehouse']; $res = 0;
					foreach($categories AS $category)
					{
						if($this->system->check_access($category, 'A'))
						{
							$res++;
						}
					}
					if($res > 0): ?>
                    <li class="kt-menu__item kt-menu__item--submenu <?php if (in_array($this->uri->segment(1), ['product', 'department', 'unit', 'warehouse'])) { echo "kt-menu__item--open kt-menu__item--here"; } ?>" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                        <a href="javascript:void(0)" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                        <i class="fa fa-cube"></i>
                        </span>
                        <span class="kt-menu__link-text">Produk</span><i class="kt-menu__ver-arrow la la-angle-right"></i>
                        </a>
                        <div class="kt-menu__submenu " kt-hidden-height="160" >
                            <span class="kt-menu__arrow"></span>
                            <ul class="kt-menu__subnav">
                                <?php if($this->system->check_access('product', 'A')): ?>
                                <li class="kt-menu__item" aria-haspopup="true"><a href="<?php  echo site_url('product')?>" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Produk</span></a></li>
                                <?php endif; ?>
                                <?php if($this->system->check_access('department', 'A')): ?>
                                <li class="kt-menu__item" aria-haspopup="true"><a href="<?php  echo site_url('department')?>" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Kategori Produk</span></a></li>
                                <?php endif; ?>
                                <?php if($this->system->check_access('unit', 'A')): ?> 
                                <li class="kt-menu__item" aria-haspopup="true"><a href="<?php  echo site_url('unit')?>" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Satuan</span></a></li>
                                <?php endif; ?>
								<?php if($this->system->check_access('warehouse', 'A')): ?> 
                                <li class="kt-menu__item" aria-haspopup="true"><a href="<?php  echo site_url('warehouse')?>" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Gudang</span></a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>
                    <?php if($this->system->check_access('customer', 'A')): ?>
                    <li class="kt-menu__item" aria-haspopup="true">
                        <a href="<?php echo site_url('customer'); ?>"class="kt-menu__link ">
                        <span class="kt-menu__link-icon">
                        <i class="fa fa-users"></i>
                        </span>
                        <span class="kt-menu__link-text">Pelanggan</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if($this->system->check_access('supplier', 'A')): ?>
                    <li class="kt-menu__item" aria-haspopup="true">
                        <a href="<?php echo site_url('supplier'); ?>"class="kt-menu__link ">
                        <span class="kt-menu__link-icon">
                        <i class="fa fa-city"></i>
                        </span>
                        <span class="kt-menu__link-text">Supplier</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if($this->system->check_access('employee', 'A')): ?>
                    <li class="kt-menu__item" aria-haspopup="true">
                        <a href="<?php echo site_url('employee'); ?>"class="kt-menu__link ">
                        <span class="kt-menu__link-icon">
                        <i class="fa fa-id-card-alt"></i>
                        </span>
                        <span class="kt-menu__link-text">Pegawai</span>
                        </a>
                    </li>
                    <?php endif; ?>                       
                    <?php $categories = ['zone', 'position']; $res = 0;
					foreach($categories AS $category)
					{
						if($this->system->check_access($category, 'A'))
						{
							$res++;
						}
					}
					if($res > 0): ?>                                           
                    <li class="kt-menu__item kt-menu__item--submenu <?php if ($this->uri->segment(1) == "cost" ||$this->uri->segment(1) == "position" || $this->uri->segment(1) == "bank" || $this->uri->segment(1) == "zone" || $this->uri->segment(1) == "education" || $this->uri->segment(1) == "religion" || $this->uri->segment(1) == "depreciation" ) { echo "kt-menu__item--open kt-menu__item--here"; } ?>" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                        <a href="javascript:void(0)" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                        <i class="fa fa-database"></i>
                        </span>
                        <span class="kt-menu__link-text">Lainnya</span><i class="kt-menu__ver-arrow la la-angle-right"></i>
                        </a>
                        <div class="kt-menu__submenu " kt-hidden-height="160" >
                            <span class="kt-menu__arrow"></span>
                            <ul class="kt-menu__subnav">                                
                                <?php if($this->system->check_access('zone', 'A')): ?>
                                <li class="kt-menu__item" aria-haspopup="true"><a href="<?php  echo site_url('zone')?>" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Zona Pelanggan</span></a></li>
                                <?php endif; ?>
                                <?php if($this->system->check_access('position', 'A')): ?>
                                <li class="kt-menu__item" aria-haspopup="true"><a href="<?php  echo site_url('position')?>" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Jabatan Pegawai</span></a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>
					<?php $modules = $this->crud->get_where('module', ['category >=' => 2, 'category <=' => 3])->result_array(); $res = 0;
					foreach($modules AS $module)
					{
						if($this->system->check_access($module['url'], 'A'))
						{
							$res++;
						}
					}
					if($res > 0): ?>
                    <li class="kt-menu__section ">
                        <h4 class="kt-menu__section-text">TRANSAKSI & STOK</h4>
                        <i class="kt-menu__section-icon flaticon-more-v2"></i>
                    </li>
					<?php endif; ?>
                    <?php
					$categories = ['purchase/invoice', 'purchase/return']; $res = 0;
					foreach($categories AS $category)
					{
						if($this->system->check_access($category, 'A'))
						{
							$res++;
						}
					}                    
					if($res > 0): ?>
                    <li class="kt-menu__item kt-menu__item--submenu <?php if ($this->uri->segment(1) == "purchase") { echo "kt-menu__item--open kt-menu__item--here"; } ?>" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                        <a href="javascript:void(0)" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                        <i class="fa fa-shopping-cart"></i>
                        </span>
                        <span class="kt-menu__link-text">Pembelian</span><i class="kt-menu__ver-arrow la la-angle-right"></i>
                        </a>
                        <div class="kt-menu__submenu" kt-hidden-height="160" >
                            <span class="kt-menu__arrow"></span>
                            <ul class="kt-menu__subnav">
                                <?php if($this->system->check_access('purchase/invoice', 'A')): ?>
                                <li class="kt-menu__item <?php if ($this->uri->segment(1) == "purchase" && $this->uri->segment(2) != "return" && $this->uri->segment(2) != "return") { echo "kt-menu__item--active"; } ?>" aria-haspopup="true"><a href="<?php echo site_url('purchase/invoice'); ?>" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Pembelian</span></a></li>
                                <?php endif; ?>
                                <?php if($this->system->check_access('purchase/return', 'A')): ?>
                                <li class="kt-menu__item <?php if ($this->uri->segment(1) == "purchase" && $this->uri->segment(2) == "return" ) { echo "kt-menu__item--active"; } ?>" aria-haspopup="true"><a href="<?php  echo site_url('purchase/return')?>" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Retur Pembelian</span></a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>
					<?php
					$categories = ['sales/invoice', 'sales/return']; $res = 0;
					foreach($categories AS $category)
					{
						if($this->system->check_access($category, 'A'))
						{
							$res++;
						}
					}                    
					if($res > 0): ?>
                    <li class="kt-menu__item kt-menu__item--submenu <?php if ($this->uri->segment(1) == "sales") { echo "kt-menu__item--open kt-menu__item--here"; } ?>" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                        <a href="javascript:void(0)" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                        <i class="fa fa-dolly"></i>
                        </span>
                        <span class="kt-menu__link-text">Penjualan</span><i class="kt-menu__ver-arrow la la-angle-right"></i>
                        </a>
                        <div class="kt-menu__submenu" kt-hidden-height="160" >
                            <span class="kt-menu__arrow"></span>
                            <ul class="kt-menu__subnav">
                                <?php if($this->system->check_access('sales/invoice', 'A')): ?>
                                <li class="kt-menu__item <?php if ($this->uri->segment(1) == "sales" && $this->uri->segment(2) == "invoice") { echo "kt-menu__item--active"; } ?>" aria-haspopup="true"><a href="<?php echo site_url('sales/invoice'); ?>" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Penjualan</span></a></li>
                                <?php endif; ?>
                                <?php if($this->system->check_access('sales/return', 'A')): ?>
                                <li class="kt-menu__item <?php if ($this->uri->segment(1) == "sales" && $this->uri->segment(2) == "return") { echo "kt-menu__item--active"; } ?>" aria-haspopup="true"><a href="<?php  echo site_url('sales/return')?>" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Retur Penjualan</span></a></li>
                                <?php endif; ?>
                                <?php if($this->system->check_access('sales/delivery', 'A')): ?>
                                <li class="kt-menu__item <?php if ($this->uri->segment(1) == "delivery") { echo "kt-menu__item--active"; } ?>" aria-haspopup="true"><a href="<?php  echo site_url('delivery')?>" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Pengiriman</span></a></li>
                                <?php endif; ?>
                                <?php if($this->system->check_access('sales/billing', 'A')): ?>
                                <li class="kt-menu__item <?php if ($this->uri->segment(1) == "sales" && $this->uri->segment(2) == "billing") { echo "kt-menu__item--active"; } ?>" aria-haspopup="true"><a href="<?php  echo site_url('sales/billing')?>" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Penagihan</span></a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
					<?php endif; ?>                    
                    <li class="kt-menu__item kt-menu__item--submenu <?php if ($this->uri->segment(1) == "mutation" || $this->uri->segment(1) == "repacking" || $this->uri->segment(1) == "opname" || $this->uri->segment(1) == "product_usage") { echo "kt-menu__item--open kt-menu__item--here"; } ?>" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                        <a href="javascript:void(0);" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                        <i class="fa fa-dot-circle"></i>
                        </span>
                        <span class="kt-menu__link-text">Persediaan</span><i class="kt-menu__ver-arrow la la-angle-right"></i>
                        </a>
                        <div class="kt-menu__submenu " kt-hidden-height="160" >
                            <span class="kt-menu__arrow"></span>
                            <ul class="kt-menu__subnav">
                                <?php if($this->system->check_access('mutation', 'A')): ?>
                                <li class="kt-menu__item <?php if ($this->uri->segment(1) == "mutation") { echo "kt-menu__item--active"; } ?>" aria-haspopup="true"><a href="<?php echo site_url('mutation'); ?>" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Mutasi</span></a></li>
                                <?php endif; ?>
                                <?php if($this->system->check_access('repacking', 'A')): ?>
                                <li class="kt-menu__item <?php if ($this->uri->segment(1) == "repacking") { echo "kt-menu__item--active"; } ?>" aria-haspopup="true"><a href="<?php  echo site_url('repacking')?>" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Repacking</span></a></li>
                                <?php endif; ?>
                                <?php if($this->system->check_access('opname', 'A')): ?>
                                <li class="kt-menu__item <?php if ($this->uri->segment(1) == "opname") { echo "kt-menu__item--active"; } ?>" aria-haspopup="true"><a href="<?php  echo site_url('opname')?>" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Stok Opname</span></a></li>
                                <?php endif; ?>                                
                                <?php if($this->system->check_access('product_usage', 'A')): ?>
                                <li class="kt-menu__item <?php if ($this->uri->segment(1) == "product_usage") { echo "kt-menu__item--active"; } ?>" aria-haspopup="true"><a href="<?php echo site_url('product_usage'); ?>" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Pemakaian</span></a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
					<?php $modules = $this->crud->get_where('module', ['category' => 4])->result_array(); $res = 0;
					foreach($modules AS $module)
					{
						if($this->system->check_access($module['url'], 'A'))
						{
							$res++;
						}
					}
					if($res > 0): ?>
                    <li class="kt-menu__section ">
                        <h4 class="kt-menu__section-text">KEUANGAN & AKUNTANSI</h4>
                        <i class="kt-menu__section-icon flaticon-more-v2"></i>
                    </li>
					<?php endif; ?>
                    <li class="kt-menu__item kt-menu__item--submenu <?php if ($this->uri->segment(1) == "cash_ledger" && in_array($this->uri->segment(2), ['cash', 'bank'])) { echo "kt-menu__item--open kt-menu__item--here"; } ?>" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                        <a href="javascript:void(0)" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                        <i class="fa fa-money-check-alt"></i>
                        </span>
                        <span class="kt-menu__link-text">Kas/Bank/Deposit</span><i class="kt-menu__ver-arrow la la-angle-right"></i>
                        </a>
                        <div class="kt-menu__submenu" kt-hidden-height="160" >
                            <span class="kt-menu__arrow"></span>
                            <ul class="kt-menu__subnav">
                                <?php if($this->system->check_access('cash_ledger/cash', 'A')): ?>
                                <li class="kt-menu__item <?php if ($this->uri->segment(1) == "cash_ledger" && $this->uri->segment(2) == "cash") { echo "kt-menu__item--active"; } ?>" aria-haspopup="true"><a href="<?php echo site_url('cash_ledger/cash'); ?>" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Buku Kas</span></a></li>
                                <?php endif; ?>
                                <?php if($this->system->check_access('cash_ledger/bank', 'A')): ?>
                                <li class="kt-menu__item <?php if ($this->uri->segment(1) == "cash_ledger" && $this->uri->segment(2) == "bank") { echo "kt-menu__item--active"; } ?>" aria-haspopup="true"><a href="<?php echo site_url('cash_ledger/bank'); ?>" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Buku Bank</span></a></li>
                                <?php endif; ?>
                                <?php if($this->system->check_access('cash_ledger/supplier_deposit', 'A')): ?>
                                <li class="kt-menu__item <?php if ($this->uri->segment(1) == "finance" && $this->uri->segment(2) == "cash_ledger" && $this->uri->segment(3) == "supplier_deposit") { echo "kt-menu__item--active"; } ?>" aria-haspopup="true"><a href="<?php echo site_url('finance/cash_ledger/supplier_deposit'); ?>" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Deposit Supplier</span></a></li>
                                <?php endif; ?>
                                <?php if($this->system->check_access('cash_ledger/customer_deposit', 'A')): ?>
                                <li class="kt-menu__item <?php if ($this->uri->segment(1) == "finance" && $this->uri->segment(2) == "cash_ledger" && $this->uri->segment(3) == "customer_deposit") { echo "kt-menu__item--active"; } ?>" aria-haspopup="true"><a href="<?php echo site_url('finance/cash_ledger/customer_deposit'); ?>" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Deposit Pelanggan</span></a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                    <li class="kt-menu__item kt-menu__item--submenu" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                        <a href="javascript:void(0)" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                        <i class="fa fa-money-check-alt"></i>
                        </span>
                        <span class="kt-menu__link-text">Pembayaran</span><i class="kt-menu__ver-arrow la la-angle-right"></i>
                        </a>
                        <div class="kt-menu__submenu" kt-hidden-height="160" >
                            <span class="kt-menu__arrow"></span>
                            <ul class="kt-menu__subnav">
                                <li class="kt-menu__item <?php if ($this->uri->segment(1) == "payment" && $this->uri->segment(2) == "debt") { echo "kt-menu__item--active"; } ?>" aria-haspopup="true"><a href="<?php echo site_url('payment/debt'); ?>" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Pembayaran Pembelian</span></a></li>
                                <li class="kt-menu__item <?php if ($this->uri->segment(1) == "payment" && $this->uri->segment(2) == "receivable") { echo "kt-menu__item--active"; } ?>" aria-haspopup="true"><a href="<?php echo site_url('payment/receivable'); ?>" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Pembayaran Penjualan</span></a></li>
                            </ul>
                        </div>
                    </li>
                    <?php if($this->system->check_access('expense', 'A')): ?>               
                    <li class="kt-menu__item " aria-haspopup="true">
                        <a href="<?php echo site_url('finance/expense'); ?>"class="kt-menu__link ">
                        <span class="kt-menu__link-icon">
                        <i class="fa fa-money-bill"></i>
                        </span>
                        <span class="kt-menu__link-text">Biaya</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="kt-menu__item kt-menu__item--submenu <?php if (in_array($this->uri->segment(1), ['general_ledger', 'journal'])) { echo "kt-menu__item--open kt-menu__item--here"; } ?>" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                        <a href="javascript:void(0)" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                        <i class="fa fa-book-open"></i>
                        </span>
                        <span class="kt-menu__link-text">Akuntansi</span><i class="kt-menu__ver-arrow la la-angle-right"></i>
                        </a>
                        <div class="kt-menu__submenu" kt-hidden-height="160" >
                            <span class="kt-menu__arrow"></span>
                            <ul class="kt-menu__subnav">
                                <li class="kt-menu__item <?php if ($this->uri->segment(1) == "general_ledger") { echo "kt-menu__item--active"; } ?>" aria-haspopup="true"><a href="<?php echo site_url('general_ledger'); ?>" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Buku Besar</span></a></li>
                                <li class="kt-menu__item <?php if ($this->uri->segment(1) == "journal") { echo "kt-menu__item--active"; } ?>" aria-haspopup="true"><a href="<?php echo site_url('journal'); ?>" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Jurnal Umum</span></a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="kt-menu__section ">
                        <h4 class="kt-menu__section-text">LAPORAN</h4>
                        <i class="kt-menu__section-icon flaticon-more-v2"></i>
                    </li>
                    <li class="kt-menu__item " aria-haspopup="true">
                        <a href="<?php echo site_url('report'); ?>"class="kt-menu__link ">
                        <span class="kt-menu__link-icon">
                        <i class="fa fa-book"></i>
                        </span>
                        <span class="kt-menu__link-text">Daftar Laporan</span>
                        </a>
                    </li>    
                    <li class="kt-menu__section ">
                        <h4 class="kt-menu__section-text">LAINNYA</h4>
                        <i class="kt-menu__section-icon flaticon-more-v2"></i>
                    </li>
                    <?php  $access_user_id = [1, 3, 14, 17];
		            if(in_array($this->session->userdata('id_u'), $access_user_id)): ?>
                    <li class="kt-menu__item  <?php if ($this->uri->segment(1) == "setting") { echo "kt-menu__item--active"; } ?>" aria-haspopup="true">
                        <a href="<?php echo site_url('setting'); ?>" class="kt-menu__link ">
                        <span class="kt-menu__link-icon">
                        <i class="fa fa-cog"></i>
                        </span>
                        <span class="kt-menu__link-text">Pengaturan</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="kt-menu__item " aria-haspopup="true">
                        <a href="<?php echo site_url('logout'); ?>"class="kt-menu__link ">
                        <span class="kt-menu__link-icon">
                        <i class="fa fa-door-open"></i>
                        </span>
                        <span class="kt-menu__link-text">Keluar</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- end:: Aside Menu -->
    </div>
    <!-- end:: Aside -->