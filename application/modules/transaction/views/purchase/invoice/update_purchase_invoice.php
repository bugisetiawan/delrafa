<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Transaksi</b></h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>
            <h3 class="kt-subheader__title"><b><?php echo $title; ?></b></h3>
        </div>
    </div>
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
        <?php echo form_open_multipart('', ['class' => 'repeater', 'autocomplete'=>'off']); ?>
            <div class="kt-portlet">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            Informasi | No.Transaksi <span class="kt-font-bold kt-font-success"><?php echo $purchase_invoice['code']; ?></span>
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <input type="hidden" name="purchase_id" value=<?php echo encrypt_custom($purchase_invoice['id']); ?>>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label text-dark"><span class="text-danger">*</span>TGL. PEMBELIAN</label>
                                <div class="col-md-9">
                                    <?php 
                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'date', 'name' => 'date',  'value' => date('d-m-Y', strtotime($purchase_invoice['date'])), 'placeholder' => 'Silahkan isi tanggal pembelian', 'required' => 'true'); 
                                        echo form_input($data);                                             
                                        echo form_error('date', '<p class="text-danger">', '</p>');
                                    ?> 
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label text-dark text-right"><span class="text-danger">*</span>NO. REFRENSI</label>
                                <div class="col-md-9">
                                    <?php 
                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'invoice', 'name' => 'invoice',  'value' => $purchase_invoice['invoice'], 'placeholder' => 'Silahkan isi nomor refrensi pembelian dari supplier...', 'required' => 'true');
                                        echo form_input($data);                                             
                                        echo form_error('invoice', '<p class="text-danger">', '</p>');
                                    ?>
                                    <span style="color:red;" id="invoice_message"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label text-dark"><span class="text-danger">*</span>SUPPLIER</label>                                            
                                <div class="col-md-9">
                                    <input type="hidden" id="supplier_code_update" value="<?php echo $purchase_invoice['code_s']; ?>">
                                    <select class="form-control" name="supplier_code" id="supplier_code" required></select>                                                
                                </div>                                            
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label text-dark text-right"><span class="text-danger">*</span>PEMBAYARAN</label>
                                <div class="col-md-3">
                                    <select class="form-control" name="payment" id="payment" required>                                                
                                        <option value="1" <?php if($purchase_invoice['payment'] == 1){ echo "selected"; } ?>>TUNAI</option>
                                        <option value="2" <?php if($purchase_invoice['payment'] == 2){ echo "selected"; } ?>>KREDIT</option>
                                    </select>
                                </div>
                                <label class="col-md-1 col-form-label text-dark text-right"><span class="text-danger">*</span>TEMPO</label>
                                <div class="col-md-2">
                                    <?php 
                                        $data = array('type' => 'number', 'min'=> 0, 'class' => 'form-control', 'id' => 'payment_due', 'name' => 'payment_due',  'value' => $purchase_invoice['payment_due'], 'required' => 'true', 'readonly' => 'true');
                                        echo form_input($data);                                             
                                        echo form_error('payment_due', '<p class="text-danger">', '</p>');
                                    ?>
                                </div>        
                                <label class="col-md-1 col-form-label text-dark text-right"><span class="text-danger">*</span>PPN</label>                                                                            
                                <div class="col-md-2">
                                    <select class="form-control" name="ppn" id="ppn">
                                        <option value="0" <?php if($purchase_invoice['ppn'] == 0){ echo "selected"; } ?>>NON</option>
                                        <option value="1" <?php if($purchase_invoice['ppn'] == 1){ echo "selected"; } ?>>PPN</option>
                                    </select>
                                </div>
                            </div>                                        
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label kt-font-dark">KETERANGAN</label>
                                <div class="col-md-9">
                                    <textarea class="form-control" rows="3" name="information" id="information" placeholder="Silahkan isi keterangan bila ada..."><?php echo $purchase_invoice['information']; ?></textarea>
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
                                <div class="row" id="include_tax_method">
                                    <label class="col-9 col-form-label text-dark">Harga termasuk pajak</label>
                                    <div class="col-3">
                                        <span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--success">
                                            <label>
                                                <input type="checkbox" id="price_include_tax" name="price_include_tax" value="1" <?php if($purchase_invoice['price_include_tax'] == 1){ echo "checked"; } ?>>
                                                <span></span>
                                            </label>
                                        </span>
                                    </div>
                                </div>                                                                                                                                   
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
                                        <th width="120px"><span class="text-danger">*</span>HARGA</th>
                                        <th width="180px"><span class="text-danger">*</span>DISKON (%)</th>
                                        <th width="150px"><span class="text-danger">*</span>GUDANG</th>
                                        <th width="180px">TOTAL</th>                                                     
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
                                                $data = array('type' => 'text', 'class' => 'form-control text-right qty', 'name' => 'qty', 'placeholder' => '0',  'value' => set_value('qty')); 
                                                echo form_input($data);                                             
                                                echo form_error('qty', '<p class="text-danger">', '</p>');
                                            ?>
                                        </td>												
                                        <td>
                                            <select class="form-control unit" name="unit_id">
                                            </select>
                                        </td>
                                        <td>
                                            <?php 
                                                $data = array('type' => 'text', 'class' => 'form-control text-right price', 'name' => 'price', 'placeholder' => '0'); 
                                                echo form_input($data);                                             
                                                echo form_error('price', '<p class="text-danger">', '</p>');
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                                $data = array('type' => 'text', 'class' => 'form-control text-right disc_product', 'name' => 'disc_product', 'placeholder' => '0',  'value' => set_value('discount_p')); 
                                                echo form_input($data);                                             
                                                echo form_error('disc_product', '<p class="text-danger">', '</p>');
                                            ?>
                                        </td>
                                        <td>
                                            <select class="form-control warehouse" name="warehouse_id">
                                            </select>
                                        </td>
                                        <td>
                                            <?php 
                                                $data = array('type' => 'text', 'class' => 'form-control text-right total', 'name' => 'total',  'value' => set_value('total'), 'readonly' => 'true'); 
                                                echo form_input($data);                                             
                                                echo form_error('total', '<p class="text-danger">', '</p>');
                                            ?>
                                        </td>
                                        <td>
                                            <label data-repeater-delete class="col-form-label text-danger text-center delete_product"><i class="fa fa-times"></i></label>
                                        </td>
                                    </tr>
                                    <?php foreach($purchase_invoice_detail AS $info): ?>
                                    <tr data-repeater-item style="text-align:center;">
                                        <?php 
                                            $data = array('type' => 'hidden', 'name' => 'purchase_detail_id', 'value' => $info['id']); 
                                            echo form_input($data);
                                        ?>
                                        <td>
                                            <div class="typeahead">
                                            <?php
                                                $data = array('type' => 'text', 'class' => 'form-control product_input', 'placeholder' => 'Silahkan ketik Kode/Nama Produk...', 'value' => $info['name_p'] , 'required' => 'true'); 
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
                                                $data = array('type' => 'text', 'class' => 'form-control text-right qty', 'name' => 'qty', 'placeholder' => '0',  'value' => $info['qty'], 'required' => 'true'); 
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
                                                $units = $this->purchase->get_unit($where)->result_array();
                                            ?>
                                            <select class="form-control unit" name="unit_id" required>
                                                <?php foreach($units AS $unit): ?>
                                                <option value="<?php echo $unit['id_u']; ?>" <?php if($info['id_u'] == $unit['id_u']) { echo 'selected'; } ?>><?php echo $unit['code_u']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <?php 
                                                $data = array('type' => 'text', 'class' => 'form-control text-right price', 'name' => 'price', 'value' =>  number_format($info['price'],'0','.',','), 'placeholder' => '0', 'required' => 'true'); 
                                                echo form_input($data);                                             
                                                echo form_error('price', '<p class="text-danger">', '</p>');
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                                $data = array('type' => 'text', 'class' => 'form-control text-right disc_product', 'name' => 'disc_product', 'placeholder' => '0',  'value' => $info['disc_product'], 'required' => 'true'); 
                                                echo form_input($data);                                             
                                                echo form_error('disc_product', '<p class="text-danger">', '</p>');
                                            ?>
                                        </td>
                                        <td>
                                            <?php $warehouses = $this->purchase->get_warehouse($info['code_p'], $info['id_u']); ?>
                                            <select class="form-control warehouse" name="warehouse_id" required>
                                                <?php foreach($warehouses As $warehouse): ?>
                                                    <option value="<?php echo $warehouse['id_w']; ?>" <?php if($info['id_w'] == $warehouse['id_w']) { echo 'selected'; } ?>><?php echo $warehouse['code_w']." | ".$warehouse['stock']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <?php                                             
                                                $data = array('type' => 'text', 'class' => 'form-control text-right total', 'name' => 'total',  'value' => number_format($info['total'],'2','.',','), 'required' => 'true', 'readonly' => 'true'); 
                                                echo form_input($data);
                                                echo form_error('total', '<p class="text-danger">', '</p>');
                                            ?>
                                        </td>
                                        <td>
                                            <label data-repeater-delete class="col-form-label text-danger text-center delete_product"><i class="fa fa-times"></i></label>
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
                                <label class="col-md-3 col-form-label text-dark text-right">TOTAL PRODUK</label>
                                <div class="col-md-3">
                                    <?php
                                        $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_product', 'name' => 'total_product',  'value' => $purchase_invoice['total_product'], 'required' => 'true', 'readonly' => 'true'); 
                                        echo form_input($data);                                             
                                        echo form_error('total_product', '<p class="text-danger">', '</p>');
                                    ?>
                                </div>
                                <label class="col-md-3 col-form-label text-dark text-right">TOTAL KUANTITAS</label>
                                <div class="col-md-3">
                                    <?php
                                        $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_qty', 'name' => 'total_qty',  'value' => $purchase_invoice['total_qty'], 'required' => 'true', 'readonly' => 'true'); 
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
                                        $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'subtotal', 'name' => 'subtotal',  'value' => number_format($purchase_invoice['total_price'],'0','.',','), 'required' => 'true', 'readonly' => 'true'); 
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
                        <div class="col-md-6"></div>
                        <div class="col-md-6">                                                                            
                            <div class="form-group row">
                                <div class="col-md-1"></div>
                                <select class="col-md-3 form-control text-right" id="discount_method">
                                    <option value="1">DISKON (%)</option>
                                    <option value="2">DISKON (Rp)</option>
                                </select>
                                <div class="col-md-8" id="discount_percent">
                                    <?php 
                                        $data = array('type' => 'text', 'class' => 'form-control text-right', 'name' => 'discount_p', 'id' => 'discount_p', 'placeholder' => 'Diskon (%)', 'value' => number_format($purchase_invoice['discount_p'], 2,'.',','), 'required' => 'true'); 
                                        echo form_input($data);
                                        echo form_error('discount_p', '<p class="text-danger">', '</p>');                                            
                                    ?>
                                    <small>*Diskon Persen (%)</small>
                                </div>
                                <div class="col-md-8" id="discount_amount">
                                    <?php 
                                        $data = array('type' => 'text',  'class' => 'form-control text-right', 'name' => 'discount_rp', 'id' => 'discount_rp', 'placeholder' => 'Diskon (Rupiah)', 'value' => number_format($purchase_invoice['discount_rp'], 2,'.',','), 'required' => 'true'); 
                                        echo form_input($data);                                             
                                        echo form_error('discount_rp', '<p class="text-danger">', '</p>');
                                    ?>
                                    <small>*Diskon Rupiah (Rp)</small>
                                </div>
                            </div>
                            <div class="form-group row" id="tax_form">
                                <label class="col-md-4 col-form-label text-dark text-right">PPN</label>
                                <div class="col-md-8">
                                    <?php
                                        $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_tax', 'name' => 'total_tax',  'value' => number_format($purchase_invoice['total_tax'],'0','.',','), 'required' => 'true', 'readonly' => 'true'); 
                                        echo form_input($data);                                             
                                        echo form_error('total_tax', '<p class="text-danger">', '</p>');
                                    ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-dark text-right"><strong>GRANDTOTAL</strong></label>
                                <div class="col-md-8">
                                    <?php
                                        $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'grandtotal', 'name' => 'grandtotal',  'value' => number_format($purchase_invoice['grandtotal'],'2','.',','), 'required' => 'true', 'readonly' => 'true'); 
                                        echo form_input($data);                                             
                                        echo form_error('grandtotal', '<p class="text-danger">', '</p>');
                                    ?>
                                </div>
                            </div>
                            <div class="form-group row" id="downpayment_form">
                                <label class="col-md-4 col-form-label text-dark text-right">Total Pembayaran</label>
                                <div class="col-md-8">
                                    <?php
                                        $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_payment', 'placeholder' => '0', 'value' => number_format($payment_ledger['grandtotal'],'2','.',','), 'readonly' => 'true');
                                        echo form_input($data);
                                        echo form_error('total_payment', '<p class="text-danger">', '</p>');
                                    ?>
                                </div>
                            </div>
                            <div class="form-group row" id="credit_method">
                                <label class="col-md-4 col-form-label text-dark text-right"><strong>Hutang Pembelian</strong></label>
                                <div class="col-md-8">
                                    <?php 
                                        $data = array('type' => 'text', 'class' => 'form-control text-right', 'name' => 'account_payable', 'id' => 'account_payable', 'placeholder' => 'Hutang Dagang...', 'value' => number_format($purchase_invoice['account_payable'],'2','.',','), 'readonly' => 'true', 'required' => 'true');
                                        echo form_input($data);                                             
                                        echo form_error('account_payable', '<p class="text-danger">', '</p>');
                                    ?>
                                    <small class="text-danger" id="account_payable_message">*Mohon Maaf, hutang pembelian tidak boleh minus</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="kt-portlet__foot">
                    <div class="kt-form__actions">
                        <div class="row">
                            <div class="col-md-6">
                                <?php $link = ($this->agent->referrer() != "") ? urldecode($this->agent->referrer()) : site_url('purchase'); ?>
                                <a href="<?php echo $link; ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" id="btn_save" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-save"></i> SIMPAN</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>        
        <?php echo form_close() ?>
    </div>
    <!-- end:: Content -->
</div>