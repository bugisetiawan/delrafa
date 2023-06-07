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
            </div>
            <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="la la-close"></i></span>
                </button>
            </div>            
        </div>
        <?php endif;?>          
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
                            <a href="<?php echo site_url('delivery'); ?>" class="btn btn-outline-primary btn-elevate btn-icon"
                                data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Kembali">
                            <i class="fa fa-arrow-left"></i>
                            </a>                            
                            <a class="btn btn-icon btn-outline-warning"
                                href="<?php echo site_url('delivery/update/'.encrypt_custom($delivery['id'])); ?>"  data-id="<?php echo $delivery['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Perbarui Pengiriman">
                            <i class="fa fa-edit"></i>
                            </a>
                            <button class="btn btn-icon btn-outline-danger" id="delete_sales_invoice_btn"
                                data-id="<?php echo $delivery['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Hapus Pengiriman">
                            <i class="fa fa-trash"></i>
                            </button>
                            <a href="<?php echo site_url('delivery/print/'. encrypt_custom($delivery['id'])); ?>" target="_blank" class="btn btn-icon btn-outline-success btn-elevate"
                                data-id="<?php echo $delivery['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Cetak Pengiriman">
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
                            <label class="col-md-3 col-form-label kt-font-dark">TGL. PENGIRIMAN</label>
                            <div class="col-md-9">
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control font-weight-bold', 'value' => date('d-m-Y', strtotime($delivery['date'])), 'readonly' => 'true'); 
                                    echo form_input($data);
                                    ?>
                            </div>
                        </div>
                    </div>                    
                </div>                
            </div>
        </div>
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Daftar Transaksi
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-md-12"> 
                        <input type="hidden" id="delivery_id" value="<?php echo $delivery['id']; ?>">
                        <table class="table table-bordered table-hover" id="datatable">
                            <thead>
                                <tr style="text-align:center;">
                                    <th>NO</th>
                                    <th>TANGGAL</th>
                                    <th>NO. TRANSAKSI</th>
                                    <th>PELANGGAN</th>
                                    <th>SALES</th>
                                    <th>TOTAL</th>                                    
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