<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Laporan</b></h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>            
            <h3 class="kt-subheader__title"><b><?php echo $title; ?></b></h3>
        </div>
    </div>
    <!-- end:: Content Head -->    
    <!-- begin:: Content -->
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
        <div class="kt-portlet kt-portlet--mobile" id="filter_portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                        <i class="kt-font-brand fa fa-info"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">                        
                        Informasi
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-square btn-warning btn-elevate btn-elevate-air dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-cog"></i>
								    <span class="d-none d-sm-inline">Lainnya</span>
                                </button>
                                <div class="dropdown-menu">
                                    <a href="javascript:void(0);" class="dropdown-item text-dark" id="print_purchase_invoice_report">- Cetak Laporan Daftar Pembelian</a>
                                    <a href="javascript:void(0);" class="dropdown-item text-dark" id="print_purchase_invoice_detail_report">- Cetak Laporan Detail Pembelian</a>
                                    <a href="javascript:void(0);" class="dropdown-item text-dark" id="print_purchase_invoice_daily_report">- Cetak Laporan Pembelian Harian</a>
                                </div>
                            </div>                            
                            <a href="javascript: void(0);" class="btn btn-square btn-brand btn-elevate btn-elevate-air" id="btn_refresh">
                                <i class="la la-refresh"></i>
                                <span class="d-none d-sm-inline"> Refresh Data</span>
                            </a>                                                        
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <?php echo form_open_multipart('', ['id' => 'filter_form', 'autocomplete'=>'off']); ?>
                <div class="form-group row">                
                    <div class="col-md-2">                            
                        <label class="text-dark"><span class="text-danger">*</span>PERIODE PEMBELIAN</label>
                        <div class='input-group'>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'from_date', 'name' => 'from_date', 'value' => date('d-m-Y'),  'placeholder' => 'Tanggal Awal'); 
                                echo form_input($data);
                            ?>
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="la la-calendar-check-o"></i></span>
                            </div>
                        </div>                                                                    
                    </div>
                    <div class="col-md-2">                            
                        <label class="text-dark"><span class="text-danger">*</span>HINGGA</label>
                        <div class='input-group'>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'to_date', 'name' => 'to_date', 'value' => date('d-m-Y'),  'placeholder' => 'Tanggal Akhir');
                                echo form_input($data);
                            ?>
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="la la-calendar-check-o"></i></span>
                            </div>
                        </div>                                                        
                    </div>                        
                    <div class="col-md-2">                            
                        <label class="text-dark">PEMBAYARAN</label>
                        <select class="form-control" name="payment" id="payment">
                            <option value="">- SEMUA -</option>
                            <option value="1">TUNAI</option>
                            <option value="2">KREDIT</option>
                        </select>                                                        
                    </div>
                    <div class="col-md-2">                            
                        <label class="text-dark">SUPPLIER</label>
                        <select class="form-control" name="supplier_code" id="supplier_code"></select>
                    </div>    
                    <div class="col-md-2">                            
                        <label class="text-dark">PPN</label>
                        <select class="form-control" name="ppn" id="ppn">
                            <option value="">- SEMUA -</option>
                            <option value="0">NON</option>
                            <option value="1">PPN</option>
                        </select>                                                             
                    </div>                                   
                    <div class="col-md-2">                            
                        <label class="text-dark">STATUS PEMBAYARAN</label>
                        <select class="form-control" name="payment_status" id="payment_status">
                            <option value="">- SEMUA -</option>
                            <option value="1">LUNAS</option>
                            <option value="2">BELUM LUNAS</option>
                            <option value="3">JATUH TEMPO</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <label class="text-dark">PENCARIAN PRODUK</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="la la-search kt-font-brand"></i></span></div>
                            <input type="text" class="form-control" name="search_product" id="search_product" placeholder="Silahkan isi Nama Produk untuk melakukan pencarian..." autocomplete="off">                            
                        </div>
                        <small class="kt-font-dark">*tekan tombol <b>ENTER</b> didalam kotak pencarian untuk melakukan pencarian. Terima kasih</small>
                    </div> 
                </div>    
                <?php echo form_close(); ?>
                <?php if($this->session->userdata('id_u') <= 3 ): ?>
                <div class="row row-no-padding row-col-separator-sm">
                    <div class="col-md-6 col-lg-6 col-xl-6">                        
                        <div class="kt-widget24">
                            <div class="kt-widget24__details">
                                <div class="kt-widget24__info">
                                    <h4 class="kt-widget24__title text-danger font-weight-bold">
                                        TOTAL HUTANG (SISA TAGIHAN)
                                    </h4>
                                </div>
                                <span class="kt-widget24__stats kt-font-danger font-weight-bold">
                                    <span id="total_account_payable"></span>
                                </span>
                            </div>
                        </div>                        
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-6">
                        <div class="kt-widget24">
                            <div class="kt-widget24__details">
                                <div class="kt-widget24__info">
                                    <h4 class="kt-widget24__title text-primary font-weight-bold">
                                        TOTAL PEMBELIAN
                                    </h4>
                                </div>
                                <span class="kt-widget24__stats kt-font-primary font-weight-bold">
                                    <span id="total_grandtotal"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>            
        </div>
        <?php if($this->system->check_access('chart/purchase_invoice', 'read')): ?>
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-icon">
                        <i class="kt-font-brand fa fa-chart-bar"></i>
                        </span>
                        <h3 class="kt-portlet__head-title">Grafik</h3>
                    </div>
                    <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-wrapper">
                            <select class="form-control" id="view_type">
                                <option value="day" selected>HARIAN</option>
                                <option value="month">BULANAN</option>
                                <option value="year">TAHUNAN</option>                            
                            </select> 
                        </div>
                    </div>
                </div>
                <div class="kt-portlet__body">                             
                    <div id="chart" style="height:250px;"></div>
                </div>
            </div>
        <?php endif; ?>        
        <div class="kt-portlet kt-portlet--mobile">            
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-md-12">	
                        <!--begin: Datatable -->
                        <table class="table table-bordered table-hover" id="purchase_invoice_datatable">
                            <thead>
                                <tr>
                                    <th class="notexport">NO.</th>
                                    <th class="text-center">TANGGAL</th>
                                    <th class="text-center">NO. TRANSAKSI</th>                            
                                    <th class="text-center">NO. REFRENSI</th>
                                    <th class="text-center">PEMBAYARAN</th>
                                    <th class="text-center">JATUH TEMPO</th>  
                                    <th class="text-center">TOTAL</th>
                                    <th class="text-center">HUTANG</th>
                                    <th class="text-center">SUPPLIER</th>
                                    <th class="text-center">PPN</th>
                                    <th class="text-center">STATUS</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>                                                                                                                 
                            </tbody>
                        </table>
                        <!--end: Datatable -->									
                    </div>
                </div>
            </div>            
        </div>        
    </div>
    <!-- end:: Content -->    
</div>