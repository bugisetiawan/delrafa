<?php if($date != date('Y-m-d')) :?>
<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content -->    
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">                
        <div class="container">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title kt-font-danger kt-font-bold">
                            MOHON PERHATIAN!
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <div class="alert alert-danger fade show" role="alert">
                        <div class="alert-icon"><i class="flaticon-warning"></i></div>
                        <div class="alert-text">                        
                        Mohon maaf, anda tidak dapat melakukan pembukaan kasir. <br> Masih ada kasir yang belum di rekap pada tanggal <b><?php echo date('d-m-Y', strtotime($date)); ?></b>
                        <br>
                        Silahkan lakukan rekap kasir terlebih dahulu untuk melakukan pembukaan kasir baru. Terima Kasih
                        </div>							
                    </div>
                    <div class="row">
                        <div class="col-md-12">                            								
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <a href="<?php echo base_url('dashboard'); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-arrow-left"></i>KEMBALI</a>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control" id="close_cashier_automatic_btn"><i class="fa fa-save"></i> REKAP KASIR</button>
                                </div>                                                                
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
    <!--begin::Close Modal-->
    <div class="modal fade" id="close_modal" tabindex="-1" role="dialog" aria-labelledby="closeModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <?php echo form_open('pos/Cashier/close', array('class' => 'form-horizontal', 'id' => 'close_form', 'autocomplete' => 'off')); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Penutupan Kasir</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="close_code_e" id="close_code_e">
                    <div class="form-group">
                        <p class="text-dark">
                            Apakah anda yakin ingin menutup kasir? 
                        </p>
                    </div>
                </div>
                <div class="modal-footer">																					
                    <button type="button" class="btn btn-danger form-control" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
                    <button type="submit" class="btn btn-success form-control" onclick="this.disabled=true; $('#close_form').submit();"><i class="fa fa-save"></i>  TUTUP KASIR</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
    <!--end::End Close Modal-->
