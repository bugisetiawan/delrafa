<!DOCTYPE html>
<html lang="en">
    <!-- begin::Head -->
    <head>
        <meta charset="utf-8" />
        <title>Faktur Penjualan | <?php echo $sales_invoice['invoice']; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <base href="<?php echo base_url('/'); ?>">
        <link href="./assets/css/printout.min.css" rel="stylesheet" type="text/css" />
        <link href="./assets/vendors/general/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
        <style>
            /* Styles go here */
            .page-header, .page-header-space {
                height: 110px;
            }
            .page-footer, .page-footer-space {
                height: 20px;
            }
            .page-footer {
                position: fixed;
                bottom: 0;
                width: 100%;
                border-top: 1px solid black; /* for demo */
            }
            .page-header {
            position: fixed;
            top: 0mm;
            width: 100%;
            /* border-bottom: 1px solid black; for demo */
            background: yellow; /* for demo */
            }

            .page {
            page-break-after: always;
            }

            @media print {
                thead {display: table-header-group;} 
                tfoot {display: table-footer-group;}
                button {display: none;}
                body {margin: 0;}
                html, body{                
                    height :auto;
                    width  :100%;
                    margin : 0mm !important; 
                    padding: 0mm !important;
                    overflow: hidden;
                    font-family: "Calibri";
                    font-size: 12px;
                    page-break-after: always;
                }                
            }
        </style>
    </head>
    <!-- end::Head -->
    <!-- begin::Body -->
    <body>        
        <div class="page-header">
            <div class="row">
                <div class="col-md-6">                                    
                    <table style="width:100%;">
                        <thead>
                            <tr>
                                <th class="text-left">
                                    <?php echo $perusahaan['name']; ?><br>
                                    <small>Alamat: <?php echo $perusahaan['address']; ?></small><br>
                                    <small>Telepon: <?php echo $perusahaan['telephone']; ?></small>
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="col-md-6">				
                    <table style="width:100%;">
                        <thead>
                            <tr>
                                <th colspan="3" class="text-right">FAKTUR PENJUALAN | NO. TRANSAKSI: <?php echo $sales_invoice['invoice']; ?></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-5">                                    
                    <table >
                        <tbody>
                            <tr>
                                <td>Nama</td>
                                <td>:</td>
                                <td><?php echo strtoupper($sales_invoice['name_c']); ?></td>
                            </tr>   
                            <tr>
                                <td>Alamat</td>
                                <td>:</td>
                                <td><?php echo strtoupper($sales_invoice['address_c']); ?></td>
                            </tr>
                            <tr>
                                <td>Telepon</td>
                                <td>:</td>
                                <td><?php echo strtoupper($sales_invoice['telephone_c']); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-7">
                    <table >
                        <tbody>
                            <tr>
                                <td>Tgl. Transaksi</td>
                                <td class="text-center">:</td>
                                <td><?php echo date('d-m-Y', strtotime($sales_invoice['date'])); ?></td>
                                <td>Sales</td>
                                <td class="text-center">:</td>
                                <td><?php echo $sales_invoice['name_s']; ?></td>
                            </tr>
                            <tr>                                                
                                <td>Pembayaran</td>
                                <td class="text-center">:</td>
                                <?php $sales_invoice['payment'] == 1 ? $payment="CASH" : $payment="KREDIT"; ?>
                                <td><?php echo $payment; ?></td>
                                <td>Operator</td>
                                <td class="text-center">:</td>
                                <td><?php echo strtoupper($sales_invoice['name_e']); ?></td>
                            </tr>
                            <tr>
                                <td>Jatuh Tempo</td>
                                <td class="text-center">:</td>
                                <td><?php echo date('d-m-Y', strtotime($sales_invoice['due_date'])); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="page-footer">
            <small>Waktu Cetak: <?php echo date('d-m-Y H:i:s'); ?> | Opt. <?php echo $this->session->userdata('name_e'); ?></small>
        </div>
        <table>    
            <thead>
                <tr>
                    <td>
                        <!--place holder for the fixed-position header-->
                        <div class="page-header-space"></div>
                    </td>
                </tr>
            </thead>        
            <tbody>
                <tr>
                    <td>
                        <!--*** CONTENT GOES HERE ***-->
                        <div class="page">
                            <table style="width:100%;" id="product_table">
                                <thead>
                                    <tr id="table-title">
                                        <th class="text-right">NO.</th>
                                        <th class="text-center">KODE</th>
                                        <th class="text-left">PRODUK</th>
                                        <th class="text-right">QTY</th>
                                        <th class="text-right">HARGA</th>                                        
                                        <th class="text-right">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $total_dpp = 0; $total_ppn = 0; ?>
                                    <?php $no=1; foreach($sales_invoice_detail AS $info): ?>                                        
                                        <tr>
                                            <td class="text-right"><?php echo $no; ?></td>
                                            <td class="text-center"><?php echo $info['code_p']; ?></td>
                                            <td class="text-left"><?php echo $info['name_p']; ?></td>
                                            <td class="text-right"><?php echo $info['qty']; ?> <?php echo $info['code_u']; ?></td>
                                            <?php 

                                                if($info['ppn'] == 1)
                                                {
                                                    $dpp = ($info['price'] / 1.1)*$info['qty'];
                                                    $ppn = ($info['price'] - ($dpp/$info['qty']))*$info['qty'];
                                                }
                                                else
                                                {
                                                    $dpp = $info['price']*$info['qty'];
                                                    $ppn = 0;
                                                }                      
                                                $total_ppn = $total_ppn + $ppn;
                                                $total_dpp = $total_dpp + $dpp;
                                            ?>
                                            <td class="text-right"><?php echo number_format($info['price'],0,".",","); ?></td>                                            
                                            <td class="text-right"><?php echo number_format($info['total'],0,".",","); ?></td>
                                        </tr>
                                    <?php $no++; endforeach; ?>
                                    <tr style="border-top: 1px solid black;">                                        
                                        <td colspan="5" class="text-right">SUBTOTAL</td>
                                        <td class="text-right"><?php echo number_format($sales_invoice['total_price'],0,".",","); ?></td>
                                    </tr>
                                    <tr style="border-top: 1px solid black;">
                                        <td colspan="5" class="text-right">DISKON</td>
                                        <td class="text-right"><?php echo number_format($sales_invoice['discount_rp'],0,".",","); ?></td>
                                    </tr>
                                    <tr style="border-top: 1px solid black; border-bottom: 1px solid black;">
                                        <td colspan="2">*Terbilang</td>
                                        <td>: <?php echo $this->global->terbilang($sales_invoice['grandtotal']); ?></td>
                                        <td colspan="2" class="text-right">GRANDTOTAL</td>
                                        <td class="text-right"><?php echo number_format($sales_invoice['grandtotal'],0,".",","); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">*PPN</td>
                                        <td>: <?php echo number_format($total_ppn,0,".",","); ?></td>
                                        <td colspan="2" class="text-right">UANG MUKA</td>
                                        <td class="text-right"><?php echo number_format($sales_invoice['down_payment'],0,".",","); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-right">SISA TAGIHAN</td>
                                        <td class="text-right"><?php echo number_format($sales_invoice['grandtotal']-$sales_invoice['down_payment'],0,".",","); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>                            
                            <div class="row">
                                <div class="col-md-6 text-center">
                                    <p>HORMAT KAMI</p>
                                    <br><br>
                                    <p>(___________________________)</p>
                                </div>
                                <div class="col-md-6 text-center">
                                    <p>PENERIMA</p>
                                    <br><br>
                                    <p>(___________________________)</p>
                                </div>
                            </div>                            
                        </div>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td>
                        <!--place holder for the fixed-position footer-->
                        <div class="page-footer-space"></div>
                    </td>
                </tr>
            </tfoot>
        </table>
        <!--begin:: Global Mandatory Vendors -->
        <script src="./assets/vendors/general/jquery/dist/jquery.js" type="text/javascript"></script>               
        <!--begin::Global Theme Bundle(used by all pages) -->
        <script src="./assets/js/demo1/scripts.bundle.js" type="text/javascript"></script>
        <!--end::Global Theme Bundle -->
        <!--begin::Page Scripts(used by this page) -->
        <script>
            $(document).ready(function (){
                window.print();
            });
        </script>
        <!--end::Page Scripts -->        
    </body>
    <!-- end::Body -->
</html>