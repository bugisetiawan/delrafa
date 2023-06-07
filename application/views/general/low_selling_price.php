<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Umum</h3>
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
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        <i class="fa fa-clipboard-list text-primary"></i>
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <button class="btn btn-square btn-brand btn-elevate btn-elevate-air" id="btn-refresh" onclick="window.location.reload();">
                        <i class="la la-refresh"></i>
                        <span class="d-none d-sm-inline">Refresh Data</span>
                    </button>  
                </div>
            </div>
            <div class="kt-portlet__body">
                <table class="table table-sm table-bordered table-hover" id="datatable">
                    <thead>
                        <tr style="text-align:center;">
                            <th class="text-dark">NO</th>
                            <th class="text-dark">KODE</th>
                            <th class="text-dark">NAMA</th>
                            <th class="text-dark">SATUAN</th>
                            <?php for($h=1;$h<=5;$h++): ?>
                                <?php if($this->system->check_access('view_sellprice_'.$h, 'read')): ?>
                                <th class="text-dark">H<?php echo $h; ?></th>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </tr>
                    </thead>
                    <tbody> 
                        <?php $no=1; foreach($low_selling_price AS $info): ?>
                        <tr>
                            <td class="text-center" width="10%"><?php echo $no; ?></td>                                
                            <td class="text-center"><a class="kt-font-primary kt-link text-center" href="<?php echo site_url('product/detail/'.$this->global->encrypt($info['code'])) ?>" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk detail"><b><?php echo $info['code']; ?></b></a></td>
                            <td class="text-dark"><?php echo $info['name']; ?></td>
                            <td class="text-dark"><?php echo $info['name_u']; ?></td>
                            <?php for($i=1;$i<=5;$i++): ?>
                                <?php                                                
                                    $info_percent = ($info['buyprice'] > 0) ? ($info['price_'.$i]-$info['buyprice'])/$info['buyprice']*100 : 100;
                                    $class_percent = $info_percent <= 0 ? 'text-danger' : 'text-success'; 
                                ?>
                                <?php if($this->system->check_access('view_sellprice_'.$i, 'read')): ?>
                                <td class="text-right text-dark"><?php echo number_format($info['price_'.$i],0,".",",").' / <b class="'.$class_percent.'">'.number_format($info_percent, 2, ".", ",").'</b> %'; ?></td>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </tr>                                  
                        <?php $no++; endforeach; ?>                                                                         
                    </tbody>
                </table>  
            </div>
        </div>
    </div>
    <!-- end:: Content -->
</div>