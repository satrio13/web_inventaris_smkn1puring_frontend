<div class="container">
  <div class="content-wrapper bg-white">
      <div class="content-header">
          <div class="row mb-2">
              <div class="col-sm-12">
                  <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="<?= base_url(); ?>">Home</a></li>
                      <li class="breadcrumb-item active"><?= $title; ?></li>
                  </ol>
              </div>
          </div>
          <div class="row mb-2">
              <div class="col-sm-12">
                  <h1 class="text-dark"><?= $title; ?></h1>
              </div>
          </div>
      </div>

        <section class="content">
            <div class="row">
                <div class="col-12">
                    <?php 
                    if($this->session->flashdata('msg-baranghp'))
                    {
                        echo pesan_sukses($this->session->flashdata('msg-baranghp'));
                    }elseif($this->session->flashdata('msg-gagal-baranghp'))
                    {
                        echo pesan_gagal($this->session->flashdata('msg-gagal-baranghp'));
                    }elseif($this->session->flashdata('msg-import-baranghp'))
                    {
                        echo pesan_sukses($this->session->flashdata('msg-import-baranghp'));
                    }
                    ?>
                    <div class="card border border-secondary">
                        <div class="card-header">
                            <button onclick="add_baranghp()" class="btn bg-primary btn-sm"><i class="fa fa-plus"></i> TAMBAH DATA</button>
                            <a href="<?= base_url('backend/form-baranghp'); ?>" class="btn bg-danger btn-sm"><i class="fa fa-upload"></i> IMPORT DATA</a>
                            <a href="<?= base_url('backend/export-baranghp'); ?>" class="btn bg-navy btn-sm"><i class="fa fa-file-excel"></i> EXPORT DATA EXCEL</a>
                            <a href="<?= base_url("backend/cetak-baranghp-pdf"); ?>" target="_blank" class="btn bg-info btn-sm"><i class="fa fa-print"></i> CETAK PDF</a>
                            <a href="<?= base_url("backend/cetak-baranghp"); ?>" target="_blank" class="btn bg-purple btn-sm"><i class="fa fa-print"></i> CETAK</a>
                            <button onclick="reload_table()" class="btn bg-maroon btn-sm"><i class="fas fa-sync-alt"></i> REFRESH</button>
                            <br><br>
                            <h3 class="text-center"><?= strtoupper($title); ?></h3>
                        </div>
                        <div class="card-body">
                            <div class="table table-responsive">
                                <table class="table table-bordered table-striped table-sm" id="table-baranghp">
                                    <thead class="bg-secondary text-center">
                                        <tr>
                                            <th width="5%" nowrap>NO</th>
                                            <th nowrap>KODE BARANG</th>
                                            <th nowrap>NAMA BARANG</th>
                                            <th nowrap>KATEGORI</th>
                                            <th nowrap>STOK</th>
                                            <th nowrap>SATUAN</th>
                                            <th nowrap>HARGA SATUAN</th>
                                            <th nowrap>AKSI</th>
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
        </section>
    </div>

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal">
                    <input type="hidden" value="" name="id_baranghp"/> 
                    <div class="form-body">
                        <div class="form-group row">
                            <label for="kode_hp" class="col-md-3 col-form-label">KODE BARANG <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <input type="text" name="kode_hp" id="kode_hp" maxlength="15" class="form-control required sepasi" placeholder="KODE BARANG">
                                <span class="help-block text-danger"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="barang" class="col-md-3 col-form-label">NAMA BARANG <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <input type="text" name="barang" id="barang" maxlength="100" class="form-control required" placeholder="NAMA BARANG">
                                <span class="help-block text-danger"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="id_kategori" class="col-md-3 col-form-label">KATEGORI BARANG <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <select name="id_kategori" id="id_kategori" class="form-control required">
                                <?php foreach($kategori as $r): ?>
                                    <option value="<?= $r->id_kategori; ?>"><?= $r->kategori; ?></option>
                                <?php endforeach; ?>
                                </select>
                                <span class="help-block text-danger"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="satuan" class="col-md-3 col-form-label">SATUAN <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <input type="text" name="satuan" id="satuan" maxlength="15" class="form-control required" placeholder="SATUAN">
                                <span class="help-block text-danger"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="harga" class="col-md-3 col-form-label">HARGA</label>
                            <div class="col-md-9">
                                <input type="number" name="harga" id="harga" min="0" class="form-control" placeholder="HARGA">
                                <span class="help-block text-danger"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="offset-sm-3 col-md-9">
                                <span class="text-danger"><b>*</b></span>) Field Wajib Diisi
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_baranghp()" class="btn btn-primary"><i class="fa fa-check"></i> Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->