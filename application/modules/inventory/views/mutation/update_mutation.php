<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Persediaan</h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>   
            <h3 class="kt-subheader__title"><b><?php echo $title; ?></b></h3>         
        </div>
    </div>
    <!-- end:: Content Head -->    
    <!-- begin:: Content -->
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
        <?php echo form_open_multipart('', ['id' => 'update_form', 'class' => 'repeater','autocomplete'=>'off']); ?>
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
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
						<input type="hidden" name="mutation_id" value="<?php echo encrypt_custom($mutation['id']); ?>">
						<div class="form-group row">
							<div class="col-md-6">
								<label class="text-dark"><span class="text-danger">*</span>TGL. MUTASI</label>
								<?php 
									$data = array('type' => 'text', 'class' => 'form-control', 'id' => 'date', 'name' => 'date',  'value' => date('d-m-Y', strtotime($mutation['date'])), 'placeholder' => 'Silahkan isikan tanggal mutasi stok', 'required' => 'true'); 
									echo form_input($data);                                             
									echo form_error('date', '<p class="text-danger">', '</p>');
								?>
							</div>
							<div class="col-md-6">
								<label class="text-dark text-right"><span class="text-danger">*</span>PETUGAS</label>
								<input type="hidden" id="checker_code_update" value="<?php echo $mutation['code_c']; ?>">
								<select class="form-control" name="checker_code" id="checker_code" required>                                            
								</select>							
							</div>							
						</div>						
                    </div>
                </div>
                <!--end::Portlet-->
                <!--begin::Portlet-->
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
										<th width="150px"><span class="text-danger">*</span>GUDANG ASAL</th>
                                        <th width="150px"><span class="text-danger">*</span>GUDANG TUJUAN</th>
									</tr>
								</thead>
								<tbody data-repeater-list="product">
									<tr data-repeater-item style="text-align:center; display:none;">
										<td>
											<div class="typeahead">
												<?php  
													$data = array('type' => 'text', 'class' => 'form-control product_input', 'placeholder' => 'Silahkan ketik Kode/Nama Produk...'); 
													echo form_input($data);
													?>
											</div>
										</td>
										<?php 
											$data = array('type' => 'hidden', 'class' => 'form-control product_code', 'name' => 'product_code', 'value' => set_value('product_code')); 
											echo form_input($data);
											echo form_error('product_code', '<p class="text-danger">', '</p>');
											?>
										<td>
											<?php 
												$data = array('type' => 'text', 'class' => 'form-control text-right qty', 'name' => 'qty', 'placeholder' => 'Qty',  'value' => set_value('qty')); 
												echo form_input($data);                                             
												echo form_error('qty', '<p class="text-danger">', '</p>');
												?>
										</td>
										<td>
											<select class="form-control unit" name="unit_id">
											</select>
										</td>																				
										<td>                                                
											<select class="form-control from_warehouse" name="from_warehouse_id">
											</select>
										</td>
                                        <td>                                                
											<select class="form-control to_warehouse" name="to_warehouse_id">
											</select>
										</td>
										<td>
											<label class="col-form-label"><a data-repeater-delete href="javascript:void(0);" class="text-danger text-center kt-font-bold delete_product"><i class="fa fa-times"></i></a></label>
										</td>
									</tr>
									<?php foreach($mutation_detail AS $info): ?>
									<tr data-repeater-item style="text-align:center;">
										<td>
											<div class="typeahead">
												<?php  
													$data = array('type' => 'text', 'class' => 'form-control product_input', 'placeholder' => 'Silahkan ketik Kode/Nama Produk...', 'value' => $info['name_p'], 'required' => 'true'); 
													echo form_input($data);
													?>
											</div>
										</td>
										<?php 
											$data = array('type' => 'hidden', 'class' => 'form-control product_code', 'name' => 'product_code', 'value' => $info['code_p'], 'required' => 'true'); 
											echo form_input($data);
											echo form_error('product_code', '<p class="text-danger">', '</p>');
											?>
										<td>
											<?php 
												$data = array('type' => 'text', 'class' => 'form-control text-right qty', 'name' => 'qty', 'placeholder' => 'Qty',  'value' => $info['qty'], 'required' => 'true'); 
												echo form_input($data);                                             
												echo form_error('qty', '<p class="text-danger">', '</p>');
												?>
										</td>
										<td>
											<?php 
												$where = array(
													'product_unit.product_code'  => $info['code_p'],
													'product_unit.deleted'       => 0
												);
												$units = $this->sales->get_unit($where)->result_array();
											?>
											<select class="form-control unit" name="unit_id" required>
												<?php foreach($units AS $unit): ?>
													<option value="<?php echo $unit['id_u']; ?>" <?php if($info['id_u'] == $unit['id_u']) { echo 'selected'; } ?>><?php echo $unit['code_u']; ?></option>
												<?php endforeach; ?>
											</select>
										</td>																				
										<td>                                                
											<?php $warehouses = $this->sales->get_warehouse($info['code_p'], $info['id_u']); ?>
											<select class="form-control from_warehouse" name="from_warehouse_id" required>
												<?php foreach($warehouses As $warehouse): ?>
													<?php if($warehouse['stock'] > 0):?>
														<option value="<?php echo $warehouse['id_w']; ?>" <?php if($info['id_fw'] == $warehouse['id_w']) { echo 'selected'; } ?>><?php echo $warehouse['code_w']." | ".number_format($warehouse['stock'], 2, '.', ','); ?></option>
													<?php else: ?>
														<?php continue; ?>
													<?php endif; ?>
												<?php endforeach; ?>
											</select>
										</td>
                                        <td>                                                
										<?php $warehouses = $this->purchase->get_warehouse($info['code_p'], $info['id_u']); ?>
											<select class="form-control to_warehouse" name="to_warehouse_id" required>
												<?php foreach($warehouses As $warehouse): ?>													
													<option value="<?php echo $warehouse['id_w']; ?>" <?php if($info['id_tw'] == $warehouse['id_w']) { echo 'selected'; } ?>><?php echo $warehouse['code_w']." | ".number_format($warehouse['stock'], 2, '.', ','); ?></option>
												<?php endforeach; ?>
											</select>
										</td>
										<td>
											<label class="col-form-label"><a data-repeater-delete href="javascript:void(0);" class="text-danger text-center kt-font-bold delete_product"><i class="fa fa-times"></i></a></label>
										</td>
									</tr>
									<?php endforeach; ?>
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
								<label class="col-md-3 col-form-label kt-font-dark text-right">TOTAL PRODUK</label>
								<div class="col-md-3">
									<?php
										$data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_product', 'name' => 'total_product',  'value' => $mutation['total_product'], 'required' => 'true', 'readonly' => 'true'); 
										echo form_input($data);                                             
										echo form_error('total_product', '<p class="text-danger">', '</p>');
										?>
								</div>
								<label class="col-md-3 col-form-label kt-font-dark text-right">TOTAL KUANTITAS</label>
								<div class="col-md-3">
									<?php
										$data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_qty', 'name' => 'total_qty',  'value' => $mutation['total_qty'], 'required' => 'true', 'readonly' => 'true'); 
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
								<a href="<?php echo site_url('mutation'); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>                                        
							</div>
							<div class="col-md-6">
								<button type="button" id="btn_save" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-save"></i> SIMPAN</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--end::Portlet-->
            </div>
        </div>
		<?php echo form_close() ?>	
    </div>
    <!-- end:: Content -->
</div>