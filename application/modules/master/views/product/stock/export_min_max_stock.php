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
        <div class="alert alert-dark alert-elevate fade show" role="alert">
            <div class="alert-icon"><i class="flaticon-exclamation-2"></i></div>
            <div class="alert-text">
                Silahkan pilih produk yang akan dilakukan export, terima kasih                
            </div>
        </div>
        <div class="kt-portlet kt-portlet--mobile">
            <?php echo form_open_multipart('', ['autocomplete'=>'off']); ?>
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand fa fa-clipboard-list"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">
                        Daftar Produk
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">                          
                            <a href="<?php echo site_url('product'); ?>" class="btn btn-outline-primary btn-elevate btn-icon"
                                data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Kembali ke Daftar Produk">
                                <i class="fa fa-arrow-left"></i>
                            </a>
                            <a href="<?php echo site_url('product/import/min_max_stock'); ?>" class="btn btn-outline-success btn-elevate"
                                data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Import Stok Minimal dan Maksimal">
                                <i class="fa fa-upload"></i> Import
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-form-label kt-font-dark"><span class="text-danger">*</span>DEPARTEMEN</label>
                            <select class="form-control" name="department_code" id="department_code"></select>                                        
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-form-label kt-font-dark"><span class="text-danger">*</span>SUB DEPARTEMEN</label> 
                            <select class="form-control" name="subdepartment_code" id="subdepartment_code">
                                <option value="">-- SEMUA SUB DEPARTEMEN --</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <!--begin: Datatable -->
                        <table class="table table-bordered table-hover table-checkable" id="datatable">
                            <thead>
                                <tr style="text-align:center;">
                                    <th>NO.</th>
                                    <th>
                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
                                        <input type="checkbox" class="choose-all">&nbsp;<span></span>
                                        </label>
                                    </th>
                                    <th>KODE</th>
                                    <th>NAMA</th>
                                    <th>DEPARTEMEN</th>
                                    <th>SUB DEPARTEMEN</th>
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
                            <a href="<?php echo base_url('product'); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>                                        
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-file-export"></i> EXPORT</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_close() ?>
        </div>
    </div>
    <!-- end:: Content -->
</div>