<!DOCTYPE html>
<html lang="en">
    <!-- begin::Head -->
    <head>
        <meta charset="utf-8" />
        <title>Summary</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="shortcut icon" href="./assets/media/logos/favicon.png" />
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
                                                <th colspan="3" class="text-right">REKAP KASIR</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">                                    
                                    <table >
                                        <tbody>       
                                            <tr>
                                                <td colspan="6" style="border-bottom: 0.5px solid black;">INFORMASI</td>
                                            </tr>      
                                            <tr>
                                                <td>Kasir</td>
                                                <td class="text-center">:</td>
                                                <td><?php echo strtoupper($cashier['name_c']); ?></td>
                                                <td class="text-right">Jam Buka</td>
                                                <td class="text-center">:</td>
                                                <td><?php echo $cashier['open_time']; ?></td>
                                            </tr>   
                                            <tr>
                                                <td>Tanggal</td>
                                                <td>:</td>
                                                <td><?php echo date('d-m-Y', strtotime($cashier['date'])); ?></td>
                                                <td class="text-right">Jam Tutup</td>
                                                <td class="text-center">:</td>
                                                <td><?php echo $cashier['close_time']; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <hr><br>
                            <div class="row">
                                <div class="col-md-12">                                    
                                    <table style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th colspan="2" class="text-left">PEMASUKAN</th>
                                            </tr>
                                        </thead>
                                        <tbody>                                
                                            <tr style="border-bottom:1px solid black;">
                                                <td>MODAL KASIR</td>                                                
                                                <td class="text-right"><?php echo number_format($cashier['modal'], 0, ".", ","); ?></td>
                                            </tr>
                                            <tr style="border-bottom:1px solid black;">
                                                <td>UANG MUKA</td>                                                
                                                <td class="text-right"><?php echo number_format($cashier['total_dp'], 0, ".", ","); ?></td>
                                            </tr>
                                            <tr style="border-bottom:1px solid black;">
                                                <td>PENJUALAN</td>                                                
                                                <td class="text-right"><?php echo number_format($cashier['total_sales'],0, ".", ","); ?></td>
                                            </tr>
                                            <tr style="border-bottom:1px solid black;">
                                                <td>POS</td>                                                
                                                <td class="text-right"><?php echo number_format($cashier['total_pos'],0, ".", ","); ?></td>
                                            </tr>                                
                                            <tr>
                                                <?php $in = $cashier['modal']+$cashier['total_dp']+$cashier['total_sales']+$cashier['total_pos'];   ?>
                                                <td colspan="2" class="text-right"><?php echo number_format($in, 0, ".", ","); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <table style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th colspan="2" class="text-left">PENGELUARAN</th>
                                            </tr>
                                        </thead>
                                        <tbody>                                
                                            <tr style="border-bottom:1px solid black;">
                                                <td class="">RETUR PENJUALAN</td>                                                
                                                <td class="text-right"><?php echo number_format($cashier['total_sales_return'], 0, ".", ","); ?></td>
                                            </tr>
                                            <tr style="border-bottom:1px solid black;">
                                                <td class="">BIAYA</td>                                                
                                                <td class="text-right"><?php echo number_format($cashier['total_expense'], 0, ".", ","); ?></td>
                                            </tr>
                                            <tr style="border-bottom:1px solid black;">
                                                <td class="">COLLECT</td>
                                                <td class="text-right"><?php echo number_format($cashier['total_collect'],0, ".", ","); ?></td>
                                            </tr>                                                               
                                            <tr>                                                
                                                <?php $out = $cashier['total_sales_return']+$cashier['total_expense']+$cashier['total_collect'];   ?>
                                                <td colspan="2" class="text-right"><?php echo number_format($out, 0, ".", ","); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <table style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th class="text-right">TOTAL SETORAN</th>
                                                <th><h4 class="text-right"><?php echo number_format($cashier['grandtotal'],0, ".", ","); ?></h4>   </th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <hr><br>
                            <div class="row">
                                <div class="col-md-6 text-center">
                                    <h4>KASIR</h4>
                                    <br>
                                    <br>
                                    <br>
                                    <p>( <?php echo $cashier['name_c']; ?> )</p>
                                </div>
                                <div class="col-md-6 text-center">
                                    <h4>PEREKAP</h4>
                                    <br>
                                    <br>
                                    <br>
                                    <p>(______________________)</p>
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