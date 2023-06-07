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
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand fa fa-info"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">Informasi</h3>
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
                                    <a class="dropdown-item text-dark" href="javascript:void(0);" id="print_sales_profit_detail_report">- Cetak Laporan Detail Profitabilitas Penjualan</a>
                                </div>
                            </div>                          
                            <a href="javascript: void(0);" onclick="$('.datatable').DataTable().ajax.reload();" class="btn btn-square btn-brand btn-elevate btn-elevate-air">
                            <i class="la la-refresh"></i>
                            <span class="d-none d-sm-inline">Refresh Data</span>
                            </a>                                                        
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <?php echo form_open_multipart('', ['id' => 'filter_form', 'autocomplete'=>'off']); ?>
                <div class="form-group row">                    
                    <div class="col-md-3">
                        <label class="text-dark">PERIODE DARI</label>
                        <div class='input-group'>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'from_date', 'name' => 'from_date',  'value' => date('d-m-Y'), 'placeholder' => 'Tanggal Awal'); 
                                echo form_input($data);
                                ?>
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="la la-calendar-check-o"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="text-dark">HINGGA</label>
                        <div class='input-group'>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'to_date', 'name' => 'to_date',  'value' => date('d-m-Y'), 'placeholder' => 'Tanggal Akhir'); 
                                echo form_input($data);
                                ?>
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="la la-calendar-check-o"></i></span>
                            </div>
                        </div>
                    </div>                    
                    <div class="col-md-3">
                        <label class="text-dark text-right">PELANGGAN</label>
                        <select name="customer_code" id="customer_code" class="form-control">
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="text-dark text-right">SALES</label>
                        <select name="sales_code" id="sales_code" class="form-control">
                        </select>
                    </div>
                </div>
                <?php echo form_close(); ?>            
                <div class="row row-no-padding row-col-separator-sm">
                    <div class="col-md-3 col-lg-3 col-xl-3">                        
                        <div class="kt-widget24">                            
                            <h5 class="kt-widget24__title text-success font-weight-bold">
                                TOTAL PENJUALAN
                            </h5>
                            <hr style="height:1px;border:none;color:#333;background-color:#333;">
                            <h4 class="kt-widget24__stats kt-font-success font-weight-bold text-right">
                                <span id="total_sales"></span>
                            </h4>
                        </div>                        
                    </div>
                    <div class="col-md-3 col-lg-3 col-xl-3">
                        <div class="kt-widget24">                                                            
                            <h5 class="kt-widget24__title text-danger font-weight-bold">
                                TOTAL RETUR PENJUALAN
                            </h5>                                                                
                            <hr style="height:1px;border:none;color:#333;background-color:#333;">                             
                            <h4 class="kt-widget24__stats kt-font-danger font-weight-bold text-right">
                                <span id="total_sales_return"></span>
                            </h4>                                                                                                                           
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3 col-xl-3">
                        <div class="kt-widget24">
                            <h5 class="kt-widget24__title text-danger font-weight-bold">
                                TOTAL HPP
                            </h5>
                            <hr style="height:1px;border:none;color:#333;background-color:#333;">
                            <h4 class="kt-widget24__stats kt-font-danger font-weight-bold text-right">
                                <span id="total_hpp"></span>
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3 col-xl-3">
                        <div class="kt-widget24">
                            <h5 class="kt-widget24__title text-primary font-weight-bold">
                                LABA
                            </h5>                   
                            <hr style="height:1px;border:none;color:#333;background-color:#333;">
                            <h4 class="kt-widget24__stats kt-font-primary font-weight-bold text-right">
                                <span id="total_profit"></span>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet kt-portlet--mobile">       
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand fa fa-clipboard-list"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">Daftar Penjualan</h3>
                </div>
            </div>     
            <div class="kt-portlet__body">
                <table class="table table-bordered table-hover datatable" id="datatable_sales_invoice">
                    <thead>
                        <tr class="text-center">
                            <th class="notexport">NO.</th>
                            <th>TGL. TRANSAKSI</th>
                            <th>NO. TRANSAKSI</th>
                            <th class="text-center">TOTAL</th>
                            <th class="text-center">HPP</th>
                            <th class="text-center">LABA</th>
                            <th class="text-center">(%)</th>
                            <th>PELANGGAN</th>
                            <th>SALES</th>                            
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody> 
                </table>      							                                    
            </div>            
        </div>
        <div class="kt-portlet kt-portlet--mobile">       
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand fa fa-clipboard-list"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">Daftar Retur Penjualan</h3>
                </div>
            </div>     
            <div class="kt-portlet__body">
                <table class="table table-bordered table-hover datatable" id="datatable_sales_return">
                    <thead>
                        <tr class="text-center">
                            <th class="notexport">NO</th>
                            <th>TGL. RETUR</th>
                            <th>NO. TRANSAKSI</th>
                            <th>NO. FAKTUR</th>
                            <th>TOTAL PRODUK</th>
                            <th class="text-center">TOTAL</th>
                            <th>CUSTOMER</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>   							                                    
            </div>            
        </div>
    </div>
    <!-- end:: Content -->    
</div>