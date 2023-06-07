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
        <?php echo form_open_multipart('', ['class' => 'repeater', 'autocomplete'=>'off']); ?>
        <div class="alert alert-dark fade show" role="alert">
            <div class="alert-icon"><i class="flaticon-exclamation-2"></i></div>
            <div class="alert-text">Mohon Perhatian! Label yang memiliki bintang(<span class="text-danger">*</span>) wajib diisi, terima kasih.</div>
        </div>
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Informasi
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="form-group">
                    <div class="row">
                        <label class="col-md-2 col-form-label text-dark"><span class="text-danger">*</span>TGL. TRANSAKSI</label>
                        <div class="col-md-10">
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'date', 'name' => 'date',  'value' => date('d-m-Y'), 'placeholder' => 'Silahkan isi tanggal pembelian', 'required' => 'true'); 
                                echo form_input($data);                                             
                                echo form_error('date', '<p class="text-danger">', '</p>');
                                ?> 
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <label class="col-md-2 col-form-label text-dark"><span class="text-danger">*</span>DARI AKUN</label>
                        <div class="col-md-2">
                            <select class="form-control" name="from_cl_type" id="from_cl_type" required>
                                <option value="1">KAS</option>
                                <option value="2">BANK</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control" name="from_account_id" id="from_account_id" required></select>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <label class="col-md-2 col-form-label text-dark"><span class="text-danger">*</span>KE AKUN</label>
                        <div class="col-md-2">
                            <select class="form-control" name="to_cl_type" id="to_cl_type" required>
                                <option value="1">KAS</option>
                                <option value="2">BANK</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control" name="to_account_id" id="to_account_id" required></select>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <label class="col-md-2 col-form-label text-dark"><span class="text-danger">*</span>TOTAL</label>
                        <div class="col-md-10">
                            <?php
                                $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'amount', 'name' => 'amount', 'placeholder' => 'Silahkan isi nominal mutasi...',  'value' => set_value('amount'), 'required' => true); 
                                echo form_input($data);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-md-6">
                            <?php $link = ($this->agent->referrer()) ? urldecode($this->agent->referrer()) : site_url('cash_ledger/cash'); ?>
                            <a href="<?php echo $link; ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>                                        
                        </div>
                        <div class="col-md-6">
                            <button type="submit" id="btn_save" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control" disabled><i class="fa fa-save"></i> SIMPAN</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php echo form_close() ?>
    </div>
    <!-- end:: Content -->
</div>