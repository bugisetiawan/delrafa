<!DOCTYPE html>
<html lang="en">
    <!-- begin::Head -->
    <head>
        <meta charset="utf-8" />
        <title>Daftar Penagihan</title>
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
                    <td>DAFTAR PENAGIHAN</td>
                </tr>
                <tr>
                    <td>Tanggal: <?php echo date('d-m-Y', strtotime($sales_billing['date'])); ?> | No: <?php echo $sales_billing['code']; ?> | Sales: <?php echo $sales_billing['name_s']; ?></td>
                </tr>
            </thead>
        </table>
		<table style="width: 100%;" border="0">
            <tbody>
                <tr style="border-top: 1px solid black; border-bottom: 1px solid black; text-align:center;">
                    <th width="5">NO.</th>                    
                    <th colspan="2">PELANGGAN</th>
                    <th>ALAMAT</th>
                    <th>TANGGAL</th>
                    <th>NO. TRANSAKSI</th>
                    <th>JATUH TEMPO</th>
                    <th width="5">UMUR</th>
                    <th>PIUTANG</th>                    
                    <th>KETERANGAN</th>
                </tr>
                <?php $grandtotal=0; $no=1; foreach($sales_billing_detail AS $info): ?>
                <tr>
                    <td valign="top"><?php echo $no.'.'; ?></td>
                    <td valign="top" class="text-center"><?php echo $info['code_c']; ?></td>
                    <td valign="top"><?php echo $info['name_c']; ?></td>
                    <td valign="top"><?php echo $info['address']; ?></td>
                    <td valign="top" class="text-center"><?php echo date('d-m-Y', strtotime($info['date'])); ?></td>
                    <td valign="top" class="text-center"><?php echo $info['invoice']; ?></td>
                    <td valign="top" class="text-center"><?php echo date('d-m-Y', strtotime($info['due_date'])); ?></td>
                    <td valign="top"><?php echo $info['remaining_time']; ?></td>
                    <td valign="top" class="text-right"><?php echo number_format($info['account_payable'], 2, ".", ","); ?></td>
                    <td></td>
                </tr>
                <?php $grandtotal = $grandtotal+$info['account_payable']; $no++; endforeach; ?>
                <tr style="border-top: 1px solid black;">
                    <td colspan="8" class="text-right">TOTAL</td>
                    <td class="text-right"><?php echo number_format($grandtotal, 2, ".", ","); ?></td>
                    <td></td>
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