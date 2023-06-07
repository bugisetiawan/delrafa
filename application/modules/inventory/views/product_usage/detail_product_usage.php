<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Persediaan</b></h3>
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
            <div class="alert-text">
                <?php echo $this->session->flashdata('error'); ?><br>
                <?php if($this->session->flashdata('min_product')) :?>
                <?php foreach($this->session->flashdata('min_product') AS $min_product): ?>
                    <?php echo $min_product['code'].' | '.$min_product['name']; ?><br>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
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
                        <?php $do_status = ($product_usage['do_status'] == 1) ? "<span class='text-success'><i class='fa fa-check'></i></span>" : "<span class='text-danger'><i class='fa fa-times'></i></span>"; ?>
                        Informasi | No. Transaksi <span class="kt-font-bold kt-font-success"><?php echo $product_usage['code']; ?></span> | Cetak DO: <?php echo $do_status; ?> | User: <?php echo $product_usage['operator']; ?>
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
							<?php $link = ($this->agent->referrer()) ? urldecode($this->agent->referrer()) : site_url('product_usage'); ?>
                            <a href="<?php echo $link; ?>" class="btn btn-outline-primary btn-elevate btn-icon"
                                data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Kembali">
                                <i class="fa fa-arrow-left"></i>
                            </a>
                            <?php if($product_usage['do_status'] != 1): ?>
                            <button class="btn btn-icon btn-outline-danger" id="delete_product_usage_btn"
                                data-id="<?php echo $product_usage['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Hapus Pemakaian">
                            <i class="fa fa-trash"></i>
                            </button>
                            <button class="btn btn-outline-primary btn-elevate" id="create_product_usage_do_btn" 
                                data-id="<?php echo $product_usage['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Cetak DO">
                            <i class="fa fa-print"></i>
                            Cetak DO
                            </button>
                            <?php else: ?>                            
                            <button class="btn btn-outline-danger btn-elevate" id="cancel_product_usage_do_btn"
                                data-id="<?php echo $product_usage['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Batal DO">
                            <i class="fa fa-times"></i>
                            Batal DO
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="form-group row">
                    <div class="col-md-5">
                        <div class="row">
                            <label class="col-md-3 col-form-label text-dark">TGL. PEMAKAIAN</label>
                            <div class="col-md-9">
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control font-weight-bold', 'value' => date('d-m-Y', strtotime($product_usage['date'])), 'readonly' => 'true'); 
                                    echo form_input($data);
                                    ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">   
						<div class="row">
                            <label class="col-md-3 col-form-label text-dark text-right">KETERANGAN</label>
                            <div class="col-md-9">
                                <textarea class="form-control" rows="3" name="information" id="information" readonly><?php echo $product_usage['information']; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Daftar Produk
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="form-group row">
                    <div class="col-md-12">
                        <?php 
                            $data = array('type' => 'hidden', 'class' => 'form-control', 'id' => 'product_usage_id', 'value' => $product_usage['id'], 'readonly' => 'true');
                            echo form_input($data);
						?>
                        <table class="table table-bordered table-hover" id="datatable">
                            <thead>
                                <tr style="text-align:center;">
                                    <th width="10px">NO</th>
                                    <th width="100px">KODE</th>
                                    <th>NAMA</th>
                                    <th width="100px">QTY</th>
                                    <th width="100px">SATUAN</th>
                                    <th width="120px">HARGA</th>
                                    <th width="150px">GUDANG</th>
                                    <th width="120px">TOTAL</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>                
                <div class="form-group row">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-dark text-right">TOTAL PRODUK</label>
                            <div class="col-md-3">
                                <?php
                                    $data = array('type' => 'number', 'min' => 0, 'class' => 'form-control text-right', 'id' => 'total_product', 'value' => $product_usage['total_product'], 'required' => 'true', 'readonly' => 'true'); 
                                    echo form_input($data);                                             
                                    echo form_error('total_product', '<p class="text-danger">', '</p>');
                                    ?>
                            </div>
                            <label class="col-md-3 col-form-label text-dark text-right">TOTAL KUANTITAS</label>
                            <div class="col-md-3">
                                <?php
                                    $data = array('type' => 'number', 'min' => 0, 'class' => 'form-control text-right', 'id' => 'total_qty', 'value' => $product_usage['total_qty'], 'required' => 'true', 'readonly' => 'true'); 
                                    echo form_input($data);                                             
                                    echo form_error('total_qty', '<p class="text-danger">', '</p>');
                                    ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-dark text-right">GRANDTOTAL</label>
                            <div class="col-md-9">
                                <?php
                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'grandtotal', 'value' => number_format($product_usage['grandtotal'], 2, '.', ','), 'required' => 'true', 'readonly' => 'true'); 
                                    echo form_input($data);                                             
                                    echo form_error('subtotal', '<p class="text-danger">', '</p>');
                                    ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--begin::Verify Module Password Modal-->
    <div class="modal fade" id="verify_module_password_modal" tabindex="-1" role="dialog" aria-labelledby="closeModal" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <?php echo form_open('', array('class' => 'form-horizontal', 'id' => 'verify_module_password_form', 'autocomplete' => 'off')); ?>
                <input type="hidden" id="module_url" name="module_url"> <input type="hidden" id="action_module" name="action">
                <div class="modal-header">
                    <h5 class="modal-title">Verifikasi Password</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="password" name="verifypassword" id="verifypassword" class="form-control" placeholder="Silahkan isi Password untuk melanjutkan...">
                    </div>
                </div>
                <div class="modal-footer">																					
                    <button type="button" class="btn btn-danger form-control" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
                    <button type="submit" class="btn btn-success form-control"><i class="fa fa-save"></i>  Verifikasi</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
    <!--end::End Verify Module Password Modal-->
</div>