</div>
<?php else: ?>
<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
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
        <?php echo form_open_multipart('', ['class' => 'repeater', 'autocomplete' => 'off', "onkeydown"=>"return event.key != 'Enter';"]); ?>        
        <div class="kt-portlet">
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-md-10">
                        <div class="row">       
                            <label class="col-md-1 col-form-label text-dark kt-font-bold">PRODUK<b class="text-primary">(F2)</b></label>
                            <div class="col-md-5">
                                <div class="typeahead">                            
                                    <input type="text" id="search_product" class="form-control" placeholder="Scan barcode atau ketik nama produk untuk melakukan pencarian..." autofocus="true">
                                </div>
                                <small class="kt-font-bold text-primary">*Tekan <b>ENTER</b> untuk menambahkan produk yang dipilih</small>
                            </div>
                            <div class="col-md-6">
                                <select class="form-control" name="customer_code" id="customer_code" required>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-sm table-hover table-bordered" id="product_table">
                                    <thead class="text-center">
                                        <tr>                                    
                                            <th class="text-dark" width="100">KODE</th>
                                            <th class="text-dark">NAMA</th>
                                            <th class="text-dark" width="80">QTY</th>
                                            <th class="text-dark" width="100">SATUAN</th>
                                            <th class="text-dark" width="120">HARGA</th>
                                            <th class="text-dark" width="120">DISKON (%)</th>
                                            <th class="text-dark" width="120">SUBTOTAL</th>
                                            <th class="text-dark text-center" width="10">HAPUS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>     
                    <div class="col-md-2">      
                        <div class="form-group">
                            <div>
                                <img class="img-fluid" id="product_photo" src="<?php echo base_url('assets/media/system/products/nophoto.png'); ?>" alt="Foto Produk" style="height:150px; width:100%;">
                            </div>
                        </div>
                        <div class="form-group">
                            <table class="table table-hover">
                                <tbody>
                                    <tr>                                        
                                        <td>TOTAL PRODUK</td>
                                        <input type="hidden" name="total_qty" id="total_qty" value="0" required>
                                        <td><span class="kt-font-bold" id="view_total_product">0</span></td>
                                    </tr>
                                    <tr>
                                        <td>GRANDTOTAL</td>
                                        <input type="hidden" name="grandtotal" id="grandtotal" value="0" required>
                                        <td><span class="kt-font-bold text-primary" id="view_grandtotal">0</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-outline-brand btn-square btn-block" id="payment_btn" data-toggle="modal" data-target="#payment_modal" disabled="true">BAYAR<b class="text-primary">(END)</b></button>
                        </div>
                    </div>               
                </div>                
            </div>
        </div>
        <!--begin::Payment Modal-->
        <div class="modal fade" id="payment_modal" tabindex="-1" role="dialog" aria-labelledby="paymentModal" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pembayaran</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="text-dark">CARA PEMBAYARAN</label>
                            <select class="form-control" name="payment" id="payment" onchange="functionselect()">
                                <option value="0">TUNAI</option>
                                <option value="1">KARTU DEBIT</option>
                                <option value="2">KARTU KREDIT</option>
                                <option value="3">QRIS</option>
                            </select>
                        </div>
                        <div class="form-group row" id="bank_form" style="display:none;">
                            <div class="col-md-6">
                                <label class="text-dark">BANK</label>
                                <select class="form-control" id="bank_id" name="bank_id">
                                    <option>-- Pilih BANK --</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="text-dark">NO. KARTU DEBIT/KREDIT</label>
                                <input type="text" class="form-control form-control-lg" id="card_number" name="card_number" placeholder="Silahkan isi nomor kartu">
                            </div>
                            <div class="col-md-6">
                                <label class="text-dark">NAMA PEMEGANG KARTU</label>
                                <input type="text" class="form-control form-control-lg" id="card_holder" name="card_holder" placeholder="Silahkan isi nama pemegang kartu">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label class="text-dark">BAYAR</label>
                                <input type="text" class="form-control form-control-lg" placeholder="Jumlah Bayar" name="pay" id="pay">
                            </div>
                            <div class="col-md-6">
                                <label class="text-dark">KEMBALIAN</label>
                                <input type="text" class="form-control form-control-lg" id="kembalian" value="0" readonly>
                            </div>
                        </div>                        
                        <div class="form-group text-center">                            
                            <button type="button" class="btn btn-outline-primary btn-sm btn-wide btn-money" id="money_5000"   value="5000">5.000</button>
                            <button type="button" class="btn btn-outline-primary btn-sm btn-wide btn-money" id="money_10000"  value="10000">10.000</button>
                            <button type="button" class="btn btn-outline-primary btn-sm btn-wide btn-money" id="money_20000"  value="20000">20.000</button>
                            <button type="button" class="btn btn-outline-primary btn-sm btn-wide btn-money" id="money_50000"  value="50000">50.000</button>
                            <button type="button" class="btn btn-outline-primary btn-sm btn-wide btn-money" id="money_100000" value="100000">100.000</button>
                            <button type="button" class="btn btn-outline-success btn-sm btn-wide btn-money" id="custom-money"></button>
                            <button type="button" class="btn btn-outline-danger btn-sm btn-wide" id="delete-money">HAPUS</button>
                        </div>
                        <div class="form-group" id="payment_loading">
						    <button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary btn-block">Loading...</button>
						</div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success form-control" id="btn-save" disabled><i class="fa fa-save"></i>  SIMPAN</button>
                    </div>
                </div>
            </div>
        </div>    
        <!--end::Payment Modal-->   
        <?php echo form_close(); ?>
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
                        <input type="password" name="verifypassword" class="form-control verifypassword" placeholder="Silahkan isi Password untuk melanjutkan..." autocomplete="false">
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
    <!--begin::Discount_p Modal-->
    <div class="modal fade" id="discount_p_modal" tabindex="-1" role="dialog" aria-labelledby="closeModal" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <?php echo form_open('', array('class' => 'form-horizontal', 'id' => 'discount_p_form', 'autocomplete' => 'off')); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Verifikasi Password</h5>                    
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Silahkan masukan password untuk melanjutkan...</label>                        
                        <input type="password" name="verifypassword" class="form-control verifypassword" placeholder="Silahkan isi password untuk melakukan verifikasi..." autocomplete="false">
                    </div>
                </div>
                <div class="modal-footer">																					
                    <button type="button" class="btn btn-danger form-control" id="cancel_discount_p_modal" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
                    <button type="submit" class="btn btn-success form-control"><i class="fa fa-save"></i>  Verifikasi</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
    <!--end::End Discount_p Modal-->
    <!--begin::Add Customer Modal-->
    <div class="modal fade" id="add_customer_modal" tabindex="-1" role="dialog" aria-labelledby="collectModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <?php echo form_open('', array('id' => 'add_customer_form', 'autocomplete' => 'off')); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Pelanggan Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-2"><label class="col-form-label text-dark"><span class="text-danger">*</span>Nama</label></div>
                                <div class="col-md-10"><input type="text" class="form-control" id="name_customer" name="name_customer" placeholder="Silahkan isi nama pelanggan..." required></div>
                            </div>                                                        
                        </div>                        
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-md-3 col-form-label text-dark"><b><span class="text-danger">*</span>Harga</b> Jual</label>
                                <div class="col-md-9">
                                    <div class="kt-radio-inline">	
                                        <?php for($i=1;$i<=5;$i++): ?>
                                            <label class="kt-radio kt-radio--bold kt-radio--brand">
                                            <input type="radio" id="price_class" name="price_class" value="<?php echo $i; ?>"
                                            <?php if($i == "1"){ echo "required checked"; } ?>><?php echo $i; ?>
                                            <span></span>
                                        </label>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>                          
                        </div>
                    </div>                    
                </div>
                <div class="modal-footer">																					
                    <button type="button" class="btn btn-danger form-control" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
                    <button type="submit" class="btn btn-success form-control" id="btn-add-customer" onclick="this.disabled=true; $('#add_customer_form').submit();" disabled><i class="fa fa-save"></i>  SIMPAN</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
    <!--end::End Add Customer Modal-->
    <!--begin::Collect Modal-->
    <div class="modal fade" id="collect_modal" tabindex="-1" role="dialog" aria-labelledby="collectModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <?php echo form_open('', array('id' => 'collect_form', 'autocomplete' => 'off')); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Collect Kasir</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="text-dark">Collector:</label>
                        <select class="form-control" id="employee_code" name="employee_code" required>
                            <option>-- Pilih Pegawai --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="text-dark">Jumlah Uang:</label>
                        <input type="text" class="form-control" id="collect_amount" name="collect_amount" required>
                    </div>
                </div>
                <div class="modal-footer">																					
                    <button type="button" class="btn btn-danger form-control" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
                    <button type="submit" class="btn btn-success form-control" id="btn-collect" onclick="this.disabled=true; $('#collect_form').submit();" disabled><i class="fa fa-save"></i>  COLLECT</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
    <!--end::End Collect Modal-->
    <!--begin::Close Modal-->
    <div class="modal fade" id="close_modal" tabindex="-1" role="dialog" aria-labelledby="closeModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <?php echo form_open('pos/Cashier/summary', array('class' => 'form-horizontal', 'id' => 'close_form', 'autocomplete' => 'off')); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Penutupan Kasir</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="close_code_e" id="close_code_e">
                    <div class="form-group">
                        <p class="text-dark">
                            Apakah anda yakin ingin menutup kasir? 
                        </p>
                    </div>
                </div>
                <div class="modal-footer">																					
                    <button type="button" class="btn btn-danger form-control" data-dismiss="modal"><i class="fa fa-times"></i> BATAL</button>
                    <button type="submit" class="btn btn-success form-control" onclick="this.disabled=true; $('#close_form').submit();"><i class="fa fa-save"></i>  TUTUP KASIR</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
    <!--end::End Close Modal-->
</div>
<?php endif; ?>