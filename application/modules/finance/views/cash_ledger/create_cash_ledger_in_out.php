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
                        <label class="col-md-2 col-form-label text-dark"><span class="text-danger">*</span>JENIS TRANSAKSI</label>
                        <div class="col-md-10">
                            <select class="form-control" name="method" id="method">
                                <option value="1">MASUK</option>
                                <option value="2">KELUAR</option>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <label class="col-md-2 col-form-label text-dark"><span class="text-danger">*</span>AKUN</label>
                        <div class="col-md-2">
                            <select class="form-control" name="cl_type" id="cl_type" required>
                                <option value="1">KAS</option>
                                <option value="2">BANK</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control" name="account_id" id="account_id" required></select>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <label class="col-md-2 col-form-label text-dark">KETERANGAN</label>
                        <div class="col-md-10">
                            <textarea class="form-control" name="information" id="information" rows="5" placeholder="Silahkan isi keterangan..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <table class="table table-hover table-bordered table-sm" id="account_table">
                            <thead>
                                <tr style="text-align:center;">
                                    <th><span class="text-danger">*</span>AKUN</th>
                                    <th width="25%"><span class="text-danger">*</span>TOTAL</th>
                                </tr>
                            </thead>
                            <tbody data-repeater-list="account">
                                <tr data-repeater-item style="text-align:center;">
                                    <td>
                                        <div class="typeahead">
                                            <?php  
                                                $data = array('type' => 'text', 'class' => 'form-control account_input', 'placeholder' => 'Silahkan ketik Kode/Nama Akun...', 'required' => 'true'); 
                                                echo form_input($data);
                                                ?>
                                        </div>
                                    </td>
                                    <?php 
                                        $data = array('type' => 'hidden', 'class' => 'form-control coa_account_code', 'name' => 'coa_account_code', 'value' => set_value('coa_account_code'), 'required' => 'true'); 
                                        echo form_input($data);
                                        echo form_error('coa_account_code', '<p class="text-danger">', '</p>');
                                        ?>
                                    <td>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control text-right amount', 'name' => 'amount', 'placeholder' => '0',  'value' => 0); 
                                            echo form_input($data);                                             
                                            echo form_error('amount', '<p class="text-danger">', '</p>');
                                            ?>
                                    </td>
                                    <td>
                                        <label class="col-form-label"><a data-repeater-delete href="javascript:void(0);" class="text-danger text-center kt-font-bold delete_product"><i class="fa fa-times"></i></a></label>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-sm btn-primary btn-block" id="add_account" data-repeater-create><i class="fa fa-plus"></i> Tambah Baris</button>
                    </div>
                </div>
                <div class="form-group row">
					<div class="col-md-8">
					</div>
					<div class="col-md-4">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="col-form-label text-dark font-weight-bold text-right">GRANDTOTAL</label>
                            </div>
                            <div class="col-md-8">
                                <?php
                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'grandtotal', 'name' => 'grandtotal',  'value' => 0, 'required' => 'true', 'readonly' => 'true'); 
                                    echo form_input($data);
                                    echo form_error('grandtotal', '<p class="text-danger">', '</p>');
                                ?>
                            </div>
                        </div>												
					</div>
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="<?php echo base_url('cash_ledger/in_out'); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>                                        
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