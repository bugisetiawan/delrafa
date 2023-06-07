<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Laporan</h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>
            <h3 class="kt-subheader__title"><b><?php echo $title; ?></b></h3>
        </div>
    </div>
    <!-- end:: Content Head -->
    <!-- begin:: Content -->
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
        <div class="kt-portlet kt-portlet--mobile" id="filter_form">
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
                            <a href="javascript: void(0);" onclick="$('#datatable').DataTable().ajax.reload();" class="btn btn-square btn-brand btn-elevate btn-elevate-air">
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
                        <label class="text-dark">PERIODE KARTU STOK</label>
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
                    <div class="col-md-2">
                        <label class="text-dark">DEPARTEMEN</label>
                        <select name="department_code" id="department_code" class="form-control">                                    
                        </select>
                    </div>                      
                    <div class="col-md-2">
                        <label class="text-dark">SUBDEPARTEMEN</label>
                        <select name="subdepartment_code" id="subdepartment_code" class="form-control">
                            <option value="">- SEMUA SUBDEPARTEMEN -</option>
                        </select> 
                    </div>   
                    <div class="col-md-2">                            
                        <label class="text-dark">TRANSAKSI</label>
                        <select class="form-control" name="transaction_type" id="transaction_type">
                            <option value="">- SEMUA -</option>
                            <option value="1">PEMBELIAN</option>
                            <option value="2">RETUR PEMBELIAN</option>
                            <option value="3">POS</option>
                            <option value="4">PENJUALAN</option>
                            <option value="5">RETUR PENJUALAN</option>
                            <option value="6">PRODUKSI</option>
                            <option value="7">REPACKING</option>
                            <option value="8">ADJUSMENT STOK</option>
                            <option value="9">MUTASI PRODUK</option>
                        </select>                                                             
                    </div>                                   
                    <div class="col-md-2">
                        <label class="text-dark">GUDANG</label>
                        <select class="form-control" name="warehouse_id" id="warehouse_id"></select>
                    </div> 
                </div>
            </div>            
        </div>
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand fa fa-clipboard-list"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
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
                <!--begin: Datatable -->
                <table class="table table-bordered table-hover table-checkable" id="datatable">
                    <thead>
                        <tr style="text-align:center;">
                            <th class="text-dark">NO.</th>
                            <th class="text-dark text-center">NO. TRANSAKSI</th>
                            <th class="text-dark text-center">KODE</th>
                            <th class="text-dark text-center">PRODUK</th>
                            <th class="text-dark">QTY</th>
                            <th class="text-dark">TRANSAKSI</th>
                            <th class="text-dark">MASUK/KELUAR</th>
                            <th class="text-dark">STOK</th>
                            <th class="text-dark text-center">GUDANG</th>
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
    <!-- end:: Content -->
</div>