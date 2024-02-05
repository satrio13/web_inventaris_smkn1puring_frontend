<div class="container">
  <div class="content-wrapper bg-white">
      <div class="content-header">
          <div class="row mb-2">
              <div class="col-sm-12">
                  <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="<?= base_url(); ?>">Home</a></li>
                      <li class="breadcrumb-item"><a href="<?= base_url('backend/baranghp'); ?>">Barang Habis Pakai</a></li>
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

      <section class="content bg-white">
        <div class="row">
          <div class="col-md-12">
          <?php 
          if($this->session->flashdata('msg-gagal-import-baranghp'))
          {
            echo pesan_gagal($this->session->flashdata('msg-gagal-import-baranghp'));
          }
          ?>
            <!-- Horizontal Form -->
                  <div class="card card-info border border-secondary">
                    <div class="card-header">
                      <a href="http://localhost:8080/web_inventaris_smkn1puring_backend/excel/baranghp/format-import-data-barang-habis-pakai.xlsx" class="btn bg-maroon btn-sm" target="_blank"><i class="fa fa-download"></i> Download Format</a>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                  <?php echo form_open_multipart('backend/form-baranghp'); ?>
                      <div class="card-body">
                        <div class="callout callout-danger">
                          <h5>CARA MELAKUKAN IMPORT DATA BARANG HABIS PAKAI :</h5>
                          <b>1.</b> Klik tombol <b>Download Format</b> untuk mengunduh file template excel yg dibutuhkan <b>( format-import-data-barang-habis-pakai.xlsx )</b>.<br>
                          <b>2.</b> Setelah file  <b>( format-import-data-barang-habis-pakai.xlsx )</b> berhasil diunduh, kemudian buka file tersebut dan mulailah untuk mengisi datanya mulai dari <b>baris/row 2</b> !! <br>
                          <b>3.</b> Setelah semua data dirasa sudah benar, kemudian simpan <b>( ctrl+s )</b> file tersebut. Langkah selanjutnya adalah melakukan upload file tersebut ke dalam Aplikasi ini. Caranya Klik <b>Choose File / Browse</b> untuk mencari file tersebut kemudian klik <b>Preview</b> untuk divalidasi lebih lanjut oleh sistem.<br>
                          <b>4.</b> Setelah klik Preview dan data telah lolos verifikasi sistem maka <b>Tombol Import</b> akan muncul, dan selanjutnya <b>klik Import</b> untuk melakukan import data ke database.
                        </div>
                        <div class="form-group row">
                          <div class="col-sm-5">
                            <input type="file" name="file" class="form-control" accept=".xlsx" required>
                            <p style="color: red">*) ukuran file max 5 MB</p>
                          </div>
                        </div>
                      </div>
                      <!-- /.card-body -->
                      <div class="card-footer">
                        <button type='submit' name="preview" value="Preview" class='btn btn-primary btn-sm'><i class="fa fa-upload"></i> Import</button>
                        <a href="<?= base_url('backend/baranghp'); ?>" class="btn btn-danger float-right btn-sm"><i class="fa fa-arrow-left"></i> BATAL</a>
                      </div>
                    <?php echo form_close() ?>
  </section>
</div>     
