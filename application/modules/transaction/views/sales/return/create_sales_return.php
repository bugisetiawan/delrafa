<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Transaksi</h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>
            <h3 class="kt-subheader__title"><b><?php echo $title; ?></b></h3>
        </div>
    </div>
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
        <?php echo form_open_multipart('', ['class' => 'repeater', 'autocomplete' => 'off']); ?>        
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
					<div class="form-group row">
						<div class="col-md-6">
							<div class="row">
								<label class="col-md-4 col-form-label text-dark"><span class="text-danger">*</span>TGL. RETUR PENJUALAN</label>
								<div class="col-md-8">												
									<?php 
										$data = array('type' => 'text', 'class' => 'form-control', 'id' => 'date', 'name' => 'date',  'value' => date('d-m-Y'), 'placeholder' => 'Silahkan isikan tanggal retur pembelian', 'required' => 'true'); 
										echo form_input($data);                                             
										echo form_error('date', '<p class="text-danger">', '</p>');
										?>  
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="row">
								<label class="col-md-3 col-form-label text-dark text-right"><span class="text-danger">*</span>JENIS RETUR</label>
								<div class="col-md-9">
									<select class="form-control" name="method" id="method">
										<option value="1">TIDAK POTONG PENJUALAN</option>
										<option value="2">POTONG PENJUALAN</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-md-6">
							<div class="row">
								<label class="col-md-4 col-form-label text-dark"><span class="text-danger">*</span>PELANGGAN</label>
								<div class="col-md-8">                                                
									<select class="form-control" name="customer_code" id="customer_code"></select>											
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="row">
								<label class="col-md-3 col-form-label text-dark text-right"><span class="text-danger">*</span>PPN</label>
								<div class="col-md-3">
									<select class="form-control" name="ppn" id="ppn">
										<option value="0">NON</option>
										<option value="1">PPN</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-md-6">
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
							<div class="row choose_invoice">
								<label class="col-md-4 col-form-label text-dark">NO. TRANSAKSI</label>
								<div class="col-md-8">
									<select class="form-control" name="sales_invoice_id" id="sales_invoice_id">
										<option value="">- PILIH PENJUALAN -</option>
									</select>
									<small class="text-dark">*Hanya transaksi <b class="text-danger">BELUM LUNAS</b> yang dapat dipotong</small>
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
							Daftar Produk
						</h3>                                            
					</div>						
				</div>
				<div class="kt-portlet__body">
					<div class="row">
						<div class="col-md-12">
							<table class="table table-sm table-checkable" id="product_table">
								<thead>
									<tr style="text-align:center;">
										<th><span class="text-danger">*</span>PRODUK</th>                                            
										<th width=85px;><span class="text-danger">*</span>QTY</th>
										<th width=100px;><span class="text-danger">*</span>SATUAN</th>
										<th width=150px;><span class="text-danger">*</span>HARGA</th>
										<th width=150px;><span class="text-danger">*</span>GUDANG</th>
										<th width=150px;>TOTAL</th>
										<th>KETERANGAN</th>
									</tr>
								</thead>
								<tbody data-repeater-list="product">
									<tr data-repeater-item style="text-align:center;">
										<td>
											<div class="typeahead">
											<?php  
												$data = array('type' => 'text', 'class' => 'form-control product_input', 'placeholder' => 'Ketik Kode/Nama Produk...', 'required' => 'true'); 
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
											$data = array('type' => 'text', 'class' => 'form-control qty', 'name' => 'qty', 'placeholder' => 'QTY',  'value' => set_value('qty'), 'required' => 'true'); 
											echo form_input($data);                                             
											echo form_error('qty', '<p class="text-danger">', '</p>');
										?>
										</td>												
										<td>
											<select class="form-control unit" name="unit_id" required>
											</select>
										</td>
										<td>
											<?php
												$data = array('type' => 'text', 'class' => 'form-control text-right price', 'name' => 'price',  'value' => set_value('price'), 'required' => 'true'); 
												echo form_input($data);                                             
												echo form_error('price', '<p class="text-danger">', '</p>');
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
										<?php 
											$data = array('type' => 'text', 'class' => 'form-control', 'id' => 'information', 'name' => 'information',  'value' => set_value('information'), 'placeholder' => 'Keterangan Retur'); 
											echo form_input($data);                                             
											echo form_error('information', '<p class="text-danger">', '</p>');
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
										$data = array('type' => 'number', 'min' => 0, 'class' => 'form-control text-right', 'id' => 'total_product', 'name' => 'total_product',  'value' => set_value('total_product'), 'required' => 'true', 'readonly' => 'true'); 
										echo form_input($data);                                             
										echo form_error('total_product', '<p class="text-danger">', '</p>');
										?>
								</div>
								<label class="col-md-3 col-form-label text-dark text-right">TOTAL KUANTITAS</label>
								<div class="col-md-3">
									<?php
										$data = array('type' => 'number', 'min' => 0, 'class' => 'form-control text-right', 'id' => 'total_qty', 'name' => 'total_qty',  'value' => set_value('total_qty'), 'required' => 'true', 'readonly' => 'true'); 
										echo form_input($data);                                             
										echo form_error('total_qty', '<p class="text-danger">', '</p>');
										?>
								</div>
							</div>
						</div>
						<div class="col-md-6">

						</div>
						<div class="col-md-6">
							<div class="form-group row">
								<label class="col-md-3 col-form-label text-dark text-right">TOTAL RETUR</label>
								<div class="col-md-9">
									<?php
										$data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_return', 'name' => 'total_return',  'value' => set_value('total_return'), 'required' => 'true', 'readonly' => 'true'); 
										echo form_input($data);                                             
										echo form_error('total_return', '<p class="text-danger">', '</p>');
										?>
								</div>
							</div>
							<div class="form-group row choose_invoice">
								<label class="col-md-3 col-form-label text-danger text-right">PIUTANG PENJUALAN</label>
								<div class="col-md-9">
									<?php
										$data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'account_payable', 'name' => 'account_payable',  'value' => set_value('account_payable'), 'readonly' => 'true'); 
										echo form_input($data);                                             
										echo form_error('account_payable', '<p class="text-danger">', '</p>');
										?>                                            
								</div>
							</div>
							<div class="form-group row choose_invoice">
								<label class="col-md-3 col-form-label text-primary text-right"><strong>SISA TAGIHAN</strong></label>
								<div class="col-md-9">
									<?php
										$data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'grandtotal', 'name' => 'grandtotal',  'value' => set_value('grandtotal'), 'readonly' => 'true'); 
										echo form_input($data);                                             
										echo form_error('grandtotal', '<p class="text-danger">', '</p>');
										?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="kt-portlet">
				<div class="kt-portlet__foot">
					<div class="row">
						<div class="col-md-6">
							<a href="<?php echo base_url('sales/return'); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>                                        
						</div>
						<div class="col-md-6">
							<button type="submit" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control" id="btn_save"><i class="fa fa-save"></i> SIMPAN</button>
						</div>
					</div>
				</div>
			</div>
        <?php echo form_close() ?>
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