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
                        <div class="kt-portlet__head-toolbar">
                            <div class="kt-portlet__head-wrapper">
                                <div class="kt-portlet__head-actions"> 									
                                    <a href="<?php echo base_url('employee/update/'.$this->global->encrypt($info['code'])); ?>" class="btn btn-outline-warning btn-elevate btn-icon"
                                        data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Perbaharui Data Karyawan">
                                    <i class="la la-edit"></i>
                                    </a>
                                    <?php if($info['status'] != 0): ?>
                                    <button type="button" id="resign" class="btn btn-outline-dark btn-elevate"
                                        data-id="<?php echo $info['code']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Resign Karyawan">
                                        <i class="fa fa-times-circle"></i>
                                        RESIGN
                                    </button>
                                    <?php endif; ?>
                                    <button type="button" id="delete" class="btn btn-outline-danger btn-elevate btn-icon"
                                        data-id="<?php echo $info['code']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Hapus Data Karyawan">
                                    <i class="la la-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="kt-section">
                            <div class="kt-section__body">
                                <div class="row">
                                    <label class="col-md-12 col-form-label kt-font-dark text-center"><strong>FOTO </strong>KARYAWAN</label>   
                                    <div class="col-md-12">
                                        <div class="text-center">                                        
                                            <img src="<?php echo base_url('assets/media/system/employee/').$info['photo']; ?>" id="preview" name="preview" height="118" width="118" alt="Preview Photo">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-md-6 col-form-label kt-font-dark"><strong>Nomor Induk</strong> Kependudukan (NIK)</label>
                                            <div class="col-md-6">
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'nik', 'name' => 'nik',  'value' => $info['nik'], 'required' => 'true', 'readonly' => 'true'); 
                                                    echo form_input($data);                                             
                                                    echo form_error('nik', '<p class="text-danger">', '</p>');
                                                    ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label kt-font-dark text-right"><strong>Nama</strong> Karyawan</label>
                                            <div class="col-md-9">
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'name', 'name' => 'name',  'value' => $info['name'], 'required' => 'true', 'readonly' => 'true'); 
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
                                            <label class="col-md-4 col-form-label kt-font-dark">Jenis Kelamin</label>
                                            <div class="col-md-8">                                                   
                                                <?php
                                                    if($info['gender'] == "L")
                                                    {
                                                        $gender = "Laki - Laki";
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'value' => $gender, 'readonly' => 'true'); 
                                                        echo form_input($data);
                                                    }                         
                                                    if($info['gender'] == "P")
                                                    {
                                                        $gender = "Perempuan";
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'value' => $gender, 'readonly' => 'true'); 
                                                        echo form_input($data);  
                                                    }                                                                                                                                      
                                                    ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label kt-font-dark text-right">Agama</label>
                                            <div class="col-md-9">                                              
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'value' => $religion['name'], 'readonly' => 'true'); 
                                                    echo form_input($data);
                                                    ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="col-md-5 col-form-label kt-font-dark text-right">Pendidikan Terakhir</label>
                                            <div class="col-md-7">                                                  
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'value' => $education['name'], 'readonly' => 'true'); 
                                                    echo form_input($data);
                                                    ?>
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
                                                    $data = array('class' => 'form-control', 'rows' => '5', 'id' => 'address', 'name' => 'address',  'value' => $info['address'], 'required' => 'true', 'readonly' => 'true'); 
                                                    echo form_textarea($data);                                                        
                                                    ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label kt-font-dark text-right">Provinsi</label>
                                            <div class="col-md-9">                                                    
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'value' => $province['name'], 'readonly' => 'true'); 
                                                    echo form_input($data);
                                                    ?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label kt-font-dark text-right">Kota</label>
                                            <div class="col-md-9">
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'value' => $city['name'], 'readonly' => 'true'); 
                                                    echo form_input($data);
                                                    ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="col-md-5 col-form-label kt-font-dark text-right">No. Handphone</label>
                                            <div class="col-md-7">
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'phone', 'name' => 'phone',  'value' => $info['phone'], 'required' => 'true', 'readonly' => 'true'); 
                                                    echo form_input($data);                                                        
                                                    ?> 
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-5 col-form-label kt-font-dark text-right">No. Telepon</label>
                                            <div class="col-md-7">
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'telephone', 'name' => 'telephone',  'value' => $info['telephone'], 'required' => 'true', 'readonly' => 'true'); 
                                                    echo form_input($data);                                             
                                                    ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label kt-font-dark">Tempat & Tgl. Lahir</label>
                                            <div class="col-md-5">
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'value' => $born['name'], 'readonly' => 'true'); 
                                                    echo form_input($data);
                                                    ?>
                                            </div>
                                            <div class="col-md-4">
                                                <?php
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'birthday', 'name' => 'birthday',  'value' => date('d-m-Y', strtotime($info['birthday'])), 'required' => 'true', 'readonly' => 'true'); 
                                                    echo form_input($data);                                                        
                                                    ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label kt-font-dark text-right">Status Perkawinan</label>
                                            <div class="col-md-8">
                                                <?php
                                                    switch ($info['married']){
                                                        case 1:
                                                        $gender = "BELUM KAWIN";
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'value' => $gender, 'readonly' => 'true'); 
                                                        echo form_input($data);
                                                        break;

                                                        case 2:
                                                        $gender = "SUDAH KAWIN";
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'value' => $gender, 'readonly' => 'true'); 
                                                        echo form_input($data);  
                                                        break;

                                                        case 3:
                                                        $gender = "DUDA";
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'value' => $gender, 'readonly' => 'true'); 
                                                        echo form_input($data);
                                                        break;

                                                        case 4:
                                                        $gender = "JANDA";
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'value' => $gender, 'readonly' => 'true'); 
                                                        echo form_input($data);
                                                        break;

                                                        default:
                                                        echo "-";
                                                    }
                                                    ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="col-md-2 col-form-label kt-font-dark">NPWP</label>
                                            <div class="col-md-10">
                                                <?php
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'npwp', 'name' => 'npwp',  'value' => $info['npwp'], 'required' => 'true', 'readonly' => 'true'); 
                                                    echo form_input($data);                                                        
                                                    ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="col-md-6 col-form-label kt-font-dark text-right">No. BPJS Kesehatan</label>
                                            <div class="col-md-6">
                                                <?php
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'bpjs', 'name' => 'bpjs',  'value' => $info['bpjs'], 'required' => 'true', 'readonly' => 'true'); 
                                                    echo form_input($data);                                                        
                                                    ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="col-md-7 col-form-label kt-font-dark text-right">No. BPJS Ketenagakerjaan</label>
                                            <div class="col-md-5">
                                                <?php
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'bpjsk', 'name' => 'bpjsk',  'value' => $info['bpjsk'], 'required' => 'true', 'readonly' => 'true'); 
                                                    echo form_input($data);                                                        
                                                    ?>
                                            </div>
                                        </div>
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
                                Data Pekerjaan
                            </h3>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="kt-section">
                            <div class="kt-section__body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label kt-font-dark"><strong>Jabatan</strong></label>
                                            <div class="col-md-8">
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'value' => $position['name'], 'readonly' => 'true'); 
                                                    echo form_input($data);
                                                    ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-md-6 col-form-label kt-font-dark text-right"><strong>Tgl.</strong> Bergabung</label>
                                            <div class="col-md-6">
                                                <?php 
                                                    $join = date('d-m-Y', strtotime($info['join']));
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'join', 'name' => 'join',  'value' => $join, 'readonly' => true); 
                                                    echo form_input($data);                                                                                                     
                                                    ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-md-6 col-form-label kt-font-dark text-right"><strong>Tgl.</strong> Keluar</label>
                                            <div class="col-md-6">                                                    
                                                <?php
												if($info['out'] == '0000-00-00')
												{
													$out = "-";
												} 
												else
												{
													$out = date('d-m-Y', strtotime($info['out']));
												}                                                        
												$data = array('type' => 'text', 'class' => 'form-control', 'id' => 'out', 'name' => 'out',  'value' => $out, 'readonly' => true); 
												echo form_input($data);
												?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-md-5 col-form-label kt-font-dark text-right">Status</label>
                                            <div class="col-md-7">
                                                <select class="form-control" name="status" id="status" disabled>
                                                    <option value="1" <?php if($info['status'] == 1){ echo "selected"; } ?>>AKTIF</option>
                                                    <option value="0" <?php if($info['status'] == 0){ echo "selected"; } ?>>TIDAK AKTIF</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Portlet-->
            </div>
        </div>
    </div>
    <!-- end:: Content -->
</div>