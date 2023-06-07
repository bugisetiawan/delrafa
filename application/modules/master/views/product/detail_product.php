<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Master</h3>
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
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="text-dark">ID: <strong class="text-success"><?php echo $product['id_p']; ?></strong> | Kode <b>Produk</b>: <strong class="text-success"><?php echo $product['code_p']; ?></strong> | <?php echo $product['name_p'] ?></h4>
                    </div>
                </div>
            </div>
        </div>        
        <div class="row">
            <div class="col-lg-12">
                <div class="kt-portlet kt-portlet--tabs">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-toolbar">
                            <ul class="nav nav-tabs nav-tabs-line nav-tabs-line-primary" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#information" role="tab" aria-selected="true">
                                    <i class="la la-info"></i> Detail Produk
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#multi_price" role="tab" aria-selected="false">
                                    <i class="la la-money"></i> Informasi Harga
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#multi_unit" role="tab" aria-selected="false">
                                    <i class="la la-cog"></i> Multi Satuan
                                    </a>
                                </li>
                                <!-- <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#location" role="tab" aria-selected="false">
                                    <i class="la la-dropbox"></i> Lokasi Penyimpanan
                                    </a>
                                </li> -->
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#stock_card" role="tab" aria-selected="false">
                                    <i class="la la-clipboard"></i> Kartu Stok
                                    </a>
                                </li>
                                <?php  
                                $access_user_id = [1, 3, 14, 17];
                                // $access_user_id = [1];
		                        if(in_array($this->session->userdata('id_u'), $access_user_id)): ?>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#stock_movement" role="tab" aria-selected="false">
                                    <i class="la la-file"></i> Pergerakan Stok
                                    </a>
                                </li>
                                <?php endif; ?>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#list_of_purchase_sales_invoice" role="tab" aria-selected="false">
                                    <i class="fa fa-clipboard-list"></i> Pembelian & Penjualan
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="information" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6 text-left">
                                        <h4 class="text-dark">
                                            <?php 
                                                $data = array('type' => 'hidden', 'class' => 'form-control', 'id' => 'product_id', 'value' => $product['id_p']);
                                                echo form_input($data);
                                                $data = array('type' => 'hidden', 'class' => 'form-control', 'id' => 'product_code', 'value' => $product['code_p']);
                                                echo form_input($data);                                                
                                            ?>                                            
                                        </h4>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <a href="<?php echo site_url('product'); ?>" class="btn btn-outline-primary btn-elevate btn-icon"
                                            data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Kembali ke Daftar Produk"><b><i class="fa fa-arrow-left"></i></b>
                                        </a>
                                        <!-- <a href="<?php echo site_url('product/barcode/'.encrypt_custom($product['code_p'])); ?>" class="btn btn-outline-success btn-elevate btn-icon"
                                            data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Cetak Barcode Produk"><b><i class="fa fa-barcode"></i></b>
                                        </a> -->
                                        <?php if($product['type'] == 2): ?>
                                        <!-- <a href="<?php echo site_url('stock/production/add/'.encrypt_custom($product['id_p'])); ?>" class="btn btn-outline-success btn-elevate"
                                            data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Produksi Produk"><b><i class="fa fa-cube"></i></b>
                                            Produksi Produk
                                        </a> -->
                                        <?php endif; ?>
                                        <a href="<?php echo site_url('product/update/'.encrypt_custom($product['code_p'])); ?>" class="btn btn-outline-warning btn-elevate btn-icon"
                                            data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Perbarui Data Produk">
                                        <i class="la la-edit"></i>
                                        </a>
                                        <button type="button" id="delete" class="btn btn-outline-danger btn-elevate btn-icon"
                                            data-id="<?php echo $product['code_p']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Hapus Data Produk">
                                        <i class="la la-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <hr>
                                <div class="kt-widget kt-widget--user-profile-3">
                                    <div class="kt-widget__top">
                                        <div class="kt-widget__content">
                                            <div class="form-group row">
                                                <div class="col-md-4">                                                    
                                                    <label class="text-dark">DEPARTEMEN</label>
                                                    <?php 
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'department', 'name' => 'department',  'value' => $product['department'], 'readonly' => 'true'); 
                                                        echo form_input($data);                                             													
                                                    ?>
                                                </div>
                                                <div class="col-md-4">                                                    
                                                    <label class="text-dark">SUB DEPARTEMEN</label>
                                                    <?php 
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'subdepartment', 'name' => 'subdepartment',  'value' => $product['subdepartment'], 'readonly' => 'true'); 
                                                        echo form_input($data);                                             													
                                                    ?>
                                                </div>
                                                <div class="col-md-2">                                                    
                                                    <label class="text-dark">TIPE PRODUK</label>
                                                    <?php 
                                                        $product_type = ($product['type'] == 1) ? "SINGLE" : "BUNDLE";
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'value' => $product_type, 'readonly' => 'true');
                                                        echo form_input($data);
                                                    ?>
                                                </div>
                                                <div class="col-md-2">                                                    
                                                    <label class="text-dark">PPN</label>
                                                    <?php 
                                                        if($product['ppn'] == 1)
                                                        {
                                                            $ppn = "PPN";
                                                        }
                                                        elseif($product['ppn'] == 2)
                                                        {
                                                            $ppn = "FINAL";
                                                        }
                                                        else
                                                        {
                                                            $ppn = "NON";
                                                        }
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'ppn', 'name' => 'ppn',  'value' => $ppn, 'readonly' => 'true'); 
                                                        echo form_input($data);                                             													
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4">                                                    
                                                    <label class="text-dark">BARCODE</label>
                                                    <?php 
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'barcode', 'name' => 'barcode',  'value' => $product['barcode'], 'readonly' => 'true'); 
                                                        echo form_input($data);
                                                    ?>                                                    
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="text-dark">NAMA</label>
                                                    <?php 
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'name_p', 'name' => 'name_p',  'value' => $product['name_p'], 'readonly' => 'true'); 
                                                        echo form_input($data);                                             													
                                                    ?>
                                                </div>
                                                <div class="col-md-4">                                                    
                                                    <label class="text-dark">IDENTITAS</label>
                                                    <?php 
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'productid', 'name' => 'productid',  'value' => $product['productid'], 'readonly' => 'true'); 
                                                        echo form_input($data);                                             													
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-8">                                                    
                                                    <label class="col-form-label text-dark">DESKRIPSI</label>
                                                    <?php 
                                                        $data = array('class' => 'form-control', 'id' => 'description', 'name' => 'description',  'value' => $product['description'], 'rows' => '3', 'readonly' => 'true'); 
                                                        echo form_textarea($data);
                                                    ?>
                                                </div>
                                                <div class="col-md-4">
                                                    <?php 
                                                        $image = ($product['photo'] != null) ? $product['photo'] : "nophoto.png";
                                                    ?>
                                                    <img src="<?php echo base_url('assets/media/system/products/'.$image) ?>" alt="<?php echo $image; ?>" class="img-fluid">
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="form-group row">
                                                <div class="col-6 col-md-2">
                                                    <label class="col-form-label text-dark">STOK MINIMAL</label>
                                                    <?php 
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'minimal', 'name' => 'minimal',  'value' => $product['minimal'], 'readonly' => 'true'); 
                                                        echo form_input($data);                                             													
                                                    ?>                                                    
                                                </div>
                                                <div class="col-6 col-md-2">                                                    
                                                    <label class="col-form-label text-dark">STOK MAKSIMAL</label>
                                                    <?php
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'maximal', 'name' => 'maximal',  'value' => $product['maximal'], 'readonly' => 'true'); 
                                                        echo form_input($data);                                             													
                                                    ?>
                                                </div>
                                                <div class="col-6 col-md-2">
                                                    <label class="col-form-label text-dark">SATUAN DASAR</label>
                                                    <?php
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'name_u', 'name' => 'name_u',  'value' => $product['name_u'], 'readonly' => 'true'); 
                                                        echo form_input($data);                                             													
                                                    ?>                                                    
                                                </div>                                                
                                                <div class="col-6 col-md-2">                                                    
                                                    <label class="col-form-label text-dark">BERAT DASAR (KG)</label>
                                                    <?php
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'weight', 'name' => 'name_u',  'value' => $product['weight'], 'readonly' => 'true'); 
                                                        echo form_input($data);                                             													
                                                    ?>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="col-form-label text-dark">KOMISI PENJUALAN (%)</label>
                                                    <?php
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'commission_sales', 'name' => 'commission_sales', 'placeholder' => 'Komisi Penjualan', 'value' => $product['commission_sales'], 'readonly' => 'true');
                                                        echo form_input($data);
                                                        echo form_error('commission_sales', '<p class="text-danger">', '</p>');
                                                    ?>
                                                </div>
                                                <div class="col-6 col-md-2">                                                    
                                                    <label class="col-form-label text-dark">STATUS TRANSAKSI</label>
                                                    <?php 
                                                        $status = ($product['status'] == 1) ? "KONTINU" : "DISKONTINU";                                                            
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'status', 'name' => 'status',  'value' => $status, 'readonly' => 'true'); 
                                                        echo form_input($data);                                             													
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if($product['type'] == 2): ?>
                                <hr><br>
                                <h4 class="text-dark">Daftar Produk Bundle</h4>
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <td>KODE</td>
                                            <td>NAMA</td>
                                            <td>QTY</td>
                                            <td>SATUAN</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($product_bundle AS $info_bundle): ?>
                                        <tr>
                                            <td class="text-center"><?php echo $info_bundle['code_p']; ?></td>
                                            <td><?php echo $info_bundle['name_p']; ?></td>
                                            <td class="text-right"><?php echo $info_bundle['qty']; ?></td>
                                            <td class="text-left"><?php echo $info_bundle['code_u']; ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php endif; ?>                                
                            </div>
                            <div class="tab-pane" id="multi_price" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <td class="text-dark text-center font-weight-bold" colspan="3">
                                                        HARGA BELI TERAKHIR
                                                        <?php if($this->system->check_access('product/change_buyprice', 'A')): ?>
                                                        &nbsp;<a href="javascript:void(0);" data-id="<?php echo $product['code_p']; ?>" id="update_buyprice_btn"><i class="fa fa-pen text-warning"></i></a>
                                                        <?php endif; ?>
                                                    </td>                                                
                                                    <td class="text-dark text-center font-weight-bold">
                                                        HPP
                                                        <?php if($this->system->check_access('product/change_hpp', 'A')): ?>
                                                        &nbsp;<a href="javascript:void(0);" data-id="<?php echo $product['code_p']; ?>" id="update_hpp_btn"><i class="fa fa-pen text-warning"></i></a>
                                                        <?php endif; ?>
                                                    </td>                                                    
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>                                                    
                                                    <input type="hidden" id="buyprice" value="<?php $last_buyprice['price']; ?>">
                                                    <?php if($this->system->check_access('product/view_buyprice', 'A')): ?>
                                                    <td class="text-primary text-center font-weight-bold"><?php echo number_format($last_buyprice['price'], 2, ".", ","); ?></td>
                                                    <?php endif; ?>
                                                    <td class="text-dark text-center">SATUAN: <?php echo $product['name_u']; ?></td>
                                                    <td class="text-primary text-center font-weight-bold">SUPPLIER: <?php echo $last_buyprice['supplier']; ?></td>
                                                    <?php if($this->system->check_access('product/view_hpp', 'A')): ?>
                                                    <td class="text-primary text-center font-weight-bold"><?php echo number_format($hpp, 2, ".", ","); ?></td>
                                                    <?php endif; ?>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-3">                                        
                                        <button type="button" class="btn btn-sm btn-wide btn-outline-primary" data-toggle="modal" data-target="#add_price_form"><i class="fa fa-plus"></i> Tambah Harga Jual Per Satuan</button>
                                    </div>
                                    <div class="col-md-9">                                        
                                    </div>
                                </div>
                                <hr>                                
                                <table class="table table-bordered table-hover" id="datatable_multi_price">
                                    <thead>
                                        <tr style="text-align:center;">
                                            <th class="text-dark">NO.</th>
                                            <th class="text-dark">SATUAN</th>
                                            <?php for($i=1;$i<=5;$i++): ?>
                                            <th class="text-dark" width="100px">HARGA JUAL <?php echo $i; ?></th>
                                            <?php endfor; ?>
                                            <th class="text-dark" width="80px">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no=1; foreach($multi_price AS $info): ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no; ?></td>
                                            <td class="text-dark"><?php echo $info['name_u']; ?></td>
                                            <?php for($i=1;$i<=5;$i++): ?>
                                            <?php                                                
                                                $info_percent = ($last_buyprice['price'] > 0) ? ($info['price_'.$i]-($last_buyprice['price']*$info['value']))/($last_buyprice['price']*$info['value'])*100 : 100;
                                                $class_percent = $info_percent <= 0 ? 'text-danger' : 'text-success'; 
                                            ?>
                                            <td class="text-right text-dark"><?php echo number_format($info['price_'.$i],0,".",",").' / <b class="'.$class_percent.'">'.number_format($info_percent, 2, ".", ",").'</b> %'; ?></td>
                                            <?php endfor; ?>
                                            <td class="text-center">
                                                <a href="javascript:void(0);" class="kt-font-warning kt-link update_price" data-id="<?php echo $info['id_sellprice']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" title="Edit Harga Jual">
                                                <i class="fa fa-edit"></i>
                                                </a>
                                                &nbsp;
                                                <a href="javascript:void(0);" class="kt-font-danger kt-link delete_multi_price"
                                                    data-id=<?php echo $info['id_sellprice']; ?> data-container="body" data-toggle="kt-tooltip" data-placement="left" title="Hapus Harga Jual">
                                                <i class="fa fa-times"></i>
                                                </a>                                                                                                        
                                            </td>
                                        </tr>
                                        <?php $no++; endforeach; ?>
                                    </tbody>
                                </table>                                
                            </div>
                            <div class="tab-pane" id="multi_unit" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-sm btn-wide btn-outline-primary" data-toggle="modal" data-target="#add_unit_form"><i class="fa fa-plus"></i> Tambah Satuan</button>
                                    </div>
                                </div>
                                <br>
                                <div class="table-responsive">
                                    <!--begin: Datatable -->
                                    <table class="table table-bordered table-hover table-checkable" id="datatable_multi_unit">
                                        <thead>
                                            <tr style="text-align:center;">
                                                <th class="text-dark" width="10%">NO.</th>
                                                <th class="text-dark">SATUAN</th>
                                                <th class="text-dark" width="100px">JUMLAH <b>(<?php echo $product['code_u']; ?>)</b></th>
                                                <th class="text-dark" width="100px">AKSI</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no=1; foreach($multi_unit AS $info): ?>
                                            <tr>
                                                <td class="text-center"><?php echo $no; ?></td>
                                                <td class="text-dark"><?php echo $info['name_u']; ?></td>
                                                <td class="text-dark"><?php echo $info['value']; ?></td>
                                                <td class="text-center">  
                                                    <a href="javascript:void(0);" class="kt-font-warning kt-link update_multi_unit" data-id="<?php echo $info['id_mu']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" title="Edit Data">
                                                    <i class="fa fa-edit"> Edit</i>
                                                    </a>
                                                    &nbsp;                                                 
                                                    <a href="javascript:void(0);" class="kt-font-danger kt-link delete_multi_unit"
                                                        data-id=<?php echo $info['id_mu']; ?> data-container="body" data-toggle="kt-tooltip" data-placement="left" title="Hapus Data">
                                                    <i class="fa fa-times"> Hapus</i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php $no++; endforeach; ?>
                                        </tbody>
                                    </table>
                                    <!--end: Datatable -->
                                </div>
                            </div>
                            <div class="tab-pane" id="location" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-sm btn-wide btn-outline-primary" data-toggle="modal" data-target="#add_product_location_form"><i class="fa fa-plus"></i> Tambah Lokasi Penyimpanan</button>
                                    </div>
                                </div>
                                <br>							
                                <div class="table-responsive">
                                    <!--begin: Datatable -->
                                    <table class="table table-bordered table-hover table-checkable" id="datatable_product_location">
                                        <thead>
                                            <tr style="text-align:center;">
                                                <th width="10%">NO.</th>
                                                <th width="200px">GUDANG</th>
                                                <th>LOKASI</th>
                                                <th width="150px">AKSI</th>
                                            </tr>
                                        </thead>
                                        <?php $no=1; foreach($product_location AS $info):?>
                                        <tr>
                                            <td class="text-center"><?php echo $no; ?></td>
                                            <td><?php echo $info['name_w']; ?></td>
                                            <td><?php echo $info['location']; ?></td>
                                            <td class="text-center">  
                                                <a href="javascript:void(0);" class="kt-font-warning kt-link update_product_location" data-id="<?php echo $info['id_pl']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" title="Edit Data">
                                                <i class="fa fa-edit"> Edit</i>
                                                </a>
                                                &nbsp;                                                 
                                                <a href="javascript:void(0);" class="kt-font-danger kt-link delete_product_location"
                                                    data-id=<?php echo $info['id_pl']; ?> data-container="body" data-toggle="kt-tooltip" data-placement="left" title="Hapus Data">
                                                <i class="fa fa-times"> Hapus</i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php $no++; endforeach; ?>
                                        <tbody>										
                                        </tbody>
                                    </table>
                                    <!--end: Datatable -->
                                </div>
                            </div>
                            <div class="tab-pane" id="stock_card" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-3">                                         
                                        <select class="form-control"name="warehouse_id_sc" id="warehouse_id_sc">
                                        <?php $total_stock = 0; foreach($warehouse_stock_card AS $info_warehouse_stock_card): ?>
                                        <option value="<?php echo $info_warehouse_stock_card['id_w']; ?>"><?php echo $info_warehouse_stock_card['code_w'].' | '.$info_warehouse_stock_card['qty']; ?></option>                                    
                                        <?php $total_stock = $total_stock + $info_warehouse_stock_card['qty']; endforeach; ?>
                                        </select>                                        
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-control" name="transaction_type" id="transaction_type">
                                            <option value="">- SEMUA TRANSAKSI -</option>
                                            <option value="1">PEMBELIAN</option>
                                            <option value="2">RETUR PEMBELIAN</option>
                                            <!-- <option value="3">POS</option> -->
                                            <option value="4">PENJUALAN</option>
                                            <option value="5">RETUR PENJUALAN</option>
                                            <!-- <option value="6">PRODUKSI</option> -->
                                            <option value="7">PENGEMASAN PRODUK</option>
                                            <option value="8">PENYESUAIAN STOK</option>
                                            <option value="9">MUTASI PRODUK</option>
                                        </select>  
                                    </div>
                                    <div class="col-md-2">
                                        <p class="col-form-label text-dark">Total Stok : <?php echo $total_stock; ?></p>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <?php if($this->session->userdata('id_u') == 1): ?>                                    
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-square btn-warning btn-elevate btn-elevate-air dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="la la-align-justify"></i>
                                                <span class="d-none d-sm-inline">Lainnya</span>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                <a class="dropdown-item kt-font-dark" href="javascript:void(0)" id="check_stock_card_movement_product_btn" data-id="<?php echo $product['id_p']; ?>">- Cek Kartu & Pergerakan Stok</a>
                                                <a class="dropdown-item kt-font-dark" href="javascript:void(0)" id="validate_stock_card_movement_product_btn" data-id="<?php echo $product['id_p']; ?>">- Validasi Kartu & Pergerakan Stok</a>
                                                <a class="dropdown-item kt-font-dark" href="javascript:void(0)" id="sort_inventory_product_btn" data-id="<?php echo $product['id_p']; ?>">- Mengurutkan Pergerakan Stok</a>
                                                <a class="dropdown-item kt-font-dark" href="javascript:void(0)" id="recalculate_inventory_product_btn" data-id="<?php echo $product['id_p']; ?>">- Hitung Ulang Persediaan</a>                                                
                                                <a class="dropdown-item kt-font-dark" href="javascript:void(0)" id="recalculate_hpp_product_btn" data-id="<?php echo $product['id_p']; ?>">- Hitung Ulang HPP</a>
                                            </div>
                                        </div>
                                        <?php endif; ?>     
                                        <button type="button" class="btn btn-sm btn-wide btn-outline-brand font-weight-bold"> Satuan Dasar: <?php echo $product['name_u']; ?></button>
                                    </div>
                                </div>
                                <hr>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="datatable_stock_card">
                                        <thead>
                                            <tr>
                                                <th class="text-center notexport">NO.</th>
                                                <th class="text-center text-dark">TANGGAL</th>
                                                <th class="text-center text-dark">NO. TRANSAKSI</th>
                                                <th class="text-center text-left">KETERANGAN</th>
                                                <th class="text-center text-dark">QTY (<b class="text-success">M</b>/<b class="text-danger">K</b>)</th>
                                                <th class="text-center text-dark">STOK</th>
                                                <th class="text-center text-dark">GUDANG</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php  $access_user_id = [1, 3, 4, 14, 17];
		                    if(in_array($this->session->userdata('id_u'), $access_user_id)): ?>
                            <div class="tab-pane" id="stock_movement" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <select class="form-control" id="transaction_movement_type">
                                            <option value="">- SEMUA TRANSAKSI -</option>
                                            <option value="1">PEMBELIAN</option>
                                            <option value="2">RETUR PEMBELIAN</option>
                                            <option value="4">PENJUALAN</option>
                                            <option value="5">RETUR PENJUALAN</option>
                                            <option value="7">PENGEMASAN PRODUK</option>
                                            <option value="8">PENYESUAIAN STOK</option>
                                            <option value="9">MUTASI PRODUK</option>
                                        </select>  
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <button type="button" class="btn btn-sm btn-wide btn-outline-brand font-weight-bold"> Satuan Dasar: <?php echo $product['name_u']; ?></button>
                                    </div>
                                </div>
                                <hr>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="datatable_stock_movement">
                                        <thead>
                                            <tr>
                                                <th class="text-center notexport">NO.</th>
                                                <th class="text-center text-dark">TANGGAL</th>
                                                <th class="text-center text-dark">NO. TRANSAKSI</th>
                                                <th class="text-center text-left">KETERANGAN</th>
                                                <th class="text-center text-dark">QTY (<b class="text-success">M</b>/<b class="text-danger">K</b>)</th>
                                                <th class="text-center text-dark">STOK</th>
                                                <th class="text-center text-dark">HARGA</th>
                                                <th class="text-center text-dark">HPP</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="tab-pane" id="list_of_purchase_sales_invoice" role="tabpanel">                                                
                                <h5 class="text-dark">Daftar Pembelian</h5>                                
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-hover" id="datatable_list_of_purchase_invoice">
                                        <thead>
                                            <tr>
                                                <th>NO.</th>
                                                <th>TANGGAL</th>
                                                <th>NO. TRANSAKSI</th>
                                                <th>QTY</th>
                                                <th>SATUAN</th>
                                                <th>HARGA</th>
                                                <th>DISKON</th>
                                                <th>TOTAL</th>
                                                <th>SUPPLIER</th>                                         
                                            </tr>
                                        </thead>
                                        <tbody>                                            
                                        </tbody>
                                    </table>
                                </div>
                                <hr>
                                <h5 class="text-dark">Daftar Penjualan</h5>
                                <br>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-hover" id="datatable_list_of_sales_invoice">
                                        <thead>
                                            <tr>
                                                <th>NO.</th>
                                                <th>TANGGAL</th>
                                                <th>NO. TRANSAKSI</th>
                                                <th>QTY</th>
                                                <th>SATUAN</th>
                                                <th>HARGA</th>
                                                <th>DISKON</th>
                                                <th>TOTAL</th>
                                                <th>PELANGGAN</th>                                         
                                            </tr>
                                        </thead>
                                        <tbody>                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end:: Content -->   
    <!--begin::Update Buyprice and HPP-->
    <div class="modal fade" id="update_buyprice_form">
        <div class="modal-dialog modal-lg" role="document">
            <?php echo form_open('master/Product/update_buyprice_product', ['id' => 'update_hpp_data', 'autocomplete' => 'off']); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Perbaharui HPP Produk</h5>
                </div>
                <div class="modal-body">
                    <?php 
                        $data = array('type' => 'hidden', 'class' => 'form-control', 'name' => 'product_code',  'value' => $product['code_p'], 'required' => 'true');
                        echo form_input($data);
                    ?>
                    <div class="form-group row">
                        <div class="col-md-8">
                            <label class="text-dark">NAMA</label>
                            <?php
                                $data = array('type' => 'text', 'class' => 'form-control', 'value' => $product['name_p'], 'readonly' => 'true'); 
                                echo form_input($data);                                
                            ?>                            
                        </div>
                        <div class="col-md-4">
                            <label class="text-dark">Harga Beli (Per <b><?php echo $product['name_u']; ?></b>)</label>
                            <?php
                                $data = array('type' => 'text', 'class' => 'form-control price', 'id' => 'buyprice_product', 'name' => 'buyprice_product', 'placeholder' => 'Silahkan isi HPP produk...',  'value' => set_value('hpp_product'), 'required' => 'true'); 
                                echo form_input($data);                                
                            ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
                    <button type="submit" class="btn btn-success" id="save-update-hpp"><i class="fa fa-save"></i> SIMPAN</button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
    <div class="modal fade" id="update_hpp_form">
        <div class="modal-dialog modal-lg" role="document">
            <?php echo form_open('master/Product/update_hpp_product', ['id' => 'update_hpp_data', 'autocomplete' => 'off']); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Perbaharui HPP Produk</h5>
                </div>
                <div class="modal-body">
                    <?php 
                        $data = array('type' => 'hidden', 'class' => 'form-control', 'name' => 'product_code',  'value' => $product['code_p'], 'required' => 'true');
                        echo form_input($data);
                    ?>
                    <div class="form-group row">
                        <div class="col-md-8">
                            <label class="text-dark">NAMA</label>
                            <?php
                                $data = array('type' => 'text', 'class' => 'form-control', 'value' => $product['name_p'], 'readonly' => 'true'); 
                                echo form_input($data);                                
                            ?>                            
                        </div>
                        <div class="col-md-4">
                            <label class="text-dark">HPP (Per <b><?php echo $product['name_u']; ?></b>)</label>
                            <?php
                                $data = array('type' => 'text', 'class' => 'form-control price', 'id' => 'hpp_product', 'name' => 'hpp_product', 'placeholder' => 'Silahkan isi HPP produk...',  'value' => set_value('hpp_product'), 'required' => 'true'); 
                                echo form_input($data);                                
                            ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
                    <button type="submit" class="btn btn-success" id="save-update-hpp"><i class="fa fa-save"></i> SIMPAN</button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
    <!--end::Update Buyprice and HPP-->
    <!--begin::Add Sell Price Modal-->
    <div class="modal fade" id="add_price_form">
        <div class="modal-dialog modal-lg" role="document">
            <?php echo form_open('master/Product/multi_price', ['id' => 'add_multi_price_data', 'autocomplete' => 'off']); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Harga Jual Produk Per Satuan</h5>
                </div>
                <div class="modal-body">
                    <?php 
                        $data = array('type' => 'hidden', 'class' => 'form-control', 'name' => 'product_code',  'value' => $product['code_p'], 'required' => 'true');
                        echo form_input($data);
                        ?>
                    <p class="text-dark kt-font-bold">
                        <b>MOHON PERHATIAN:</b><br>
                        - Harga Jual 1 s.d 5 merupakan harga bertingkat dari tinggi ke rendah.<br>                        
                        - <b>Harga Jual 1</b> wajib di isi, bagi harga jual yang tidak terisi akan mengikui harga jual terakhir.<br>
                        - Bila ingin mengganti harga jual, silahkan klik <b>Edit</b> pada data yang sudah terdaftar.
                    </p>
                    <div class="alert alert-outline-danger" role="alert" id="price_alert">
                        <div class="alert-text" id="price_message"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label text-dark">SATUAN:</label>
                        <select name="unit_id" id="unit_id" class="form-control" required>
                            <option value="">- PILIH SATUAN -</option>
                            <?php foreach($unit_product as $info): ?>
                            <option value="<?php echo $info['id_u']; ?>"><?php echo $info['name_u']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label class="text-dark">HARGA JUAL 1:</label>
                            <?php
                                $data = array('type' => 'text', 'class' => 'form-control price', 'id' => 'price_1', 'name' => 'price_1', 'placeholder' => 'Silahkan isi Harga Jual 1...',  'value' => set_value('price_1'), 'required' => 'true'); 
                                echo form_input($data);
                                echo form_error('price_1', '<p class="text-danger">', '</p>');
                            ?>
                        </div>
                        <div class="col-md-4">
                            <label class="text-dark">HARGA JUAL 2:</label>
                            <?php
                                $data = array('type' => 'text', 'class' => 'form-control price', 'id' => 'price_2', 'name' => 'price_2', 'placeholder' => 'Silahkan isi Harga Jual 2...',  'value' => set_value('price_2')); 
                                echo form_input($data);
                                echo form_error('price_2', '<p class="text-danger">', '</p>');
                                ?>
                        </div>
                        <div class="col-md-4">
                            <label class="text-dark">HARGA JUAL 3:</label>
                            <?php
                                $data = array('type' => 'text', 'class' => 'form-control price', 'id' => 'price_3', 'name' => 'price_3', 'placeholder' => 'Silahkan isi Harga Jual 3...',  'value' => set_value('price_3')); 
                                echo form_input($data);
                                echo form_error('price_3', '<p class="text-danger">', '</p>');
                                ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="text-dark">HARGA JUAL 4:</label>
                            <?php
                                $data = array('type' => 'text', 'class' => 'form-control price', 'id' => 'price_4', 'name' => 'price_4', 'placeholder' => 'Silahkan isi Harga Jual 4...',  'value' => set_value('price_4')); 
                                echo form_input($data);
                                echo form_error('price_4', '<p class="text-danger">', '</p>');
                                ?>
                        </div>
                        <div class="col-md-6">
                            <label class="text-dark">HARGA JUAL 5:</label>
                            <?php
                                $data = array('type' => 'text', 'class' => 'form-control price', 'id' => 'price_5', 'name' => 'price_5', 'placeholder' => 'Silahkan isi Harga Jual 5... ',  'value' => set_value('price_5')); 
                                echo form_input($data);
                                echo form_error('price_5', '<p class="text-danger">', '</p>');
                                ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
                    <button type="submit" class="btn btn-success" id="save-multiprice" disabled="true"><i class="fa fa-save"></i> SIMPAN</button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
    <!--end::Add Sell Price Modal--> 
    <!--begin::Update Sell Price Modal-->
    <div class="modal fade" id="update_price_form">
        <div class="modal-dialog modal-lg" role="document">
            <?php echo form_open('master/Product/multi_price', ['id' => 'update_multi_price_data', 'autocomplete' => 'off']); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Perbaharui Harga Jual Produk</h5>
                </div>
                <div class="modal-body">
                    <?php 
                        $data = array('type' => 'hidden', 'class' => 'form-control', 'name' => 'product_code',  'value' => $product['code_p'], 'required' => 'true');
                        echo form_input($data);
                        ?>
                    <?php 
                        $data = array('type' => 'hidden', 'class' => 'form-control', 'id' => 'e_unit_id', 'name' => 'unit_id', 'required' => 'true');
                        echo form_input($data);
                        ?>
                    <?php 
                        $data = array('type' => 'hidden', 'class' => 'form-control', 'id' => 'e_unit_value');
                        echo form_input($data);
                        ?>
                    <p class="text-dark kt-font-bold">
                        <b>MOHON PERHATIAN:</b><br>
                        - Harga Jual 1 s.d 5 merupakan harga bertingkat dari tinggi ke rendah.<br>                        
                        - <b>Harga Jual 1</b> wajib di isi, bagi harga jual yang tidak terisi akan mengikui harga jual terakhir.<br>                        
                    </p>
                    <div class="alert alert-outline-danger" role="alert" id="e_price_alert">
                        <div class="alert-text" id="e_price_message"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label text-dark">SATUAN:</label>
                        <?php 
                            $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'unit_name', 'readonly' => 'true');
                            echo form_input($data);
                            ?>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label class="text-dark">HARGA JUAL 1:</label>
                            <?php
                                $data = array('type' => 'text', 'class' => 'form-control price', 'id' => 'e_price_1', 'name' => 'price_1', 'placeholder' => 'Silahkan isi Harga Jual 1...',  'value' => set_value('price_1'), 'required' => 'true', 'autofocus' => 'true'); 
                                echo form_input($data);
                                echo form_error('price_1', '<p class="text-danger">', '</p>');
                                ?>
                            <small class="text-dark kt-font-bold">*Harga Jual yang akan di gunakan di POS</small>
                        </div>
                        <div class="col-md-4">
                            <label class="text-dark">HARGA JUAL 2:</label>
                            <?php
                                $data = array('type' => 'text', 'class' => 'form-control price', 'id' => 'e_price_2', 'name' => 'price_2', 'placeholder' => 'Silahkan isi Harga Jual 2...',  'value' => set_value('price_2')); 
                                echo form_input($data);
                                echo form_error('price_2', '<p class="text-danger">', '</p>');
                                ?>
                        </div>
                        <div class="col-md-4">
                            <label class="text-dark">HARGA JUAL 3:</label>
                            <?php
                                $data = array('type' => 'text', 'class' => 'form-control price', 'id' => 'e_price_3', 'name' => 'price_3', 'placeholder' => 'Silahkan isi Harga Jual 3...',  'value' => set_value('price_3')); 
                                echo form_input($data);
                                echo form_error('price_3', '<p class="text-danger">', '</p>');
                                ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label class="text-dark">HARGA JUAL 4:</label>
                            <?php
                                $data = array('type' => 'text', 'class' => 'form-control price', 'id' => 'e_price_4', 'name' => 'price_4', 'placeholder' => 'Silahkan isi Harga Jual 4...',  'value' => set_value('price_4')); 
                                echo form_input($data);
                                echo form_error('price_4', '<p class="text-danger">', '</p>');
                                ?>
                        </div>
                        <div class="col-md-4">
                            <label class="text-dark">HARGA JUAL 5:</label>
                            <?php
                                $data = array('type' => 'text', 'class' => 'form-control price', 'id' => 'e_price_5', 'name' => 'price_5', 'placeholder' => 'Silahkan isi Harga Jual 5... ',  'value' => set_value('price_5')); 
                                echo form_input($data);
                                echo form_error('price_5', '<p class="text-danger">', '</p>');
                                ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
                    <button type="submit" class="btn btn-success" id="save-update-multiprice" disabled="true"><i class="fa fa-save"></i> SIMPAN</button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
    <!--end::Update Sell Price Modal-->
    <!--begin::Add Unit Modal-->
    <div class="modal fade" id="add_unit_form">
        <div class="modal-dialog modal-lg" role="document">
            <?php echo form_open('master/Product/add_multi_unit', ['id' => 'add_multi_unit_data', 'autocomplete' => 'off']); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Satuan Produk</h5>
                </div>
                <div class="modal-body">
                    <?php 
                        $data = array('type' => 'hidden', 'class' => 'form-control', 'name' => 'product_code',  'value' => $product['code_p'], 'required' => 'true');
                        echo form_input($data);   
                        ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-form-label text-dark">PER SATUAN:</label>
                                <select name="unit_id" class="form-control" required>
                                    <option value="">-- Pilih Satuan --</option>
                                    <?php foreach($unit_option as $info): ?>
                                    <option value="<?php echo $info['id_u']; ?>"><?php echo $info['name_u']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-form-label text-dark">BERISI JUMLAH SATUAN DASAR (<?php echo $product['name_u']; ?>):</label>
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'value', 'name' => 'value',  'value' => set_value('value'), 'placeholder' => 'Silahkan isi jumlah satuan dasar produk', 'required' => 'true');
                                    echo form_input($data);                                                
                                    ?> 
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> SIMPAN</button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
    <!--end::Add Unit Modal-->
    <!--begin::Update Unit Modal-->
    <div class="modal fade" id="update_unit_form">
        <div class="modal-dialog modal-lg" role="document">
            <?php echo form_open('master/Product/update_multi_unit', ['id' => 'update_multi_unit_data', 'autocomplete' => 'off']); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Perbaharui Satuan Produk</h5>
                </div>
                <div class="modal-body">
                    <?php 
                        $data = array('type' => 'hidden', 'class' => 'form-control', 'name' => 'product_code',  'value' => $product['code_p'], 'required' => 'true');
                        echo form_input($data);
                        $data = array('type' => 'hidden', 'id' => 'e_id_mu', 'name' => 'id_mu');
                        echo form_input($data);
                        ?>					                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-form-label text-dark">PER SATUAN:</label>                                
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'name_mu', 'name' => 'name_mu', 'readonly' => 'true');
                                    echo form_input($data);                                                
                                    ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-form-label text-dark">BERISI JUMLAH SATUAN DASAR (<?php echo $product['name_u']; ?>):</label>
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'e_value', 'name' => 'value',  'value' => set_value('value'), 'placeholder' => 'Silahkan isi jumlah satuan dasar produk', 'required' => 'true');
                                    echo form_input($data);                                                
                                    ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> SIMPAN</button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
    <!--end::Update Unit Modal-->  
    <!--begin::Add Product Location Modal-->
    <div class="modal fade" id="add_product_location_form">
        <div class="modal-dialog modal-lg" role="document">
            <?php echo form_open('master/Product/add_product_location', ['id' => 'add_product_location_data', 'autocomplete' => 'off']); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Lokasi Penyimpanan</h5>
                </div>
                <div class="modal-body">
                    <?php 
                        $data = array('type' => 'hidden', 'class' => 'form-control', 'name' => 'product_code',  'value' => $product['code_p'], 'required' => 'true');
                        echo form_input($data);   
                        ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-form-label text-dark">GUDANG:</label>
                                <select name="warehouse_id" id="warehouse_id" class="form-control" required>
                                    <option value="">-- Pilih Gudang --</option>
                                    <?php foreach($warehouse_option as $info): ?>
                                    <option value="<?php echo $info['id_w']; ?>"><?php echo $info['name_w']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-form-label text-dark">KETERANGAN PENYIMPANAN:</label>
                                <textarea class="form-control" name="location" id="location" rows="5" placeholder="Contoh: Rak A bagian bawah.... dekat dengan ..., dibawah mejaa, dibagian...." required></textarea> 
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> SIMPAN</button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
    <!--end::Add Product Location Modal-->
    <!--begin::Update Product Location Modal-->
    <div class="modal fade" id="update_product_location_form">
        <div class="modal-dialog modal-lg" role="document">
            <?php echo form_open('master/Product/update_product_location', ['id' => 'update_product_location_data', 'autocomplete' => 'off']); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Perbaharui Lokasi Penyimpanan</h5>
                </div>
                <div class="modal-body">
                    <?php 
                        $data = array('type' => 'hidden', 'class' => 'form-control', 'name' => 'product_code',  'value' => $product['code_p'], 'required' => 'true');
                        echo form_input($data);
                        ?>					
                    <?php 
                        $data = array('type' => 'hidden', 'id' => 'e_id_pl', 'name' => 'id_pl');
                        echo form_input($data);   
                        ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-form-label text-dark">GUDANG:</label>                                
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'name_w', 'name' => 'name_w', 'readonly' => 'true');
                                    echo form_input($data);                                                
                                    ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">                                
                                <label class="col-form-label text-dark">KETERANGAN PENYIMPANAN:</label>
                                <textarea class="form-control" name="location" id="e_location" rows="5" placeholder="Contoh: Rak A bagian bawah.... dekat dengan ..., dibawah mejaa, dibagian...." required></textarea> 
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> SIMPAN</button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
    <!--end::Update Product Location Modal-->  
</div>