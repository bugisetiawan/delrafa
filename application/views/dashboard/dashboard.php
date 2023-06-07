<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title"><b><?php echo $this->session->userdata['company']->name; ?></b></h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>            
            <h3 class="kt-subheader__title"><b><?php echo $title; ?></b></h3>
        </div>
    </div>        
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
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
        <div class="kt-portlet">
            <div class="kt-portlet__body  kt-portlet__body--fit">
                <div class="row row-no-padding row-col-separator-xl">
                    <div class="col-md-12 col-lg-6 col-xl-3">
                        <div class="kt-widget24">
                            <div class="kt-widget24__details">
                                <div class="kt-widget24__info">
                                    <h4 class="kt-widget24__title text-dark">
                                        <b>PRODUK</b>
                                    </h4>
                                    <span class="kt-widget24__desc">
                                    Jumlah Produk Terdaftar
                                    </span>
                                </div>
                                <span class="kt-widget24__stats kt-font-brand">
                                    <b id="total_product"></b>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-6 col-xl-3">
                        <div class="kt-widget24">
                            <div class="kt-widget24__details">
                                <div class="kt-widget24__info">
                                    <h4 class="kt-widget24__title text-dark">
                                        <b>SUPPLIER</b>
                                    </h4>
                                    <span class="kt-widget24__desc">
                                    Jumlah Supplier Terdaftar
                                    </span>
                                </div>
                                <span class="kt-widget24__stats kt-font-primary">
                                    <b id="total_supplier"></b>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-6 col-xl-3">
                        <div class="kt-widget24">
                            <div class="kt-widget24__details">
                                <div class="kt-widget24__info">
                                    <h4 class="kt-widget24__title text-dark">
                                        <b>PELANGGAN</b>
                                    </h4>
                                    <span class="kt-widget24__desc">
                                        Jumlah Pelanggan Terdaftar
                                    </span>
                                </div>
                                <span class="kt-widget24__stats kt-font-primary">
                                    <b id="total_customer"></b>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-6 col-xl-3">
                        <div class="kt-widget24">
                            <div class="kt-widget24__details">
                                <div class="kt-widget24__info">
                                    <h4 class="kt-widget24__title text-dark">
                                        <b>KARYAWAN</b>
                                    </h4>
                                    <span class="kt-widget24__desc">
                                        Jumlah Karyawan Aktif
                                    </span>
                                </div>
                                <span class="kt-widget24__stats kt-font-primary">
                                    <b id="total_employee"></b>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php $access_user_id = [1, 3, 14, 17]; if(in_array($this->session->userdata('id_u'), $access_user_id)): ?>
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        <i class="flaticon-squares text-primary"></i> LOG AKTIVITAS USER
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <ul class="nav nav-pills nav-pills" role="tablist">
                        <a href="javascript: void(0);" onclick="$('#datatable_log_login_user').DataTable().ajax.reload(); $('#datatable_log_activity_user').DataTable().ajax.reload();" class="btn btn-square btn-brand btn-elevate btn-elevate-air">
                            <i class="la la-refresh"></i>
                            <span class="d-none d-sm-inline"> Refresh Data</span>
                        </a>
                    </ul>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-md-4">
                        <table class="table table-sm table-bordered table-hover" id="datatable_log_login_user">
                            <thead>
                                <tr style="text-align:center;">
                                    <th>NO</th>
                                    <th>USER</th>
                                    <th class="text-center">LOGIN TERAKHIR</th>
                                </tr>
                            </thead>
                            <tbody>                                                                                                            
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-sm table-bordered table-hover" id="datatable_log_activity_user">
                            <thead>
                                <tr style="text-align:center;">
                                    <th>NO</th>
                                    <th>KETERANGAN</th>                        
                                    <th class="text-center">USER</th>
                                    <th class="text-center">WAKTU</th>
                                </tr>
                            </thead>
                            <tbody>                                                                                                            
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <div class="kt-portlet ">
            <div class="kt-portlet__body">
                <div class="kt-list-timeline">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="kt-list-timeline__items m-2">
                                <div class="kt-list-timeline__item">
                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--primary"></span>
                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('general/low_selling_price'); ?>" class="kt-link kt-font-dark">DAFTAR HARGA JUAL PRODUK KURANG DARI/SAMA DENGAN HARGA BELI</a></span>
                                </div>
                                <div class="kt-list-timeline__item">
                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--primary"></span>
                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('general/out_stock_product'); ?>" class="kt-link kt-font-dark">DAFTAR STOK PRODUK SEGERA & SUDAH KOSONG</a></span>
                                </div>
                                <div class="kt-list-timeline__item">
                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--primary"></span>
                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('general/more_stock_product'); ?>" class="kt-link kt-font-dark">DAFTAR STOK PRODUK BERLEBIH</a></span>
                                </div>                                
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="kt-list-timeline__items m-2">
                                <div class="kt-list-timeline__item">
                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--primary"></span>
                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('general/due_purchase_invoice/'); ?>" class="kt-link kt-font-dark">DAFTAR HUTANG JATUH TEMPO</a></span>
                                </div>
                                <div class="kt-list-timeline__item">
                                    <span class="kt-list-timeline__badge kt-list-timeline__badge--primary"></span>
                                    <span class="kt-list-timeline__text"><a href="<?php echo site_url('general/due_sales_invoice/'); ?>" class="kt-link kt-font-dark">DAFTAR PIUTANG JATUH TEMPO</a></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        <i class="fa fa-clipboard-list text-primary"></i> DAFTAR HUTANG CEK/GIRO DAN PIUTANG CEK/GIRO
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <ul class="nav nav-pills nav-pills" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tab_cheque_debt" onclick="$('#datatable_cheque_debt').DataTable().ajax.reload()" role="tab">
                                CEK/GIRO HUTANG JATUH TEMPO
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab_cheque_receivable" onclick="$('#datatable_cheque_receivable').DataTable().ajax.reload()" role="tab">
                                CEK/GIRO PIUTANG JATUH TEMPO
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_cheque_debt" role="tabpanel">
                        <table class="table table-sm table-bordered table-hover" id="datatable_cheque_debt">
                            <thead>
                                <tr style="text-align:center;">
                                    <th class="notexport">NO</th>
                                    <th>TGL. PEMBAYARAN</th>
                                    <th>KODE</th>
                                    <th class="text-center">NO. CEK/GIRO</th>
                                    <th class="text-center">TOTAL</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>                                                                                                                 
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane" id="tab_cheque_receivable" role="tabpanel">
                        <table class="table table-sm table-bordered table-hover" id="datatable_cheque_receivable">
                            <thead>
                                <tr style="text-align:center;">
                                    <th class="notexport">NO</th>
                                    <th>TGL. PEMBAYARAN</th>
                                    <th>KODE</th>
                                    <th class="text-center">NO. CEK/GIRO</th>
                                    <th class="text-center">TOTAL</th>                                    
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>                                                                                                                 
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>  -->
    </div>
</div>