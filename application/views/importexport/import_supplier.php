<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Import Export</h3>
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

        <?php echo form_open_multipart('', ['autocomplete'=>'off']); ?>
        <?php if(!isset($sheet)): ?>
        <div class="kt-portlet kt-portlet--mobile">            
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand fa fa-clipboard-list"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">                          
                            <a href="<?php echo base_url('dashboard'); ?>" class="btn btn-outline-primary btn-elevate btn-icon"
                                data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Kembali ke Dashboard">
                                <i class="fa fa-arrow-left"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <?php if(isset($error)): ?>
                <div class="alert alert-outline-danger fade show" role="alert">
                    <div class="alert-icon"><i class="fa fa-times"></i></div>
                    <div class="alert-text"><?php echo $error; ?></div>
                    <div class="alert-close">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true"><i class="la la-close"></i></span>
                        </button>
                    </div>
                </div>
                <?php endif; ?>
                <div class="form-group row">
                    <label class="col-md-2 col-form-label kt-font-dark text-right">FILE IMPORT</label>
                    <div class="col-md-10">
                        <input type="hidden" name="form_type" value="0">
                        <input type="file" class="col-md-8 form-control" id="import_file" name="import_file" required>
                    </div>                    
                </div>
                <div class="row">
                    <div class="col-md-2"></div>                    
                    <div class="col-md-10">
                        <button type="submit" class="btn btn-sm btn-wide btn-success btn-brand btn-elevate-hover btn-square  form-control"><i class="fa fa-file-export"></i> IMPORT</button>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="kt-portlet kt-portlet--mobile">            
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand fa fa-clipboard-list"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">
                        Preview
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">                          
                            <a href="<?php echo base_url('product'); ?>" class="btn btn-outline-primary btn-elevate btn-icon"
                                data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Kembali ke Daftar Produk">
                                <i class="fa fa-arrow-left"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-md-12">
                        <!--begin: Datatable -->
                        <table class="table table-bordered table-hover table-checkable" id="datatable">
                            <thead>
                                <tr style="text-align:center;" class="kt-font-dark">                                    
                                    <th>NO.</th>
                                    <th>NAMA</th>
                                </tr>
                            </thead>
                            <tbody>                                 
                                <?php $error=0; $no=0; foreach($sheet AS $info): ?>
                                    <?php if($no == 0):?>
                                        <?php $no ++; continue; ?>                                    
                                    <?php else: ?>
                                    <tr>
                                        <td class="text-center text-dark"><?php echo $no; ?></td>
                                        <td class="text-dark"><?php echo $info[1]; ?></td>
                                        <td class="text-dark"><?php echo $info[2]; ?></td>                                        
                                    </tr>
                                    <?php endif; ?>
                                <?php $no++; endforeach; ?>
                            </tbody>
                        </table>
                        <!--end: Datatable -->                                                                           
                    </div>
                </div>
            </div>
            <?php if($error == 0): ?>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="<?php echo base_url('product'); ?>" class="btn btn-danger btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-times"></i>BATAL</a>
                        </div>
                        <div class="col-md-6">                                
                            <button type="submit" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-save"></i> IMPORT</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php echo form_close() ?>        
    </div>
    <!-- end:: Content -->
</div>