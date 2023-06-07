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
        <div class="row">
            <div class="col-lg-12">
                <!--begin::Form-->
                <?php echo form_open_multipart('', ['id' => 'form_print_stock_product', 'target' => '_blank', 'autocomplete' => 'off']); ?>                
                <!--begin::Portlet-->
                <div class="kt-portlet">
                    <div class="kt-portlet__body">
                        <div class="kt-section">
                            <div class="kt-section__body">
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <label class="text-dark">NAMA PRODUK</label>
                                        <input type="text" class="form-control" id="search" name="search" placeholder="Mengandung nama..." autofocus>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">                                        
                                        <label class="text-dark">DEPARTEMEN</label>
                                        <select class="form-control" id="department_code" name="department_code"></select>
                                    </div>
                                    <div class="col-md-3">                                        
                                        <label class="text-dark">SUBDEPARTEMEN</label>                                            
                                        <select class="form-control" id="subdepartment_code" name="subdepartment_code">
                                            <option value="">- SEMUA SUBDEPARTEMEN -</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="text-dark">GUDANG</label>                                            
                                        <select class="form-control" id="warehouse_id" name="warehouse_id">
                                            <option value="">- SEMUA GUDANG -</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="text-dark"><span class="text-danger">*</span>STOK MINIMAL</label>
                                        <input type="text" class="form-control" id="min" name="min" placeholder="Stock Minimal..." value="0">
                                        <small class="text-dark"><span class="text-danger">*</span>Dalam Satuan Dasar</small>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="text-dark"><span class="text-danger">*</span>STOK MAKSIMAL</label>
                                        <input type="text" class="form-control" id="max" name="max" placeholder="Stock Maksimal..." value="999999">
                                        <small class="text-dark"><span class="text-danger">*</span>Dalam Satuan Dasar</small>
                                    </div>
                                </div>
                            </div>                            
                        </div>
                    </div>
                    <div class="kt-portlet__foot">
                        <div class="kt-form__actions">
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="<?php echo base_url('product'); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>
                                </div>
                                <div class="col-md-6">                                
                                    <button type="submit" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control" id="btn_print_stock_product"><i class="fa fa-print"></i> CETAK</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
                <!--end::Form-->
            </div>
        </div>
    </div>
    <!-- end:: Content -->
</div>