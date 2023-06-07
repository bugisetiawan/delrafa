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
        <table style="width: 100%;" cellpadding="1">
            <tbody>
            <?php $total_return = 0; foreach($data AS $info): ?>                                                                
                <tr>
                    <td width="25%">Tanggal : <b><?php echo date('d-m-Y', strtotime($info['sales_return']['date'])); ?></b></td>                                            
                    <td>No. Transaksi : <b><?php echo $info['sales_return']['code']; ?></b></td>
                </tr>       
                <tr>
                    <td>Pelanggan</b></td> 
                    <td>: <b><?php echo $info['sales_return']['name_c']; ?></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>                      
                <tr>
                    <td colspan="2" style="border-bottom: 1px solid black;">
                        <table style="width: 100%;" cellpadding="1">
                            <thead>
                                <tr>
                                    <td width="10"><u>No.</u></td>
                                    <td width="100"><u>Kode</u></td>
                                    <td><u>Nama</u></td>
                                    <td width="100" colspan="2" class="text-center"><u>Qty</u></td>
                                    <td width="80" class="text-center"><u>Harga</u></td>
                                    <td width="150" class="text-center"><u>Gudang</u></td>
                                    <td width="80" class="text-right"><u>Total</u></td>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $subtotal=0; $no=1; foreach($info['detail_sales_return'] AS $info2): ?>                                                    
                                <tr>
                                    <td valign="top" class="text-right"><?php echo $no; ?>.</td>
                                    <td valign="top"><?php echo $info2['code_p']; ?></td>
                                    <td valign="top"><?php echo $info2['name_p']; ?></td>
                                    <td valign="top" width="50" class="text-right"><?php echo number_format($info2['qty'],2,".",","); ?></td>
                                    <td valign="top" width="50" class="text-left">&nbsp;<?php echo $info2['code_u']; ?></td>
                                    <td valign="top" class="text-right"><?php echo number_format($info2['price'],2,".",","); ?></td>
                                    <td valign="top" class="text-center"><?php echo $info2['name_w']; ?></td>
                                    <td valign="top" class="text-right"><?php echo number_format($info2['total'],2,".",","); ?></td>
                                </tr>
                                <?php $no++; endforeach; ?>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>                                    
                                    <td colspan="7" class="text-right">TOTAL RETUR:</td>
                                    <td class="text-right">&nbsp;<?php echo number_format($info['sales_return']['total_return'],2,".",","); ?></td>
                                    <?php $total_return = $total_return + $info['sales_return']['total_return']; ?>
                                </tr>
                            </tbody>                                            
                        </table> 
                    </td>
                </tr>
            <?php endforeach; ?>                        
            </tbody>            
        </table>    
        <table style="width: 100%;" celpadding="1">
            <tbody>
                <tr>
                    <td class="text-right">TOTAL KESELURUHAN</td>
                    <td class="text-right"><?php echo number_format($total_return,2,".",","); ?></td>
                </tr>            
            </tbody>
        </table>        
    </body>
    <!-- end::Body -->
</html>