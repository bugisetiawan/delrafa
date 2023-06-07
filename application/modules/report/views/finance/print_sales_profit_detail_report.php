<!DOCTYPE html>
<html>
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
        <table style="width: 100%;" celpadding="1" cellspacing="1">
            <tbody>
                <?php $grandtotal = 0; foreach($data AS $info): ?>                                                                
                <tr>
                    <td width="25%">Tanggal : <b><?php echo date('d-m-Y', strtotime($info['sales_invoice']['date'])); ?></b></td>
                    <td width="35%" class="text-center">No. Transaksi : <b><?php echo $info['sales_invoice']['invoice']; ?></b></td>
                    <td>Sales: <b><?php echo $info['sales_invoice']['name_s']; ?></b></td>
                </tr>
                <tr>
                <td>Pelanggan</b></td> 
                <td colspan="2">: <b><?php echo $info['sales_invoice']['name_c']; ?></td>                    
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="3" style="border-bottom: 1px solid black;">
                        <table style="width: 100%;" cellpadding="1">
                            <thead>
                                <tr>
                                    <td width="10"><u>No.</u></td>
                                    <td><u>Nama</u></td>
                                    <td width="100" colspan="2" class="text-center"><u>Qty</u></td>
                                    <td width="80" class="text-center"><u>Harga</u></td>
									<td width="80" class="text-center"><u>Hpp@StnDasar</u></td>
                                    <td width="150" class="text-right"><u>Total</u></td>
                                    <td width="150" class="text-right"><u>Total HPP</u></td>
                                    <td width="150" class="text-right"><u>Laba</u></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $total_profit=0; $total_hpp=0; $no=1; foreach($info['detail_sales_invoice'] AS $info2): ?>                                                    
                                <tr>
                                    <td valign="top" class="text-right"><?php echo $no; ?>.</td>
                                    <td valign="top"><?php echo $info2['name_p']; ?></td>
                                    <td valign="top" width="50" class="text-right"><?php echo number_format($info2['qty'], 2, ".", ","); ?></td>
                                    <td valign="top" width="50" class="text-left">&nbsp;<?php echo $info2['code_u']; ?></td>
                                    <td valign="top" class="text-right"><?php echo number_format($info2['price'], 2, ".", ","); ?></td>
									<td valign="top" class="text-right"><?php echo number_format($info2['hpp'], 2, ".", ","); ?></td>
                                    <td valign="top" class="text-right"><?php echo number_format($info2['total'], 2, ".", ","); ?></td>
                                    <?php 
                                        $hpp= $info2['qty']*$info2['unit_value']*$info2['hpp'];
                                        $total_hpp = $total_hpp + $hpp;
                                    ?>
                                    <td valign="top" class="text-right"><?php echo number_format($hpp, 2, ".", ","); ?></td>
                                    <?php 
                                        $profit = ($info2['qty']*$info2['price']) - ($info2['qty']*$info2['unit_value']*$info2['hpp']);
                                        $percent = $profit/($info2['qty']*$info2['price'])*100;
                                        $total_profit = $total_profit+$profit;
                                    ?>
                                    <td valign="top" class="text-right"><?php echo number_format($profit, 2, ".", ",").'|'.number_format($percent, 2, ".", ",").'%'; ?></td>
                                </tr>
                                <?php $no++; endforeach; ?>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>                                    
                                    <td colspan="6" class="text-right">TOTAL AKHIR:</td>
                                    <td class="text-right">&nbsp;<?php echo number_format($info['sales_invoice']['grandtotal'],2,".",","); ?></td>
                                    <td class="text-right">&nbsp;<?php echo number_format($total_hpp, 2,".",","); ?></td>
                                    <?php 
                                        $total_profit = $total_profit;  
                                        $total_percent = $total_profit/$info['sales_invoice']['grandtotal']*100;
                                    ?>
                                    <td class="text-right">&nbsp;<?php echo number_format($total_profit, 2,".",",").'|'.number_format($total_percent, 2, ".", ",").'%';; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </body>
</html>