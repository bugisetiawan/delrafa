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
        <?php if($this->session->flashdata('success')) :?>
        <div class="alert alert-success fade show" role="alert">
            <div class="alert-icon"><i class="flaticon2-checkmark"></i></div>
            <div class="alert-text"><?php echo $this->session->flashdata('success'); ?></div>
            <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="la la-close"></i></span>
                </button>
            </div>
        </div>
        <?php elseif($this->session->flashdata('error')): ?>
        <div class="alert alert-danger fade show" role="alert">
            <div class="alert-icon"><i class="flaticon-warning"></i></div>
            <div class="alert-text">
            <?php echo $this->session->flashdata('error'); ?><br>
            <?php if($this->session->flashdata('min_product')) :?>
                <?php foreach($this->session->flashdata('min_product') AS $min_product): ?>
                    <?php echo $min_product['code_p'].' | '.$min_product['name_p']; ?><br>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
            <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="la la-close"></i></span>
                </button>
            </div>
        </div>
        <?php endif;?>
        <!--begin::Portlet-->
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                    <?php $do_status = ($purchase_return['do_status'] == 1) ? "<span class='text-success'><i class='fa fa-check'></i></span>" : "<span class='text-danger'><i class='fa fa-times'></i></span>"; ?>
                    Informasi | No. Transaksi <span class="kt-font-bold kt-font-success"><?php echo $purchase_return['code']; ?></span> | Cetak DO : <?php echo $do_status; ?> | Opt. <?php echo $purchase_return['name_e']; ?>
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                            <a href="<?php echo base_url('purchase/return'); ?>" class="btn btn-outline-primary btn-elevate btn-icon"
                                data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Kembali ke Daftar Retur Pembelian">
                                <i class="fa fa-arrow-left"></i>
                            </a>             
                            <?php if($purchase_return['do_status'] != 1): ?>
                            <button class="btn btn-icon btn-outline-warning" id="update_purchase_return_btn"
                                data-link="<?php echo site_url('purchase/return/update/'.encrypt_custom($purchase_return['id'])); ?>"  data-id="<?php echo $purchase_return['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Perbarui Retur Pembelian">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-icon btn-outline-danger" id="delete_purchase_return_btn"
                                data-id="<?php echo $purchase_return['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Hapus Retur Pembelian">
                                <i class="fa fa-trash"></i>
                            </button>
                            <button class="btn btn-outline-primary btn-elevate" id="create_purchase_return_do_btn" 
                                data-id="<?php echo $purchase_return['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Cetak DO">
                                <i class="fa fa-print"></i>
                                Cetak DO
                            </button>
                            <?php else: ?>
                            <button class="btn btn-outline-danger btn-elevate" id="cancel_purchase_return_do_btn"
                                data-link="<?php echo site_url('transaction/Purchase/cancel_purchase_return_do/'.encrypt_custom($purchase_return['id'])); ?>" data-id="<?php echo $purchase_return['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Batal DO">
                                <i class="fa fa-times"></i>
                                Batal DO
                            </button>
                            <?php endif; ?>
                            <a href="<?php echo base_url('purchase/return/print/'.encrypt_custom($purchase_return['id'])); ?>" target="_blank" class="btn btn-outline-success btn-elevate btn-icon"
                                data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Cetak Retur Pembelian"><b><i class="fa fa-print"></i></b>
                            </a>
                        </div>
                    </div>
                </div> 
            </div>                                            
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group row">
                            <label class="col-md-5 col-form-label kt-font-dark">TGL. RETUR PEMBELIAN</label>
                            <div class="col-md-7">												
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'date', 'name' => 'date',  'value' => date('d-m-Y', strtotime($purchase_return['date'])), 'readonly' => 'true'); 
                                    echo form_input($data);
                                ?>
                            </div>
                        </div>                                        
                    </div>    
                    <div class="col-md-4">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label kt-font-dark text-right">SUPPLIER</label>
                            <div class="col-md-9">                                                
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'supplier_code', 'name' => 'supplier_code',  'value' => $purchase_return['name_s'], 'readonly' => 'true'); 
                                    echo form_input($data);                                                                                         
                                ?>
                            </div>											
                        </div>                                        
                    </div>
                    <div class="col-md-4">
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label kt-font-dark text-right">JENIS RETUR</label>
                            <div class="col-md-8">                                        
                                <?php 
                                    $method = ($purchase_return['method'] == 1) ? "TIDAK POTONG PEMBELIAN" : "POTONG PEMBELIAN";
                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'method', 'name' => 'method',  'value' => $method, 'readonly' => 'true'); 
                                    echo form_input($data);
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
                        Daftar Produk
                    </h3>
                </div>						                       
            </div>
            <div class="kt-portlet__body">
                <?php 
                    $data = array('type' => 'hidden', 'class' => 'form-control', 'id' => 'purchase_return_id', 'name' => 'purchase_return_id',  'value' => $purchase_return['id'], 'readonly' => 'true'); 
                    echo form_input($data);                                                                                         
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <!--begin: Datatable -->
                        <table class="table table-bordered table-hover table-checkable" id="datatable_detail">
                            <thead>
                                <tr style="text-align:center;">
                                    <th>NO.</th>
                                    <th>KODE</th>
                                    <th>NAMA</th>
                                    <th>QTY</th> 
                                    <th>SATUAN</th>                                    
                                    <th>HARGA</th>
                                    <th>GUDANG</th>
                                    <th>TOTAL</th>
                                    <th>KETERANGAN</th>
                                </tr>
                            </thead>
                            <tbody>                                                                                                            
                            </tbody>
                        </table>
                        <!--end: Datatable -->                                                                           
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
                                    $data = array('type' => 'number', 'min' => 0, 'class' => 'form-control text-right', 'id' => 'total_product', 'name' => 'total_product',  'value' => $purchase_return['total_product'], 'required' => 'true', 'readonly' => 'true'); 
                                    echo form_input($data);                                             
                                    echo form_error('total_product', '<p class="text-danger">', '</p>');
                                ?>
                            </div>
                            <label class="col-md-3 col-form-label kt-font-dark text-right">TOTAL KUANTITAS</label>
                            <div class="col-md-3">
                                <?php
                                    $data = array('type' => 'number', 'min' => 0, 'class' => 'form-control text-right', 'id' => 'total_qty', 'name' => 'total_qty',  'value' => $purchase_return['total_qty'], 'required' => 'true', 'readonly' => 'true'); 
                                    echo form_input($data);                                             
                                    echo form_error('total_qty', '<p class="text-danger">', '</p>');
                                ?>
                            </div>                                        
                        </div>                                        
                    </div>
                    <div class="col-md-6">                            
                        <?php if($purchase_return['method'] == 1): ?>
                        <div class="form-group row"  id="cash_ledger_form">
                            <div class="col-md-4">
                                <select class="form-control cash_ledger_input" id="from_cl_type" name="from_cl_type" required>
                                    <option value="1">KAS BESAR</option>
                                    <option value="2">KAS KECIL</option>
                                    <option value="3">KAS BANK</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <select class="form-control cash_ledger_input" id="from_account_id" name="from_account_id" required></select>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label kt-font-dark text-right">TOTAL RETUR</label>
                            <div class="col-md-9">
                                <?php
                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_return', 'name' => 'total_return',  'value' => number_format($purchase_return['total_return'], 2, ".", ","), 'required' => 'true', 'readonly' => 'true'); 
                                    echo form_input($data);                                             
                                    echo form_error('total_return', '<p class="text-danger">', '</p>');
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Portlet-->
        <!--begin::Portlet-->
        <?php if($purchase_return['method'] == 2): ?>
        <div class="kt-portlet choose_invoice">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Informasi Potong Pembelian
                    </h3>
                </div>
            </div>                                                
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-md-4"> 
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label kt-font-dark">NO. FAKTUR</label>
                            <div class="col-md-9">  
                                <?php
                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'invoice', 'name' => 'invoice',  'value' => $purchase_return['invoice'], 'readonly' => 'true'); 
                                    echo form_input($data);                                             
                                    echo form_error('invoice', '<p class="text-danger">', '</p>');
                                ?>                                              
                            </div>
                        </div>                                       
                    </div>
                    <div class="col-md-4">
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label kt-font-dark text-right">NILAI FAKTUR</label>
                            <div class="col-md-8">
                                <?php
                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'account_payable', 'name' => 'account_payable',  'value' => number_format($purchase_return['account_payable'], 0, '.', ','), 'readonly' => 'true'); 
                                    echo form_input($data);                                             
                                    echo form_error('account_payable', '<p class="text-danger">', '</p>');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">                                                                                                                
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label kt-font-dark text-right"><strong>SISA FAKTUR</strong></label>
                            <div class="col-md-8">
                                <?php
                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'grandtotal', 'name' => 'grandtotal',  'value' => number_format($purchase_return['grandtotal'], 0, '.', ','), 'readonly' => 'true'); 
                                    echo form_input($data);                                             
                                    echo form_error('grandtotal', '<p class="text-danger">', '</p>');
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>                                                                         
        </div>
        <!--end::Portlet-->
        <?php endif; ?>
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
                    <div class="form-group">
                        <input type="password" name="verifypassword" id="verifypassword" class="form-control" placeholder="Silahkan isi Password untuk melanjutkan...">
                    </div>
                </div>
                <div class="modal-footer">																					
                    <button type="button" class="btn btn-danger form-control" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
                    <button type="submit" class="btn btn-success form-control"><i class="fa fa-save"></i>  Verifikasi</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>    
    <!--end::End Verify Module Password Modal-->
</div>