<!DOCTYPE html>
<html lang="en">
    <!-- begin::Head -->
    <head>
        <base href="<?php echo base_url('/'); ?>">
        <meta charset="utf-8" />
        <title>TRUST System | <?php echo $title; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">        
        <link href="./assets/css/printout.min.css" rel="stylesheet" type="text/css" />
        <link href="./assets/vendors/general/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
        <style>
            /* Styles go here */
            .page-header, .page-header-space {
                height: 50px;
            }
            .page-footer, .page-footer-space {
                height: 20px;
            }
            .page-header {
                position: fixed;
                top: 0mm;
                width: 100%;
                 border-bottom: 1px solid black;
            }
            .page-footer {
                position: fixed;
                bottom: 0;
                width: 100%;
                border-top: 1px solid black;
            }
            .page {
                page-break-after: always;
            }                        
            .content {
                margin-bottom:10px;
            }
            @media print {
                thead {display: table-header-group;} 
                tfoot {display: table-footer-group;}
                button {display: none;}
                html, body{
                    height : auto;
                    width  : auto;
                    margin : 0; 
                    padding: 0;
                    font-family: "Calibri";
                    font-size: 14px;
                }
            }
        </style>
    </head>
    <!-- end::Head -->
    <!-- begin::Body -->
    <body>        
        <div class="page-header">
            <table style="width:100%;">
                <thead>
                    <tr>
                        <th class="text-left" style="font-size:18px;">LAPORAN DETAIL PEMBELIAN</th>
                    </tr>
                </thead>
            </table>
            <table style="width:100%">
                <tbody>
                    <tr>
                        <td>Periode Transaksi : <?php echo date('d-m-Y', strtotime($filter['from_date'])); ?> s.d. <?php echo date('d-m-Y', strtotime($filter['to_date'])); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="page-footer">
            <small>Waktu Cetak: <?php echo date('d-m-Y H:i:s'); ?> | Opt. <?php echo $this->session->userdata('name_e'); ?></small>
        </div>
        <table>    
            <thead>
                <tr>
                    <td>
                        <!--place holder for the fixed-position header-->
                        <div class="page-header-space"></div>
                    </td>
                </tr>
            </thead>        
            <tbody>
                <tr>
                    <td>                        
                        <div class="page">
                            <table style="width: 100%;" celpadding="1" cellspacing="1">
                                <tbody>
                                <?php $grandtotal = 0; foreach($data AS $info): ?>                                                                
                                    <tr>
                                        <td>Tgl. : <b><?php echo date('d-m-Y', strtotime($info['purchase']['date'])); ?></b></td>                                            
                                        <td>No. Transaksi : <b><?php echo $info['purchase']['code']; ?></b></td>
                                        <td>No. Refrensi : <b><?php echo $info['purchase']['invoice']; ?></b></td>
                                        <td>Supplier : <b><?php echo $info['purchase']['name_s']; ?></b></td> 
                                        <?php 
                                            if($info['purchase']['ppn'] == 1)
                                            {
                                                $ppn = "PPN";
                                            }
                                            elseif($info['purchase']['ppn'] == 2)
                                            {
                                                $ppn = "FINAL";
                                            }
                                            else
                                            {
                                                $ppn = "NON";
                                            }
                                        ?>
                                        <td>PPN : <b><?php echo $ppn; ?></b></td>
                                    </tr>   
                                    <tr style="border-bottom: 1px solid black;">
                                        <td colspan="5">
                                            <table style="width: 100%;" cellpadding="1" cellspacing="1">
                                                <thead>
                                                    <tr>
                                                        <td width="10"><u>No.</u></td>
                                                        <td width="100"><u>Kode</u></td>
                                                        <td><u>Nama</u></td>
                                                        <td width="100" colspan="2" class="text-center"><u>Qty</u></td>
                                                        <td width="80" class="text-right"><u>Harga</u></td>
                                                        <td width="150" class="text-right"><u>Gudang</u></td>
                                                        <td width="80" class="text-right"><u>Total</u></td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php $subtotal=0; $no=1; foreach($info['detail_purchase'] AS $info2): ?>                                                    
                                                    <tr>
                                                        <td valign="top" class="text-right"><?php echo $no; ?>.</td>
                                                        <td valign="top"><?php echo $info2['code_p']; ?></td>
                                                        <td valign="top"><?php echo $info2['name_p']; ?></td>
                                                        <td valign="top" width="50" class="text-right"><?php echo number_format($info2['qty'],2,".",","); ?></td>
                                                        <td valign="top" width="50" class="text-left">&nbsp;<?php echo $info2['name_u']; ?></td>
                                                        <td valign="top" class="text-right"><?php echo number_format($info2['price'],0,".",","); ?></td>
                                                        <td valign="top" class="text-right"><?php echo $info2['name_w']; ?></td>
                                                        <td valign="top" class="text-right"><?php echo number_format($info2['total'],0,".",","); ?></td>
                                                    </tr>
                                                    <?php $no++; endforeach; ?>
                                                    <tr style="border-top: 1px dashed black;">
                                                        <td colspan="7">&nbsp;</td>  
                                                        <td class="text-right"><?php echo number_format($info['purchase']['total_price'],0,".",","); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" class="text-right">Diskon (%):</td>
                                                        <td> <?php echo number_format($info['purchase']['discount_p'],2,".",","); ?> %</td>
                                                        <td colspan="2" class="text-right">Diskon (Rp):</td>
                                                        <td> <?php echo number_format($info['purchase']['discount_rp'],0,".",","); ?></td>
                                                        <td class="text-right">TOTAL AKHIR:</td>
                                                        <td class="text-right"> <?php echo number_format($info['purchase']['grandtotal'],0,".",","); ?></td>
                                                        <?php $grandtotal = $grandtotal + $info['purchase']['grandtotal']; ?>
                                                    </tr>
                                                </tbody>                                            
                                            </table> 
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                    <tr>
                                        <td colspan="3">&nbsp;</td>  
                                        <td class="text-right">TOTAL KESELURUHAN</td>
                                        <td>
                                            <table style="width: 100%;">
                                                <tbody>
                                                    <tr>
                                                        <td class="text-right"><?php echo number_format($grandtotal,0,".",","); ?></td>                                                        
                                                    </tr>                                                    
                                                </tbody>
                                            </table>
                                        </td>                                                                                                                      
                                    </tr>
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