<!DOCTYPE html>
<html lang="en">
    <!-- begin::Head -->
    <head>
        <meta charset="utf-8" />
        <base href="<?php echo base_url('/'); ?>">
        <title>Pembelian | <?php echo $purchase_invoice['code']; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="shortcut icon" href="./assets/media/logos/favicon.png" />        
        <link href="./assets/css/printout.min.css" rel="stylesheet" type="text/css" />
        <link href="./assets/vendors/general/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />                
    </head>
    <!-- end::Head -->
    <!-- begin::Body -->
    <body>        
        <table style="width:100%;" id="product_table" >
            <thead>
                <?php if($purchase_invoice['price_include_tax'] == 1): ?>
                <tr>
                    <td colspan="6" class="text-right">*Harga pembelian sudah termasuk pajak</td>
                </tr>
                <?php endif; ?>
                <tr id="table-title">
                    <th class="text-right">NO.</th>
                    <th class="text-center">KODE</th>
                    <th class="text-left">PRODUK</th>
                    <th class="text-right">QTY</th>
                    <th class="text-right">HARGA</th>
                    <th class="text-right">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($purchase_invoice_detail AS $info): ?>
                    <tr>
                        <td width="10" class="text-right"><?php echo $no; ?></td>
                        <td class="text-center"><?php echo $info['code_p']; ?></td>
                        <td class="text-left"><?php echo $info['name_p']; ?></td>
                        <td class="text-right"><?php echo $info['qty']; ?> <?php echo $info['code_u']; ?></td>                                            
                        <td class="text-right"><?php echo number_format($info['price'],0,".",","); ?></td>
                        <td class="text-right"><?php echo number_format($info['total'],0,".",","); ?></td>
                    </tr>                
                <?php $no++; endforeach; ?>
                <tr>
                    <td style="border-top: 1px solid black;" colspan="5" class="text-right">SUBTOTAL</td>
                    <td style="border-top: 1px solid black;" class="text-right"><?php echo number_format($purchase_invoice['total_price'],0,".",","); ?></td>
                </tr>
                <tr>
                    <td style="border-top: 1px solid black;" colspan="5" class="text-right">DISKON</td>
                    <td style="border-top: 1px solid black;" class="text-right"><?php echo number_format($purchase_invoice['discount_rp'],0,".",","); ?></td>
                </tr>
                <?php if($purchase_invoice['ppn'] == 1): ?>
                    <?php if($purchase_invoice['price_include_tax'] != 1): ?>
                    <tr>
                        <td style="border-top: 1px solid black;" colspan="5" class="text-right">PPN</td>
                        <td style="border-top: 1px solid black;" class="text-right"><?php echo number_format($purchase_invoice['total_tax'],0,".",","); ?></td>
                    </tr>
                    <?php endif; ?>                        
                <?php endif; ?>
                <tr style="border-top: 1px solid black;">
                    <td colspan="3">*Terbilang: <?php echo $this->global->terbilang($purchase_invoice['grandtotal']); ?></td>
                    <td colspan="2" class="text-right">GRANDTOTAL</td>
                    <td class="text-right"><?php echo number_format($purchase_invoice['grandtotal'],0,".",","); ?></td>
                </tr>
            </tbody>
        </table>
        <!-- <sethtmlpagefooter name="LastPageFooter"/> -->
    </body>
    <!-- end::Body -->
</html>