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
                    <th class="text-right" style="border-bottom: 1px solid black;">No.</th>
                    <th class="text-center" style="border-bottom: 1px solid black;">Tgl. Retur</th>
                    <th class="text-center" style="border-bottom: 1px solid black;">No. Transaksi</th>
                    <th class="text-center" style="border-bottom: 1px solid black;">Supplier</th>
                    <th class="text-center" style="border-bottom: 1px solid black;">Total</th>                    
                </tr>
            </thead>
            <tbody>
                <?php $no=1; $total_return = 0; foreach($data AS $purchase_return): ?>
                <tr>
                    <td class="text-right"><?php echo $no.'.'; ?></td>
                    <td class="text-center"><?php echo date('d-m-Y', strtotime($purchase_return['date'])); ?></td>
                    <td class="text-center"><?php echo $purchase_return['code']; ?></td>
                    <td><?php echo $purchase_return['name_s']; ?></td>
                    <td class="text-right"><?php echo number_format($purchase_return['total_return'], 2, '.', ','); ?></td>
                    <?php $total_return = $total_return + $purchase_return['total_return']; ?>
                </tr>
                <?php $no++; endforeach; ?>
                <tr>
                    <td colspan="4" class="text-right" style="border-top: 1px solid black;">TOTAL KESELURUHAN</td>
                    <td class="text-right" style="border-top: 1px solid black;"><?php echo number_format($total_return, 2, '.', ','); ?></td>
                </tr>
            </tbody>
        </table>            
    </body>
    <!-- end::Body -->
</html>