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
        <div class="alert alert-dark" role="alert">
            <div class="alert-icon"><i class="fa fa-info-circle"></i></div>
            <div class="alert-text">
                Data yang terlampir sesuai Bulan ini: <b>(<?php echo date('m-Y'); ?>)</b>. Untuk melihat data secara lengkap, silahkan ke bagian laporan.
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
                    <h3 class="kt-portlet__head-title"></h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                            <a href="javascript: void(0);" onclick="$('#datatable').DataTable().ajax.reload(); total();" class="btn btn-square btn-brand btn-elevate btn-elevate-air">
                                <i class="la la-refresh"></i>
                                <span class="d-none d-sm-inline"> Refresh Data</span>
                            </a>
                            <button class="btn btn-square btn-success btn-elevate btn-elevate-air" id="add_expense" data-toggle="modal" data-target="#add_expense_form">
                                <i class="la la-plus"></i>
                                <span class="d-none d-sm-inline">Pengeluaran Biaya Baru</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">                            
                <div class="row row-no-padding row-col-separator-sm">
                    <div class="col-md-12 col-lg-12 col-xl-12">
                        <div class="kt-widget24">
                            <div class="kt-widget24__details">
                                <div class="kt-widget24__info">
                                    <h4 class="kt-widget24__title text-primary font-weight-bold">
                                        TOTAL PENGELUARAN
                                    </h4>
                                </div>
                                <span class="kt-widget24__stats kt-font-primary font-weight-bold">
                                    <span id="total_grandtotal"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-striped- table-bordered table-hover table-checkable" id="datatable">
                    <thead>
                        <tr style="text-align:center;">
                            <th width="10">NO.</th>
                            <th width="80">TANGGAL</th>
                            <th>NO. TRANSAKSI</th>
                            <th>BIAYA</th>
                            <th>JUMLAH</th>
                            <th>KETERANGAN</th>
                            <th width="100">AKSI</th>
                        </tr>
                    </thead>   
                    <tbody id="table_data"></tbody>             
                </table>
            </div>             
        </div>
    </div>    
    <input type="hidden" id="cash_ledger_balance">
    <!--begin::Create Modal-->
    <div class="modal fade" id="add_expense_form">
        <div class="modal-dialog modal-lg" role="document">
            <?php echo form_open('finance/Expense/add', ['id'=> 'create_data', 'class' => 'kt-form', 'autocomplete' => 'off']); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pengeluaran Biaya</h5>
                </div>
                <div class="modal-body">                      
                    <div class="form-group row">
                        <div class="col-md-6">                            
                            <label class="text-dark"><span class="text-danger">*</span>Tanggal:</label>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'date', 'name' => 'date', 'placeholder' => 'Tanggal',  'value' => date('d-m-Y'), 'required' => 'true'); 
                                echo form_input($data);
                            ?>                            
                        </div>
                        <div class="col-md-6">                            
                            <label class="text-dark"><span class="text-danger">*</span>Biaya:</label>
                            <select name="cost_id" id="cost_id" class="form-control" required>
                            </select>                            
                        </div>
                    </div>
                    <div class="form-group row"  id="cash_ledger_form">
                        <div class="col-md-4">
                            <select class="form-control cash_ledger_input" id="from_cl_type" name="from_cl_type" required>
                                <option value="1">KAS</option>
                                <option value="2">BANK</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control cash_ledger_input" id="from_account_id" name="from_account_id" required></select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label class="text-dark"><span class="text-danger">*</span>Jumlah:</label>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control amount', 'id' => 'amount', 'name' => 'amount', 'placeholder' => 'Silahkan isi besaran biaya...',  'value' => set_value('amount'), 'required' => 'true'); 
                                echo form_input($data);
                            ?>                            
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label class="text-dark">Keterangan:</label>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control', 'rows' => 5, 'id' => 'information', 'name' => 'information', 'placeholder' => 'Silahkan isi Keterangan jika ada...',  'value' => set_value('information')); 
                                echo form_textarea($data);
                            ?>
                            <small>*Kosongkan jika tidak ada keterangan</small>
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
    <!--end::Create Modal-->
    <!--begin::Update Modal-->
    <div class="modal fade" id="update_expense_form">
        <div class="modal-dialog modal-lg" role="document">
            <?php echo form_open('finance/Expense/update', ['id'=> 'update_data', 'class' => 'kt-form', 'autocomplete' => 'off']); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pengeluaran Biaya</h5>
                </div>
                <div class="modal-body">
                    <?php 
                        $data = array('type' => 'hidden', 'id' => 'e_expense_id', 'name' => 'expense_id', 'required' => 'true'); 
                        echo form_input($data);
                    ?>
                    <div class="form-group row">
                        <div class="col-md-6">                            
                            <label class="text-dark">*Tanggal:</label>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'e_date', 'name' => 'date', 'placeholder' => 'Tanggal', 'required' => 'true'); 
                                echo form_input($data);
                            ?>
                        </div>
                        <div class="col-md-6">                            
                            <label class="text-dark">*Biaya:</label>
                            <select name="cost_id" id="e_cost_id" class="form-control" required>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row"  id="cash_ledger_form">
                        <div class="col-md-4">
                            <select class="form-control cash_ledger_input" id="e_from_cl_type" name="from_cl_type" required>
                                <option value="1">KAS BESAR</option>
                                <option value="2">KAS KECIL</option>
                                <option value="3">KAS BANK</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control cash_ledger_input" id="e_from_account_id" name="from_account_id" required></select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="text-dark">*Jumlah:</label>
                            <?php
                                $data = array('type' => 'text', 'class' => 'form-control amount', 'id' => 'e_amount', 'name' => 'amount', 'placeholder' => 'Silahkan isi besaran biaya...',  'value' => set_value('amount'), 'required' => 'true'); 
                                echo form_input($data);
                            ?>
                        </div>
                        <div class="col-md-6">                            
                            <label class="text-dark">Nomor Bukti:</label>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'e_invoice', 'name' => 'invoice', 'placeholder' => 'Silahkan isi Nomor Bukti jika ada...',  'value' => set_value('invoice')); 
                                echo form_input($data);
                            ?>
                            <small>*Kosongkan jika tidak ada nomor bukti</small>                        
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">                            
                            <label class="text-dark">Keterangan:</label>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control', 'rows' => 5, 'id' => 'e_information', 'name' => 'information', 'placeholder' => 'Silahkan isi Keterangan jika ada...',  'value' => set_value('information'));
                                echo form_textarea($data);
                            ?>
                            <small>*Kosongkan jika tidak ada keterangan</small>
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
    <!--end::Update Modal-->
</div>