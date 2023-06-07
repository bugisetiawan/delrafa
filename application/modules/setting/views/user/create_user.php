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
                        <!--begin: Nav -->
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
                        <!--end: Nav -->
                    </div>
                    <div class="kt-grid__item kt-grid__item--fluid kt-wizard-v2__wrapper">
					<?php echo form_open_multipart('', ['autocomplete' => 'off']); ?>
                        <div class="kt-portlet kt-portlet--mobile">
                            <div class="kt-portlet__head kt-portlet__head--lg">
                                <div class="kt-portlet__head-label">
                                    <span class="kt-portlet__head-icon">
                                    <i class="kt-font-brand fa fa-info"></i>
                                    </span>
                                    <h3 class="kt-portlet__head-title">
                                        Informasi
                                    </h3>
                                </div>
							</div>
							<div class="kt-portlet__body">						
									<div class="form-group row">
										<div class="col-md-2">
											<label class="col-form-label text-dark"><span class="text-danger">*</span>KODE</label>
										</div>							
										<div class="col-md-10">
											<?php 
												$data = array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Silahkan isi kode user... (3 Karakter Huruf)', 'name' => 'code', 'id' => 'code', 'value' => set_value('code'), 'required' => 'true', 'autofocus' => true);
												echo form_input($data);
												echo form_error('code', '<p class="text-danger">', '</p>');
											?>
											<span style="color:red;" id="code_user_message"></span>
										</div>													
									</div>
									<div class="form-group row">
										<div class="col-md-2">
											<label class="col-form-label text-dark"><span class="text-danger">*</span>NAMA</label>
										</div>							
										<div class="col-md-10">
											<?php 
												$data = array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Silahkan isi nama user...', 'name' => 'name', 'id' => 'name', 'value' => set_value('name'), 'required' => 'true');
												echo form_input($data);
												echo form_error('name', '<p class="text-danger">', '</p>');
											?>
											<span style="color:red;" id="name_user_message"></span>
										</div>													
									</div>
									<div class="form-group row">
										<label class="col-md-2 col-form-label text-dark"><span class="text-danger">*</span>JAM AKSES</label>
										<div class="col-md-3">
											<input type="text" class="form-control text-center time" name="start_time" value="08:00" required>
										</div>
										<label class="col-md-2 col-form-label text-dark text-center">HINGGA</label>
										<div class="col-md-3">
											<input type="text" class="form-control text-center time" name="end_time" value="16:00" required>
										</div>
									</div>
									<div class="form-group row">
										<div class="col-md-2">
											<label class="col-form-label text-dark"><span class="text-danger">*</span>KATA SANDI</label>
										</div>
										<div class="col-md-10">
											<div class="input-group">
												<?php 
													$data = array('type' => 'password', 'class' => 'form-control', 'placeholder' => 'Silahkan isi password...', 'name' => 'password', 'id' => 'password', 'value' => set_value('password'), 'required' => 'true', 'autocomplete' => 'off');
													echo form_input($data);
													echo form_error('password', '<p class="text-danger">', '</p>');
												?> 
												<div class="input-group-append"><span class="input-group-text" id="view-password"><i class="fa fa-eye"></i></span></div>
											</div>       
											<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
                                                <input type="checkbox" id="view-password-check">Lihat Kata Sandi<span></span>
                                            </label>                                      
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
														<th>SEMUA</th>
													</tr>
												</thead>													
												<tbody>
													<?php foreach($master AS $info): ?>
													<tr>
														<td class="kt-font-dark kt-font-bold"><label><?php echo strtoupper($info['name']); ?></label></td>
														<input type="hidden" name="module-id[]" value="<?php echo $info['id']; ?>">
														<td class="text-center">
															<?php if(in_array("A", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="A-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>														
														<td class="text-center">
															<?php if(in_array("C", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="C-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">
															<?php if(in_array("R", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="R-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">
															<?php if(in_array("U", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="U-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">
															<?php if(in_array("D", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="D-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">														
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-all-<?php echo $info['id']; ?> check-all-module" data-module-id="<?php echo $info['id']; ?>" >&nbsp;<span></span>
															</label>
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
														<th>SEMUA</th>
													</tr>
												</thead>													
												<tbody>
													<?php foreach($purchase AS $info): ?>
													<tr>
														<td class="kt-font-dark kt-font-bold"><label><?php echo strtoupper($info['name']); ?></label></td>
														<input type="hidden" name="module-id[]" value="<?php echo $info['id']; ?>">
														<td class="text-center">
															<?php if(in_array("A", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="A-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>														
														<td class="text-center">
															<?php if(in_array("C", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="C-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">
															<?php if(in_array("R", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="R-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">
															<?php if(in_array("U", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="U-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">
															<?php if(in_array("D", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="D-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">														
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-all-<?php echo $info['id']; ?> check-all-module" data-module-id="<?php echo $info['id']; ?>" >&nbsp;<span></span>
															</label>
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
														<th>SEMUA</th>
													</tr>
												</thead>													
												<tbody>
													<?php foreach($sales AS $info): ?>
													<tr>
														<td class="kt-font-dark kt-font-bold"><label><?php echo strtoupper($info['name']); ?></label></td>
														<input type="hidden" name="module-id[]" value="<?php echo $info['id']; ?>">
														<td class="text-center">
															<?php if(in_array("A", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="A-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>														
														<td class="text-center">
															<?php if(in_array("C", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="C-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">
															<?php if(in_array("R", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="R-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">
															<?php if(in_array("U", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="U-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">
															<?php if(in_array("D", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="D-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">														
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-all-<?php echo $info['id']; ?> check-all-module" data-module-id="<?php echo $info['id']; ?>" >&nbsp;<span></span>
															</label>
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
														<th>SEMUA</th>
													</tr>
												</thead>													
												<tbody>
													<?php foreach($inventory AS $info): ?>
													<tr>
														<td class="kt-font-dark kt-font-bold"><label><?php echo strtoupper($info['name']); ?></label></td>
														<input type="hidden" name="module-id[]" value="<?php echo $info['id']; ?>">
														<td class="text-center">
															<?php if(in_array("A", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="A-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>														
														<td class="text-center">
															<?php if(in_array("C", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="C-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">
															<?php if(in_array("R", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="R-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">
															<?php if(in_array("U", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="U-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">
															<?php if(in_array("D", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="D-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">														
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-all-<?php echo $info['id']; ?> check-all-module" data-module-id="<?php echo $info['id']; ?>" >&nbsp;<span></span>
															</label>
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
														<th>SEMUA</th>
													</tr>
												</thead>													
												<tbody>
													<?php foreach($finance AS $info): ?>
													<tr>
														<td class="kt-font-dark kt-font-bold"><label><?php echo strtoupper($info['name']); ?></label></td>
														<input type="hidden" name="module-id[]" value="<?php echo $info['id']; ?>">
														<td class="text-center">
															<?php if(in_array("A", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="A-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>														
														<td class="text-center">
															<?php if(in_array("C", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="C-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">
															<?php if(in_array("R", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="R-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">
															<?php if(in_array("U", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="U-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">
															<?php if(in_array("D", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="D-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">														
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-all-<?php echo $info['id']; ?> check-all-module" data-module-id="<?php echo $info['id']; ?>" >&nbsp;<span></span>
															</label>
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
														<th>SEMUA</th>
													</tr>
												</thead>													
												<tbody>
													<?php foreach($accounting AS $info): ?>
													<tr>
														<td class="kt-font-dark kt-font-bold"><label><?php echo strtoupper($info['name']); ?></label></td>
														<input type="hidden" name="module-id[]" value="<?php echo $info['id']; ?>">
														<td class="text-center">
															<?php if(in_array("A", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="A-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>														
														<td class="text-center">
															<?php if(in_array("C", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="C-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">
															<?php if(in_array("R", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="R-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">
															<?php if(in_array("U", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="U-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">
															<?php if(in_array("D", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="D-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">														
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-all-<?php echo $info['id']; ?> check-all-module" data-module-id="<?php echo $info['id']; ?>" >&nbsp;<span></span>
															</label>
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
														<th>BUAT</th>
														<th>DETAIL</th>														
														<th>UBAH</th>
														<th>HAPUS</th>
														<th>SEMUA</th>
													</tr>
												</thead>													
												<tbody>
													<?php foreach($report AS $info): ?>
													<tr>
														<td class="kt-font-dark kt-font-bold"><label><?php echo strtoupper($info['name']); ?></label></td>
														<input type="hidden" name="module-id[]" value="<?php echo $info['id']; ?>">
														<td class="text-center">
															<?php if(in_array("A", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="A-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>														
														<td class="text-center">
															<?php if(in_array("C", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="C-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">
															<?php if(in_array("R", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="R-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">
															<?php if(in_array("U", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="U-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">
															<?php if(in_array("D", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="D-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">														
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-all-<?php echo $info['id']; ?> check-all-module" data-module-id="<?php echo $info['id']; ?>" >&nbsp;<span></span>
															</label>
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
														<th>BUAT</th>
														<th>DETAIL</th>														
														<th>UBAH</th>
														<th>HAPUS</th>
														<th>SEMUA</th>
													</tr>
												</thead>													
												<tbody>
													<?php foreach($other AS $info): ?>
													<tr>
														<td class="kt-font-dark kt-font-bold"><label><?php echo strtoupper($info['name']); ?></label></td>
														<input type="hidden" name="module-id[]" value="<?php echo $info['id']; ?>">
														<td class="text-center">
															<?php if(in_array("A", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="A-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>														
														<td class="text-center">
															<?php if(in_array("C", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="C-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">
															<?php if(in_array("R", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="R-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">
															<?php if(in_array("U", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="U-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">
															<?php if(in_array("D", json_decode($info['method']))): ?>
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-module <?php echo 'module-'.$info['id']; ?>" data-module-id="<?php echo $info['id']; ?>" name="D-<?php echo $info['id']; ?>" value="1">&nbsp;<span></span>
															</label>
															<?php endif; ?>
														</td>
														<td class="text-center">														
															<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
															<input type="checkbox" class="check-all-<?php echo $info['id']; ?> check-all-module" data-module-id="<?php echo $info['id']; ?>" >&nbsp;<span></span>
															</label>
														</td>
													</tr>
													<?php endforeach; ?>
												</tbody>
											</table>
										</div>
									</div>
							</div>
							<div class="kt-portlet__foot">
								<div class="kt-form__actions">
									<div class="row">
										<div class="col-md-6">
											<a href="<?php echo site_url('setting'); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>
										</div>
										<div class="col-md-6">                                
											<button type="submit" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control" id="btn_save"><i class="fa fa-save"></i> SIMPAN</button>
										</div>
									</div>
								</div>
							</div>
						</div> 
					<?php echo form_close(); ?>                        
                    </div>                                    
                </div>
            </div>
        </div>
    </div>
    <!-- end:: Content -->
</div>