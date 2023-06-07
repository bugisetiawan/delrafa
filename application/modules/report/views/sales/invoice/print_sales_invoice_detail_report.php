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
        <table style="width: 100%;" celpadding="1" cellspacing="1" border="0">
            <tbody>
            <?php $grandtotal = 0; foreach($data AS $info): ?>                                                                
            <tr>
                    <td width="25%">Tanggal : <b><?php echo date('d-m-Y', strtotime($info['sales_invoice']['date'])); ?></b></td>                                            
                    <td width="35%">No. Transaksi : <b><?php echo $info['sales_invoice']['invoice']; ?></b></td>
                    <?php 
                        if($info['sales_invoice']['ppn'] == 0)
                        {
                            $ppn = "NON";
                        }
                        elseif($info['sales_invoice']['ppn'] == 1)
                        {
                            $ppn = "PPN";
                        }
                        else
                        {
                            $ppn = "-";
                        }
                    ?>
                    <td>&nbsp;Sales: <b><?php echo $info['sales_invoice']['name_s']; ?></td>
                    <td class="text-right">PPN : <b><?php echo $ppn; ?></b></td>                    
                </tr>       
                <tr>
                    <td>Pelanggan</b></td> 
                    <td colspan="3">: <b><?php echo $info['sales_invoice']['name_c']; ?></td>                    
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>                      
                <tr>
                    <td colspan="4" style="border-bottom: 1px solid black;">
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
                            <?php $subtotal=0; $no=1; foreach($info['detail_sales_invoice'] AS $info2): ?>                                                    
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
                                    <td colspan="2" class="text-right">Diskon(%):</td>
                                    <td> <?php echo number_format($info['sales_invoice']['discount_p'],2,".",","); ?> %</td>
                                    <td colspan="2" class="text-right">Diskon(Rp):</td>
                                    <td> <?php echo number_format($info['sales_invoice']['discount_rp'],2,".",","); ?></td>
                                    <td class="text-right">TOTAL AKHIR:</td>
                                    <td class="text-right">&nbsp;<?php echo number_format($info['sales_invoice']['grandtotal'],2,".",","); ?></td>
                                    <?php $grandtotal = $grandtotal + $info['sales_invoice']['grandtotal']; ?>
                                </tr>
                            </tbody>                                            
                        </table> 
                    </td>
                </tr>
            <?php endforeach; ?>                        
                <tr>
                    <td colspan="2">&nbsp;</td>  
                    <td class="text-right">TOTAL KESELURUHAN</td>
                    <td>
                        <table style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td class="text-right"><?php echo number_format($grandtotal,0,".",","); ?></td>                                                        
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