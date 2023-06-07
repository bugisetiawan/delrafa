<!DOCTYPE html>
<html lang="en">
    <!-- begin::Head -->
    <head>
        <!--begin::Base Path (base relative path for assets of this page) -->
        <base href="<?php echo base_url('/'); ?>">
        <!--end::Base Path -->
        <meta charset="utf-8" />
        <title>TRUST System | Retur Pembelian</title>
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
                <h2 style="text-align:center;">RETUR PEMBELIAN</h2>
                <table width="100%">
                    <tbody>
                        <tr>
                            <td>SUPPLIER</td>
                            <td style="text-align:center;">:</td>
                            <td><?php echo $purchase_return['name_s']; ?> </td>
                            <td style="text-align:right;">TANGGAL</td>
                            <td style="text-align:center;">:</td>
                            <td><?php echo date('d-m-Y', strtotime($purchase_return['date'])); ?> </td>
                            <td style="text-align: right;">NOMOR</td>
                            <td style="text-align:center;">:</td>
                            <td style="text-align: right;"><?php echo $purchase_return['code']; ?> </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <hr>            
			<table width="100%" border="1" cellspacing="0">
                <thead>
                    <tr style="text-align:center;">
                        <th>NO.</th>
                        <th>KODE</th>
                        <th>NAMA</th>                                              
                        <th>QTY</th>                                                
                        <th>HARGA BELI</th>
                        <th>TOTAL</th>
                        <th>KETERANGAN</th>
                    </tr>
                </thead>				
                <tbody>
                    <?php $no=1; foreach($purchase_return_detail AS $info): ?>
                        <tr>
                            <td style="text-align: center;"><?php echo $no; ?></td>
                            <td style="text-align: center;"><?php echo $info['code_p']; ?></td>
                            <td><?php echo $info['name_p']; ?></td>                            
                            <td style="text-align: center;"> <?php echo $info['qty']; ?></td>
                            <td style="text-align: right;"> <?php echo number_format($info['buyprice'],0,",","."); ?></td>
                            <td style="text-align: right;"> <?php echo number_format($info['total'],0,",","."); ?></td>
                            <td><?php echo $info['information']; ?></td>
                        </tr>
                    <?php $no++; endforeach; ?>
                    <tr>
                        <td colspan="3"  style="text-align: right;">TOTAL</td>
                        <td style="text-align: center;"><?php echo $purchase_return['total_qty']; ?></td>
                        <td></td>
                        <td style="text-align: right;"><?php echo number_format($purchase_return['total_return'],0,",","."); ?></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>                        
            <div class="kanan" style="text-align: center;">
                <div class="kiri" style="text-align: center;">                    
                    <h4>YANG MERETUR</h4>
                    <br>
                    <br>
                    ( <?php echo $purchase_return['name_e']; ?> )
                </div>
                <div class="kanan" style="text-align: center;">
                    <h4>PENERIMA RETUR</h4>
                    <br>
                    <br>
                    (______________________)
                </div>                
            </div>
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