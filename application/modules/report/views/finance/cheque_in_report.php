<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Laporan</b></h3>
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
        <div class="kt-portlet kt-portlet--mobile" id="filter_portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                        <i class="kt-font-brand fa fa-info"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">Informasi</h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">                           
                            <a href="javascript: void(0);" onclick="$('#datatable').DataTable().ajax.reload();" class="btn btn-square btn-brand btn-elevate btn-elevate-air">
                            <i class="la la-refresh"></i>
                            <span class="d-none d-sm-inline">Refresh Data</span>
                            </a>                                                        
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="text-dark">PERIODE PENCAIRAN</label>
                            <div class='input-group'>
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'from_date', 'name' => 'from_date',  'value' => date('d-m-Y'), 'placeholder' => 'Tanggal Awal'); 
                                    echo form_input($data);
                                    ?>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="la la-calendar-check-o"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="text-dark">HINGGA</label>
                            <div class='input-group'>
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'to_date', 'name' => 'to_date',  'value' => date('d-m-Y'), 'placeholder' => 'Tanggal Akhir'); 
                                    echo form_input($data);
                                    ?>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="la la-calendar-check-o"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="text-dark">PELANGGAN</label>
                            <select class="form-control text-dark" name="customer_code" id="customer_code">
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="text-dark">STATUS CEK/GIRO</label>
                            <select class="form-control text-dark" name="cheque_status" id="cheque_status">
                                <option value="">- SEMUA STATUS -</option>
                                <option value="1">LUNAS</option>
                                <option value="2">BELUM DIKONFIRMASI</option>
                                <option value="3">DITOLAK</option>
                            </select>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet kt-portlet--mobile">            
            <div class="kt-portlet__body">
                <!--begin: Datatable -->
                <table class="table table-bordered table-hover table-checkable" id="datatable">
                    <thead>
                        <tr style="text-align:center;">
                            <th class="notexport">NO</th>
                            <th>TANGGAL</th>
                            <th>NO. TRANSAKSI</th>
                            <th class="text-center">NO. CEK/GIRO</th>
                            <th class="text-center">TGL. BUKA</th>
                            <th class="text-center">TGL. CAIR</th>
                            <th class="text-center">TGL. LUNAS</th>
                            <th class="text-center">JUMLAH</th>
                            <th class="text-center">PELANGGAN</th>
                            <th>STATUS</th>                            
                            <th class="notexport">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <!--end: Datatable -->
            </div>
        </div>
    </div>
    <!-- end:: Content -->    
</div>