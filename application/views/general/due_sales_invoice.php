<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Umum</h3>
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
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        <i class="fa fa-cube text-primary"></i> DAFTAR PIUTANG JATUH TEMPO
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="text-dark">PELANGGAN</label>
                        <select class="form-control" name="customer_code" id="customer_code"></select>                      
                    </div>
                    <div class="col-md-6">
                        <label class="text-dark">SALES</label>
                        <select class="form-control" name="sales_code" id="sales_code"></select>                                               
                    </div>                    
                </div>
                <table class="table table-sm table-bordered table-hover" id="datatable_sales_invoice">
                    <thead>
                        <tr style="text-align:center;">
                            <th class="notexport">NO.</th>
                            <th>TGL. PENJUALAN</th>
                            <th>NO. TRANSAKSI</th>                                    
                            <th>JATUH TEMPO</th>
                            <th>TENGAT WAKTU</th>
                            <th class="text-center">TOTAL</th>
                            <th>PIUTANG</th>
                            <th>PELANGGAN</th>
                            <th>SALES</th>
                            <th>STATUS</th>
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