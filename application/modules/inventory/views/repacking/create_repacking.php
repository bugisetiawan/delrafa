<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Persediaan</h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>            
            <h3 class="kt-subheader__title"><b><?php echo $title; ?></b></h3>
        </div>
    </div>
    <!-- end:: Content Head -->    
    <!-- begin:: Content -->
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
        <?php echo form_open_multipart('', ['class' => 'repeater', 'autocomplete'=>'off']); ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="alert alert-dark fade show" role="alert">
                        <div class="alert-icon"><i class="fa fa-info-circle"></i></div>
                        <div class="alert-text">Mohon Perhatian! Label yang memiliki bintang(<span class="text-danger">*</span>) wajib di disi, terima kasih.</div>			
                    </div>
                    <!--begin::Portlet-->
                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    Informasi
                                </h3>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label text-dark"><span class="text-danger">*</span>TGL. REPACKING</label>
                                        <div class="col-md-8">
                                            <?php 
                                                $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'date', 'name' => 'date',  'value' => date('d-m-Y'), 'placeholder' => 'Silahkan isi tanggal repacking', 'required' => 'true');
                                                echo form_input($data);
                                                echo form_error('date', '<p class="text-danger">', '</p>');
                                            ?> 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label text-dark text-right"><span class="text-danger">*</span>PETUGAS</label>
                                        <div class="col-md-8">
                                            <select class="form-control" name="repacker_code" id="repacker_code" required></select>
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
                                    Daftar Produk
                                    <small class="kt-font-primary" id="add_product" data-repeater-create></small>
                                </h3>                                            
                            </div>						
                        </div>
                        <div class="kt-portlet__body">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-sm product_table">
                                        <thead>
                                            <tr style="text-align:center;">
                                                <th>PRODUK ASAL</th>
                                                <th width="200px">QTY</th>
                                                <th width="200px">SATUAN</th>
                                                <th width="200px">GUDANG</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr style="text-align:center;">
                                                <td>
                                                    <div class="typeahead">
                                                    <?php  
                                                        $data = array('type' => 'text', 'class' => 'form-control product_input', 'placeholder' => 'Kode/Nama Produk...', 'required' => 'true'); 
                                                        echo form_input($data);
                                                    ?>
                                                    </div>
                                                </td>
													<?php 
														$data = array('type' => 'hidden', 'class' => 'form-control product_code', 'name' => 'product_code_1', 'value' => set_value('product_code_1'), 'required' => 'true'); 
														echo form_input($data);
														echo form_error('product_code_1', '<p class="text-danger">', '</p>');
                                                    ?>
                                                <td>
													<?php 
														$data = array('type' => 'text', 'class' => 'form-control text-right qty', 'name' => 'qty_1', 'placeholder' => 'QTY',  'value' => set_value('qty_1'), 'required' => 'true'); 
														echo form_input($data);                                             
														echo form_error('qty', '<p class="text-danger">', '</p>');
													?>
                                                </td>	
                                                <td>
                                                    <select class="form-control unit" name="unit_id_1" required>
                                                    </select>
                                                </td>  
                                                <td>
                                                    <select class="form-control warehouse" name="warehouse_id_1" required>
                                                    </select>
												</td>
                                            </tr>
                                        </tbody>
                                        <thead>
                                            <tr style="text-align:center;">
                                                <th>PRODUK REPACK</th>
                                                <th width="100px">QTY</th>
                                                <th width="100px">SATUAN</th>
                                                <th width="200px">GUDANG</th>
                                            </tr>
                                        </thead>                                        
                                        <tbody data-repeater-list="product">
                                            <tr data-repeater-item style="text-align:center;">                                                                                       
                                                <td>
                                                    <div class="typeahead">
                                                    <?php  
                                                        $data = array('type' => 'text', 'class' => 'form-control to_product_input', 'placeholder' => 'Kode/Nama Produk...', 'required' => 'true'); 
                                                        echo form_input($data);
                                                    ?>
                                                    </div>
                                                </td>
													<?php 
														$data = array('type' => 'hidden', 'class' => 'form-control product_code', 'name' => 'product_code_2', 'value' => set_value('product_code_2'), 'required' => 'true'); 
														echo form_input($data);
														echo form_error('product_code_2', '<p class="text-danger">', '</p>');
                                                    ?>
                                                <td>
													<?php 
														$data = array('type' => 'text', 'class' => 'form-control text-right qty', 'name' => 'qty_2', 'placeholder' => 'QTY',  'value' => set_value('qty_2'), 'required' => 'true'); 
														echo form_input($data);                                             
														echo form_error('qty', '<p class="text-danger">', '</p>');
													?>
                                                </td>	
                                                <td>
                                                    <select class="form-control unit" name="unit_id_2" required>
                                                    </select>
                                                </td> 
                                                <td>
                                                    <select class="form-control warehouse" name="warehouse_id_2" required>
                                                    </select>
												</td>                                            
                                                <td>													
                                                    <a data-repeater-delete href="javascript:void(0);" class="text-danger text-center kt-font-bold delete_product"><i class="fa fa-times"></i></a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__foot">
                            <div class="kt-form__actions">
                                <div class="row">
                                    <div class="col-md-6">
                                        <a href="<?php echo base_url('repacking'); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>                                        
                                    </div>
                                    <div class="col-md-6">
                                        <button type="submit" id="btn_save" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-save"></i> SIMPAN</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Portlet-->                     
                </div>
            </div>
        <?php echo form_close() ?>
    </div>
    <!-- end:: Content -->
</div>