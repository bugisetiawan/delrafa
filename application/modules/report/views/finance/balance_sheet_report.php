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
                            <?php if($this->session->userdata('id_u') == 1): ?>
                            <a href="javascript: void(0);" id="check_unbalance_gl_btn" class="btn btn-warning btn-brand btn-elevate btn-elevate-air">
                                <i class="la la-refresh"></i>
                                <span class="d-none d-sm-inline">Check Unbalance GL</span>
                            </a>        
                            <?php endif; ?>
                            <a href="javascript: void(0);" id="balance_sheet_refresh_btn" class="btn btn-square btn-brand btn-elevate btn-elevate-air">
                            <i class="la la-refresh"></i>
                            <span class="d-none d-sm-inline">Refresh Data</span>
                            </a>                                                        
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="form-group row">
                    <div class="col-md-2">                        
                        <label class="col-form-label text-dark">TANGGAL</label>                        
                    </div>
                    <div class="col-md-10">
                        <div class='input-group'>
                            <?php
                                $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'date', 'value' => date('d-m-Y'),  'placeholder' => 'Tanggal Awal'); 
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
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless table-hover" id="left_table">
                            <tbody>
                                <tr class="font-weight-bold" id="aset_header">
                                    <td colspan="3" style="border-bottom: 1px solid black;" class="bg-dark text-white">ASET</td>
                                </tr>
                                <tr><td>&nbsp;</td></tr>
                                <tr style="border-top: 1px solid black; border-bottom: 1px solid black;">
                                    <td colspan="2" class="font-weight-bold"><h4>Total Aset</h4></td>
                                    <td class="text-right font-weight-bold"><h4 id="total_aset"></h4></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-hover" id="right_table">
                            <tbody>
                                <tr class="font-weight-bold" id="kewajiban_header">
                                    <td colspan="3" style="border-bottom: 1px solid black;" class="bg-dark text-white">KEWAJIBAN</td>
                                </tr>
                                <tr class="font-weight-bold" id="ekuitas_header">
                                    <td colspan="3" style="border-bottom: 1px solid black;" class="bg-dark text-white">EKUITAS</td>
                                </tr>                                
                                <tr><td>&nbsp;</td></tr>
                                <tr style="border-top: 1px solid black; border-bottom: 1px solid black;">
                                    <td colspan="2" class="font-weight-bold"><h4>Total Kewajiban & Ekuitas</h4></td>
                                    <td class="text-right font-weight-bold"><h4 id="total_kewajiban_ekuitas"></h4></td>
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