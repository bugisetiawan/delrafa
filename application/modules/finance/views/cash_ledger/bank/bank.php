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
                                    <a class="dropdown-item kt-font-dark" href="<?php echo site_url('cash_ledger/bank/account') ?>">-Daftar Akun Bank</a>
                                    <a class="dropdown-item kt-font-dark" href="<?php echo site_url('cash_ledger/in_out') ?>">-Masuk/Keluar</a>
                                    <a class="dropdown-item kt-font-dark" href="<?php echo site_url('cash_ledger/mutation') ?>">-Mutasi</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <?php if($this->system->check_access('cash_ledger/bank/list_of_last_balance', 'read')): ?>
                <div class="form-group">                
                    <table class="table table-striped- table-bordered table-hover table-checkable">
                        <thead>
                            <tr>
                                <th width="10" class="text-center">NO.</th>                        
                                <th class="text-center">AKUN</th>
                                <th class="text-center">SALDO TERAKHIR</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $total_last_balance_bank=0; $no=1; foreach($last_balance_bank AS $info_last_balance_bank): ?>
                            <tr>
                                <td class="text-center"><?php echo $no; ?></td>
                                <td><?php echo $info_last_balance_bank['code'].' | '.$info_last_balance_bank['name']; ?></td>
                                <?php $total_last_balance_bank = $total_last_balance_bank+$info_last_balance_bank['balance']; ?>
                                <td class="text-right"><?php echo number_format($info_last_balance_bank['balance'], 2, '.', ','); ?></td>
                            </tr>
                            <?php $no++; endforeach; ?>
                            <tr>
                                <td colspan="2" class="text-right">TOTAL SALDO BANK</td>                                
                                <td class="text-right font-weight-bold"><?php echo number_format($total_last_balance_bank, 2, '.', ','); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
                <div class="form-group row">
                    <div class="col-md-6">                        
                        <select class="form-control" id="account_id"></select>                                                            
                    </div>
                    <div class="col-md-6 text-center">
                        <div id="account_id_notify">
                            <p class="col-form-label text-danger font-weight-bold">*Pilih 1 (satu) akun untuk melihat riwayat transaksi</p>
                        </div>
                    </div>
                </div>   
                <!--begin: Datatable -->
                <table class="table table-striped- table-bordered table-hover table-checkable" id="datatable">
                    <thead>
                        <tr>
                            <th class="text-center" width="10%">NO.</th>
                            <th class="text-center" width="10%">AKUN</th>
                            <th class="text-center" width="15%">TANGGAL</th>
                            <th class="text-center">DESKRIPSI</th>
                            <th class="text-dark text-center" width="15%">MUTASI (<b class="text-success">D</b>/<b class="text-danger">K</b>)</th>
                            <th class="text-dark text-center" width="15%">SALDO</th>
                        </tr>
                    </thead>   
                    <tbody id="table_data"></tbody>             
                </table>
                <!--end: Datatable -->
            </div>             
        </div>
    </div>
    <!-- end:: Content -->
    <!--begin::Add Modal-->
    <div class="modal fade" id="add_balance_form">
        <div class="modal-dialog modal-lg" role="document">
            <?php echo form_open('finance/Cash_ledger/add', ['id'=> 'create_data', 'class' => 'kt-form', 'autocomplete' => 'off']); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Saldo</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="to_cl_type" value="1">
                    <input type="hidden" name="to_account_id" value="<?php echo $this->session->userdata('code_e'); ?>">
                    <div class="row">                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="text-dark"><span class="text-danger">*</span>Tanggal</label>
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'date', 'name' => 'date', 'placeholder' => 'Tanggal',  'value' => date('d-m-Y'), 'required' => true); 
                                    echo form_input($data);
                                ?>
                            </div>  
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-dark"><span class="text-danger">*</span>Jumlah</label>
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control clear price', 'id' => 'amount', 'name' => 'amount', 'placeholder' => 'Silahkan isi nominal yang ingin ditambahkan...',  'value' => set_value('amount'), 'required' => true); 
                                    echo form_input($data);
                                ?>
                            </div>  
                        </div>
                        <div class="col-md-4">
                            <label class="text-dark"><span class="text-danger">*</span>Jenis Penambahan</label>
                            <select id="transaction_type" name="transaction_type" class="form-control" required>
                                <option value="1">PENYETORAN/DEPOSIT</option>
                                <option value="2">MUTASI DARI KAS LAIN</option>
                            </select>
                        </div>                                            
                    </div>                                             
                    <div class="row" id="mutation_transaction">
                        <div class="col-md-4">
                            <label class="text-dark"><span class="text-danger">*</span>Keluar Kas</label>
                                <select id="from_cl_type" name="from_cl_type"  class="form-control">
                                    <option value="1">KAS BESAR</option>
                                    <option value="2">KAS KECIL</option>
                                    <option value="3">KAS BANK</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="text-dark"><span class="text-danger">*</span>Sumber Akun</label>
                                <select name="from_account_id" id="from_account_id" class="form-control">
                                </select>
                            </div>
                        </div>                                                
                    </div>                    
                    <div class="row">                        
                        <div class="col-md-12">                                                             
                            <div class="form-group">
                                <label class="text-dark">Catatan:</label>
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control clear', 'rows' => 5, 'id' => 'note', 'name' => 'note', 'placeholder' => 'Silahkan isi Catatan jika ada...',  'value' => set_value('note')); 
                                    echo form_textarea($data);
                                ?>
                                <small class="text-dark">*Kosongkan jika tidak ada catatan</small>
                            </div>                                    
                        </div>                                                        
                    </div>                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> SIMPAN</button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
    <!--end::Add Modal-->    
</div>