<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Baranginv extends CI_Controller 
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
		
		$url = api_url()."list-kondisi";
		$output = call_api_get($url);
		$dt2 = json_decode($output);

		$data['title'] = 'Barang Inventaris';
		$data['kategori'] = $dt->data;
		$data['kondisi'] = $dt2->data;
		//layout
		$this->load->view('backend/template/head');
		$this->load->view('backend/template/topbar');
		$this->load->view('backend/template/nav');
		$this->load->view('baranginv/index', $data);
		$this->load->view('backend/template/js');
		$this->load->view('baranginv/script');
    }

    function get_data_baranginv()
	{
		$page = $_POST['start'] / $_POST['length'] + 1;
		$limit = $_POST['length'];
		$search = urlencode($_POST['search']['value']);
		if(!empty($search))
		{
			$url = api_url()."list-baranginv?page=$page&limit=$limit&search=$search";
		}else
		{
			$url = api_url()."list-baranginv?page=$page&limit=$limit";
		}

		$output = call_api_get($url);      
		$dt = json_decode($output);

		$list = $dt->data;
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $r)
		{	
			if($r->id_kondisi == 1)
			{
				$kondisi = '<label class="badge badge-primary">Baik</label>';
			}elseif($r->id_kondisi == 2)
			{
				$kondisi = '<label class="badge bg-warning"><span class="text-white">Rusak Ringan</span></label>';
			}elseif($r->id_kondisi == 3)
			{
				$kondisi = '<label class="badge bg-orange"><span class="text-white">Rusak Sedang</span></label>';
			}elseif($r->id_kondisi == 4)
			{
				$kondisi = '<label class="badge badge-danger">Rusak Berat</label>';
			}elseif($r->id_kondisi == 5)
			{
				$kondisi = '<label class="badge bg-maroon">Hilang</label>';
			
			}elseif($r->id_kondisi == 6)
			{
				$kondisi = '<label class="badge bg-navy">Dihapus</label>';
			}else
			{
				$kondisi = '';
			}

			$no++;
			$row = array();
			$row[] = '<div class="text-center">'.$no.'</div>';
            $row[] = $r->kode_inv;
            $row[] = $r->barang;
            $row[] = $r->kategori;
            $row[] = $r->merk;
            $row[] = $r->satuan;
            $row[] = $r->th_beli;
			$row[] = '<div class="text-center">'.$kondisi.'</div>';
            $row[] = $r->keterangan;
            $action = '<div class="text-center">
						<a href="javascript:void(0)" class="btn btn-info btn-xs" title="EDIT DATA" onclick="edit_baranginv('.$r->id_baranginv.')"><i class="fa fa-edit"></i> EDIT</a>
						<a href="javascript:void(0)" class="btn btn-danger btn-xs" title="HAPUS DATA" onclick="delete_baranginv('.$r->id_baranginv.')"><i class="fa fa-trash"></i> HAPUS</a>
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

	function tambah_baranginv()
	{ 
		$this->_validate_tambah();  
		$url = api_url().'tambah-baranginv';
		$kode_inv = $this->input->post('kode_inv', true);
		$barang = $this->input->post('barang', true);
		$merk = $this->input->post('merk', true);
		$satuan = $this->input->post('satuan', true);
		$id_kategori = $this->input->post('id_kategori', true);
		$th_beli = $this->input->post('th_beli', true);
		$id_kondisi = $this->input->post('id_kondisi', true);
		$keterangan = $this->input->post('keterangan', true);

		$param = '{ "kode_inv":"'.$kode_inv.'", "barang":"'.$barang.'", "merk":"'.$merk.'", "satuan":"'.$satuan.'", "id_kategori":"'.$id_kategori.'", "th_beli":"'.$th_beli.'", "id_kondisi":"'.$id_kondisi.'", "keterangan":"'.$keterangan.'" }';
		
		$output = call_api_post($url, $param);
		echo $output;	
    }

	function get_baranginv_by_id($id_baranginv)
	{
		$url = api_url()."get_baranginv_by_id/$id_baranginv";
		$output = call_api_get($url);
		echo $output;
	}

    function edit_baranginv()
	{ 
		$id_baranginv = $this->input->post('id_baranginv', true);
		$kode_inv = $this->input->post('kode_inv', true);
		$this->_validate_edit($id_baranginv, $kode_inv);
		$barang = $this->input->post('barang', true);
		$merk = $this->input->post('merk', true);
		$satuan = $this->input->post('satuan', true);
		$id_kategori = $this->input->post('id_kategori', true);
		$th_beli = $this->input->post('th_beli', true);
		$id_kondisi = $this->input->post('id_kondisi', true);
		$keterangan = $this->input->post('keterangan', true);

		$url = api_url()."edit-baranginv/$id_baranginv";
		
		$param = '{ "id_baranginv":"'.$id_baranginv.'", "kode_inv":"'.$kode_inv.'", "barang":"'.$barang.'", "merk":"'.$merk.'", "satuan":"'.$satuan.'", "id_kategori":"'.$id_kategori.'", "th_beli":"'.$th_beli.'", "id_kondisi":"'.$id_kondisi.'", "keterangan":"'.$keterangan.'" }';

		$output = call_api_put($url, $param);
		echo $output;	
    }

	function hapus_baranginv($id_baranginv)
	{ 
		$url = api_url()."hapus-baranginv/$id_baranginv";
		$output = call_api_delete($url);		
		echo $output;
    }

	private function _validate_tambah()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		$kode_inv = $this->input->post('kode_inv');
		$cek = $this->_cek_kode_inv_tambah($kode_inv);

		if($kode_inv == '')
		{
			$data['inputerror'][] = 'kode_inv';
			$data['error_string'][] = 'Kode Barang wajib diisi';
			$data['status'] = FALSE;
		}elseif($cek == FALSE)
		{
			$data['inputerror'][] = 'kode_inv';
			$data['error_string'][] = 'Kode Barang sudah digunakan!';
			$data['status'] = FALSE;
		}

		if($this->input->post('barang') == '')
		{
			$data['inputerror'][] = 'barang';
			$data['error_string'][] = 'Nama Barang wajib diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('satuan') == '')
		{
			$data['inputerror'][] = 'satuan';
			$data['error_string'][] = 'Satuan wajib diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('id_kategori') == '')
		{
			$data['inputerror'][] = 'id_kategori';
			$data['error_string'][] = 'Kategori Barang wajib diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('id_kondisi') == '')
		{
			$data['inputerror'][] = 'id_kondisi';
			$data['error_string'][] = 'Kondisi Barang wajib diisi';
			$data['status'] = FALSE;
		}
		
		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	private function _validate_edit($id_baranginv, $kode_inv)
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		$cek = $this->_cek_kode_inv_edit($id_baranginv, $kode_inv);

		if($kode_inv == '')
		{
			$data['inputerror'][] = 'kode_inv';
			$data['error_string'][] = 'Kode Barang wajib diisi';
			$data['status'] = FALSE;
		}elseif($cek == FALSE)
		{
			$data['inputerror'][] = 'kode_inv';
			$data['error_string'][] = 'Kode Barang sudah digunakan!';
			$data['status'] = FALSE;
		}

		if($this->input->post('barang') == '')
		{
			$data['inputerror'][] = 'barang';
			$data['error_string'][] = 'Nama Barang wajib diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('satuan') == '')
		{
			$data['inputerror'][] = 'satuan';
			$data['error_string'][] = 'Satuan wajib diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('id_kategori') == '')
		{
			$data['inputerror'][] = 'id_kategori';
			$data['error_string'][] = 'Kategori Barang wajib diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('id_kondisi') == '')
		{
			$data['inputerror'][] = 'id_kondisi';
			$data['error_string'][] = 'Kondisi Barang wajib diisi';
			$data['status'] = FALSE;
		}
		
		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	function _cek_kode_inv_tambah($kode_inv = '')
    {
	    $url = api_url()."get_baranginv_by_kode/$kode_inv";
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

    function _cek_kode_inv_edit($id_baranginv = '', $kode_inv = '')
    {
		$url = api_url()."validasi_edit_baranginv/$id_baranginv/$kode_inv";
		$output = call_api_get($url);
		$dt = json_decode($output);
		
		return $dt->status;
	}
	
	function form_baranginv()
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
			curl_setopt($curl, CURLOPT_URL, api_url().'import-baranginv');
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($curl);
			curl_close($curl);
			$upload = json_decode($response);

			if($upload->status == true)
			{
				$this->session->set_flashdata('msg-import-baranginv', $upload->message);
				redirect('backend/baranginv');
			}else
			{
				$this->session->set_flashdata('msg-gagal-import-baranginv', $upload->message);
			}
		}
		$data['title']='Import Data Barang Inventaris';
		//layout
		$this->load->view('backend/template/head');
		$this->load->view('backend/template/topbar');
		$this->load->view('backend/template/nav');
		$this->load->view('baranginv/form_baranginv', $data);
		$this->load->view('backend/template/js');
		$this->load->view('baranginv/script');
	}
	
	function cetak_baranginv_pdf()
	{	
		$url = api_url()."listbaranginv";
		$output = call_api_get($url);
		$dt = json_decode($output);

		$url = api_url()."pengurusbarang";
		$output = call_api_get($url); 
		$pengurus = json_decode($output);

		$data['data'] = $dt->data;
		$data['pengurus'] = $pengurus[0];
		$this->load->library('pdfgenerator');
		$html = $this->load->view('baranginv/cetak_baranginv_pdf', $data, true);
		$filename = 'Barang Inventaris - Aplikasi Manajemen Barang SMK N 1 Puring';
		$this->pdfgenerator->generate($html, $filename, TRUE, 'A4', 'landscape');	
	}

	function cetak_baranginv()
	{	
		$url = api_url()."listbaranginv";
		$output = call_api_get($url);
		$dt = json_decode($output);

		$url = api_url()."pengurusbarang";
		$output = call_api_get($url); 
		$pengurus = json_decode($output);

		$data['data'] = $dt->data;
		$data['pengurus'] = $pengurus[0];
		$this->load->view('baranginv/cetak_baranginv', $data);
	}

	function export_baranginv()
    {
        include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        $excel = new PHPExcel();

        $excel->getProperties()->setCreator("SMK N 1 PURING")
            ->setLastModifiedBy("SMK N 1 PURING")
            ->setTitle("Data Barang Inventaris")
            ->setSubject("Data Barang Inventaris")
            ->setDescription("Data Barang Inventaris")
            ->setKeywords("Data Barang Inventaris");

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
		
		$url = api_url()."listbaranginv";
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
		$excel->getActiveSheet()->mergeCells('A2:I2');
		$excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 

		$excel->setActiveSheetIndex(0)->setCellValue('A3', "SEKOLAH MENENGAH KEJURUAN NEGERI 1 PURING");
		$excel->getActiveSheet()->mergeCells('A3:I3');
		$excel->getActiveSheet()->getStyle('A3')->getFont()->setBold(TRUE);
		$excel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$excel->setActiveSheetIndex(0)->setCellValue('A4', "Jl. Selatan-Selatan Kilometer 04 Puring - Kebumen, Kode Pos 54383");
		$excel->getActiveSheet()->mergeCells('A4:I4');
		$excel->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$excel->setActiveSheetIndex(0)->setCellValue('A5', "Email : smknegeri1puring@gmail.com - Telp : 0811-2635-864");
		$excel->getActiveSheet()->mergeCells('A5:I5');
		$excel->getActiveSheet()->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$excel->getActiveSheet()->getStyle('A6:I6')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
       
		$excel->setActiveSheetIndex(0)->setCellValue('A7', "DATA BARANG INVENTARIS");
        $excel->getActiveSheet()->mergeCells('A7:I7');
        $excel->getActiveSheet()->getStyle('A7')->getFont()->setBold(TRUE);
        $excel->getActiveSheet()->getStyle('A7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 

        $excel->setActiveSheetIndex(0)->setCellValue('A9', "NO"); 
        $excel->setActiveSheetIndex(0)->setCellValue('B9', "KODE BARANG"); 
        $excel->setActiveSheetIndex(0)->setCellValue('C9', "NAMA BARANG"); 
        $excel->setActiveSheetIndex(0)->setCellValue('D9', "KATEGORI");
		$excel->setActiveSheetIndex(0)->setCellValue('E9', "MERK");
		$excel->setActiveSheetIndex(0)->setCellValue('F9', "SATUAN");
		$excel->setActiveSheetIndex(0)->setCellValue('G9', "TAHUN BELI");
		$excel->setActiveSheetIndex(0)->setCellValue('H9', "KONDISI");
		$excel->setActiveSheetIndex(0)->setCellValue('I9', "KETERANGAN");

        $excel->getActiveSheet()->getStyle('A9')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('B9')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('C9')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('D9')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('E9')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('F9')->applyFromArray($style_col);
		$excel->getActiveSheet()->getStyle('G9')->applyFromArray($style_col);
		$excel->getActiveSheet()->getStyle('H9')->applyFromArray($style_col);
		$excel->getActiveSheet()->getStyle('I9')->applyFromArray($style_col);
        $no = 1; 
        $numrow = 10; 
        foreach($data as $r):
			
			if($r->id_kondisi == 1)
			{
				$kondisi = 'Baik';
			}elseif($r->id_kondisi == 2)
			{
				$kondisi = 'Rusak Ringan';
			}elseif($r->id_kondisi == 3)
			{
				$kondisi = 'Rusak Sedang';
			}elseif($r->id_kondisi == 4)
			{
				$kondisi = 'Rusak Berat';
			}elseif($r->id_kondisi == 5)
			{
				$kondisi = 'Hilang';
			}elseif($r->id_kondisi == 6)
			{
				$kondisi = 'Dihapus';
			}else
			{
				$kondisi = '';
			}

			$excel->setActiveSheetIndex(0)->setCellValue('A'.$numrow, $no);
            $excel->setActiveSheetIndex(0)->setCellValue('B'.$numrow, $r->kode_inv);
            $excel->setActiveSheetIndex(0)->setCellValue('C'.$numrow, $r->barang);
            $excel->setActiveSheetIndex(0)->setCellValue('D'.$numrow, $r->kategori);
            $excel->setActiveSheetIndex(0)->setCellValue('E'.$numrow, $r->merk);
            $excel->setActiveSheetIndex(0)->setCellValue('F'.$numrow, $r->satuan);
			$excel->setActiveSheetIndex(0)->setCellValue('G'.$numrow, $r->th_beli);
			$excel->setActiveSheetIndex(0)->setCellValue('H'.$numrow, $kondisi);
			$excel->setActiveSheetIndex(0)->setCellValue('I'.$numrow, $r->keterangan);

            $excel->getActiveSheet()->getStyle('A'.$numrow)->applyFromArray($style_isi_tengah);
            $excel->getActiveSheet()->getStyle('B'.$numrow)->applyFromArray($style_row);
            $excel->getActiveSheet()->getStyle('C'.$numrow)->applyFromArray($style_row);
            $excel->getActiveSheet()->getStyle('D'.$numrow)->applyFromArray($style_row);
            $excel->getActiveSheet()->getStyle('E'.$numrow)->applyFromArray($style_row);
            $excel->getActiveSheet()->getStyle('F'.$numrow)->applyFromArray($style_row);
            $excel->getActiveSheet()->getStyle('G'.$numrow)->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('H'.$numrow)->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('I'.$numrow)->applyFromArray($style_row);
            $no++;
            $numrow++;
        endforeach;

		$row_puring = $numrow + 1;
		$row_pengurus = $numrow + 2;
		$row_nama_pengurus = $numrow + 6;
		$row_nip_pengurus = $numrow + 7;
		$excel->getActiveSheet()->setCellValue('G'.$row_puring, 'Puring, '.tgl_indo(date('Y-m-d')));
		$excel->getActiveSheet()->setCellValue('G'.$row_pengurus, 'Pengurus Barang');
		$excel->getActiveSheet()->setCellValue('G'.$row_nama_pengurus, $pengurus[0]->nama);
		$excel->getActiveSheet()->setCellValue('G'.$row_nip_pengurus, 'NIP. '.$pengurus[0]->nip);

        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(5); 
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(30); 
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
		$excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
		$excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
		$excel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
        $excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);
        $excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $excel->getActiveSheet(0)->setTitle("Barang Inventaris");
        $excel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="data-barang-inventaris.xlsx"'); 
        header('Cache-Control: max-age=0');
        $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $write->save('php://output');  
	}

}
