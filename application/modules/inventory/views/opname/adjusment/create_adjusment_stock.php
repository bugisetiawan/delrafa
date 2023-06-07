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
        <?php echo form_open_multipart('', ['class' => 'repeater', 'autocomplete'=>'off']); ?>
            <div class="row">
                <div class="col-lg-12">
                    <!--begin::Portlet-->
                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    Informasi | No. Transaksi Stok Opname <b class="kt-font-success"><?php echo $stock_opname['code']; ?></b>
                                </h3>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <div class="row">           
                                <?php
                                    $data = array('type' => 'hidden', 'class' => 'form-control', 'name' => 'stock_opname_id', 'value' => $stock_opname['id'], 'required' => 'true', 'readonly' => 'true'); 
                                    echo form_input($data);
                                    echo form_error('stock_opname_id', '<p class="text-danger">', '</p>');
                                    $data = array('type' => 'hidden', 'class' => 'form-control', 'name' => 'stock_opname_code', 'value' => $stock_opname['code'], 'required' => 'true', 'readonly' => 'true'); 
                                    echo form_input($data);
                                    echo form_error('stock_opname_code', '<p class="text-danger">', '</p>');
                                    $data = array('type' => 'hidden', 'class' => 'form-control', 'name' => 'warehouse_id', 'value' => $stock_opname['warehouse_id'], 'required' => 'true', 'readonly' => 'true');
                                    echo form_input($data);
                                    echo form_error('warehouse_id', '<p class="text-danger">', '</p>');
                                ?>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="col-form-label kt-font-dark">TGL. STOK OPNAME</label>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control', 'value' => date('d-m-Y', strtotime($stock_opname['date'])), 'readonly' => 'true'); 
                                            echo form_input($data);
                                        ?>                                        
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="col-form-label kt-font-dark text-right">GUDANG</label>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control', 'value' => $stock_opname['warehouse'], 'readonly' => 'true'); 
                                            echo form_input($data);
                                        ?>                                        
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="col-form-label kt-font-dark text-right">PETUGAS</label>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control', 'value' => $stock_opname['checker'], 'readonly' => 'true'); 
                                            echo form_input($data);
                                        ?>                                        
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="col-form-label kt-font-dark text-right">OPERATOR</label>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control', 'value' => $stock_opname['operator'], 'readonly' => 'true'); 
                                            echo form_input($data);
                                        ?>
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
                                    Daftar Produk
                                    <small class="kt-font-bold kt-font-primary">*Mohon Perhatian! Data Stok yang tampil adalah per Tanggal & Jam: <?php echo date('d-m-Y H:i:s'); ?></small>
                                </h3>                                            
                            </div>						
                        </div>
                        <div class="kt-portlet__body">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-sm" id="product_table">
                                        <thead>
                                            <tr style="text-align:center;">
                                                <th width=150px;>KODE</th>
                                                <th>NAMA</th>
                                                <th width=150px;>SATUAN DASAR</th>
                                                <th width=150px;>STOK</th>
                                                <th width=150px;>PENYESUAIAN</th>
                                                <th width=150px;>STOK AKHIR</th>
                                            </tr>
                                        </thead>
                                        <tbody data-repeater-list="product">
                                            <?php foreach($product AS $info): ?>
                                            <tr data-repeater-item style="text-align:center;">
                                                <?php
                                                    $data = array('type' => 'hidden', 'class' => 'form-control', 'name' => 'product_code', 'value' => $info['code_p'], 'required' => 'true', 'readonly' => 'true'); 
                                                    echo form_input($data);
                                                    echo form_error('product_code', '<p class="text-danger">', '</p>');
                                                ?>
                                                <td> 
                                                    <?php
														$data = array('type' => 'text', 'class' => 'form-control text-center', 'value' => $info['code_p'], 'readonly' => 'true'); 
														echo form_input($data);														
													?>
                                                </td>
                                                <td>  
                                                    <?php
														$data = array('type' => 'text', 'class' => 'form-control', 'value' => $info['name_p'], 'readonly' => 'true'); 
														echo form_input($data);														
													?>                                                  
                                                </td>
                                                <?php
                                                    $data = array('type' => 'hidden', 'class' => 'form-control', 'name' => 'unit_id', 'value' => $info['id_u'], 'required' => 'true', 'readonly' => 'true'); 
                                                    echo form_input($data);
                                                    echo form_error('unit_id', '<p class="text-danger">', '</p>');
                                                ?>
                                                <td>  
                                                    <?php
														$data = array('type' => 'text', 'class' => 'form-control text-center', 'value' => $info['name_u'], 'readonly' => 'true'); 
														echo form_input($data);
													?>
                                                </td>
                                                <td>
                                                    <?php
                                                        $data = array('type' => 'text', 'class' => 'form-control text-right stock', 'name' => 'stock', 'value' => $info['stock'], 'required' => 'true', 'readonly' => 'true'); 
                                                        echo form_input($data);
                                                        echo form_error('stock', '<p class="text-danger">', '</p>');
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                        $data = array('type' => 'text', 'class' => 'form-control text-right adjust', 'name' => 'adjust', 'value' => 0, 'required' => 'true'); 
                                                        echo form_input($data);
                                                        echo form_error('adjust', '<p class="text-danger">', '</p>');
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                        $data = array('type' => 'text', 'class' => 'form-control text-right end_stock', 'name' => 'end_stock', 'value' => $info['stock'] , 'required' => 'true', 'readonly' => 'true', 'required' => 'true'); 
                                                        echo form_input($data);
                                                        echo form_error('end_stock', '<p class="text-danger">', '</p>');
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>                            
                            <hr>
							<div class="row">
								<div class="col-md-6">                                        
								</div>                                    
								<div class="col-md-6">
									<div class="form-group row">
										<label class="col-md-6 col-form-label kt-font-dark text-right">TOTAL PRODUK</label>
										<div class="col-md-6">
											<?php
												$data = array('type' => 'number', 'min' => 0, 'class' => 'form-control text-right', 'id' => 'total_product', 'name' => 'total_product',  'value' => $stock_opname['total_product'], 'required' => 'true', 'readonly' => 'true'); 
												echo form_input($data);                                             
												echo form_error('total_product', '<p class="text-danger">', '</p>');
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
                                        <a href="<?php echo site_url('opname/detail/'.encrypt_custom($stock_opname['id'])); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>                                        
                                    </div>
                                    <div class="col-md-6">
                                        <button type="submit" id="btn_save" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-save"></i> SIMPAN</button>
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