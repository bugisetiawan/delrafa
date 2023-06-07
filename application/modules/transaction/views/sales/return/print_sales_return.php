<!DOCTYPE html>
<html lang="en">
    <!-- begin::Head -->
    <head>
        <meta charset="utf-8" />
        <title>Cetak Retur Penjualan | <?php echo $sales_return['code']; ?></title>
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
            font-size: 12px;
        }
    </style>
    <!-- end::Head -->
    <!-- begin::Body -->
    <body>
        <table width="100%" border="0" cellspacing="0">
            <thead>
                <tr style="text-align:center;">
                    <th style="border-top:1px solid black; border-bottom:1px solid black;">NO.</th>
                    <th style="border-top:1px solid black; border-bottom:1px solid black;">KODE</th>
                    <th style="border-top:1px solid black; border-bottom:1px solid black;">NAMA</th>                                              
                    <th style="border-top:1px solid black; border-bottom:1px solid black;">QTY</th>                                                
                    <th style="border-top:1px solid black; border-bottom:1px solid black;">HARGA</th>
                    <th style="border-top:1px solid black; border-bottom:1px solid black;">TOTAL</th>
                    <th style="border-top:1px solid black; border-bottom:1px solid black;">KETERANGAN</th>
                </tr>
            </thead>				
            <tbody>
                <?php $no=1; foreach($sales_return_detail AS $info): ?>
                    <tr>
                        <td style="text-align:center;"><?php echo $no; ?></td>
                        <td style="text-align:center;"><?php echo $info['code_p']; ?></td>
                        <td><?php echo $info['name_p']; ?></td>                            
                        <td style="text-align:center;"> <?php echo $info['qty']; ?></td>
                        <td style="text-align:right;"> <?php echo number_format($info['buyprice'],0,",","."); ?></td>
                        <td style="text-align:right;"> <?php echo number_format($info['total'],0,",","."); ?></td>
                        <td><?php echo $info['information']; ?></td>
                    </tr>
                <?php $no++; endforeach; ?>
                <tr>
                    <td colspan="3"  style="text-align:right; border-top:1px solid black;">TOTAL</td>
                    <td style="text-align:center; border-top:1px solid black;"><?php echo $sales_return['total_qty']; ?></td>
                    <td></td>
                    <td style="text-align:right; border-top:1px solid black;"><?php echo number_format($sales_return['total_return'],0,",","."); ?></td>
                    <td></td>
                </tr>
            </tbody>
        </table>  
        <!-- end:: Page -->                
        <!--begin:: Global Mandatory Vendors -->
        <script src="./assets/vendors/general/jquery/dist/jquery.js" type="text/javascript"></script>               
        <!--begin::Global Theme Bundle(used by all pages) -->
        <script src="./assets/js/demo1/scripts.bundle.js" type="text/javascript"></script>
        <!--end::Global Theme Bundle -->
        <!--begin::Page Scripts(used by this page) -->
        <!--end::Page Scripts -->
        <script>
            $(document).ready(function (){
                window.print();
            });
        </script>
    </body>
    <!-- end::Body -->
</html>