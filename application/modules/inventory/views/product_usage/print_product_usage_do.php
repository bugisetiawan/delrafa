<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Cetak DO</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <base href="<?php echo base_url('/'); ?>">
        <link href="./assets/css/printout.min.css" rel="stylesheet" type="text/css" />
        <link href="./assets/vendors/general/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
        <style>
            body{
            color : black;
            font-family: 'Calibri';
            }
            body, #product_table{
            font-size: 15px;
            }
        </style>
    </head>
    <body>
        <?php foreach($sot AS $info_sot): ?>
        <table style="width:100%;">
            <thead>
                <tr>
                    <td class="text-left">PEMAKAIAN | <?php echo date('d-m-Y H:i:s'); ?></b></th>
                </tr>
            </thead>
        </table>
        <table style="width:100%;">
            <tbody>
                <tr>
                    <td style="font-size:22px;"><?php echo $info_sot['code_w']; ?></td>
                </tr>
                <tr>
                    <td><br></td>
                </tr>                
                <tr>
                    <td><?php echo $product_usage['code']; ?></td>
                </tr>
            </tbody>
        </table>
        <br>
        <table style="width:100%;" id="product_table" >
            <tbody>
                <tr id="table-title">
                    <td class="text-center" style="border-top: 1px solid black; border-bottom: 1px solid black;">NO.</td>
                    <td class="text-center" style="border-top: 1px solid black; border-bottom: 1px solid black;">PRODUK</td>
                </tr>
                <?php $no=1; foreach($info_sot['product'] AS $info_product): ?>
                <tr>
                    <td valign="top" class="text-center"><?php echo $no; ?></td>
                    <td valign="top"><?php echo $info_product['name_p']; ?></td>
                </tr>
                <tr>
                    <td colspan="2" valign="top" class="text-right">= <?php echo $info_product['qty']; ?> <?php echo $info_product['code_u']; ?></td>
                </tr>
                <?php $no++; endforeach; ?>
            </tbody>
        </table>
        <br><br><br>
        <table style="width:100%;">
            <tbody>
                <tr>
                    <td>PARAF</td>
                </tr>
            </tbody>
        </table>
        <br><br><br>
        <hr>
        <?php endforeach; ?>        
        <script src="./assets/vendors/general/jquery/dist/jquery.js" type="text/javascript"></script>
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
    </body>
</html>