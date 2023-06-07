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
                                Informasi | Tgl. Bergabung <span class="text-primary"><?php echo date('d-m-Y', strtotime($customer['created'])); ?></span>
                            </h3>
						</div>
						<div class="kt-portlet__head-toolbar">
                            <div class="kt-portlet__head-wrapper">
                                <div class="kt-portlet__head-actions"> 
                                    <a href="<?php echo base_url('customer'); ?>" class="btn btn-outline-primary btn-elevate btn-icon"
                                        data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Kembali ke Data Pelanggan">
                                        <i class="fa fa-arrow-left"></i>
                                    </a>
                                    <?php if($customer['id'] > 1): ?>
                                    <a href="<?php echo base_url('customer/update/'.$this->global->encrypt($customer['code'])); ?>" class="btn btn-outline-warning btn-elevate btn-icon"
                                        data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Perbaharui Data Pelanggan">
                                    <i class="la la-edit"></i>
                                    </a>
                                    <button type="button" id="delete" class="btn btn-outline-danger btn-elevate btn-icon"
                                        data-id="<?php echo $customer['code']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Hapus Data Pelanggan">
                                    <i class="la la-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
						</div>
                    </div>                                                                      
                    <div class="kt-portlet__body">
                        <div class="kt-section">                                
                            <div class="kt-section__body">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label kt-font-dark"><strong>Nama</strong> Pelanggan</label>
                                            <div class="col-md-8">
                                                <?php
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'name', 'name' => 'name',  'value' => $customer['name'], 'readonly' => 'true'); 
                                                    echo form_input($data);
                                                ?>
                                            </div>
                                        </div>                                                                                   
                                    </div>                                    
                                    <div class="col-md-5">
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label kt-font-dark text-right"><b>Harga</b> Jual</label>
                                            <div class="col-md-9">
                                                <div class="kt-radio-inline">	
                                                    <?php for($i=1;$i<=5;$i++): ?>
                                                        <label class="kt-radio kt-radio--bold kt-radio--brand">
                                                        <input type="radio" id="price_class" name="price_class" value="<?php echo $i; ?>" disabled
                                                        <?php if($i == "1"){ echo "required"; } ?> <?php if($customer['price_class'] == $i){ echo "checked"; } ?>
                                                        ><?php echo $i; ?>
                                                        <span></span>
                                                    </label>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>                                                                                                                                                
                                        </div>                                            
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label kt-font-dark text-right">Zona</label>
                                            <div class="col-md-9">  
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'value' => $customer['zone'], 'readonly' => 'true'); 
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
                                                    $data = array('class' => 'form-control', 'rows' => '5', 'id' => 'address', 'name' => 'address',  'value' => $customer['address'], 'readonly' => 'true'); 
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
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'province_name', 'name' => 'province_name',  'value' => $customer['province'], 'readonly' => 'true');
                                                    echo form_input($data);
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label kt-font-dark text-right">Kota</label>
                                            <div class="col-md-9">
                                                <?php
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'city_name', 'name' => 'city_name',  'value' => $customer['city'], 'readonly' => 'true'); 
                                                    echo form_input($data);
                                                ?>
                                            </div>
                                        </div>                                           
                                    </div>
                                    <div class="col-md-4">   
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label kt-font-dark text-right"><b>PKP</b></label>
                                            <div class="col-md-8">
                                                <div class="kt-checkbox-inline">
                                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand">
                                                        <input type="checkbox" id="pkp" name="pkp" <?php if($customer['pkp'] == 1){ echo 'checked'; } ?> disabled> YA
                                                        <span></span>
                                                    </label>
                                                    <small>PKP: Pengusaha Kena Pajak</small>                                                    
                                                </div>                                                    
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label kt-font-dark text-right">NPWP</label>
                                            <div class="col-md-8">
                                            <?php
                                                $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'npwp', 'name' => 'npwp',  'value' => $customer['npwp'], 'readonly' => 'true');
                                                echo form_input($data);
                                            ?>                                            
                                            </div>                                                                                     
                                        </div>                                        
                                                                                   
                                    </div>                                        
                                </div> 
                                <div class="row">     
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label kt-font-dark">No. Handphone</label>
                                            <div class="col-md-8">
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'phone', 'name' => 'phone',  'value' => $customer['phone'], 'readonly' => 'true');
                                                    echo form_input($data);
                                                ?>                                                    
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label kt-font-dark">No. Telepon</label>
                                            <div class="col-md-8">
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'telephone', 'name' => 'telephone',  'value' => $customer['telephone'], 'readonly' => 'true');
                                                    echo form_input($data);
                                                ?>
                                            </div>
                                        </div>
                                    </div>                                   
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label kt-font-dark text-right">Nama Kontak</label>
                                            <div class="col-md-8">
                                                <?php
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'contact', 'name' => 'contact',  'value' => $customer['contact'], 'readonly' => 'true'); 
                                                    echo form_input($data);
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label kt-font-dark text-right">Email</label>
                                            <div class="col-md-8">
                                                <?php 
                                                    $data = array('type' => 'email', 'class' => 'form-control', 'id' => 'email', 'name' => 'email',  'value' => $customer['email'], 'readonly' => 'true'); 
                                                    echo form_input($data);
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label kt-font-dark text-right">Plafon Kredit</label>
                                            <div class="col-md-8">
                                                <?php
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'credit', 'name' => 'credit',  'value' => number_format($customer['credit'],'0','.',','), 'readonly' => 'true'); 
                                                    echo form_input($data);
                                                ?>
                                            </div>
                                        </div>  
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label kt-font-dark text-right">TOP (Hari)</label>
                                            <div class="col-md-8">
                                                <?php
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'dueday', 'name' => 'dueday',  'value' => $customer['dueday'], 'readonly' => 'true'); 
                                                    echo form_input($data);
                                                ?>
                                                <small>*Waktu jatuh tempo</small>
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