<!DOCTYPE html>
<html lang="en">
    <!-- begin::Head -->
    <head>
        <base href="<?php echo base_url('/'); ?>">
        <meta charset="utf-8" />
        <title>TRUST System | Cetak Daftar Harga Produk</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link href="./assets/css/printout.min.css" rel="stylesheet" type="text/css" />
        <link href="./assets/vendors/general/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
        <style>
            /* Styles go here */
            .page-header, .page-header-space {
            height: 50px;
            }
            .page-footer, .page-footer-space {
            height: 20px;
            }
            .page-header {
            position: fixed;
            top: 0mm;
            width: 100%;
            border-bottom: 1px solid black;
            }
            .page-footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            border-top: 1px solid black;
            }            
            .content {
            margin-bottom:10px;
            }
            @media print {
            thead {display: table-header-group;} 
            tfoot {display: table-footer-group;}
            button {display: none;}
            html, body{
            height : auto;
            width  : auto;
            margin : 0; 
            padding: 0;
            font-family: "Calibri";
            font-size: 14px;
            }
            }
        </style>
    </head>
    <!-- end::Head -->
    <!-- begin::Body -->
    <body>
        <div class="page-header">
            <table style="width:100%;">
                <thead class="text-center">
                    <tr>
                        <th>DAFTAR HARGA PRODUK</th>
                    </tr>
                    <tr>
                        <th>Per Tanggal: <?php echo date('d-m-Y'); ?> | Jam: <?php echo date('H:i:s'); ?></th>
                    </tr>
                </thead>
            </table>
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
                        <div class="page">
                            <table style="width: 100%;" border="1" cellspacing="1">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th width="10">NO.</th>
                                        <th>NAMA</th>
                                        <th colspan="2" width="100" class="text-center">STOK</th>
                                        <th>HRG. 1</th>
                                        <?php $coloumn=1; foreach($name AS $info): ?>
                                            <?php if($coloumn <= 3): ?>
                                                <?php $coloumn++; continue; ?>
                                            <?php else: ?>
                                                <?php if($info == 6): ?>
                                                <th>HRG. BELI</th>
                                                <?php elseif($info == 7): ?>
                                                <th>HPP</th>
                                                <?php else: ?>
                                                <th>HRG. <?php echo $info; ?></th>
                                                <?php endif; ?>
                                            <?php endif; ?>                            
                                        <?php endforeach; ?>                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $nomor_produk=1; foreach($price_list AS $info): ?>
                                    <tr>
                                        <td class="text-right"><?php echo $nomor_produk.'.'; ?></td>
                                        <td><?php echo $info['name'] ?></td>
                                        <td width="50" class="text-right"><?php echo number_format($info['qty'],2,".",",");; ?></td>
                                        <td width="50" class="text-left"><?php echo $info['name_u']; ?></td>                                        
                                        <td style="text-align:right;"><?php echo number_format($info['price_1'],0,".",","); ?></td>
                                        <?php $coloumn=1; foreach($name AS $info2): ?>
                                            <?php if($coloumn <= 3): ?>
                                                <?php $coloumn++; continue; ?>
                                            <?php else: ?>
                                                <?php if($info2 == 6): ?>
                                                <?php $price = "buyprice"; ?>
                                                <td style="text-align:right;"><?php echo number_format($info[$price],0,".",","); ?></td>
                                                <?php elseif($info2 == 7): ?>
                                                <?php $price = "hpp"; ?>
                                                <td style="text-align:right;"><?php echo number_format($info[$price],0,".",","); ?></td>                                                
                                                <?php else: ?>
                                                <?php $price = "price_".$info2; ?>
                                                <td style="text-align:right;"><?php echo number_format($info[$price],0,".",","); ?></td>
                                                <?php endif; ?>
                                            <?php endif; ?>                            
                                        <?php endforeach; ?>                            
                                    </tr>
                                    <?php $nomor_produk++; endforeach; ?>
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