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
        <div class="kt-portlet kt-portlet--mobile" id="filter_portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand fa fa-info"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">
                        Informasi
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                            <a href="javascript: void(0);" onclick="$('#datatable').DataTable().ajax.reload(); total_customer_sales_receivable();" class="btn btn-square btn-brand btn-elevate btn-elevate-air">
                            <i class="la la-refresh"></i>
                            <span class="d-none d-sm-inline">Refresh Data</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <?php echo form_open_multipart('', ['id' => 'filter_form', 'autocomplete'=>'off']); ?>
                <div class="row">                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="kt-font-dark">PERIODE TRANSAKSI</label>
                            <div class='input-group'>
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'from_date', 'name' => 'from_date', 'value' => date('d-m-Y'),  'placeholder' => 'Tanggal Awal'); 
                                    echo form_input($data);
                                ?>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="la la-calendar-check-o"></i></span>
                                </div>
                            </div>
                        </div>                                        
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="kt-font-dark">HINGGA</label>
                            <div class='input-group'>
                                <?php 
                                    $data = array('type' => 'text', 'class' => 'form-control date', 'id' => 'to_date', 'name' => 'to_date', 'value' => date('d-m-Y'),  'placeholder' => 'Tanggal Akhir');
                                    echo form_input($data);
                                ?>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="la la-calendar-check-o"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>                                                               
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="kt-font-dark">PELANGGAN</label>
                            <select class="form-control" name="customer_code" id="customer_code"></select>
                        </div>                                        
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="text-dark">SALES</label>
                            <select class="form-control" name="sales_code" id="sales_code"></select>
                        </div>                                                                 
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="text-dark">ZONA</label>
                            <select class="form-control" name="zone_id" id="zone_id">
                                <option value="">- PILIH ZONA -</option>
                            </select>
                        </div>                                                                 
                    </div>
                </div>
                <?php echo form_close(); ?>
                <div class="row row-no-padding row-col-separator-sm">
                    <div class="col-md-6 col-lg-6 col-xl-6">                        
                        <div class="kt-widget24">
                            <div class="kt-widget24__details">
                                <div class="kt-widget24__info">
                                    <h4 class="kt-widget24__title text-danger font-weight-bold">
                                        TOTAL PIUTANG (SISA TAGIHAN)
                                    </h4>
                                </div>
                                <span class="kt-widget24__stats kt-font-danger font-weight-bold">
                                    <span id="total_account_payable"></span>
                                </span>
                            </div>
                        </div>                        
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-6">
                        <div class="kt-widget24">
                            <div class="kt-widget24__details">
                                <div class="kt-widget24__info">
                                    <h4 class="kt-widget24__title text-primary font-weight-bold">
                                        TOTAL PENJUALAN
                                    </h4>
                                </div>
                                <span class="kt-widget24__stats kt-font-primary font-weight-bold">
                                    <span id="total_grandtotal"></span>
                                </span>
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
                        <table class="table table-bordered table-hover" id="datatable">
                            <thead>
                                <tr style="text-align:center;">
                                    <th class="notexport">NO.</th>
                                    <th>NAMA</th>
                                    <th>PENJUALAN</th>
                                    <th>PIUTANG</th>
                                    <th>PIUTANG CEK/GIRO</th>                                    
                                    <th>SALES</th>
                                    <th>ZONA</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>            
        </div>        
    </div>
    <!-- end:: Content -->    
</div>