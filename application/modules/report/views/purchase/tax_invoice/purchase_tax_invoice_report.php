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
                    <h3 class="kt-portlet__head-title">                        
                        Informasi
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">                           
                            <a href="javascript: void(0);" onclick="$('#purchase_tax_invoice_datatable').DataTable().ajax.reload();" class="btn btn-square btn-brand btn-elevate btn-elevate-air">
                            <i class="la la-refresh"></i>
                            <span class="d-none d-sm-inline"> Refresh Data</span>
                            </a>                                                        
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="form-group row">                                   
                    <div class="col-md-2">                            
                        <label class="text-dark">PERIODE PEMBELIAN</label>
                        <div class='input-group'>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'from_date', 'name' => 'from_date', 'value' => date('d-m-Y'),  'placeholder' => 'Tanggal Awal'); 
                                echo form_input($data);
                            ?>
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="la la-calendar-check-o"></i></span>
                            </div>
                        </div>                                                                    
                    </div>
                    <div class="col-md-2">                            
                        <label class="text-dark">HINGGA</label>
                        <div class='input-group'>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'to_date', 'name' => 'to_date', 'value' => date('d-m-Y'),  'placeholder' => 'Tanggal Akhir');
                                echo form_input($data);
                            ?>
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="la la-calendar-check-o"></i></span>
                            </div>
                        </div>                                                        
                    </div>                                        
                    <div class="col-md-3">
                        <label class="text-dark">SUPPLIER</label>
                        <select class="form-control" name="supplier_code" id="supplier_code"></select>
                    </div>
                    <div class="col-md-3">
                        <label class="text-dark">STATUS FAKTUR PAJAK</label>
                        <select class="form-control" name="tax_invoice_status" id="tax_invoice_status">
                            <option value="">- SEMUA -</option>
                            <option value="=">BELUM ADA</option>
                            <option value="!=">SUDAH ADA</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="text-dark">DIGUNAKAN</label>
                        <select class="form-control" name="is_used" id="is_used">
                            <option value="">- SEMUA -</option>
                            <option value="0" selected>BELUM</option>
                            <option value="1">SUDAH</option>
                        </select>
                    </div>
                </div>
            </div>            
        </div>        
        <div class="kt-portlet kt-portlet--mobile">            
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-md-12">	
                        <!--begin: Datatable -->
                        <table class="table table-bordered table-hover" id="purchase_tax_invoice_datatable">
                            <thead>
                                <tr>
                                    <th class="notexport">NO.</th>
                                    <th class="text-center">TANGGAL</th>
                                    <th class="text-center">NO. TRANSAKSI</th>
                                    <th class="text-center">NO. REFRENSI</th>
                                    <th class="text-center">TGL. FAKTUR</th>
                                    <th class="text-center">NO. FAKTUR</th>
                                    <th class="text-center">DPP</th>
                                    <th class="text-center">PPN</th>
                                    <th class="text-center">TOTAL FAKTUR</th>
                                    <th class="text-center">DIGUNAKAN</th>
                                    <th class="text-center">SUPPLIER</th>                                    
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>                                                                                                                 
                            </tbody>
                        </table>
                        <!--end: Datatable -->									
                    </div>
                </div>
            </div>            
        </div>        
    </div>
    <!-- end:: Content -->    
</div>