<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Master</h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>
            <h3 class="kt-subheader__title"><b><?php echo $title; ?></b></h3>            
        </div>
    </div>    
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
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-square btn-warning btn-elevate btn-elevate-air dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="la la-align-justify"></i>
								    <span class="d-none d-sm-inline">Lainnya</span>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <a class="dropdown-item text-dark" href="<?php echo site_url('product/print/stock_product'); ?>">- Cetak Daftar Stok</a>
                                    <a class="dropdown-item text-dark" href="<?php echo site_url('product/print/price_list/'); ?>">- Cetak Daftar Harga</a>
                                    <a class="dropdown-item text-dark" href="<?php echo site_url('product/export/sellprice'); ?>">- Export Harga Jual</a>
                                    <a class="dropdown-item text-dark" href="<?php echo site_url('product/import/sellprice'); ?>">- Import Harga Jual</a>
                                    <a class="dropdown-item text-dark" href="<?php echo site_url('product/export/min_max_stock'); ?>">- Export Stok Minimal & Maksimal</a>
                                    <a class="dropdown-item text-dark" href="<?php echo site_url('product/import/min_max_stock'); ?>">- Import Stok Minimal & Maksimal</a>
                                </div>
                            </div>
                            <button class="btn btn-square btn-light btn-elevate btn-elevate-air" id="btn-filter" data-toggle="modal" data-target="#filter-modal">
                                <i class="la la-filter"></i>
                                <span class="d-none d-sm-inline"> Filter Produk</span>
                            </button>
                            <a href="javascript: void(0);" class="btn btn-square btn-brand btn-elevate btn-elevate-air" id="btn-refresh">
								<i class="la la-refresh"></i>
								<span class="d-none d-sm-inline">Refresh Data</span>
                            </a>                                                        
                            <a href="<?php echo site_url('product/create'); ?>" class="btn btn-square btn-success btn-elevate btn-elevate-air">
								<i class="la la-plus"></i>
								<span class="d-none d-sm-inline"> Produk Baru</span>
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
                            <input type="text" class="form-control" name="search" id="search" placeholder="Silahkan isi Barcode/Kode/Nama untuk melakukan pencarian produk..." autocomplete="off">
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
                    <h6 class="text-danger font-weight-bold">ISI KATA KUNCI DI KOLOM PENCARIAN ATAU PILIH SALAH SATU DEPARTEMEN UNTUK MENAMPILKAN DAFTAR PRODUK</h6>
                </div>
                <?php if($this->system->check_access('product/sellprice_warning', 'A') && count($lower_buyprice) != 0): ?>
                <div class="alert alert-outline-danger fade show" role="alert">
                    <div class="alert-icon"><i class="flaticon-warning"></i></div>
                    <div class="alert-text">                        
                        <h4><b>MOHON PERHATIAN!</b></h4>
                        <p>Terdapat <b class="text-dark">HARGA JUAL</b> sama atau lebih rendah daripada<b class="text-dark">HARGA BELI</b>. Segera melakukan perubahan <b class="text-danger">HARGA JUAL</b>!</p>
                        <p><a href="<?php echo site_url('general/low_selling_price') ?>" class="text-primary"><b>KLIK DISINI</b></a> untuk melihat daftar!</p>
                    </div>
                </div>
                <?php endif; ?>
                <p class="text-dark">Tampilan Kolom: <a class="toggle-vis" data-column="1">BARCODE</a> - <a class="toggle-vis" data-column="2">KODE</a> - <a class="toggle-vis" data-column="4">STOK</a> - <a class="toggle-vis" data-column="-1">PPN</a></p>
                <table class="table table-bordered table-hover" id="datatable">
                    <thead>
                        <tr style="text-align:center;">
                            <th class="notexport">NO.</th>
                            <th>BARCODE</th>
                            <th>KODE</th>
                            <th>NAMA</th>
                            <th>STOK</th>
                            <th>SATUAN</th>                            
                            <th>HRG. JUAL 1</th>
                            <th>PPN</th>                            
                        </tr>
                    </thead>
                    <tbody>                                                                                                            
                    </tbody>
                </table>                
            </div>
        </div>
    </div>
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