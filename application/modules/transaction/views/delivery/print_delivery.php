<!DOCTYPE html>
<html lang="en">
    <!-- begin::Head -->
    <head>
        <meta charset="utf-8" />
        <title>Cetak Pengiriman</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <base href="<?php echo base_url('/'); ?>">
        <link href="./assets/css/printout.min.css" rel="stylesheet" type="text/css" />
        <link href="./assets/vendors/general/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
        <style>
            body{
                color : black;
                font-family: 'Calibri';
                font-size: 14px;
            }
        </style>
    </head>
    <!-- end::Head -->    
    <!-- begin::Body -->
    <body>
        <table style="width:100%;">
            <thead class="text-center">
                <tr>
                    <td>DAFTAR PENGIRIMAN</td>
                </tr>
                <tr>
                    <td>Tanggal: <?php echo date('d-m-Y', strtotime($delivery['date'])); ?> | No.: <?php echo $delivery['code']; ?></td>
                </tr>
            </thead>
        </table>
		<table style="width: 100%;" border="0">
            <tbody>
                <tr style="border-top: 1px solid black; border-bottom: 1px solid black; text-align:center;">
                    <td width="5">NO.</td>
                    <td>TANGGAL</td>
                    <td>NO. TRANSAKSI</td>
                    <td>PELANGGAN</td>
                    <td>SALES</td>
                    <td>TOTAL</td>
                </tr>
                <?php $grandtotal=0; $no=1; foreach($delivery_detail AS $info): ?>
                <tr>
                    <td valign="top" class="text-right"><?php echo $no.'.'; ?></td>
                    <td valign="top"><?php echo date('d-m-Y', strtotime($info['date'])); ?></td>
                    <td valign="top" class="text-left"><?php echo $info['invoice']; ?></td>
                    <td valign="top" class="text-left"><?php echo $info['name_c']; ?></td>
                    <td valign="top" class="text-left"><?php echo $info['name_s']; ?></td>
                    <td valign="top" class="text-right"><?php echo number_format($info['grandtotal'], 2, ".", ","); ?></td>
                </tr>
                <?php $grandtotal = $grandtotal+$info['grandtotal']; $no++; endforeach; ?>
                <tr style="border-top: 1px solid black;">
                    <td colspan="5" class="text-right">TOTAL</td>
                    <td class="text-right"><?php echo number_format($grandtotal, 2, ".", ","); ?></td>
                </tr>
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