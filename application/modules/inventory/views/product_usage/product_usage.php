<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">    
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Persediaan</h3>
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
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand fa fa-clipboard-list"></i>
                    </span>
                    <h3 class="kt-portlet__head-title"></h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                            <a href="javascript: void(0);" class="btn btn-brand btn-square btn-elevate btn-elevate-air" onclick="$('#datatable').DataTable().ajax.reload();">
                                <i class="la la-refresh"></i>
                                <span class="d-none d-sm-inline"> Refresh Data</span>
                            </a>
                            <a href="<?php echo site_url('product_usage/create'); ?>" class="btn btn-success btn-square btn-elevate btn-elevate-air">
								<i class="la la-plus"></i>
								Pemakaian Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="form-group row">
                    <div class="col-md-3">
                        <select class="form-control" id="do_status" onchange="$('#datatable').DataTable().ajax.reload();">
                            <option value="">- SEMUA STATUS DO -</option>
                            <option value="0">BELUM CETAK DO</option>
                            <option value="1">SUDAH CETAK DO</option>
                        </select>
                    </div>
                </div>
                <table class="table table-bordered table-hover" id="datatable">
                    <thead>
                        <th class="text-center">NO.</th>
                        <th class="text-center">TANGGAL</th>
                        <th class="text-center">NO. TRANSAKSI</th>
                        <th class="text-dark text-center">TOTAL</th>
                        <th class="text-center">USER</th>
                        <th class="text-center">CETAK DO</th>
                        <th>&nbsp;</th>
                    </thead>
                    <tbody>                                                                                                            
                    </tbody>
                </table>                
            </div>
        </div>
    </div>
    <!-- end:: Content -->
</div>