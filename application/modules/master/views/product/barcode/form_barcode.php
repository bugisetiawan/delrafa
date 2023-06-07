<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Master <b><?php echo $title; ?></b></h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>
            <span class="kt-subheader__desc"><strong>TAMBAH DATA</strong></span>
        </div>
    </div>
    <!-- end:: Content Head -->    
    <!-- begin:: Content -->    
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
		<div class="alert alert-dark fade show" role="alert">
			<div class="alert-icon"><i class="flaticon-exclamation-2"></i></div>
			<div class="alert-text">Mohon Perhatian! Label yang memiliki bintang(*) wajib di disi, terima kasih.</div>			
		</div>
        <div class="row">
            <div class="col-lg-12">
                <!--begin::Form-->
                <?php echo form_open_multipart('product/print/barcode', ['target' => '_blank', 'autocomplete' => 'off']); ?>                
                <!--begin::Portlet-->
                <div class="kt-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title"> 
                            Informasi | Kode <b>Produk</b> : <strong id="product_code" class="text-success"><?php echo $product['code_p']; ?></strong>                            
                            </h3>
                        </div>                        
                    </div>
                    <div class="kt-portlet__body">
                        <div class="kt-section">
                            <div class="kt-section__body">
                                <div class="row">
                                    <?php 
                                        $data = array('type' => 'hidden', 'class' => 'form-control', 'id' => 'product_code', 'name' => 'product_code',  'value' => $product['code_p'], 'required' => 'true'); 
                                        echo form_input($data);
                                    ?>
                                    <div class="col-lg-4">
                                        <div class="form-group row">
                                            <label class="col-md-2 col-form-label kt-font-dark">Nama</label>
                                            <div class="col-md-10">                                                
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'value' => $product['name_p'], 'readonly' => 'true'); 
                                                    echo form_input($data);
                                                    ?>
                                            </div>
										</div> 
										<div class="form-group row">
                                            <label class="col-md-2 col-form-label kt-font-dark">Supplier</label>
                                            <div class="col-md-10">                                                
                                                <select class="form-control" name="supplier_code" id="supplier_code">
													<option value="">-- Pilih Supplier --</option>
													<?php foreach($supplier AS $info): ?>
														<option value="<?php echo $info['code_s']; ?>"><?php echo $info['code_s']; ?></option>
													<?php endforeach; ?>
												</select>
                                            </div>
                                        </div>                                                                       
                                    </div>
                                    <div class="col-lg-4">                                        
                                        <div class="form-group row">
                                            <label class="col-md-2 col-form-label kt-font-dark">Harga</label>
                                            <div class="col-md-10">                                                
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'value' => 'Rp. '.number_format($product['sellprice'],'0','0','.'), 'readonly' => 'true'); 
                                                    echo form_input($data);
                                                    ?>
                                            </div>
										</div>  
										<div class="form-group row">
                                            <label class="col-md-2 col-form-label kt-font-dark">*Cetak</label>
                                            <div class="col-md-10">                                                
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'print_qty', 'name' => 'print_qty',  'value' => set_value('print_qty'), 'placeholder' => 'Jumlah barcode yang akan dicetak...', 'required' => 'true'); 
                                                    echo form_input($data);                                             
                                                    echo form_error('print_qty', '<p class="text-danger">', '</p>');
                                                ?>
                                            </div>
                                        </div>                                                                               
                                    </div>
                                    <div class="col-lg-4">                                        
                                                                               
                                    </div>
								</div>								
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
                                    <button type="submit" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-print"></i> CETAK BARCODE</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
                <!--end::Form-->
            </div>
        </div>
    </div>
    <!-- end:: Content -->
</div>