<!DOCTYPE html>
<html lang="en">
    <!-- begin::Head -->
    <head>
        <meta charset="utf-8" />
        <title>Faktur Penjualan | <?php echo $sales_invoice['invoice']; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <base href="<?php echo base_url('/'); ?>">
        <link href="./assets/css/printout.min.css" rel="stylesheet" type="text/css" />
        <link href="./assets/vendors/general/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
    </head>
    <style>
        body{
            font-family: 'Calibri';
        }
        body, #product_table{
            font-size: 11px;
        }
    </style>
    <!-- end::Head -->
    <!-- begin::Body -->
    <body>
        <table style="width:100%;" id="product_table" border="0">
            <thead>
                <tr>
                    <th style="border-top:1px solid black; border-bottom:1px solid black;" width="5%" class="text-left">NO.</th>
                    <th style="border-top:1px solid black; border-bottom:1px solid black;" width="45%" class="text-left">PRODUK</th>
                    <th style="border-top:1px solid black; border-bottom:1px solid black;" class="text-right">QTY</th>
                    <th style="border-top:1px solid black; border-bottom:1px solid black;" class="text-right">HARGA</th>
                    <th style="border-top:1px solid black; border-bottom:1px solid black;" class="text-right">DISKON(%)</th>
                    <th style="border-top:1px solid black; border-bottom:1px solid black;" class="text-right">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?php $total_dpp = 0; $total_ppn = 0; ?>
                <?php $no=1; foreach($sales_invoice_detail AS $info): ?>                                        
                <tr>
                    <td><?php echo $no; ?></td>
                    <td class="text-left"><?php echo $info['name_p']; ?></td>
                    <td class="text-right"><?php echo $info['qty']; ?> <?php echo $info['code_u']; ?></td>
                    <?php 
                        if($info['ppn'] == 1)
                        {
                            $dpp = ($info['price'] / 1.1)*$info['qty'];
                            $ppn = ($info['price'] - ($dpp/$info['qty']))*$info['qty'];
                        }
                        else
                        {
                            $dpp = $info['price']*$info['qty'];
                            $ppn = 0;
                        }                      
                        $total_ppn = $total_ppn + $ppn;
                        $total_dpp = $total_dpp + $dpp;
                        ?>
                    <td class="text-right"><?php echo number_format($info['price'], 2,".",","); ?></td>
                    <td class="text-right"><?php echo $info['disc_product']; ?></td>
                    <td class="text-right"><?php echo number_format($info['total'], 2,".",","); ?></td>
                </tr>
                <tr><td></td></tr>
                <?php $no++; endforeach; ?>
                <?php if($sales_invoice['discount_rp'] != 0): ?>
                <tr>
                    <td colspan="4" style="border-top: 1px solid black;" class="text-right">DISKON</td>
                    <td style="border-top: 1px solid black;" class="text-right"><?php echo number_format($sales_invoice['discount_rp'],0,".",","); ?></td>
                </tr>
                <?php endif; ?>
                <tr>    
                    <td colspan="5" style="border-top:1px solid black;" class="text-right">TOTAL AKHIR</td>
                    <td style="border-top:1px solid black;" class="text-right"><?php echo number_format($sales_invoice['grandtotal'], 2,".",","); ?></td>
                </tr>
                <?php if($sales_invoice['down_payment'] != 0): ?>
                <tr>
                    <td style="border-top: 1px solid black;" colspan="4" class="text-right">UANG MUKA</td>
                    <td style="border-top: 1px solid black;" class="text-right"><?php echo number_format($sales_invoice['down_payment'],0,".",","); ?></td>
                </tr>
                <tr>
                    <td style="border-top: 1px solid black;" colspan="4" class="text-right">SISA TAGIHAN</td>
                    <td style="border-top: 1px solid black;" class="text-right"><?php echo number_format($sales_invoice['down_payment'] - $sales_invoice['grandtotal'],0,".",","); ?></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <table style="width:100%;">
            <tr>
                <td width="15%">*Terbilang</td>
                <td width="5%" class="text-center">:</td>                
                <td><i><?php echo $this->global->terbilang($sales_invoice['grandtotal']); ?></i></td>
            </tr>
            <tr>
                <td style="height:5px;">&nbsp;</td>
            </tr>
            <tr>
                <td width="15%">KETERANGAN</td>
                <td width="5%" class="text-center">:</td>                
                <td><?php echo $sales_invoice['information']; ?></td>
            </tr>
        </table>
        <sethtmlpagefooter name="LastPageFooter"/>
    </body>
    <!-- end::Body -->
</html>