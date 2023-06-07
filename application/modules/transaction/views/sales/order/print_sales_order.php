<!DOCTYPE html>
<html lang="en">
    <!-- begin::Head -->
    <head>
        <meta charset="utf-8" />
        <title>Pemesanan Penjualan | <?php echo $sales_order['invoice']; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <base href="<?php echo base_url('/'); ?>">
        <link href="./assets/css/printout.min.css" rel="stylesheet" type="text/css" />
        <link href="./assets/vendors/general/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
        <style>
            /* Styles go here */
            .page-header, .page-header-space {
                height: 100px;
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
            border-bottom: 1px solid black; /* for demo */
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
                    font-family: "Times New Roman", Times, serif;				
                    page-break-after: always;
                }                
            }
        </style>
    </head>
    <!-- end::Head -->
    <!-- begin::Body -->
    <body>
        <div class="page-footer">
            <small>Waktu Cetak: <?php echo date('d-m-Y H:i:s'); ?> | Opt. <?php echo $this->session->userdata('name_e'); ?></small>
        </div>
        <table>            
            <tbody>
                <tr>
                    <td>
                        <!--*** CONTENT GOES HERE ***-->
                        <div class="page">
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
                                                <th colspan="3" class="text-right">PEMESANAN PENJUALAN | NO. TRANSAKSI: <?php echo $sales_order['invoice']; ?></th>
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
                                                <td colspan="3" style="border-bottom: 0.5px solid black;">PELANGGAN</td>
                                            </tr>      
                                            <tr>
                                                <td>Nama</td>
                                                <td>:</td>
                                                <td><?php echo strtoupper($sales_order['name_c']); ?></td>
                                            </tr>   
                                            <tr>
                                                <td>Alamat</td>
                                                <td>:</td>
                                                <td><?php echo strtoupper($sales_order['address_c']); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Telepon</td>
                                                <td>:</td>
                                                <td><?php echo strtoupper($sales_order['telephone_c']); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-7">
                                    <table >
                                        <tbody> 
                                            <tr>
                                                <td colspan="6" style="border-bottom: 0.5px solid black;">INFORMASI</td>
                                            </tr>   
                                            <tr>
                                                <td>Tgl. Transaksi</td>
                                                <td class="text-center">:</td>
                                                <td><?php echo date('d-m-Y', strtotime($sales_order['date'])); ?></td>
                                                <td class="text-right">Sales</td>
                                                <td class="text-center">:</td>
                                                <td><?php echo $sales_order['name_s']; ?></td>
                                            </tr>   
                                            <tr>
                                                <td>Pengambilan</td>
                                                <td>:</td>
                                                <?php 
                                                    if($sales_order['taking_method'] == 1)
                                                    {
                                                        $taking_method = "Langsung";
                                                    }
                                                    else
                                                    {
                                                        $taking_method = "Pengiriman";
                                                    }
                                                ?>
                                                <td><?php echo $taking_method; ?></td>
                                                <td class="text-right">Operator</td>
                                                <td class="text-center">:</td>
                                                <td><?php echo strtoupper($sales_order['name_e']); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <br>                            
                            <table style="width:100%;" id="product_table" >
                                <thead>
                                    <tr id="table-title">
                                        <th class="text-right">NO.</th>
                                        <th class="text-center">KODE</th>
                                        <th class="text-left">PRODUK</th>
                                        <th class="text-right">QTY</th>
                                        <th class="text-right">DPP</th>
                                        <th class="text-right">PPN</th>
                                        <th class="text-right">HARGA</th>
                                        <th class="text-right">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $total_ppn = 0; ?>
                                    <?php $no=1; foreach($sales_order_detail AS $info): ?>                                        
                                        <tr>
                                            <td class="text-right"><?php echo $no; ?></td>
                                            <td class="text-center"><?php echo $info['code_p']; ?></td>
                                            <td class="text-left"><?php echo $info['name_p']; ?></td>
                                            <td class="text-right"><?php echo $info['qty']; ?> <?php echo $info['code_u']; ?></td>
                                            <?php 

                                                if($info['ppn'] == 1)
                                                {
                                                    $dpp = $info['price'] / 1.1;
                                                    $ppn = $info['price'] - $dpp;
                                                }
                                                else
                                                {
                                                    $dpp = 0;
                                                    $ppn = 0;
                                                }
                                                $total_ppn = $total_ppn + ($info['qty']*$ppn);
                                            ?>
                                            <td class="text-right"><?php echo number_format($dpp,0,".",","); ?></td>
                                            <td class="text-right"><?php echo number_format($ppn,0,".",","); ?></td>
                                            <td class="text-right"><?php echo number_format($info['price'],0,".",","); ?></td>
                                            <td class="text-right"><?php echo number_format($info['subtotal'],0,".",","); ?></td>
                                        </tr>
                                    <?php $no++; endforeach; ?>
                                    <tr style="border-top: 1px solid black;">
                                        <td colspan="3">*PPN: <?php echo number_format($total_ppn,0,".",","); ?></td>
                                        <td colspan="4" class="text-right">SUBTOTAL</td>
                                        <td class="text-right"><?php echo number_format($sales_order['total_price'],0,".",","); ?></td>
                                    </tr>
                                    <tr style="border-top: 1px solid black;">
                                        <td colspan="7" class="text-right">DISKON</td>
                                        <td class="text-right"><?php echo number_format($sales_order['discount_rp'],0,".",","); ?></td>
                                    </tr>                                                                        
                                    <tr style="border-top: 1px solid black;">
                                        <td colspan="3">*Terbilang: <?php echo $this->global->terbilang($sales_order['grandtotal']); ?></td>
                                        <td colspan="4" class="text-right">GRANDTOTAL</td>
                                        <td class="text-right"><?php echo number_format($sales_order['grandtotal'],0,".",","); ?></td>
                                    </tr>
                                </tbody>
                            </table>
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