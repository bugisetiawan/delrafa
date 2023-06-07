<!DOCTYPE html>
<html lang="en">
    <!-- begin::Head -->
    <head>
        <meta charset="utf-8" />
        <title>Cetak Penjualan | <?php echo $sales_invoice['invoice']; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <base href="<?php echo base_url('/'); ?>">
        <link href="./assets/css/printout.min.css" rel="stylesheet" type="text/css" />
        <link href="./assets/vendors/general/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
    </head>
    <style>
        body{
            font-family: Arial, sans-serif;
        }
        body, #product_table{
            font-size: 12px;
        }
        .grandtotal{
            font-size: 13px;
        }
    </style>
    <!-- end::Head -->
    <!-- begin::Body -->
    <body>
        <?php 
            $page= ceil(count($sales_invoice_detail)/12); 
            $product=0; $no=1; $total_price = 0; $total_dpp = 0; $total_ppn = 0; $total_weight = 0;
        ?>
        <?php for($i=1;$i<=$page;$i++): ?>
            <table style="width:100%;" id="product_table" border="0">
                <thead>
                    <tr>
                        <td style="border-top:1px solid black; border-bottom:1px solid black;" width="5%" class="text-left">NO.</td>
                        <td style="border-top:1px solid black; border-bottom:1px solid black;" width="45%" class="text-left">PRODUK</td>
                        <td style="border-top:1px solid black; border-bottom:1px solid black;" class="text-right">BERAT</td>
                        <td style="border-top:1px solid black; border-bottom:1px solid black;" class="text-right">QTY</td>
                        <td style="border-top:1px solid black; border-bottom:1px solid black;" class="text-right">HARGA</td>
                        <td style="border-top:1px solid black; border-bottom:1px solid black;" class="text-right">TOTAL</td>
                    </tr>
                </thead>
                <tbody>
                    <?php for($j=0;$j<=12;$j++): ?>
                        <?php if(isset($sales_invoice_detail[$product])): ?>
                        <tr>
                        <td><?php echo $no; ?></td>
                        <td class="text-left"><?php echo $sales_invoice_detail[$product]['name_p']; ?></td>
                        <?php 
                            $where_product_unit = [
                                'product_code' => $sales_invoice_detail[$product]['code_p'],
                                'unit_id'      => $sales_invoice_detail[$product]['id_u'],
                                'deleted'      => 0,
                            ];
                            $product_unit = $this->crud->get_where('product_unit', $where_product_unit)->row_array();
                        ?>
                        <td class="text-right"><?php echo round($sales_invoice_detail[$product]['qty']*$product_unit['weight'], 2); ?></td>
                        <td class="text-right"><?php echo $sales_invoice_detail[$product]['qty']; ?> <?php echo $sales_invoice_detail[$product]['code_u']; ?></td>
                        <?php 
                            if($sales_invoice_detail[$product]['ppn'] == 1)
                            {
                                $dpp = ($sales_invoice_detail[$product]['price'] / 1.1)*$sales_invoice_detail[$product]['qty'];
                                $ppn = ($sales_invoice_detail[$product]['price'] - ($dpp/$sales_invoice_detail[$product]['qty']))*$sales_invoice_detail[$product]['qty'];
                            }
                            else
                            {
                                $dpp = $sales_invoice_detail[$product]['price']*$sales_invoice_detail[$product]['qty'];
                                $ppn = 0;
                            }                      
                                $total_ppn = $total_ppn + $ppn;
                                $total_dpp = $total_dpp + $dpp;
                                $total_weight = $total_weight + ($sales_invoice_detail[$product]['qty']*$product_unit['weight']) ;
                                $total_price = $total_price + $sales_invoice_detail[$product]['total'];
                            ?>
                        <td class="text-right"><?php echo number_format($sales_invoice_detail[$product]['price'],0,".",","); ?></td>
                        <td class="text-right"><?php echo number_format($sales_invoice_detail[$product]['total'],0,".",","); ?></td>
                    </tr>
                        <?php endif; ?>
                    <?php $product++; $no++; endfor; ?>
                    <tr>
                        <td colspan="5" style="border-top: 1px solid black;" class="text-right">TOTAL</td>
                        <td style="border-top: 1px solid black;" class="text-right"><?php echo number_format($total_price,0,".",","); ?></td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-right">DISKON</td>
                        <td class="text-right"><?php echo number_format($sales_invoice['discount_rp'],0,".",","); ?></td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-right">UANG MUKA</td>
                        <td class="text-right"><?php echo number_format($sales_invoice['down_payment'],0,".",","); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border-top: 1px solid black;"><?php echo $this->global->terbilang($sales_invoice['grandtotal']-$sales_invoice['down_payment']); ?></td>
                        <td class="text-right" style="border-top: 1px solid black;"><?php echo round($total_weight, 2); ?></td>    
                        <td colspan="2"  style="border-top: 1px solid black;" class="text-right grandtotal"><b>GRANDTOTAL</b></td>
                        <td style="border-top: 1px solid black;" class="text-right grandtotal"><b><?php echo number_format($sales_invoice['grandtotal']-$sales_invoice['down_payment'],0,".",","); ?></b></td>
                    </tr>
                </tbody>
            </table>
        <?php endfor; ?>
        <!--<sethtmlpagefooter name="LastPageFooter"/>-->
    </body>
    <!-- end::Body -->
</html>