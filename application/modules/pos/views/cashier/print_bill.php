<!DOCTYPE html>
<html lang="en">
    <!-- begin::Head -->
    <head>
        <!--begin::Base Path (base relative path for assets of this page) -->
        <base href="<?php echo base_url('/'); ?>">
        <!--end::Base Path -->
        <meta charset="utf-8" />
        <title>TRUST System | <?php echo $pos['invoice']; ?></title>
        <meta name="description" content="No aside layout examples">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <style>
            @media print{
            html, body{
            height:100%; 
            margin: 0mm !important; 
            padding: 0mm !important;
            overflow: hidden;
            font-family: "Times New Roman", Times, serif;				
            }
            }
            div.utama{				
            	font-size: 10px;
            }            
			#kiri
			{
				width:50%;
				height:100px;
				float:left;
			}
			#kanan
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
            <center><?= $perusahaan['name']; ?></center>
			<center><?= $perusahaan['address']; ?></center>
			<hr>
			<table width="100%">
				<tbody>
					<tr>
						<center><?php echo "NO. ".$pos['invoice']; ?> | <?php echo date('d-m-Y', strtotime($pos['date'])); ?> | <?php echo date('H:i:s', strtotime($pos['time'])); ?></center>
					</tr>
					<tr>
						<td>KASIR</td>
						<td>:</td>
						<td><?php echo $pos['name_cashier']; ?></td>
					</tr>
				</tbody>
			</table>			
            <hr>
            <table width="100%">
                <tbody>
					<?php foreach($pos_detail as $info): ?>					
                    <tr>
                        <td colspan="10" style="text-align:left;"><?php echo $info['name_p']; ?></td>
                    </tr>
                    <tr>	
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td style="text-align:right;"><?php echo $info['qty']; ?> <?php echo $info['code_u']; ?></td>
						<td style="text-align:center;">x</td>
						<td style="text-align:right;"><?php echo number_format($info['price'],'0','.',','); ?></td>
						<td style="text-align:center;">=</td>
						<td style="text-align:right;"><?= number_format($info['total'],'0','.',','); ?></td>
					</tr>					
					<?php endforeach; ?>					
                </tbody>
            </table>
			<hr>
			<table width="100%">
				<tbody>
					<tr>																	
						<td style="text-align:right;">JUMLAH</td>
						<td style="text-align:center;">:</td>
						<td style="text-align:right;"><?php echo number_format($pos['grandtotal'],'0','0','.'); ?></td>						
					</tr>
					<tr>
						<td style="text-align:right;">BAYAR</td>
						<td style="text-align:center;">:</td>
						<td style="text-align:right;"><?php echo number_format($pos['pay'],'0','0','.'); ?></td>
					</tr>
					<tr>
						<td style="text-align:right;">KEMBALI</td>
						<td style="text-align:center;">:</td>
						<td style="text-align:right;"><?php echo number_format($pos['pay'] - $pos['grandtotal'],'0','0','.'); ?></td>
					</tr>
				</tbody>
			</table>
            <hr>
			<center>TERIMA KASIH</center>
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