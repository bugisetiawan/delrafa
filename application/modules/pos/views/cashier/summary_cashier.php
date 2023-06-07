<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">    
    <!-- begin:: Content -->    
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">                
        <div class="container">            
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__body">
                    <ul class="nav nav-pills nav-fill" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#summary" role="tab">INFORMASI KASIR</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">PEMASUKAN</a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" data-toggle="tab" href="#dp">UANG MUKA</a>
                                <a class="dropdown-item" data-toggle="tab" href="#sales">PENJUALAN</a>
                                <a class="dropdown-item" data-toggle="tab" href="#pos">POS</a>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">PENGELUARAN</a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" data-toggle="tab" href="#sales_return">RETUR PENJUALAN</a>
                                <a class="dropdown-item" data-toggle="tab" href="#expense">PENGELUARAN BIAYA</a>
                                <a class="dropdown-item" data-toggle="tab" href="#collect">COLLECT</a>
                            </div>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="summary" role="tabpanel">
                            <table class="table">
                                <tbody>
                                    <tr class="kt-font-dark kt-font-bold">
                                        <td class="kt-font-dark kt-font-bold">KASIR</td>
                                        <td class="kt-font-dark kt-font-bold text-center">:</td>
                                        <td class="kt-font-dark kt-font-bold"><b><?php echo $cashier['name_c']; ?></b></td>
                                        <td class="kt-font-dark kt-font-bold text-right">JAM BUKA</td>
                                        <td class="kt-font-dark kt-font-bold text-center">:</td>
                                        <td class="kt-font-dark kt-font-bold"><b><?php echo $cashier['open_time']; ?></b></td>                                        
                                    </tr>
                                    <tr>                                        
                                        <td class="kt-font-dark kt-font-bold">TANGGAL</td>
                                        <td class="kt-font-dark kt-font-bold text-center">:</td>
                                        <td class="kt-font-dark kt-font-bold"><b><?php echo date('d-m-Y', strtotime($cashier['date'])); ?></b></td>
                                        <td class="kt-font-dark kt-font-bold text-right">JAM TUTUP</td>
                                        <td class="kt-font-dark kt-font-bold text-center">:</td>
                                        <td class="kt-font-dark kt-font-bold"><b><?php echo $cashier['close_time']; ?></b></td>
                                    </tr>
                                    <tr>                                        
                                    </tr>                                                                
                                </tbody>
                            </table>
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="kt-font-success">PEMASUKAN</h5>                            
                                    <table class="table">
                                        <tbody>                                
                                            <tr>
                                                <td class="kt-font-dark kt-font-bold">MODAL KASIR</td>
                                                <td class="kt-font-dark kt-font-bold text-center">:</td>
                                                <td class="kt-font-success kt-font-bold text-right"><?php echo number_format($cashier['modal'], 0, ".", ","); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="kt-font-dark kt-font-bold">UANG MUKA</td>
                                                <td class="kt-font-dark kt-font-bold text-center">:</td>
                                                <td class="kt-font-success kt-font-bold text-right"><?php echo number_format($cashier['total_dp'], 0, ".", ","); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="kt-font-dark kt-font-bold">PENJUALAN</td>
                                                <td class="kt-font-dark kt-font-bold text-center">:</td>
                                                <td class="kt-font-success kt-font-bold text-right"><?php echo number_format($cashier['total_sales'],0, ".", ","); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="kt-font-dark kt-font-bold">POS</td>
                                                <td class="kt-font-dark kt-font-bold text-center">:</td>
                                                <td class="kt-font-success kt-font-bold text-right"><?php echo number_format($cashier['total_pos'],0, ".", ","); ?></td>
                                            </tr>                                
                                            <tr>
                                                <td class="kt-font-dark kt-font-bold" colspan="2"></td>
                                                <?php $in = $cashier['modal']+$cashier['total_dp']+$cashier['total_sales']+$cashier['total_pos'];   ?>
                                                <td class="kt-font-success kt-font-bold text-right"><?php echo number_format($in, 0, ".", ","); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="kt-font-danger">PENGELUARAN</h5>   
                                    <table class="table">
                                        <tbody>                                
                                            <tr>
                                                <td class="kt-font-dark kt-font-bold">RETUR PENJUALAN</td>
                                                <td class="kt-font-dark kt-font-bold text-center">:</td>
                                                <td class="kt-font-danger kt-font-bold text-right"><?php echo number_format($cashier['total_sales_return'], 0, ".", ","); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="kt-font-dark kt-font-bold">BIAYA</td>
                                                <td class="kt-font-dark kt-font-bold text-center">:</td>
                                                <td class="kt-font-danger kt-font-bold text-right"><?php echo number_format($cashier['total_expense'], 0, ".", ","); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="kt-font-dark kt-font-bold">COLLECT</td>
                                                <td class="kt-font-dark kt-font-bold text-center">:</td>
                                                <td class="kt-font-danger kt-font-bold text-right"><?php echo number_format($cashier['total_collect'],0, ".", ","); ?></td>
                                            </tr>                                                               
                                            <tr>
                                                <td class="kt-font-dark kt-font-bold" colspan="2"></td>
                                                <?php $out = $cashier['total_sales_return']+$cashier['total_expense']+$cashier['total_collect'];   ?>
                                                <td class="kt-font-danger kt-font-bold text-right"><?php echo number_format($out, 0, ".", ","); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="text-dark">TOTAL SETORAN :</h4>
                                </div>
                                <div class="col-md-6 text-right">
                                <h4 class="text-primary kt-font-bold"><?php echo number_format($cashier['grandtotal'],0, ".", ","); ?></h4>   
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="dp" role="tabpanel">
                            <h5 class="text-dark">Daftar Penerimaan Uang Muka</h5>
                            <hr>
                            <table class="table table-hover" id="dp_table">
                                <thead>
                                    <tr class="text-center">
                                        <th class="kt-font-dark">NO.</th>
                                        <th class="kt-font-dark">NO. TRANSAKSI</th>
                                        <th class="kt-font-dark">UANG MUKA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no=1; foreach($dp AS $info_dp): ?>
                                    <tr>
                                        <td width="10" class="text-dark"><?php echo $no; ?></td>
                                        <td class="text-dark text-center"><?php echo $info_dp['invoice']; ?></td>
                                        <td class="text-primary text-right"><?php echo number_format($info_dp['down_payment'],0, ".", ","); ?></td>
                                    </tr>
                                    <?php $no++; endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="sales" role="tabpanel">
                            <h5 class="text-dark">Daftar Transaksi Penjualan</h5>
                            <hr>
                            <table class="table table-hover" id="dp_table">
                                <thead>
                                    <tr class="text-center">
                                        <th class="kt-font-dark">NO.</th>
                                        <th class="kt-font-dark">NO. TRANSAKSI</th>
                                        <th class="kt-font-dark">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no=1; foreach($sales AS $info_sales): ?>
                                    <tr>
                                        <td width="10" class="text-dark"><?php echo $no; ?></td>
                                        <td class="text-dark text-center"><?php echo $info_sales['invoice']; ?></td>
                                        <td class="text-primary text-right"><?php echo number_format($info_sales['grandtotal'],0, ".", ","); ?></td>
                                    </tr>
                                    <?php $no++; endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="pos" role="tabpanel">
                            <h5 class="text-dark">Daftar Transaksi POS</h5>
                            <hr>
                            <table class="table table-hover" id="pos_table">
                                <thead>
                                    <tr class="text-center">
                                        <th class="kt-font-dark">NO.</th>
                                        <th class="kt-font-dark">NO. TRANSAKSI</th>
                                        <th class="kt-font-dark">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no=1; foreach($pos AS $info_pos): ?>
                                    <tr>
                                        <td width="10" class="text-dark"><?php echo $no; ?></td>
                                        <td class="text-dark text-center"><?php echo $info_pos['invoice']; ?></td>
                                        <td class="text-primary text-right"><?php echo number_format($info_pos['grandtotal'],0, ".", ","); ?></td>
                                    </tr>
                                    <?php $no++; endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="sales_return" role="tabpanel">
                            <h5 class="text-dark">Daftar Transaksi Retur Penjualan</h5>
                            <hr>
                            <table class="table table-hover" id="sales_return_table">
                                <thead>
                                    <tr class="text-center">
                                        <th class="kt-font-dark">NO.</th>
                                        <th class="kt-font-dark">NO. TRANSAKSI</th>
                                        <th class="kt-font-dark">TOTAL RETUR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no=1; foreach($sales_return AS $info_sales_return): ?>
                                    <tr>
                                        <td width="10" class="text-dark"><?php echo $no; ?></td>
                                        <td class="text-dark text-center"><?php echo $info_sales_return['code']; ?></td>
                                        <td class="text-primary text-right"><?php echo number_format($info_sales_return['total_return'],0, ".", ","); ?></td>
                                    </tr>
                                    <?php $no++; endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="expense" role="tabpanel">   
                            <h5 class="text-dark">Daftar Pengeluaran Biaya</h5>
                            <hr>  
                            <table class="table table-hover" id="expense_table">
                                <thead>
                                    <tr class="text-center">
                                        <th class="kt-font-dark">NO.</th>
                                        <th class="kt-font-dark">BIAYA</th>
                                        <th class="kt-font-dark">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no=1; foreach($expense AS $info_expense): ?>
                                    <tr>
                                        <td width="10" class="text-dark"><?php echo $no; ?></td>
                                        <td class="text-dark"><?php echo $info_expense['cost']; ?></td>
                                        <td class="text-primary text-right"><?php echo number_format($info_expense['amount'],0, ".", ","); ?></td>
                                    </tr>
                                    <?php $no++; endforeach; ?>
                                </tbody>
                            </table>                                                 
                        </div>
                        <div class="tab-pane" id="collect" role="tabpanel">                            
                            <h5 class="text-dark">Daftar Collect</h5>
                            <hr>
                            <table class="table table-hover" id="collect_table">
                                <thead>
                                    <tr class="text-center">
                                        <th class="kt-font-dark">NO.</th>
                                        <th class="kt-font-dark">COLLECTOR</th>
                                        <th class="kt-font-dark">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no=1; foreach($collect AS $info_collect): ?>
                                    <tr>
                                        <td width="10" class="text-dark"><?php echo $no; ?></td>
                                        <td class="text-dark"><?php echo $info_collect['collector']; ?></td>
                                        <td class="text-primary text-right"><?php echo number_format($info_collect['total'],0, ".", ","); ?></td>
                                    </tr>
                                    <?php $no++; endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="kt-portlet__foot">
                    <div class="row">
                        <div class="col-md-4">
                            <a href="<?php echo site_url('dashboard'); ?>" class="btn btn-outline-primary btn-elevate-hover btn-square form-control"><i class="fa fa-arrow-left"></i> DASHBOARD</a>
                        </div>
                        <div class="col-md-4">
                            <a href="<?php echo site_url('pos/cashier/summary/print/'.encrypt_custom($cashier['id'])); ?>" target="_blank" class="btn btn-outline-success btn-elevate-hover btn-square form-control"><i class="fa fa-print"></i>CETAK</a>
                        </div>
                        <div class="col-md-4">
                            <a href="<?php echo site_url('pos/cashier/'); ?>" class="btn btn-outline-info btn-elevate-hover btn-square form-control"><i class="fa fa-print"></i>BUKA KASIR BARU</a>
                        </div>
                    </div>              
                </div>
            </div>
        </div>        
    </div>
    <!-- end:: Content -->           
</div>