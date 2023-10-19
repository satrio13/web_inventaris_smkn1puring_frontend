<html>
    <head>
        <title>Detail Pemindahan Barang - Aplikasi Manajemen Barang SMK N 1 Puring</title>
        <style type="text/css">
            * { font-size: 11pt; font-family: arial; }
        </style>
    </head>
    <body>
        <table width="100%" cellspacing="0" cellpadding="2"> 
            <tr>
                <td width="10%" ><img src="assets/img/logo_smkn1puring.png" width="70"></td>
                <td align="center" width="80%" >DINAS PENDIDIKAN KABUPATEN KEBUMEN<br>
                                                        <b>SEKOLAH MENENGAH KEJURUAN NEGERI 1 PURING</b><br>
                                                        Jl. Selatan-Selatan Kilometer 04 Puring - Kebumen, Kode Pos 54383<br>
                                                        Email : smknegeri1puring@gmail.com - Telp : 0811-2635-864
                </td>
                <td width="10%" ></td>
            </tr>
        </table>
        <table width="100%">
            <tr>
                <td >
                    <div style="border-bottom:3px solid black;"></div>
                </td>
            </tr>
        </table>
        <br><center><b>DETAIL PEMINDAHAN BARANG</b></center>
        <br>
        <table width="100%" cellspacing="0" cellpadding="2">
            <tr>
                <td>
                    KODE PINDAH : <?= $data[0]['kode_pindah']; ?>
                </td>
                <td>
                    TGL PINDAH : <?= date('d-m-Y', strtotime($data[0]['tgl_pindah'])); ?>
                </td>
                <td>
                    KE RUANG : <?= $data[0]['ruang']; ?>
                </td>
            </tr>
        </table>
        <table cellspacing="0" cellpadding="3" width="100%" border="1">
            <thead style="background-color: #ccc" align="center">
                <tr>
                    <th width="5%" nowrap>NO</th>
                    <th nowrap>KODE BARANG</th>
                    <th nowrap>NAMA BARANG</th>
                    <th nowrap>KONDISI BARANG</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            $no = 1; 
            foreach($data as $r):
                echo'<tr>
                        <td align="center">'.$no++.'</td>
                        <td>'.$r['kode_inv'].'</td>
                        <td>'.$r['barang'].'</td>
                        <td>'.$r['kondisi'].'</td>
                    </tr>';
            endforeach; 
            ?>
            </tbody>
        </table>
        <br>
        <table cellspacing="0" cellpadding="3" width="100%">
            <tr>
                <td width="70%"></td>
                <td>Puring, <?php echo tgl_indo(date('Y-m-d')); ?><br>Pengurus Barang</td>
            </tr>
            <tr>
                <td></td>
                <td>
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