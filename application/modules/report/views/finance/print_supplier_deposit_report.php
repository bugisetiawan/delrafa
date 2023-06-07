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
        <table style="width: 100%;" border="0">
            <thead>
                <tr>
                    <th style="border-bottom: 1px solid black;">Tanggal</th>
                    <th style="border-bottom: 1px solid black;">No. Transaksi</th>
                    <th colspan="2" style="border-bottom: 1px solid black;">Keterangan</th>
                    <th  class="text-center" style="border-bottom: 1px solid black;">Mutasi</th>
                    <th class="text-center" style="border-bottom: 1px solid black;">Saldo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($cash_ledger AS $info): ?>                
                    <tr>                                                
                        <td class="text-center"><?php echo date('d-m-Y', strtotime($info['date'])); ?></td>
                        <td><?php echo $info['invoice']; ?></td>
                        <td><?php echo $info['information']; ?></td>
                        <td><?php echo $info['note']; ?></td>
                        <?php $method = ($info['method'] == 1) ? '<span class="text-success">DB</span>' : '<span class="text-danger">KR</span>'; ?>
                        <td class="text-right"><?php  echo number_format($info['amount'],2,'.', ','); ?>&nbsp; <?php echo $method; ?></td>
                        <td class="text-right"><?php  echo number_format($info['balance'],2,'.', ','); ?>&nbsp;</td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>            
    </body>
    <!-- end::Body -->
</html>