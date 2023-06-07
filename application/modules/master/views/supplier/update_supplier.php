<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Master</b></h3>
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
                                Informasi
                            </h3>
                        </div>
                    </div>                              
                    <!--begin::Form-->
                    <?php echo form_open('', ['class' => '', 'autocomplete' => 'off']); ?>
                        <div class="kt-portlet__body">
                            <div class="kt-section">                                
                                <div class="kt-section__body">
                                    <?php 
                                        $data = array('type' => 'hidden', 'class' => 'form-control', 'id' => 'code', 'name' => 'code',  'value' => $info['code'], 'required' => 'true'); 
                                        echo form_input($data);                                        
                                    ?> 
                                    <div class="row">                                      
                                        <div class="col-md-10">
                                            <div class="form-group row">
                                                <label class="col-md-2 col-form-label kt-font-dark"><strong><span class="text-danger">*</span>Nama</strong> Supplier</label>
                                                <div class="col-md-10">
                                                    <?php 
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'name', 'name' => 'name',  'value' => $info['name'], 'placeholder' => 'Contoh: PT. SUPLLIER INDO JAYA, CV.INDO MAKMUR', 'required' => 'true'); 
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
                                                        <input type="checkbox" name="ppn" value="1" <?php if($info['ppn'] == 1) { echo "checked"; } ?>> Ya
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
                                                        $data = array('class' => 'form-control', 'rows' => '5', 'id' => 'address', 'name' => 'address',  'value' => $info['address'], 'placeholder' => 'Contoh: Jl. Kenangan Indah No.168 Blok 9 Menteng, Jakarta Pusat, DKI Jakarta'); 
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
                                                       $data = array('type' => 'hidden', 'class' => 'form-control', 'id' => 'province_id', 'name' => 'province_id',  'value' => $info['province_id']); 
                                                       echo form_input($data);                                                                                                    
                                                    ?>                                  
                                                    <select class="form-control" id="province" name="province">
                                                        <option value="">-- Pilih Provinsi -- </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label kt-font-dark">Kota</label>
                                                <div class="col-md-9">
                                                    <?php 
                                                       $data = array('type' => 'hidden', 'class' => 'form-control', 'id' => 'city_id', 'name' => 'city_id',  'value' => $info['city_id']); 
                                                       echo form_input($data);                                                                                                    
                                                    ?>
                                                    <select class="form-control" id="city" name="city">
                                                        <option value="">-- Pilih Kota -- </option>
                                                    </select>
                                                    <small>*Pilih provinsi untuk menampilkan data kota</small>
                                                </div>
                                            </div>                                           
                                        </div>
                                        <div class="col-md-4">                                           
                                            <div class="form-group row">
                                                <label class="col-md-5 col-form-label kt-font-dark">No. Handphone</label>
                                                <div class="col-md-7">
                                                    <?php 
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'phone', 'name' => 'phone',  'value' => $info['phone'], 'placeholder' => 'Contoh: 081923457890'); 
                                                        echo form_input($data);                                             
                                                        echo form_error('phone', '<p class="text-danger">', '</p>');
                                                    ?>                                                    
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-5 col-form-label kt-font-dark">No. Telepon</label>
                                                <div class="col-md-7">
                                                    <?php 
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'telephone', 'name' => 'telephone',  'value' => $info['telephone'], 'placeholder' => 'Contoh: (021) 128596'); 
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
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'contact', 'name' => 'contact',  'value' => $info['contact'], 'placeholder' => 'Contoh: Bpk. Joko, Ibu Mega'); 
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
                                                        $data = array('type' => 'email', 'class' => 'form-control', 'id' => 'email', 'name' => 'email',  'value' => $info['email'], 'placeholder' => 'iniemail@provider.com'); 
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
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'dueday', 'name' => 'dueday',  'value' => $info['dueday']); 
                                                        echo form_input($data);                                             
                                                        echo form_error('dueday', '<p class="text-danger">', '</p>');
                                                    ?>
                                                    <small>*Waktu jatuh tempo</small>
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
                                        <a href="<?php echo base_url('supplier'); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>                                        
                                    </div>                                 
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-save"></i> SIMPAN</button>
                                    </div>
                                </div>
                            </div>
                        </div>                    
                    <?php echo form_close(); ?>
                    <!--end::Form-->
                </div>
                <!--end::Portlet--> 
            </div>
        </div>
    </div>
    <!-- end:: Content -->
</div>

<!-- 
<script>
$(document).ready(function(){
    $('.edit_province option[value=<?php $info['province_id']; ?>]').attr('selected','selected');    
});    
</script> -->