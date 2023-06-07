<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Umum</h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>
            <h3 class="kt-subheader__title"><b><?php echo $title; ?></b></h3>            
        </div>
    </div>
    <!-- end:: Content Head -->
    <!-- begin:: Content -->
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head">
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
                            <a href="javascript: void(0);" onclick="$('#datatable').DataTable().ajax.reload();" class="btn btn-square btn-brand btn-elevate btn-elevate-air" id="btn-refresh">
								<i class="la la-refresh"></i>
								<span class="d-none d-sm-inline">Refresh Data</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">                   
                <div class="form-group row">
                    <div class="col-md-3">                        
                        <select name="department_code" id="department_code" class="form-control">                                    
                        </select>                                
                    </div>
                    <div class="col-md-3">                        
                        <select name="subdepartment_code" id="subdepartment_code" class="form-control">
                            <option value="">- SEMUA SUBDEPARTEMEN -</option>
                        </select>                                  
                    </div>
                    <div class="col-md-6">                        
                        <select name="customer_code" id="customer_code" class="form-control">
                            <option value="">- PILIH PELANGGAN -</option>
                        </select>                                  
                    </div>                    
                </div>
                <div class="form-group" id="customer_notify">
                    <div class="alert alert-danger" role="alert">
                        <div class="alert-icon"><i class="fa fa-info-circle"></i></div>
                        <div class="alert-text">
                            Silahkan pilih pelanggan terlebih dahulu untuk menampilkan data. Terima Kasih
                        </div>
                    </div>
                </div>
                <table class="table table-bordered table-hover" id="datatable">
                    <thead>
                        <tr>
                            <th class="notexport text-center">NO.</th>
                            <th class="text-center">BARCODE</th>
                            <th class="text-center">KODE</th>
                            <th class="text-center">NAMA</th>
                            <th class="text-center">QTY</th>
                            <th class="text-center">SATUAN</th>                            
                            <th class="text-center">HARGA</th>
                            <th class="text-center">DISKON (%)</th>
                            <th class="text-center">TOTAL</th>
                            <th class="text-center">AKSI</th>
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
    <!--begin::Sellprice History Modal-->
    <div class="modal fade" id="sellprice-history-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Riwayat Harga Jual Produk (10 Transaksi Terakhir)</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="sellprice-history-modal-body">
                    </div>                                        
                </div>                
            </div>
        </div>
    </div>
    <!--end::Sellprice History Modal-->
</div>