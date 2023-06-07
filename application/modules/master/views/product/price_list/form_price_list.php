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
                <?php echo form_open_multipart('', ['target' => '_blank', 'autocomplete' => 'off']); ?>                
                <!--begin::Portlet-->
                <div class="kt-portlet">
                    <div class="kt-portlet__body">
                        <div class="kt-section">
                            <div class="kt-section__body">
                                <div class="from-group row">
                                    <div class="col-md-4">
                                        <label class="text-dark">NAMA</label>
                                        <input type="text" class="form-control" id="search" name="search" placeholder="Silahkan isi nama produk untuk melakukan pencarian..." autofocus>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="text-dark"><span class="text-danger">*</span>DEPARTEMEN</label>                                            
                                            <select class="form-control kt-select2 department_select2" id="department_code" name="department_code"></select>
                                            <?php echo form_error('department_code', '<p class="text-danger">', '</p>'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="text-dark text-right"><span class="text-danger">*</span>SUBDEPARTEMEN</label>
                                            <select class="form-control kt-select2" id="subdepartment_code" name="subdepartment_code">
                                                <option value="">- SEMUA SUBDEPARTEMEN -</option>
                                            </select>
                                            <?php echo form_error('subdepartment_code', '<p class="text-danger">', '</p>'); ?>                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <div class="kt-checkbox-inline">
                                            <?php for($i=2;$i<=5;$i++): ?>
                                                <?php if($this->system->check_access('view_sellprice_'.$i, 'read')): ?>
                                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand text-dark">
                                                    <input type="checkbox" name="<?php echo $i; ?>" value="sellprice.price_<?php echo $i; ?> AS price_<?php echo $i; ?>">HARGA JUAL <?php echo $i; ?>
                                                    <span></span>
                                                    </label>
                                                <?php endif; ?>                                                
                                            <?php endfor; ?>
                                            <?php $access_user_id = [1, 3, 14, 17, 9];
                                            if(in_array($this->session->userdata('id_u'), $access_user_id)): ?>
                                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand text-dark">
                                                <input type="checkbox" name="6" value="product.buyprice AS buyprice">HARGA BELI
                                                <span></span>
                                                </label>
                                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand text-dark">
                                                <input type="checkbox" name="7" value="product.hpp AS hpp">HPP
                                                <span></span>
                                                </label>
                                            <?php endif; ?>                                                
                                        </div> 
                                    </div>
                                </div>                                                                
                            </div>                            
                        </div>
                    </div>
                    <div class="kt-portlet__foot">
                        <div class="kt-form__actions">
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="<?php echo site_url('product'); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>
                                </div>
                                <div class="col-md-6">                                
                                    <button type="submit" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-print"></i> CETAK</button>
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