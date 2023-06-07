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
        <?php echo form_open_multipart('', ['class' => 'repeater', 'autocomplete'=>'off', 'target' => '_blank']); ?>            
            <!--begin::Portlet-->
            <div class="kt-portlet">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            Informasi | No. Transaksi <span class="kt-font-bold kt-font-success"><?php echo $sales_invoice['invoice']; ?></span>
                            <input type="hidden" name="sales_invoice_id" value="<?php echo $sales_invoice['id']; ?>">
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label text-dark">TGL. FAKTUR</label>
                                <div class="col-md-9">
                                    <?php 
                                        $data = array('type' => 'text', 'class' => 'form-control', 'value' => date('d-m-Y', strtotime($sales_invoice['date'])), 'readonly' => 'true'); 
                                        echo form_input($data);
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label text-dark text-right">SALES</label>
                                <div class="col-md-9">
                                    <?php 
                                        $data = array('type' => 'text', 'class' => 'form-control', 'value' => $sales_invoice['code_s'].' | '.$sales_invoice['name_s'], 'readonly' => 'true'); 
                                        echo form_input($data);
                                    ?>
                                </div>
                            </div>                                                                
                        </div>                            
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label text-dark">PELANGGAN</label>
                                <div class="col-md-9">
                                    <?php 
                                        $data = array('type' => 'text', 'class' => 'form-control', 'value' => $sales_invoice['code_c'].' | '.$sales_invoice['name_c'], 'readonly' => 'true'); 
                                        echo form_input($data);
                                    ?>
                                </div>
                            </div>                                                                
                        </div>                            
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label text-dark text-right">KETERANGAN</label>
                                <div class="col-md-9">
                                    <textarea class="form-control" rows="3" name="information" id="information" readonly><?php echo $sales_invoice['information']; ?></textarea>
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
                            Daftar Produk <small class="text-danger">*Silahkan pilih produk yang ingin dicetak untuk surat jalan</small>
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body">                    
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-sm" id="product_table">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th></th>
                                        <th><span class="text-danger">*</span>PRODUK</th>
                                        <th width=100px;><span class="text-danger">*</span>QTY</th>
                                        <th width=100px;><span class="text-danger">*</span>SATUAN</th>                                        
                                        <th width=150px;><span class="text-danger">*</span>GUDANG</th>                                        
                                    </tr>
                                </thead>
                                <tbody data-repeater-list="product">                                    
                                    <?php foreach($sales_invoice_detail AS $info): ?>
                                    <tr data-repeater-item style="text-align:center;">
                                        <td>
                                            <label class="col-form-label"><input type="checkbox" name="sales_invoice_detail_id" value="<?php echo $info['id']; ?>"></label>                                            
                                        </td>                                        
                                        <td>
                                            <div class="typeahead">
                                                <?php  
                                                    $data = array('type' => 'text', 'class' => 'form-control product_input', 'placeholder' => 'Silahkan isi Kode/Nama Produk...', 'value' => $info['name_p'], 'readonly' => 'true', 'disabled' => 'true'); 
                                                    echo form_input($data);
                                                    ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php 
                                                $data = array('type' => 'text', 'class' => 'form-control text-right qty', 'name' => 'qty', 'placeholder' => 'Qty',  'value' => $info['qty'], 'readonly' => 'true', 'disabled' => 'true');
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
                                            <select class="form-control unit" name="unit_id" readonly disabled>
                                                <?php foreach($units AS $unit): ?>
                                                    <option value="<?php echo $unit['id_u']; ?>" <?php if($info['id_u'] == $unit['id_u']) { echo 'selected'; } ?>><?php echo $unit['code_u']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <?php $warehouses = $this->sales->get_warehouse($info['code_p'], $info['id_u']); ?>
                                            <select class="form-control warehouse" name="warehouse_id" readonly disabled>
                                                <?php foreach($warehouses As $warehouse): ?>
                                                    <option value="<?php echo $warehouse['id_w']; ?>" <?php if($info['id_w'] == $warehouse['id_w']) { echo 'selected'; } ?>><?php echo $warehouse['code_w']." | ".number_format($warehouse['stock'], 2, '.', ','); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="kt-portlet__foot">
                    <div class="kt-form__actions">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="<?php echo site_url('sales/invoice/detail/'.encrypt_custom($sales_invoice['id'])); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>                                        
                            </div>
                            <div class="col-md-6">
                                <button type="submit" id="btn_save" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-print"></i> CETAK</button>
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