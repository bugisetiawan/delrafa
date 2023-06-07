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
        <style>
            body{
                font-family: 'Calibri';
            }
            body, #product_table{
                font-size: 12px;
            }
        </style>
    </head>
    <!-- end::Head -->
    <!-- begin::Body -->
    <body>
        <table class="table table-striped" style="width: 100%;" border="0">
            <thead>
                <tr>
                    <th style="border-bottom: 1px solid black;">Tanggal</th>                    
                    <th style="border-bottom: 1px solid black;">Keterangan</th>
                    <th class="text-center" style="border-bottom: 1px solid black;">Debit</th>
                    <th class="text-center" style="border-bottom: 1px solid black;">Kredit</th>
                    <th class="text-center" style="border-bottom: 1px solid black;">Saldo</th>
                </tr>
            </thead>
            <tbody>
                <?php $total_credit=0; $total_debit =0; ?>
                    <tr>                                                
                        <td class="text-center"></td>
                        <td>Saldo Awal</td>       
                        <?php if($last_balance['balance'] >= 0): ?>                 
                        <td class="text-right"><?php  echo number_format(0, 2,'.', ','); ?></td>
                        <td class="text-right"><?php  echo number_format(0, 2,'.', ','); ?></td>
                        <?php elseif($last_balance['balance'] < 0): ?>
                        <td class="text-right"><?php  echo number_format(0, 2,'.', ','); ?></td>
                        <td class="text-right"><?php  echo number_format(0, 2,'.', ','); ?></td>
                        <?php endif; ?>                    
                        <td class="text-right"><?php  echo number_format($last_balance['balance'], 2,'.', ','); ?></td>
                    </tr>
                <?php foreach($cash_ledger AS $info): ?>                
                    <tr>                                                
                        <td class="text-center"><?php echo date('d-m-Y', strtotime($info['date'])); ?></td>
                        <td><?php echo $info['note']; ?></td>       
                        <?php if($info['method'] == 1): ?>   
                        <?php $total_debit=$total_debit+$info['amount']; ?>
                        <td class="text-right"><?php  echo number_format($info['amount'], 2,'.', ','); ?></td>
                        <td class="text-right"><?php  echo number_format(0, 2,'.', ','); ?></td>
                        <?php elseif($info['method'] == 2): ?>
                        <?php $total_credit=$total_credit+$info['amount']; ?>
                        <td class="text-right"><?php  echo number_format(0, 2,'.', ','); ?></td>
                        <td class="text-right"><?php  echo number_format($info['amount'], 2,'.', ','); ?></td>
                        <?php endif; ?>                    
                        <td class="text-right"><?php  echo number_format($info['balance'],2,'.', ','); ?></td>
                    </tr>
                <?php endforeach;?>
                    <tr>                                                
                        <td style="border-top: 1px solid black;" colspan="2"></td>
                        <td style="border-top: 1px solid black;" class="text-right"><?php  echo number_format($total_debit, 2, '.', ','); ?></td>
                        <td style="border-top: 1px solid black;" class="text-right"><?php  echo number_format($total_credit, 2, '.', ','); ?></td>
                        <td style="border-top: 1px solid black;"></td>
                    </tr>
            </tbody>
        </table>            
    </body>
    <!-- end::Body -->
</html>