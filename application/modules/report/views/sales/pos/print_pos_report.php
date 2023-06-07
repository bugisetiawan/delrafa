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
        <table style="width: 100%;" celpadding="1" cellspacing="1">
            <thead>
                <tr>
                    <th style="border-bottom: 1px solid black;">No.</th>
                    <th style="border-bottom: 1px solid black;">Tgl.Penjualan</th>
                    <th style="border-bottom: 1px solid black;">Jam</th>
                    <th style="border-bottom: 1px solid black;">No. Transaksi</th>
                    <th style="border-bottom: 1px solid black;">Pelanggan</th>
                    <th style="border-bottom: 1px solid black;">PPN</th>
                    <th style="border-bottom: 1px solid black;">Total</th>
                </tr>
            </thead>
            <tbody>        
                <?php $no=1; $total_grandtotal=0; foreach($data As $pos): ?>
                    <tr>
                        <td class="text-right"><?php echo $no.'.'; ?></td>
                        <td class="text-center"><?php echo date('d-m-Y', strtotime($pos['date'])); ?></td>
                        <td class="text-center"><?php echo date('H:i:s', strtotime($pos['time'])); ?></td>
                        <td class="text-center"><?php  echo $pos['invoice']; ?></td>
                        <td><?php echo $pos['name_c']; ?></td>
                        <?php 
                        switch ($pos['ppn']){
                            case "0":
                                $ppn = 'NON';
                              break;
                            case "1":
                                $ppn = 'PPN';
                              break;
                            default:
                                $ppn = '-';
                        }
                        ?>
                        <td class="text-center"><?php  echo $ppn; ?></td>
                        <td class="text-right"><?php  echo number_format($pos['grandtotal'],2,'.', ','); ?>&nbsp;</td>
                        <?php $total_grandtotal = $total_grandtotal + $pos['grandtotal']; ?>
                    </tr>
                <?php $no++; endforeach; ?>
                    <tr>
                        <td style="border-top: 1px solid black;" colspan="6" class="text-right">TOTAL KESELURUHAN &nbsp;</td>
                        <td style="border-top: 1px solid black;" class="text-right"><?php  echo number_format($total_grandtotal,2,'.', ','); ?>&nbsp;</td>                        
                    </tr>
            </tbody>
        </table>            
    </body>
    <!-- end::Body -->
</html>