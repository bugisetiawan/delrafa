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
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        <i class="fa fa-cube text-primary"></i> DAFTAR STOK PRODUK SEGERA DAN SUDAH KOSONG
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">                                                        
                            <button onclick="$('#datatable_out_stock_product').DataTable().ajax.reload();" class="btn btn-square btn-brand btn-elevate btn-elevate-air" id="btn-refresh">
								<i class="la la-refresh"></i>
								<span class="d-none d-sm-inline">Refresh Data</span>
                            </button>
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
                            <input type="text" class="form-control" name="search_product" id="search_product" placeholder="Silahkan isi Nama untuk melakukan pencarian produk..." autocomplete="off">
                        </div>                                          
                        <small class="kt-font-dark">*tekan tombol <b>ENTER</b> didalam kotak pencarian untuk melakukan pencarian. Terima kasih</small>
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
                <table class="table table-sm table-bordered table-hover" id="datatable_out_stock_product">
                    <thead>
                        <tr>
                            <th style="width:10px;">NO</th>
                            <th style="width:100px;">KODE</th>
                            <th class="text-center">NAMA</th>
                            <th style="width:80px;" class="text-center">STOK</th>
                            <th style="width:80px;" class="text-center">SATUAN</th>
                            <th style="width:100px;" class="text-center">DEPARTEMEN</th>
                            <th style="width:100px;" class="text-center">SUBDEPARTEMEN</th>
                        </tr>
                    </thead>
                    <tbody>                                                                                                            
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- end:: Content -->
</div>