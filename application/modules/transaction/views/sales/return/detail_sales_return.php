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
        <div class="row">
            <div class="col-lg-12"> 
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
                        <div class="alert-text"><?php echo $this->session->flashdata('error'); ?></div>
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
                            <?php $do_status = ($sales_return['do_status'] == 1) ? "<span class='text-success'><i class='fa fa-check'></i></span>" : "<span class='text-danger'><i class='fa fa-times'></i></span>"; ?>
                            Informasi | No. Transaksi <span class="kt-font-bold kt-font-success"><?php echo $sales_return['code']; ?></span> | Cetak DO : <?php echo $do_status; ?> | Opt. <?php echo $sales_return['name_e']; ?>
                            </h3>
						</div>
						<div class="kt-portlet__head-toolbar">
                            <div class="kt-portlet__head-wrapper">
                                <div class="kt-portlet__head-actions"> 
                                    <a href="<?php echo base_url('sales/return'); ?>" class="btn btn-outline-primary btn-elevate btn-icon"
                                        data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Kembali ke Daftar Retur Penjualan">
                                        <i class="fa fa-arrow-left"></i>
                                    </a>                            
                                    <?php if($sales_return['do_status'] != 1): ?>
                                    <a class="btn btn-icon btn-outline-warning"
                                        href="<?php echo site_url('sales/return/update/'.encrypt_custom($sales_return['id'])); ?>"  data-id="<?php echo $sales_return['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Perbarui Retur Penjualan">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <button class="btn btn-icon btn-outline-danger" id="delete_sales_return_btn"
                                        data-id="<?php echo $sales_return['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Hapus Retur Penjualan">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    <button class="btn btn-outline-primary btn-elevate" id="create_sales_return_do_btn" 
                                        data-id="<?php echo $sales_return['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Cetak DO">
                                        <i class="fa fa-print"></i>
                                        Cetak DO
                                    </button>
                                    <?php else: ?>
                                    <!-- <button class="btn btn-icon btn-outline-warning" id="update_sales_invoice_btn"
                                        data-link="<?php echo site_url('sales/invoice/update/'.encrypt_custom($sales_return['id'])); ?>"  data-id="<?php echo $sales_return['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Perbarui Retur Penjualan">
                                        <i class="fa fa-edit"></i>
                                    </button> -->
                                    <button class="btn btn-outline-danger btn-elevate" id="cancel_sales_return_do_btn"
                                        data-link="<?php echo site_url('transaction/Sales/cancel_sales_return_do/'.encrypt_custom($sales_return['id'])); ?>" data-id="<?php echo $sales_return['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Batal DO">
                                        <i class="fa fa-times"></i>
                                        Batal DO
                                    </button>
                                    <?php endif; ?>                                
                                    <a href="<?php echo base_url('sales/return/print/'.encrypt_custom($sales_return['id'])); ?>" target="_blank" class="btn btn-outline-success btn-elevate btn-icon"
                                        data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Cetak Retur Penjualan"><b><i class="fa fa-print"></i></b>
                                    </a>
                                </div>
                            </div>
						</div> 
                    </div>                                            
                    <div class="kt-portlet__body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <label class="col-md-5 col-form-label kt-font-dark">TGL. RETUR PENJUALAN</label>
                                    <div class="col-md-7">												
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'date', 'name' => 'date',  'value' => date('d-m-Y', strtotime($sales_return['date'])), 'readonly' => 'true'); 
                                            echo form_input($data);
                                        ?>
                                    </div>
                                </div>                                        
                            </div>    
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label kt-font-dark text-right">PELANGGAN</label>
                                    <div class="col-md-9">                                                
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'customer_code', 'name' => 'customer_code',  'value' => $sales_return['name_c'], 'readonly' => 'true'); 
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
                                            $method = ($sales_return['method'] == 1) ? "TIDAK POTONG PENJUALAN" : "POTONG PENJUALAN";
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
                            $data = array('type' => 'hidden', 'class' => 'form-control', 'id' => 'sales_return_id', 'name' => 'sales_return_id',  'value' => $sales_return['id'], 'readonly' => 'true'); 
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
                                            $data = array('type' => 'number', 'min' => 0, 'class' => 'form-control text-right', 'id' => 'total_product', 'name' => 'total_product',  'value' => $sales_return['total_product'], 'required' => 'true', 'readonly' => 'true'); 
                                            echo form_input($data);                                             
                                            echo form_error('total_product', '<p class="text-danger">', '</p>');
                                        ?>
                                    </div>
                                    <label class="col-md-3 col-form-label kt-font-dark text-right">TOTAL KUANTITAS</label>
                                    <div class="col-md-3">
                                        <?php
                                            $data = array('type' => 'number', 'min' => 0, 'class' => 'form-control text-right', 'id' => 'total_qty', 'name' => 'total_qty',  'value' => $sales_return['total_qty'], 'required' => 'true', 'readonly' => 'true'); 
                                            echo form_input($data);                                             
                                            echo form_error('total_qty', '<p class="text-danger">', '</p>');
                                        ?>
                                    </div>                                        
                                </div>                                        
                            </div>
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label kt-font-dark text-right">TOTAL RETUR</label>
                                    <div class="col-md-9">
                                        <?php
                                            $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_return', 'name' => 'total_return',  'value' => number_format($sales_return['total_return'],0,".",","), 'required' => 'true', 'readonly' => 'true'); 
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
                <div class="kt-portlet choose_invoice">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                <?php echo $title ?>
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
                                            $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'invoice', 'name' => 'invoice',  'value' => $sales_return['invoice'], 'readonly' => 'true'); 
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
                                            $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'account_payable', 'name' => 'account_payable',  'value' => number_format($sales_return['account_payable'],0,".",","), 'readonly' => 'true'); 
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
                                            $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'grandtotal', 'name' => 'grandtotal',  'value' => number_format($sales_return['grandtotal'],0,".",","), 'readonly' => 'true');
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
            </div>
        </div>
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