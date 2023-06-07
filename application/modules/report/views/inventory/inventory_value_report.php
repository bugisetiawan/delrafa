<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
	<div class="kt-subheader   kt-grid__item" id="kt_subheader">
		<div class="kt-subheader__main">
			<h3 class="kt-subheader__title">Laporan</h3>
			<span class="kt-subheader__separator kt-subheader__separator--v"></span>
			<h3 class="kt-subheader__title"><b><?php echo $title; ?></b></h3>
		</div>
	</div>    
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
		<div class="alert alert-dark fade show" role="alert">
			<div class="alert-icon"><i class="flaticon-exclamation-2"></i></div>
			<div class="alert-text">
				Mohon Perhatian! Berikut laporan nilai persediaan barang per Tanggal: <b><?php echo date('d-m-Y'); ?></b> Jam: <b><?php echo date('H:i:s'); ?></b>
				<br>Data dibawah berubah sesuai dengan kondisi barang yang tersedia.
			</div>
		</div>  
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
							<a href="javascript: void(0);" onclick="$('#datatable').DataTable().ajax.reload(); total_inventory_value_report();" class="btn btn-square btn-brand btn-elevate btn-elevate-air">
								<i class="la la-refresh"></i>
								<span class="d-none d-sm-inline">Refresh Data</span>
							</a>                                                        
						</div>
					</div>
				</div>
			</div>
			<div class="kt-portlet__body">				              
				<div class="form-group row">
					<div class="col-md-3">
						<label class="text-dark">PENCARIAN PRODUK</label>
						<div class="input-group">
							<div class="input-group-prepend"><span class="input-group-text"><i class="la la-search kt-font-brand"></i></span></div>
							<input type="text" class="form-control" name="search" id="search" placeholder="Silahkan isi Kode/Nama Produk untuk melakukan pencarian..." autocomplete="off">
						</div>
					</div>          
					<div class="col-md-3">
						<label class="text-dark text-right">DEPARTEMEN</label>
						<select name="department_code" id="department_code" class="form-control">                                    
						</select>
					</div>                      
					<div class="col-md-2">
						<label class="text-dark text-right">SUBDEPARTEMEN</label>
						<select name="subdepartment_code" id="subdepartment_code" class="form-control">
							<option value="">- SEMUA SUBDEPARTEMEN -</option>
						</select> 
					</div>
					<div class="col-md-2">
						<label class="text-dark text-right">PPN</label>
						<select name="ppn" id="ppn" class="form-control">
							<option value="">- SEMUA -</option>
							<option value="0">NON</option>
							<option value="1">PPN</option>
							<option value="2">FINAL</option>
						</select> 
					</div>			
					<div class="col-md-2">
						<label class="text-dark">GUDANG:</label>
						<select class="form-control" name="warehouse_id" id="warehouse_id"></select>
					</div>          		      
				</div>
				<div class="form-group text-center" id="message">
                    <h6 class="text-danger font-weight-bold">ISI KATA KUNCI DI KOLOM PENCARIAN ATAU PILIH SALAH SATU DEPARTEMEN UNTUK MENAMPILKAN DAFTAR DAN NILAI PERSEDIAAN PRODUK TERTENTU</h6>
                </div>
				<div class="row row-no-padding row-col-separator-sm">
					<div class="col-md-6 col-lg-6 col-xl-6">                        
						<div class="kt-widget24">
							<div class="kt-widget24__details">
								<div class="kt-widget24__info">
									<h4 class="kt-widget24__title text-dark font-weight-bold">
										TOTAL PRODUK
									</h4>
								</div>
								<span class="kt-widget24__stats kt-font-dark font-weight-bold">
									<span id="total_product"></span>
								</span>
							</div>
						</div>                        
					</div>
					<div class="col-md-6 col-lg-6 col-xl-6">
						<div class="kt-widget24">
							<div class="kt-widget24__details">
								<div class="kt-widget24__info">
									<h4 class="kt-widget24__title text-primary font-weight-bold">
										TOTAL NILAI
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
				<table class="table table-bordered table-hover table-checkable" id="datatable">
					<thead>
						<tr style="text-align:center;">
							<th class="notexport">NO</th>                            
							<th>KODE</th>
							<th>NAMA</th>
							<th>STOK</th>
							<th>SATUAN</th>
							<th>HPP</th>
							<th>TOTAL</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>                
			</div>
		</div>
	</div>
</div>