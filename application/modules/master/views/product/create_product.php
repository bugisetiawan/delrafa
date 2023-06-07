<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Master</h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>
            <h3 class="kt-subheader__title"><b><?php echo $title; ?></b></h3>
        </div>
    </div>    
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
		<div class="alert alert-dark fade show" role="alert">
			<div class="alert-icon"><i class="flaticon-exclamation-2"></i></div>
			<div class="alert-text">Mohon Perhatian! Label yang memiliki bintang(<span class="text-danger">*</span>) wajib diisi, terima kasih.</div>			
		</div>
        <?php echo form_open_multipart('', ['class' => 'repeater', 'autocomplete' => 'off']); ?>
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
                                <a href="<?php echo base_url('department'); ?>" class="btn btn-outline-success btn-elevate btn-icon" target="_blank"
                                    data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Tambah Data Departemen">
                                <i class="la la-plus"></i>
                                </a>                                    
                                <button type="button" id="refresh_department" name="refresh_department" class="btn btn-outline-brand btn-elevate btn-icon"
                                    data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Refresh Data Departemen">
                                <i class="la la-refresh"></i>
                                </button>
                            </div>
                        </div>
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
                                    <select class="form-control department_select2" id="department_code" name="department_code" required></select>
                                    <?php echo form_error('department_code', '<p class="text-danger">', '</p>'); ?>                                            
                                </div>
                                <div class="col-md-4">
                                    <label class="text-dark"><span class="text-danger">*</span>SUB DEPARTEMEN</label>
                                    <select class="form-control"  id="subdepartment_code" name="subdepartment_code" required>
                                        <option value="">- PILIH SUBDEPARTEMEN -</option>
                                    </select>
                                    <?php echo form_error('subdepartment_code', '<p class="text-danger">', '</p>'); ?>
                                </div>
                                <div class="col-md-2">
                                    <label class="text-dark"><span class="text-danger">*</span>TIPE PRODUK</label>
                                    <select class="form-control  id="product_type" name="product_type" required>
                                        <option value="1" checked>SINGLE</option>
                                        <!-- <option value="2">BUNDLE</option> -->
                                    </select>
                                    <?php echo form_error('product_type', '<p class="text-danger">', '</p>'); ?>
                                </div>
                                <div class="col-md-2">
                                    <label class="text-dark">PAJAK</label>
                                    <select class="form-control" id="ppn" name="ppn">
                                        <option value="0">NON</option>
                                        <option value="1">PPN</option>
                                        <!-- <option value="2">FINAL</option> -->
                                    </select>                                                                             
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label class="text-dark">BARCODE</label>
                                    <?php 
                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'barcode', 'name' => 'barcode',  'value' => set_value('barcode'), 'placeholder' => 'Silahkan isi barcode produk'); 
                                        echo form_input($data);                                             
                                        echo form_error('barcode', '<p class="text-danger">', '</p>');
                                    ?>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-dark"><span class="text-danger">*</span>NAMA</label>
                                    <?php 
                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'name', 'name' => 'name',  'value' => set_value('name'), 'placeholder' => 'Silahkan isi nama produk', 'required' => 'true'); 
                                        echo form_input($data);                                             
                                        echo form_error('name', '<p class="text-danger">', '</p>');
                                    ?>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-dark">IDENTITAS</label>                                            
                                    <?php 
                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'productid', 'name' => 'productid',  'value' => set_value('productid'), 'placeholder' => 'Silahkan isi identitas produk'); 
                                        echo form_input($data);                                             
                                        echo form_error('productid', '<p class="text-danger">', '</p>');
                                    ?>										
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">                                        
                                    <label class="text-dark">DESKRIPSI</label>                                            
                                    <?php 
                                        $data = array('class' => 'form-control', 'id' => 'description', 'name' => 'description',  'value' => set_value('description'), 'rows' => '3', 'placeholder' => 'Silahkan isi deskripsi/keterangan produk'); 
                                        echo form_textarea($data);                                             
                                        echo form_error('description', '<p class="text-danger">', '</p>');
                                    ?>
                                </div>
                                <div class="col-md-4">										
                                    <label class="text-dark">FOTO</label>
                                    <input type="file" class="form-control" id="photo" name="photo">
                                </div>
                                <div class="col-md-2">										
                                    <label class="text-dark">PREVIEW</label><br>
                                    <img src="#" id="preview" name="preview" height="118" width="118" alt="Preview Photo">
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
                            Persediaan & Harga Jual <small class="text-primary">Per Satuan Dasar</small>
                        </h3>
                    </div>
                    <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-wrapper">
                            <div class="kt-portlet__head-actions"> 
                                <a href="<?php echo base_url('unit'); ?>" class="btn btn-outline-success btn-elevate btn-icon" target="_blank"
                                    data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Tambah Data Satuan">
                                <i class="la la-plus"></i>
                                </a>
                                <button type="button" id="refresh_unit" name="refresh_unit" class="btn btn-outline-brand btn-elevate btn-icon"
                                    data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Refresh Data Satuan">
                                <i class="la la-refresh"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <div class="kt-section">
                        <div class="kt-section__body">
                            <div class="alert alert-dark fade show" role="alert">                                    
                                <div class="alert-text">*Harap mengisi dengan benar <b>Data Satuan Dasar Transaksi Produk</b>, karena tidak dapat diperbaharui ketika sudah tersimpan. Terima Kasih</div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="col-form-label text-dark"><span class="text-danger">*</span>STOK MINIMAL</label>
                                        <?php 
                                            $data = array('type' => 'number', 'class' => 'form-control', 'id' => 'minimal', 'name' => 'minimal', 'placeholder' => 'Jumlah Stok Minimal', 'min' => '0', 'value' => 0, 'required' => 'true'); 
                                            echo form_input($data);                                             
                                            echo form_error('minimal', '<p class="text-danger">', '</p>');
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="col-form-label text-dark"><span class="text-danger">*</span>STOK MAKSIMAL</label>
                                        <?php 
                                            $data = array('type' => 'number', 'class' => 'form-control', 'id' => 'maximal', 'name' => 'maximal', 'placeholder' => 'Jumlah Stok Maksimal', 'min' => '0',  'value' => 0, 'required' => 'true'); 
                                            echo form_input($data);                                             
                                            echo form_error('maximal', '<p class="text-danger">', '</p>');
                                        ?>                                        
                                    </div>
                                </div>                                    
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="col-form-label text-dark"><span class="text-danger">*</span>SATUAN DASAR TRANSAKSI</label>
                                        <select class="form-control unit_select text-dark" id="unit_id" name="unit_id" required>
                                            <option value="">- PILIH SATUAN -</option>
                                        </select>                                            
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="col-form-label text-dark">BERAT DASAR (KG)</label>
                                    <?php
                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'weight', 'name' => 'weight', 'placeholder' => 'Berat Dasar Produk', 'value' => 0);
                                        echo form_input($data);
                                        echo form_error('weight', '<p class="text-danger">', '</p>');
                                    ?>																																								
                                </div> 
                                <div class="col-md-2">
                                    <label class="col-form-label text-dark">KOMISI PENJUALAN (%)</label>
                                    <?php
                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'commission_sales', 'name' => 'commission_sales', 'placeholder' => 'Komisi Penjualan', 'value' => 0); 
                                        echo form_input($data);
                                        echo form_error('commission_sales', '<p class="text-danger">', '</p>');
                                    ?>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="col-form-label text-dark"><span class="text-danger">*</span>STATUS TRANSAKSI</label>                                            
                                        <select class="form-control" id="status" name="status">
                                            <option value="1">KONTINU</option>
                                            <option value="0">DISKONTINU</option>
                                        </select>
                                    </div>                                        
                                </div>
                            </div> 
                            <div class="alert alert-dark fade show" role="alert">                                    
                                <div class="alert-text">*Harga Jual 1 s.d. 5, Merupakan Harga Jual BERTINGKAT dari TINGGI ke RENDAH</div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <label class="text-dark"><span class="text-danger">*</span>HARGA JUAL 1</label>
                                    <?php
                                        $data = array('type' => 'text', 'class' => 'form-control price', 'id' => 'price_1', 'name' => 'price_1', 'placeholder' => 'Silahkan isi Harga Jual 1...',  'value' => set_value('price_1'), 'required' => 'true'); 
                                        echo form_input($data);
                                        echo form_error('price_1', '<p class="text-danger">', '</p>');
                                        ?>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-dark">HARGA JUAL 2</label>
                                    <?php
                                        $data = array('type' => 'text', 'class' => 'form-control price', 'id' => 'price_2', 'name' => 'price_2', 'placeholder' => 'Silahkan isi Harga Jual 2...',  'value' => set_value('price_2')); 
                                        echo form_input($data);
                                        echo form_error('price_2', '<p class="text-danger">', '</p>');
                                        ?>
                                </div>
                                <div class="col-md-2">
                                    <label class="text-dark">HARGA JUAL 3</label>
                                    <?php
                                        $data = array('type' => 'text', 'class' => 'form-control price', 'id' => 'price_3', 'name' => 'price_3', 'placeholder' => 'Silahkan isi Harga Jual 3...',  'value' => set_value('price_3')); 
                                        echo form_input($data);
                                        echo form_error('price_3', '<p class="text-danger">', '</p>');
                                        ?>
                                </div>
                                <div class="col-md-2">
                                    <label class="text-dark">HARGA JUAL 4</label>
                                    <?php
                                        $data = array('type' => 'text', 'class' => 'form-control price', 'id' => 'price_4', 'name' => 'price_4', 'placeholder' => 'Silahkan isi Harga Jual 4...',  'value' => set_value('price_4')); 
                                        echo form_input($data);
                                        echo form_error('price_4', '<p class="text-danger">', '</p>');
                                        ?>
                                </div>
                                <div class="col-md-2">
                                    <label class="text-dark">HARGA JUAL 5</label>
                                    <?php
                                        $data = array('type' => 'text', 'class' => 'form-control price', 'id' => 'price_5', 'name' => 'price_5', 'placeholder' => 'Silahkan isi Harga Jual 5... ',  'value' => set_value('price_5')); 
                                        echo form_input($data);
                                        echo form_error('price_5', '<p class="text-danger">', '</p>');
                                        ?>
                                </div>
                            </div>                              
                        </div>
                    </div>
                </div>
            </div>
            <!--begin::Portlet-->
            <!-- <div class="kt-portlet" id="bundle_form">
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
                                    <tr data-repeater-item style="text-align:center;">
                                        <td>
                                            <div class="typeahead">
                                            <?php
                                                $data = array('type' => 'text', 'class' => 'form-control product_input', 'placeholder' => 'Silahkan isi Kode/Nama Produk...'); 
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
                                                $data = array('type' => 'text', 'class' => 'form-control qty', 'name' => 'qty', 'placeholder' => 'QTY',  'value' => set_value('qty')); 
                                                echo form_input($data);                                             
                                                echo form_error('qty', '<p class="text-danger">', '</p>');
                                            ?>
                                        </td>												
                                        <td>
                                            <select class="form-control unit" name="unit_id">
                                            </select>
                                        </td>
                                        <td>
                                            <a data-repeater-delete href="javascript:void(0);" class="text-danger text-center kt-font-bold delete_product"><i class="fa fa-times"></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> -->
            <!--end::Portlet-->            
            <div class="kt-portlet">
                <div class="kt-portlet__foot">
                    <div class="kt-form__actions">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="<?php echo site_url('product'); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>
                            </div>
                            <div class="col-md-6">                                
                                <button type="submit" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-save"></i> SIMPAN</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php echo form_close(); ?>
    </div>
</div>