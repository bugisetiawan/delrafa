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
                                Informasi | Tgl. Bergabung <span class="kt-font-primary"><?php echo date('d-m-Y', strtotime($info['created'])); ?></span>
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <div class="kt-portlet__head-wrapper">
                                <div class="kt-portlet__head-actions"> 									
                                    <a href="<?php echo base_url('supplier'); ?>" class="btn btn-outline-primary btn-elevate btn-icon"
                                        data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Kembali ke Data Supplier">
                                        <i class="fa fa-arrow-left"></i>
                                    </a>
                                    <a href="<?php echo base_url('supplier/update/'.$this->global->encrypt($info['code'])); ?>" class="btn btn-outline-warning btn-elevate btn-icon"
                                        data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Perbaharui Data Supplier">
                                    <i class="la la-edit"></i>
                                    </a>
                                    <button type="button" id="delete" class="btn btn-outline-danger btn-elevate btn-icon"
                                        data-id="<?php echo $info['code']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Hapus Data Supplier">
                                    <i class="la la-trash"></i>
                                    </button>
                                </div>
                            </div>
						</div>
                    </div>                              
                    <!--begin::Form-->
                        <div class="kt-portlet__body">
                            <div class="kt-section">                                
                                <div class="kt-section__body">
                                    <div class="row">                                      
                                        <div class="col-md-10">
                                            <div class="form-group row">
                                                <label class="col-md-2 col-form-label kt-font-dark"><strong>Nama</strong> Supplier</label>
                                                <div class="col-md-10">
                                                    <?php 
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'name', 'name' => 'name',  'value' => $info['name'], 'required' => 'true', 'readonly' => 'true'); 
                                                        echo form_input($data);                                             
                                                        echo form_error('name', '<p class="text-danger">', '</p>');
                                                    ?>                            
                                                </div>
                                            </div>                                                                                   
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label  kt-font-dark">PKP</label>
                                                <div class="col-md-9">
                                                    <div class="kt-checkbox-inline">
                                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
                                                        <input type="checkbox" name="ppn" value="1" <?php if($info['ppn'] == 1) { echo "checked"; } ?> disabled> Ya
                                                        <span></span>
                                                        </label>                                                        
                                                    </div>
                                                    <?php echo form_error('ppn', '<p class="text-danger">', '</p>'); ?>
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
                                                        echo form_error('address', '<p class="text-danger">', '</p>');
                                                    ?>
                                                </div>
                                            </div>                                            
                                        </div>
                                        <div class="col-md-4">                                           
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label kt-font-dark">Provinsi</label>
                                                <div class="col-md-9">                                  
                                                    <?php 
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'province_name', 'name' => 'province_name',  'value' => $province['name'], 'required' => 'true', 'readonly' => 'true'); 
                                                        echo form_input($data);                                                                                                     
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label kt-font-dark">Kota</label>
                                                <div class="col-md-9">
                                                    <?php 
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'city_name', 'name' => 'city_name',  'value' => $city['name'], 'required' => 'true', 'readonly' => 'true'); 
                                                        echo form_input($data);                                                                                                     
                                                    ?>
                                                </div>
                                            </div>                                           
                                        </div>
                                        <div class="col-md-4">                                           
                                            <div class="form-group row">
                                                <label class="col-md-5 col-form-label kt-font-dark">No. Handphone</label>
                                                <div class="col-md-7">
                                                    <?php 
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'phone', 'name' => 'phone',  'value' => $info['phone'], 'required' => 'true', 'readonly' => 'true'); 
                                                        echo form_input($data);                                             
                                                        echo form_error('phone', '<p class="text-danger">', '</p>');
                                                    ?>                                                    
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-5 col-form-label kt-font-dark">No. Telepon</label>
                                                <div class="col-md-7">
                                                    <?php 
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'telephone', 'name' => 'telephone',  'value' => $info['telephone'], 'required' => 'true', 'readonly' => 'true'); 
                                                        echo form_input($data);                                             
                                                        echo form_error('telephone', '<p class="text-danger">', '</p>');
                                                    ?>
                                                </div>
                                            </div>                                           
                                        </div>                                        
                                    </div> 
                                    <div class="row">                                        
                                        <div class="col-md-4">
                                            <div class="form-group row">
                                                <label class="col-md-4 col-form-label kt-font-dark">Kontak Person</label>
                                                <div class="col-md-8">
                                                    <?php 
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'contact', 'name' => 'contact',  'value' => $info['contact'], 'required' => 'true', 'readonly' => 'true'); 
                                                        echo form_input($data);                                             
                                                        echo form_error('contact', '<p class="text-danger">', '</p>');
                                                    ?>
                                                </div>
                                            </div>                                                                                                                                                              
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label kt-font-dark">Email</label>
                                                <div class="col-md-9">
                                                    <?php 
                                                        $data = array('type' => 'email', 'class' => 'form-control', 'id' => 'email', 'name' => 'email',  'value' => $info['email'], 'required' => 'true', 'readonly' => 'true'); 
                                                        echo form_input($data);                                             
                                                        echo form_error('email', '<p class="text-danger">', '</p>');
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label kt-font-dark text-right">TOP (Hari)</label>
                                                <div class="col-md-9">
                                                    <?php 
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'dueday', 'name' => 'dueday',  'value' => $info['dueday'], 'required' => 'true', 'readonly' => 'true'); 
                                                        echo form_input($data);                                             
                                                        echo form_error('dueday', '<p class="text-danger">', '</p>');
                                                    ?>
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
            </div>
        </div>
    </div>
    <!-- end:: Content -->
</div>