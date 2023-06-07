<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content -->        
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
        <?php if($this->session->flashdata('success')) :?>
        <div class="alert alert-success fade show" role="alert">
            <div class="alert-icon"><i class="flaticon2-checkmark"></i></div>
            <div class="alert-text"><?php echo $this->session->flashdata('success'); ?></div>
            
        </div>
        <?php elseif($this->session->flashdata('error')): ?>
        <div class="alert alert-danger fade show" role="alert">
            <div class="alert-icon"><i class="flaticon-warning"></i></div>
            <div class="alert-text"><?php echo $this->session->flashdata('error'); ?></div>
        </div>
        <?php endif;?>        
        <div class="kt-portlet">
            <div class="kt-portlet__body">  
                <div class="form-group row">
                    <div class="col-md-9">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <td>NO. INVOICE</td>
                                    <td class="text-center">:</td>
                                    <td class="kt-font-primary kt-font-bold"><?php echo $pos['invoice']; ?></td>
                                    <td class="text-right">TANGGAL</td>
                                    <td class="text-center">:</td>
                                    <td><?php echo date('d-m-Y', strtotime($pos['date'])); ?></td>
                                    <td class="text-right">JAM</td>
                                    <td class="text-center">:</td>
                                    <td><?php echo date('H:i:s', strtotime($pos['time'])); ?></td>
                                </tr>
                                <tr>
                                    <td>CUSTOMER</td>
                                    <td class="text-center">:</td>
                                    <td><?php echo $pos['name_cust']; ?></td>
                                    <td class="text-right">TOTAL PRODUK</td>
                                    <td class="text-center">:</td>
                                    <td><?php echo $pos['total_product']; ?></td>
                                    <td class="text-right">PEMBAYARAN</td>
                                    <td class="text-center">:</td>
                                    <?php
                                    if($pos['payment'] == 0)
                                    {
                                        $pembayaran = "TUNAI";
                                    }
                                    else if($pos['payment'] == 1)
                                    {
                                        $pembayaran = "DEBIT";
                                    }
                                    else
                                    {
                                        $pembayaran = "KREDIT";
                                    }
                                    ?>
                                    <td><?php echo $pembayaran; ?></td>
                                </tr>                                 
                                <tr>
                                    <td>GRANDTOTAL</td>
                                    <td class="text-center">:</td>
                                    <td class="text-primary kt-font-bold"><?php echo number_format($pos['grandtotal'],0,'.',','); ?></td>
                                    <td class="text-right">BAYAR</td>
                                    <td class="text-center">:</td>
                                    <td class="text-success kt-font-bold"><?php echo number_format($pos['pay'],0,'.',',');  ?></td>
                                    <td class="text-right">KEMBALI</td>
                                    <td class="text-center">:</td>
                                    <td class="text-danger kt-font-bold"><?php echo number_format($pos['pay']-$pos['grandtotal'],0,'.',',');  ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-square btn-block" onclick="print_bill('<?= $this->global->encrypt($pos['id_p']); ?>')">PRINT NOTA</button>
                        <!-- <button type="button" class="btn btn-sm btn-outline-info btn-square btn-block" onclick="print_order('<?= $this->global->encrypt($pos['id_p']); ?>')">PRINT ORDER</button> -->
                        <a href="<?php echo site_url('pos/cashier'); ?>" class="btn btn-sm btn-outline-success btn-square btn-block">TRANSAKSI BARU</a>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <!--begin: Datatable -->
                        <table class="table table-sm table-bordered table-hover" id="product_table">                                        
                            <thead>
                                <tr style="text-align:center;">
                                    <th class="kt-font-dark" width="150">KODE</th>
                                    <th class="kt-font-dark">NAMA</th>
                                    <th class="kt-font-dark">QTY</th>
                                    <th class="kt-font-dark">SATUAN</th>
                                    <th class="kt-font-dark" width="100">HARGA</th>											
                                    <th class="kt-font-dark" width="100">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($pos_detail AS $info): ?>
                                <tr>
                                    <td class="kt-font-dark text-center"><?php echo $info['code_p']; ?></td>
                                    <td class="kt-font-dark"><?php echo $info['name_p']; ?></td>
                                    <td class="kt-font-dark text-right"><?php echo $info['qty']; ?></td>
                                    <td class="kt-font-dark text-left"><?php echo $info['name_u']; ?></td>
                                    <td class="kt-font-dark text-right"><?php echo number_format($info['price'],0,'.',','); ?></td>
                                    <td class="kt-font-dark text-right"><?php echo number_format($info['total'],0,'.',','); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <!--end: Datatable -->
                    </div>
                </div>             
            </div>            
        </div>          
    </div>
    <!-- end:: Content -->        
</div>