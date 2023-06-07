<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Transaksi</b></h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>   
            <h3 class="kt-subheader__title"><b><?php echo $title; ?></b></h3>
        </div>
    </div>
    <!-- end:: Content Head -->    
    <!-- begin:: Content -->
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
        <?php echo validation_errors(); ?>
        <?php echo form_open_multipart('', ['class' => 'repeater', 'autocomplete'=>'off']); ?>
            <div class="alert alert-dark fade show" role="alert">
                <div class="alert-icon"><i class="flaticon-exclamation-2"></i></div>
                <div class="alert-text">MOHON PERHATIAN! <b>Label</b> yang memiliki <b>bintang(<span class="text-danger">*</span>)</b> wajib terisi</div>
            </div>
            <!--begin::Portlet-->
            <div class="kt-portlet">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            Informasi | No. Transaksi <span class="kt-font-bold kt-font-success"><?php echo $sales_invoice['invoice']; ?></span>
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <input type="hidden" name="sales_invoice_id" value="<?php echo encrypt_custom($sales_invoice['id']); ?>">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label kt-font-dark"><span class="text-danger">*</span>TGL. PENJUALAN</label>
                                <div class="col-md-9">
                                    <?php 
                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'date', 'name' => 'date',  'value' => date('d-m-Y', strtotime($sales_invoice['date'])), 'placeholder' => 'Silahkan isi tanggal penjualan', 'required' => 'true'); 
                                        echo form_input($data);                                             
                                        echo form_error('date', '<p class="text-danger">', '</p>');
                                        ?> 
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label kt-font-dark text-right"><span class="text-danger">*</span>SALES</label>
                                <input type="hidden" id="sales_code_update" value="<?php echo $sales_invoice['sales_code']; ?>">
                                <div class="col-md-9">
                                    <select class="form-control" name="sales_code" id="sales_code" required></select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label kt-font-dark"><span class="text-danger">*</span>PELANGGAN</label>
                                <input type="hidden" id="customer_code_update" value="<?php echo $sales_invoice['customer_code']; ?>">
                                <div class="col-md-9">
                                    <select class="form-control" name="customer_code" id="customer_code" required></select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label kt-font-dark text-right"><span class="text-danger">*</span>PEMBAYARAN</label>
                                <div class="col-md-2">
                                <input type="hidden" id="payment_update" value=<?php echo $sales_invoice['payment']; ?>>
                                    <select class="form-control" name="payment" id="payment" required>
                                        <option value="1" <?php if($sales_invoice['payment'] == 1){ echo "selected";} ?>>TUNAI</option>
                                        <option value="2" <?php if($sales_invoice['payment'] == 2){ echo "selected";} ?>>KREDIT</option>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label kt-font-dark text-right"><span class="text-danger">*</span>TEMPO (Hari)</label>
                                <div class="col-md-2">
                                    <?php 
                                        $data = array('type' => 'number', 'min'=> 0, 'class' => 'form-control', 'id' => 'payment_due', 'name' => 'payment_due',  'value' => $sales_invoice['payment_due'], 'placeholder' =>'0', 'required' => 'true');
                                        echo form_input($data);                                             
                                        echo form_error('payment_due', '<p class="text-danger">', '</p>');
                                        ?>
                                </div>
								<label class="col-md-1 col-form-label text-dark text-right"><span class="text-danger">*</span>PPN</label>                                                                            
                                <div class="col-md-2">
                                    <select class="form-control" name="ppn" id="ppn">
                                        <option value="0" <?php if($sales_invoice['ppn'] == 0){ echo "selected"; } ?>>NON</option>
                                        <option value="1" <?php if($sales_invoice['ppn'] == 1){ echo "selected"; } ?>>PPN</option>
                                        <option value="2" <?php if($sales_invoice['ppn'] == 2){ echo "selected"; } ?>>FINAL</option>
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
                                    <textarea class="form-control" rows="3" name="information" id="information" placeholder="Silahkan isi keterangan bila ada..."><?php echo $sales_invoice['information']; ?></textarea>
                                </div>
                            </div>
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
                                        <th width="150px"><span class="text-danger">*</span>HARGA</th>
                                        <th width="180px"><span class="text-danger">*</span>DISKON (%)</th>
                                        <th width="150px"><span class="text-danger">*</span>GUDANG</th>
                                        <th width="180px">TOTAL</th>  
                                    </tr>
                                </thead>
                                <tbody data-repeater-list="product">
                                    <?php if($sales_invoice['do_status'] == 0): ?>
                                        <tr data-repeater-item style="text-align:center; display:none;">
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
                                                <?php 
                                                    // $data = array('type' => 'text', 'class' => 'form-control text-right price', 'name' => 'price', 'placeholder' => 'Harga'); 
                                                    // echo form_input($data);                                             
                                                    // echo form_error('price', '<p class="text-danger">', '</p>');
                                                ?>
                                                <select class="form-control text-right price" name="price">
                                                </select>
                                            </td>
                                            <td>
                                                <?php
                                                    $data = array('type' => 'text', 'class' => 'form-control text-right disc_product', 'name' => 'disc_product', 'placeholder' => '0',  'value' => set_value('discount_p')); 
                                                    echo form_input($data);                                             
                                                    echo form_error('disc_product', '<p class="text-danger">', '</p>');
                                                ?>
                                            </td>
                                            <td>
                                                <form>
                                                    <select class="form-control warehouse" name="warehouse_id">
                                                    </select>
                                                </form>
                                            </td>
                                            <td>
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control text-right total', 'name' => 'total',  'value' => set_value('total'), 'required' => 'true', 'readonly' => 'true'); 
                                                    echo form_input($data);                                             
                                                    echo form_error('total', '<p class="text-danger">', '</p>');
                                                ?>
                                            </td>
                                            <td>
                                                <label class="col-form-label"><a data-repeater-delete href="javascript:void(0);" class="text-danger text-center kt-font-bold delete_product"><i class="fa fa-times"></i></a></label>
                                            </td>
                                        </tr>
                                        <?php foreach($sales_invoice_detail AS $info): ?>
                                        <tr data-repeater-item style="text-align:center;">
                                            <input type="hidden" name="sales_invoice_detail_id" value="<?php echo $info['id']; ?>">
                                            <td>
                                                <div class="typeahead">
                                                    <?php  
                                                        $data = array('type' => 'text', 'class' => 'form-control product_input', 'placeholder' => 'Silahkan isi Kode/Nama Produk...', 'value' => $info['name_p'], 'required' => 'true'); 
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
                                                <?php 
                                                    // $data = array('type' => 'text', 'class' => 'form-control text-right price', 'name' => 'price', 'value' => number_format($info['price'], 0, '.', ',') ,'required' => 'true'); 
                                                    // echo form_input($data);                                             
                                                    // echo form_error('price', '<p class="text-danger">', '</p>');
                                                    $where=array(
                                                        'product_code'	=> $info['product_code'],
                                                        'unit_id'		=> $info['unit_id'],            
                                                    );
                                                    $sellprice = $this->crud->get_where('sellprice', $where)->row_array();
                                                ?>
                                                <select class="form-control text-right update_price price" name="price" required>
                                                    <?php $sellprice_list = []; ?>
                                                    <?php for($i=1; $i<=5 ; $i++): ?>                                                        
                                                        <option value="<?php echo $sellprice['price_'.$i]; ?>" class="H<?php echo $i; ?>" <?php if($info['price'] == $sellprice['price_'.$i]) { echo 'selected'; } ?>><?php echo 'H'.$i.' | '.number_format($sellprice['price_'.$i], 0, '.', ','); ?></option>
                                                    <?php $sellprice_list[] = $info['price_'.$i]; endfor;?>
                                                    <?php if(!in_array($info['price'], $sellprice_list)): ?>
                                                        <option value="<?php echo $info['price']; ?>" <?php echo 'selected'; ?>><?php echo number_format($info['price'], 2, '.', ','); ?></option>
                                                    <?php endif; ?>                                                    
                                                </select>
                                            </td>
                                            <td>
                                                <?php
                                                    $data = array('type' => 'text', 'class' => 'form-control text-right disc_product', 'name' => 'disc_product', 'placeholder' => '0',  'value' => $info['disc_product'], 'required' => 'true'); 
                                                    echo form_input($data);                                             
                                                    echo form_error('disc_product', '<p class="text-danger">', '</p>');
                                                ?>
                                            </td>
                                            <td>
                                                <?php $warehouses = $this->sales->get_warehouse($info['code_p'], $info['id_u']); ?>
                                                <select class="form-control warehouse" name="warehouse_id" required>
                                                    <?php foreach($warehouses As $warehouse): ?>
														<?php if($warehouse['stock'] > 0):?>
                                                        	<option value="<?php echo $warehouse['id_w']; ?>" <?php if($info['id_w'] == $warehouse['id_w']) { echo 'selected'; } ?>><?php echo $warehouse['code_w']." | ".number_format($warehouse['stock'], 2, '.', ','); ?></option>
														<?php else: ?>
															<?php continue; ?>
														<?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control text-right total', 'name' => 'total',  'value' => number_format($info['total'], 0, '.', ','), 'required' => 'true', 'readonly' => 'true'); 
                                                    echo form_input($data);                                             
                                                    echo form_error('total', '<p class="text-danger">', '</p>');
                                                ?>
                                            </td>
                                            <td>
                                                <label class="col-form-label"><a data-repeater-delete href="javascript:void(0);" class="text-danger text-center kt-font-bold delete_product"><i class="fa fa-times"></i></a></label>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <?php foreach($sales_invoice_detail AS $info): ?>
                                        <tr data-repeater-item style="text-align:center;">
                                            <input type="hidden" name="sales_invoice_detail_id" value="<?php echo $info['id']; ?>">
                                            <td>
                                                <div class="typeahead">
                                                    <?php  
                                                        $data = array('type' => 'text', 'class' => 'form-control product_input', 'placeholder' => 'Silahkan isi Kode/Nama Produk...', 'value' => $info['name_p'], 'required' => 'true' ,'readonly' => true); 
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
                                                    $data = array('type' => 'text', 'class' => 'form-control text-right qty', 'name' => 'qty', 'placeholder' => 'Qty',  'value' => $info['qty'], 'required' => 'true', 'readonly' => true); 
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
                                                <select class="form-control unit" name="unit_id" required readonly disabled>
                                                    <?php foreach($units AS $unit): ?>
                                                        <option value="<?php echo $unit['id_u']; ?>" <?php if($info['id_u'] == $unit['id_u']) { echo 'selected'; } ?>><?php echo $unit['code_u']; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <?php 
                                                    // $data = array('type' => 'text', 'class' => 'form-control text-right price', 'name' => 'price', 'value' => number_format($info['price'], 0, '.', ',') ,'required' => 'true'); 
                                                    // echo form_input($data);                                             
                                                    // echo form_error('price', '<p class="text-danger">', '</p>');
                                                    $where=array(
                                                        'product_code'	=> $info['product_code'],
                                                        'unit_id'		=> $info['unit_id'],            
                                                    );
                                                    $sellprice = $this->crud->get_where('sellprice', $where)->row_array();
                                                ?>
                                                <select class="form-control text-right update_price price" name="price" required>
                                                    <?php for($i=1; $i<=5 ; $i++): ?>
                                                        <option value="<?php echo $sellprice['price_'.$i]; ?>" class="H<?php echo $i; ?>" <?php if($info['price'] == $sellprice['price_'.$i]) { echo 'selected'; } ?>><?php echo 'H'.$i.' | '.number_format($sellprice['price_'.$i], 0, '.', ','); ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <?php
                                                    $data = array('type' => 'text', 'class' => 'form-control text-right disc_product', 'name' => 'disc_product', 'placeholder' => '0',  'value' => $info['disc_product'], 'required' => 'true'); 
                                                    echo form_input($data);                                             
                                                    echo form_error('disc_product', '<p class="text-danger">', '</p>');
                                                ?>
                                            </td>
                                            <td>
                                                <?php $warehouses = $this->sales->get_warehouse($info['code_p'], $info['id_u']); ?>
                                                <select class="form-control warehouse" name="warehouse_id" required readonly disabled>
                                                    <?php foreach($warehouses As $warehouse): ?>
                                                        <option value="<?php echo $warehouse['id_w']; ?>" <?php if($info['id_w'] == $warehouse['id_w']) { echo 'selected'; } ?>><?php echo $warehouse['code_w']." | ".number_format($warehouse['stock'], 2, '.', ','); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control text-right total', 'name' => 'total',  'value' => number_format($info['total'], 0, '.', ','), 'required' => 'true', 'readonly' => 'true'); 
                                                    echo form_input($data);                                             
                                                    echo form_error('total', '<p class="text-danger">', '</p>');
                                                ?>
                                            </td>                                            
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>                                   
                                </tbody>
                            </table>
                            <?php if($sales_invoice['do_status'] == 0): ?>
                                <button type="button" class="btn btn-sm btn-primary btn-block" id="add_product" data-repeater-create><i class="fa fa-plus"></i> Tambah Baris Produk</button>
                            <?php endif; ?>
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
                                        $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_product', 'name' => 'total_product',  'value' => number_format($sales_invoice['total_product'], 0, '.', ','), 'required' => 'true', 'readonly' => 'true'); 
                                        echo form_input($data);                                             
                                        echo form_error('total_product', '<p class="text-danger">', '</p>');
                                        ?>
                                </div>
                                <label class="col-md-3 col-form-label kt-font-dark text-right">TOTAL KUANTITAS</label>
                                <div class="col-md-3">
                                    <?php
                                        $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_qty', 'name' => 'total_qty',  'value' => number_format($sales_invoice['total_qty'], 0, '.', ','), 'required' => 'true', 'readonly' => 'true'); 
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
                                        $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'subtotal', 'name' => 'subtotal',  'value' => number_format($sales_invoice['total_price'], 0, '.', ','), 'required' => 'true', 'readonly' => 'true'); 
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
                            <div class="form-group row" id="cash_ledger_form">
                                <div class="col-md-4">
                                    <select class="form-control cash_ledger_input" id="from_cl_type" name="from_cl_type" required>
                                        <option value="1" <?php if($sales_invoice['cl_type'] == 1) { echo "selected"; } ?>>KAS</option>
                                        <option value="2" <?php if($sales_invoice['cl_type'] == 2) { echo "selected"; } ?>>BANK</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <input type="hidden" id="account_id" value="<?php echo $sales_invoice['account_id']; ?>">
                                    <select class="form-control cash_ledger_input" id="from_account_id" name="from_account_id" required></select>
                                </div>
                            </div>
                            <div class="form-group row" id="dp_checklist_form">
                                <div class="col-md-4">
                                </div>
                                <div class="col-md-2">
                                    <span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--success">
                                    <label>
                                    <input type="checkbox" id="dp_checklist" value="1" <?php if($sales_invoice['down_payment'] > 0) { echo "checked"; } ?>>
                                    <span></span>
                                    </label>
                                    </span>                                                                        
                                </div>
                                <label class="col-md-6 col-form-label text-dark">Membayar Uang Muka Penjualan</label>
                            </div>
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
                                        $data = array('type' => 'text', 'class' => 'form-control text-right', 'name' => 'discount_p', 'id' => 'discount_p', 'placeholder' => 'Diskon (%)', 'value' => number_format($sales_invoice['discount_p'], 2, '.', ','), 'required' => 'true');
                                        echo form_input($data);                                             
                                        echo form_error('discount_p', '<p class="text-danger">', '</p>');                                            
                                        ?>
                                    <small>*Diskon Persen (%)</small>
                                </div>
                                <div class="col-md-8" id="discount_amount">
                                    <?php 
                                        $data = array('type' => 'text',  'class' => 'form-control text-right', 'name' => 'discount_rp', 'id' => 'discount_rp', 'placeholder' => 'Diskon (Rupiah)', 'value' => number_format($sales_invoice['discount_rp'], 0, '.', ','), 'required' => 'true'); 
                                        echo form_input($data);                                             
                                        echo form_error('discount_rp', '<p class="text-danger">', '</p>');
                                        ?>
                                    <small>*Diskon Rupiah (Rp)</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label kt-font-dark text-right">Biaya Pengiriman</label>
                                <div class="col-md-8">
                                    <?php
                                        $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'delivery_cost', 'name' => 'delivery_cost',  'value' => number_format($sales_invoice['delivery_cost'],'0','.',','), 'required' => 'true'); 
                                        echo form_input($data);
                                        echo form_error('delivery_cost', '<p class="text-danger">', '</p>');
                                    ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label kt-font-dark text-right"><strong>GRANDTOTAL</strong></label>
                                <div class="col-md-8">
                                    <?php
                                        $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'grandtotal', 'name' => 'grandtotal',  'value' => number_format($sales_invoice['grandtotal'], 0, '.', ','), 'required' => 'true', 'readonly' => 'true'); 
                                        echo form_input($data);                                             
                                        echo form_error('grandtotal', '<p class="text-danger">', '</p>');
                                        ?>
                                </div>
                            </div>			
                            <div class="form-group row" id="downpayment_form">
                                <label class="col-md-4 col-form-label text-dark">Uang Muka Pembayaran</label>
                                <div class="col-md-8">
                                    <?php 
                                        $data = array('type' => 'text', 'class' => 'form-control text-right', 'name' => 'down_payment', 'id' => 'down_payment', 'placeholder' => 'Silahkan Isi Uang Muka Pembayaran (DP)...', 'value' => 0, 'required' => 'true');
                                        echo form_input($data);                                             
                                        echo form_error('down_payment', '<p class="text-danger">', '</p>');
                                        ?>
                                </div>
                            </div>				
                            <div class="form-group row" id="credit_method">
                                <label class="col-md-4 col-form-label text-dark text-right"><strong>Piutang Penjualan</strong></label>
                                <div class="col-md-8">
                                    <?php 
                                        $data = array('type' => 'text', 'class' => 'form-control text-right', 'name' => 'account_payable', 'id' => 'account_payable', 'placeholder' => 'Hutang Dagang...', 'value' => number_format($sales_invoice['account_payable'], 0, '.',','), 'readonly' => 'true', 'required' => 'true');
                                        echo form_input($data);                                             
                                        echo form_error('account_payable', '<p class="text-danger">', '</p>');
                                    ?>
                                    <small class="text-danger" id="account_payable_message">*Mohon Maaf, piutang penjualan tidak boleh minus</small>
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
                                <button type="submit" id="btn_save" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-save"></i> SIMPAN</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Portlet--> 
        <?php echo form_close() ?>        
    </div>    
    <!-- end:: Content -->
    <!--begin::Verify Module Password Modal-->
	<div class="modal fade" id="verify_module_password_modal" tabindex="-1" role="dialog" aria-labelledby="closeModal" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<?php echo form_open('', array('class' => 'form-horizontal', 'id' => 'verify_module_password_form', 'autocomplete' => 'off')); ?>
				<input type="hidden" id="module_url" name="module_url"> <input type="hidden" id="action_module" name="action">
				<div class="modal-header">
					<h5 class="modal-title">Verifikasi Password</h5>
				</div>
				<div class="modal-body">
                    <div id="veryfy_message">
					</div>
					<div class="form-group">
						<input type="password" name="verifypassword" id="verifypassword" class="form-control" placeholder="Silahkan isi Password untuk melanjutkan...">
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success form-control"><i class="fa fa-save"></i>  Verifikasi</button>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
	<!--end::End Verify Module Password Modal-->        
</div>