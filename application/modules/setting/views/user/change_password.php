<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">User <b>Aplikasi</b></h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>
            <span class="kt-subheader__desc"><strong><?php echo $title; ?></strong></span>
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
        <?php echo form_open_multipart('', ['autocomplete' => 'off']); ?>
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-icon">
                        <i class="kt-font-brand fa fa-info"></i>
                        </span>
                        <h3 class="kt-portlet__head-title">                                        
                        </h3>
                    </div>                                
                </div>							
                <div class="kt-portlet__body">
                    <?php 
                        $data = array('type' => 'hidden', 'id' => 'id_u', 'name' => 'id_u',  'value' => $this->global->encrypt($user['id_u'])); 
                        echo form_input($data);
                    ?>
                    <div class="form-group row">
                        <div class="col-md-6">
                        <label class="kt-font-dark">PEGAWAI</label>
                        <?php 
                            $data = array('type' => 'text', 'class' => 'form-control', 'value' => $user['code_e'].' | '.$user['name_e'], 'readonly' => 'true'); 
                            echo form_input($data);
                        ?>
                        </div>
                        <div class="col-md-6">
                            <label class="kt-font-dark">PASSWORD BARU</label>
                            <div class="input-group">
                                <?php 
                                    $data = array('type' => 'password', 'class' => 'form-control', 'placeholder' => 'Silahkan isikan password baru...', 'name' => 'password', 'id' => 'password', 'value' => set_value('password'));
                                    echo form_input($data);
                                    echo form_error('password', '<p class="text-danger">', '</p>');
                                ?> 
                                <div class="input-group-append"><span class="input-group-text" id="view-password"><i class="fa fa-eye"></i></span></div>
                            </div>                                            
                            <small class="kt-font-bold">*Silahkan kosongkan apa bila tidak ingin mengganti password.</small>
                        </div>                        
                    </div>
                </div>
                <div class="kt-portlet__foot">
                    <div class="kt-form__actions">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="<?php echo base_url('dashboard'); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>
                            </div>
                            <div class="col-md-6">                                
                                <button type="submit" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-save"></i> SIMPAN</button>
                            </div>
                        </div>
                    </div>
                </div>							
            </div>   
        <?php echo form_close(); ?>
    </div>
    <!-- end:: Content -->
</div>