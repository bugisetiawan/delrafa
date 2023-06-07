<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Persediaan</h3>
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
                        Informasi
                    </h3>                                
                </div> 
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions"> 									
                        <?php if($stock_opname['status'] != 1): ?>
                            <a href="<?php echo base_url('opname/adjusment/create/'.$this->global->encrypt($stock_opname['id'])); ?>" class="btn btn-outline-warning btn-elevate"
                                data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Sesuaikan Stok">
                                <i class="la la-edit"></i>SESUAIKAN STOK
                            </a>   
                            <button class="btn btn-icon btn-outline-primary" id="synchronize_stok_opname_btn"
                                data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Sinkronkan Stok">
                                <i class="la la-refresh"></i>
                            </button>   
                            <button class="btn btn-icon btn-outline-danger" id="delete_stock_opname_btn"
                                data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Hapus Stok Opname">
                                <i class="fa fa-trash"></i>
                            </button>   
                            <!-- <a href="<?php echo base_url('opname/print/'.encrypt_custom($stock_opname['id'])); ?>" class="btn btn-outline-success btn-elevate btn-icon" target="_blank"
                                data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Cetak Data Stock Opname">
                                <i class="fa fa-print"></i>
                            </a> -->
                        <?php endif; ?>                                
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row">                                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="col-form-label kt-font-dark">TGL. STOK OPNAME</label>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control', 'value' => date('d-m-Y', strtotime($stock_opname['date'])), 'readonly' => 'true'); 
                                echo form_input($data);
                            ?>                                        
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="col-form-label kt-font-dark text-right">GUDANG</label>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control', 'value' => $stock_opname['warehouse'], 'readonly' => 'true'); 
                                echo form_input($data);
                            ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="col-form-label kt-font-dark text-right">PETUGAS STOK OPNAME</label>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control', 'value' => $stock_opname['checker'], 'readonly' => 'true');
                                echo form_input($data);
                            ?>                                        
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="col-form-label kt-font-dark text-right">OPERATOR</label>
                            <?php 
                                $data = array('type' => 'text', 'class' => 'form-control', 'value' => $stock_opname['operator'], 'readonly' => 'true'); 
                                echo form_input($data);                                                                                         
                            ?>
                        </div>
                    </div>
                </div>
            </div>                        
        </div>
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Daftar Produk                                    
                    </h3>                                            
                </div>						
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-md-12">
                        <?php 
                            $data = array('type' => 'hidden', 'class' => 'form-control', 'id' => 'stock_opname_id', 'name' => 'stock_opname_id',  'value' => $stock_opname['id'], 'readonly' => 'true');
                            echo form_input($data);
                        ?>
                        <!--begin: Datatable -->
                        <table class="table table-bordered table-hover table-checkable" id="datatable_detail_stock_opname">
                            <thead>
                                <tr style="text-align:center;">
                                    <th>NO</th>
                                    <th>KODE</th>
                                    <th>NAMA</th>                                                
                                    <th>SATUAN</th>
                                    <th>STOCK</th>
                                    <th>ADJUST</th>
                                    <th>HPP</th>
                                    <th>TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>                                                                                                            
                            </tbody>
                        </table>
                        <!--end: Datatable -->                                                                           
                    </div>
                </div>                          
                <hr>
                <div class="row">
                    <div class="col-md-6">                                        
                    </div>                                    
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-md-6 col-form-label kt-font-dark text-right">GRANDTOTAL</label>
                            <div class="col-md-6">                                        
                                <?php
                                    if($total_hpp < 0)
                                    {                                                
                                        $class="text-danger";                                                
                                    }
                                    else
                                    {
                                        $class="text-success";
                                    }
                                    $data = array('type' => 'text', 'min' => 0, 'class' => 'form-control text-right '. $class , 'id' => 'total_product', 'name' => 'total_product',  'value' => number_format($total_hpp ,0,".",","), 'required' => 'true', 'readonly' => 'true');
                                    echo form_input($data);                                             
                                    echo form_error('total_product', '<p class="text-danger">', '</p>');
                                ?>
                            </div>										                                   
                        </div>                                        
                    </div>								
                </div> 
            </div>                      
        </div>
    </div>
    <!-- end:: Content -->
</div>