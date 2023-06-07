<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Keuangan</h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>
            <h3 class="kt-subheader__title"><b><?php echo $title; ?></b></h3>
        </div>
    </div>
    <!-- end:: Content Head -->        
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
                        Informasi | No.Transaksi <b class="text-success"><?php echo $pod['code']; ?></b> | Opt. <span id="font-weight-bold"><?php echo $pod['name_e']; ?></span>
						<input type="hidden" id="pod_id" value=<?php echo encrypt_custom($pod['id']); ?>>
                    </h3>
                </div>
				<div class="kt-portlet__head-toolbar">
					<div class="kt-portlet__head-wrapper">
						<div class="kt-portlet__head-actions">
							<?php $link = ($this->agent->referrer()) ? urldecode($this->agent->referrer()) : site_url('report/finance/payment_of_debt'); ?>
                            <a href="<?php echo $link; ?>" class="btn btn-outline-primary btn-elevate btn-icon"
                                data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Kembali">
                                <i class="fa fa-arrow-left"></i>
                            </a>
							<!-- <button type="button"  class="btn btn-outline-warning btn-elevate btn-icon" id="update_pod_btn"
								data-link="<?php echo site_url('payment/debt/update/'. encrypt_custom($pod['id'])); ?>"  data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Perbarui Pembayaran">
								<i class="fa fa-edit"></i>
							</button> -->
							<button type="button" class="btn btn-outline-danger btn-elevate btn-icon" id="delete_pod_btn"
								data-id="<?php echo $pod['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Hapus Pembayaran">
							<i class="la la-trash"></i>
							</button>							
						</div>
					</div>
				</div>
            </div>
            <div class="kt-portlet__body">
                <div class="form-group row">
                    <div class="col-md-6">
                        <div class="row">
                            <label class="col-md-3 col-form-label text-dark">TGL. PEMBAYARAN</label>
                            <div class="col-md-9">
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control', 'value' => date('d-m-Y', strtotime($pod['date'])), 'readonly' => 'true'); 
                                    echo form_input($data);                                                                                 
								?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <label class="col-md-3 col-form-label text-dark text-right">SUPPLIER</label>                                            
                            <div class="col-md-9">
								<?php 
                                    $data = array('type' => 'text', 'class' => 'form-control', 'value' => $pod_transaction[0]['supplier'], 'readonly' => 'true'); 
                                    echo form_input($data);                                                                                 
								?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-6">
                        <div class="row">
                            <label class="col-md-3 col-form-label kt-font-dark">KETERANGAN</label>
                            <div class="col-md-9">
                                <textarea class="form-control" rows="3" readonly><?php echo $pod['information']; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet">
            <div class="kt-portlet__body">
                <h5 class="text-dark">Daftar Transaksi</h5>                
                <table class="table table-sm table-bordered table-hover" id="datatable_transaction">
                    <thead>
                        <tr style="text-align:center;">
                            <th width="5">NO.</th>
                            <th>TANGGAL</th>
                            <th>NO. TRANSAKSI</th>
                            <th>NO. REFRENSI</th>
                            <th>POTONGAN</th>
                            <th>BAYAR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach($pod_transaction AS $info_pod_transaction): ?>
                        <tr>
                            <td class="text-dark"><?php echo $no; ?></td>
                            <td class="text-dark text-center"><?php echo $info_pod_transaction['date']; ?></td>
                            <td class="text-dark text-center"><?php echo $info_pod_transaction['code_pi']; ?></td>                            
                            <td class="text-dark text-left"><?php echo $info_pod_transaction['invoice']; ?></td>
                            <td class="text-dark text-right"><?php echo $info_pod_transaction['disc_rp']; ?></td>
                            <td class="text-primary text-right"><?php echo $info_pod_transaction['amount']; ?></td>
                        </tr>
                        <?php $no++; endforeach; ?>
                        <tr>
                            <td colspan="5" class="text-right">BIAYA LAINNYA</td>
                            <td class="text-primary text-right"><?php echo $pod['cost']; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="kt-portlet">
            <div class="kt-portlet__body">
                <h5 class=text-dark>Daftar Pembayaran</h5>                
                <table class="table table-sm table-bordered table-hover" id="datatable_payment">
                    <thead>
                        <tr style="text-align:center;">
                            <th width="10%">METODE</th>
                            <th width="15%">AKUN</th>
                            <th>NO. CEK/GIRO</th>
                            <th>TGL. BUKA</th>
                            <th>TGL. CAIR</th>
                            <th>TGL. LUNAS</th>
                            <th width="25%">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pod_detail AS $info_pod_detail): ?>
                        <tr>		
                            <td class="text-center"><?php echo $info_pod_detail['method']; ?></td>
                            <td class="text-center"><?php echo $info_pod_detail['account_id']; ?></td>
                            <td class="text-center"><?php echo $info_pod_detail['cheque_number']; ?></td>
                            <td class="text-center"><?php echo $info_pod_detail['cheque_open_date']; ?></td>
                            <td class="text-center">
                                <?php echo $info_pod_detail['cheque_close_date']; ?>
                                <?php if($info_pod_detail['method'] == "CEK/GIRO" && $info_pod_detail['cheque_status'] == 2): ?>
                                    <span class="kt-font-bold text-warning cheque_acquittance_btn" data-toggle="modal" data-target="#cheque_acquittance_modal" data-id="<?php echo $info_pod_detail['id']; ?>" data-chequenumbertitle="<?php echo $info_pod_detail['cheque_number']; ?>"
                                    data-container="body" data-toggle="kt-tooltip" data-placement="right" data-skin="dark" title="Konfirmasi Cek/Giro"><i class="fa fa-cog"></i></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?php echo $info_pod_detail['cheque_acquittance_date']; ?></td>
                            <td class="text-primary text-right"><?php echo $info_pod_detail['amount']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="kt-portlet">
            <div class="kt-portlet__body">
                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="text-dark font-weight-bold">TOTAL TRANSAKSI</label>
                        <?php
                            $data = array('type' => 'text', 'class' => 'form-control text-right', 'value' => number_format($pod['grandtotal'], 2, '.' , ','), 'readonly' => 'true'); 
                            echo form_input($data);
                            ?>
                    </div>
                    <div class="col-md-6">
                        <label class="text-dark font-weight-bold">TOTAL PEMBAYARAN</label>
                        <?php
                            $data = array('type' => 'text', 'class' => 'form-control text-right', 'value' => number_format($pod['grandtotal'], 2, '.' , ','), 'readonly' => 'true');
                            echo form_input($data);
                            ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    <!--begin::Cheque Acquittance Modal-->
    <div class="modal fade" id="cheque_acquittance_modal">
        <div class="modal-dialog modal-lg" role="document">
            <?php echo form_open('finance/Payment/cheque_acquittance', ['id'=> 'cheque_acquittance_form', 'autocomplete' => 'off']); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Cek/Giro | No.Cek/Giro <span class="text-success" id="cheque_number_title"></span></h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="pl_detail_id" name="pl_detail_id">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="text-dark"><span class="text-danger">*</span>Status Cek/Giro</label>                                                                    
                            <select class="form-control" name="cheque_status">
                                <option value="1">LUNAS</option>
                                <!-- <option value="3">DITOLAK</option> -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="text-dark"><span class="text-danger">*</span>Tanggal</label>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control date', 'name' => 'cheque_acquittance_date', 'placeholder' => 'Tanggal Konfirmasi',  'value' => date('d-m-Y'), 'required' => 'true');
                                echo form_input($data);
                            ?>
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
    <!--end::Cheque Acquittance Modal-->
</div>