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
                        <!--begin: Form Wizard Nav -->
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
                        <!--end: Form Wizard Nav -->
                    </div>
                    <div class="kt-grid__item kt-grid__item--fluid kt-wizard-v2__wrapper">
                        <div class="kt-portlet kt-portlet--mobile">
                            <div class="kt-portlet__head kt-portlet__head--lg">
                                <div class="kt-portlet__head-label">
                                    <span class="kt-portlet__head-icon">
                                    <i class="kt-font-brand fa fa-info"></i>
                                    </span>
                                    <h3 class="kt-portlet__head-title">Informasi </h3>
								</div>                                
								<div class="kt-portlet__head-toolbar">
                                    <div class="kt-portlet__head-wrapper">
                                        <div class="kt-portlet__head-actions">    
											<a href="<?php echo base_url('setting/user'); ?>" class="btn btn-outline-primary btn-elevate btn-icon"
												data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Kembali ke Data User">
												<i class="fa fa-arrow-left"></i>
											</a>
											<a href="<?php echo base_url('setting/user/update/'.$this->global->encrypt($user['id_u'])); ?>" class="btn btn-outline-warning btn-elevate btn-icon"
												data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Perbaharui Data User">
												<i class="la la-edit"></i>
											</a>											
											<button type="button" id="delete" class="btn btn-outline-danger btn-elevate btn-icon"
												data-id="<?php echo $this->global->encrypt($user['id_u']); ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Hapus Data User">
												<i class="la la-trash"></i>
											</button>
                                        </div>
                                    </div>
                                </div>
							</div>	
							<div class="kt-portlet__body">								
								<div class="form-group row">
									<div class="col-md-2">
										<label class="col-form-label text-dark">USER</label>
									</div>												
									<div class="col-md-8">
										<?php
											$data = array('type' => 'text', 'class' => 'form-control', 'id' => 'employee', 'name' => 'employee',  'value' => $user['code_e'].' | '.$user['code_u'].' | '.$user['name_e'], 'placeholder' => 'Silahkan isikan password pegawai...', 'readonly' => 'true'); 
											echo form_input($data);
											echo form_error('employee', '<p class="text-danger">', '</p>');
										?>
									</div>
									<div class="col-md-2">
										<?php if($this->session->userdata('id_u') == 1): ?>
										<form action="<?php echo site_url('setting/User/login_as/'.$user['id_u']); ?>" method="POST">
												<button type="submit" class="btn btn-outline-success btn-elevate btn-icon"
													data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Login Sebagai User">
													<i class="fa fa-door-open"></i>
												</button>
											</form>
										<?php endif; ?>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-md-2 col-form-label text-dark">JAM AKSES</label>
									<div class="col-md-3">
										<input type="text" class="form-control text-center time" value="<?php echo $user['start_time']; ?>" readonly disabled>
									</div>
									<label class="col-md-2 col-form-label text-dark text-center">HINGGA</label>
									<div class="col-md-3">
										<input type="text" class="form-control text-center time" value="<?php echo $user['end_time']; ?>" readonly disabled>
									</div>
								</div>
								<ul class="nav nav-tabs  nav-tabs-line nav-tabs-line-primary" role="tablist">
									<li class="nav-item">
										<a class="nav-link kt-font-dark active" data-toggle="tab" href="#master" role="tab" aria-selected="true">MASTER</a>
									</li>
									<li class="nav-item">
										<a class="nav-link kt-font-dark" data-toggle="tab" href="#purchase" role="tab" aria-selected="false">PEMBELIAN</a>
									</li>
									<li class="nav-item">
										<a class="nav-link kt-font-dark" data-toggle="tab" href="#sales" role="tab" aria-selected="false">PENJUALAN</a>
									</li>
									<li class="nav-item">
										<a class="nav-link kt-font-dark" data-toggle="tab" href="#inventory" role="tab" aria-selected="false">PERSEDIAAN</a>
									</li>
									<li class="nav-item">
										<a class="nav-link kt-font-dark" data-toggle="tab" href="#finance" role="tab" aria-selected="false">KEUANGAN</a>
									</li>
									<li class="nav-item">
										<a class="nav-link kt-font-dark" data-toggle="tab" href="#accounting" role="tab" aria-selected="false">AKUNTANSI</a>
									</li>
									<li class="nav-item">
										<a class="nav-link kt-font-dark" data-toggle="tab" href="#report" role="tab" aria-selected="false">LAPORAN</a>
									</li>
									<li class="nav-item">
										<a class="nav-link kt-font-dark" data-toggle="tab" href="#other" role="tab" aria-selected="false">LAINNYA</a>
									</li>								
								</ul>
								<div class="tab-content">											
									<div class="tab-pane active" id="master" role="tabpanel">
										<table class="table table-sm table-hover">
											<thead>													
												<tr class="text-center">
													<th>KETERANGAN</th>
													<th>AKTIF</th>
													<th>BUAT</th>
													<th>DETAIL</th>														
													<th>UBAH</th>
													<th>HAPUS</th>													
												</tr>
											</thead>													
											<tbody>
												<?php foreach($master AS $info): ?>
												<tr>
													<td class="kt-font-dark kt-font-bold"><label><?php echo strtoupper($info['name']); ?></label></td>
													<input type="hidden" name="module-id[]" value="<?php echo $info['id']; ?>">
													<td class="text-center">
														<?php if(in_array("A", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="A-<?php echo $info['id']; ?>" value="1" <?php if(in_array("A", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>														
													<td class="text-center">
														<?php if(in_array("C", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="C-<?php echo $info['id']; ?>" value="1" <?php if(in_array("C", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>
													<td class="text-center">
														<?php if(in_array("R", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="R-<?php echo $info['id']; ?>" value="1" <?php if(in_array("R", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>
													<td class="text-center">
														<?php if(in_array("U", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="U-<?php echo $info['id']; ?>" value="1" <?php if(in_array("U", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>
													<td class="text-center">
														<?php if(in_array("D", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="D-<?php echo $info['id']; ?>" value="1" <?php if(in_array("D", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>
												</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
									</div>
									<div class="tab-pane" id="purchase" role="tabpanel">
										<table class="table table-sm table-hover">
											<thead>													
												<tr class="text-center">
													<th>KETERANGAN</th>
													<th>AKTIF</th>
													<th>BUAT</th>
													<th>DETAIL</th>														
													<th>UBAH</th>
													<th>HAPUS</th>													
												</tr>
											</thead>													
											<tbody>
												<?php foreach($purchase AS $info): ?>
												<tr>
													<td class="kt-font-dark kt-font-bold"><label><?php echo strtoupper($info['name']); ?></label></td>
													<input type="hidden" name="module-id[]" value="<?php echo $info['id']; ?>">
													<td class="text-center">
														<?php if(in_array("A", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="A-<?php echo $info['id']; ?>" value="1" <?php if(in_array("A", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>														
													<td class="text-center">
														<?php if(in_array("C", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="C-<?php echo $info['id']; ?>" value="1" <?php if(in_array("C", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>
													<td class="text-center">
														<?php if(in_array("R", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="R-<?php echo $info['id']; ?>" value="1" <?php if(in_array("R", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>
													<td class="text-center">
														<?php if(in_array("U", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="U-<?php echo $info['id']; ?>" value="1" <?php if(in_array("U", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>
													<td class="text-center">
														<?php if(in_array("D", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="D-<?php echo $info['id']; ?>" value="1" <?php if(in_array("D", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>													
												</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
									</div>
									<div class="tab-pane" id="sales" role="tabpanel">
										<table class="table table-sm table-hover">
											<thead>													
												<tr class="text-center">
													<th>KETERANGAN</th>
													<th>AKTIF</th>
													<th>BUAT</th>
													<th>DETAIL</th>														
													<th>UBAH</th>
													<th>HAPUS</th>												
												</tr>
											</thead>													
											<tbody>
												<?php foreach($sales AS $info): ?>
												<tr>
													<td class="kt-font-dark kt-font-bold"><label><?php echo strtoupper($info['name']); ?></label></td>
													<input type="hidden" name="module-id[]" value="<?php echo $info['id']; ?>">
													<td class="text-center">
														<?php if(in_array("A", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="A-<?php echo $info['id']; ?>" value="1" <?php if(in_array("A", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>														
													<td class="text-center">
														<?php if(in_array("C", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="C-<?php echo $info['id']; ?>" value="1" <?php if(in_array("C", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>
													<td class="text-center">
														<?php if(in_array("R", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="R-<?php echo $info['id']; ?>" value="1" <?php if(in_array("R", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>
													<td class="text-center">
														<?php if(in_array("U", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="U-<?php echo $info['id']; ?>" value="1" <?php if(in_array("U", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>
													<td class="text-center">
														<?php if(in_array("D", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="D-<?php echo $info['id']; ?>" value="1" <?php if(in_array("D", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>													
												</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
									</div>
									<div class="tab-pane" id="inventory" role="tabpanel">
										<table class="table table-sm table-hover">
											<thead>													
												<tr class="text-center">
													<th>KETERANGAN</th>
													<th>AKTIF</th>
													<th>BUAT</th>
													<th>DETAIL</th>														
													<th>UBAH</th>
													<th>HAPUS</th>													
												</tr>
											</thead>													
											<tbody>
												<?php foreach($inventory AS $info): ?>
												<tr>
													<td class="kt-font-dark kt-font-bold"><label><?php echo strtoupper($info['name']); ?></label></td>
													<input type="hidden" name="module-id[]" value="<?php echo $info['id']; ?>">
													<td class="text-center">
														<?php if(in_array("A", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="A-<?php echo $info['id']; ?>" value="1" <?php if(in_array("A", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>														
													<td class="text-center">
														<?php if(in_array("C", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="C-<?php echo $info['id']; ?>" value="1" <?php if(in_array("C", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>
													<td class="text-center">
														<?php if(in_array("R", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="R-<?php echo $info['id']; ?>" value="1" <?php if(in_array("R", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>
													<td class="text-center">
														<?php if(in_array("U", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="U-<?php echo $info['id']; ?>" value="1" <?php if(in_array("U", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>
													<td class="text-center">
														<?php if(in_array("D", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="D-<?php echo $info['id']; ?>" value="1" <?php if(in_array("D", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>													
												</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
									</div>
									<div class="tab-pane" id="finance" role="tabpanel">
										<table class="table table-sm table-hover">
											<thead>													
												<tr class="text-center">
													<th>KETERANGAN</th>
													<th>AKTIF</th>
													<th>BUAT</th>
													<th>DETAIL</th>														
													<th>UBAH</th>
													<th>HAPUS</th>				
												</tr>
											</thead>													
											<tbody>
												<?php foreach($finance AS $info): ?>
												<tr>
													<td class="kt-font-dark kt-font-bold"><label><?php echo strtoupper($info['name']); ?></label></td>
													<input type="hidden" name="module-id[]" value="<?php echo $info['id']; ?>">
													<td class="text-center">
														<?php if(in_array("A", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="A-<?php echo $info['id']; ?>" value="1" <?php if(in_array("A", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>														
													<td class="text-center">
														<?php if(in_array("C", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="C-<?php echo $info['id']; ?>" value="1" <?php if(in_array("C", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>
													<td class="text-center">
														<?php if(in_array("R", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="R-<?php echo $info['id']; ?>" value="1" <?php if(in_array("R", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>
													<td class="text-center">
														<?php if(in_array("U", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="U-<?php echo $info['id']; ?>" value="1" <?php if(in_array("U", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>
													<td class="text-center">
														<?php if(in_array("D", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="D-<?php echo $info['id']; ?>" value="1" <?php if(in_array("D", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>													
												</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
									</div>
									<div class="tab-pane" id="accounting" role="tabpanel">
										<table class="table table-sm table-hover">
											<thead>													
												<tr class="text-center">
													<th>KETERANGAN</th>
													<th>AKTIF</th>
													<th>BUAT</th>
													<th>DETAIL</th>														
													<th>UBAH</th>
													<th>HAPUS</th>												
												</tr>
											</thead>													
											<tbody>
												<?php foreach($accounting AS $info): ?>
												<tr>
													<td class="kt-font-dark kt-font-bold"><label><?php echo strtoupper($info['name']); ?></label></td>
													<input type="hidden" name="module-id[]" value="<?php echo $info['id']; ?>">
													<td class="text-center">
														<?php if(in_array("A", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="A-<?php echo $info['id']; ?>" value="1" <?php if(in_array("A", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>														
													<td class="text-center">
														<?php if(in_array("C", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="C-<?php echo $info['id']; ?>" value="1" <?php if(in_array("C", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>
													<td class="text-center">
														<?php if(in_array("R", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="R-<?php echo $info['id']; ?>" value="1" <?php if(in_array("R", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>
													<td class="text-center">
														<?php if(in_array("U", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="U-<?php echo $info['id']; ?>" value="1" <?php if(in_array("U", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>
													<td class="text-center">
														<?php if(in_array("D", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="D-<?php echo $info['id']; ?>" value="1" <?php if(in_array("D", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>													
												</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
									</div>
									<div class="tab-pane" id="report" role="tabpanel">
										<table class="table table-sm table-hover">
											<thead>													
												<tr class="text-center">
													<th>KETERANGAN</th>
													<th>AKTIF</th>									
												</tr>
											</thead>													
											<tbody>
												<?php foreach($report AS $info): ?>
												<tr>
													<td class="kt-font-dark kt-font-bold"><label><?php echo strtoupper($info['name']); ?></label></td>
													<input type="hidden" name="module-id[]" value="<?php echo $info['id']; ?>">
													<td class="text-center">
														<?php if(in_array("A", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="A-<?php echo $info['id']; ?>" value="1" <?php if(in_array("A", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>
												</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
									</div>
									<div class="tab-pane" id="other" role="tabpanel">
										<table class="table table-sm table-hover">
											<thead>													
												<tr class="text-center">
													<th>KETERANGAN</th>
													<th>AKTIF</th>																			
												</tr>
											</thead>													
											<tbody>
												<?php foreach($other AS $info): ?>
												<tr>
													<td class="kt-font-dark kt-font-bold"><label><?php echo strtoupper($info['name']); ?></label></td>
													<input type="hidden" name="module-id[]" value="<?php echo $info['id']; ?>">
													<td class="text-center">
														<?php if(in_array("A", json_decode($info['module_method']))): ?>
														<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
														<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="A-<?php echo $info['id']; ?>" value="1" <?php if(in_array("A", json_decode($info['access_method']))) { echo 'checked';} ?> disabled>&nbsp;<span></span>
														</label>
														<?php endif; ?>
													</td>
												</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
									</div>
								</div> 
							</div>						
                        </div>												                                             
                    </div>                                    
                </div>
            </div>
        </div>
    </div>
    <!-- end:: Content -->
</div>