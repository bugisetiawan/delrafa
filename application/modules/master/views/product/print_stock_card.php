<!DOCTYPE html>
<html lang="en">
    <!-- begin::Head -->
    <head>
        <!--begin::Base Path (base relative path for assets of this page) -->
        <base href="<?php echo base_url('/'); ?>">
        <!--end::Base Path -->
        <meta charset="utf-8" />
        <title>TRUST System | Kartu Stok</title>
        <meta name="description" content="No aside layout examples">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <style>
            @media print{
            html, body{                
                height :auto;
                width  :100%;
                margin : 0mm !important; 
                padding: 0mm !important;
                overflow: hidden;
                font-family: "Times New Roman", Times, serif;				
                page-break-after: always;
            }
            }
            div.utama{				
            	font-size: 10pt;
            }            
			.kiri
			{
				width:50%;
				height:100px;
				float:left;
			}
			.kanan
			{
				width:50%;
				height:100px;				
				float:right;
			}
        </style>
        <!--end::Fonts -->
    </head>
    <!-- end::Head -->
    <!-- begin::Body -->
    <body>
        <div class="utama">
            <div>
                <h2 style="text-align:center;">KARTU STOK</h2>
                <table width="100%">
                    <tbody>
                        <tr>
                            <td>KODE</td>
                            <td>:</td>
                            <td><?php echo $product['code_p']; ?></td>
                            <td>NAMA</td>
                            <td>:</td>
                            <td><?php echo $product['name_p']; ?></td>
                            <td>SATUAN</td>
                            <td>:</td>
                            <td><?php echo $product['name_u']; ?></td>
                        </tr>                        
                    </tbody>
                </table>
            </div>
            <hr>
			<table width="100%" border="1" cellspacing="0">
                <thead>
                    <tr style="text-align:center;">
                        <th width="10%">NO.</th>
                        <th>TRANSAKSI</th>
                        <th>QTY</th>                        
                        <th>JENIS</th>
                        <th>MASUK/KELUAR</th>
                        <th>STOK</th>
                        <th>USER</th>                                                
                    </tr>
                </thead>
                    <?php $no=1; foreach($stock_card AS $info):?>
                        <tr>
                            <?php 
                            if($info['type'] == 1)
                            {
                                $type = "POS";                                
                            }
                            elseif($info['type'] == 2)
                            {
                                $type = "PEMBELIAN";                                
                            }
                            elseif($info['type'] == 3)
                            {
                                $type = " RETUR PEMBELIAN";                                
                            }
                            elseif($info['type'] == 4)
                            {
                                $type = "PENJUALAN";                                
                            }
                            elseif($info['type'] == 5)
                            {
                                $type = "RETUR PENJUALAN";                                
                            }
                            else
                            {
                                $type = "lainnya";
                            }                                                    
                            ?>
                            <?php 
                            if($info['method'] == 1)
                            {
                                $style = "style ='text-align:center; color:green;'";
                            }
                            else
                            {
                                $style = "style='text-align:center; color:red;'";
                            }
                            ?>
                            <td style="text-align:center;"><?php echo $no; ?></td>                                                    
                            <td><?php echo $info['invoice']; ?></td>                                                    
                            <td <?php echo $style; ?>><?php echo $info['qty']; ?></td>                            
                            <td style="text-align:center;"><?php echo $type; ?></td>
                            <?php 
                            if($info['method'] == 1)
                            {
                                $method = "MASUK";
                            }
                            else
                            {
                                $method = "KELUAR";
                            }
                            ?>
                            <td <?php echo $style; ?>><?php echo $method ?></td>
                            <td style="text-align:center;"><?php echo $info['stock']; ?></td>
                            <td style="text-align:center;"><?php echo $info['code_e']; ?></td>
                        </tr>
                    <?php $no++; endforeach; ?>
                <tbody>										
                </tbody>
            </table>            
        </div>
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