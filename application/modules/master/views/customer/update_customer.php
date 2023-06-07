<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Master</h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>
            <h3 class="kt-subheader__title"><b><?php echo $title; ?></b></h3>
        </div>
    </div>    
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
		<div class="alert alert-dark fade show" role="alert">
			<div class="alert-icon"><i class="flaticon-exclamation-2"></i></div>
			<div class="alert-text">Mohon Perhatian! Label yang memiliki bintang(*) wajib di disi, terima kasih.</div>			
		</div>
        <div class="row">
            <div class="col-lg-12">                
                <div class="kt-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Informasi                                                                    
                            </h3>
                        </div>
                    </div>
                    <?php echo form_open('', ['class' => '', 'autocomplete' => 'off']); ?>
                        <div class="kt-portlet__body">
                            <div class="kt-section">                                
                                <div class="kt-section__body">
                                    <?php 
                                        $data = array('type' => 'hidden', 'class' => 'form-control', 'id' => 'code', 'name' => 'code',  'value' => $customer['code'], 'required' => 'true'); 
                                        echo form_input($data);                                        
                                    ?> 
                                    <div class="row">                                      
                                        <div class="col-md-5">
                                            <div class="form-group row">
                                                <label class="col-md-4 col-form-label kt-font-dark"><strong><span class="text-danger">*</span>Nama</strong> Pelanggan</label>
                                                <div class="col-md-8">
                                                    <?php 
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'name', 'name' => 'name', 'placeholder' => 'Contoh: PT. INDO MAKMUR / BPK. BUDI / TOKO SEJATI', 'value' => $customer['name'], 'required' => 'true'); 
                                                        echo form_input($data);                                             
                                                        echo form_error('name', '<p class="text-danger">', '</p>');
                                                    ?>                            
                                                </div>
                                            </div>                                                                                   
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group row">
												<label class="col-md-3 col-form-label kt-font-dark text-right"><b><span class="text-danger">*</span>Harga</b> Jual</label>
												<div class="col-md-9">
													<div class="kt-radio-inline">	
														<?php for($i=1;$i<=5;$i++): ?>
															<label class="kt-radio kt-radio--bold kt-radio--brand">
															<input type="radio" name="price_class" value="<?php echo $i; ?>"
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
                                                       $data = array('type' => 'hidden', 'class' => 'form-control', 'id' => 'zone_id', 'name' => 'zone_id',  'value' => $customer['zone_id']); 
                                                       echo form_input($data);                                                                                                    
                                                    ?>
													<select class="form-control" id="zone" name="zone">
                                                        <option value="">- Pilih Zona -</option>
                                                    </select>
                                                </div>
                                            </div>                                            
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">                                            
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label kt-font-dark">*Alamat</label>
                                                <div class="col-md-9">                                                    
                                                    <?php 
                                                        $data = array('class' => 'form-control', 'rows' => '5', 'id' => 'address', 'name' => 'address',  'value' => $customer['address'], 'placeholder' => 'Contoh: Jl. Kenangan Indah No.168 Blok 9 Menteng, Jakarta Pusat, DKI Jakarta', 'required' => 'true'); 
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
                                                    <?php 
                                                       $data = array('type' => 'hidden', 'class' => 'form-control', 'id' => 'province_id', 'name' => 'province_id',  'value' => $customer['province_id']); 
                                                       echo form_input($data);                                                                                                    
                                                    ?>
                                                    <select class="form-control" id="province" name="province">
                                                        <option value="">-- Pilih Provinsi -- </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label kt-font-dark text-right">Kota</label>
                                                <div class="col-md-9">
                                                    <?php 
                                                       $data = array('type' => 'hidden', 'class' => 'form-control', 'id' => 'city_id', 'name' => 'city_id',  'value' => $customer['city_id']); 
                                                       echo form_input($data);                                                                                                    
                                                    ?>
                                                    <select class="form-control" id="city" name="city">
														<option value="">-- Pilih Kota -- </option>
													</select>
													<small>*Pilih Provinsi untuk menampilkan data kota</small>
                                                </div>
                                            </div>                                           
                                        </div>
                                        <div class="col-md-4">    
											<div class="form-group row">
                                                <label class="col-md-4 col-form-label kt-font-dark text-right"><b>PKP</b></label>
                                                <div class="col-md-8">
                                                    <div class="kt-checkbox-inline">
                                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand">
                                                            <input type="checkbox" id="pkp" name="pkp" value="1" <?php if($customer['pkp'] == 1) { echo "checked"; } ?>> YA
                                                            <span></span>
                                                        </label>
                                                        <small>PKP: Pengusaha Kena Pajak</small>
                                                        <?php echo form_error('pkp', '<p class="text-danger">', '</p>'); ?>
                                                    </div>                                                    
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-4 col-form-label kt-font-dark text-right">NPWP</label>
                                                <div class="col-md-8">
                                                <?php
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'npwp', 'name' => 'npwp',  'value' => $customer['npwp']);
                                                    echo form_input($data);
                                                    echo form_error('npwp', '<p class="text-danger">', '</p>');
                                                ?>
                                                <small>*Wajib Di Isi Apabila Pelanggan adalah <b>PKP</b></small>
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
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'phone', 'name' => 'phone',  'value' => $customer['phone'], 'placeholder' => 'Contoh: 08781998XXXX'); 
                                                        echo form_input($data);
                                                        echo form_error('phone', '<p class="text-danger">', '</p>');
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-4 col-form-label kt-font-dark">No. Telepon</label>
                                                <div class="col-md-8">
                                                    <?php 
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'telephone', 'name' => 'telephone',  'value' => $customer['telephone'], 'placeholder' => 'Contoh: (021) 21899898'); 
                                                        echo form_input($data);                                             
                                                        echo form_error('telephone', '<p class="text-danger">', '</p>');
                                                    ?>
                                                </div>
                                            </div>  
										</div>                                    
                                        <div class="col-md-4">
                                            <div class="form-group row">
                                                <label class="col-md-4 col-form-label kt-font-dark text-right">Nama Kontak</label>
                                                <div class="col-md-8">
                                                    <?php 
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'contact', 'name' => 'contact',  'value' => $customer['contact'], 'placeholder' => 'Contoh: Bpk. Joko, Ibu Mega'); 
                                                        echo form_input($data);                                             
                                                        echo form_error('contact', '<p class="text-danger">', '</p>');
                                                    ?>
                                                </div>
											</div>            
											<div class="form-group row">
                                                <label class="col-md-4 col-form-label kt-font-dark text-right">Email</label>
                                                <div class="col-md-8">
                                                    <?php 
                                                        $data = array('type' => 'email', 'class' => 'form-control', 'id' => 'email', 'name' => 'email',  'value' => $customer['email'], 'placeholder' => 'iniemail@provider.com'); 
                                                        echo form_input($data);                                             
                                                        echo form_error('email', '<p class="text-danger">', '</p>');
                                                    ?>
                                                </div>
                                            </div>                                                                                                                                                   
                                        </div>
                                        <div class="col-md-4">
											<div class="form-group row">
                                                <label class="col-md-4 col-form-label kt-font-dark text-right"><span class="text-danger">*</span>Plafon Kredit</label>
                                                <div class="col-md-8">
                                                    <?php 
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'credit', 'name' => 'credit',  'value' => number_format($customer['credit'],'0','.',','), 'required' => 'true'); 
                                                        echo form_input($data);                                             
                                                        echo form_error('credit', '<p class="text-danger">', '</p>');
                                                    ?>
                                                </div>
											</div>
											<div class="form-group row">
                                                <label class="col-md-4 col-form-label kt-font-dark text-right"><span class="text-danger">*</span>TOP (Hari)</label>
                                                <div class="col-md-8">
                                                    <?php
                                                        $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'dueday', 'name' => 'dueday',  'value' => $customer['dueday'], 'required' => 'true');
                                                        echo form_input($data);
                                                        echo form_error('dueday', '<p class="text-danger">', '</p>');
													?>
													<small>*Waktu jatuh tempo</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8"></div>
                                        <div class="col-md-4">
											<div class="form-group row">
                                                <label class="col-md-4 col-form-label kt-font-dark text-right"><span class="text-danger">*</span>Status</label>
                                                <div class="col-md-8">
                                                    <select class="form-control" name="status" id="status" required>
                                                            <option value="1" <?php if($customer['status'] == 1){ echo "selected"; } ?>>AKTIF</option>
                                                            <option value="0" <?php if($customer['status'] == 0){ echo "selected"; } ?>>TIDAK AKTIF</option>
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
                                        <a href="<?php echo base_url('customer'); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>                                        
                                    </div>                                 
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-save"></i> SIMPAN</button>
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


<script>
$(document).ready(function(){
    $('.edit_province option[value=<?php $customer['province_id']; ?>]').attr('selected','selected');    
});    
</script>