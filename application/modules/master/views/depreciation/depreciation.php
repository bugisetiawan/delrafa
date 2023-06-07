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
        <div class="alert alert-dark" role="alert">
            <div class="alert-icon"><i class="fa fa-info-circle"></i></div>
            <div class="alert-text">
                Nilai Buku sesuai dengan per Tanggal: <b><?php echo date('d-m-Y'); ?></b>
            </div>
            <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true"><i class="la la-close"></i></span>
                </button>
            </div>
        </div>
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                        <i class="text-primary fa fa-clipboard-list"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                            <a href="javascript: void(0);" onclick="$('#datatable').DataTable().ajax.reload();" class="btn btn-square btn-brand btn-elevate btn-elevate-air">
                                <i class="la la-refresh"></i>
                                <span class="d-none d-sm-inline">Refresh Data</span>
                            </a>
                            <button class="btn btn-square btn-success btn-elevate btn-elevate-air" data-toggle="modal" data-target="#add_depreciation_form">
                                <i class="la la-plus"></i>
                                <span class="d-none d-sm-inline">Data Baru</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <table class="table table-bordered table-hover" id="datatable">
                    <thead>
                        <tr style="text-align:center;">
                            <th>NO</th>
                            <th>TANGGAL</th>
                            <th>KETERANGAN</th>
                            <th>HARGA</th>                            
                            <th>TENOR</th>
                            <th>DEPRESIASI/BULAN</th>
                            <th>NILAI BUKU</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>   
                    <tbody></tbody>
                </table>
            </div>             
        </div>
    </div>
    <!-- end:: Content -->
    <!--begin::Add Modal-->
    <div class="modal fade" id="add_depreciation_form">
        <div class="modal-dialog modal-lg" role="document">
            <?php echo form_open('master/Depreciation/add', ['id'=> 'create_data', 'autocomplete' => 'off']); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Depresiasi Baru</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="text-dark"><span class="text-danger">*</span>Tanggal</label>
                        <?php 
                            $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'date', 'name' => 'date', 'placeholder' => 'Silahkan isi tanggal...',  'value' => date('d-m-Y'), 'required' => 'true'); 
                            echo form_input($data);
                        ?>
                    </div>
                    <div class="form-group">
                        <label class="text-dark"><span class="text-danger">*</span>Keterangan</label>
                        <?php 
                            $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'name', 'name' => 'name', 'placeholder' => 'Silahkan isi keterangan...',  'value' => set_value('name'), 'required' => 'true'); 
                            echo form_input($data);
                        ?>                                
                    </div>
                    <div class="form-group row">
                        <div class="col-md-8">
                            <label class="text-dark"><span class="text-danger">*</span>Harga</label>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control ammount', 'id' => 'price', 'name' => 'price', 'placeholder' => 'Silahkan isi besaran harga...',  'value' => set_value('price'), 'required' => 'true'); 
                                echo form_input($data);
                            ?>
                        </div>
                        <div class="col-md-4">
                            <label class="text-dark"><span class="text-danger">*</span>Tenor Penyusutan (BULAN)</label>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control ammount', 'id' => 'period', 'name' => 'period', 'placeholder' => 'Silahkan isi tenor...',  'value' => set_value('period'), 'required' => 'true'); 
                                echo form_input($data);
                            ?>
                        </div>                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> SIMPAN</button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
    <!--end::Add Modal-->
    <!--begin::Update Modal-->
    <div class="modal fade" id="update_depreciation_form">
        <div class="modal-dialog modal-lg" role="document">
            <?php echo form_open('master/Depreciation/update', ['id'=> 'update_data', 'autocomplete' => 'off']); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Perbaharui Data Depresiasi</h5>
                </div>
                <div class="modal-body">
                    <?php 
                        $data = array('type' => 'hidden', 'id' => 'e_depreciation_id', 'name' => 'depreciation_id', 'required' => 'true'); 
                        echo form_input($data);
                    ?>
                    <div class="form-group">
                        <label class="text-dark"><span class="text-danger">*</span>Tanggal</label>
                        <?php 
                            $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'e_date', 'name' => 'date', 'placeholder' => 'Silahkan isi tanggal...',  'value' => date('d-m-Y'), 'required' => 'true'); 
                            echo form_input($data);
                        ?>
                    </div>
                    <div class="form-group">
                        <label class="text-dark"><span class="text-danger">*</span>Keterangan</label>
                        <?php 
                            $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'e_name', 'name' => 'name', 'placeholder' => 'Silahkan isi nama...',  'value' => set_value('name'), 'required' => 'true'); 
                            echo form_input($data);
                        ?>                                
                    </div>                    
                    <div class="form-group row">
                        <div class="col-md-8">
                            <label class="text-dark"><span class="text-danger">*</span>Harga</label>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control ammount', 'id' => 'e_price', 'name' => 'price', 'placeholder' => 'Silahkan isi besaran harga...',  'value' => set_value('price'), 'required' => 'true'); 
                                echo form_input($data);
                            ?>
                        </div>
                        <div class="col-md-4">
                            <label class="text-dark"><span class="text-danger">*</span>Tenor Penyusutan (BULAN)</label>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control ammount', 'id' => 'e_period', 'name' => 'period', 'placeholder' => 'Silahkan isi tenor waktu...',  'value' => set_value('period'), 'required' => 'true'); 
                                echo form_input($data);
                            ?>
                        </div>                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> SIMPAN</button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
    <!--end::Update Modal-->
</div>