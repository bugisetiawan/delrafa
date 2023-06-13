<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Transaksi</b></h3>
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
            <div class="alert-text">
                <?php echo $this->session->flashdata('error'); ?><br>
                <?php if($this->session->flashdata('min_product')) :?>
                    <?php foreach($this->session->flashdata('min_product') AS $min_product): ?>
                        <?php echo $min_product['code_p'].' | '.$min_product['name_p']; ?><br>
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
        <?php if($sales_invoice['do_status'] == 1 && $sales_invoice['payment_status'] != 1): ?>
        <div class="alert alert-danger fade show" role="alert">
            <div class="alert-icon"><i class="flaticon-exclamation-2"></i></div>
            <div class="alert-text">Mohon Perhatian! Penjualan ini <b>BELUM LUNAS</b> harap segera melakukan pembayaran/cek pembayaran piutang, terima kasih.</div>
        </div>
        <?php endif; ?>
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        <?php $do_status = ($sales_invoice['do_status'] == 1) ? "<span class='text-success'><i class='fa fa-check'></i></span>" : "<span class='text-danger'><i class='fa fa-times'></i></span>"; ?>
                        <?php $sales_invoice_status = ($sales_invoice['payment_status'] == 1) ? "<span class='text-success'>LUNAS</span>" : "<span class='text-danger'>BELUM LUNAS</span>";  ?>
                        Informasi | No. Transaksi <span class="kt-font-bold kt-font-success"><?php echo $sales_invoice['invoice']; ?></span> | Cetak DO : <?php echo $do_status; ?> | Status Penjualan: <?php echo $sales_invoice_status; ?> | Opt. <?php echo $sales_invoice['name_e']; ?>
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                            <a href="<?php echo site_url('sales/invoice'); ?>" class="btn btn-outline-primary btn-elevate btn-icon"
                                data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Kembali">
                            <i class="fa fa-arrow-left"></i>
                            </a>
                            <?php if($sales_invoice['do_status'] != 1): ?>
                            <a class="btn btn-icon btn-outline-warning"
                                href="<?php echo site_url('sales/invoice/update/'.encrypt_custom($sales_invoice['id'])); ?>"  data-id="<?php echo $sales_invoice['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Perbarui Penjualan">
                            <i class="fa fa-edit"></i>
                            </a>
                            <button class="btn btn-icon btn-outline-danger" id="delete_sales_invoice_btn"
                                data-id="<?php echo $sales_invoice['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Hapus Penjualan">
                            <i class="fa fa-trash"></i>
                            </button>
                            <button class="btn btn-outline-primary btn-elevate" id="create_sales_invoice_do_btn" 
                                data-id="<?php echo $sales_invoice['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Cetak DO">
                            <i class="fa fa-print"></i>
                            Cetak DO
                            </button>
                            <?php else: ?>
                            <!-- <button class="btn btn-icon btn-outline-warning" id="update_sales_invoice_btn"
                                data-link="<?php echo site_url('sales/invoice/update/'.encrypt_custom($sales_invoice['id'])); ?>"  data-id="<?php echo $sales_invoice['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Perbarui Penjualan">
                            <i class="fa fa-edit"></i>
                            </button> -->
                            <button class="btn btn-outline-danger btn-elevate" id="cancel_sales_invoice_do_btn"
                                data-link="<?php echo site_url('transaction/Sales/cancel_sales_invoice_do/'.encrypt_custom($sales_invoice['id'])); ?>" data-id="<?php echo $sales_invoice['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Batal DO">
                            <i class="fa fa-times"></i>
                            Batal DO
                            </button>
                            <?php endif; ?>
                            <?php if($sales_invoice['ppn']==1): ?>
                            <a href="<?php echo site_url('transaction/Sales/print_sales_invoice_ppn/'. $this->global->encrypt($sales_invoice['id'])); ?>" target="_blank" class="btn btn-icon btn-outline-success btn-elevate"
                                data-id="<?php echo $sales_invoice['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Cetak Penjualan">
                            <i class="fa fa-print"></i>
                            </a>
                            <?php else: ?>
                            <a href="<?php echo site_url('transaction/Sales/print_sales_invoice_non/'. $this->global->encrypt($sales_invoice['id'])); ?>" target="_blank" class="btn btn-icon btn-outline-success btn-elevate"
                                data-id="<?php echo $sales_invoice['id']; ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Cetak Penjualan">
                                <i class="fa fa-print"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label kt-font-dark">TGL. PENJUALAN</label>
                            <div class="col-md-9">
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control font-weight-bold', 'value' => date('d-m-Y', strtotime($sales_invoice['date'])), 'readonly' => 'true'); 
                                    echo form_input($data);
                                    ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label kt-font-dark text-right">SALES</label>
                            <div class="col-md-9">
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control font-weight-bold', 'value' => $sales_invoice['code_s'].' | '.$sales_invoice['name_s'], 'readonly' => 'true'); 
                                    echo form_input($data);
                                    ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label kt-font-dark">PELANGGAN</label>
                            <div class="col-md-9">
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control font-weight-bold', 'value' => $sales_invoice['code_c'].' | '.$sales_invoice['name_c'], 'readonly' => 'true'); 
                                    echo form_input($data);
                                    ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label kt-font-dark text-right"><span class="text-danger">*</span>PEMBAYARAN</label>
                            <div class="col-md-2">
                                <input type="hidden" id="payment_update" value=<?php echo $sales_invoice['payment']; ?>>
                                <select class="form-control font-weight-bold" name="payment" id="payment" readonly disabled>
                                    <option value="1" <?php if($sales_invoice['payment'] == 1){ echo "selected";} ?>>TUNAI</option>
                                    <option value="2" <?php if($sales_invoice['payment'] == 2){ echo "selected";} ?>>KREDIT</option>
                                </select>
                            </div>
                            <label class="col-md-2 col-form-label kt-font-dark text-right"><span class="text-danger">*</span>TEMPO (Hari)</label>
                            <div class="col-md-2">
                                <?php 
                                    $data = array('type' => 'number', 'min'=> 0, 'class' => 'form-control font-weight-bold', 'id' => 'payment_due', 'name' => 'payment_due',  'value' => $sales_invoice['payment_due'], 'placeholder' =>'0', 'readonly' => 'true', 'disabled' => 'true');
                                    echo form_input($data);                                             
                                    echo form_error('payment_due', '<p class="text-danger">', '</p>');
                                    ?>
                            </div>
                            <label class="col-md-1 col-form-label text-dark text-right"><span class="text-danger">*</span>PPN</label>                                                                            
                            <div class="col-md-2">
                                <select class="form-control font-weight-bold" name="ppn" id="ppn" readonly disabled>
                                    <option value="0" <?php if($sales_invoice['ppn'] == 0){ echo "selected"; } ?>>NON</option>
                                    <option value="1" <?php if($sales_invoice['ppn'] == 1){ echo "selected"; } ?>>PPN</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label kt-font-dark">KETERANGAN</label>
                            <div class="col-md-9">
                                <textarea class="form-control" rows="3" name="information" id="information" readonly><?php echo $sales_invoice['information']; ?></textarea>
                            </div>
                        </div>
                    </div>
					<div class="col-md-7">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-dark text-right">TGL. JATUH TEMPO</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control text-dark font-weight-bold" value="<?php echo date('d-m-Y', strtotime($sales_invoice['due_date'])); ?>" readonly>
                                <hr>
                                <small class="text-dark">Dibuat: <?php echo date('d-m-Y H:i:s', strtotime($sales_invoice['created'])); ?></small> | <small class="text-dark">Diperbarui: <?php echo date('d-m-Y H:i:s', strtotime($sales_invoice['modified'])); ?></small>
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
                <div class="row">
                    <div class="col-md-12">
                        <!--begin: Datatable -->
                        <?php 
                            $data = array('type' => 'hidden', 'class' => 'form-control', 'id' => 'sales_invoice_id', 'name' => 'sales_invoice_id',  'value' => $sales_invoice['id'], 'readonly' => 'true');
                            echo form_input($data);
                            ?>
                        <table class="table table-bordered table-hover" id="datatable_detail_sales_invoice">
                            <thead>
                                <tr style="text-align:center;">
                                    <th width="10px">NO</th>
                                    <th width="100px">KODE</th>
                                    <th>NAMA</th>
                                    <th width="100px">QTY</th>
                                    <th width="100px">SATUAN</th>
                                    <th width="120px">HARGA</th>
                                    <th width="100px">DISKON (%)</th>
                                    <th width="150px">GUDANG</th>
                                    <th width="120px">TOTAL</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>                                                                                                            
                            </tbody>
                        </table>
                        <!--end: Datatable -->                                                                           
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6">   
                        <?php if($sales_invoice['do_status'] == 0): ?>                                                    
                            <?php if($sales_invoice['payment'] == 1): ?>
                                <p class="text-dark">Informasi Perimaan Pembayaran setelah cetak DO</p>
                                <div class="form-group row" id="cash_ledger_form">
                                    <div class="col-md-4">
                                        <select class="form-control cash_ledger_input" id="from_cl_type" name="from_cl_type" readonly disabled>
                                            <option value="1" <?php if($sales_invoice['cl_type'] == 1) { echo "selected"; } ?>>KAS</option>
                                            <option value="2" <?php if($sales_invoice['cl_type'] == 2) { echo "selected"; } ?>>BANK</option>
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="hidden" id="account_id" value="<?php echo $sales_invoice['account_id']; ?>">
                                        <select class="form-control cash_ledger_input" id="from_account_id" name="from_account_id" readonly disabled></select>
                                    </div>
                                </div>
                            <?php elseif($sales_invoice['payment'] == 2 && $sales_invoice['down_payment'] > 0): ?>
                                <p class="text-dark">Informasi Perimaan Pembayaran Uang Muka setelah cetak DO</p>
                                <div class="form-group row" id="cash_ledger_form">
                                    <div class="col-md-4">
                                        <select class="form-control cash_ledger_input" id="from_cl_type" name="from_cl_type" readonly disabled>
                                            <option value="1" <?php if($sales_invoice['cl_type'] == 1) { echo "selected"; } ?>>KAS</option>
                                            <option value="2" <?php if($sales_invoice['cl_type'] == 2) { echo "selected"; } ?>>BANK</option>
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="hidden" id="account_id" value="<?php echo $sales_invoice['account_id']; ?>">
                                        <select class="form-control cash_ledger_input" id="from_account_id" name="from_account_id" readonly disabled></select>
                                    </div>
                                </div>
                            <?php endif; ?>                            
                        <?php endif; ?>                                     
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label kt-font-dark text-right">TOTAL PRODUK</label>
                            <div class="col-md-3">
                                <?php
                                    $data = array('type' => 'number', 'min' => 0, 'class' => 'form-control text-right', 'id' => 'total_product', 'name' => 'total_product',  'value' => $sales_invoice['total_product'], 'required' => 'true', 'readonly' => 'true'); 
                                    echo form_input($data);                                             
                                    echo form_error('total_product', '<p class="text-danger">', '</p>');
                                    ?>
                            </div>
                            <label class="col-md-3 col-form-label kt-font-dark text-right">TOTAL KUANTITAS</label>
                            <div class="col-md-3">
                                <?php
                                    $data = array('type' => 'number', 'min' => 0, 'class' => 'form-control text-right', 'id' => 'total_qty', 'name' => 'total_qty',  'value' => $sales_invoice['total_qty'], 'required' => 'true', 'readonly' => 'true'); 
                                    echo form_input($data);                                             
                                    echo form_error('total_qty', '<p class="text-danger">', '</p>');
                                    ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label kt-font-dark text-right">SUBTOTAL</label>
                            <div class="col-md-9">
                                <?php
                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'subtotal', 'name' => 'subtotal',  'value' => number_format($sales_invoice['total_price'],'0','.',','), 'required' => 'true', 'readonly' => 'true'); 
                                    echo form_input($data);                                             
                                    echo form_error('subtotal', '<p class="text-danger">', '</p>');
                                    ?>
                            </div>
                        </div>
						<div class="form-group row">
                            <label class="col-md-3 col-form-label kt-font-dark text-right">Diskon % / Diskon Rp.</label>
                            <div class="col-md-4">
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'name' => 'discount_p', 'id' => 'discount_p', 'placeholder' => 'Diskon (%)', 'value' => $sales_invoice['discount_p'], 'required' => 'true', 'readonly' => 'true'); 
                                    echo form_input($data);
                                    ?>
                            </div>
                            <div class="col-md-5">
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'name' => 'discount_rp', 'id' => 'discount_rp', 'placeholder' => 'Diskon (Rupiah)', 'value' => number_format($sales_invoice['discount_rp'],'0','.',','), 'required' => 'true', 'readonly' => 'true'); 
                                    echo form_input($data);                                                                                     
                                    ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label kt-font-dark text-right">Biaya Pengiriman</label>
                            <div class="col-md-9">
                                <?php
                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'delivery_cost', 'name' => 'delivery_cost',  'value' => number_format($sales_invoice['delivery_cost'],'0','.',','), 'required' => 'true', 'readonly' => 'true'); 
                                    echo form_input($data);
								?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label kt-font-dark text-right"><strong>GRANDTOTAL</strong></label>
                            <div class="col-md-9">
                                <?php
                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'grandtotal', 'name' => 'grandtotal',  'value' => number_format($sales_invoice['grandtotal'], 2,'.',','), 'required' => 'true', 'readonly' => 'true'); 
                                    echo form_input($data);
								?>
                            </div>
                        </div>
						<?php if($sales_invoice['account_payable'] > 0): ?>
						<div class="form-group row">
							<label class="col-md-3 col-form-label text-dark text-right">Piutang Penjualan</label>
							<div class="col-md-9">
								<?php 
									$data = array('type' => 'text', 'class' => 'form-control text-right', 'name' => 'account_payable', 'id' => 'account_payable', 'placeholder' => 'Hutang Dagang...', 'value' => number_format($sales_invoice['account_payable'], 2,'.',','), 'readonly' => 'true', 'required' => 'true');
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
                                Daftar Pembayaran Penjualan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#sales_return_tab" role="tab" aria-selected="false">
                                Daftar Retur Penjualan
                            </a>
                        </li>
                        <?php if($sales_invoice['ppn'] != 0): ?>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#sales_tax_invoice_tab" role="tab" aria-selected="false">
                                Daftar Faktur Pajak Penjualan
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
                            <div class="col-md-6">
                                <!-- <div class="row">
                                    <div class="col-form-label col-12">
                                        <div class="kt-checkbox-inline">
                                            <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand text-dark">
                                                <input type="checkbox" class="payment" name="payment[]" value="1">TUNAI
                                                <span></span>
                                            </label>
                                            <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand text-dark">
                                                <input type="checkbox" class="payment"name="payment[]" value="2">TRANSFER
                                                <span></span>
                                            </label>
                                            <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand text-dark">
                                                <input type="checkbox" class="payment" name="payment[]" value="3">CEK/GIRO
                                                <span></span>
                                            </label>
                                            <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand text-dark">
                                                <input type="checkbox" class="payment" name="payment[]" value="4">DEPOSIT
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>   -->
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="javascript: void(0);" onclick="$('#payment_datatable').DataTable().ajax.reload();" class="btn btn-brand btn-square btn-elevate btn-elevate-air">
                                    <i class="la la-refresh"></i>
                                    <span class="d-none d-sm-inline">Refresh Data</span>
                                </a>
                                <?php if($sales_invoice['payment_status'] != 1 && $sales_invoice['account_payable']-$sales_invoice['cheque_payable'] > 0): ?>                     
                                    <?php if($sales_invoice['do_status'] == 1): ?>
                                    <a href="<?php echo base_url('payment/receivable/create/').encrypt_custom($sales_invoice['id']); ?>" class="btn btn-success btn-square btn-elevate btn-elevate-air">
                                        <i class="la la-plus"></i>
                                        Pembayaran Baru
                                    </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div><br>
                        <table class="table table-bordered table-hover" id="payment_datatable">
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
                    <div class="tab-pane" id="sales_return_tab" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-md-6 text-right">
                                <a href="javascript: void(0);" onclick="$('#sales_return_datatable').DataTable().ajax.reload();" class="btn btn-brand btn-square btn-elevate btn-elevate-air">
                                    <i class="la la-refresh"></i>
                                    <span class="d-none d-sm-inline">Refresh Data</span>
                                </a>
                            </div>
                        </div><br>
                        <table class="table table-bordered table-hover" id="sales_return_datatable">
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
                    <div class="tab-pane" id="sales_tax_invoice_tab" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-md-6 text-right">
                                <a href="javascript: void(0);" onclick="$('#sales_tax_invoice_datatable').DataTable().ajax.reload();" class="btn btn-brand btn-square btn-elevate btn-elevate-air">
                                    <i class="la la-refresh"></i>
                                    <span class="d-none d-sm-inline">Refresh Data</span>
                                </a>
                                <a href="javascript:void(0)" class="btn btn-success btn-square btn-elevate btn-elevate-air" data-toggle="modal" data-target="#add_sales_tax_invoice_modal">
                                    <i class="la la-plus"></i>
                                    Faktur Pajak Baru
                                </a>
                            </div>
                        </div><br>
                        <table class="table table-bordered table-hover" id="sales_tax_invoice_datatable">
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
    <?php if($sales_invoice['ppn'] != 0): ?>
    <!--begin::Add Modal-->
    <div class="modal fade" id="add_sales_tax_invoice_modal">
        <div class="modal-dialog modal-lg" role="document">
            <?php echo form_open('transaction/Sales/create_sales_tax_invoice', ['id'=> 'create_data', 'autocomplete' => 'off']); ?>
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
                                $data = array('type' => 'text', 'class' => 'form-control ammount', 'id' => 'tax_invoice_dpp', 'name' => 'dpp', 'placeholder' => 'Silahkan isi besaran dpp...',  'value' => set_value('dpp'), 'required' => 'true'); 
                                echo form_input($data);
                            ?>
                        </div>
                        <div class="col-md-6">
                            <label class="text-dark"><span class="text-danger">*</span>PPN</label>
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control ammount', 'id' => 'tax_invoice_ppn', 'name' => 'ppn', 'placeholder' => 'Silahkan isi besaran ppn...',  'value' => set_value('ppn'), 'required' => 'true'); 
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