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
            <tbody>
            <?php $grandtotal = 0; foreach($data AS $info): ?>                                                                
            <tr>
                    <td width="25%">Tanggal: <b><?php echo date('d-m-Y', strtotime($info['purchase']['date'])); ?></b></td>                                            
                    <td width="30%">No. Transaksi: <b><?php echo $info['purchase']['code']; ?></b></td>
                    <td>No. Ref: <b><?php echo $info['purchase']['invoice']; ?></b></td>                    
                    <?php 
                        if($info['purchase']['ppn'] == 1)
                        {
                            $ppn = "PPN";
                        }
                        elseif($info['purchase']['ppn'] == 2)
                        {
                            $ppn = "FINAL";
                        }
                        else
                        {
                            $ppn = "NON";
                        }
                    ?>
                    <td width="15%" class="text-right">PPN : <b><?php echo $ppn; ?></b></td>
                </tr>       
                <tr>
                    <td>Supplier</b></td> 
                    <td  colspan="3">: <b><?php echo $info['purchase']['name_s']; ?></td>
                </tr>     
                <tr>
                    <td>&nbsp;</td>
                </tr>                      
                <tr>
                    <td colspan="4" style="border-bottom: 1px solid black;">
                        <table style="width: 100%;" cellpadding="1">
                            <thead>
                                <tr>
                                    <td width="10px"><u>No.</u></td>                                    
                                    <td><u>Nama</u></td>
                                    <td width="100px" colspan="2" class="text-center"><u>Qty</u></td>
                                    <td width="80px" class="text-center"><u>Harga</u></td>
                                    <td width="150px" class="text-center"><u>Gudang</u></td>
                                    <td width="80px" class="text-right"><u>Total</u></td>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $subtotal=0; $no=1; foreach($info['detail_purchase'] AS $info2): ?>                                                    
                                <tr>
                                    <td valign="top" class="text-right"><?php echo $no; ?>.</td>
                                    <td valign="top"><?php echo $info2['name_p']; ?></td>                                    
                                    <td valign="top" width="50" class="text-right"><?php echo number_format($info2['qty'],2,".",","); ?></td>
                                    <td valign="top" width="50" class="text-left">&nbsp;<?php echo $info2['code_u']; ?></td>
                                    <td valign="top" class="text-right"><?php echo number_format($info2['price'],2,".",","); ?></td>
                                    <td valign="top" class="text-center"><?php echo $info2['code_w']; ?></td>
                                    <td valign="top" class="text-right"><?php echo number_format($info2['total'],2,".",","); ?></td>
                                </tr>
                                <?php $no++; endforeach; ?>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="7">
                                        <table style="width: 100%;" cellpadding="1">
                                            <tbody>
                                                <tr>
                                                    <td class="text-right">Diskon(%):</td>
                                                    <td> <?php echo number_format($info['purchase']['discount_p'],2,".",","); ?> %</td>
                                                    <td class="text-right">Diskon(Rp):</td>
                                                    <td> <?php echo number_format($info['purchase']['discount_rp'],2,".",","); ?></td>
                                                    <td class="text-right">PPN:</td>
                                                    <td> <?php echo number_format($info['purchase']['total_tax'],2,".",","); ?></td>
                                                    <td class="text-right">TOTAL:</td>
                                                    <td class="text-right">&nbsp;<?php echo number_format($info['purchase']['grandtotal'],2,".",","); ?></td>
                                                    <?php $grandtotal = $grandtotal + $info['purchase']['grandtotal']; ?>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>                                
                            </tbody>                                            
                        </table> 
                    </td>
                </tr>
            <?php endforeach; ?>                        
                <tr>
                    <td colspan="3">&nbsp;TOTAL</td>
                    <td>
                        <table style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td class="text-right"><?php echo number_format($grandtotal, 2,".",","); ?></td>                                                        
                                </tr>                                                    
                            </tbody>
                        </table>
                    </td>                                                                                                                      
                </tr>
            </tbody>
        </table>            
    </body>
    <!-- end::Body -->
</html>