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
                        <i class="fa fa-cube text-primary"></i> DAFTAR STOK PRODUK BERLEBIH
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                </div>
            </div>
            <div class="kt-portlet__body">
                <table class="table table-sm table-bordered table-hover" id="datatable_more_stock_product">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>KODE</th>
                            <th class="text-center">NAMA</th>
                            <th class="text-center">STOK</th>
                            <th class="text-center">SATUAN</th>
                            <th class="text-center">DEPARTEMEN</th>
                            <th class="text-center">SUBDEPARTEMEN</th>
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