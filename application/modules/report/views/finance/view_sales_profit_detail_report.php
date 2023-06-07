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
        <div style="font-size:14px;">
            DETAIL PROFITABILITAS PENJUALAN | NO. <?php echo $sales_invoice['invoice']; ?>
        </div>
        <table style="width:100%; font-size:13px;">
            <tbody>
                <tr>
                    <td width="17%">TGL. TRANSAKSI</td>
                    <td width="20%">: <?php echo date('d-m-Y', strtotime($sales_invoice['date'])); ?></td>
                    <td width="5%">Sales</td>
                    <td>: <?php echo $sales_invoice['name_s']; ?></td>
                    <td width="10%">Pelanggan</td>
                    <td>: <?php echo $sales_invoice['name_c'] ?></td>
                </tr>
                <tr>
                    <td>JTH. TEMPO (<?php echo $sales_invoice['payment_due']; ?>) Hari</td>
                    <td>: <?php $payment = ($sales_invoice['payment'] == 1) ? 'TUNAI' : 'KREDIT'; echo $payment; ?> | <?php echo date('d-m-Y', strtotime($sales_invoice['due_date'])); ?></td>
                    <td>OPT</td>
                    <td>: <?php echo $sales_invoice['name_e'];?></td>
                    <td>Alamat</td>
                    <td>: <?php  echo $sales_invoice['address_c']; ?></td>
                </tr>
            </tbody>
        </table>
        <table style="width:100%;" id="product_table" border="0">
            <thead>
                <tr>
                    <td style="border-top:1px solid black; border-bottom:1px solid black;" class="text-left">NO.</td>
                    <td style="border-top:1px solid black; border-bottom:1px solid black;" class="text-left">PRODUK</td>
                    <td style="border-top:1px solid black; border-bottom:1px solid black;" class="text-right">BERAT</td>
                    <td style="border-top:1px solid black; border-bottom:1px solid black;" class="text-right">QTY</td>
                    <td style="border-top:1px solid black; border-bottom:1px solid black;" class="text-right">HARGA</td>                    
                    <td style="border-top:1px solid black; border-bottom:1px solid black;" class="text-right">TOTAL</td>
                    <td style="border-top:1px solid black; border-bottom:1px solid black;" class="text-right">HPP@StnDsr</td>
                    <td style="border-top:1px solid black; border-bottom:1px solid black;" class="text-right">TOTAL HPP</td>
                    <td style="border-top:1px solid black; border-bottom:1px solid black;" class="text-right">LABA</td>
                </tr>
            </thead>
            <tbody>
                <?php $product=0; $no=1; $total_price = 0; $total_hpp=0; $total_profit=0; $total_dpp = 0; $total_ppn = 0; $total_weight = 0; ?>
                <?php foreach($sales_invoice_detail AS $sales_invoice_detail): ?>
                <tr>
                    <td><?php echo $no; ?></td>
                    <td valign="top" class="text-left"><?php echo $sales_invoice_detail['name_p']; ?></td>
                    <?php 
                        $where_product_unit = [
                            'product_code' => $sales_invoice_detail['code_p'],
                            'unit_id'      => $sales_invoice_detail['id_u'],
                            'deleted'      => 0,
                        ];
                        $product_unit = $this->crud->get_where('product_unit', $where_product_unit)->row_array();
                        ?>
                    <td valign="top" class="text-right"><?php echo $sales_invoice_detail['qty']*$product_unit['weight']; ?></td>
                    <td valign="top" class="text-right"><?php echo $sales_invoice_detail['qty']; ?> <?php echo $sales_invoice_detail['code_u']; ?></td>
                    <?php 
                        if($sales_invoice_detail['ppn'] == 1)
                        {
                            $dpp = ($sales_invoice_detail['price'] / 1.1)*$sales_invoice_detail['qty'];
                            $ppn = ($sales_invoice_detail['price'] - ($dpp/$sales_invoice_detail['qty']))*$sales_invoice_detail['qty'];
                        }
                        else
                        {
                            $dpp = $sales_invoice_detail['price']*$sales_invoice_detail['qty'];
                            $ppn = 0;
                        }                      
                            $total_ppn = $total_ppn + $ppn;
                            $total_dpp = $total_dpp + $dpp;
                            $total_weight = $total_weight + ($sales_invoice_detail['qty']*$product_unit['weight']) ;
                            $total_price = $total_price + $sales_invoice_detail['total'];
                        ?>
                    <td valign="top" class="text-right"><?php echo number_format($sales_invoice_detail['price'], 2,".",","); ?></td>
                    <td valign="top" class="text-right"><?php echo number_format($sales_invoice_detail['total'], 2,".",","); ?></td>
                    <td valign="top" class="text-right"><?php echo number_format($sales_invoice_detail['hpp'], 2,".",","); ?></td>
                    <?php 
                        $hpp = $sales_invoice_detail['hpp']*$sales_invoice_detail['qty']*$sales_invoice_detail['unit_value'];
                        $total_hpp = $total_hpp+$hpp; 
                    ?>
                    <td valign="top" class="text-right"><?php echo number_format($hpp, 2,".",","); ?></td>
                    <?php 
                        $profit = $sales_invoice_detail['total']-$hpp;
                        if( $hpp != 0)
                        {
                            $percent_profit = $profit/$hpp*100;
                            $total_profit = $total_profit+$profit;
                        }                        
                        else
                        {
                            $percent_profit = 0;
                            $total_profit = 0;                         
                        }
                    ?>
                    <td class="text-right"><?php echo number_format($profit, 2,".",",").' | '.number_format($percent_profit, 2,".",",").'%'; ?></td>
                </tr>                
                <?php $no++; endforeach; ?>
                <tr>
                    <td valign="top" colspan="5" style="border-top: 1px solid black;" class="text-right">TOTAL</td>
                    <td valign="top" style="border-top: 1px solid black;" class="text-right"><?php echo number_format($total_price, 2,".",","); ?></td>
                    <td valign="top" style="border-top: 1px solid black;" class="text-right"></td>
                    <td valign="top" style="border-top: 1px solid black;" class="text-right"><?php echo number_format($total_hpp, 2,".",","); ?></td>
                    <?php 
                        if($hpp != 0)
                        {
                            $percent_total_profit = $total_profit/$sales_invoice['total_hpp']*100; 
                        }
                        else
                        {
                            $percent_total_profit = 0; 
                        }                        
                    ?>
                    <td valign="top" style="border-top: 1px solid black;" class="text-right"><?php echo number_format($total_profit, 2,".",",").' | '.number_format($percent_total_profit, 2,".",",").'%'; ?></td>
                </tr>
                <tr>
                    <td valign="top" colspan="5" class="text-right">DISKON</td>
                    <td valign="top" class="text-right"><?php echo number_format($sales_invoice['discount_rp'], 2,".",","); ?></td>
                </tr>
                <tr>
                    <td valign="top" colspan="5" class="text-right">UANG MUKA</td>
                    <td valign="top" class="text-right"><?php echo number_format($sales_invoice['down_payment'], 2,".",","); ?></td>
                </tr>
                <tr>
                    <td valign="top" colspan="2" style="border-top: 1px solid black;"><?php echo $this->global->terbilang($sales_invoice['grandtotal']-$sales_invoice['down_payment']); ?></td>
                    <td valign="top" class="text-right" style="border-top: 1px solid black;"><?php echo $total_weight; ?></td>
                    <td valign="top" colspan="2"  style="border-top: 1px solid black;" class="text-right grandtotal"><b>GRANDTOTAL</b></td>
                    <td valign="top" style="border-top: 1px solid black;" class="text-right grandtotal"><b><?php echo number_format($sales_invoice['grandtotal']-$sales_invoice['down_payment'], 2,".",","); ?></b></td>
                    <?php ?>
                    <td valign="top" style="border-top: 1px solid black;" class="text-right"></td>
                    <td valign="top" style="border-top: 1px solid black;" class="text-right"><b><?php echo number_format($total_hpp, 2,".",","); ?></b></td>
                    <?php if($total_hpp != 0): ?>
                    <td valign="top" style="border-top: 1px solid black;" class="text-right"><b><?php echo number_format($sales_invoice['grandtotal']-$sales_invoice['down_payment']-$total_hpp, 2,".",",").' | '.number_format(($sales_invoice['grandtotal']-$sales_invoice['down_payment']-$total_hpp)/$total_hpp*100, 2,".",",").'%'; ?></b></td>
                    <?php else: ?>
                        <td valign="top" style="border-top: 1px solid black;" class="text-right"><b><?php echo number_format($sales_invoice['grandtotal']-$sales_invoice['down_payment']-$total_hpp, 2,".",",").' | 0 %'; ?></b></td>
                    <?php endif; ?>                    
                </tr>
            </tbody>
        </table>
    </body>
    <!-- end::Body -->
</html>