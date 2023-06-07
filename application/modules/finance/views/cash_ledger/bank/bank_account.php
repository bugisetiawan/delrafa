<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Keuangan</h3>
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
                    <h3 class="kt-portlet__head-title"></h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">                                                           
                            <a href="javascript: void(0);" onclick="$('#datatable').DataTable().ajax.reload();" class="btn btn-square btn-brand btn-elevate btn-elevate-air">
                                <i class="la la-refresh"></i>
                                <span class="d-none d-sm-inline"> Refresh Data</span>
                            </a>
                            <a href="<?php echo site_url('cash_ledger/bank'); ?>" class="btn btn-square btn-warning btn-elevate btn-elevate-air">
                                <i class="la la-file"></i>
                                <span class="d-none d-sm-inline">Daftar Transaksi Bank</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <!--begin::Form-->
                <?php echo form_open('finance/Cash_ledger/create_bank_account', array('class' => 'form-horizontal kt-form', 'id' => 'create_data', 'autocomplete' => 'off')); ?>             
                    <div class="form-group row">
                        <div class="col-md-5">
                            <div class="row">
                                <label class="col-md-2 col-form-label kt-font-dark"><b>KODE</b></label>
                                <div class="col-md-10">
                                    <?php 
                                        $data = array('type' => 'text', 'class' => 'form-control uppercase', 'placeholder' => 'Isikan Kode Akun Bank', 'id' => 'code', 'name' => 'code', 'value' => set_value('code'), 'autofocus' => 'true'); 
                                        echo form_input($data);                                             
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="row">
                                <label class="col-md-2 col-form-label kt-font-dark text-right"><b>NAMA</b></label>   
                                <div class="col-md-10">
                                    <?php 
                                        $data = array('type' => 'text', 'class' => 'form-control uppercase', 'placeholder' => 'Isikan Nama Akun Bank', 'id' => 'name', 'name' => 'name', 'value' => set_value('name')); 
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
                            <th class="text-dark">NO</th>
                            <th class="text-dark">KODE</th>
                            <th class="text-dark">NAMA</th>
                            <th class="text-dark">AKSI</th>
                        </tr>
                    </thead>   
                    <tbody id="table_data"></tbody>             
                </table>
                <!--end: Datatable -->
            </div>
        </div>
    </div>
    <!-- end:: Content -->
</div>