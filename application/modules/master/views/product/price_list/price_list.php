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
                            <a href="javascript: void(0);" class="btn btn-square btn-brand btn-elevate btn-elevate-air" id="btn-refresh" onclick="$('#datatable').DataTable().ajax.reload();">
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
                        <label class="text-dark text-right">PENCARIAN PRODUK</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="la la-search kt-font-brand"></i></span></div>
                            <input type="text" class="form-control" name="search_product" id="search_product" placeholder="Silahkan isi Kode/Nama untuk melakukan pencarian produk..." autocomplete="off">
                        </div>                                          
                        <small class="text-dark">*tekan tombol <b>ENTER</b> didalam kotak pencarian untuk melakukan pencarian. Terima kasih</small>
                    </div>
                    <div class="col-md-3">
                        <label class="text-dark text-right">DEPARTEMEN</label>
                        <select name="department_code" id="department_code" class="form-control">                                    
                        </select>                                
                    </div>
                    <div class="col-md-3">
                        <label class="text-dark text-right">SUBDEPARTEMEN</label>
                        <select name="subdepartment_code" id="subdepartment_code" class="form-control">
                            <option value="">- SEMUA SUBDEPARTEMEN -</option>
                        </select>                                  
                    </div>
                </div>
                <div class="form-group text-center" id="message">
                    <h6 class="text-danger font-weight-bold">ISI NAMA PRODUK ATAU PILIH SALAH SATU DEPARTEMEN UNTUK MENAMPILKAN DAFTAR PRODUK</h6>
                </div>
                <table class="tabble table-sm table-bordered" id="datatable">
                    <thead>
                        <tr style="text-align:center;">
                            <th>NO.</th>
                            <th>KODE</th>
                            <th>NAMA</th>
                            <th>STOK</th>
                            <th>SATUAN</th>
                            <th class="text-center">HRG. 1</th>
                            <th class="text-center">HRG. 2</th>
                            <th class="text-center">HRG. 3</th>
                            <th class="text-center">HRG. 4</th>
                            <th class="text-center">HRG. 5</th>
                            <th class="text-center">HRG. BELI</th>
                            <th class="text-center">HPP</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>                    
                </table>
            </div>
        </div>
    </div>
    <!-- end:: Content -->   
    <!--begin::Filter Modal-->
    <div class="modal fade" id="filter-modal">
        <div class="modal-dialog modal-lg">        
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Produk</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>                
                <div class="modal-body">                      
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label class="text-dark">URUTAN INPUT</label>
                            <select id="input_order" class="form-control">                                                                    
                                <option value="" selected>-</option>
                                <option value="desc">TERBARU</option>
                                <option value="asc">TERLAMA</option>
                            </select>                                
                        </div>
                        <div class="col-md-3">
                            <label class="text-dark">TIPE PRODUK</label>
                            <select id="product_type" class="form-control">
                                <option value="" selected>SEMUA</option>
                                <option value="1">SINGLE</option>
                                <option value="2">BUNDLE</option>
                            </select>                                
                        </div>                        
                        <div class="col-md-3">
                            <label class="text-dark">PPN</label>
                            <select id="ppn" class="form-control">                                    
                                <option value="">SEMUA</option>
                                <option value="0">NON</option>
                                <option value="1">PPN</option>
                                <option value="2">FINAL</option>
                            </select>                                
                        </div>
                        <div class="col-md-3">
                            <label class="text-dark">STATUS PENJUALAN</label>
                            <select id="status" class="form-control">                                    
                                <option value="">SEMUA</option>
                                <option value="0">DISKONTINU</option>
                                <option value="1" selected>KONTINU</option>
                            </select>                                  
                        </div>
                    </div>  
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label class="text-dark">GUDANG</label>
                            <select id="warehouse_id" class="form-control">
                                <option value="0">SEMUA</option>
                                <?php foreach($warehouse AS $info): ?>
                                    <option value="<?php echo $info['id']; ?>"><?php echo $info['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>                      
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-brand btn-elevate-hover btn-square" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
                    <button type="button" class="btn btn-success btn-brand btn-elevate-hover btn-square" id="btn-search"><i class="fa fa-save"></i> TERAPKAN</button>
                </div>
            </div>
        </div>
    </div>
    <!--end::Filter Modal-->
    <!--begin::Stock Modal-->
    <div class="modal fade" id="stock-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Informasi Stok Produk</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row" id="stock-modal-body">
                    </div>                                        
                </div>                
            </div>
        </div>
    </div>
    <!--end::Stock Modal-->
</div>