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
        <?php echo form_open_multipart('', ['class' => 'repeater', 'autocomplete'=>'off']); ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-dark fade show" role="alert">
                    <div class="alert-icon"><i class="flaticon-exclamation-2"></i></div>
                    <div class="alert-text">Mohon Perhatian! Label yang memiliki bintang(<span class="text-danger">*</span>) wajib di disi, terima kasih.</div>			
                </div>
                <!--begin::Portlet-->
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
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label kt-font-dark"><span class="text-danger">*</span>TGL. PEMESANAN</label>
                                    <div class="col-md-9">
                                    <?php 
                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'date', 'name' => 'date',  'value' => date('d-m-Y'), 'placeholder' => 'Silahkan isi tanggal pemesanan penjualan', 'required' => 'true'); 
                                        echo form_input($data);                                             
                                        echo form_error('date', '<p class="text-danger">', '</p>');
                                    ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label kt-font-dark"><span class="text-danger">*</span>PELANGGAN</label>
                                    <div class="col-md-9">
                                        <select class="form-control" name="customer_code" id="customer_code" required>
                                        </select>
                                    </div>
                                </div>                                
                            </div>
                            <div class="col-md-6">                                
                                <div class="form-group row">                                
                                    <label class="col-md-3 col-form-label kt-font-dark text-right"><span class="text-danger">*</span>SALES</label>
                                    <div class="col-md-9">
                                        <select class="form-control" name="sales_code" id="sales_code" required>
                                            <option value="">-- Pilih Karyawan --</option>
                                        </select>
                                    </div>
                                </div>                            
                                <div class="form-group row">                                                                                 
                                    <label class="col-md-3 col-form-label kt-font-dark text-right"><span class="text-danger">*</span>PENGAMBILAN</label>
                                    <div class="col-md-9">
                                        <select class="form-control" name="taking" id="taking" disabled>
                                            <option value="1">LANGSUNG</option>
                                            <option value="2">PENGIRIMAN</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row" id="delivery">
                                    <label class="col-md-3 col-form-label kt-font-dark text-right"><span class="text-danger">*</span>ALAMAT PENGIRIMAN</label>
                                    <div class="col-md-9">                                        
                                        <?php 
                                            $data = array('class' => 'form-control', 'rows' => 3, 'id' => 'delivery_address', 'name' => 'delivery_address',  'placeholder' => 'Silahkan isi alamat pengiriman...'); 
                                            echo form_textarea($data);                                             
                                            echo form_error('delivery_address', '<p class="text-danger">', '</p>');
                                        ?>
                                    </div>
                                </div>
                            </div>                            
                        </div>    
                        <div class="row" id="delivery_info">
                            <div class="col-md-4">                                
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
                                <small class="kt-font-primary" id="add_product" data-repeater-create>*<b>ENTER / Klik Disini</b> untuk menambah baris produk</small>
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
                                            <th width=150px;><span class="text-danger">*</span>TOTAL</th>
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
                                                <select class="form-control unit" name="unit_id" required></select>
                                            </td>
                                            <td>
                                                <?php 
                                                $data = array('type' => 'text', 'class' => 'form-control text-right price', 'name' => 'price', 'value' => set_value('price'), 'required' => 'true');
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
                                                <a data-repeater-delete href="javascript:void(0);" class="text-danger text-center kt-font-bold delete_product"><i class="fa fa-times"></i></a>
                                            </td>
                                        </tr>
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
                                    <label class="col-md-3 col-form-label kt-font-dark text-right">TOTAL PRODUK</label>
                                    <div class="col-md-3">
                                        <?php
                                            $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_product', 'name' => 'total_product',  'value' => set_value('total_product'), 'required' => 'true', 'readonly' => 'true'); 
                                            echo form_input($data);                                             
                                            echo form_error('total_product', '<p class="text-danger">', '</p>');
                                        ?>
                                    </div>
                                    <label class="col-md-3 col-form-label kt-font-dark text-right">TOTAL KUANTITAS</label>
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
                                    <label class="col-md-3 col-form-label kt-font-dark text-right">SUBTOTAL</label>
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
                                    <label class="col-md-4 col-form-label kt-font-dark text-right"><strong>Grand Total</strong></label>
                                    <div class="col-md-8">
                                        <?php
                                            $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'grandtotal', 'name' => 'grandtotal',  'value' => set_value('grandtotal'), 'required' => 'true', 'readonly' => 'true'); 
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
                                    <a href="<?php echo base_url('sales'); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>                                        
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" id="btn_save" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control" onclick="this.disabled=true;this.form.submit();" disabled><i class="fa fa-save"></i> SIMPAN</button>
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