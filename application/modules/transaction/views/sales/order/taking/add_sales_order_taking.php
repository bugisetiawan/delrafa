<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Transaksi</h3>
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
        <div class="alert alert-dark alert-elevate fade show" role="alert">
            <div class="alert-icon"><i class="flaticon-exclamation-2"></i></div>
            <div class="alert-text">
                Silahkan pilih transaksi pemesanan penjualan yang akan melakukan pengambilan produk <br>
                Pemesanan yang tampil adalah <b>yang belum melakukan pengambilan dan belum ada faktur</b>, terima kasih
            </div>
        </div>   
        <div class="kt-portlet kt-portlet--mobile">
        <?php echo form_open_multipart('', ['autocomplete'=>'off']); ?>
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                        <i class="kt-font-brand fa fa-clipboard-list"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">
                        Data Tgl. <b><?php echo date('d-m-Y');?></b>
                    </h3>   
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">                          
                            <a href="<?php echo base_url('sales'); ?>" class="btn btn-brand btn-elevate btn-icon-sm">
                                <i class="fa fa-arrow-left"></i>
                                <span class="d-none d-sm-inline"> Daftar Pengambilan Produk</span>
                            </a>                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <!--begin: Datatable -->                
                <table class="table table-bordered table-hover table-checkable" id="datatable">
                    <thead>
                        <tr style="text-align:center;">
                            <th class="kt-font-dark">
                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
                                <input type="checkbox" class="taken-all">&nbsp;<span></span>
                                </label>
                            </th>
                            <th class="kt-font-dark">TGL. PEMESANAN</th>
                            <th class="kt-font-dark">NO. TRANSAKSI</th>
                            <th class="kt-font-dark">PENGAMBILAN</th>
                            <th class="kt-font-dark">PRODUK</th>
                            <th class="kt-font-dark">TOTAL</th>
                            <th class="kt-font-dark">PELANGGAN</th>
                            <th class="kt-font-dark">SALES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($sales_order_taking AS $info):  ?>
                        <tr>
                            <td width="10px" class="text-center kt-font-dark">
                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
                                <input type="checkbox" class="taken" name="so_id[]" value="<?php echo $info['id']; ?>">&nbsp;<span></span>
                                </label>
                            </td>
                            <td width="130px" class="text-center kt-font-dark"><?php echo date('d-m-Y', strtotime($info['date'])); ?></td>
                            <td class="text-center kt-font-dark">
                                <a class="kt-font-primary kt-link text-center" href="<?php echo site_url('sales/order/detail/'.$this->global->encrypt($info['id'])); ?>"><b><?php echo $info['invoice']; ?></b></a>
                            </td>
                            <td class=" text-center kt-font-dark">
                                <?php 
                                if($info['taking'] == 1)
                                {
                                    $taking = "LANGSUNG";
                                }
                                else
                                {
                                    $taking = "PENGIRIMAN";
                                }
                                echo $taking; ?>
                            </td>
                            <td class="text-center kt-font-dark"><?php echo $info['total_product']; ?></td>
                            <td class="text-right kt-font-dark"><?php echo number_format($info['grandtotal'],0,".",","); ?></td>
                            <td class="text-center kt-font-dark"><?php echo $info['name_c']; ?></td>
                            <td class="text-center kt-font-dark"><?php echo $info['name_s']; ?></td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
                <!--end: Datatable -->
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" id="btn_save" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-save"></i> BUAT PENGAMBILAN</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php echo form_close() ?>
        </div>
    </div>
    <!-- end:: Content -->
</div>