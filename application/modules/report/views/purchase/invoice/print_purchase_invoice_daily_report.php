<!DOCTYPE html>
<html lang="en">
    <!-- begin::Head -->
    <head>
        <base href="<?php echo base_url('/'); ?>">
        <meta charset="utf-8" />
        <title>TRUST System | <?php echo $title; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">        
        <link rel="shortcut icon" href="./assets/media/logos/favicon.png" />
        <link href="./assets/css/printout.min.css" rel="stylesheet" type="text/css" />
        <link href="./assets/vendors/general/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
    </head>
    <!-- end::Head -->
    <!-- begin::Body -->
    <body>
        <table style="width: 100%;" celpadding="1">
            <thead>  
                <tr>
                    <th width="25%">Tanggal Pembelian</th>
                    <th width="25%">Jmlh. Transaksi</th>
                    <th class="text-right">Total Pembelian</th>                    
                </tr>
            </thead>
            <tbody>
            <?php $total_grandtotal = 0; $total_account_payable=0; foreach($data AS $purchase_invoice): ?>                                                                
                <tr>
                    <td class="text-center"><?php echo date('d-m-Y', strtotime($purchase_invoice['date'])); ?></td>
                    <td class="text-center"><?php echo number_format($purchase_invoice['count_purchase_invoice'], 0, '.', ','); ?></td>
                    <td class="text-right"><?php echo number_format($purchase_invoice['total_purchase_invoice'], 2, '.', ','); ?></td>                    
                    <?php 
                        $total_grandtotal = $total_grandtotal + $purchase_invoice['total_purchase_invoice'];
                    ?>
                </tr>
            <?php endforeach; ?>
                <tr><td>&nbsp;</td></tr>
                <tr>
                    <td style="border-top: 1px solid black;" colspan="2">&nbsp;TOTAL</td>
                    <td style="border-top: 1px solid black;" class="text-right"><?php echo number_format($total_grandtotal, 2, '.', ','); ?></td>
                </tr>                                    
            </tbody>
        </table>                       
    </body>
    <!-- end::Body -->
</html>