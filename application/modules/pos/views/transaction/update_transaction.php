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
                            Informasi | No. Transaksi <span class="kt-font-bold kt-font-success"><?php echo $pos['invoice']; ?></span>
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <input type="hidden" name="pos_id" value="<?php echo encrypt_custom($pos['id']); ?>">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="kt-font-dark">TANGGAL & WAKTU TRANSAKSI</label>
                                <?php
                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'date', 'name' => 'date',  'value' => date('d-m-Y', strtotime($pos['date'])) .' | '. $pos['time'], 'readonly' => 'true');
                                    echo form_input($data);
                                ?>                                                                                                                
                            </div>
                        </div>                   
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="kt-font-dark">PELANGGAN</label>
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control', 'value' => $pos['name_c'], 'readonly' => 'true');
                                    echo form_input($data);
                                ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="kt-font-dark">KASIR</label>
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control', 'value' => $pos['code_e'].' | '.$pos['name_e'], 'readonly' => 'true');
                                    echo form_input($data);
                                ?>
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
                            <small class="kt-font-primary" id="add_product" data-repeater-create>*<b>ENTER / Klik Disini</b> untuk menambah baris produk</small>
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
                                        <th width=100px;><span class="text-danger">*</span>QTY</th>
                                        <th width=100px;><span class="text-danger">*</span>SATUAN</th>
                                        <th width=120px;><span class="text-danger">*</span>HARGA</th>
                                        <th width=150px;><span class="text-danger">*</span>GUDANG</th>
                                        <th width=150px;>TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody data-repeater-list="product">
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
                                                $data = array('type' => 'text', 'class' => 'form-control text-right price', 'name' => 'price', 'placeholder' => 'Harga'); 
                                                echo form_input($data);                                             
                                                echo form_error('price', '<p class="text-danger">', '</p>');
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
                                    <?php foreach($pos_detail AS $info): ?>
                                    <tr data-repeater-item style="text-align:center;">
                                        <input type="hidden" name="pos_detail_id" value="<?php echo $info['id']; ?>">
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
                                                $data = array('type' => 'text', 'class' => 'form-control text-right price', 'name' => 'price', 'value' => number_format($info['price'], 0, '.', ',') ,'required' => 'true'); 
                                                echo form_input($data);                                             
                                                echo form_error('price', '<p class="text-danger">', '</p>');
											?>
                                        </td>
                                        <td>
											<?php $warehouses = $this->sales->get_warehouse($info['code_p'], $info['id_u']); ?>
                                            <select class="form-control warehouse" name="warehouse_id" required>
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
                                        <td>
                                            <label class="col-form-label"><a data-repeater-delete href="javascript:void(0);" class="text-danger text-center kt-font-bold delete_product"><i class="fa fa-times"></i></a></label>
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
                                <label class="col-md-3 col-form-label kt-font-dark text-right">TOTAL PRODUK</label>
                                <div class="col-md-3">
                                    <?php
                                        $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_product', 'name' => 'total_product',  'value' => number_format($pos['total_product'], 0, '.', ','), 'required' => 'true', 'readonly' => 'true'); 
                                        echo form_input($data);                                             
                                        echo form_error('total_product', '<p class="text-danger">', '</p>');
                                        ?>
                                </div>
                                <label class="col-md-3 col-form-label kt-font-dark text-right">TOTAL KUANTITAS</label>
                                <div class="col-md-3">
                                    <?php
                                        $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_qty', 'name' => 'total_qty',  'value' => number_format($pos['total_qty'], 0, '.', ','), 'required' => 'true', 'readonly' => 'true'); 
                                        echo form_input($data);                                             
                                        echo form_error('total_qty', '<p class="text-danger">', '</p>');
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
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label kt-font-dark text-right"><strong>GRANDTOTAL</strong></label>
                                <div class="col-md-8">
                                    <?php
                                        $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'grandtotal', 'name' => 'grandtotal',  'value' => number_format($pos['grandtotal'], 0, '.', ','), 'required' => 'true', 'readonly' => 'true'); 
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
                                <a href="<?php echo site_url('pos/transaction'); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>                                        
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
</div>
<!-- end:: Content -->
</div>