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
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th colspan="9">LAPORAN PIUTANG PENJUALAN</th>
                </tr>
                <tr>                    
                    <th colspan="9">Periode Transaksi: <?php echo date('d-m-Y', strtotime($filter['from_date'])); ?> s.d. <?php echo date('d-m-Y', strtotime($filter['to_date'])); ?></th>
                </tr>
                <tr><th>&nbsp;</th></tr>
                <tr>
                    <th style="border-top: 1px solid black; border-bottom: 1px solid black;"  width="10" rowspan="2">NO.</th>
                    <th style="border-top: 1px solid black; border-bottom: 1px solid black;" rowspan="2">PELANGGAN</th>
                    <th style="border-top: 1px solid black; border-bottom: 1px solid black;" colspan="7">TRANSAKSI</th>
                </tr>
                <tr>                
                    <th style="border-bottom: 1px solid black;">TGL.</th>
                    <th style="border-bottom: 1px solid black;">NO. TRANSAKSI</th>                    
                    <th style="border-bottom: 1px solid black;">JT. TMP</th>                    
                    <th style="border-bottom: 1px solid black;">TOTAL</th>
                    <th style="border-bottom: 1px solid black;">TAGIHAN</th> 
                    <th style="border-bottom: 1px solid black;">TAGIHAN (CEK/GIRO)</th> 
                    <th style="border-bottom: 1px solid black;">PEMBAYARAN</th>
                </tr>  
            </thead>
            <tbody> 
                <?php $total_sales=0; $total_account_payable=0; $total_cheque_payable=0; $total_payment=0; ?>
                <?php $no=1; foreach($data AS $info): ?>
                <tr>
                    <td valign="top" class="text-center"><?php echo $no.'.'; ?></td>
                    <td valign="top"><?php echo $info['customer']['name']; ?></td>
                    <td valign="top" class="text-center">
                        <table style="width: 100%;">
                            <tbody>
                                <?php foreach($info['data'] AS $info2): ?>
                                <tr>
                                    <td><?php echo date('d-m-Y', strtotime($info2['sales_receivable']['date'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>                        
                    </td>
                    <td valign="top" class="text-center">
                        <table style="width: 100%;">
                            <tbody>
                                <?php foreach($info['data'] AS $info2): ?>
                                <tr>                                    
                                    <td><?php echo $info2['sales_receivable']['invoice']; ?></td>                                    
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>                        
                    </td>
                    <td valign="top" class="text-center">
                        <table style="width: 100%;">
                            <tbody>
                                <?php foreach($info['data'] AS $info2): ?>
                                <tr>                                    
                                    <td><?php echo date('d-m-Y', strtotime($info2['sales_receivable']['due_date'])); ?></td>                                    
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>                        
                    </td>
                    <td valign="top" class="text-right">
                        <table style="width: 100%;">
                            <tbody>
                                <?php foreach($info['data'] AS $info2): ?>
                                <tr>                                    
                                    <td><?php echo number_format($info2['sales_receivable']['grandtotal'], 2, '.', ','); ?></td>
                                </tr>
                                <?php $total_sales=$total_sales+$info2['sales_receivable']['grandtotal']; endforeach; ?>
                            </tbody>
                        </table>                        
                    </td>
                    <td valign="top" class="text-right">
                        <table style="width: 100%;">
                            <tbody>
                                <?php foreach($info['data'] AS $info2): ?>
                                <tr>                                    
                                    <td><?php echo number_format($info2['sales_receivable']['account_payable'], 2, '.', ','); ?></td>
                                </tr>
                                <?php $total_account_payable=$total_account_payable+$info2['sales_receivable']['account_payable']; endforeach; ?>
                            </tbody>
                        </table>                        
                    </td>
                    <td valign="top" class="text-right">
                        <table style="width: 100%;">
                            <tbody>
                                <?php foreach($info['data'] AS $info2): ?>
                                <tr>                                    
                                    <td><?php echo number_format($info2['sales_receivable']['cheque_payable'], 2, '.', ','); ?></td>
                                </tr>
                                <?php $total_cheque_payable=$total_cheque_payable+$info2['sales_receivable']['cheque_payable']; endforeach; ?>
                            </tbody>
                        </table>                        
                    </td>
                    <td valign="top" class="text-right">
                        <table style="width: 100%;">
                            <tbody>
                                <?php foreach($info['data'] AS $info2): ?>
                                <tr>                                    
                                    <td><?php echo number_format($info2['sales_receivable_payment']['payment'], 2, '.', ','); ?></td>
                                </tr>
                                <?php $total_payment=$total_payment+$info2['sales_receivable_payment']['payment']; endforeach; ?>
                            </tbody>
                        </table>                        
                    </td>                    
                </tr>
                <tr><td colspan="9" style="border-bottom: 1px solid black;"></td></tr>
                <?php $no++; endforeach; ?>
                <tr>
                    <td colspan="5">&nbsp;</td>
                    <td class="text-right"><?php echo number_format($total_sales, 2, '.', ','); ?></td>
                    <td class="text-right"><?php echo number_format($total_account_payable, 2, '.', ','); ?></td>
                    <td class="text-right"><?php echo number_format($total_cheque_payable, 2, '.', ','); ?></td>
                    <td class="text-right"><?php echo number_format($total_payment, 2, '.', ','); ?></td>
                </tr>
            </tbody>
        </table>
    </body>
    <!--begin:: Global Mandatory Vendors -->
    <script src="./assets/vendors/general/jquery/dist/jquery.js" type="text/javascript"></script>               
    <!--begin::Global Theme Bundle(used by all pages) -->
    <script src="./assets/js/demo1/scripts.bundle.js" type="text/javascript"></script>
    <!--end::Global Theme Bundle -->
    <script>
        $(document).ready(function (){
            window.print();
        });
        // window.onfocus=function(){window.close();}
        window.onafterprint = function(){
            window.close();
        }
    </script>
</html>