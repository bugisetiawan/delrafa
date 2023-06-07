<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Manajemen Aplikasi</h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>
            <h3 class="kt-subheader__title"><b><?php echo $title; ?></b></h3>
        </div>
    </div>
    <!-- end:: Content Head -->    
    <!-- begin:: Content -->
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
        <div class="kt-portlet">
            <div class="kt-portlet__body kt-portlet__body--fit">
                <div class="kt-grid  kt-wizard-v2 kt-wizard-v2--white" id="kt_wizard_v2">
                    <div class="kt-grid__item kt-wizard-v2__aside">
                        <!--begin:Nav -->
                        <div class="kt-wizard-v2__nav">
                            <div class="kt-wizard-v2__nav-items">
                                <div class="kt-wizard-v2__nav-item">
									<h2 style="padding-left:5%;">Pengaturan</h2>
									<hr>
                                </div>
                                <?php if($this->session->userdata('id_u') == 1): ?>	
                                <a class="kt-wizard-v2__nav-item" href="<?php echo base_url('setting/profile'); ?>" data-ktwizard-type="step">
                                    <div class="kt-wizard-v2__nav-body">
                                        <div class="kt-wizard-v2__nav-icon">
                                            <i class="flaticon-globe"></i>
                                        </div>
                                        <div class="kt-wizard-v2__nav-label">
                                            <div class="kt-wizard-v2__nav-label-title">
                                                Profil Perusahaan
                                            </div>
                                            <div class="kt-wizard-v2__nav-label-desc">
                                                Mengatur Nama Perusahan, Alamat, dll.
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <?php endif; ?>
                                <a class="kt-wizard-v2__nav-item" href="<?php echo base_url('setting'); ?>" data-ktwizard-type="step" data-ktwizard-state="current">
                                    <div class="kt-wizard-v2__nav-body">
                                        <div class="kt-wizard-v2__nav-icon">
                                            <i class="flaticon-user"></i>
                                        </div>
                                        <div class="kt-wizard-v2__nav-label">
                                            <div class="kt-wizard-v2__nav-label-title">
                                                User
                                            </div>
                                            <div class="kt-wizard-v2__nav-label-desc">
                                                Mengatur Daftar User dan Akses di aplikasi
                                            </div>
                                        </div>
                                    </div>
                                </a>                                                            
                            </div>
                        </div>
                        <!--end:Nav -->
                    </div>
                    <div class="kt-grid__item kt-grid__item--fluid kt-wizard-v2__wrapper">
                        <div class="kt-portlet kt-portlet--mobile">
                            <div class="kt-portlet__head kt-portlet__head--lg">
                                <div class="kt-portlet__head-label">
                                    <span class="kt-portlet__head-icon">
                                    <i class="kt-font-brand fa fa-clipboard-list"></i>
                                    </span>
                                    <h3 class="kt-portlet__head-title">
                                    </h3>
                                </div>
                                <div class="kt-portlet__head-toolbar">
                                    <div class="kt-portlet__head-wrapper">
                                        <div class="kt-portlet__head-actions">
                                            <a href="javascript: void(0);" onclick="$('#datatable').DataTable().ajax.reload();" class="btn btn-square btn-brand btn-elevate btn-elevate-air">
												<i class="la la-refresh"></i>
												<span class="d-none d-sm-inline"> Refresh Data</span>
                                            </a>
                                            <a href="<?php echo base_url('setting/user/create'); ?>" class="btn btn-square btn-success btn-elevate btn-elevate-air">
												<i class="la la-plus"></i>
												<span class="d-none d-sm-inline"> User Baru</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="kt-portlet__body">
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
                                <!--begin: Datatable -->
                                <table class="table table-striped- table-bordered table-hover table-checkable" id="datatable">
                                    <thead>
                                        <tr style="text-align:center;">
                                            <th>NO.</th>
                                            <th>KODE</th>
                                            <th>NAMA</th>
                                            <th>STATUS</th>                                            
                                        </tr>
                                    </thead>
                                    <tbody id="table_data"></tbody>
                                </table>
                                <!--end: Datatable -->
                            </div>
                        </div>
                    </div>
                    <!-- Begin Modal -->                   
                    <!--begin::Update Modal-->
                    <div class="modal fade" id="update_form">
                        <div class="modal-dialog modal-lg">
                            <?php echo form_open('setting/role/update', array('id' => 'update_data', 'autocomplete' => 'off')); ?>
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Data <?php echo $title; ?></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    </button>
                                </div>
                                <div class="modal-body">     
                                    <div class="kt-wizard-v2__form">
                                        <div class="form-group">                                        
                                            <label>Pegawai</label>
                                            <?php 
                                                $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'employee_name', 'name' => 'employee_name',  'value' => set_value('employee_name'), 'required' => 'true', 'readonly' => 'true'); 
                                                echo form_input($data);                                             
                                            ?>      
                                        </div>                                          
                                        <div class="form-group">
                                            <label class="form-control-label">Role:</label>                            
                                            <select class="form-control" id="role_id" name="role_id">
                                                <option value="">--- Pilih Role ---</option>
                                            </select>
                                        </div>                                   
                                    </div>                                                                                                             
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger btn-brand btn-elevate-hover btn-square" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
                                    <button type="submit" class="btn btn-success btn-brand btn-elevate-hover btn-square"><i class="fa fa-save"></i> SIMPAN</button>
                                </div>
                            </div>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                    <!--end::Update Modal-->
                </div>
            </div>
        </div>
    </div>
    <!-- end:: Content -->
</div>