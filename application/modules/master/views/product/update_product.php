<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Master</h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>
            <h3 class="kt-subheader__title"><b><?php echo $title; ?></b></h3>            
        </div>
    </div>
    <!-- end:: Content Head -->    
    <!-- begin:: Content -->
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
        <div class="alert alert-dark fade show" role="alert">
			<div class="alert-icon"><i class="flaticon-exclamation-2"></i></div>
			<div class="alert-text">Mohon Perhatian! Label yang memiliki bintang(<span class="text-danger">*</span>) wajib di disi, terima kasih.</div>			
		</div>        
        <!--begin::Form-->
        <?php echo form_open_multipart('', ['class' => 'repeater', 'autocomplete' => 'off']); ?>            
            <!--begin::Portlet-->
            <div class="kt-portlet">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            <?php 
                                $data_id = array('type' => 'hidden', 'id' => 'product_id', 'name' => 'product_id',  'value' => $product['id_p'], 'required' => 'true'); 
                                echo form_input($data_id);
                                $data_code = array('type' => 'hidden', 'id' => 'product_code', 'name' => 'product_code',  'value' => $product['code_p'], 'required' => 'true'); 
                                echo form_input($data_code);
                            ?>                                
                            Informasi | Kode <b>Produk</b> : <strong class="text-success"><?php echo $product['code_p']; ?></strong>
                        </h3>
                    </div>                        
                </div>
                <div class="kt-portlet__body">
                    <div class="kt-section">                            
                        <div class="kt-section__body">
                            <div class="text-danger">
                                <?php echo validation_errors(); ?>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">                                        
                                    <label class="text-dark"><span class="text-danger">*</span>DEPARTEMEN</label>
                                    <?php 
                                        $data = array('type' => 'hidden', 'id' => 'department_code_update', 'value' => $product['code_d']);
                                        echo form_input($data);                                                
                                    ?> 
                                    <select class="form-control kt-select2 department_select2" id="department_code" name="department_code" required></select>
                                    <?php echo form_error('department_code', '<p class="text-danger">', '</p>'); ?>
                                </div>
                                <div class="col-md-4">                                        
                                    <label class="text-dark"><span class="text-danger">*</span>SUB DEPARTEMEN</label>
                                    <?php 
                                        $data = array('type' => 'hidden', 'id' => 'subdepartment_code_update', 'value' => $product['code_sd']);
                                        echo form_input($data);                                                
                                    ?> 
                                    <select class="form-control kt-select2" id="subdepartment_code" name="subdepartment_code" required>
                                        <option value="">-- PILIH SUB DEPARTEMEN --</option>
                                    </select>
                                    <?php echo form_error('subdepartment_code', '<p class="text-danger">', '</p>'); ?>                                            
                                    <small class="text-dark">*Pilih Departemen untuk menampilkan data Sub Departemen</small>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="text-dark"><span class="text-danger">*</span>TIPE PRODUK</label>
                                        <select class="form-control kt-select2" id="product_type" name="product_type" required>
                                            <option value="1" <?php if($product['type'] == 1) { echo "selected"; } ?>>SINGLE</option>
                                            <!-- <option value="2" <?php if($product['type'] == 2) { echo "selected"; } ?>>BUNDLE</option> -->
                                        </select>
                                        <?php echo form_error('product_type', '<p class="text-danger">', '</p>'); ?>
                                    </div>
                                </div>
                                <div class="col-md-2">                                                                                
                                    <div class="form-group">      
                                        <label class="text-dark">PPN</label>
                                        <select class="form-control" id="ppn" name="ppn">
                                            <option value="0" <?php if($product['ppn'] == 0){ echo "selected"; } ?>>NON</option>
                                            <option value="1" <?php if($product['ppn'] == 1){ echo "selected"; } ?>>PPN</option>
                                            <!-- <option value="2" <?php if($product['ppn'] == 2){ echo "selected"; } ?>>FINAL</option> -->
                                        </select>
                                    </div>
                                    <?php echo form_error('ppn', '<p class="text-danger">', '</p>'); ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label class="text-dark">BARCODE</label>                                            
                                    <?php 
                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'barcode', 'name' => 'barcode',  'value' => $product['barcode'], 'placeholder' => 'Silahkan isikan barcode produk'); 
                                        echo form_input($data);                                             
                                        echo form_error('barcode', '<p class="text-danger">', '</p>');
                                    ?>                                    
                                </div>
                                <div class="col-md-4">                                    
                                    <label class="text-dark"><span class="text-danger">*</span>NAMA</label>
                                    <?php 
                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'name', 'name' => 'name',  'value' => $product['name_p'], 'placeholder' => 'Silahkan isikan nama produk', 'required' => 'true'); 
                                        echo form_input($data);                                             
                                        echo form_error('name', '<p class="text-danger">', '</p>');
                                    ?>
                                </div>
                                <div class="col-md-4">                                    
                                    <label class="text-dark">IDENTITAS</label>                                            
                                    <?php 
                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'productid', 'name' => 'productid',  'value' => $product['productid'], 'placeholder' => 'Silahkan isikan identitas produk'); 
                                        echo form_input($data);                                             
                                        echo form_error('productid', '<p class="text-danger">', '</p>');
                                    ?>
                                </div>
                            </div>
                            <div class="form-group row">                                
                                <div class="col-md-6">
                                    <label class="text-dark">DESKRIPSI</label>
                                    <?php 
                                        $data = array('class' => 'form-control', 'id' => 'description', 'name' => 'description',  'value' => $product['description'], 'rows' => '3', 'placeholder' => 'Silahkan isikan deskripsi produk'); 
                                        echo form_textarea($data);                                             
                                        echo form_error('description', '<p class="text-danger">', '</p>');
                                    ?>                                                                        
                                </div>
                                <div class="col-md-3">
                                    <label class="text-dark">FOTO</label>                                            
                                    <input type="file" class="form-control" id="photo" name="photo">                                                                                
                                </div>
                                <div class="col-md-3">                                    
                                        <label class="text-dark">PREVIEW</label><br>
                                        <?php  $image = ($product['photo'] != null) ? $product['photo'] : "nophoto.png"; ?>
                                        <img src="<?php echo base_url('assets/media/system/products/'.$image) ?>" id="preview" name="preview" height="118" width="118">
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
                            Persediaan
                        </h3>
                    </div>                        
                </div>
                <div class="kt-portlet__body">
                    <div class="kt-section">
                        <div class="kt-section__body">
                            <div class="row">
                                <div class="col-md-2">                                        
                                    <label class="text-dark"><span class="text-danger">*</span>STOK MINIMAL</label>
                                    <div>                                                
                                        <?php 
                                            $data = array('type' => 'number', 'class' => 'form-control', 'id' => 'minimal', 'name' => 'minimal',  'value' => $product['minimal'], 'required' => 'true'); 
                                            echo form_input($data);                                             
                                            echo form_error('minimal', '<p class="text-danger">', '</p>');
                                            ?>
                                    </div>                                        
                                </div>
                                <div class="col-md-2">                                        
                                    <label class="text-dark"><span class="text-danger">*</span>STOK MAKSIMAL</label>
                                    <div>                                                
                                        <?php 
                                            $data = array('type' => 'number', 'class' => 'form-control', 'id' => 'maximal', 'name' => 'maximal',  'value' => $product['maximal'], 'required' => 'true'); 
                                            echo form_input($data);                                             
                                            echo form_error('maximal', '<p class="text-danger">', '</p>');
                                            ?>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="text-dark"><span class="text-danger">*</span>SATUAN DASAR</label>                                            
                                    <div>
                                        <input type="hidden" name="unit_id_update" id="unit_id_update" value="<?php echo $product['id_u']; ?>">
                                        <select class="form-control kt-select2 unit_select text-dark" id="unit_id" name="unit_id" disabled>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="text-dark">BERAT DASAR (KG)</label>
                                    <?php 
                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'weight', 'name' => 'weight', 'placeholder' => 'Berat Dasar Produk', 'value' => $product['weight']);
                                        echo form_input($data);
                                        echo form_error('weight', '<p class="text-danger">', '</p>');
                                    ?>
                                </div>
                                <div class="col-md-2">
                                    <label class="text-dark">KOMISI PENJUALAN (%)</label>
                                    <?php
                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'commission_sales', 'name' => 'commission_sales', 'placeholder' => 'Komisi Penjualan', 'value' => $product['commission_sales']); 
                                        echo form_input($data);
                                        echo form_error('commission_sales', '<p class="text-danger">', '</p>');
                                    ?>		
                                    <small class="text-dark">*Per 1 (Satu) Qty satuan dasar</small>																																						
                                </div>
                                <div class="col-md-2">                                        
                                    <label class="text-dark"><span class="text-danger">*</span>STATUS PENJUALAN</label>
                                    <div>
                                        <select class="form-control kt-selectpicker" id="status" name="status">                                                    
                                            <option value="0">DISKONTINU</option>
                                            <option value="1" <?php if($product['status'] == 1) { echo "selected"; } ?>>KONTINU</option>
                                        </select>
                                    </div>
                                    <small class="kt-font-danger" id="status_message"></small>
                                </div>                                                                                                    
                            </div>                                
                        </div>
                    </div>
                </div>
            </div>   
            <!--end::Portlet-->
            <!--begin::Portlet-->
            <div class="kt-portlet" id="bundle_form">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            Daftar Produk Bundle
                            <small class="kt-font-primary" id="add_product" data-repeater-create>*Tekan tombol <b>ENTER</b> untuk menambah baris produk</small>
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
                                        <th width=100px;><span class="text-danger">*</span>QTY</th>
                                        <th width=100px;><span class="text-danger">*</span>SATUAN</th>
                                    </tr>
                                </thead>
                                <tbody data-repeater-list="product">
                                    <?php if($product_bundle != null): ?>
                                        <?php foreach($product_bundle AS $bundle): ?>
                                        <tr  data-repeater-item style="text-align:center;">
                                            <td>
                                                <div class="typeahead">
                                                <?php  
                                                    $data = array('type' => 'text', 'class' => 'form-control product_input', 'value' => $bundle['name_p'], 'placeholder' => 'Silahkan isi Kode/Nama Produk...'); 
                                                    echo form_input($data);
                                                ?>
                                                </div>
                                            </td>     
                                                <?php 
                                                    $data = array('type' => 'hidden', 'class' => 'form-control product_code', 'name' => 'product_code', 'value' => $bundle['code_p']); 
                                                    echo form_input($data);
                                                    echo form_error('product_code', '<p class="text-danger">', '</p>');
                                                ?>
                                            <td>
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control qty', 'name' => 'qty', 'placeholder' => 'QTY',  'value' => $bundle['qty']); 
                                                    echo form_input($data);                                             
                                                    echo form_error('qty', '<p class="text-danger">', '</p>');
                                                ?>
                                            </td>												
                                            <td>
                                                <select class="form-control unit" name="unit_id">
                                                    <option value="<?php echo $bundle['id_u'] ?>"><?php echo $bundle['name_u']; ?></option>
                                                </select>
                                            </td>
                                            <td>
                                                <a data-repeater-delete href="javascript:void(0);" class="text-danger text-center kt-font-bold delete_product"><i class="fa fa-times"></i></a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>   
            </div>
            <!--end::Portlet-->
            <!--begin::Portlet-->				
            <div class="kt-portlet">
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
        <?php echo form_close(); ?>
        <!--end::Form-->            
    <!-- end:: Content -->
</div>