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
    <?php echo form_open_multipart('', ['autocomplete'=>'off']); ?>
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Portlet-->
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
                            </div>
                        </div>
                    </div>
                </div>
                <div class="kt-portlet__body">
                </div>
            </div>
            <!--end::Portlet-->
            <!--begin::Portlet-->
            <div class="kt-portlet">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            Pembayaran
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <div class="row">
                        <div class="col-md-6">
							<div class="form-group row">
								<label class="col-md-2 col-form-label text-dark">TANGGAL</label>
								<div class="col-md-10">
									<?php 
										$data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'payment_date', 'name' => 'payment_date',  'value' => date('d-m-Y'), 'placeholder' => 'Silahkan isi tanggal pembayaran', 'required' => 'true'); 
										echo form_input($data);
										echo form_error('payment_date', '<p class="text-danger">', '</p>');
										?>
								</div>
                            </div>
                            <div class="form-group row">
								<label class="col-md-2 col-form-label text-dark">METODE</label>
								<div class="col-md-10">
									<div class="kt-checkbox-inline">
										<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand text-dark">
										<input type="checkbox" class="payment" name="payment[]" value="1" <?php if(in_array('1', json_decode($pod['payment']))){ echo "checked"; } ?>> TUNAI
										<span></span>
										</label>
										<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand text-dark">
										<input type="checkbox" class="payment"name="payment[]" value="2" <?php if(in_array('2', json_decode($pod['payment']))){ echo "checked"; } ?>> TRANSFER
										<span></span>
										</label>
										<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand text-dark">
										<input type="checkbox" class="payment" name="payment[]" value="3" <?php if(in_array('3', json_decode($pod['payment']))){ echo "checked"; } ?>> CEK/GIRO
										<span></span>
										</label>
										<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand text-dark">
										<input type="checkbox" class="payment" name="payment[]" value="4" <?php if(in_array('4', json_decode($pod['payment']))){ echo "checked"; } ?>> DEPOSIT
										<span></span>
										</label>                                        
										<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand text-dark">
										<input type="checkbox" class="payment" name="payment[]" value="5" <?php if(in_array('5', json_decode($pod['payment']))){ echo "checked"; } ?>> OPER CEK/GIRO
										<span></span>
										</label>
									</div>
								</div>								
                            </div>
                        </div>
                        <div class="col-md-6">                            
                            <div id="cash-method">
                                <div class="form-group row" id="cash_ledger_form">
                                    <label class="col-md-4 col-form-label text-dark text-right">AKUN (TUNAI)</label>
                                    <div class="col-md-3">
                                        <select class="form-control cash_ledger_input" id="cash_cl_type" name="cash_cl_type">
                                            <option value="1" <?php if($pod['cash_cl_type'] == 1) { echo "selected"; } ?>>KAS BESAR</option>
                                            <option value="2" <?php if($pod['cash_cl_type'] == 2) { echo "selected"; } ?>>KAS KECIL</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="hidden" id="cash_account_id_update" value="<?php echo $pod['cash_account_id']; ?>">
                                        <select class="form-control cash_ledger_input" id="cash_account_id" name="cash_account_id"></select>
                                    </div>
                                </div>
                                <input type="hidden" id="cash_balance">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label text-dark text-right">PEMBAYARAN (TUNAI)</label>
                                    <div class="col-md-8">
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'cash', 'name' => 'cash', 'placeholder' => 'Nominal pembayaran tunai', 'value' => number_format($pod['cash'], 0, '.', ','));
                                            echo form_input($data);                                             
                                            echo form_error('cash', '<p class="text-danger">', '</p>');
                                            ?>
                                    </div>
                                </div>
                            </div>                                                        
                            <div id="transfer-method">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label text-dark text-right">AKUN (TRANSFER)</label>
                                    <div class="col-md-8">
                                        <input type="hidden" id="transfer_bank_id_update" value="<?php echo $pod['transfer_bank_id']; ?>">
                                        <select class="form-control bank_account" name="transfer_bank_id" id="transfer_bank_id"></select>
                                    </div>
                                </div>
                                <input type="hidden" id="transfer_balance">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label text-dark text-right">PEMBAYARAN (TRANSFER)</label>
                                    <div class="col-md-8">
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'transfer', 'name' => 'transfer', 'placeholder' => 'Nominal pembayaran transfer', 'value' => number_format($pod['transfer'], 0, '.', ','));
                                            echo form_input($data);                                             
                                            echo form_error('transfer', '<p class="text-danger">', '</p>');
                                            ?>
                                    </div>
                                </div>
                            </div>                            
                            <div id="cheque-method">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label text-dark text-right">AKUN (CEK/GIRO)</label>
                                    <div class="col-md-8">
                                        <input type="hidden" id="cheque_bank_id_update" value="<?php echo $pod['cheque_bank_id']; ?>">
                                        <select class="form-control bank_account" name="cheque_bank_id" id="cheque_bank_id"></select>
                                    </div>
                                </div>
                                <input type="hidden" id="cheque_balance">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label text-dark text-right">NO.CEK/GIRO</label>
                                    <div class="col-md-8">
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'cheque_number', 'name' => 'cheque_number', 'placeholder' => 'Nomor Cek/Giro', 'value' => $pod['cheque_number']); 
                                            echo form_input($data);                                             
                                            echo form_error('cheque_number', '<p class="text-danger">', '</p>');
                                            ?> 
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label text-dark text-right">TGL. BUKA</label>
                                    <div class="col-md-3">
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'cheque_open_date', 'name' => 'cheque_open_date', 'placeholder' => 'Tgl. Pembukaan...', 'value' => $pod['cheque_open_date']); 
                                            echo form_input($data);
                                            echo form_error('cheque_open_date', '<p class="text-danger">', '</p>');
                                            ?> 
                                    </div>
                                    <label class="col-md-2 col-form-label text-dark text-right">TGL. CAIR</label>
                                    <div class="col-md-3">
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'cheque_close_date', 'name' => 'cheque_close_date', 'placeholder' => 'Tgl. Pencairan...', 'value' => $pod['cheque_close_date']); 
                                            echo form_input($data);                                             
                                            echo form_error('cheque_close_date', '<p class="text-danger">', '</p>');
                                            ?> 
                                    </div>
                                </div>
                                <!-- <div class="form-group row">
                                    <label class="col-md-4 col-form-label text-dark text-right">PENERIMA CEK/GIRO</label>
                                    <div class="col-md-8">
                                        <?php 
                                            // $data = array('type' => 'text', 'class' => 'form-control contact', 'id' => 'cheque_contact', 'name' => 'cheque_contact', 'placeholder' => 'Nama orang penerima cek/giro',); 
                                            // echo form_input($data);                                             
                                            // echo form_error('cheque_contact', '<p class="text-danger">', '</p>');
                                            ?> 
                                    </div>
                                </div> -->
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label text-dark text-right">PEMBAYARAN (CEK/GIRO)</label>
                                    <div class="col-md-8">
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'cheque', 'name' => 'cheque', 'placeholder' => 'Nominal pembayaran giro', 'value' => number_format($pod['cheque'], 0, '.', ',')); 
                                            echo form_input($data);                                             
                                            echo form_error('cheque', '<p class="text-danger">', '</p>');
                                            ?> 
                                    </div>
                                </div>
                            </div>                            
							<div id="deposit-method">
								<div class="form-group row">
									<label class="col-md-4 col-form-label text-dark text-right">SALDO DEPOSIT</label>
									<div class="col-md-8">
										<?php
											$data = array('type' => 'text', 'class' => 'form-control', 'id' => 'deposit_balance', 'readonly' => 'true');
											echo form_input($data);
										?>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-md-4 col-form-label text-dark text-right">PEMBAYARAN (DEPOSIT)</label>
									<div class="col-md-8">
										<?php 
											$data = array('type' => 'text', 'class' => 'form-control', 'id' => 'deposit', 'name' => 'deposit', 'placeholder' => 'Nominal pembayaran deposit', 'value' => number_format($pod['deposit'], 0, '.', ','));
											echo form_input($data);                                             
											echo form_error('deposit', '<p class="text-danger">', '</p>');
										?>
									</div>
								</div>
							</div>                            
                            <div id="move-cheque-method">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label text-dark text-right">NO. CEK/GIRO (OPER)</label>
                                    <div class="col-md-8">
                                        <input type="hidden" id="move_cheque_number_update" value="<?php echo $pod['move_cheque_number']; ?>">
                                        <select class="form-control cheque_account" name="move_cheque_number" id="move_cheque_number"></select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label text-dark text-right">PEMBAYARAN (OPER CEK/GIRO)</label>
                                    <div class="col-md-8">
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'move_cheque', 'name' => 'move_cheque',  'placeholder' => 'Nominal pembayaran oper cek/giro', 'value' => number_format($pod['move_cheque'], 0, '.', ','), 'readonly' => 'true');
                                            echo form_input($data);                                             
                                            echo form_error('move_cheque', '<p class="text-danger">', '</p>');
                                            ?>				
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-dark text-right"><strong>GRANDTOTAL</strong></label>
                                <div class="col-md-8">
                                    <?php
                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'grandtotal', 'name' => 'grandtotal',  'value' => number_format($pod['grandtotal'], 0, '.', ','), 'required' => 'true', 'readonly' => 'true'); 
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
                                <a href="<?php echo urldecode($this->agent->referrer()); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control" id="btn-save"><i class="fa fa-save"></i> SIMPAN</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Portlet-->
            </div>
        </div>
    </div>
    <?php echo form_close() ?>
    <!-- end:: Content -->	
</div>