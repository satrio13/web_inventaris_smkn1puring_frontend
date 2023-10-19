<html>
    <head>
        <title>Laporan Stok Barang Habis Pakai - Aplikasi Manajemen Barang SMK N 1 Puring</title>
        <style type="text/css">
            * { font-size: 11pt; font-family: arial; }
        </style>
    </head>
    <body>
        <table width="100%" cellspacing="0" cellpadding="2"> 
            <tr>
                <td width="10%"><img src="<?= base_url('assets/img/logo_smkn1puring.png'); ?>" width="70"></td>
                <td align="center" width="80%">DINAS PENDIDIKAN KABUPATEN KEBUMEN<br>
                                                        <b>SEKOLAH MENENGAH KEJURUAN NEGERI 1 PURING</b><br>
                                                        Jl. Selatan-Selatan Kilometer 04 Puring - Kebumen, Kode Pos 54383<br>
                                                        Email : smknegeri1puring@gmail.com - Telp : 0811-2635-864
                </td>
                <td width="10%"></td>
            </tr>
        </table>
        <table width="100%">
            <tr>
                <td>
                    <div style="border-bottom:3px solid black;"></div>
                </td>
            </tr>
        </table>
        <br><center><b>LAPORAN REKAPITULASI STOK BARANG HABIS PAKAI</b></center>
        <br>
        <table cellspacing="0" cellpadding="3" width="100%" border="1">
            <thead style="background-color: #ccc" align="center">
                <tr>
                    <th width="5%" nowrap>NO</th>
                    <th nowrap>KODE BARANG</th>
                    <th nowrap>NAMA BARANG</th>
                    <th nowrap>JUMLAH STOK</th>
                    <th nowrap>HARGA</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if($data->totalRecords > 0)
            {
                $no = 1;
                $total = 0;
                foreach($data->data as $r):
                    $harga = $r->stok * $r->harga;
                    $total = $total + $harga;
                    echo'<tr>
                            <td align="center">'.$no++.'</td>
                            <td>'.$r->kode_hp.'</td>
                            <td>'.$r->barang.'</td>
                            <td align="right">'.$r->stok.' '.$r->satuan.'</td>
                            <td align="right">'.number_format($harga, 0, ',', '.').'</td>
                        </tr>';
                endforeach;
                    echo'<tr>
                            <td colspan="4"></td>
                            <td align="right"><b>'.number_format($total, 0, ',', '.').'</b></td>
                        </tr>';
            }else
            {   
                echo'<tr>
                        <td colspan="5" align="center">DATA KOSONG</td>
                    </tr>';
            }
            ?>
            </tbody>
        </table>
        <br>
        <table cellspacing="0" cellpadding="3" width="100%">
            <tr>
                <td width="40%"><br>Kepala SMK N 1 Puring</td>
                <td width="30%"></td>
                <td width="30%">Puring, <?php echo tgl_indo(date('Y-m-d')); ?><br>Pengurus Barang</td>
            </tr>
            <tr>
                <td width="40%"><?php echo'<br><br><br><b><u>'.$ks->nama.'</u></b><br>NIP. '.$ks->nip; ?></td>
                <td width="30%"></td>
                <td width="30%">
                    <?php
                    echo'<br><br><br><b><u>'.$pengurus->nama.'</u></b>';
                    if(!empty($pengurus->nip))
                    {
                        echo'<br>NIP. '.$pengurus->nip;
                    } 
                    ?>
                </td>
            </tr>
        </table>
    </body>
</html>
<script>
    window.print();
</script>