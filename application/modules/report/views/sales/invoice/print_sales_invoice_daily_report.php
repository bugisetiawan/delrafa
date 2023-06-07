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
                    <th width="25%">Tanggal Penjualan</th>
                    <th width="25%">Jmlh. Transaksi</th>
                    <th class="text-right">Total Penjualan</th>
                    <th class="text-right">Total Piutang</th>
                </tr>
            </thead>
            <tbody>
            <?php $total_grandtotal = 0; $total_account_payable=0; foreach($data AS $sales_invoice): ?>                                                                
                <tr>
                    <td class="text-center"><?php echo date('d-m-Y', strtotime($sales_invoice['date'])); ?></td>
                    <td class="text-center"><?php echo number_format($sales_invoice['count_sales_invoice'], 0, '.', ','); ?></td>
                    <td class="text-right"><?php echo number_format($sales_invoice['total_sales_invoice'], 2, '.', ','); ?></td>
                    <td class="text-right"><?php echo number_format($sales_invoice['total_account_payable'], 2, '.', ','); ?></td>
                    <?php 
                        $total_grandtotal = $total_grandtotal + $sales_invoice['total_sales_invoice'];
                        $total_account_payable = $total_account_payable + $sales_invoice['total_account_payable'];
                    ?>
                </tr>
            <?php endforeach; ?>
                <tr><td>&nbsp;</td></tr>
                <tr>
                    <td style="border-top: 1px solid black;" colspan="2" class="text-right">TOTAL KESELURUHAN</td>
                    <td style="border-top: 1px solid black;" class="text-right"><?php echo number_format($total_grandtotal, 2, '.', ','); ?></td>
                    <td style="border-top: 1px solid black;" class="text-right"><?php echo number_format($total_account_payable, 2, '.', ','); ?></td>
                </tr>                                    
            </tbody>
        </table>                       
    </body>
    <!-- end::Body -->
</html>