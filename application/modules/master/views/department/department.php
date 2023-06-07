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
                            <a href="javascript: void(0);" onclick="$('#tables').DataTable().ajax.reload();" class="btn btn-square btn-brand btn-elevate btn-elevate-air">
                                <i class="la la-refresh"></i>
                                <span class="d-none d-sm-inline"> Refresh Data</span>
                            </a>
                            &nbsp;                            
                            <a href="" class="btn btn-square btn-success btn-elevate btn-elevate-air" data-toggle="modal" data-target="#form_add">
                            <i class="la la-plus"></i>
                                Kategori/Subkategori Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <!--begin: Datatable -->
                <table class="table table-striped- table-bordered table-hover table-checkable" id="tables">
                    <thead>
                        <tr style="text-align:center;">
                            <th width="10">NO</th>
                            <th width="100">KODE KATG.</th>
                            <th>KATEGORI</th>
                            <th width="100">KODE SUBKATG.</th>
                            <th>SUBKATEGORI</th>
                            <th width="300">AKSI</th>
                        </tr>
                    </thead>
                    <tbody id="table_data"></tbody>
                </table>
                <!--end: Datatable -->
            </div>
        </div>
    </div>
    <!-- end:: Content -->
    <!--begin::Modal-->
    <div class="modal fade" id="form_add">
        <div class="modal-dialog modal-lg">
            <?php echo form_open('', ['id'  => 'create_data', 'autocomplete' => 'off']); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kategori/Subkategori Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="text-dark">TIPE</label>
                        <select class="form-control" id="type" name="type">
                            <option value="">- KATG./SUB. KATG. -</option>
                            <option value="1">KATEGORI</option>
                            <option value="2">SUBKATEGORI</option>
                        </select>
                    </div>
                    <div class="form-group code">
                        <label class="text-dark">KATEGORI</label>
                        <select class="form-control department" id="code" name="department_code">
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="text-dark" id="keterangan">NAMA</label>
                        <input class="form-control" type="text" name="name" id="name">
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
    <div class="modal fade" id="form_update">
        <div class="modal-dialog modal-lg">
            <?php echo form_open('master/department/update_department_process', ['id'  => 'update_department', 'autocomplete' => 'off']); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Perbarui Kategori/Subkategori</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="code_update" name="code">
                    <div class="form-group">
                        <label class="text-label">NAMA</label>
                        <input type="text" class="form-control" id="department_update" name="name">
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
    <div class="modal fade" id="modal_updatesub">
        <div class="modal-dialog modal-lg">
            <?php echo form_open('master/department/update_subdepartment_process', ['id'  => 'form_updatesub', 'autocomplete' => 'off']); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Perbarui Data Subkategori</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" id="subdepartment_id" name="subdepartment_id">
                        <label>Kategori</label>
                        <select class="form-control department" id="department_code" name="department_code">
                            <option value="">-- PILIH --</option>
                        </select>
                        <input type="hidden" id="department_code_old" name="department_code_old">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Nama</label>
                        <input type="text" class="form-control" name="subdepartment_name" id="subdepartment_name">
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
    <!--end::Modal-->
</div>