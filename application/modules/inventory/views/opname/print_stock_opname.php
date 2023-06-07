<!DOCTYPE html>
<html lang="en">
    <!-- begin::Head -->
    <head>
        <meta charset="utf-8" />
        <title>Stock Opname | <?php echo $stock_opname['code']; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <base href="<?php echo base_url('/'); ?>">
        <link href="./assets/css/printout.min.css" rel="stylesheet" type="text/css" />
        <link href="./assets/vendors/general/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
        <style>
            /* Styles go here */
            .page-header, .page-header-space {
                height: 100px;
            }
            .page-footer, .page-footer-space {
                height: 20px;
            }
            .page-footer {
                position: fixed;
                bottom: 0;
                width: 100%;
                border-top: 1px solid black; /* for demo */
            }
            .page-header {
            position: fixed;
            top: 0mm;
            width: 100%;
            border-bottom: 1px solid black; /* for demo */
            background: yellow; /* for demo */
            }

            .page {
            page-break-after: always;
            }

            @media print {
                thead {display: table-header-group;} 
                tfoot {display: table-footer-group;}
                button {display: none;}
                body {margin: 0;}
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
        </style>
    </head>
    <!-- end::Head -->
    <!-- begin::Body -->
    <body>
        <div class="page-footer">
            <small>Waktu Cetak: <?php echo date('d-m-Y H:i:s'); ?> | Opt. <?php echo $this->session->userdata('name_e'); ?></small>
        </div>
        <table>            
            <tbody>
                <tr>
                    <td>
                        <!--*** CONTENT GOES HERE ***-->
                        <div class="page">
                            <div class="row">
                                <div class="col-md-6">                                    
                                    <table style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th class="text-left">
                                                    <?php echo $perusahaan['name']; ?><br>
                                                    <small>Alamat: <?php echo $perusahaan['address']; ?></small><br>
                                                    <small>Telepon: <?php echo $perusahaan['telephone']; ?></small>
                                                </th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="col-md-6">				
                                    <table style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th colspan="3" class="text-right">STOK OPNAME | NO. TRANSAKSI: <?php echo $stock_opname['code']; ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td>Tgl. Stock Opname</td>
                                                <td class="text-center">:</td>
                                                <td><?php echo date('d-m-Y', strtotime($stock_opname['date'])); ?></td>
                                                <td class="text-right">Gudang</td>
                                                <td class="text-center">:</td>
                                                <td><?php echo $stock_opname['warehouse']; ?></td>
                                                <td class="text-right">Petugas</td>
                                                <td class="text-center">:</td>
                                                <td><?php echo $stock_opname['checker']; ?></td>
                                                <td class="text-right">Operator</td>
                                                <td class="text-center">:</td>
                                                <td><?php echo $stock_opname['operator']; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>                            
                            <br>                            
                            <table style="width:100%;" id="product_table" >
                                <thead>                                    
                                    <tr id="table-title">
                                        <th class="text-right">NO.</th>
                                        <th class="text-center">KODE</th>
                                        <th class="text-left">PRODUK</th>
                                        <th colspan="2" class="text-center">STOK TERSEDIA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no=1; foreach($stock_opname_detail AS $info): ?>
                                        <tr>
                                            <td class="text-right"><?php echo $no; ?></td>
                                            <td class="text-center"><?php echo $info['code_p']; ?></td>
                                            <td class="text-left"><?php echo $info['name_p']; ?></td>
                                            <td class="text-right">________</td>
                                            <td class="text-left"> <?php echo $info['name_u']; ?></td>
                                        </tr>                
                                    <?php $no++; endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td>
                        <!--place holder for the fixed-position footer-->
                        <div class="page-footer-space"></div>
                    </td>
                </tr>
            </tfoot>
        </table>
        <!--begin:: Global Mandatory Vendors -->
        <script src="./assets/vendors/general/jquery/dist/jquery.js" type="text/javascript"></script>               
        <!--begin::Global Theme Bundle(used by all pages) -->
        <script src="./assets/js/demo1/scripts.bundle.js" type="text/javascript"></script>
        <!--end::Global Theme Bundle -->
        <!--begin::Page Scripts(used by this page) -->
        <script>
            $(document).ready(function (){
                window.print();
            });
        </script>
        <!--end::Page Scripts -->        
    </body>
    <!-- end::Body -->
</html>