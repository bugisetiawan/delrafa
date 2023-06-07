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
        <div class="alert alert-dark fade show" role="alert">
			<div class="alert-icon"><i class="flaticon-exclamation-2"></i></div>
			<div class="alert-text">Mohon Perhatian! Label yang memiliki bintang(<span class="text-danger">*</span>) wajib di disi, terima kasih.</div>			
		</div>
        <div class="row">
            <div class="col-lg-12">
                <!--begin::Portlet-->
                <div class="kt-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Data Pribadi
                            </h3>
                        </div>
                    </div>
                    <!--begin::Form-->
                    <?php echo form_open_multipart('', ['class' => '', 'autocomplete' => 'off']); ?>
                    <div class="kt-portlet__body">
                        <div class="text-danger">
                            <?php echo validation_errors(); ?>
                        </div>                        
                        <div class="kt-section">
                            <div class="kt-section__body"> 
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label kt-font-dark"><strong>Foto </strong>Karyawan</label>
                                            <div class="col-md-9">
                                                <input type="file" id="photo" name="photo" class="form-control">
                                            </div>
                                        </div>                                                                    
                                    </div>
                                    <div class="col-md-6">                                        
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label kt-font-dark"><strong>Preview</strong> Foto</label>
                                            <div class="col-md-9">                                                
                                                <img src="#" id="preview" name="preview" height="118" width="118" alt="Preview Photo">
                                            </div>
                                        </div>            
                                    </div>
                                </div>                               
                                <div class="row">
                                    <div class="col-md-6">
                                    <div class="form-group row">
                                            <label class="col-md-5 col-form-label kt-font-dark"><strong><span class="text-danger">*</span>Nomor Induk </strong> Kependudukan (NIK)</label>
                                            <div class="col-md-7">
                                                <?php 
                                                    $data = array('type' => 'text', 'maxlength'=> '16', 'maxlength' => '16', 'class' => 'form-control', 'id' => 'nik', 'name' => 'nik',  'value' => set_value('nik'), 'required' => 'true', 'placeholder' => 'Silahkan isi 16 digit nomor ktp...'); 
                                                    echo form_input($data);                                             
                                                    echo form_error('nik', '<p class="text-danger">', '</p>');
                                                    ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">                                        
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label kt-font-dark text-right"><strong><span class="text-danger">*</span>Nama</strong> Karyawan</label>
                                            <div class="col-md-9">
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'name', 'name' => 'name',  'value' => set_value('name'), 'required' => 'true', 'placeholder' => 'Silahkan isi nama karyawan...'); 
                                                    echo form_input($data);                                             
                                                    echo form_error('name', '<p class="text-danger">', '</p>');
                                                    ?>
                                            </div>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label kt-font-dark"><span class="text-danger">*</span>Jenis Kelamin</label>
                                            <div class="col-md-8">
                                                <select class="form-control kt-selectpicker" name="gender">
                                                    <option value="L">LAKI - LAKI</option>
                                                    <option value="P">PEREMPUAN</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label kt-font-dark text-right">Agama</label>
                                            <div class="col-md-9">
                                                <select class="form-control" id="religion" name="religion">
                                                    <option value="">- Pilih Agama -</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label kt-font-dark text-right">Pendidikan Terakhir</label>
                                            <div class="col-md-8">
                                                <select class="form-control" id="education" name="education">
                                                    <option value="">- Pilih Pendidikan -</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label kt-font-dark">Alamat</label>
                                            <div class="col-md-9">
                                                <?php 
                                                    $data = array('class' => 'form-control', 'rows' => '5', 'id' => 'address', 'name' => 'address',  'value' => set_value('address'), 'placeholder' => 'Silahkan isi alamat...'); 
                                                    echo form_textarea($data);                                             
                                                    echo form_error('address', '<p class="text-danger">', '</p>');
                                                    ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label kt-font-dark text-right">Provinsi</label>
                                            <div class="col-md-9">
                                                <select class="form-control" id="province" name="province">
                                                    <option value="">- Pilih Provinsi -</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label kt-font-dark text-right">Kota</label>
                                            <div class="col-md-9">
                                                <select class="form-control" id="city" name="city">
                                                    <option value="">- Pilih Kota -</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="col-md-5 col-form-label kt-font-dark text-right"><span class="text-danger">*</span>No. Handphone</label>
                                            <div class="col-md-7">
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'phone', 'name' => 'phone',  'value' => set_value('phone'), 'required' => 'true', 'placeholder' => 'Silahkan isi no.handphone...'); 
                                                    echo form_input($data);                                             
                                                    echo form_error('phone', '<p class="text-danger">', '</p>');
                                                    ?> 
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-5 col-form-label kt-font-dark text-right">No. Telepon</label>
                                            <div class="col-md-7">
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'telephone', 'name' => 'telephone',  'value' => set_value('telephone'), 'placeholder' => 'Silahkan isi no.telepon...'); 
                                                    echo form_input($data);                                             
                                                    echo form_error('telephone', '<p class="text-danger">', '</p>');
                                                    ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label kt-font-dark">Tempat & Tgl.Lahir</label>
                                            <div class="col-md-5">
                                                <select class="form-control" id="born" name="born">
                                                    <option value="">- Pilih Kota -</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <?php
                                                    $data = array('type' => 'date', 'class' => 'form-control', 'id' => 'birthday', 'name' => 'birthday',  'value' => set_value('birthday')); 
                                                    echo form_input($data);                                             
                                                    echo form_error('birthday', '<p class="text-danger">', '</p>');
                                                ?>
                                                <small>*Tanggal Lahir</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group row">
                                            <label class="col-md-6 col-form-label kt-font-dark text-right"><span class="text-danger">*</span>Status Perkawinan</label>
                                            <div class="col-md-6">
                                                <select class="form-control kt-selectpicker" name="married">
                                                    <option value="1">BELUM KAWIN</option>
                                                    <option value="2">SUDAH KAWIN</option>
                                                    <option value="3">DUDA</option>
                                                    <option value="4">JANDA</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label kt-font-dark">NPWP</label>
                                            <div class="col-md-9">
                                                <?php
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'npwp', 'name' => 'npwp',  'value' => set_value('npwp'), 'placeholder' => 'Silahkan isi NPWP...'); 
                                                    echo form_input($data);                                             
                                                    echo form_error('npwp', '<p class="text-danger">', '</p>');
                                                    ?>
                                                    <small>*Kosongkan jika tidak memiliki NPWP</small>
                                            </div>                                            
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="col-md-5 col-form-label kt-font-dark text-right">No. BPJS Kesehatan</label>
                                            <div class="col-md-7">
                                                <?php
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'bpjs', 'name' => 'bpjs',  'value' => set_value('bpjs'), 'placeholder' => 'Silahkan isi No.BPJS Kesehatan...'); 
                                                    echo form_input($data);                                             
                                                    echo form_error('bpjs', '<p class="text-danger">', '</p>');
                                                    ?>
                                                <small>*Kosongkan jika tidak memiliki NO. BPJS Kesehatan</small>
                                            </div>                                            
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group row">
                                            <label class="col-md-6 col-form-label kt-font-dark text-right">No. BPJS Ketenagakerjaan</label>
                                            <div class="col-md-6">
                                                <?php
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'bpjsk', 'name' => 'bpjsk',  'value' => set_value('bpjsk'), 'placeholder' => 'Silahkan isi No.BPJS Ketengakerjaan...'); 
                                                    echo form_input($data);                                             
                                                    echo form_error('bpjsk', '<p class="text-danger">', '</p>');
                                                    ?>
                                                <small>*Kosongkan jika tidak memiliki NO. BPJS Keternagakerjaan</small>
                                            </div>                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Form-->
                </div>
                <!--end::Portlet--> 
                <!--begin::Portlet-->
                <div class="kt-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Data Pekerjaan
                            </h3>
                        </div>
                    </div>
                    <!--begin::Form-->                    
                    <div class="kt-portlet__body">
                        <div class="kt-section">
                            <div class="kt-section__body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label kt-font-dark"><strong><span class="text-danger">*</span>Jabatan</strong></label>
                                            <div class="col-md-8">
                                                <select class="form-control" id="position" name="position" required>
                                                    <option value="">- Pilih Jabatan -</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-md-6 col-form-label kt-font-dark text-right"><strong><span class="text-danger">*</span>Tgl.</strong> Bergabung</label>
                                            <div class="col-md-6">
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'join', 'name' => 'join',  'value' => date('d-m-Y'), 'required' => 'true'); 
                                                    echo form_input($data);                                             
                                                    echo form_error('join', '<p class="text-danger">', '</p>');
                                                    ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-md-6 col-form-label kt-font-dark text-right"><strong>Tgl.</strong> Keluar</label>
                                            <div class="col-md-6">
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'out', 'name' => 'out',  'value' => set_value('out')); 
                                                    echo form_input($data);                                             
                                                    echo form_error('out', '<p class="text-danger">', '</p>');
                                                    ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-md-5 col-form-label kt-font-dark text-right"><span class="text-danger">*</span>Status</label>
                                            <div class="col-md-7">
                                                <select class="form-control" name="status" id="status">
                                                    <option value="1">AKTIF</option>
                                                    <option value="0">TIDAK AKTIF</option>
                                                </select>
                                            </div>
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
                                    <a href="<?php echo base_url('employee'); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>  BATAL</a>                                        
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control" onclick="this.disabled=true;this.form.submit();"><i class="fa fa-save"></i> SIMPAN</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                    <!--end::Form-->
                </div>
                <!--end::Portlet-->
            </div>
        </div>
    </div>
    <!-- end:: Content -->
</div>