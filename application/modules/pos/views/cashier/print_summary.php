<!DOCTYPE html>
<html lang="en">
    <!-- begin::Head -->
    <head>
        <meta charset="utf-8" />
        <title>Summary</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="shortcut icon" href="./assets/media/logos/favicon.png" />
        <base href="<?php echo base_url('/'); ?>">
        <link href="./assets/css/printout.min.css" rel="stylesheet" type="text/css" />
        <link href="./assets/vendors/general/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
    </head>
    <!-- end::Head -->
    <!-- begin::Body -->
    <body>
        <table style="width:100%;">
            <thead class="text-center">
                <tr>
                    <th>
                        <?php echo $perusahaan['name']; ?><br>
                        <small>Alamat: <?php echo $perusahaan['address']; ?></small><br>
                        <small>Telepon: <?php echo $perusahaan['telephone']; ?></small>
                    </th>
                </tr>
                <tr>&nbsp;</tr>
                <tr>
                    <th>REKAP KASIR</th>
                </tr>
            </thead>
        </table>
        <br>
        <div class="row">
            <div class="col-md-12">                                    
                <table >
                    <tbody>       
                        <tr>
                            <td colspan="6" style="border-bottom: 0.5px solid black;">INFORMASI</td>
                        </tr>      
                        <tr>
                            <td>Kasir</td>
                            <td>:</td>
                            <td><?php echo strtoupper($cashier['name_c']); ?></td>                            
                        </tr>  
                        <tr>
                            <td>Tanggal</td>
                            <td>:</td>
                            <td><?php echo date('d-m-Y', strtotime($cashier['date'])); ?></td>
                        </tr> 
                        <tr>
                            <td>Jam Buka</td>
                            <td>:</td>
                            <td><?php echo $cashier['open_time']; ?></td>
                        </tr>
                        <tr>                            
                            <td>Jam Tutup</td>
                            <td>:</td>
                            <td><?php echo $cashier['close_time']; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th colspan="2" class="text-left">PEMASUKAN</th>
                </tr>
            </thead>
            <tbody>                                
                <tr style="border-bottom:1px solid black;">
                    <td>MODAL KASIR</td>                                                
                    <td class="text-right"><?php echo number_format($cashier['modal'], 0, ".", ","); ?></td>
                </tr>
                <tr style="border-bottom:1px solid black;">
                    <td>UANG MUKA</td>                                                
                    <td class="text-right"><?php echo number_format($cashier['total_dp'], 0, ".", ","); ?></td>
                </tr>
                <tr style="border-bottom:1px solid black;">
                    <td>PENJUALAN</td>                                                
                    <td class="text-right"><?php echo number_format($cashier['total_sales'],0, ".", ","); ?></td>
                </tr>
                <tr style="border-bottom:1px solid black;">
                    <td>POS</td>                                                
                    <td class="text-right"><?php echo number_format($cashier['total_pos'],0, ".", ","); ?></td>
                </tr>                                
                <tr>
                    <?php $in = $cashier['modal']+$cashier['total_dp']+$cashier['total_sales']+$cashier['total_pos'];   ?>
                    <td colspan="2" class="text-right"><?php echo number_format($in, 0, ".", ","); ?></td>
                </tr>
            </tbody>
        </table>
        <hr>
        <table style="width:100%;">
            <thead>
                <tr>
                    <th colspan="2" class="text-left">PENGELUARAN</th>
                </tr>
            </thead>
            <tbody>                                
                <tr style="border-bottom:1px solid black;">
                    <td class="">RETUR PENJUALAN</td>                                                
                    <td class="text-right"><?php echo number_format($cashier['total_sales_return'], 0, ".", ","); ?></td>
                </tr>
                <tr style="border-bottom:1px solid black;">
                    <td class="">BIAYA</td>                                                
                    <td class="text-right"><?php echo number_format($cashier['total_expense'], 0, ".", ","); ?></td>
                </tr>
                <tr style="border-bottom:1px solid black;">
                    <td class="">COLLECT</td>
                    <td class="text-right"><?php echo number_format($cashier['total_collect'],0, ".", ","); ?></td>
                </tr>                                                               
                <tr>                                                
                    <?php $out = $cashier['total_sales_return']+$cashier['total_expense']+$cashier['total_collect'];   ?>
                    <td colspan="2" class="text-right"><?php echo number_format($out, 0, ".", ","); ?></td>
                </tr>
            </tbody>
        </table>
        <hr>
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th>TOTAL SETORAN</th>
                    <th><?php echo number_format($cashier['grandtotal'],0, ".", ","); ?></th>
                </tr>
            </thead>
        </table>
        <hr><br>
        <div class="row">
            <div class="col-md-6 text-center">
                <h4>KASIR</h4>
                <br>
                <br>
                <br>
                <p>( <?php echo $cashier['name_c']; ?> )</p>
            </div>
            <div class="col-md-6 text-center">
                <h4>PEREKAP</h4>
                <br>
                <br>
                <br>
                <p>(______________________)</p>
            </div>
        </div>
    </body>
    <!-- end::Body -->
</html>