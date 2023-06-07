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
                    <div class="alert alert-dark fade show" role="alert">
                        <div class="alert-icon"><i class="flaticon-exclamation-2"></i></div>
                        <div class="alert-text">Mohon Perhatian! Label yang memiliki bintang(<span class="text-danger">*</span>) wajib di disi, terima kasih.</div>			
                    </div>                    
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
                                        <a href="<?php echo base_url('purchase'); ?>" class="btn btn-outline-primary btn-elevate btn-icon"
                                            data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Kembali ke Daftar Pemesanan Pembelian">
                                            <i class="fa fa-arrow-left"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-md-5 col-form-label kt-font-dark"><span class="text-danger">*</span>TGL. PEMESANAN PEMBELIAN</label>
                                        <div class="col-md-7">
                                            <?php 
                                                $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'date', 'name' => 'date',  'value' => date('d-m-Y'), 'placeholder' => 'Silahkan isi tanggal pemesanan pembelian', 'required' => 'true'); 
                                                echo form_input($data);                                             
                                                echo form_error('date', '<p class="text-danger">', '</p>');
                                            ?> 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <?php if(isset($supplier_code)): ?>
                                            <input type="hidden" id="supplier_code_update" value="<?php echo $supplier_code; ?>">
                                        <?php endif; ?>
                                        <label class="col-md-2 col-form-label kt-font-dark text-right"><span class="text-danger">*</span>SUPPLIER</label>                                            
                                        <div class="col-md-10">
                                            <select class="form-control" name="supplier_code" id="supplier_code"></select>                                                
                                        </div>                                            
                                    </div>
                                </div>                                
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success btn-square form-control" id="btn-search-product" disabled onclick="this.disabled=true;this.form.submit();"><i class="fa fa-search"></i> CARI PRODUK</button>
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
                                    <table class="table table-sm table-bordered table-hover table-checkable" id="datatable">
                                        <thead>
                                            <tr style="text-align:center;">
                                                <th>NO.</th>
                                                <th class="text-center">
                                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" ><input type="checkbox" class="choose-all">&nbsp;<span></span></label>
                                                </th>
                                                <th>KODE</th>
                                                <th>NAMA</th>
                                                <th>MINIMAL</th>
                                                <th>STOK</th>                                                                                                
                                                <th>QTY</th>                                       
                                                <th>MAKSIMAL</th>                                                         
                                                <th>SATUAN</th>
                                                <th>HARGA</th>
                                                <th>TOTAL</th>
                                            </tr>
                                        </thead>
                                        <tbody> 
                                            <?php if(isset($product)): ?>
                                                <?php $no=1; foreach($product AS $info): ?>
                                                <tr>
                                                    <td><?= $no; ?></td>
                                                    <td class="text-center">
                                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
                                                            <input type="checkbox" name="product[]" value="<?php echo $info['code_p']; ?>" class="choose">&nbsp;<span></span>
                                                        </label>
                                                    </td>
                                                    <td><?= $info['code_p']; ?></td>
                                                    <td><?= $info['name_p']; ?></td>
                                                    <td class="text-right kt-font-bold"><?php echo $info['minimal']; ?></td>
                                                    <td class="text-right text-danger kt-font-bold"><?= $info['stock']; ?></td>
                                                    <td><input type="text" class="text-right form-control qty" name="qty-<?php echo $info['code_p']; ?>" value="<?php echo $info['maximal']-$info['stock']; ?>"></td>
                                                    <td class="text-right text-primary kt-font-bold"><?php echo $info['maximal']; ?></td>
                                                    <td class="text-center"><?= $info['name_u']; ?></td>
                                                    <td class="text-right text-dark kt-font-bold">
                                                        <input type="text" class="form-control text-right price" name="price-<?php echo $info['code_p']; ?>" value="<?php echo number_format($info['buyprice'],0,'.',','); ?>" readonly>
                                                    </td>
                                                    <td class="text-right text-dark kt-font-bold">
                                                        <input type="text" class="form-control text-right total" name="total-<?php echo $info['code_p']; ?>" value="<?php echo number_format((($info['maximal']-$info['stock'])*$info['buyprice']),0,'.',','); ?>" readonly>
                                                    </td>
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
                                                $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_product', 'name' => 'total_product',  'value' => set_value('total_product'), 'required' => 'true', 'readonly' => 'true'); 
                                                echo form_input($data);                                             
                                                echo form_error('total_product', '<p class="text-danger">', '</p>');
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>                      
                        </div>
                        <div class="kt-portlet__foot" id="confirm-button">
                            <div class="kt-form__actions">
                                <div class="row">
                                    <div class="col-md-6">
                                        <a href="<?php echo base_url('purchase/order'); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>                                        
                                    </div>
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-save"></i> SIMPAN</button>
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