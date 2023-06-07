<!DOCTYPE html>
<html>
    <head>
        <base href="<?php echo base_url('/'); ?>">
        <meta charset="utf-8" />
        <title>TRUST System | <?php echo $title; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="shortcut icon" href="./assets/media/logos/favicon.png" />
        <link href="./assets/css/printout.min.css" rel="stylesheet" type="text/css" />
        <link href="./assets/vendors/general/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th colspan="5">LAPORAN PIUTANG GLOBAL PER PELANGGAN</th>
                </tr>
                <tr>                    
                    <th colspan="5">Daftar yang terlampir adalah per Tanggal: <?php echo date('d-m-Y'); ?> | Jam: <?php echo date('H:i:s'); ?></th>
                </tr>
                <tr><th>&nbsp;</th></tr>
                <tr>                
                    <th style="border-bottom: 1px solid black;">NO.</th>
                    <th style="border-bottom: 1px solid black;">PELANGGAN</th>
                    <th style="border-bottom: 1px solid black;">TOTAL</th>
                    <th style="border-bottom: 1px solid black;">TAGIHAN</th> 
                    <th style="border-bottom: 1px solid black;">TAGIHAN (CEK/GIRO)</th>                    
                </tr>
            </thead>
            <tbody>
                <?php $total_grandtotal = 0; $total_account_payable = 0; $total_cheque_payable=0; ?>
                <?php $no=1; foreach($data AS $info): ?>
                <tr>
                    <td><?php echo $no; ?></td>
                    <td><?php echo $info['name_c']; ?></td>
                    <td class="text-right"><?php echo number_format($info['total_grandtotal'], 2, '.', ','); ?></td>
                    <td class="text-right"><?php echo number_format($info['total_account_payable'], 2, '.', ','); ?></td>
                    <td class="text-right"><?php echo number_format($info['total_cheque_payable'], 2, '.', ','); ?></td>
                    <?php 
                        $total_grandtotal = $total_grandtotal + $info['total_grandtotal'];
                        $total_account_payable = $total_account_payable + $info['total_account_payable'];
                        $total_cheque_payable = $total_cheque_payable + $info['total_cheque_payable'];
                    ?>
                </tr>
                <?php $no++; endforeach; ?>
                <tr>
                    <td style="border-top: 1px solid black;" colspan="2" class="text-right">TOTAL</td>
                    <td style="border-top: 1px solid black;" class="text-right"><?php echo number_format($total_grandtotal, 2, '.', ','); ?></td>
                    <td style="border-top: 1px solid black;" class="text-right"><?php echo number_format($total_account_payable, 2, '.', ','); ?></td>
                    <td style="border-top: 1px solid black;" class="text-right"><?php echo number_format($total_cheque_payable, 2, '.', ','); ?></td>
                </tr>
            </tbody>
        </table>
    </body>
    <!--begin:: Global Mandatory Vendors -->
    <script src="./assets/vendors/general/jquery/dist/jquery.js" type="text/javascript"></script>               
    <!--begin::Global Theme Bundle(used by all pages) -->
    <script src="./assets/js/demo1/scripts.bundle.js" type="text/javascript"></script>
    <!--end::Global Theme Bundle -->
    <script>
        $(document).ready(function (){
            window.print();
        });
        // window.onafterprint = function(){
        //     window.close();
        // }
    </script>
</html>