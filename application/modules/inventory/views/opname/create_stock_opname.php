<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Persediaan</b></h3>
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
                                        <a href="<?php echo site_url('opname'); ?>" class="btn btn-outline-primary btn-elevate btn-icon"
                                            data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Kembali ke Daftar Stock Opname">
                                            <i class="fa fa-arrow-left"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <?php if(isset($products)): ?>
                            <div class="form-group row">                            
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label kt-font-dark"><span class="text-danger">*</span>TGL. STOK OPNAME</label>                                    
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'date', 'name' => 'date',  'value' => date('d-m-Y'), 'placeholder' => 'Silahkan isi tanggal stock opname', 'required' => 'true'); 
                                            echo form_input($data);                                             
                                            echo form_error('date', '<p class="text-danger">', '</p>');
                                        ?>                                        
                                    </div>
                                </div>                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label kt-font-dark"><span class="text-danger">*</span>GUDANG</label>                            
                                        <select class="form-control" name="warehouse_id" id="warehouse_id" required></select>                                        
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label kt-font-dark"><span class="text-danger">*</span>PETUGAS</label>                                        
                                        <select class="form-control" name="employee_code" id="employee_code" required></select>                                        
                                    </div>
                                </div>                            
                            </div>
                            <?php else: ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label kt-font-dark"><span class="text-danger">*</span>DEPARTEMEN PRODUK</label>
                                        <select class="form-control" name="department_code" id="department_code"></select>                                        
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label kt-font-dark"><span class="text-danger">*</span>SUBDEPARTEMEN PRODUK</label> 
                                        <select class="form-control" name="subdepartment_code" id="subdepartment_code">
                                            <option value="">- SEMUA SUBDEPARTEMEN -</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success btn-square form-control" id="btn-search-product" disabled onclick="this.disabled=true;this.form.submit();"><i class="fa fa-search"></i> CARI PRODUK</button>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="form-group text-center" id="loading">
                                 <button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>
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
                                                <th class="text-dark">NO.</th>
                                                <th class="text-dark">
                                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
                                                    <input type="checkbox" class="choose-all">&nbsp;<span></span>
                                                    </label>
                                                </th>
                                                <th class="text-dark">KODE</th>
                                                <th class="text-dark">NAMA</th>
                                                <th class="text-dark">DEPARTEMEN</th>
                                                <th class="text-dark">SUB DEPARTEMEN</th>
                                            </tr>
                                        </thead>
                                        <tbody>   
                                        <?php if(isset($products)): ?>
                                            <?php $no=1; foreach($products AS $product): ?>
                                            <tr>
                                                <td class="text-dark"><?php echo $no; ?></td>
                                                <td class="text-dark text-center">
                                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
                                                        <input type="checkbox" name="product[]" value="<?php echo $product['code_p']; ?>" class="choose">&nbsp;<span></span>
                                                    </label>
                                                </td>
                                                <td class="text-dark text-center"><?php echo $product['code_p']; ?></td>
                                                <td class="text-dark"><?php echo $product['name_p']; ?></td>
                                                <td class="text-dark"><?php echo $product['name_d']; ?></td>
                                                <td class="text-dark"><?php echo $product['name_sd']; ?></td>
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
                                        <a href="<?php echo base_url('stock/opname'); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>                                        
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