<!DOCTYPE html>
<html lang="en">    
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
        <table style="width: 100%;" celpadding="1" cellspacing="1" border="0">
            <tbody>
            <?php $grandtotal = 0; foreach($data AS $info): ?>                                                                
                <tr>
                    <td>Tanggal : <b><?php echo date('d-m-Y', strtotime($info['product_usage']['date'])); ?></b></td>                                            
                    <td>No. Transaksi : <b><?php echo $info['product_usage']['code']; ?></b></td>                    
					<td>User: <b><?php echo $info['product_usage']['name_u']; ?></td>
                </tr>                
                <tr>
                    <td>&nbsp;</td>
                </tr>                      
                <tr>
                    <td colspan="3" style="border-bottom: 1px solid black;">
                        <table style="width: 100%;" cellpadding="1">
                            <thead>
                                <tr>
                                    <td width="10"><u>No.</u></td>
                                    <td><u>Nama</u></td>
                                    <td colspan="2" class="text-center"><u>Qty</u></td>
                                    <td class="text-center"><u>Harga</u></td>
                                    <td class="text-center"><u>Gudang</u></td>
                                    <td class="text-right"><u>Total</u></td>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $subtotal=0; $no=1; foreach($info['detail_product_usage'] AS $info2): ?>                                                    
                                <tr>
                                    <td valign="top" class="text-center"><?php echo $no; ?>.</td>
                                    <td valign="top"><?php echo $info2['name_p']; ?></td>
                                    <td valign="top" width="50" class="text-right"><?php echo number_format($info2['qty'],2,".",","); ?></td>
                                    <td valign="top" width="50" class="text-left">&nbsp;<?php echo $info2['code_u']; ?></td>
                                    <td valign="top" class="text-right"><?php echo number_format($info2['price'],2,".",","); ?></td>
                                    <td valign="top" class="text-left"><?php echo $info2['code_w']; ?></td>
                                    <td valign="top" class="text-right"><?php echo number_format($info2['total'],2,".",","); ?></td>
                                </tr>
                                <?php $no++; endforeach; ?>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-right">TOTAL</td>
                                    <td class="text-right">: <?php echo number_format($info['product_usage']['grandtotal'],2,".",","); ?></td>
                                    <?php $grandtotal = $grandtotal + $info['product_usage']['grandtotal']; ?>
                                </tr>
                            </tbody>                                            
                        </table> 
                    </td>
                </tr>
            <?php endforeach; ?>                        
                <tr>
                    <td>&nbsp;</td>  
                    <td class="text-right">TOTAL KESELURUHAN</td>
                    <td>
                        <table style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td class="text-right"><?php echo number_format($grandtotal, 2, ".", ","); ?></td>                                                        
                                </tr>                                                    
                            </tbody>
                        </table>
                    </td>                                                                                                                      
                </tr>
            </tbody>
        </table>            
    </body>    
</html>