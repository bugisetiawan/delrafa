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
        <?php echo form_open_multipart('', ['autocomplete'=>'off']); ?>
            <div class="row">
                <div class="col-lg-12">
                    <!--begin::Portlet-->
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
                                        <a href="<?php echo base_url('pos/promotion'); ?>" class="btn btn-outline-primary btn-elevate btn-icon"
                                            data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Kembali ke Daftar Promo">
                                            <i class="fa fa-arrow-left"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label kt-font-dark"><span class="text-danger">*</span>NAMA PROMO</label>
                                        <div class="col-md-9">
                                            <?php 
                                                $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'name', 'name' => 'name',  'value' => $promotion['name'], 'placeholder' => 'Silahkan isi nama promo', 'readonly' => 'true'); 
                                                echo form_input($data);                                             
                                                echo form_error('name', '<p class="text-danger">', '</p>');
                                            ?> 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label kt-font-dark text-right"><span class="text-danger">*</span>TIPE PROMO</label>
                                        <div class="col-md-9">
                                            <select class="form-control" name="type" id="type" disabled>
                                                <option value="1">DISKON PRODUK</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="kt-font-dark"><span class="text-danger">*</span>WAKTU MULAI</label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'start_date', 'name' => 'start_date',  'value' => date('d-m-Y'), 'placeholder' => 'Silahkan isi tanggal pemesanan pembelian', 'required' => 'true'); 
                                                    echo form_input($data);                                             
                                                    echo form_error('start_date', '<p class="text-danger">', '</p>');
                                                ?>                                            
                                            </div>
                                            <div class="col-md-6">
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control time', 'id' => 'start_time', 'name' => 'start_time',  'value' => date('H:i:s'), 'placeholder' => 'Silahkan isi tanggal pemesanan pembelian', 'required' => 'true'); 
                                                    echo form_input($data);                                             
                                                    echo form_error('start_time', '<p class="text-danger">', '</p>');
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="kt-font-dark"><span class="text-danger">*</span>WAKTU SELESAI</label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'end_date', 'name' => 'end_date',  'value' => date('d-m-Y'), 'placeholder' => 'Silahkan isi tanggal pemesanan pembelian', 'required' => 'true'); 
                                                    echo form_input($data);                                             
                                                    echo form_error('end_date', '<p class="text-danger">', '</p>');
                                                ?>                                            
                                            </div>
                                            <div class="col-md-6">
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control time', 'id' => 'end_time', 'name' => 'end_time',  'value' => date('H:i:s'), 'placeholder' => 'Silahkan isi tanggal pemesanan pembelian', 'required' => 'true'); 
                                                    echo form_input($data);                                             
                                                    echo form_error('end_time', '<p class="text-danger">', '</p>');
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label kt-font-dark text-right"><span class="text-danger">*</span>DISKON (%)</label>
                                        <div class="col-md-9">
                                            <?php 
                                                $data = array('type' => 'number', 'min' => 0, 'class' => 'form-control', 'id' => 'discount', 'name' => 'discount',  'value' => $promotion['discount'], 'placeholder' => 'Silahkan isi besaran diskon', 'readonly' => 'true'); 
                                                echo form_input($data);
                                                echo form_error('discount', '<p class="text-danger">', '</p>');
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
                                    <table class="table table-bordered table-hover table-checkable" id="datatable">
                                        <thead>
                                            <tr style="text-align:center;">
                                                <th>NO.</th>
                                                <th>KODE</th>
                                                <th>NAMA</th>
                                            </tr>
                                        </thead>
                                        <tbody> 
                                            <?php if(isset($products)): ?>
                                                <?php $no=1; foreach($products AS $info): ?>
                                                <tr>
                                                    <td><?= $no; ?></td>
                                                    <td class="text-dark"><?= $info['code']; ?></td>
                                                    <td class="text-dark"><?= $info['name']; ?></td>
                                                </tr>      
                                                <?php $no++; endforeach; ?>
                                            <?php endif; ?>                                                                                                          
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
                                    <div class="from-group row">
                                        <label class="col-md-4 col-form-label kt-font-bold kt-font-dark text-right">TOTAL PRODUK</label>
                                        <div class="col-md-8">
                                            <?php
                                                $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_product', 'name' => 'total_product',  'value' => $promotion['total_product'], 'required' => 'true', 'readonly' => 'true'); 
                                                echo form_input($data);                                             
                                                echo form_error('total_product', '<p class="text-danger">', '</p>');
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
        <?php echo form_close() ?>
    </div>
    <!-- end:: Content -->
</div>