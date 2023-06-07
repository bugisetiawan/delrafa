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
                            <a href="javascript: void(0);" class="btn btn-square btn-brand btn-elevate btn-elevate-air" id="btn-refresh">
								<i class="la la-refresh"></i>
								<span class="d-none d-sm-inline">Refresh Data</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">                   
                <div class="form-group row">                    
                    <div class="col-md-6">                        
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="la la-search kt-font-brand"></i></span></div>
                            <input type="text" class="form-control" name="search" id="search" placeholder="Silahkan isi Nama untuk melakukan pencarian produk..." autocomplete="off">
                        </div>                                          
                        <small class="kt-font-dark">*tekan tombol <b>ENTER</b> didalam kotak pencarian untuk melakukan pencarian. Terima kasih</small>
                    </div>
                    <div class="col-md-3">                        
                        <select name="department_code" id="department_code" class="form-control">                                    
                        </select>                                
                    </div>
                    <div class="col-md-3">                        
                        <select name="subdepartment_code" id="subdepartment_code" class="form-control">
                            <option value="">- SEMUA SUBDEPARTEMEN -</option>
                        </select>                                  
                    </div>
                </div>      
                <div class="form-group text-center" id="message">
                    <h6 class="text-danger font-weight-bold">ISI KATA KUNCI DI KOLOM PENCARIAN ATAU PILIH SALAH SATU DEPARTEMEN UNTUK MENAMPILKAN DAFTAR PRODUK</h6>
                </div>           
                <table class="table table-bordered table-hover" id="datatable">
                    <thead>
                        <?php if($this->system->check_access('view_global_stock/separated', 'read')): ?>
                        <input type="hidden" id="separated" value=1>
                        <tr style="text-align:center;">
                            <th class="notexport">NO.</th>                            
                            <th>KODE</th>
                            <th>NAMA</th>
                            <th>STOK UTAMA</th>
                            <th>STOK LAINNYA</th>
                            <th>TOTAL STOK</th>
                            <th>SATUAN</th>                            
                            <th>HRG. JUAL 1</th>
							<th>HRG. JUAL 2</th>                                                  
                        </tr>
                        <?php else: ?>
                        <input type="hidden" id="separated" value=0>
                        <tr style="text-align:center;">
                            <th class="notexport">NO.</th>
                            <th>KODE</th>
                            <th>NAMA</th>
                            <th>STOK</th>
                            <th>SATUAN</th>                            
                            <th>HRG. JUAL 1</th>
							<th>HRG. JUAL 2</th>                                                  
                        </tr>
                        <?php endif; ?>
                    </thead>
                    <tbody>                                                                                                            
                    </tbody>
                </table>
                <!--end: Datatable -->
            </div>
        </div>
    </div>
    <!-- end:: Content -->
    <!--begin::Sellprice Modal-->
    <div class="modal fade" id="sellprice-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Informasi Harga Jual Produk</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row" id="sellprice-modal-body">
                    </div>                                        
                </div>                
            </div>
        </div>
    </div>
    <!--end::Sellprice Modal-->
</div>