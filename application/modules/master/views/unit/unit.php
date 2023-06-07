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
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                        <i class="kt-font-brand fa fa-clipboard-list"></i>
                    </span>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                            <a href="javascript: void(0);" onclick="$('#datatable').DataTable().ajax.reload();" class="btn btn-square btn-brand btn-elevate btn-elevate-air">
                                <i class="la la-refresh"></i>
                                <span class="d-none d-sm-inline"> Refresh Data</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <!--begin::Form-->
                <?php echo form_open('master/Unit/add', array('class' => 'form-horizontal kt-form', 'id' => 'create_data', 'autocomplete' => 'off')); ?>             
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label kt-font-dark"><b>KODE</b></label>
                                <div class="col-md-10">
                                    <?php 
                                        $data = array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Isikan Kode Satuan', 'id' => 'code', 'name' => 'code', 'value' => set_value('code'), 'autofocus' => 'true'); 
                                        echo form_input($data);                                             
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label kt-font-dark text-right"><b>NAMA</b></label>   
                                <div class="col-md-10">
                                    <?php 
                                        $data = array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Isikan Nama Satuan', 'id' => 'name', 'name' => 'name', 'value' => set_value('name')); 
                                        echo form_input($data);                                             
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">                            
                            <button type="submit" class="btn btn-square btn-success form-control"><i class="fa fa-save"></i> SIMPAN</button>
                        </div>
                    </div>
                <?php echo form_close(); ?>
                <!--end::Form-->
                <!--begin: Datatable -->
                <table class="table table-striped- table-bordered table-hover table-checkable" id="datatable">
                    <thead>
                        <tr style="text-align:center;">
                            <th width="10">NO</th>
                            <th width="50">KODE</th>
                            <th>NAMA SATUAN</th>                            
                            <th width="100">AKSI</th>
                        </tr>
                    </thead>   
                    <tbody id="table_data"></tbody>             
                </table>
                <!--end: Datatable -->
            </div>
        </div>
    </div>
    <!-- end:: Content -->
    <!--begin::Update Modal-->
    <div class="modal fade" id="update_form">
        <div class="modal-dialog modal-lg">
        <?php echo form_open('master/Unit/update', array('class' => 'form-horizontal kt-form kt-form--label-right', 'id' => 'update_data', 'autocomplete' => 'off')); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Data <?php echo $title; ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                
                    <div class="modal-body"> 
                        <?php 
                            $data = array('type' => 'hidden', 'name' => 'editId', 'id' => 'editId', 'value' => set_value('editId'), 'required' => 'true'); 
                            echo form_input($data);                                             
                        ?>                       
                        <div class="form-group">
                            <label class="form-control-label">Kode:</label>                            
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control','placeholder' => 'Isikan Kode Satuan', 'id' => 'editCode', 'name' => 'editCode',  'value' => set_value('editCode'),'required' => 'true', 'autofocus' => 'true'); 
                                echo form_input($data);                                             
                            ?>
                        </div>
                        <div class="form-group">
                            <label class="form-control-label">Nama:</label>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control','placeholder' => 'Isikan Nama Satuan', 'id' => 'editName', 'name' => 'editName',  'value' => set_value('editName'),'required' => 'true'); 
                                echo form_input($data);                                             
                            ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-brand btn-elevate-hover btn-square" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
                        <button type="submit" class="btn btn-success btn-brand btn-elevate-hover btn-square"><i class="fa fa-save"></i> SIMPAN</button>
                    </div>                
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
    <!--end::Update Modal-->
</div>