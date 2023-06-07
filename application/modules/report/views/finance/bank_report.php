<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Keuangan</b></h3>
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
                                    <a class="dropdown-item text-dark" href="javascript:void(0);" id="print_bank_report">-Cetak Laporan Bank</a>
                                </div>
                            </div>
                            <a href="javascript: void(0);" onclick="$('#datatable').DataTable().ajax.reload(); total_bank();" class="btn btn-square btn-brand btn-elevate btn-elevate-air">
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
                    <div class="col-md-3">                            
                        <label class="text-dark">PERIODE TRANSAKSI</label>
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
                    <div class="col-md-3">                            
                        <label class="text-dark">HINGGA</label>
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
                    <div class="col-md-6">                            
                        <label class="text-dark">AKUN</label>
                        <select class="form-control" name="account_id" id="account_id"></select>
                        <p class="col-form-label text-danger font-weight-bold" id="account_id_notify">*Pilih 1 (satu) Akun untuk melihat riwayat transaksi</p>
                    </div>
                </div>    
                <?php echo form_close(); ?>
                <div class="row row-no-padding row-col-separator-sm">
                    <div class="col-md-3 col-lg-3 col-xl-3">                        
                        <div class="kt-widget24">                            
                            <h5 class="kt-widget24__title text-dark font-weight-bold">
                                SALDO AWAL                                
                            </h5>
                            <hr style="height:1px;border:none;color:#333;background-color:#333;">
                            <h4 class="kt-widget24__stats kt-font-dark font-weight-bold text-right">
                                <span id="last_balance"></span>
                            </h4>
                        </div>                        
                    </div>
                    <div class="col-md-3 col-lg-3 col-xl-3">
                        <div class="kt-widget24">                                                            
                            <h5 class="kt-widget24__title text-success font-weight-bold">
                                DEBIT
                            </h5>                                                                
                            <hr style="height:1px;border:none;color:#333;background-color:#333;">                             
                            <h4 class="kt-widget24__stats text-success font-weight-bold text-right">
                                <span id="total_debit"></span>
                            </h4>                                                                                                                           
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3 col-xl-3">
                        <div class="kt-widget24">
                            <h5 class="kt-widget24__title text-danger font-weight-bold">
                                KREDIT
                            </h5>
                            <hr style="height:1px;border:none;color:#333;background-color:#333;">
                            <h4 class="kt-widget24__stats text-danger font-weight-bold text-right">
                                <span id="total_credit"></span>
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3 col-xl-3">
                        <div class="kt-widget24">
                            <h5 class="kt-widget24__title text-primary font-weight-bold">
                                SALDO AKHIR
                            </h5>                   
                            <hr style="height:1px;border:none;color:#333;background-color:#333;">
                            <h4 class="kt-widget24__stats text-primary font-weight-bold text-right">
                                <span id="end_balance"></span>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>            
        </div>
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__body">
                <table class="table table-striped- table-bordered table-hover table-checkable" id="datatable">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">NO.</th>
                            <th class="text-center" width="10%">AKUN</th>
                            <th class="text-center" width="10%">TANGGAL</th>
                            <th class="text-center text-dark" width="10%">NO.TRANSAKSI</th>
                            <th class="text-center">DESKRIPSI</th>
                            <th class="text-dark text-center" width="15%">MUTASI (<b class="text-success">D</b>/<b class="text-danger">K</b>)</th>
                            <th class="text-dark text-center" width="15%">SALDO</th>
                        </tr>
                    </thead>   
                    <tbody></tbody>
                </table>                
            </div>             
        </div>
    </div>
</div>