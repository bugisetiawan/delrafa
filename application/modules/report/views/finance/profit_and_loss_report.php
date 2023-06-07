<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Laporan</b></h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>            
            <h3 class="kt-subheader__title"><b><?php echo $title; ?></b></h3>
        </div>
    </div>
    <!-- end:: Content Head -->    
    <!-- begin:: Content -->
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
        <?php if($this->session->flashdata('success')) :?>
        <div class="alert alert-success fade show" role="alert">
            <div class="alert-icon"><i class="flaticon2-checkmark"></i></div>
            <div class="alert-text"><?php echo $this->session->flashdata('success'); ?></div>
            <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="la la-close"></i></span>
                </button>
            </div>
        </div>
        <?php elseif($this->session->flashdata('error')): ?>
        <div class="alert alert-danger fade show" role="alert">
            <div class="alert-icon"><i class="flaticon-warning"></i></div>
            <div class="alert-text"><?php echo $this->session->flashdata('error'); ?></div>
            <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="la la-close"></i></span>
                </button>
            </div>
        </div>
        <?php endif;?>
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand fa fa-info"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">Informasi</h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">                           
                            <a href="javascript: void(0);" onclick="profit_and_loss_report();" class="btn btn-square btn-brand btn-elevate btn-elevate-air">
                            <i class="la la-refresh"></i>
                            <span class="d-none d-sm-inline">Refresh Data</span>
                            </a>                                                        
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="form-group row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-2">
                                <label class="col-form-label text-dark">PERIODE DARI</label>
                            </div>
                            <div class="col-md-10">
                                <div class='input-group'>
                                    <?php 
                                        $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'from_date', 'name' => 'from_date', 'value' => date('01-m-Y'),  'placeholder' => 'Tanggal Awal'); 
                                        echo form_input($data);
                                        ?>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="la la-calendar-check-o"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>                                                
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-2 text-right">
                                <label class="col-form-label text-dark">HINGGA</label>
                            </div>
                            <div class="col-md-10">
                                <div class='input-group'>
                                    <?php 
                                        $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'to_date', 'name' => 'to_date', 'value' => date('t-m-Y'),  'placeholder' => 'Tanggal Akhir');
                                        echo form_input($data);
                                        ?>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="la la-calendar-check-o"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>                                                
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-borderless table-hover" id="profit_and_lost_table">
                            <tbody>
                                <tr class="font-weight-bold">
                                    <td colspan="4" style="border-bottom: 1px solid black;" class="bg-dark text-white">PENDAPATAN</td>
                                </tr>
                                <tr class="font-weight-bold">
                                    <td width="5%"></td>
                                    <td colspan="3" style="border-bottom: 1px solid black;">PENJUALAN</td>
                                </tr>
                                <tr>
                                    <td width="5%"></td>
                                    <td width="5%"></td>
                                    <td>Penjualan</td>
                                    <td class="text-right" id="total_sales"></td>
                                </tr>
                                <tr>
                                    <td width="5%"></td>
                                    <td width="5%"></td>
                                    <td>Retur Penjualan</td>
                                    <td class="text-right" id="total_sales_return"></td>
                                </tr>
                                <tr class="font-weight-bold">
                                    <td width="5%"></td>
                                    <td colspan="3" style="border-bottom: 1px solid black;">PENDAPATAN LAIN-LAIN</td>
                                </tr>
                                <tr>
                                    <td width="5%"></td>
                                    <td width="5%"></td>
                                    <td>Pendapatan Lain-lain</td>
                                    <td class="text-right" id="total_other_income"></td>
                                </tr>                                
                                <tr style="border-top: 1px solid black;" class="font-weight-bold">
                                    <td colspan="3" class="font-weight-bold">TOTAL PENDAPATAN</td>
                                    <td class="text-right" id="net_sales"></td>
                                </tr>
                                <tr><td colspan="4">&nbsp;</td></tr> 
                                <tr class="font-weight-bold">
                                    <td colspan="4" style="border-bottom: 1px solid black;" class="bg-dark text-white">BEBAN POKOK PENJUALAN</td>
                                </tr>
                                <tr>
                                    <td width="5%"></td>
                                    <td width="5%"></td>
                                    <td>Harga Pokok Penjualan</td>
                                    <td class="text-right" id="total_hpp"></td>
                                </tr>
                                <tr style="border-top: 1px solid black;" class="font-weight-bold">
                                    <td colspan="3" class="font-weight-bold">TOTAL BEBAN POKOK PENDAPATAN</td>
                                    <td class="text-right" id="total_net_hpp"></td>
                                </tr>
                                <tr><td colspan="4">&nbsp;</td></tr> 
                                <tr style="border-top: 1px solid black; border-bottom: 1px solid black;" class="font-weight-bold">
                                    <td colspan="3" class="font-weight-bold"><h4>Laba Kotor</h4></td>
                                    <td class="text-right"><h4 id="gross_profit"></h4></td>
                                </tr>
                                <tr><td colspan="3">&nbsp;</td></tr> 
                                <tr class="font-weight-bold" id="expense_header">
                                    <td colspan="4" style="border-bottom: 1px solid black;" class="bg-dark text-white">BIAYA/BEBAN</td>
                                </tr>
                                <tr style="border-top: 1px solid black;" class="font-weight-bold">
                                    <td colspan="3" class="font-weight-bold">Total Biaya/Beban</td>
                                    <td class="text-right" id="total_expense"></td>
                                </tr>
                                <tr class="font-weight-bold">
                                    <td colspan="4" style="border-bottom: 1px solid black;" class="bg-dark text-white">BIAYA/BEBAN LAIN-LAIN</td>
                                </tr>
                                <tr>
                                    <td width="5%"></td>
                                    <td width="5%"></td>
                                    <td>Penyesuaian Persediaan</td>
                                    <td class="text-right" id="total_stock_opname"></td>
                                </tr>
                                <tr><td colspan="3">&nbsp;</td></tr> 
                                <tr style="border-top: 1px solid black; border-bottom: 1px solid black;" class="font-weight-bold">
                                    <td colspan="3" class="font-weight-bold"><h4>Laba Bersih</h4></td>
                                    <td class="text-right"><h4 id="net_profit"></h4></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end:: Content -->    
</div>