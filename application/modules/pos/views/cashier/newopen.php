<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
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
        <?php echo form_open_multipart('', ['autocomplete' => 'off']); ?>        
        <div class="container">
            <div class="kt-portlet">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title kt-font-primary kt-font-bold">
                            PEMBUKAAN KASIR
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <div class="row">
                        <div class="col-md-12">                            
                            <div class="form-group">
                                <label class="col-form-label kt-font-dark kt-font-bold">MODAL KASIR (<?php echo date('d-m-Y'); ?>)</label>
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control form-control-lg', 'id' => 'modal', 'name' => 'modal', 'placeholder' => 'Silahkan isikan Modal Kasir untuk melanjutkan...',  'value' => set_value('modal'), 'required' => 'true', 'autofocus' => 'true'); 
                                    echo form_input($data);                                             
                                    echo form_error('modal', '<p class="text-danger">', '</p>');
                                ?>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <a href="<?php echo base_url('dashboard'); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-arrow-left"></i>KEMBALI</a>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-save"></i> BUKA KASIR</button>
                                </div>                                                                
                            </div>
                        </div>                        
                    </div>
                </div>            
            </div>        
        </div>        
        <?php echo form_close(); ?>
    </div>
    <!-- end:: Content -->
</div>