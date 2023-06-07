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
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                            <?php $link = ($this->agent->referrer()) ? urldecode($this->agent->referrer()) : site_url('cash_ledger/mutation'); ?>
                            <a href="<?php echo $link; ?>" class="btn btn-outline-primary btn-elevate btn-icon"
                                data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Kembali">
                                <i class="fa fa-arrow-left"></i>
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-elevate btn-icon" id="delete_cash_ledger_mutation_btn"
                                data-id="<?php echo $cash_ledger['from_cl']['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Hapus Kas&Bank Mutasi">
                                <i class="la la-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <?php 
                    $data = array('type' => 'hidden', 'id' => 'cash_ledger_id', 'value' => $cash_ledger['from_cl']['id'], 'readonly' => 'true');
                    echo form_input($data);
                ?>
                <div class="form-group">
                    <div class="row">
                        <label class="col-md-2 col-form-label text-dark"><span class="text-danger">*</span>TGL. TRANSAKSI</label>
                        <div class="col-md-10">
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'date', 'name' => 'date',  'value' => date('d-m-Y', strtotime($cash_ledger['from_cl']['date'])), 'readonly' => 'true');
                                echo form_input($data);
                            ?>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <label class="col-md-2 col-form-label text-dark"><span class="text-danger">*</span>DARI AKUN</label>
                        <div class="col-md-2">
                            <select class="form-control" name="from_cl_type" id="from_cl_type" readonly disabled>
                                <option value="1" <?php if($cash_ledger['from_cl']['cl_type'] == 1){ echo "selected"; } ?>>KAS</option>
                                <option value="2" <?php if($cash_ledger['from_cl']['cl_type'] == 2){ echo "selected"; } ?>>BANK</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'date', 'name' => 'date',  'value' => $cash_ledger['from_account']['name'], 'readonly' => 'true');
                                echo form_input($data);
                            ?>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <label class="col-md-2 col-form-label text-dark"><span class="text-danger">*</span>KE AKUN</label>
                        <div class="col-md-2">
                            <select class="form-control" name="to_cl_type" id="to_cl_type" readonly disabled>
                                <option value="1" <?php if($cash_ledger['to_cl']['cl_type'] == 1){ echo "selected"; } ?>>KAS</option>
                                <option value="2" <?php if($cash_ledger['to_cl']['cl_type'] == 2){ echo "selected"; } ?>>BANK</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'date', 'name' => 'date',  'value' => $cash_ledger['to_account']['name'], 'readonly' => 'true');
                                echo form_input($data);
                            ?>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <label class="col-md-2 col-form-label text-dark"><span class="text-danger">*</span>TOTAL</label>
                        <div class="col-md-10">
                            <?php
                                $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'amount', 'name' => 'amount', 'value' => number_format($cash_ledger['from_cl']['amount'], 2, '.', ','), 'readonly' => true); 
                                echo form_input($data);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
    <!-- end:: Content -->
</div>