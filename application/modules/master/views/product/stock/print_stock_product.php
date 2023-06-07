<!DOCTYPE html>
<html lang="en">
    <!-- begin::Head -->
    <head>
        <meta charset="utf-8" />
        <title>Cetak Daftar Stok Produk</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <base href="<?php echo base_url('/'); ?>">
        <link href="./assets/css/printout.min.css" rel="stylesheet" type="text/css" />
        <link href="./assets/vendors/general/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
        <style>
            body{
                color : black;
                font-family: 'Calibri';
                font-size: 12px;
            }
        </style>
    </head>
    <!-- end::Head -->    
    <!-- begin::Body -->
    <body>
        <table style="width:100%;">
            <thead class="text-center">
                <tr>
                    <td>DAFTAR STOK PRODUK</td>
                </tr>
                <tr>
                    <td>Per Tanggal: <?php echo date('d-m-Y'); ?> & Waktu: <?php echo date('H:i:s'); ?></td>
                </tr>
                <tr>
                    <td>Gudang: <?php echo $warehouse; ?></td>
                </tr>
                <tr>
                    <td>Dept: <?php echo $department; ?> | Subdept: <?php echo $subdepartment; ?></td>
                </tr>
            </thead>
        </table>
		<table style="width: 100%;" border="0">
            <tbody>
                <tr style="border-top: 1px solid black; border-bottom: 1px solid black; text-align:center;">
                    <td width="5">NO.</td>
                    <td>NAMA</td>
                    <td colspan="2" width="80" class="text-center">STOK</td>
                </tr>
                <?php $nomor_produk=1; foreach($stock_product AS $info): ?>
                <tr>
                    <td valign="top" class="text-right"><?php echo $nomor_produk.'.'; ?></td>
                    <td valign="top"><?php echo $info['name_p'] ?></td>
                    <td valign="top" class="text-right"><?php echo number_format($info['qty'],2,".",",");; ?></td>
                    <td valign="top" class="text-left"><?php echo $info['code_u']; ?></td>
                </tr>
                <?php $nomor_produk++; endforeach; ?>
            </tbody>
        </table>
        <!--begin:: Global Mandatory Vendors -->
        <script src="./assets/vendors/general/jquery/dist/jquery.js" type="text/javascript"></script>               
        <!--begin::Global Theme Bundle(used by all pages) -->
        <script src="./assets/js/demo1/scripts.bundle.js" type="text/javascript"></script>
        <!--end::Global Theme Bundle -->
        <script>
            $(document).ready(function (){
                window.print();
            });
        </script>
    </body>
    <!-- end::Body -->    
</html>