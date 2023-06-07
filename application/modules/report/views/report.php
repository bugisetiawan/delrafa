<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title"><b><?php echo $title; ?></b></h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>			
        </div>
    </div>
    <!-- end:: Content Head -->
    <!-- begin:: Content -->
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">        
        <div class="kt-portlet ">
            <div class="kt-portlet__body">
                <div class="accordion accordion-light  accordion-toggle-arrow" id="accordionReports">
                    <div class="card">
                        <div class="card-header" id="headingOne5">
                            <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOne5" aria-expanded="false" aria-controls="collapseOne5">
                                <i class="fa fa-shopping-cart"></i> LAPORAN PEMBELIAN
                            </div>
                        </div>
                        <div id="collapseOne5" class="collapse" aria-labelledby="headingOne5" data-parent="#accordionReports">
                            <div class="card-body">
                                <div class="kt-list-timeline">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="kt-list-timeline__items m-2">
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--success"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/purchase/purchase_invoice/'); ?>" class="kt-link kt-font-dark">DAFTAR PEMBELIAN</a></span>
                                                </div>
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--success"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/purchase/product_purchase/'); ?>" class="kt-link kt-font-dark">PEMBELIAN PER PRODUK</a></span>
                                                </div>
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--success"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/purchase/purchase_tax_invoice/'); ?>" class="kt-link kt-font-dark">FAKTUR PAJAK PEMBELIAN</a></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="kt-list-timeline__items m-2">
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--success"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/purchase/purchase_return/'); ?>" class="kt-link kt-font-dark">RETUR PEMBELIAN</a></span>
                                                </div>
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--success"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/purchase/product_purchase_return/'); ?>" class="kt-link kt-font-dark">RETUR PEMBELIAN PER PRODUK</a></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" id="headingTwo5">
                            <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseTwo5" aria-expanded="false" aria-controls="collapseTwo5">
                                <i class="fa fa-dolly"></i> LAPORAN PENJUALAN
                            </div>
                        </div>
                        <div id="collapseTwo5" class="collapse" aria-labelledby="headingTwo5" data-parent="#accordionReports">
                            <div class="card-body">
                                <div class="kt-list-timeline">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="kt-list-timeline__items m-2">
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--danger"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/sales/sales_invoice/'); ?>" class="kt-link kt-font-dark">DAFTAR PENJUALAN</a></span>
                                                </div>
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--danger"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/sales/product_sales'); ?>" class="kt-link kt-font-dark">PENJUALAN PER PRODUK</a></span>
                                                </div>
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--danger"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/sales/customer_sales'); ?>" class="kt-link kt-font-dark">PENJUALAN PER PELANGGAN</a></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="kt-list-timeline__items m-2">
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--danger"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/sales/sales_return/'); ?>" class="kt-link kt-font-dark">RETUR PENJUALAN</a></span>
                                                </div>
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--danger"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/sales/product_sales_return/'); ?>" class="kt-link kt-font-dark">RETUR PENJUALAN PER PRODUK</a></span>
                                                </div>
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--danger"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/sales/inactive_customer_sales/'); ?>" class="kt-link kt-font-dark">PENJUALAN PELANGGAN TIDAK AKTIF</a></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" id="headingFour5">
                            <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseFour5" aria-expanded="false" aria-controls="collapseFour5">
                                <i class="fa fa-dot-circle"></i> LAPORAN PERSEDIAAN
                            </div>
                        </div>
                        <div id="collapseFour5" class="collapse" aria-labelledby="headingFour5" data-parent="#accordionReports">
                            <div class="card-body">
                                <div class="kt-list-timeline">
                                    <div class="kt-list-timeline__items m-2">                                        
                                        <div class="kt-list-timeline__item">
                                            <span class="kt-list-timeline__badge kt-list-timeline__badge--warning"></span>
                                            <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/inventory/inventory_value'); ?>" class="kt-link kt-font-dark">NILAI PERSEDIAAN</a></span>
                                        </div>
                                        <div class="kt-list-timeline__item">
                                            <span class="kt-list-timeline__badge kt-list-timeline__badge--warning"></span>
                                            <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/stock/stock_card'); ?>" class="kt-link kt-font-dark">KARTU STOK</a></span>
                                        </div>
                                        <div class="kt-list-timeline__item">
                                            <span class="kt-list-timeline__badge kt-list-timeline__badge--warning"></span>
                                            <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/inventory/mutation/'); ?>" class="kt-link kt-font-dark">MUTASI</a></span>
                                        </div>                                        
                                        <div class="kt-list-timeline__item">
                                            <span class="kt-list-timeline__badge kt-list-timeline__badge--warning"></span>
                                            <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/stock/repacking/'); ?>" class="kt-link kt-font-dark">REPACKING</a></span>
                                        </div>
                                        <div class="kt-list-timeline__item">
                                            <span class="kt-list-timeline__badge kt-list-timeline__badge--warning"></span>
                                            <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/stock/stock_opname/'); ?>" class="kt-link kt-font-dark">STOK OPNAME</a></span>
                                        </div>
                                        <div class="kt-list-timeline__item">
                                            <span class="kt-list-timeline__badge kt-list-timeline__badge--warning"></span>
                                            <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/inventory/product_usage/'); ?>" class="kt-link kt-font-dark">PEMAKAIAN</a></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" id="headingThree5">
                            <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseThree5" aria-expanded="false" aria-controls="collapseThree5">
                                <i class="fa fa-money-bill"></i> LAPORAN KEUANGAN
                            </div>
                        </div>
                        <div id="collapseThree5" class="collapse" aria-labelledby="headingThree5" data-parent="#accordionReports">
                            <div class="card-body">
                                <div class="kt-list-timeline">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="kt-list-timeline__items m-2">
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--primary"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/finance/purchase_payable'); ?>" class="kt-link kt-font-dark">HUTANG PEMBELIAN</a></span>
                                                </div>
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--primary"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/finance/supplier_purchase_payable'); ?>" class="kt-link kt-font-dark">HUTANG PER SUPLIER</a></span>
                                                </div>
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--primary"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/finance/payment_of_debt/'); ?>" class="kt-link kt-font-dark">PEMBAYARAN PEMBELIAN</a></span>
                                                </div>
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--primary"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/finance/cheque_out'); ?>" class="kt-link kt-font-dark">CEK/GIRO KELUAR</a></span>
                                                </div>
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--primary"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/finance/sales_receivable'); ?>" class="kt-link kt-font-dark">PIUTANG PENJUALAN</a></span>
                                                </div>
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--primary"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/finance/customer_sales_receivable'); ?>" class="kt-link kt-font-dark">PIUTANG PER PELANGGAN</a></span>
                                                </div>
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--primary"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/finance/payment_of_receivable/'); ?>" class="kt-link kt-font-dark">PEMBAYARAN PENJUALAN</a></span>
                                                </div>
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--primary"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/finance/cheque_in'); ?>" class="kt-link kt-font-dark">CEK/GIRO MASUK</a></span>
                                                </div>
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--primary"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/finance/expense'); ?>" class="kt-link kt-font-dark">BIAYA</a></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="kt-list-timeline__items m-2">
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--primary"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/finance/cash/'); ?>" class="kt-link kt-font-dark">BUKU KAS</a></span>
                                                </div>
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--primary"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/finance/bank/'); ?>" class="kt-link kt-font-dark">BUKU BANK</a></span>
                                                </div>
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--primary"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/finance/supplier_deposit/'); ?>" class="kt-link kt-font-dark">DEPOSIT SUPPLIER</a></span>
                                                </div>
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--primary"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/finance/customer_deposit/'); ?>" class="kt-link kt-font-dark">DEPOSIT PELANGGAN</a></span>
                                                </div>
                                                <?php  $access_user_id = [1, 3, 14, 17];
		                                        if(in_array($this->session->userdata('id_u'), $access_user_id)): ?>
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--primary"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/finance/product_profit/'); ?>" class="kt-link kt-font-dark">PROFITABILITAS PER PRODUK</a></span>
                                                </div>
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--primary"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/finance/sales_profit'); ?>" class="kt-link kt-font-dark">PROFITABILITAS PER PENJUALAN</a></span>
                                                </div>
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--primary"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/finance/profit_and_loss/'); ?>" class="kt-link kt-font-dark">LABA RUGI</a></span>
                                                </div>
                                                <div class="kt-list-timeline__item">
                                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--primary"></span>
                                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('report/finance/balance_sheet/'); ?>" class="kt-link kt-font-dark">NERACA</a></span>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>
    </div>
    <!-- end:: Content -->
</div>