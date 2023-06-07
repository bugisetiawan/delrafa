<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Keuangan</b></h3>
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
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                        <i class="kt-font-brand fa fa-clipboard-list"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">Daftar Transaksi: <b><?php echo date('d-m-Y',strtotime(format_date(date('Y-m-d')) . "-7 days")); ?></b> s.d. <b><?php echo date('d-m-Y'); ?></b></h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                            <a href="javascript: void(0);" onclick="$('#datatable').DataTable().ajax.reload();" class="btn btn-square btn-brand btn-elevate btn-elevate-air">
                                <i class="la la-refresh"></i>
                                <span class="d-none d-sm-inline"> Refresh Data</span>
                            </a>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-square btn-warning btn-elevate btn-elevate-air dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="la la-align-justify"></i>
								    <span class="d-none d-sm-inline">Lainnya</span>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <a class="dropdown-item kt-font-dark" href="<?php echo site_url('cash_ledger/cash') ?>">-Buku Kas</a>
                                    <a class="dropdown-item kt-font-dark" href="<?php echo site_url('cash_ledger/bank') ?>">-Buku Bank</a>
                                </div>
                            </div>
                            <a href="<?php echo site_url('cash_ledger/mutation/create'); ?>" class="btn btn-square btn-success btn-elevate btn-elevate-air">
                                <i class="la la-plus"></i>
                                <span class="d-none d-sm-inline">Transaksi Baru</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">                
                <table class="table table-striped- table-bordered table-hover table-checkable" id="datatable">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">NO.</th>
                            <th class="text-center" width="10%">AKUN</th>
                            <th class="text-center" width="10%">TANGGAL</th>
                            <th class="text-center text-dark" width="10%">NO.TRANSAKSI</th>
                            <th class="text-center">DESKRIPSI</th>
                            <th class="text-center" class="text-center" width="10%">MASUK/KELUAR</th>
                            <th class="text-dark text-center" width="15%">TOTAL</th>
                        </tr>
                    </thead>   
                    <tbody></tbody>             
                </table>
            </div>             
        </div>
    </div>
    <!-- end:: Content -->
</div>