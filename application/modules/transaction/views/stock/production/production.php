<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Persediaan</h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>
            <h3 class="kt-subheader__title"><b><?php echo $title; ?></b></h3>
        </div>
    </div>
    <!-- end:: Content Head -->
    <!-- begin:: Content -->
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">       
        <div class="alert alert-dark" role="alert">
            <div class="alert-icon"><i class="fa fa-info-circle"></i></div>
            <div class="alert-text">
                Data yang terlampir sesuai penginputan hari ini <b>(<?php echo date('d-m-Y'); ?>)</b>. Untuk melihat data secara lengkap, silahkan ke bagian laporan.
            </div>
            <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true"><i class="la la-close"></i></span>
                </button>
            </div>
        </div>
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand fa fa-clipboard-list"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                            <a href="javascript: void(0);" onclick="$('#datatable').DataTable().ajax.reload();" class="btn btn-square btn-brand btn-elevate btn-elevate-air">
                                <i class="la la-refresh"></i>
                                <span class="d-none d-sm-inline"> Refresh Data</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
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
                <!--begin: Datatable -->
                <table class="table table-bordered table-hover table-checkable" id="datatable">
                    <thead>
                        <tr style="text-align:center;">
                            <th class="text-dark">NO.</th>
                            <th class="text-dark">TGL. PRODUKSI</th>
                            <th class="text-dark">NO.PRODUKSI</th>
                            <th class="text-dark">PRODUK</th>
                            <th class="text-dark">GUDANG</th>
                            <th class="text-dark">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>                                                                                                            
                    </tbody>
                </table>
                <!--end: Datatable -->
            </div>
        </div>
    </div>
    <!-- end:: Content -->
</div>