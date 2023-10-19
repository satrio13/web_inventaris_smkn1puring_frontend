<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Barangmasukhp extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		cek_login();
    } 

    function index()
	{	
		$url = api_url()."listbaranghp";
		$output = call_api_get($url);
		$dt = json_decode($output);

		$data['title'] = 'Barang Masuk Habis Pakai';
        $data['baranghp'] = $dt->data;
       	//layout
		$this->load->view('backend/template/head');
		$this->load->view('backend/template/topbar');
		$this->load->view('backend/template/nav');
		$this->load->view('barangmasukhp/index', $data);
		$this->load->view('backend/template/js');
		$this->load->view('barangmasukhp/script');
    }

    function get_data_barangmasukhp()
	{
		$page = $_POST['start'] / $_POST['length'] + 1;
		$limit = $_POST['length'];
		$search = $_POST['search']['value'];
		if(!empty($search))
		{
			$url = api_url()."list-barangmasukhp?page=$page&limit=$limit&search=$search";
		}else
		{
			$url = api_url()."list-barangmasukhp?page=$page&limit=$limit";
		}

		$output = call_api_get($url);
		$dt = json_decode($output);

		$list = $dt->data;
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $r)
		{	
			$no++;
			$row = array();
			$row[] = '<div class="text-center">'.$no.'</div>';
            $row[] = $r->kode_hp;
            $row[] = $r->barang;
            $row[] = $r->kategori;
            $row[] = date('d-m-Y', strtotime($r->tgl_masuk));
			$row[] = '<div class="text-right">'.$r->jml_masuk.'</div>';
			$row[] = $r->satuan;
            $action = '<div class="text-center">
						<a href="javascript:void(0)" class="btn btn-danger btn-xs" title="HAPUS DATA" onclick="delete_barangmasukhp('.$r->id_masukhp.')"><i class="fa fa-trash"></i> HAPUS</a>
                    </div>';
			$row[] = $action;
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $dt->totalRecords,
			"recordsFiltered" => $dt->totalRecords,
			"data" => $data,
		);
		//output dalam format JSON
		echo json_encode($output);
    } 

	function tambah_barangmasukhp()
	{ 
		$this->_validate();  
        $url = api_url().'tambah-barangmasukhp';
		$id_baranghp = $this->input->post('id_baranghp', true);
		$tgl_masuk = $this->input->post('tgl_masuk', true);
		$jml_masuk = $this->input->post('jml_masuk', true);

		$param = '{ "id_baranghp":"'.$id_baranghp.'", "tgl_masuk":"'.$tgl_masuk.'", "jml_masuk":"'.$jml_masuk.'" }';
		
		$output = call_api_post($url, $param);
		echo $output;	
    }

    function hapus_barangmasukhp($id_masukhp)
	{ 
        $url = api_url()."hapus-barangmasukhp/$id_masukhp";
		$output = call_api_delete($url);
		echo $output; 
    }

	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('id_baranghp') == '')
		{
			$data['inputerror'][] = 'id_baranghp';
			$data['error_string'][] = 'Nama Barang wajib diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('tgl_masuk') == '')
		{
			$data['inputerror'][] = 'tgl_masuk';
			$data['error_string'][] = 'Tgl Masuk wajib diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('jml_masuk') == '')
		{
			$data['inputerror'][] = 'jml_masuk';
			$data['error_string'][] = 'Jumlah Masuk wajib diisi';
			$data['status'] = FALSE;
		}
		
		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	function cetak_barangmasukhp_pdf()
	{	
		$url = api_url()."listbarangmasukhp";
		$output = call_api_get($url);
		$dt = json_decode($output);

		$url = api_url()."pengurusbarang";
		$output = call_api_get($url);
		$pengurus = json_decode($output);

		$data['data'] = $dt->data;
		$data['pengurus'] = $pengurus[0];
		$this->load->library('pdfgenerator');
		$html = $this->load->view('barangmasukhp/cetak_barangmasukhp_pdf', $data, true);
		$filename = 'Barang Masuk Habis Pakai - Aplikasi Manajemen Barang SMK N 1 Puring';
		$this->pdfgenerator->generate($html, $filename, TRUE, 'A4', 'landscape');	
	}

	function cetak_barangmasukhp()
	{	
		$url = api_url()."listbarangmasukhp";
		$output = call_api_get($url);
		$dt = json_decode($output);

		$url = api_url()."pengurusbarang";
		$output = call_api_get($url);
		$pengurus = json_decode($output);

		$data['data'] = $dt->data;
		$data['pengurus'] = $pengurus[0];
		$this->load->view('barangmasukhp/cetak_barangmasukhp', $data);
	}

	function export_barangmasukhp()
    {
        include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        $excel = new PHPExcel();

        $excel->getProperties()->setCreator("SMK N 1 PURING")
            ->setLastModifiedBy("SMK N 1 PURING")
            ->setTitle("Data Barang Masuk Habis Pakai")
            ->setSubject("Data Barang Masuk Habis Pakai")
            ->setDescription("Data Barang Masuk Habis Pakai")
            ->setKeywords("Data Barang Masuk Habis Pakai");

		$style_col = array(
			'font' => array(
				'bold' => true,
				'color' => array('rgb' => 'FFFFFF')
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER 
			),
			'borders' => array(
				'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),
				'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  
				'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), 
				'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) 
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => '6495ED')
			)
		);

		$style_row = array(
			'alignment' => array(
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER 
			),
			'borders' => array(
				'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), 
				'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  
				'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), 
				'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) 
			)
		);

		$style_isi_tengah = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			),
			'borders' => array(
				'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), 
				'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  
				'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), 
				'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) 
			)
		);

		$style_isi_kiri = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			),
			'borders' => array(
				'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), 
				'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  
				'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), 
				'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) 
			)
		);
	
		$url = api_url()."listbarangmasukhp";
		$output = call_api_get($url);
		$dt = json_decode($output);

		$url = api_url()."pengurusbarang";
		$output = call_api_get($url);
		$pengurus = json_decode($output);

        $data = $dt->data;		
		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('Logo');
		$objDrawing->setDescription('Logo');
		$objDrawing->setPath('assets/img/logo_smkn1puring.png');
		$objDrawing->setOffsetY(9);
		//$objDrawing->setOffsetX(4.1);
		$objDrawing->setCoordinates('C1');
		$objDrawing->setHeight(80);
		$objDrawing->setWorksheet($excel->getActiveSheet());

		$excel->setActiveSheetIndex(0)->setCellValue('A2', "DINAS PENDIDIKAN KABUPATEN KEBUMEN");
		$excel->getActiveSheet()->mergeCells('A2:G2');
		$excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 

		$excel->setActiveSheetIndex(0)->setCellValue('A3', "SEKOLAH MENENGAH KEJURUAN NEGERI 1 PURING");
		$excel->getActiveSheet()->mergeCells('A3:G3');
		$excel->getActiveSheet()->getStyle('A3')->getFont()->setBold(TRUE);
		$excel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$excel->setActiveSheetIndex(0)->setCellValue('A4', "Jl. Selatan-Selatan Kilometer 04 Puring - Kebumen, Kode Pos 54383");
		$excel->getActiveSheet()->mergeCells('A4:G4');
		$excel->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$excel->setActiveSheetIndex(0)->setCellValue('A5', "Email : smknegeri1puring@gmail.com - Telp : 0811-2635-864");
		$excel->getActiveSheet()->mergeCells('A5:G5');
		$excel->getActiveSheet()->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$excel->getActiveSheet()->getStyle('A6:F6')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
       
		$excel->setActiveSheetIndex(0)->setCellValue('A7', "DATA BARANG MASUK HABIS PAKAI");
        $excel->getActiveSheet()->mergeCells('A7:G7');
        $excel->getActiveSheet()->getStyle('A7')->getFont()->setBold(TRUE);
        $excel->getActiveSheet()->getStyle('A7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 

        $excel->setActiveSheetIndex(0)->setCellValue('A9', "NO"); 
        $excel->setActiveSheetIndex(0)->setCellValue('B9', "KODE BARANG"); 
        $excel->setActiveSheetIndex(0)->setCellValue('C9', "NAMA BARANG"); 
        $excel->setActiveSheetIndex(0)->setCellValue('D9', "KATEGORI");
		$excel->setActiveSheetIndex(0)->setCellValue('E9', "TGL MASUK");
		$excel->setActiveSheetIndex(0)->setCellValue('F9', "SATUAN");

        $excel->getActiveSheet()->getStyle('A9')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('B9')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('C9')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('D9')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('E9')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('F9')->applyFromArray($style_col);

        $no = 1; 
        $numrow = 10; 
        foreach($data as $r):
			$excel->setActiveSheetIndex(0)->setCellValue('A'.$numrow, $no);
            $excel->setActiveSheetIndex(0)->setCellValue('B'.$numrow, $r->kode_hp);
            $excel->setActiveSheetIndex(0)->setCellValue('C'.$numrow, $r->barang);
            $excel->setActiveSheetIndex(0)->setCellValue('D'.$numrow, $r->kategori);
            $excel->setActiveSheetIndex(0)->setCellValue('E'.$numrow, date('d-m-Y', strtotime($r->tgl_masuk)));
            $excel->setActiveSheetIndex(0)->setCellValue('F'.$numrow, $r->satuan);

            $excel->getActiveSheet()->getStyle('A'.$numrow)->applyFromArray($style_isi_tengah);
            $excel->getActiveSheet()->getStyle('B'.$numrow)->applyFromArray($style_row);
            $excel->getActiveSheet()->getStyle('C'.$numrow)->applyFromArray($style_row);
            $excel->getActiveSheet()->getStyle('D'.$numrow)->applyFromArray($style_row);
            $excel->getActiveSheet()->getStyle('E'.$numrow)->applyFromArray($style_row);
            $excel->getActiveSheet()->getStyle('F'.$numrow)->applyFromArray($style_row);
			
            $no++;
            $numrow++;
        endforeach;

		$row_puring = $numrow + 1;
		$row_pengurus = $numrow + 2;
		$row_nama_pengurus = $numrow + 6;
		$row_nip_pengurus = $numrow + 7;
		date_default_timezone_set('Asia/Jakarta');
		$excel->getActiveSheet()->setCellValue('E'.$row_puring, 'Puring, '.tgl_indo(date('Y-m-d')));
		$excel->getActiveSheet()->setCellValue('E'.$row_pengurus, 'Pengurus Barang');
		$excel->getActiveSheet()->setCellValue('E'.$row_nama_pengurus, $pengurus[0]->nama);
		$excel->getActiveSheet()->setCellValue('E'.$row_nip_pengurus, 'NIP. '.$pengurus[0]->nip);

        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(5); 
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(30); 
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);
        $excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $excel->getActiveSheet(0)->setTitle("Barang Masuk Habis Pakai");
        $excel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="data-barang-masuk-habis-pakai.xlsx"'); 
        header('Cache-Control: max-age=0');
        $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $write->save('php://output');  
	}

}