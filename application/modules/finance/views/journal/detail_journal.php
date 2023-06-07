<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Akuntansi</b></h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>   
            <h3 class="kt-subheader__title"><b><?php echo $title; ?></b></h3>
        </div>
    </div>
    <!-- end:: Content Head -->    
    <!-- begin:: Content -->
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
        <?php echo form_open_multipart('', ['id' => 'create_journal_form', 'class' => 'repeater', 'autocomplete'=>'off']); ?>
        <div class="alert alert-dark fade show" role="alert">
            <div class="alert-icon"><i class="flaticon-exclamation-2"></i></div>
            <div class="alert-text">Mohon Perhatian! Label yang memiliki bintang(<span class="text-danger">*</span>) wajib diisi, terima kasih.</div>
        </div>
        <!--begin::Portlet-->
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Informasi | No.Transaksi <span class="font-weight-bold text-success"><?php echo $journal['code']; ?></span>
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                            <?php $link = ($this->agent->referrer()) ? urldecode($this->agent->referrer()) : site_url('journal'); ?>
                            <a href="<?php echo $link; ?>" class="btn btn-outline-primary btn-elevate btn-icon"
                                data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Kembali">
                                <i class="fa fa-arrow-left"></i>
                            </a>
                            <button type="button" class="btn btn-icon btn-outline-danger" id="btn_delete_journal"
                                data-id="<?php echo encrypt_custom($journal['id']); ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Hapus Jurnal"><i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="form-group">
                    <div class="row">
                        <label class="col-md-2 col-form-label text-dark">TGL. JURNAL</label>
                        <div class="col-md-10">
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'date', 'name' => 'date',  'value' => date('d-m-Y', strtotime($journal['date'])), 'placeholder' => 'Silahkan isi tanggal pembelian', 'readonly' => true, 'disabled'=> true); 
                                echo form_input($data);                                             
                                echo form_error('date', '<p class="text-danger">', '</p>');
                                ?> 
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <label class="col-md-2 col-form-label text-dark">KETERANGAN</label>
                        <div class="col-md-10">
                            <textarea class="form-control" name="information" id="information" rows="5" placeholder="Silahkan isi keterangan..." readonly disabled><?php echo $journal['information']; ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <table class="table table-hover table-bordered table-sm" id="account_table">
                            <thead>
                                <tr style="text-align:center;">
                                    <th>AKUN</th>
                                    <th width="25%">DEBIT</th>
                                    <th width="25%">KREDIT</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($journal_detail AS $info_journal_detail): ?>
                                <tr>
                                    <td><?php echo $info_journal_detail['code_coa'].' | '.$info_journal_detail['name_coa']; ?></td>
                                    <td class="text-right"><?php echo number_format($info_journal_detail['debit'], 2, '.', ','); ?></td>
                                    <td class="text-right"><?php echo number_format($info_journal_detail['credit'], 2, '.', ','); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-group row">
					<div class="col-md-6">
						<label class="text-dark font-weight-bold">TOTAL DEBIT</label>
						<?php
							$data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_debit', 'name' => 'total_debit',  'value' => number_format($journal['total_debit'], 2, '.', ','), 'readonly' => 'true'); 
							echo form_input($data);
							echo form_error('total_debit', '<p class="text-danger">', '</p>');
						?>
					</div>
					<div class="col-md-6">
						<label class="text-dark font-weight-bold">TOTAL KREDIT</label>
						<?php
							$data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_credit', 'name' => 'total_credit',  'value' => number_format($journal['total_credit'], 2, '.', ','), 'readonly' => 'true'); 
							echo form_input($data);
							echo form_error('total_credit', '<p class="text-danger">', '</p>');
						?>
					</div>
                </div>
            </div>
        </div>
        <!--end::Portlet-->
        <?php echo form_close() ?>        
    </div>
    <!-- end:: Content -->
</div>