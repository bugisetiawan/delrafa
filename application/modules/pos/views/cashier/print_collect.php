<!DOCTYPE html>
<html lang="en">
    <!-- begin::Head -->
    <head>
        <!--begin::Base Path (base relative path for assets of this page) -->
        <base href="<?php echo base_url('/'); ?>">
        <!--end::Base Path -->
        <meta charset="utf-8" />
        <title>TRUST System | Collect</title>
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
            <center><?= $perusahaan['name']; ?></center>
			<center><?= $perusahaan['address']; ?></center>
			<hr>
			<table width="100%">
				<tbody>
					<tr>
						<center><?php echo date('d-m-Y', strtotime($collect['date'])); ?> | <?php echo date('H:i:s', strtotime($collect['time'])); ?></center>
					</tr>
				</tbody>
			</table>			
			<hr>
			<p>TANDA TERIMA PENGAMBILAN UANG SEJUMLAH:</p>
			<center><?php echo number_format($collect['total'], 0, ".", ","); ?></center>
			<p>terbilang: <?php echo $this->global->terbilang($collect['total']); ?></p>
            <hr>
			<div class="kiri">
				<center>COLLECTOR</center><br><br><br><br><br><center><?php echo $collect['collector']; ?></center>
			</div>
			<div class="kanan">
				<center>KASIR</center><br><br><br><br><br><center><?php echo $collect['cashier']; ?></center>
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