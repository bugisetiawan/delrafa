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
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Informasi | No.Transaksi <span class="font-weight-bold text-success"><?php echo $cash_ledger['invoice']; ?></span>
                        <?php 
                            $data = array('type' => 'hidden', 'class' => 'form-control', 'id' => 'cash_ledger_id', 'name' => 'cash_ledger_id',  'value' => $cash_ledger['id'], 'readonly' => 'true');
                            echo form_input($data);
                        ?>
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                            <?php $link = ($this->agent->referrer()) ? urldecode($this->agent->referrer()) : site_url('cash_ledger/in_out'); ?>
                            <a href="<?php echo $link; ?>" class="btn btn-outline-primary btn-elevate btn-icon"
                                data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Kembali">
                                <i class="fa fa-arrow-left"></i>
                            </a>
                            <button class="btn btn-icon btn-outline-danger" id="delete_cash_ledger_in_out_btn"
                                data-id="<?php echo $cash_ledger['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Hapus Kas Masuk/Keluar">
                            <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="form-group">
                    <div class="row">
                        <label class="col-md-2 col-form-label text-dark"><span class="text-danger">*</span>TGL. TRANSAKSI</label>
                        <div class="col-md-10">
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control', 'value' => date('d-m-Y', strtotime($cash_ledger['date'])), 'readonly' => 'true'); 
                                echo form_input($data);
                            ?>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <label class="col-md-2 col-form-label text-dark"><span class="text-danger">*</span>JENIS TRANSAKSI</label>
                        <div class="col-md-10">
                            <select class="form-control" readonly disabled>
                                <option value="1" <?php if($cash_ledger['method'] == 1) { echo "selected"; } ?>>MASUK</option>
                                <option value="2" <?php if($cash_ledger['method'] == 2) { echo "selected"; } ?>>KELUAR</option>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <label class="col-md-2 col-form-label text-dark"><span class="text-danger">*</span>AKUN</label>
                        <div class="col-md-2">
                            <select class="form-control" readonly disabled>
                                <option value="1"<?php if($cash_ledger['cl_type'] == 1) { echo "selected"; } ?>>KAS</option>
                                <option value="2"<?php if($cash_ledger['cl_type'] == 2) { echo "selected"; } ?>>BANK</option>
                            </select>
                        </div>
                        <div class="col-md-8">
							<?php 
								$data = array('type' => 'text', 'class' => 'form-control', 'value' => $cash_ledger['account'], 'readonly' => 'true'); 
								echo form_input($data);
                            ?>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <label class="col-md-2 col-form-label text-dark">KETERANGAN</label>
                        <div class="col-md-10">
                            <textarea class="form-control" rows="5" readonly><?php echo $cash_ledger['note']; ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <table class="table table-hover table-bordered table-sm" id="account_table">
                            <thead>
                                <tr style="text-align:center;">
                                    <th>AKUN</th>
                                    <th width="25%">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody> 
                                <?php foreach($cash_ledger_in_out AS $info): ?>
                                <tr>
                                    <td><?php echo $info['code_coa'].' | '.$info['name_coa']; ?></td>
                                    <td class="text-right"><?php echo number_format($info['amount'], 2, '.', ','); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'value' => number_format($cash_ledger['amount'], 2, '.', ','), 'required' => 'true', 'readonly' => 'true'); 
                                    echo form_input($data);
                                ?>
                            </div>
                        </div>												
					</div>
                </div>
            </div>
        </div>        
    </div>
    <!-- end:: Content -->
    <!--begin::Verify Module Password Modal-->
    <div class="modal fade" id="verify_module_password_modal" tabindex="-1" role="dialog" aria-labelledby="closeModal" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <?php echo form_open('', array('class' => 'form-horizontal', 'id' => 'verify_module_password_form', 'autocomplete' => 'off')); ?>
                <input type="hidden" id="module_url" name="module_url"> <input type="hidden" id="action_module" name="action">
                <div class="modal-header">
                    <h5 class="modal-title">Verifikasi Password</h5>                    
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <span class="text-dark" id="veryfy_message"></span>
                        <input type="password" name="verifypassword" id="verifypassword" class="form-control" placeholder="Silahkan isi Password untuk melanjutkan..." autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer">																					
                    <button type="button" class="btn btn-danger form-control" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
                    <button type="submit" class="btn btn-success form-control"><i class="fa fa-save"></i>  Verifikasi</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>    
    <!--end::End Verify Module Password Modal-->
</div>