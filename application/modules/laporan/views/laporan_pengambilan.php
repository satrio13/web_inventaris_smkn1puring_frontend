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

<section class="content bg-white">
    <div class="row">
        <div class="col-12">
            <div class="card border border-secondary">
                <div class="card-header bg-info">
                    <?php echo form_open('backend/laporan-pengambilan'); ?>
                        <div class="row">
                            <div class="col-md-3 text-bold p-1">
                              <label for="tgl_awal">Periode Awal :</label>
                              <input type="date" name="tgl_awal" id="tgl_awal" value="<?= set_value('tgl_awal'); ?>" class="form-control-sm" required>
                            </div>
                            <div class="col-md-3 text-bold p-1">
                              <label for="tgl_akhir">Periode Akhir :</label> 
                              <input type="date" name="tgl_akhir" id="tgl_akhir" value="<?= set_value('tgl_akhir'); ?>" class="form-control-sm" required>
                            </div>
                            <div class="col-md-3 p-1">
                                <button type="submit" name="submit" value="Submit" class="btn bg-danger btn-sm border border-white"><i class="fa fa-search"></i> Cari Data</button>
                            </div>
                        </div>
                    <?php echo form_close(); ?>
                    </div>
                    <div class="card-body">
                      <h3 class="text-center"><?= strtoupper($title); ?></h3>
                      <div class="table table-responsive">
                        <?php if(isset($submit)){ ?>
                          <table class="table" width="100%">
                            <tr>
                              <td width="15%" class="text-bold">Periode Awal</td>
                              <td width="2%" class="text-bold">:</td>
                              <td><?= tgl_indo($tgl_awal); ?></td>
                            </tr>
                            <tr>
                              <td width="15%" class="text-bold">Periode Akhir</td>
                              <td width="2%" class="text-bold">:</td>
                              <td><?= tgl_indo($tgl_akhir); ?></td>
                            </tr>
                          </table>
                          <a href="<?= base_url("backend/export-laporan-pengambilan/$tgl_awal/$tgl_akhir"); ?>" target="_blank" class="btn bg-navy btn-sm mb-2"><i class="fa fa-file-excel"></i> EXPORT DATA EXCEL</a>
                          <a href="<?= base_url("backend/cetak-laporan-pengambilan-pdf/$tgl_awal/$tgl_akhir"); ?>" target="_blank" class="btn bg-primary btn-sm mb-2"><i class="fa fa-print"></i> CETAK PDF</a>
                          <a href="<?= base_url("backend/cetak-laporan-pengambilan/$tgl_awal/$tgl_akhir"); ?>" target="_blank" class="btn bg-purple btn-sm mb-2"><i class="fa fa-print"></i> CETAK</a>
                        <?php } ?>
                        <table class="table table-bordered table-striped table-sm">
                          <thead class="bg-secondary text-center">
                            <tr>
                                <th width="5%">NO</th>
                                <th nowrap>KODE AMBIL</th>
                                <th nowrap>NAMA PENGAMBIL</th>
                                <th nowrap>TGL AMBIL</th>
                                <th nowrap>WAKTU AMBIL</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?php 
                          if(isset($submit))
                          {
                            if($data->totalRecords > 0 AND $data->totalRecords <= 1000)
                            {
                              $no = 1;
                              foreach($data->data as $r):
                              echo'<tr>
                                    <td class="text-center">'.$no++.'</td>
                                    <td><a href="javascript:void(0)" data="'.$r->kode_trans.'" class="text-bold item_detail_pengambilan" title="LIHAT DETAIL">'.$r->kode_trans.'</a></td>
                                    <td>'.$r->nama_pengambil.'</td>
                                    <td>'.date('d-m-Y', strtotime($r->tgl_keluar)).'</td>
                                    <td>'.$r->jam_keluar.' WIB</td>
                                  </tr>';
                              endforeach;
                            }elseif($data->totalRecords > 1000)
                            {
                              echo'<tr>
                                    <td colspan="6" class="text-center text-danger text-bold">
                                      DATA TERLALU BANYAK LEBIH DARI 1000 BARIS<br>
                                      Klik Tombol Dibawah Untuk Melihat / Download Data Lengkapnya.<br>
                                      <a href="'.base_url("backend/cetak-laporan-pengambilan-pdf/$tgl_awal/$tgl_akhir").'" target="_blank" class="btn bg-primary btn-flat btn-xs mb-2"><i class="fa fa-print"></i> CETAK PDF</a>
                                      <a href="'.base_url("backend/cetak-laporan-pengambilan/$tgl_awal/$tgl_akhir").'" target="_blank" class="btn bg-purple btn-flat btn-xs mb-2"><i class="fa fa-print"></i> CETAK</a>
                                    </td>
                                  </tr>';
                            }else
                            {
                              echo'<tr>
                                    <td colspan="6" class="text-center">DATA KOSONG</td>
                                  </tr>';
                            }
                          }else
                          {
                            echo'<tr>
                                    <td colspan="6" class="text-center">ANDA BELUM MELAKUKAN PENCARIAN</td>
                                  </tr>';
                          }
                          ?>
                          </tbody>
                    </table>
                </div>
            </div>
          </div>
      </div>
    </section>
</div>        
       
<div class="modal fade" id="modal_detail_pengambilan" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title">Detail Pengambilan Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row mt-2">
                        <div class="col-md-2 text-bold">KODE AMBIL</div>
                        <div class="col-md-4" id="kode_trans_detail"></div>
                        <div class="col-md-2 text-bold">NAMA PENGAMBIL</div>
                        <div class="col-md-4" id="nama_pengambil_detail"></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-2 text-bold">TGL AMBIL</div>
                        <div class="col-md-4" id="tgl_keluar_detail"></div>
                        <div class="col-md-2 text-bold">JAM AMBIL</div>
                        <div class="col-md-4" id="jam_keluar_detail"></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-2 text-bold">LOGIN PENGAMBIL</div>
                        <div class="col-md-10" id="login_pengambil_detail"></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12 text-right" id="link_cetak_pengambilan">
                           
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="table table-responsive">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead class="bg-secondary text-center">
                                        <tr>
                                            <th width="5%" nowrap>NO</th>
                                            <th nowrap>KODE BARANG</th>
                                            <th nowrap>NAMA BARANG</th>
                                            <th nowrap>JUMLAH</th>
                                        </tr>
                                    </thead>
                                    <tbody id="show_detail_pengambilan">
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->