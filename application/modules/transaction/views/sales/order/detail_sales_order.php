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
                <div class="alert-text"><?php echo $this->session->flashdata('error'); ?></div>
                <div class="alert-close">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true"><i class="la la-close"></i></span>
                    </button>
                </div>
            </div>
		<?php endif;?>
        <?php if($sales_order['sales_order_status'] == 1): ?>
        <div class="alert alert-danger fade show" role="alert">
            <div class="alert-icon"><i class="flaticon-exclamation-2"></i></div>
            <div class="alert-text">
                MOHON PERHATIAN! Harap segera <b>MELAKUKAN PEMBUATAN FAKTUR PENJUALAN</b>, terima kasih.
            </div>
        </div>
        <?php endif; ?>        
        <!--begin::Portlet-->
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Informasi | No. Transaksi <span class="kt-font-bold kt-font-success"><?php echo $sales_order['invoice']; ?></span>
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                            <?php $link = ($this->agent->referrer() != "") ? urldecode($this->agent->referrer()) : site_url('sales'); ?>
                            <a href="<?php echo $link; ?>" class="btn btn-outline-primary btn-elevate btn-icon"
                                data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Kembali">
                                <i class="fa fa-arrow-left"></i>
                            </a>
                            <button type="button" class="btn btn-outline-warning btn-elevate btn-icon" id="update_sales_order_btn"
                                data-link="<?php echo site_url('sales/order/update/'.encrypt_custom($sales_order['id'])); ?>"  data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Perbarui Pemesanan Penjualan">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-elevate btn-icon" id="delete_sales_order_btn"
                                data-id="<?php echo $sales_order['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Hapus Pemesanan Penjualan">
                                <i class="la la-trash"></i>
                            </button>
                            <?php if($sales_order['sales_order_status'] == 1): ?>
                            <a href="<?php echo site_url('sales/order/print/'.encrypt_custom($sales_order['id'])); ?>" target="_blank" class="btn btn-outline-success btn-elevate btn-icon"
                                data-id="<?php echo $sales_order['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Cetak Pemesanan Penjualan">
                                <i class="fa fa-print"></i>
                            </a>                                
                            <?php endif; ?>                                    
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label kt-font-dark">TGL. PEMESANAN</label>
                            <div class="col-md-8">
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'date', 'name' => 'date',  'value' => date('d-m-Y', strtotime($sales_order['date'])), 'readonly' => 'true'); 
                                    echo form_input($data);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label kt-font-dark text-right">SALES</label>
                            <div class="col-md-8">
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'date', 'name' => 'date',  'value' => $sales_order['code_s'].' | '.$sales_order['name_s'], 'readonly' => 'true'); 
                                    echo form_input($data);
                                ?>
                            </div>
                        </div>                                                               
                    </div>                            
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label kt-font-dark">PELANGGAN</label>
                            <div class="col-md-8">
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'date', 'name' => 'date',  'value' => $sales_order['code_c'].' | '.$sales_order['name_c'], 'readonly' => 'true'); 
                                    echo form_input($data);
                                ?>
                            </div>
                        </div>                                                                 
                    </div>                            
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label kt-font-dark text-right">PENGAMBILAN PESANAN</label>
                            <div class="col-md-8">
                                <?php 
                                    if($sales_order['taking_method'] == 1)
                                    {
                                        $taking = "LANGSUNG";
                                    }
                                    else
                                    {
                                        $taking = "PENGIRIMAN";
                                    }
                                    $data = array('type' => 'text', 'class' => 'form-control', 'value' => $taking,'readonly' => 'true'); 
                                    echo form_input($data);
                                ?>                                    
                            </div>
                        </div> 
                        <?php if($sales_order['taking_method'] != 1): ?>
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label kt-font-dark text-right">ALAMAT PENGIRIMAN</label>
                            <div class="col-md-8">
                                <?php 
                                    $data = array('class' => 'form-control', 'rows' => 3, 'id' => 'delivery_address', 'name' => 'delivery_address',  'value' => $sales_order['delivery_address'], 'readonly' => 'true'); 
                                    echo form_textarea($data);                                             
                                    echo form_error('delivery_address', '<p class="text-danger">', '</p>');
                                ?>
                            </div>
                        </div>                                
                        <?php endif; ?>
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
                <div class="row">
                    <div class="col-md-12">
                        <!--begin: Datatable -->
                        <?php 
                            $data = array('type' => 'hidden', 'class' => 'form-control', 'id' => 'sales_order_id', 'name' => 'sales_order_id',  'value' => $sales_order['id'], 'readonly' => 'true');
                            echo form_input($data);
                        ?>
                        <table class="table table-bordered table-hover table-checkable" id="datatable_detail_sales_order">
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
                                </tr>
                            </thead>
                            <tbody>                                                                                                            
                            </tbody>
                        </table>
                        <!--end: Datatable -->                                                                           
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6">                                        
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label kt-font-dark text-right">TOTAL PRODUK</label>
                            <div class="col-md-3">
                                <?php
                                    $data = array('type' => 'number', 'min' => 0, 'class' => 'form-control text-right', 'id' => 'total_product', 'name' => 'total_product',  'value' => $sales_order['total_product'], 'required' => 'true', 'readonly' => 'true'); 
                                    echo form_input($data);                                             
                                    echo form_error('total_product', '<p class="text-danger">', '</p>');
                                    ?>
                            </div>
                            <label class="col-md-3 col-form-label kt-font-dark text-right">TOTAL KUANTITAS</label>
                            <div class="col-md-3">
                                <?php
                                    $data = array('type' => 'number', 'min' => 0, 'class' => 'form-control text-right', 'id' => 'total_qty', 'name' => 'total_qty',  'value' => $sales_order['total_qty'], 'required' => 'true', 'readonly' => 'true'); 
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
                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'subtotal', 'name' => 'subtotal',  'value' => number_format($sales_order['total_price'],'0','.',','), 'required' => 'true', 'readonly' => 'true'); 
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
                            <label class="col-md-4 col-form-label kt-font-dark text-right">Diskon % / Diskon Rp.</label>
                            <div class="col-md-4">
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control text-right', 'name' => 'discount_p', 'id' => 'discount_p', 'placeholder' => 'Diskon (%)', 'value' => $sales_order['discount_p'].' %', 'required' => 'true', 'readonly' => 'true'); 
                                echo form_input($data);                                             
                                echo form_error('discount_p', '<p class="text-danger">', '</p>');
                            ?>
                            </div>
                            <div class="col-md-4">
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control text-right', 'name' => 'discount_rp', 'id' => 'discount_rp', 'placeholder' => 'Diskon (Rupiah)', 'value' => number_format($sales_order['discount_rp'],'0','.',','), 'required' => 'true', 'readonly' => 'true'); 
                                echo form_input($data);                                             
                                echo form_error('discount_rp', '<p class="text-danger">', '</p>');
                            ?>
                            </div>
                        </div>                                        
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label kt-font-dark text-right"><strong>GRANDTOTAL</strong></label>
                            <div class="col-md-8">
                                <?php
                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'grandtotal', 'name' => 'grandtotal',  'value' => number_format($sales_order['grandtotal'],'0','0',','), 'required' => 'true', 'readonly' => 'true'); 
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
                    <?php if($sales_order['sales_order_status'] == 1): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <a href="<?php echo base_url('sales/invoice/add/').$this->global->encrypt($sales_order['id']); ?>" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-save"></i> BUAT FAKTUR</a>
                        </div>
                    </div>                            
                    <?php endif; ?>                            
                </div>
            </div>                                                
        </div>
        <!--end::Portlet-->
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