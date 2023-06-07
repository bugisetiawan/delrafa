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
        <?php if($purchase_invoice['payment_status'] != 1 && $purchase_invoice['account_payable'] > 0 || $purchase_invoice['cheque_payable'] > 0): ?>
        <div class="alert alert-danger fade show" role="alert">
            <div class="alert-icon"><i class="flaticon-exclamation-2"></i></div>
            <div class="alert-text">Mohon Perhatian! Pembelian ini <b>BELUM LUNAS</b> harap segera melakukan pembayaran atau periksa konfirmasi pencairan cek/giro, terima kasih</div>
        </div>
        <?php endif; ?>
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        <?php $purchase_invoice_status = ($purchase_invoice['payment_status'] == 1) ? "<span class='font-weight-bold text-success'>LUNAS</span>" : "<span class='font-weight-bold text-danger'>BELUM LUNAS</span>";  ?>
                        Informasi | No.Transaksi: <span class="font-weight-bold text-success"><?php echo $purchase_invoice['code']; ?></span> | Status: <?php echo $purchase_invoice_status; ?> | User: <span id="font-weight-bold"><?php echo $purchase_invoice['name_e']; ?></span>
                        <?php 
                            $data = array('type' => 'hidden', 'class' => 'form-control', 'id' => 'purchase_invoice_id', 'name' => 'purchase_invoice_id',  'value' => $purchase_invoice['id'], 'readonly' => 'true');
                            echo form_input($data);
                        ?>
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                            <?php $link = ($this->agent->referrer()) ? urldecode($this->agent->referrer()) : site_url('purchase'); ?>
                            <a href="<?php echo $link; ?>" class="btn btn-outline-primary btn-elevate btn-icon"
                                data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Kembali">
                                <i class="fa fa-arrow-left"></i>
                            </a>
                            <button type="button"  class="btn btn-outline-warning btn-elevate btn-icon" id="update_purchase_invoice_btn"
                                data-link="<?php echo site_url('purchase/invoice/update/'. encrypt_custom($purchase_invoice['id'])); ?>"  data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Perbarui Pembelian">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-elevate btn-icon" id="delete_purchase_invoice_btn"
                                data-id="<?php echo $purchase_invoice['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Hapus Pembelian">
                                <i class="la la-trash"></i>
                            </button>
                            <a href="<?php echo site_url('purchase/invoice/print/'. encrypt_custom($purchase_invoice['id'])); ?>" target="_blank" class="btn btn-outline-success btn-elevate btn-icon"
                                data-id="<?php echo $purchase_invoice['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Cetak Pembelian">
                            <i class="fa fa-print"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-dark">TANGGAL</label>
                            <div class="col-md-9">                                                                                  
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control text-dark font-weight-bold', 'id' => 'date', 'name' => 'date',  'value' => date('d-m-Y', strtotime($purchase_invoice['date'])), 'readonly' => 'true');
                                    echo form_input($data);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-dark text-right">NO. REFRENSI</label>
                            <div class="col-md-9">
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control text-dark font-weight-bold', 'value' => $purchase_invoice['invoice'], 'readonly' => 'true');
                                    echo form_input($data);
                                    ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-dark">SUPPLIER</label>
                            <div class="col-md-9">
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control text-dark font-weight-bold', 'value' => $purchase_invoice['supplier_code'].' | '.$purchase_invoice['name_s'], 'readonly' => 'true');
                                    echo form_input($data);
                                    ?>
                            </div>                                        
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-dark text-right">PEMBAYARAN</label>
                            <div class="col-md-3">
                                <select class="form-control text-dark font-weight-bold" name="payment" id="payment" readonly disabled>                                                
                                    <option value="1" <?php if($purchase_invoice['payment'] == 1){ echo "selected"; } ?>>TUNAI</option>
                                    <option value="2" <?php if($purchase_invoice['payment'] == 2){ echo "selected"; } ?>>KREDIT</option>
                                </select>
                            </div>
                            <label class="col-md-1 col-form-label text-dark text-right">TEMPO</label>
                            <div class="col-md-2">
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control text-dark font-weight-bold', 'value' => $purchase_invoice['payment_due'], 'readonly' => 'true');
                                    echo form_input($data);
                                ?>
                            </div>
                            <label class="col-md-1 col-form-label text-dark text-right">PPN</label>                                      
                            <div class="col-md-2">
                                <select class="form-control text-dark font-weight-bold" name="ppn" id="ppn" readonly disabled>
                                        <option value="0" <?php if($purchase_invoice['ppn'] == 0){ echo "selected"; } ?>>NON</option>
                                        <option value="1" <?php if($purchase_invoice['ppn'] == 1){ echo "selected"; } ?>>PPN</option>
                                        <option value="2" <?php if($purchase_invoice['ppn'] == 2){ echo "selected"; } ?>>FINAL</option>
                                </select>
                            </div>                                      
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-dark">KETERANGAN</label>
                            <div class="col-md-9">
                                <textarea class="form-control" rows="3" name="information" id="information" readonly><?php echo $purchase_invoice['information']; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-dark text-right">JATUH TEMPO</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control text-dark font-weight-bold" value="<?php echo date('d-m-Y', strtotime($purchase_invoice['due_date'])); ?>" readonly>
                                <hr>
                                <small class="text-dark">Dibuat: <?php echo date('d-m-Y H:i:s', strtotime($purchase_invoice['created'])); ?></small> | <small class="text-dark">Diperbarui: <?php echo date('d-m-Y H:i:s', strtotime($purchase_invoice['modified'])); ?></small>
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
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions"> 
                            <?php if($purchase_invoice['ppn'] != 0): ?>                                       
                            <div class="row" id="include_tax_method">
                                <label class="col-9 col-form-label text-dark">Harga termasuk pajak</label>
                                <div class="col-3">
                                    <span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--success">
                                        <label>
                                            <input type="checkbox" id="price_include_tax" name="price_include_tax" value="1" <?php if($purchase_invoice['price_include_tax'] == 1){ echo "checked"; } ?> disabled>
                                            <span></span>
                                        </label>
                                    </span>
                                </div>
                            </div>                                  
                            <?php endif; ?>                                                                                                 
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-md-12">                                
                        <table class="table table-bordered table-hover" id="datatable_detail_purchase">
                            <thead>
                                <tr style="text-align:center;">
                                    <th>NO</th>
                                    <th>KODE</th>
                                    <th>NAMA</th>
                                    <th>QTY</th>
                                    <th>SATUAN</th>
                                    <th>HARGA</th>
                                    <th>DISKON (%)</th>
                                    <th>GUDANG</th>
                                    <th>TOTAL</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>                                                                                                            
                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6">                                        
                    </div>                                    
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-dark text-right">TOTAL PRODUK</label>
                            <div class="col-md-3">
                                <?php
                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_product', 'name' => 'total_product',  'value' => $purchase_invoice['total_product'], 'readonly' => 'true'); 
                                    echo form_input($data);                                             
                                    echo form_error('total_product', '<p class="text-danger">', '</p>');
                                ?>
                            </div>
                            <label class="col-md-3 col-form-label text-dark text-right">TOTAL KUANTITAS</label>
                            <div class="col-md-3">
                                <?php
                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_qty', 'name' => 'total_qty',  'value' => $purchase_invoice['total_qty'], 'readonly' => 'true'); 
                                    echo form_input($data);                                             
                                    echo form_error('total_qty', '<p class="text-danger">', '</p>');
                                ?>
                            </div>                                        
                        </div>                                        
                    </div>
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label font-weight-bold text-dark text-right">SUBTOTAL</label>
                            <div class="col-md-9">
                                <?php
                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'subtotal', 'name' => 'subtotal',  'value' => number_format($purchase_invoice['total_price'],'2','.',','), 'readonly' => 'true');
                                    echo form_input($data);
                                ?>
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-dark text-right">Diskon % / Diskon Rp.</label>
                            <div class="col-md-4">
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control text-right', 'name' => 'discount_p', 'id' => 'discount_p', 'placeholder' => 'Diskon (%)', 'value' => number_format($purchase_invoice['discount_p'],'2','.',',').' %', 'readonly' => 'true'); 
                                echo form_input($data);
                            ?>
                            </div>
                            <div class="col-md-5">
                            <?php
                                $data = array('type' => 'text', 'class' => 'form-control text-right', 'name' => 'discount_rp', 'id' => 'discount_rp', 'placeholder' => 'Diskon (Rupiah)', 'value' => number_format($purchase_invoice['discount_rp'],'2','.',','), 'readonly' => 'true'); 
                                echo form_input($data);
                            ?>
                            </div>
                        </div>
                        <?php if($purchase_invoice['ppn'] != 0): ?>
                        <div class="form-group row" id="tax_form">
                            <label class="col-md-3 col-form-label text-dark text-right">DPP</label>
                            <?php                                 
                                $dpp = $purchase_invoice['grandtotal'] / 1.11;
                                $ppn = $purchase_invoice['grandtotal'] - $dpp;
                            ?>
                            <div class="col-md-9">
                                <?php
                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_dpp', 'name' => 'total_dpp',  'value' => number_format($dpp, 2,'.',','), 'readonly' => 'true'); 
                                    echo form_input($data);                                                                                 
                                ?>
                            </div>
                        </div>
                        <div class="form-group row" id="tax_form">
                            <label class="col-md-3 col-form-label text-dark text-right">PPN</label>
                            <div class="col-md-9">
                                <?php
                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_tax', 'name' => 'total_tax',  'value' => number_format($ppn, 2,'.',','), 'readonly' => 'true'); 
                                    echo form_input($data);                                                                                 
                                ?>
                            </div>
                        </div>
                        <?php endif; ?>
						<div class="form-group row">
                            <label class="col-md-3 col-form-label text-dark text-right">Biaya Pengiriman</label>
                            <div class="col-md-9">
                                <?php
                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'delivery_cost', 'name' => 'delivery_cost',  'value' => number_format($purchase_invoice['delivery_cost'], 2,'.',','), 'readonly' => 'true'); 
                                    echo form_input($data);
                                ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-dark text-right"><strong>GRANDTOTAL</strong></label>
                            <div class="col-md-9">
                                <?php
                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'grandtotal', 'name' => 'grandtotal',  'value' => number_format($purchase_invoice['grandtotal'], 2,'.',','), 'readonly' => 'true'); 
                                    echo form_input($data);
                                ?>
                            </div>
                        </div>
                        <hr>
                        <?php if($purchase_invoice['payment_status'] != 1): ?>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-dark text-right">Hutang Pembelian</label>
                            <div class="col-md-9">
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'name' => 'account_payable', 'id' => 'account_payable', 'value' => number_format($purchase_invoice['account_payable'], 2,'.',','), 'readonly' => 'true');
                                    echo form_input($data);                                             
                                    echo form_error('account_payable', '<p class="text-danger">', '</p>');
                                    ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-dark text-right">Hutang Cek/Giro</label>
                            <div class="col-md-9">
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'name' => 'account_payable', 'id' => 'account_payable', 'value' => number_format($purchase_invoice['cheque_payable'], 2,'.',','), 'readonly' => 'true');
                                    echo form_input($data);                                             
                                    echo form_error('account_payable', '<p class="text-danger">', '</p>');
                                    ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-dark text-right"><strong>Total Hutang</strong></label>
                            <div class="col-md-9">
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'name' => 'account_payable', 'id' => 'account_payable', 'value' => number_format($purchase_invoice['account_payable']+$purchase_invoice['cheque_payable'], 2,'.',','), 'readonly' => 'true');
                                    echo form_input($data);                                             
                                    echo form_error('account_payable', '<p class="text-danger">', '</p>');
                                    ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet kt-portlet--tabs">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-toolbar">
                    <ul class="nav nav-tabs nav-tabs-line nav-tabs-line-primary" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#payment_tab" role="tab" aria-selected="false">
                                Daftar Pembayaran Pembelian
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#purchase_return_tab" role="tab" aria-selected="false">
                                Daftar Retur Pembelian
                            </a>
                        </li>
                        <?php if($purchase_invoice['ppn'] != 0): ?>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#purchase_tax_invoice_tab" role="tab" aria-selected="false">
                                Daftar Faktur Pajak Pembelian
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="tab-content">
                    <div class="tab-pane active" id="payment_tab" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-md-6 text-right">
                                <a href="javascript: void(0);" onclick="$('#datatable_payment').DataTable().ajax.reload();" class="btn btn-brand btn-square btn-elevate btn-elevate-air">
                                    <i class="la la-refresh"></i>
                                    <span class="d-none d-sm-inline">Refresh Data</span>
                                </a>
                                <?php if($purchase_invoice['payment_status'] != 1 && $purchase_invoice['account_payable']-$purchase_invoice['cheque_payable'] > 0): ?>                     
                                <a href="<?php echo base_url('payment/debt/create/').encrypt_custom($purchase_invoice['id']); ?>" class="btn btn-success btn-square btn-elevate btn-elevate-air">
                                    <i class="la la-plus"></i>
                                    Pembayaran Baru
                                </a>
                                <?php endif; ?>
                            </div>
                        </div><br>
                        <table class="table table-bordered table-hover" id="datatable_payment">
                            <thead>
                                <tr style="text-align:center;">
                                    <th class="notexport">NO</th>
                                    <th>TANGGAL</th>
                                    <th>NO. TRANSAKSI</th>
                                    <th class="text-center">TOTAL</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>                                                                                                                 
                            </tbody>
                        </table>                                                    
                    </div>
                    <div class="tab-pane" id="purchase_return_tab" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-md-6 text-right">
                                <a href="javascript: void(0);" onclick="$('#purchase_return_datatable').DataTable().ajax.reload();" class="btn btn-brand btn-square btn-elevate btn-elevate-air">
                                    <i class="la la-refresh"></i>
                                    <span class="d-none d-sm-inline">Refresh Data</span>
                                </a>
                            </div>
                        </div><br>
                        <table class="table table-bordered table-hover" id="purchase_return_datatable">
                            <thead>
                                <tr>
                                    <th class="notexport">NO</th>
                                    <th class="text-center">TANGGAL</th>
                                    <th class="text-center">NO.TRANSAKSI</th>                                                                        
                                    <th class="text-center">TOTAL</th> 
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>                                                                                                                 
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane" id="purchase_tax_invoice_tab" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-md-6 text-right">
                                <a href="javascript: void(0);" onclick="$('#purchase_tax_invoice_datatable').DataTable().ajax.reload();" class="btn btn-brand btn-square btn-elevate btn-elevate-air">
                                    <i class="la la-refresh"></i>
                                    <span class="d-none d-sm-inline">Refresh Data</span>
                                </a>
                                <a href="javascript:void(0)" class="btn btn-success btn-square btn-elevate btn-elevate-air" data-toggle="modal" data-target="#create_purchase_tax_invoice_modal">
                                    <i class="la la-plus"></i>
                                    Faktur Pajak Baru
                                </a>
                            </div>
                        </div><br>
                        <table class="table table-bordered table-hover" id="purchase_tax_invoice_datatable">
                            <thead>
                                <tr>
                                    <th class="notexport">NO.</th>
                                    <th class="text-center">TANGGAL</th>
                                    <th class="text-center">NO. FAKTUR</th>
                                    <th class="text-center">DPP</th> 
                                    <th class="text-center">PPN</th>
                                    <th class="text-center">TOTAL</th>
                                    <th class="text-center">AKSI</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>                                                                                                                 
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end:: Content -->
    <?php if($purchase_invoice['ppn'] != 0): ?>
    <!--begin::Add Modal-->
    <div class="modal fade" id="create_purchase_tax_invoice_modal">
        <div class="modal-dialog modal-lg" role="document">
            <?php echo form_open('transaction/Purchase/add_purchase_tax_invoice', ['id'=> 'create_data', 'autocomplete' => 'off']); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Faktur Pajak</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label class="text-dark"><span class="text-danger">*</span>Tanggal Faktur Pajak</label>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'tax_invoice_date', 'name' => 'date', 'placeholder' => 'Tanggal Faktur Pajak',  'value' => date('d-m-Y'), 'required' => 'true'); 
                                echo form_input($data);
                            ?>
                        </div>
                        <div class="col-md-8">
                            <label class="text-dark"><span class="text-danger">*</span>No. Faktur Pajak</label>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'tax_invoice_number', 'name' => 'number', 'placeholder' => 'Silahkan isi no faktur pajak...',  'value' => set_value('number'), 'required' => 'true'); 
                                echo form_input($data);
                            ?>    
                        </div>                       
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="text-dark"><span class="text-danger">*</span>DPP</label>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control ammount', 'id' => 'tax_invoice_dpp', 'name' => 'dpp', 'placeholder' => 'Silahkan isi besaran dpp...',  'value' => number_format($dpp, 2,'.',','), 'required' => 'true'); 
                                echo form_input($data);
                            ?>
                        </div>
                        <div class="col-md-6">
                            <label class="text-dark"><span class="text-danger">*</span>PPN</label>
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control ammount', 'id' => 'tax_invoice_ppn', 'name' => 'ppn', 'placeholder' => 'Silahkan isi besaran ppn...',  'value' => number_format($ppn, 2,'.',','), 'required' => 'true'); 
                                    echo form_input($data);
                                ?>
                        </div>                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> SIMPAN</button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
    <!--end::Add Modal-->
    <?php endif; ?>
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
                        <span class="text-dark" id="veryfy_message"></span>
                        <input type="password" name="verifypassword" id="verifypassword" class="form-control" placeholder="Silahkan isi Password untuk melanjutkan..." autocomplete="off">
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