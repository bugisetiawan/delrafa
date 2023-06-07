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
            <div class="row">
                <div class="col-lg-12">
                    <!--begin::Portlet-->
                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    Informasi | No.Transaksi <span class="kt-font-bold kt-font-success"><?php echo $purchase_order['code']; ?></span>
                                </h3>
                            </div>
                            <div class="kt-portlet__head-toolbar">
                                <div class="kt-portlet__head-wrapper">
                                    <div class="kt-portlet__head-actions">
                                        <a href="<?php echo base_url('purchase'); ?>" class="btn btn-outline-primary btn-elevate btn-icon"
                                            data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Kembali ke Daftar Pemesanan Pembelian">
                                            <i class="fa fa-arrow-left"></i>
                                        </a>
                                        <a href="<?php echo site_url('purchase/order/print/'. $this->global->encrypt($purchase_order['id'])); ?>" target="_blank" class="btn btn-outline-success btn-elevate btn-icon"
                                            data-id="<?php echo $purchase_order['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Cetak Pemesanan Pembelian">
                                        <i class="fa fa-print"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-md-5 col-form-label kt-font-dark">TGL. PEMESANAN PEMBELIAN</label>
                                        <div class="col-md-7">
                                            <?php 
                                                $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'date', 'name' => 'date',  'value' => date('d-m-Y', strtotime($purchase_order['date'])), 'placeholder' => 'Silahkan isi tanggal pemesanan pembelian', 'readonly' => 'true'); 
                                                echo form_input($data);                                             
                                                echo form_error('date', '<p class="text-danger">', '</p>');
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-md-2 col-form-label kt-font-dark text-right">SUPPLIER</label>                                            
                                        <div class="col-md-10">
                                            <?php 
                                                $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'supplier', 'name' => 'supplier',  'value' => $purchase_order['name_s'], 'placeholder' => 'Silahkan isi tanggal pemesanan pembelian', 'readonly' => 'true'); 
                                                echo form_input($data);                                             
                                                echo form_error('supplier', '<p class="text-danger">', '</p>');
                                            ?>
                                        </div>                                            
                                    </div>
                                </div>
                            </div>
                        </div>                        
                    </div>
                    <!--end::Portlet-->                    
                    <!--begin::Portlet-->
                    <div class="kt-portlet" id="product_table">
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
                                    <input type="hidden" id="purchase_order_id" value="<?php echo $purchase_order['id']; ?>">
                                    <table class="table table-bordered table-hover table-checkable" id="datatable">
                                        <thead>
                                            <tr style="text-align:center;">
                                                <th>NO.</th>
                                                <th>KODE</th>
                                                <th>NAMA</th>
                                                <th>QTY</th>
                                                <th>HARGA</th>
                                                <th>TOTAL</th>
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
                                                $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_product', 'name' => 'total_product',  'value' => $purchase_order['total_product'], 'readonly' => 'true'); 
                                                echo form_input($data);
                                            ?>
                                        </div>
                                        <label class="col-md-3 col-form-label kt-font-dark text-right">TOTAL KUANTITAS</label>
                                        <div class="col-md-3">
                                            <?php
                                                $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_qty', 'name' => 'total_qty',  'value' => $purchase_order['total_qty'], 'readonly' => 'true'); 
                                                echo form_input($data);
                                            ?>
                                        </div>                                        
                                    </div>                                        
                                </div>
                                <div class="col-md-6"></div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label kt-font-dark text-right">SUBTOTAL</label>
                                        <div class="col-md-8">
                                            <?php
                                                $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'subtotal', 'name' => 'subtotal',  'value' => number_format($purchase_order['grandtotal'],'0','.',','), 'readonly' => 'true');
                                                echo form_input($data);
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
</div>