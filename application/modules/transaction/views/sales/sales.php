<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Transaksi</h3>
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
		<div class="alert alert-dark" role="alert">
            <div class="alert-icon"><i class="fa fa-info-circle"></i></div>
				<div class="alert-text">
					Data yang terlampir sesuai penginputan Tanggal <b><?php echo date('d-m-Y',strtotime(format_date(date('Y-m-d')) . "-7 days")); ?></b> s.d. <b><?php echo date('d-m-Y'); ?></b>. Untuk melihat data secara lengkap, silahkan ke bagian laporan
				</div>
            <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true"><i class="la la-close"></i></span>
                </button>
            </div>
        </div> 
        <div class="row">
            <div class="col-lg-12">
				<div class="kt-portlet kt-portlet--tabs">
					<div class="kt-portlet__head">
						<div class="kt-portlet__head-toolbar">
							<ul class="nav nav-tabs nav-tabs-line nav-tabs-line-primary" role="tablist">
								<li class="nav-item">
									<a class="nav-link active" data-toggle="tab" href="#sales_invoice" role="tab" aria-selected="false">
										<i class="fa fa-clipboard-list"></i> Daftar Penjualan
									</a>
                                </li>
							</ul>
						</div>
					</div>
					<div class="kt-portlet__body">
						<div class="tab-content">
                            <div class="tab-pane active" id="sales_invoice" role="tabpanel">
								<div class="row">
									<div class="col-md-3 text-left">
										<select class="form-control" id="do_status" onchange="$('#datatable_si').DataTable().ajax.reload();">
											<option value="">- SEMUA STATUS DO -</option>
											<option value="0">BELUM CETAK DO</option>
											<option value="1">SUDAH CETAK DO</option>
										</select>
									</div>
									<div class="col-md-3 text-left"></div>										
									<div class="col-md-6 text-right">
										<a href="javascript: void(0);" onclick="$('#datatable_si').DataTable().ajax.reload();" class="btn btn-brand btn-square btn-elevate btn-elevate-air">
											<i class="la la-refresh"></i>
											<span class="d-none d-sm-inline">Refresh Data</span>
										</a>
										<a href="<?php echo base_url('sales/invoice/create'); ?>" class="btn btn-success btn-square btn-elevate btn-elevate-air">
										<i class="la la-plus"></i>
											<span class="d-none d-sm-inline">Penjualan Baru</span>
										</a>
									</div>									
								</div>
								<hr>
								<div class="row">
									<div class="col-md-12">
										<!--begin: Datatable -->
										<table class="table table-bordered table-hover table-checkable" id="datatable_si">
											<thead>
												<tr style="text-align:center;">
													<th>NO.</th>
													<th>TANGGAL</th>
													<th>NO. TRANSAKSI</th>                            
													<th class="text-center">TOTAL</th>	
													<th>PELANGGAN</th>
													<th>SALES</th>    
													<th class="text-center">KETERANGAN</th>
													<th>STATUS</th>
													<th>CETAK DO</th>	
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
				</div>                
            </div>
        </div>
    </div>
    <!-- end:: Content -->
</div>