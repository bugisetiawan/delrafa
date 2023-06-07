<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Keuangan</h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>
            <h3 class="kt-subheader__title"><b><?php echo $title; ?></b></h3>
        </div>
    </div>
    <?php if(isset($purchase_invoice)): ?>
        <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
        <?php echo form_open_multipart('', ['id' => 'transaction_form', 'autocomplete'=>'off']); ?>
        <div class="alert alert-dark fade show" role="alert">
            <div class="alert-icon"><i class="flaticon-exclamation-2"></i></div>
            <div class="alert-text">Mohon Perhatian! Label yang memiliki bintang(<span class="text-danger">*</span>) wajib diisi, terima kasih.</div>
        </div>
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Informasi
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="form-group row">
                    <div class="col-md-6">
                        <div class="row">
                            <label class="col-md-3 col-form-label text-dark"><span class="text-danger">*</span>TGL. PEMBAYARAN</label>
                            <div class="col-md-9">
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'date', 'name' => 'date',  'value' => date('d-m-Y'), 'placeholder' => 'Silahkan isi tanggal pembelian', 'required' => 'true'); 
                                    echo form_input($data);                                             
                                    echo form_error('date', '<p class="text-danger">', '</p>');
                                    ?> 
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">                    
                        <div class="row">
                            <label class="col-md-3 col-form-label text-dark text-right"><span class="text-danger">*</span>SUPPLIER</label>                                            
                            <div class="col-md-9">
                                <input type="text" class="form-control" value="<?php echo $purchase_invoice['code_s'].' | '.$purchase_invoice['name_s']; ?>" readonly>
                                <input type="hidden" id="supplier_id" name="supplier_id" value="<?php echo $purchase_invoice['id_s']; ?>">
                                <input type="hidden" id="supplier_code" name="supplier_code" value="<?php echo $purchase_invoice['code_s']; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-6">
                        <div class="row">
                            <label class="col-md-3 col-form-label kt-font-dark">KETERANGAN</label>
                            <div class="col-md-9">
                                <textarea class="form-control" rows="3" id="information" name="information" placeholder="Silahkan isi keterangan bila ada..."></textarea>
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
                        Daftar Transaksi                 
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-sm" id="transaction_table">
                            <thead>
                                <tr style="text-align:center;">
                                    <th width="200"><span class="text-danger">*</span>NO. TRANSAKSI</th>
                                    <th width="150px"><span class="text-danger">*</span>TGL. TRANSAKSI</th>
                                    <th><span class="text-danger">*</span>NO. REFRENSI</th>
                                    <th>GRANDTOTAL</th>
                                    <th><span class="text-danger">*</span>TAGIHAN (HUTANG)</th>
                                    <th><span class="text-danger">*</span>BAYAR</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="text-align:center;">
                                    <td> 
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control', 'value' => $purchase_invoice['code'], 'readonly' => 'true'); 
                                            echo form_input($data);
                                            $data = array('type' => 'hidden', 'class' => 'form-control transaction_id', 'name' => 'transaction_id', 'value' => $purchase_invoice['id'], 'required' => 'true'); 
                                            echo form_input($data);
                                            echo form_error('transaction_id', '<p class="text-danger">', '</p>');
                                        ?>                                       
                                    </td>                                    
                                    <td>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control text-center transaction_date', 'value' => date('d-m-Y', strtotime($purchase_invoice['date'])), 'readonly' => 'true'); 
                                            echo form_input($data);
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control transaction_invoice', 'value' => $purchase_invoice['invoice'], 'readonly' => 'true'); 
                                            echo form_input($data);
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control text-right transaction_grandtotal', 'value' => number_format($purchase_invoice['grandtotal'], 2, '.', ','), 'readonly' => 'true'); 
                                            echo form_input($data);
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control text-right transaction_account_payable', 'value' => number_format($purchase_invoice['account_payable'], 2, '.', ','), 'readonly' => 'true'); 
                                            echo form_input($data);
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control text-right amount transaction_pay', 'name' => 'transaction_pay', 'placeholder' => '0',  'value' => number_format($purchase_invoice['account_payable'], 2, '.', ','), 'required' => true); 
                                            echo form_input($data);                                             
                                            echo form_error('transaction_pay', '<p class="text-danger">', '</p>');
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right"><label class="col-form-label">BIAYA LAINNYA</label></td>
                                    <td>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control text-right amount', 'id'=> 'cost', 'name' => 'cost', 'placeholder' => '0',  'value' => set_value('cost')); 
                                            echo form_input($data);                                             
                                            echo form_error('cost', '<p class="text-danger">', '</p>');
                                        ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Daftar Pembayaran
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-sm" id="payment_table">
                            <thead>
                                <tr style="text-align:center;">
                                    <th width="200px"><span class="text-danger">*</span>METODE</th>
                                    <th><span class="text-danger">*</span>AKUN</th>
                                    <th width="200px">NO. CEK/GIRO</th>
                                    <th width="200px"><span class="text-danger">*</span>TGL. BUKA CEK/GIRO</th>
                                    <th width="200px"><span class="text-danger">*</span>TGL. CAIR CEK/GIRO</th>
                                    <th><span class="text-danger">*</span>TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select class="form-control payment_method" id="payment_method" name="payment_method[]" required>
                                            <option value="1">KAS</option>
                                            <option value="2">TRANSFER</option>
                                            <option value="3">CEK/GIRO</option>
                                            <option value="4">DEPOSIT</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control account_id" id="account_id" name="account_id[]" required></select>
                                    </td>
                                    <td>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control cheque_number', 'id' => 'cheque_number', 'name' => 'cheque_number[]', 'readonly' => true, 'required' => false); 
                                            echo form_input($data);
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control date cheque_open_date', 'id' => 'cheque_open_date', 'name' => 'cheque_open_date[]', 'readonly' => true, 'required' => false); 
                                            echo form_input($data);
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control date cheque_number', 'id' => 'cheque_number', 'name' => 'cheque_close_date[]', 'readonly' => true, 'required' => false); 
                                            echo form_input($data);
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control text-right amount payment_pay', 'id' => 'payment_pay', 'name' => 'payment_pay[]', 'placeholder' => '0',  'value' => 0, 'required' => true); 
                                            echo form_input($data);
                                        ?>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-sm btn-primary btn-block" id="add_payment"><i class="fa fa-plus"></i> Tambah Baris Pembayaran</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet">
            <div class="kt-portlet__body">
                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="text-dark font-weight-bold">TOTAL TRANSAKSI</label>
                        <?php
                            $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_transaction_pay', 'value' => 0, 'required' => 'true', 'readonly' => 'true'); 
                            echo form_input($data);
                        ?>
                    </div>
                    <div class="col-md-6">
                        <label class="text-dark font-weight-bold">TOTAL PEMBAYARAN</label>
                        <?php
                            $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_payment_pay', 'value' => 0, 'required' => 'true', 'readonly' => 'true');
                            echo form_input($data);
                        ?>
                    </div>
                    <br>
                    <div class="col-md-12 text-center">
                        <p id="notify" class="text-danger font-weight-bold"></p>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="<?php echo urldecode($this->agent->referrer()); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control" id="btn-save" disabled><i class="fa fa-save"></i> SIMPAN</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php echo form_close() ?>
    </div>
    <?php else: ?>
        <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
        <?php echo form_open_multipart('', ['class' => 'repeater', 'id' => 'transaction_form', 'autocomplete'=>'off']); ?>
        <div class="alert alert-dark fade show" role="alert">
            <div class="alert-icon"><i class="flaticon-exclamation-2"></i></div>
            <div class="alert-text">Mohon Perhatian! Label yang memiliki bintang(<span class="text-danger">*</span>) wajib diisi, terima kasih.</div>
        </div>
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Informasi
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="form-group row">
                    <div class="col-md-6">
                        <div class="row">
                            <label class="col-md-3 col-form-label text-dark"><span class="text-danger">*</span>TGL. PEMBAYARAN</label>
                            <div class="col-md-9">
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'date', 'name' => 'date',  'value' => date('d-m-Y'), 'placeholder' => 'Silahkan isi tanggal pembelian', 'required' => 'true'); 
                                    echo form_input($data);                                             
                                    echo form_error('date', '<p class="text-danger">', '</p>');
                                    ?> 
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">                    
                        <div class="row">
                            <label class="col-md-3 col-form-label text-dark text-right"><span class="text-danger">*</span>SUPPLIER</label>                                            
                            <div class="col-md-9">
                                <input type="hidden" id="supplier_id" name="supplier_id">
                                <select class="form-control" name="supplier_code" id="supplier_code" required>
                                    <option value="">- PILIH SUPPLIER -</option>
                                </select>                                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-6">
                        <div class="row">
                            <label class="col-md-3 col-form-label kt-font-dark">KETERANGAN</label>
                            <div class="col-md-9">
                                <textarea class="form-control" rows="3" id="information" name="information" placeholder="Silahkan isi keterangan bila ada..."></textarea>
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
                        Daftar Transaksi                 
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-sm" id="transaction_table">
                            <thead>
                                <tr style="text-align:center;">
                                    <th width="200"><span class="text-danger">*</span>NO. TRANSAKSI</th>
                                    <th width="150px"><span class="text-danger">*</span>TGL. TRANSAKSI</th>
                                    <th><span class="text-danger">*</span>NO. REFRENSI</th>
                                    <th>GRANDTOTAL</th>
                                    <th><span class="text-danger">*</span>TAGIHAN (HUTANG)</th>
                                    <th><span class="text-danger">*</span>BAYAR</th>
                                </tr>
                            </thead>
                            <tbody data-repeater-list="transaction">
                                <tr data-repeater-item style="text-align:center;">
                                    <td>
                                        <div class="typeahead">
                                            <?php  
                                                $data = array('type' => 'text', 'class' => 'form-control transaction_input', 'placeholder' => 'No. Transaksi/Ref...', 'required' => 'true'); 
                                                echo form_input($data);
                                                ?>
                                        </div>
                                    </td>
                                    <?php 
                                        $data = array('type' => 'hidden', 'class' => 'form-control transaction_id', 'name' => 'transaction_id', 'value' => set_value('transaction_id'), 'required' => 'true'); 
                                        echo form_input($data);
                                        echo form_error('transaction_id', '<p class="text-danger">', '</p>');
                                        ?>
                                    <td>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control transaction_date', 'readonly' => 'true'); 
                                            echo form_input($data);
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control transaction_invoice', 'readonly' => 'true'); 
                                            echo form_input($data);
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control text-right transaction_grandtotal', 'readonly' => 'true'); 
                                            echo form_input($data);
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control text-right transaction_account_payable', 'readonly' => 'true'); 
                                            echo form_input($data);
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control text-right amount transaction_pay', 'name' => 'transaction_pay', 'placeholder' => '0',  'value' => set_value('transaction_pay'), 'required' => true); 
                                            echo form_input($data);                                             
                                            echo form_error('transaction_pay', '<p class="text-danger">', '</p>');
                                            ?>
                                    </td>
                                    <td>
                                        <label data-repeater-delete class="col-form-label text-danger text-center delete_product"><i class="fa fa-times"></i></label>
                                    </td>
                                </tr>                                
                            </tbody>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-right"><label class="col-form-label">BIAYA LAINNYA</label></td>
                                    <td>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control text-right amount', 'id'=> 'cost', 'name' => 'cost', 'placeholder' => '0',  'value' => set_value('cost')); 
                                            echo form_input($data);                                             
                                            echo form_error('cost', '<p class="text-danger">', '</p>');
                                        ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-sm btn-primary btn-block" id="add_transacton" data-repeater-create><i class="fa fa-plus"></i> Tambah Baris Transaksi</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Daftar Pembayaran
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-sm" id="payment_table">
                            <thead>
                                <tr style="text-align:center;">
                                    <th width="200px"><span class="text-danger">*</span>METODE</th>
                                    <th><span class="text-danger">*</span>AKUN</th>
                                    <th width="200px">NO. CEK/GIRO</th>
                                    <th width="200px"><span class="text-danger">*</span>TGL. BUKA CEK/GIRO</th>
                                    <th width="200px"><span class="text-danger">*</span>TGL. CAIR CEK/GIRO</th>
                                    <th><span class="text-danger">*</span>TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select class="form-control payment_method" id="payment_method" name="payment_method[]" required>
                                            <option value="1">KAS</option>
                                            <option value="2">TRANSFER</option>
                                            <option value="3">CEK/GIRO</option>
                                            <option value="4">DEPOSIT</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control account_id" id="account_id" name="account_id[]" required></select>
                                    </td>
                                    <td>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control uppercase cheque_number', 'id' => 'cheque_number', 'name' => 'cheque_number[]', 'readonly' => true, 'required' => false); 
                                            echo form_input($data);
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control date cheque_open_date', 'id' => 'cheque_open_date', 'name' => 'cheque_open_date[]', 'readonly' => true, 'required' => false); 
                                            echo form_input($data);
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control date cheque_number', 'id' => 'cheque_number', 'name' => 'cheque_close_date[]', 'readonly' => true, 'required' => false); 
                                            echo form_input($data);
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control text-right amount payment_pay', 'id' => 'payment_pay', 'name' => 'payment_pay[]', 'placeholder' => '0',  'value' => 0, 'required' => true); 
                                            echo form_input($data);
                                        ?>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-sm btn-primary btn-block" id="add_payment"><i class="fa fa-plus"></i> Tambah Baris Pembayaran</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet">
            <div class="kt-portlet__body">
                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="text-dark font-weight-bold">TOTAL TRANSAKSI</label>
                        <?php
                            $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_transaction_pay', 'value' => 0, 'required' => 'true', 'readonly' => 'true'); 
                            echo form_input($data);
                        ?>
                    </div>
                    <div class="col-md-6">
                        <label class="text-dark font-weight-bold">TOTAL PEMBAYARAN</label>
                        <?php
                            $data = array('type' => 'text', 'class' => 'form-control text-right', 'id' => 'total_payment_pay', 'value' => 0, 'required' => 'true', 'readonly' => 'true');
                            echo form_input($data);
                        ?>
                    </div>
                    <br>
                    <div class="col-md-12 text-center">
                        <p id="notify" class="text-danger font-weight-bold"></p>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="<?php echo urldecode($this->agent->referrer()); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control" id="btn-save"><i class="fa fa-save"></i> SIMPAN</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php echo form_close() ?>
    </div>
    <?php endif; ?>    
</div>