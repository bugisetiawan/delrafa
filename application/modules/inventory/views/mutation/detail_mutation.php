<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Persediaan</h3>
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
            <div class="alert-text">
                <?php echo $this->session->flashdata('error'); ?><br>
                <?php if($this->session->flashdata('min_product')) :?>
                <?php foreach($this->session->flashdata('min_product') AS $min_product): ?>
                    <?php echo $min_product['code'].' | '.$min_product['name']; ?><br>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
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
                        Informasi | No. Transaksi <span class="text-success"><b><?php echo $mutation['code']; ?></b></span>
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                            <a href="<?php echo base_url('mutation'); ?>" class="btn btn-outline-primary btn-elevate btn-icon"
                                data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Kembali ke Daftar Mutasi">
                                <i class="fa fa-arrow-left"></i>
                            </a>
                            <?php if($mutation['do_status'] != 1): ?>
                                <a class="btn btn-icon btn-outline-warning"
                                    href="<?php echo site_url('mutation/update/'.encrypt_custom($mutation['id'])); ?>"  data-id="<?php echo $mutation['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Perbarui Mutasi">
                                <i class="fa fa-edit"></i>
                                </a>
                                <button class="btn btn-icon btn-outline-danger" id="delete_btn"
                                    data-id="<?php echo $mutation['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Hapus Mutasi">
                                    <i class="fa fa-trash"></i>
                                </button>
                                <button class="btn btn-outline-primary btn-elevate" id="create_do_btn" 
                                    data-id="<?php echo $mutation['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Cetak DO">
                                    <i class="fa fa-print"></i>
                                    Cetak DO
                                </button>
                            <?php else: ?>
                                <button class="btn btn-outline-danger btn-elevate" id="cancel_do_btn"
                                    data-link="<?php echo site_url('inventory/Mutation/cancel_do/'.encrypt_custom($mutation['id'])); ?>" data-id="<?php echo $mutation['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Batal DO">
                                    <i class="fa fa-times"></i>
                                    Batal DO
                                </button>
                            <?php endif; ?>
                        </div>                                
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label kt-font-dark">TGL. MUTASI</label>
                            <div class="col-md-8">                                                
                                <?php
                                    $data = array('type' => 'text', 'class' => 'form-control', 'value' => date('d-m-Y', strtotime($mutation['date'])), 'readonly' => 'true'); 
                                    echo form_input($data);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label kt-font-dark text-right">PETUGAS</label>
                            <div class="col-md-8">                                        
                                <?php
                                    $data = array('type' => 'text', 'class' => 'form-control', 'value' => $mutation['code_c'].' | '. $mutation['name_c'], 'readonly' => 'true'); 
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
                <div class="row">
                    <div class="col-md-12">
                        <?php
                            $data = array('type' => 'hidden', 'id' => 'mutation_id', 'class' => 'form-control', 'value' => $mutation['id'], 'readonly' => 'true'); 
                            echo form_input($data);
                        ?>
                        <table class="table table-bordered table-hover" id="datatable_detail_mutation">
                            <thead>
                                <tr style="text-align:center;">
                                    <th>NO.</th>
                                    <th>KODE</th>
                                    <th>NAMA</th>
                                    <th width=85px;>QTY</th>
                                    <th width=100px;>SATUAN</th>
                                    <th>GUDANG ASAL</th>
                                    <th>GUDANG TUJUAN</th>
                                </tr>
                            </thead>
                        </table>
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
                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'value' => $mutation['total_product'], 'readonly' => 'true', 'readonly' => 'true'); 
                                    echo form_input($data);
                                ?>
                            </div>
                            <label class="col-md-3 col-form-label kt-font-dark text-right">TOTAL KUANTITAS</label>
                            <div class="col-md-3">
                                <?php
                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'value' => $mutation['total_qty'], 'readonly' => 'true', 'readonly' => 'true'); 
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