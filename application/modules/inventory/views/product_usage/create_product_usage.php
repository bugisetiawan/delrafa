<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">    
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Persediaan</b></h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>   
            <h3 class="kt-subheader__title"><b><?php echo $title; ?></b></h3>
        </div>
    </div>
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
		<?php echo form_open_multipart('', ['id' => 'create_form', 'class' => 'repeater', 'autocomplete'=>'off']); ?>
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
					<div class="col-md-5">
						<div class="row">
							<label class="col-md-3 col-form-label text-dark"><span class="text-danger">*</span>TGL. PEMAKAIAN</label>
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
						<div class="row">
							<label class="col-md-3 col-form-label text-dark text-right"><span class="text-danger">*</span>KETERANGAN</label>
							<div class="col-md-9">
								<textarea class="form-control" rows="3" name="information" id="information" placeholder="Wajib mengisi keterangan..." required></textarea>
							</div>
						</div>
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
				<div class="form-group row">
					<div class="col-md-12">
						<table class="table table-sm" id="product_table">
							<thead>
								<tr style="text-align:center;">
									<th><span class="text-danger">*</span>PRODUK</th>
									<th width="100px"><span class="text-danger">*</span>QTY</th>
									<th width="100px"><span class="text-danger">*</span>SATUAN</th>
									<th width="200px"><span class="text-danger">*</span>GUDANG</th>
								</tr>
							</thead>
							<tbody data-repeater-list="product">
								<tr data-repeater-item style="text-align:center;">
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
										<select class="form-control warehouse" name="warehouse_id" required>
										</select>
									</td>
									<td>
										<label class="col-form-label"><a data-repeater-delete href="javascript:void(0);" class="text-danger text-center kt-font-bold delete_product"><i class="fa fa-times"></i></a></label>
									</td>
								</tr>
							</tbody>
						</table>
						<button type="button" class="btn btn-sm btn-primary btn-block" id="add_product" data-repeater-create><i class="fa fa-plus"></i> Tambah Baris Produk</button>
					</div>
				</div>
				<div class="form-group row">
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
				</div>
			</div>
            <div class="kt-portlet__foot">
				<div class="kt-form__actions">
					<div class="row">
						<div class="col-md-6">
							<a href="<?php echo site_url('product_usage'); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>                                        
						</div>
						<div class="col-md-6">
							<button type="button" id="btn_save" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control" disabled><i class="fa fa-save"></i> SIMPAN</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php echo form_close(); ?>
    </div>
</div>