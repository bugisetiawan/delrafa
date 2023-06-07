<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Transaksi</b></h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>   
            <h3 class="kt-subheader__title"><b><?php echo $title; ?></b></h3>
        </div>
    </div>    
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
		<?php echo form_open_multipart('', ['id' => 'create_sales_invoice_form', 'class' => 'repeater', 'autocomplete'=>'off']); ?>
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
				<div class="row">
					<div class="col-md-5">
						<div class="form-group row">
							<label class="col-md-3 col-form-label text-dark"><span class="text-danger">*</span>TGL. PENJUALAN</label>
							<div class="col-md-9">
								<?php 
									$data = array('type' => 'text', 'class' => 'form-control', 'id' => 'date', 'name' => 'date',  'value' => date('d-m-Y'), 'placeholder' => 'Silahkan isi tanggal pembelian', 'required' => 'true'); 
									echo form_input($data);                                             
									echo form_error('date', '<p class="text-danger">', '</p>');
									?> 
							</div>
						</div>
					</div>
					<div class="col-md-7">
						<div class="form-group row">
							<label class="col-md-3 col-form-label text-dark text-right"><span class="text-danger">*</span>SALES</label>
							<div class="col-md-9">
								<select class="form-control" name="sales_code" id="sales_code"></select>                                        
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-5">
						<div class="form-group row">
							<label class="col-md-3 col-form-label text-dark"><span class="text-danger">*</span>PELANGGAN</label>                                            
							<div class="col-md-9">
								<select class="form-control" name="customer_code" id="customer_code"></select>
							</div>
						</div>
					</div>
					<div class="col-md-7">
						<div class="form-group row">
							<label class="col-md-3 col-form-label text-dark text-right"><span class="text-danger">*</span>PEMBAYARAN</label>
							<div class="col-md-2">
								<select class="form-control" name="payment" id="payment" required disabled>
									<option value="1" selected>TUNAI</option>
									<option value="2">KREDIT</option>
								</select>
							</div>
							<label class="col-md-2 col-form-label text-dark text-right"><span class="text-danger">*</span>TEMPO (Hari)</label>
							<div class="col-md-2">
								<?php 
									$data = array('type' => 'number', 'min'=> 0, 'class' => 'form-control', 'id' => 'payment_due', 'name' => 'payment_due',  'value' => set_value('payment_due'), 'placeholder' =>'0', 'required' => 'true', 'readonly' => 'true');
									echo form_input($data);                                             
									echo form_error('payment_due', '<p class="text-danger">', '</p>');
									?>
							</div>
							<label class="col-md-1 col-form-label text-dark text-right"><span class="text-danger">*</span>PPN</label>
							<div class="col-md-2">
								<select class="form-control" name="ppn" id="ppn">
									<option value="0">NON</option>
									<option value="1">PPN</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-5">
						<div class="form-group row">
							<label class="col-md-3 col-form-label text-dark">KETERANGAN</label>
							<div class="col-md-9">
								<textarea class="form-control" rows="3" name="information" id="information" placeholder="Silahkan isi keterangan bila ada..."></textarea>
							</div>
						</div>
					</div>
					<div class="col-md-6">
					</div>
				</div>
			</div>
		</div>
		<div class="kt-portlet" id="product-table">
			<div class="kt-portlet__head">
				<div class="kt-portlet__head-label">
					<h3 class="kt-portlet__head-title">
						Daftar Produk
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
				<div class="row">
					<div class="col-md-12">
						<table class="table table-sm" id="product_table">
							<thead>
								<tr style="text-align:center;">
									<th><span class="text-danger">*</span>PRODUK</th>
									<th width="100px"><span class="text-danger">*</span>QTY</th>
									<th width="100px"><span class="text-danger">*</span>SATUAN</th>
									<th width="150px"><span class="text-danger">*</span>HARGA</th>
									<th width="100px"><span class="text-danger">*</span>DISKON (%)</th>
									<th width="200px"><span class="text-danger">*</span>GUDANG</th>
									<th width="180px">TOTAL</th>
								</tr>
							</thead>
							<tbody data-repeater-list="product">
								<tr data-repeater-item style="text-align:center;" class="form_product_input">
									<td>
										<div class="typeahead">
											<?php  
												$data = array('type' => 'text', 'class' => 'form-control product_input', 'placeholder' => 'Silahkan ketik Kode/Nama Produk...', 'required' => 'true'); 
												echo form_input($data);
												?>
										</div>
									</td>
									<?php 
										$data = array('type' => 'hidden', 'class' => 'form-control product_code', 'name' => 'product_code', 'value' => set_value('product_code'), 'required' => 'true'); 
										echo form_input($data);
										echo form_error('product_code', '<p class="text-danger">', '</p>');
										?>
									<td>
										<?php 
											$data = array('type' => 'text', 'class' => 'form-control text-right qty', 'name' => 'qty', 'placeholder' => 'Qty',  'value' => set_value('qty'), 'required' => 'true'); 
											echo form_input($data);                                             
											echo form_error('qty', '<p class="text-danger">', '</p>');
											?>
									</td>
									<td>
										<select class="form-control unit" name="unit_id" required>
										</select>
									</td>
									<td>
										<select class="form-control text-right price" name="price" required>
										</select>
									</td>
									<td>
										<?php
											$data = array('type' => 'text', 'class' => 'form-control text-right disc_product', 'name' => 'disc_product', 'placeholder' => '0',  'value' => set_value('discount_p')); 
											echo form_input($data);                                             
											echo form_error('disc_product', '<p class="text-danger">', '</p>');
											?>
									</td>
									<td>                                                
										<select class="form-control warehouse" name="warehouse_id" required>
										</select>
									</td>
									<td>
										<?php 
											$data = array('type' => 'text', 'class' => 'form-control text-right total', 'name' => 'total',  'value' => set_value('total'), 'required' => 'true', 'readonly' => 'true'); 
											echo form_input($data);                                             
											echo form_error('total', '<p class="text-danger">', '</p>');
											?>
									</td>
									<td>
										<label class="col-form-label"><a href="javascript:void(0);" class="text-warning text-center kt-font-bold sellprice_history"><i class="fa fa-clock"></i></a></label> &nbsp;
										<label class="col-form-label"><a data-repeater-delete href="javascript:void(0);" class="text-danger text-center kt-font-bold delete_product"><i class="fa fa-times"></i></a></label>
									</td>
								</tr>
							</tbody>
						</table>
						<button type="button" class="btn btn-sm btn-primary btn-block" id="add_product" data-repeater-create><i class="fa fa-plus"></i> Tambah Baris Produk</button>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-md-6">                                        
					</div>
					<div class="col-md-6">
						<div class="form-group row">
							<label class="col-md-3 col-form-label text-dark text-right">TOTAL PRODUK</label>
							<div class="col-md-3">
								<?php
									$data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_product', 'name' => 'total_product',  'value' => set_value('total_product'), 'required' => 'true', 'readonly' => 'true'); 
									echo form_input($data);                                             
									echo form_error('total_product', '<p class="text-danger">', '</p>');
									?>
							</div>
							<label class="col-md-3 col-form-label text-dark text-right">TOTAL KUANTITAS</label>
							<div class="col-md-3">
								<?php
									$data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_qty', 'name' => 'total_qty',  'value' => set_value('total_qty'), 'required' => 'true', 'readonly' => 'true'); 
									echo form_input($data);                                             
									echo form_error('total_qty', '<p class="text-danger">', '</p>');
									?>
							</div>
						</div>
					</div>
					<div class="col-md-6"></div>
					<div class="col-md-6">
						<div class="form-group row">
							<label class="col-md-3 col-form-label text-dark text-right">SUBTOTAL</label>
							<div class="col-md-9">	
								<?php
									$data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'subtotal', 'name' => 'subtotal',  'value' => set_value('subtotal'), 'required' => 'true', 'readonly' => 'true'); 
									echo form_input($data);                                             
									echo form_error('subtotal', '<p class="text-danger">', '</p>');
									?>										
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>		
		<div class="kt-portlet">
			<div class="kt-portlet__head">
				<div class="kt-portlet__head-label">
					<h3 class="kt-portlet__head-title">
						Pembayaran | <b class="text-primary" id="payment_method">TUNAI</b>
					</h3>
				</div>
			</div>
			<div class="kt-portlet__body">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group row" id="cash_ledger_form">
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
						<div class="form-group row" id="dp_checklist_form">
							<div class="col-md-4">
							</div>
							<div class="col-md-2">
								<span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--success">
								<label>
								<input type="checkbox" id="dp_checklist" value="1">
								<span></span>
								</label>
								</span>                                                                        
							</div>
							<label class="col-md-6 col-form-label text-dark">Membayar Uang Muka Penjualan</label>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group row">
							<div class="col-md-1"></div>
							<select class="col-md-3 form-control text-right" id="discount_method">
								<option value="1">DISKON (%)</option>
								<option value="2">DISKON (Rp)</option>
							</select>
							<div class="col-md-8" id="discount_percent">
								<?php 
									$data = array('type' => 'text', 'class' => 'form-control text-right', 'name' => 'discount_p', 'id' => 'discount_p', 'placeholder' => 'Diskon (%)', 'value' => 0, 'required' => 'true'); 
									echo form_input($data);                                             
									echo form_error('discount_p', '<p class="text-danger">', '</p>');                                            
									?>
								<small>*Diskon Persen (%)</small>
							</div>
							<div class="col-md-8" id="discount_amount">
								<?php 
									$data = array('type' => 'text',  'class' => 'form-control text-right', 'name' => 'discount_rp', 'id' => 'discount_rp', 'placeholder' => 'Diskon (Rupiah)', 'value' => 0, 'required' => 'true'); 
									echo form_input($data);                                             
									echo form_error('discount_rp', '<p class="text-danger">', '</p>');
									?>
								<small>*Diskon Rupiah (Rp)</small>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-4 col-form-label text-dark text-right">Biaya Pengiriman</label>
							<div class="col-md-8">
								<?php
									$data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'delivery_cost', 'name' => 'delivery_cost',  'value' => 0, 'required' => 'true'); 
									echo form_input($data);                                             
									echo form_error('delivery_cost', '<p class="text-danger">', '</p>');
									?>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-4 col-form-label text-dark text-right"><strong>GRANDTOTAL</strong></label>
							<div class="col-md-8">
								<?php
									$data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'grandtotal', 'name' => 'grandtotal',  'value' => 0, 'required' => 'true', 'readonly' => 'true'); 
									echo form_input($data);                                             
									echo form_error('grandtotal', '<p class="text-danger">', '</p>');
									?>
							</div>
						</div>
						<div class="form-group row" id="downpayment_form">
							<label class="col-md-4 col-form-label text-dark">Uang Muka Pembayaran</label>
							<div class="col-md-8">
								<?php 
									$data = array('type' => 'text', 'class' => 'form-control text-right', 'name' => 'down_payment', 'id' => 'down_payment', 'placeholder' => 'Silahkan Isi Uang Muka Pembayaran (DP)...', 'value' => 0, 'required' => 'true');
									echo form_input($data);                                             
									echo form_error('down_payment', '<p class="text-danger">', '</p>');
									?>
							</div>
						</div>						
						<div class="form-group row" id="credit_method">
							<label class="col-md-4 col-form-label text-dark text-right">Piutang Penjualan</label>
							<div class="col-md-8">
								<?php 
									$data = array('type' => 'text', 'class' => 'form-control text-right', 'name' => 'account_payable', 'id' => 'account_payable', 'placeholder' => 'Hutang Dagang...', 'value' => 0, 'readonly' => 'true', 'required' => 'true');
									echo form_input($data);                                             
									echo form_error('account_payable', '<p class="text-danger">', '</p>');
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
							<a href="<?php echo site_url('sales/invoice'); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>                                        
						</div>
						<div class="col-md-6">
							<button type="button" id="btn_save" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control" disabled><i class="fa fa-save"></i> SIMPAN</button>
						</div>
					</div>
				</div>
			</div>
		</div>		
		<?php echo form_close() ?>        
    </div>		
	<div class="modal fade" id="verify_module_password_modal" tabindex="-1" role="dialog" aria-labelledby="closeModal" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<?php echo form_open('', array('class' => 'form-horizontal', 'id' => 'verify_module_password_form', 'autocomplete' => 'off')); ?>
				<input type="hidden" id="module_url" name="module_url"> <input type="hidden" id="action_module" name="action">
				<div class="modal-header">
					<h5 class="modal-title">Verifikasi Password</h5>
				</div>
				<div class="modal-body">
					<div id="veryfy_message">
					</div>
					<div class="form-group">
						<input type="password" name="verifypassword" id="verifypassword" class="form-control" placeholder="Silahkan isi Password untuk melanjutkan..." autocomplete="false">
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success form-control"><i class="fa fa-save"></i>  Verifikasi</button>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>	
    <div class="modal fade" id="sellprice-history-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Riwayat Harga Jual Produk (10 Transaksi Terakhir)</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="sellprice-history-modal-body">
                    </div>                                        
                </div>                
            </div>
        </div>
    </div>
</div>