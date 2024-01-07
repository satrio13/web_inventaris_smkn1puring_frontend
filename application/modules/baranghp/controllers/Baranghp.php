<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Baranghp extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		cek_login();
		date_default_timezone_set('Asia/Jakarta');
    } 

    function index()
	{	
	 	$url = api_url()."listkategori";
		$output = call_api_get($url);
		$dt = json_decode($output);

		$data['title'] = 'Barang Habis Pakai';
		$data['kategori'] = $dt->data;
		//layout
		$this->load->view('backend/template/head');
		$this->load->view('backend/template/topbar');
		$this->load->view('backend/template/nav');
		$this->load->view('baranghp/index', $data);
		$this->load->view('backend/template/js');
		$this->load->view('baranghp/script');
    }

    function get_data_baranghp()
	{
		$page = $_POST['start'] / $_POST['length'] + 1;
		$limit = $_POST['length'];
		$search = urlencode($_POST['search']['value']);
		if(!empty($search))
		{
			$url = api_url()."list-baranghp?page=$page&limit=$limit&search=$search";
		}else
		{
			$url = api_url()."list-baranghp?page=$page&limit=$limit";
		}

		$output = call_api_get($url);
		$dt = json_decode($output);

		$list = $dt->data;
		$data = array();
		$no = $_POST['start'];
        foreach($list as $r)
        {
			$no++;
			$row = array();
			$row[] = '<div class="text-center">'.$no.'</div>';
			$row[] = $r->kode_hp;
			$row[] = $r->barang;
			$row[] = $r->kategori;
			$row[] = '<div class="text-right">'.$r->stok.'</div>';
			$row[] = $r->satuan;
			$row[] = '<div class="text-right">'.number_format($r->harga, 0, ',', '.').'</div>';
			$action = '<div class="text-center">
						<a href="javascript:void(0)" class="btn btn-info btn-xs" title="EDIT DATA" onclick="edit_baranghp('.$r->id_baranghp.')"><i class="fa fa-edit"></i> EDIT</a>
						<a href="javascript:void(0)" class="btn btn-danger btn-xs" title="HAPUS DATA" onclick="delete_baranghp('.$r->id_baranghp.')"><i class="fa fa-trash"></i> HAPUS</a>
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

		echo json_encode($output);
    }

	function tambah_baranghp()
	{ 
		$this->_validate_tambah();  
		$url = api_url().'tambah-baranghp';
		$kode_hp = $this->input->post('kode_hp', true);
		$barang = $this->input->post('barang', true);
		$id_kategori = $this->input->post('id_kategori', true);
		$satuan = $this->input->post('satuan', true);
		$harga = $this->input->post('harga', true);

		$param = '{ "kode_hp":"'.$kode_hp.'", "barang":"'.$barang.'", "id_kategori":"'.$id_kategori.'", "satuan":"'.$satuan.'", "harga":"'.$harga.'" }';
		
		$output = call_api_post($url, $param);
		echo $output;	
    }

	function get_baranghp_by_id($id_baranghp)
	{
		$url = api_url()."get_baranghp_by_id/$id_baranghp";
		$output = call_api_get($url);
		echo $output;
	}

	function edit_baranghp()
	{ 
		$id_baranghp = $this->input->post('id_baranghp', true);
		$kode_hp = $this->input->post('kode_hp', true);
		$this->_validate_edit($id_baranghp, $kode_hp);
		$barang = $this->input->post('barang', true);
		$id_kategori = $this->input->post('id_kategori', true);
		$satuan = $this->input->post('satuan', true);
		$harga = $this->input->post('harga', true);
		$url = api_url()."edit-baranghp/$id_baranghp";
		
		$param = '{ "id_baranghp":"'.$id_baranghp.'", "kode_hp":"'.$kode_hp.'", "barang":"'.$barang.'", "id_kategori":"'.$id_kategori.'", "satuan":"'.$satuan.'", "harga":"'.$harga.'" }';
		
        $output = call_api_put($url, $param);
		echo $output;	
    }

	function hapus_baranghp($id_baranghp)
	{ 
		$url = api_url()."hapus-baranghp/$id_baranghp";
		$output = call_api_delete($url);
		echo $output;
    }

	private function _validate_tambah()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		$kode_hp = $this->input->post('kode_hp');
		$cek = $this->_cek_kode_hp_tambah($kode_hp);

		if($kode_hp == '')
		{
			$data['inputerror'][] = 'kode_hp';
			$data['error_string'][] = 'Kode Barang wajib diisi';
			$data['status'] = FALSE;
		}elseif($cek == FALSE)
		{
			$data['inputerror'][] = 'kode_hp';
			$data['error_string'][] = 'Kode Barang sudah digunakan!';
			$data['status'] = FALSE;
		}

		if($this->input->post('barang') == '')
		{
			$data['inputerror'][] = 'barang';
			$data['error_string'][] = 'Nama Barang wajib diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('id_kategori') == '')
		{
			$data['inputerror'][] = 'kategori';
			$data['error_string'][] = 'Kategori Barang wajib diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('satuan') == '')
		{
			$data['inputerror'][] = 'satuan';
			$data['error_string'][] = 'Satuan wajib diisi';
			$data['status'] = FALSE;
		}
		
		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	private function _validate_edit($id_baranghp, $kode_hp)
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		$cek = $this->_cek_kode_hp_edit($id_baranghp, $kode_hp);

		if($kode_hp == '')
		{
			$data['inputerror'][] = 'kode_hp';
			$data['error_string'][] = 'Kode Barang wajib diisi';
			$data['status'] = FALSE;
		}elseif($cek == FALSE)
		{
			$data['inputerror'][] = 'kode_hp';
			$data['error_string'][] = 'Kode Barang sudah digunakan!';
			$data['status'] = FALSE;
		}

		if($this->input->post('barang') == '')
		{
			$data['inputerror'][] = 'barang';
			$data['error_string'][] = 'Nama Barang wajib diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('id_kategori') == '')
		{
			$data['inputerror'][] = 'kategori';
			$data['error_string'][] = 'Kategori Barang wajib diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('satuan') == '')
		{
			$data['inputerror'][] = 'satuan';
			$data['error_string'][] = 'Satuan wajib diisi';
			$data['status'] = FALSE;
		}
		
		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}
	
	function _cek_kode_hp_tambah($kode_hp = '')
    {
		$url = api_url()."get_baranghp_by_kode/$kode_hp";
		$output = call_api_get($url);
		$dt = json_decode($output);

		if($dt->status == true)
        {
			return FALSE;
        }else
        {
			return TRUE;
		}
	}
	
	function _cek_kode_hp_edit($id_baranghp = '', $kode_hp = '')
    {
	    $url = api_url()."validasi_edit_baranghp/$id_baranghp/$kode_hp";
		$output = call_api_get($url);
		$dt = json_decode($output);
		
		return $dt->status;
	}

	function form_baranghp()
	{ 
		if(isset($_POST['preview']))
		{ 
			// Mendapatkan informasi file yang diupload
			$fileName = $_FILES['file']['name'];
			$fileTmpName = $_FILES['file']['tmp_name'];

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_HTTPHEADER, array(
				'Authorization: '.$this->session->userdata('token').'',
			));
			$postFields = array(
				'file' => new CurlFile($fileTmpName, 'application/octet-stream', $fileName)
			);
			curl_setopt($curl, CURLOPT_URL, api_url().'import-baranghp');
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($curl);
			curl_close($curl);
			$upload = json_decode($response);

			if($upload->status == true)
			{
				$this->session->set_flashdata('msg-import-baranghp', $upload->message);
				redirect('backend/baranghp');
			}else
			{
				$this->session->set_flashdata('msg-gagal-import-baranghp', $upload->message);
			}
		}
		$data['title']='Import Data Barang Hapis Pakai';
		//layout
		$this->load->view('backend/template/head');
		$this->load->view('backend/template/topbar');
		$this->load->view('backend/template/nav');
		$this->load->view('baranghp/form_baranghp', $data);
		$this->load->view('backend/template/js');
		$this->load->view('baranghp/script');
	}

	function cetak_baranghp_pdf()
	{	
		$url = api_url()."listbaranghp";
		$output = call_api_get($url);
		$dt = json_decode($output);

		$url = api_url()."pengurusbarang";
		$output = call_api_get($url);
		$pengurus = json_decode($output);

		$data['data'] = $dt->data;
		$data['pengurus'] = $pengurus[0];
		$this->load->library('pdfgenerator');
		$html = $this->load->view('baranghp/cetak_baranghp_pdf', $data, true);
		$filename = 'Barang Habis Pakai - Aplikasi Manajemen Barang SMK N 1 Puring';
		$this->pdfgenerator->generate($html, $filename, TRUE, 'A4', 'landscape');	
	}

	function cetak_baranghp()
	{	
		$url = api_url()."listbaranghp";
		$output = call_api_get($url);
		$dt = json_decode($output);

		$url = api_url()."pengurusbarang";
		$output = call_api_get($url);
		$pengurus = json_decode($output);

		$data['data'] = $dt->data;
		$data['pengurus'] = $pengurus[0];
		$this->load->view('baranghp/cetak_baranghp', $data);
	}

	function export_baranghp()
    {
        include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        $excel = new PHPExcel();

        $excel->getProperties()->setCreator("SMK N 1 PURING")
            ->setLastModifiedBy("SMK N 1 PURING")
            ->setTitle("Data Barang Habis Pakai")
            ->setSubject("Data Barang Habis Pakai")
            ->setDescription("Data Barang Habis Pakai")
            ->setKeywords("Data Barang Habis Pakai");

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
	
		$url = api_url()."listbaranghp";
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

		$excel->getActiveSheet()->getStyle('A6:G6')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
       
		$excel->setActiveSheetIndex(0)->setCellValue('A7', "DATA BARANG HABIS PAKAI");
        $excel->getActiveSheet()->mergeCells('A7:G7');
        $excel->getActiveSheet()->getStyle('A7')->getFont()->setBold(TRUE);
        $excel->getActiveSheet()->getStyle('A7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 

        $excel->setActiveSheetIndex(0)->setCellValue('A9', "NO"); 
        $excel->setActiveSheetIndex(0)->setCellValue('B9', "KODE BARANG"); 
        $excel->setActiveSheetIndex(0)->setCellValue('C9', "NAMA BARANG"); 
        $excel->setActiveSheetIndex(0)->setCellValue('D9', "KATEGORI");
		$excel->setActiveSheetIndex(0)->setCellValue('E9', "STOK");
		$excel->setActiveSheetIndex(0)->setCellValue('F9', "SATUAN");
		$excel->setActiveSheetIndex(0)->setCellValue('G9', "HARGA SATUAN");

        $excel->getActiveSheet()->getStyle('A9')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('B9')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('C9')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('D9')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('E9')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('F9')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('G9')->applyFromArray($style_col);

        $no = 1; 
        $numrow = 10; 
        foreach($data as $r):
			$excel->setActiveSheetIndex(0)->setCellValue('A'.$numrow, $no);
            $excel->setActiveSheetIndex(0)->setCellValue('B'.$numrow, $r->kode_hp);
            $excel->setActiveSheetIndex(0)->setCellValue('C'.$numrow, $r->barang);
            $excel->setActiveSheetIndex(0)->setCellValue('D'.$numrow, $r->kategori);
            $excel->setActiveSheetIndex(0)->setCellValue('E'.$numrow, $r->stok);
            $excel->setActiveSheetIndex(0)->setCellValue('F'.$numrow, $r->satuan);
			$excel->setActiveSheetIndex(0)->setCellValue('G'.$numrow, $r->harga);

            $excel->getActiveSheet()->getStyle('A'.$numrow)->applyFromArray($style_isi_tengah);
            $excel->getActiveSheet()->getStyle('B'.$numrow)->applyFromArray($style_row);
            $excel->getActiveSheet()->getStyle('C'.$numrow)->applyFromArray($style_row);
            $excel->getActiveSheet()->getStyle('D'.$numrow)->applyFromArray($style_row);
            $excel->getActiveSheet()->getStyle('E'.$numrow)->applyFromArray($style_row);
            $excel->getActiveSheet()->getStyle('F'.$numrow)->applyFromArray($style_row);
            $excel->getActiveSheet()->getStyle('G'.$numrow)->applyFromArray($style_row);
			
            $no++;
            $numrow++;
        endforeach;

		$row_puring = $numrow + 1;
		$row_pengurus = $numrow + 2;
		$row_nama_pengurus = $numrow + 6;
		$row_nip_pengurus = $numrow + 7;
		$excel->getActiveSheet()->setCellValue('F'.$row_puring, 'Puring, '.tgl_indo(date('Y-m-d')));
		$excel->getActiveSheet()->setCellValue('F'.$row_pengurus, 'Pengurus Barang');
		$excel->getActiveSheet()->setCellValue('F'.$row_nama_pengurus, $pengurus[0]->nama);
		$excel->getActiveSheet()->setCellValue('F'.$row_nip_pengurus, 'NIP. '.$pengurus[0]->nip);

        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(5); 
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(30); 
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);
        $excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $excel->getActiveSheet(0)->setTitle("Barang Habis Pakai");
        $excel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="data-barang-habis-pakai.xlsx"'); 
        header('Cache-Control: max-age=0');
        $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $write->save('php://output');  
	}

}
