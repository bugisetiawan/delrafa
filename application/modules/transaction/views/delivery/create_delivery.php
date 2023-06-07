<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Transaksi</h3>
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
            <?php echo form_open_multipart('', ['autocomplete'=>'off']); ?>
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand fa fa-clipboard-list"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">
                        Daftar Penjualan
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">    
                <div class="form-group">
                    <label class="text-dark"><span class="text-danger">*</span>TGL. PENGIRIMAN</label>
                    <div class='input-group'>
                        <?php 
                            $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'date', 'name' => 'date', 'value' => date('d-m-Y'),  'placeholder' => 'Tanggal Pengiriman'); 
                            echo form_input($data);
                            ?>
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="la la-calendar-check-o"></i></span>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered table-hover table-checkable" id="datatable">
                    <thead>
                        <tr style="text-align:center;">
                            <th>NO.</th>
                            <th>
                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
                                <input type="checkbox" class="choose-all">&nbsp;<span></span>
                                </label>
                            </th>
                            <th>TANGGAL</th>
                            <th>NO. TRANSAKSI</th>                        
                            <th>PELANGGAN</th>
                            <th>SALES</th>
                            <th>TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>                                                                                                                 
                    </tbody>
                </table>
            </div>
            <div class="kt-portlet__foot" id="confirm-button">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="<?php echo site_url('delivery'); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>                                        
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-save"></i> SIMPAN</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_close() ?>
        </div>
    </div>
    <!-- end:: Content -->
</div>