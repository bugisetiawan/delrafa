<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">Stok</h3>
            <span class="kt-subheader__separator kt-subheader__separator--v"></span>            
            <h3 class="kt-subheader__title"><b><?php echo $title; ?></b></h3>
        </div>
    </div>
    <!-- end:: Content Head -->    
    <!-- begin:: Content -->
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
        <?php if($production['status'] != 1): ?>
        <div class="alert alert-danger fade show" role="alert">
            <div class="alert-icon"><i class="flaticon-exclamation-2"></i></div>
            <div class="alert-text">Mohon Perhatian! Produksi ini <b>BELUM SELESAI</b> harap segera melakukan penyelesaian, terima kasih.</div>
        </div>
        <?php endif; ?>
        <div class="row">            
            <div class="col-lg-12">            
            <?php echo form_open_multipart('', ['class' => 'repeater', 'autocomplete'=>'off']); ?>
                <!--begin::Portlet-->                
                <div class="kt-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Informasi | No.Produksi : <span class="kt-font-bold kt-font-success"><?php echo $production['code_pro']; ?></span>
                                <?php 
                                    $data = array('type' => 'hidden', 'class' => 'form-control', 'name' => 'production_id', 'value' => $production['id_pro'], 'readonly' => 'true'); 
                                    echo form_input($data);
                                ?>
                            </h3>
                        </div>
                    </div>                    
                    <div class="kt-portlet__body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label kt-font-dark">TGL. PRODUKSI</label>
                                    <div class="col-md-8">
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control', 'value' => date('d-m-Y', strtotime($production['date_pro'])), 'readonly' => 'true'); 
                                            echo form_input($data);
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label kt-font-dark">PRODUK</label>
                                    <div class="col-md-8">
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control', 'value' => $production['name_p'], 'readonly' => 'true'); 
                                            echo form_input($data);
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label kt-font-dark text-right">GUDANG</label>
                                    <div class="col-md-8">
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control', 'value' => $production['name_w'], 'readonly' => 'true'); 
                                            echo form_input($data);
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label kt-font-dark text-right">JUMLAH PRODUKSI</label>
                                    <div class="col-md-2">                                            
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control', 'value' => $production['qty_produce'], 'readonly' => 'true'); 
                                            echo form_input($data);
                                        ?>
                                    </div>
                                    <label class="col-md-3 col-form-label kt-font-dark text-right">HASIL PRODUKSI</label>
                                    <div class="col-md-3">                                            
                                    <?php if($production['status'] != 1): ?>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'qty_result', 'name' => 'qty_result', 'value' => $production['qty_result'], 'placeholder' => 'Hasil Produksi..', 'required' => 'true'); 
                                            echo form_input($data);
                                        ?>
                                    <?php else: ?>
                                        <?php 
                                            $data = array('type' => 'text', 'class' => 'form-control', 'id' => 'qty_result', 'name' => 'qty_result', 'value' => $production['qty_result'], 'placeholder' => 'Hasil Produksi..', 'readonly' => 'true'); 
                                            echo form_input($data);
                                        ?>
                                    <?php endif; ?>                   
                                    </div>                                       
                                </div>
                            </div>
                        </div>                           
                    </div>                                           
                </div>                
                <!--end::Portlet-->                   
                <!--begin::Portlet-->
                <div class="kt-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Daftar Produk Bundle                                   
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
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-sm repeater" id="product_table">
                                    <thead>
                                        <tr style="text-align:center;">
                                            <th>PRODUK</th>                                            
                                            <th width=100px;>QTY PER SET</th>
                                            <th width=100px;>SATUAN</th>
                                            <th width=150px;>HPP PER UNIT</th>
                                            <th width=150px;>TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody data-repeater-list="product">
                                        <?php $subtotal_hpp = 0; foreach($product_production AS $info): ?>
                                        <tr data-repeater-item style="text-align:center;">
                                            <td>
                                                <div class="typeahead">
                                                <?php  
                                                    $data = array('type' => 'text', 'class' => 'form-control product_input', 'value' => $info['name_b'], 'readonly' => 'true'); 
                                                    echo form_input($data);
                                                ?>
                                                </div>
                                            </td>     
                                                <?php 
                                                    $data = array('type' => 'hidden', 'class' => 'form-control product_code', 'name' => 'product_code', 'value' => $info['code_b'], 'required' => 'true', 'readonly' => 'true'); 
                                                    echo form_input($data);
                                                    echo form_error('product_code', '<p class="text-danger">', '</p>');
                                                ?>
                                            <td>
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control qty text-right', 'name' => 'qty', 'placeholder' => 'QTY',  'value' => $info['qty_b'], 'requried' => 'true', 'readonly' => 'true');
                                                    echo form_input($data);                                             
                                                    echo form_error('qty', '<p class="text-danger">', '</p>');
                                                ?>
                                                <?php 
                                                    $data = array('type' => 'hidden', 'class' => 'form-control', 'name' => 'qty_convert', 'value' => $info['qty_b']*$info['value_ub'], 'required' => 'true', 'readonly' => 'true'); 
                                                    echo form_input($data);
                                                ?>
                                            </td>
                                            <td>
                                                <?php  
                                                    $data = array('type' => 'text', 'class' => 'form-control', 'value' => $info['name_u'], 'readonly' => 'true'); 
                                                    echo form_input($data);
                                                ?>
                                                <?php 
                                                    $data = array('type' => 'hidden', 'class' => 'form-control', 'name' => 'unit_id', 'value' => $info['id_u'], 'required' => 'true', 'readonly' => 'true'); 
                                                    echo form_input($data);
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'name' => 'hpp','value' => number_format($info['hpp_b'],0,".",","), 'required' =>'true', 'readonly' => 'true'); 
                                                    echo form_input($data);
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                    $total_hpp = $info['hpp_b'] * $info['qty_b'] * $info['value_ub'];
                                                    $subtotal_hpp = $subtotal_hpp +  $total_hpp;											
                                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'name' => 'total_hpp','value' => number_format($total_hpp,0,".",","), 'required' =>'true', 'readonly' => 'true'); 
                                                    echo form_input($data);
                                                ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <tr>
                                            <td colspan="4" class="text-right">BIAYA PRODUKSI PER UNIT</td>
                                            <td>
                                                <?php 														
                                                    $data = array('type' => 'text', 'class' => 'form-control text-right', 'name' => 'subtotal_hpp','value' => number_format($subtotal_hpp,0,".",","), 'required' =>'true', 'readonly' => 'true'); 
                                                    echo form_input($data);
                                                ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>   
                    <?php if($production['status'] != 1): ?>
                    <div class="kt-portlet__foot">
                        <div class="kt-form__actions">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success btn-brand btn-elevate-hover btn-square form-control"><i class="fa fa-save"></i> PRODUKSI SELESAI</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <!--end::Portlet-->
            <?php echo form_close(); ?>
            </div>            
        </div>        
    </div>
    <!-- end:: Content -->
</div>