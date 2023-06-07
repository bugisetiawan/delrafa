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
        <!--begin::Portlet-->
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Informasi | No. Transaksi <span class="kt-font-bold kt-font-success"><?php echo $pos['invoice']; ?></span>
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions"> 									                                    
                            <a href="<?php echo base_url('pos/transaction'); ?>" class="btn btn-outline-primary btn-elevate btn-icon"
                                data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Kembali ke Daftar Penjualan (POS)">
                                <i class="fa fa-arrow-left"></i>
                            </a>
                            <button type="button" class="btn btn-icon btn-outline-warning" id="update_transaction_btn"
                                data-link="<?php echo site_url('pos/transaction/update/'.encrypt_custom($pos['id'])); ?>"  data-id="<?php echo $pos['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Perbarui Penjualan (POS)">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-outline-danger" id="delete_transaction_btn"
                                data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Hapus Penjualan (POS)">
                                <i class="fa fa-trash"></i>
                            </button>
                            <a href="<?php echo base_url('pos/cashier/print_bill/'.$this->global->encrypt($pos['id'])); ?>" target="_blank" class="btn btn-outline-success btn-elevate btn-icon"
                                data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Cetak Penjualan (POS)">
                            <i class="la la-print"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>                    
            <?php 
                $data = array('type' => 'hidden', 'class' => 'form-control', 'id' => 'pos_id', 'value' => $pos['id'], 'readonly' => 'true');
                echo form_input($data);
            ?>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="kt-font-dark">TANGGAL & WAKTU TRANSAKSI</label>
                            <?php
                                $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'date', 'name' => 'date',  'value' => date('d-m-Y', strtotime($pos['date'])) .' | '. $pos['time'], 'readonly' => 'true');
                                echo form_input($data);
                            ?>                                                                                                                
                        </div>
                    </div>                   
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="kt-font-dark">PELANGGAN</label>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control', 'value' => $pos['name_c'], 'readonly' => 'true');
                                echo form_input($data);
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="kt-font-dark">KASIR</label>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control', 'value' => $pos['code_e'].' | '.$pos['name_e'], 'readonly' => 'true');
                                echo form_input($data);
                            ?>
                        </div>
                    </div>
                </div>
            </div>                    
        </div>
        <!--end::Portlet--> 
        <!--begin::Portlet-->
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Daftar Produk
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-md-12">
                        <!--begin: Datatable -->
                        <table class="table table-bordered table-hover table-checkable" id="datatable_detail_pos">
                            <thead>
                                <tr style="text-align:center;">
                                    <th>NO</th>
                                    <th>KODE</th>
                                    <th>NAMA</th>
                                    <th>QTY</th>
                                    <th>SATUAN</th>
                                    <th>HARGA</th>											
                                    <th>TOTAL</th>
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
        <!--end::Portlet-->  
        <!--begin::Portlet-->
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Pembayaran
                    </h3>
                </div>
            </div>                        
            <div class="kt-portlet__body">
            <div class="row">
                    <div class="col-md-3">
                        <div class="form-group row">
                            <label class="col-md-6 col-form-label kt-font-dark">TOTAL PRODUK:</label>
                            <div class="col-md-6">
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control', 'value' => $pos['total_product'], 'readonly' => 'true');
                                    echo form_input($data);
                                    ?>
                            </div>
                        </div>								
                    </div>							
                    <div class="col-md-3">
                        <div class="form-group row">
                            <label class="col-md-6 col-form-label kt-font-dark text-right">TOTAL KUANTITAS:</label>
                            <div class="col-md-6">
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control', 'value' => $pos['total_qty'], 'readonly' => 'true');
                                    echo form_input($data);
                                    ?>
                            </div>
                        </div>	
                    </div>
                    <div class="col-md-6">								
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label kt-font-dark text-right">GRAND TOTAL</label>
                            <div class="col-md-9">
                                <?php
                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'value' => number_format($pos['grandtotal'],'0','.',','), 'readonly' => 'true');
                                    echo form_input($data);
                                    ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                    <div class="form-group row">
                            <label class="col-md-3 col-form-label kt-font-dark">JENIS PEMBAYARAN</label>
                            <div class="col-md-9">                                        
                                <?php
                                if($pos['payment'] == 0)
                                {
                                    $payment = "CASH";
                                }
                                elseif($post['payment'] == 1)
                                {
                                    $payment = "KARTU DEBIT";
                                }
                                elseif($post['payment'] == 2)
                                {
                                    $payment = "KARTU CREDIT";
                                }                                        
                                $data = array('type' => 'text', 'class' => 'form-control', 'value' => $payment, 'readonly' => 'true');
                                    echo form_input($data);
                                    ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">								
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label kt-font-dark text-right">BAYAR</label>
                            <div class="col-md-9">
                                <?php
                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'value' => number_format($pos['pay'],'0','.',','), 'readonly' => 'true');
                                    echo form_input($data);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6"></div>
                    <div class="col-md-6">								
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label kt-font-dark text-right">KEMBALI</label>
                            <?php $kembali = $pos['pay'] - $pos['grandtotal']; ?>
                            <div class="col-md-9">
                                <?php
                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'value' => number_format($kembali,'0','.',','), 'readonly' => 'true');
                                    echo form_input($data);
                                    ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>                                                                   
        </div>
        <!--end::Portlet-->  	
    </div>
    <!-- end:: Content -->
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