<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Persediaan</b></h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>
            <h3 class="kt-subheader__title"><b><?php echo $title; ?></b></h3>
        </div>
    </div>
    <!-- end:: Content Head -->    
    <!-- begin:: Content -->
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
        <div class="row">
            <div class="col-lg-12">
                <!--begin::Portlet-->
                <div class="kt-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Informasi | No.Transaksi : <span class="kt-font-bold kt-font-success"><?php echo $repacking['code_rp']; ?></span>
                                <input type="hidden" id="repacking_id" value="<?php echo $repacking['id_rp']; ?>">
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <div class="kt-portlet__head-wrapper">
                                <div class="kt-portlet__head-actions">
                                    <?php $link = ($this->agent->referrer()) ? urldecode($this->agent->referrer()) : site_url('repacking'); ?>
                                    <a href="<?php echo $link; ?>" class="btn btn-outline-primary btn-elevate btn-icon"
                                        data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Kembali">
                                        <i class="fa fa-arrow-left"></i>
                                    </a>
                                    <button class="btn btn-icon btn-outline-danger" id="delete_repacking_btn"
                                        data-id="<?php echo $repacking['id_rp']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Hapus Pengemasan">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="row">
                            <div class="col-md-4">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>                                
                                            <td>TGL. REPACKING</td>
                                            <td>:</td>
                                            <th><?php echo date('d-m-Y', strtotime($repacking['date'])); ?></th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div> 
                            <div class="col-md-4">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <td>REPACKER</td>
                                            <td>:</td>
                                            <td>REPACKER</td>                                            
                                        </tr>
                                    </tbody>
                                </table>
                            </div>    
                            <div class="col-md-4">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>                                                                        
                                            <td>OPERATOR</td>
                                            <td>:</td>
                                            <th class="kt-font-primary"><?php echo $repacking['code_op'].' | '.$repacking['name_op']; ?></th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>                           
                    </div>
                </div>
                <!--end::Portlet--> 
                <!--begin::Portlet-->
                <div class="kt-portlet" id="product-table">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Daftar Produk
                            </h3>                                            
                        </div>						
                    </div>
                    <div class="kt-portlet__body">
                        <table class="table table-bordered table-hover" id="datatable_from_product">
                            <thead>
                                <tr style="text-align:center;">
                                    <th class="text-dark" width="100px">KODE</th>
                                    <th class="text-dark">PRODUK ASAL</th>
                                    <th class="text-dark"  width="50px">QTY</th>
                                    <th class="text-dark"  width="50px">SATUAN</th>
                                    <th class="text-dark"  width="150px">GUDANG</th>                                            
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-dark text-center"><a class="kt-font-primary kt-link text-center" href="<?php echo site_url('product/detail/'.encrypt_custom($repacking['code_p'])); ?>"><?php echo $repacking['code_p']; ?></a></td>
                                    <td class="text-dark "><?php echo $repacking['name_p']; ?></td>
                                    <td class="text-dark text-right"><?php echo $repacking['qty']; ?></td>
                                    <td class="text-dark text-left"><?php echo $repacking['name_u']; ?></td>
                                    <td class="text-dark text-center"><?php echo $repacking['name_w']; ?></td>
                                </tr>
                            </tbody>
                        </table>
                        <hr>
                        <table class="table table-bordered table-hover" id="datatable_to_product">
                            <thead>
                                <tr style="text-align:center;">
                                    <th>NO.</th>
                                    <th>KODE</th>
                                    <th class="text-center">PRODUK REPACK</th>
                                    <th>QTY</th>
                                    <th>SATUAN</th>
                                    <th class="text-center">GUDANG</th>                                            
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>   
                </div>
                <!--end::Portlet-->
            </div>
        </div>
    </div>
    <!-- end:: Content -->
</div>