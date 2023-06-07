<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Manajemen <b>Aplikasi</b></h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>
            <span class="kt-subheader__desc"><strong><?php echo $title; ?></strong></span>
        </div>
    </div>
    <!-- end:: Content Head -->    
    <!-- begin:: Content -->
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
        <div class="kt-portlet">
            <div class="kt-portlet__body kt-portlet__body--fit">
                <div class="kt-grid  kt-wizard-v2 kt-wizard-v2--white" id="kt_wizard_v2">
                    <div class="kt-grid__item kt-wizard-v2__aside">
                        <!--begin: Form Wizard Nav -->
                        <div class="kt-wizard-v2__nav">
                            <div class="kt-wizard-v2__nav-items">
								<div class="kt-wizard-v2__nav-item">
									<h2 style="padding-left:5%;">Pengaturan</h2>
									<hr>
								</div>	
								<?php if($this->session->userdata('id_u') == 1): ?>	
                                <a class="kt-wizard-v2__nav-item" href="<?php echo base_url('setting/profile'); ?>" data-ktwizard-type="step" data-ktwizard-state="current">
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
                                <a class="kt-wizard-v2__nav-item" href="<?php echo base_url('setting'); ?>" data-ktwizard-type="step">
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
                        <!--end: Form Wizard Nav -->
                    </div>
                    <div class="kt-grid__item kt-grid__item--fluid kt-wizard-v2__wrapper">
						<div class="kt-portlet kt-portlet--mobile">
							<div class="kt-portlet__head kt-portlet__head--lg">
								<div class="kt-portlet__head-label">
									<span class="kt-portlet__head-icon">
									<i class="kt-font-brand fa fa-clipboard-list"></i>
									</span>
									<h3 class="kt-portlet__head-title">
										Informasi <b><?php echo $title; ?></b>
									</h3>
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
							<?php echo form_open('setting/Profile/save', ['autocomplete' => 'off']); ?>
								<div class="form-group">
									<label>Nama</label>
									<?php 
										$data = array('type' => 'text', 'class' => 'form-control', 'id' => 'name', 'name' => 'name',  'value' => $profile->name, 'placeholder' => 'Silahkan isikan Nama Perusahaan', 'required' => 'true'); 
										echo form_input($data);                                             
										echo form_error('name', '<p class="text-danger">', '</p>');
									?>											
									<span class="form-text text-muted">Silahkan isi Nama Perusahaan.</span>
								</div>
								<div class="form-group">
									<label>Alamat</label>
									<?php 
										$data = array('type' => 'text', 'class' => 'form-control', 'id' => 'address', 'name' => 'address',  'value' => $profile->address, 'placeholder' => 'Silahkan isikan Alamat Perusahaan', 'required' => 'true'); 
										echo form_input($data);                                             
										echo form_error('address', '<p class="text-danger">', '</p>');
									?>
									<span class="form-text text-muted">Silahkan isi Alamat Perusahaan.</span>
								</div>
								<div class="form-group">
									<label>No. Handphone</label>
									<?php 
										$data = array('type' => 'text', 'class' => 'form-control', 'id' => 'phone', 'name' => 'phone',  'value' => $profile->phone, 'placeholder' => 'Silahkan isikan No.Handphone Perusahaan', 'required' => 'true'); 
										echo form_input($data);                                             
										echo form_error('phone', '<p class="text-danger">', '</p>');
									?>
									<span class="form-text text-muted">Silahkan isi No. Handphone Perusahaan.</span>
								</div>
								<div class="form-group">
									<label>No. Telepon</label>
									<?php 
										$data = array('type' => 'text', 'class' => 'form-control', 'id' => 'telephone', 'name' => 'telephone',  'value' => $profile->telephone, 'placeholder' => 'Silahkan isikan No.Telepon Perusahaan', 'required' => 'true'); 
										echo form_input($data);                                             
										echo form_error('telephone', '<p class="text-danger">', '</p>');
									?>
									<span class="form-text text-muted">Silahkan isi No. Telepon Perusahaan.</span>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-md-9"></div>
										<div class="col-md-3 text-right">
											<button type="submit" class="form-control btn btn-success btn-square"><i class="fa fa-save"></i> SIMPAN</button>
										</div>
									</div>
								</div>	
							<?php echo form_close(); ?>
							</div>							
						</div>
					</div>									
                </div>
            </div>
        </div>
    </div>
    <!-- end:: Content -->
</div>