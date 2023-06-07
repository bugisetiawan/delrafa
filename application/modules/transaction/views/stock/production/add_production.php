<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Stok</h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>            
            <h3 class="kt-subheader__title"><b><?php echo $title; ?></b></h3>
        </div>
    </div>
    <!-- end:: Content Head -->    
    <!-- begin:: Content -->
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
        <?php echo form_open_multipart('', ['class' => 'repeater', 'autocomplete'=>'off']); ?>
            <div class="row">
                <div class="col-lg-12"> 
                    <!--begin::Portlet-->
                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
									Informasi | Produk : <span class="kt-font-bold kt-font-success"><?php echo $product_production[0]['code_p'].' - '.$product_production[0]['name_p']; ?></span>
									<?php 
										$data = array('type' => 'hidden', 'class' => 'form-control product_code', 'name' => 'product_code', 'value' => $product_production[0]['code_p'], 'required' => 'true', 'readonly' => 'true'); 
										echo form_input($data);
									?>
                                </h3>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label kt-font-dark">TGL. PRODUKSI</label>
                                        <div class="col-md-8">
                                            <?php 
                                                $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'date', 'name' => 'date',  'value' => date('d-m-Y'), 'placeholder' => 'Silahkan isi tanggal repacking', 'required' => 'true'); 
                                                echo form_input($data);                                             
                                                echo form_error('date', '<p class="text-danger">', '</p>');
                                            ?>
                                        </div>
                                    </div>                                    
                                </div>
                                <div class="col-md-6">
									<div class="form-group row">
                                        <label class="col-md-4 col-form-label kt-font-dark text-right">GUDANG</label>
                                        <div class="col-md-8">
                                            <select name="warehouse_id" id="warehouse_id" class="form-control" required>
                                                <option value=""> PILIH GUDANG</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label kt-font-dark text-right">JUMLAH PRODUKSI</label>
                                        <div class="col-md-4">                                            
                                            <?php 
                                                $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'qty', 'name' => 'qty',  'value' => set_value('qty'), 'placeholder' => 'QTY', 'required' => 'true'); 
                                                echo form_input($data);                                             
                                                echo form_error('qty', '<p class="text-danger">', '</p>');
                                            ?>
                                        </div>                                       
                                    </div>
                                </div>
                            </div>                           
                        </div>                        
                    </div>
                    <!--end::Portlet-->                   
                    <!--begin::Portlet-->
                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    Daftar Produk Bundle                                   
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
                                    <table class="table table-sm repeater" id="product_table">
                                        <thead>
                                            <tr style="text-align:center;">
                                                <th>PRODUK</th>                                            
                                                <th width=100px;>QTY PER SET</th>
												<th width=100px;>SATUAN</th>
												<th width=150px;>HPP PER UNIT</th>
												<th width=150px;>TOTAL</th>
                                            </tr>
                                        </thead>
                                        <tbody data-repeater-list="product">
											<?php $subtotal_hpp = 0; foreach($product_production AS $info): ?>
                                            <tr data-repeater-item style="text-align:center;">
                                                <td>
                                                    <div class="typeahead">
                                                    <?php  
                                                        $data = array('type' => 'text', 'class' => 'form-control product_input', 'value' => $info['name_b'], 'readonly' => 'true'); 
                                                        echo form_input($data);
                                                    ?>
                                                    </div>
                                                </td>     
                                                    <?php 
                                                        $data = array('type' => 'hidden', 'class' => 'form-control product_code', 'name' => 'product_code', 'value' => $info['code_b'], 'required' => 'true', 'readonly' => 'true'); 
                                                        echo form_input($data);
                                                        echo form_error('product_code', '<p class="text-danger">', '</p>');
                                                    ?>
                                                <td>
                                                    <?php 
                                                        $data = array('type' => 'text', 'class' => 'form-control qty text-right', 'name' => 'qty', 'placeholder' => 'QTY',  'value' => $info['qty_b'], 'requried' => 'true', 'readonly' => 'true');
                                                        echo form_input($data);                                             
                                                        echo form_error('qty', '<p class="text-danger">', '</p>');
													?>
													<?php 
                                                        $data = array('type' => 'hidden', 'class' => 'form-control', 'name' => 'qty_convert', 'value' => $info['qty_b']*$info['value_ub'], 'required' => 'true', 'readonly' => 'true'); 
                                                        echo form_input($data);
                                                    ?>
                                                </td>
                                                <td>
													<?php  
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'value' => $info['name_u'], 'readonly' => 'true'); 
                                                        echo form_input($data);
                                                    ?>
													<?php 
                                                        $data = array('type' => 'hidden', 'class' => 'form-control', 'name' => 'unit_id', 'value' => $info['id_u'], 'required' => 'true', 'readonly' => 'true'); 
                                                        echo form_input($data);
                                                    ?>
												</td>
												<td>
													<?php 
                                                        $data = array('type' => 'text', 'class' => 'form-control text-right', 'name' => 'hpp','value' => number_format($info['hpp_b'],0,".",","), 'required' =>'true', 'readonly' => 'true'); 
                                                        echo form_input($data);
                                                    ?>
												</td>
												<td>
													<?php 
														$total_hpp = $info['hpp_b'] * $info['qty_b'] * $info['value_ub'];
														$subtotal_hpp = $subtotal_hpp +  $total_hpp;											
                                                        $data = array('type' => 'text', 'class' => 'form-control text-right', 'name' => 'total_hpp','value' => number_format($total_hpp,0,".",","), 'required' =>'true', 'readonly' => 'true'); 
                                                        echo form_input($data);
                                                    ?>
												</td>
											</tr>
											<?php endforeach; ?>
											<tr>
												<td colspan="4" class="text-right">BIAYA PRODUKSI PER UNIT</td>
												<td>
													<?php 														
                                                        $data = array('type' => 'text', 'class' => 'form-control text-right', 'name' => 'subtotal_hpp','value' => number_format($subtotal_hpp,0,".",","), 'required' =>'true', 'readonly' => 'true'); 
                                                        echo form_input($data);
                                                    ?>
												</td>
											</tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
						</div>   
						<div class="kt-portlet__foot">
							<div class="kt-form__actions">
								<div class="row">
									<div class="col-md-6">
										<a href="<?php echo base_url('product'); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>
									</div>
									<div class="col-md-6">                                
										<button type="submit" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-save"></i> SIMPAN</button>
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