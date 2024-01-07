<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Perbaikan extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		cek_login();
    } 

    function index()
	{	
		$data['title'] = 'Perbaikan Barang';
		$url = api_url()."listbaranginv";
		$output = call_api_get($url);
		$barang = json_decode($output);

		$url = api_url()."listkondisi";
		$output = call_api_get($url);
		$kondisi = json_decode($output);

		$data['barang'] = $barang->data;
		$data['kondisi'] = $kondisi->data;
		//layout
		$this->load->view('backend/template/head');
		$this->load->view('backend/template/topbar');
		$this->load->view('backend/template/nav');
		$this->load->view('perbaikan/index', $data);
		$this->load->view('backend/template/js');
		$this->load->view('perbaikan/script');
    }

    function get_data_perbaikan()
	{
		$page = $_POST['start'] / $_POST['length'] + 1;
		$limit = $_POST['length'];
		$search = urlencode($_POST['search']['value']);
		if(!empty($search))
		{
			$url = api_url()."list-perbaikan?page=$page&limit=$limit&search=$search";
		}else
		{
			$url = api_url()."list-perbaikan?page=$page&limit=$limit";
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
            $row[] = date('d-m-Y', strtotime($r->tgl));
            $row[] = $r->siapa;
            $row[] = $r->no_hp;
            $row[] = '<div class="text-center">'.$kondisi.'</div>';
            $action = '<div class="text-center">
						<a href="javascript:void(0)" class="btn btn-info btn-xs" title="EDIT DATA" onclick="edit_perbaikan('.$r->id.')"><i class="fa fa-edit"></i> EDIT</a>
						<a href="javascript:void(0)" class="btn btn-danger btn-xs" title="HAPUS DATA" onclick="delete_perbaikan('.$r->id.')"><i class="fa fa-trash"></i> HAPUS</a>
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

	function tambah_perbaikan()
	{ 
		$this->_tambah_validate();  
        $id_baranginv = $this->input->post('id_baranginv', TRUE);
		$tgl = $this->input->post('tgl', TRUE);
		$siapa = $this->input->post('siapa', TRUE);
		$no_hp = $this->input->post('no_hp', TRUE);
		$id_kondisi = $this->input->post('id_kondisi', TRUE);

		$param = '{ "id_baranginv":"'.$id_baranginv.'", "tgl":"'.$tgl.'", "siapa":"'.$siapa.'", "no_hp":"'.$no_hp.'", "id_kondisi":"'.$id_kondisi.'" }';
		
        $url = api_url().'tambah-perbaikan';
		$output = call_api_post($url, $param);
		echo $output;			
    }

	function get_perbaikan_by_id($id)
	{ 
        $url = api_url()."get_perbaikan_by_id/$id";
		$output = call_api_get($url);
		echo $output;
    }

    function edit_perbaikan()
	{ 	
		$this->_edit_validate();  
		$id = $this->input->post('id', TRUE);
		$id_baranginv = $this->input->post('id_baranginv_edit', TRUE);
		$tgl = $this->input->post('tgl', TRUE);
		$siapa = $this->input->post('siapa', TRUE);
		$no_hp = $this->input->post('no_hp', TRUE);
		$id_kondisi = $this->input->post('id_kondisi', TRUE);

		$param = '{ "id_baranginv":"'.$id_baranginv.'", "tgl":"'.$tgl.'", "siapa":"'.$siapa.'", "no_hp":"'.$no_hp.'", "id_kondisi":"'.$id_kondisi.'" }';
		
		$url = api_url()."edit-perbaikan/$id";
		$output = call_api_put($url, $param);		
		echo $output;
    }

    function hapus_perbaikan($id)
	{ 
        $url = api_url()."hapus-perbaikan/$id";
		$output = call_api_delete($url);
		echo $output; 
    }

	private function _tambah_validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('id_baranginv') == '')
		{
			$data['inputerror'][] = 'id_baranginv';
			$data['error_string'][] = 'Barang wajib diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('tgl') == '')
		{
			$data['inputerror'][] = 'tgl';
			$data['error_string'][] = 'Tgl Diperbaiki wajib diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('siapa') == '')
		{
			$data['inputerror'][] = 'siapa';
			$data['error_string'][] = 'Nama Yang Memperbaiki wajib diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('no_hp') == '')
		{
			$data['inputerror'][] = 'no_hp';
			$data['error_string'][] = 'No HP Yang Memperbaiki wajib diisi';
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

	private function _edit_validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('tgl') == '')
		{
			$data['inputerror'][] = 'tgl';
			$data['error_string'][] = 'Tgl Diperbaiki wajib diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('siapa') == '')
		{
			$data['inputerror'][] = 'siapa';
			$data['error_string'][] = 'Nama Yang Memperbaiki wajib diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('no_hp') == '')
		{
			$data['inputerror'][] = 'no_hp';
			$data['error_string'][] = 'No HP Yang Memperbaiki wajib diisi';
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

	function cetak_perbaikan()
	{	
		$url = api_url()."listperbaikan";
		$output = call_api_get($url);
		$dt = json_decode($output);

		$url = api_url()."pengurusbarang";
		$output = call_api_get($url);
		$pengurus = json_decode($output);

		$data['data'] = $dt->data;
		$data['pengurus'] = $pengurus[0];
		$this->load->view('perbaikan/cetak_perbaikan', $data);
	}

	function cetak_perbaikan_pdf()
	{	
		$url = api_url()."listperbaikan";
		$output = call_api_get($url);
		$dt = json_decode($output);

		$url = api_url()."pengurusbarang";
		$output = call_api_get($url);
		$pengurus = json_decode($output);

		$data['data'] = $dt->data;
		$data['pengurus'] = $pengurus[0];
		$this->load->library('pdfgenerator');
		$html = $this->load->view('perbaikan/cetak_perbaikan_pdf', $data, true);
		$filename = 'Data Perbaikan Barang - Aplikasi Manajemen Barang SMK N 1 Puring';
		$this->pdfgenerator->generate($html, $filename, TRUE, 'A4', 'landscape');	
	}

	function export_perbaikan()
    {
        include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        $excel = new PHPExcel();

        $excel->getProperties()->setCreator("SMK N 1 PURING")
            ->setLastModifiedBy("SMK N 1 PURING")
            ->setTitle("Data Perbaikan Barang")
            ->setSubject("Data Perbaikan Barang")
            ->setDescription("Data Perbaikan Barang")
            ->setKeywords("Data Perbaikan Barang");

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
	
        $url = api_url()."listperbaikan";
		$output = call_api_get($url);
		$dt = json_decode($output);

		$url = api_url()."pengurusbarang";
		$output = call_api_get($url);
		$pengurus = json_decode($output);

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
       
		$excel->setActiveSheetIndex(0)->setCellValue('A7', "DATA PERBAIKAN BARANG");
        $excel->getActiveSheet()->mergeCells('A7:G7');
        $excel->getActiveSheet()->getStyle('A7')->getFont()->setBold(TRUE);
        $excel->getActiveSheet()->getStyle('A7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 

        $excel->setActiveSheetIndex(0)->setCellValue('A9', "NO"); 
        $excel->setActiveSheetIndex(0)->setCellValue('B9', "KODE BARANG"); 
        $excel->setActiveSheetIndex(0)->setCellValue('C9', "NAMA BARANG"); 
        $excel->setActiveSheetIndex(0)->setCellValue('D9', "TGL DIPERBAIKI");
		$excel->setActiveSheetIndex(0)->setCellValue('E9', "NAMA MEMPERBAIKI");
		$excel->setActiveSheetIndex(0)->setCellValue('F9', "NO HP");
		$excel->setActiveSheetIndex(0)->setCellValue('G9', "HASIL PERBAIKAN");

        $excel->getActiveSheet()->getStyle('A9')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('B9')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('C9')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('D9')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('E9')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('F9')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('G9')->applyFromArray($style_col);

        $no = 1; 
        $numrow = 10; 
        foreach($dt->data as $r):
			$excel->setActiveSheetIndex(0)->setCellValue('A'.$numrow, $no);
            $excel->setActiveSheetIndex(0)->setCellValue('B'.$numrow, $r->kode_inv);
            $excel->setActiveSheetIndex(0)->setCellValue('C'.$numrow, $r->barang);
            $excel->setActiveSheetIndex(0)->setCellValue('D'.$numrow, date('d-m-Y', strtotime($r->tgl)));
            $excel->setActiveSheetIndex(0)->setCellValue('E'.$numrow, $r->siapa);
            $excel->setActiveSheetIndex(0)->setCellValue('F'.$numrow, $r->no_hp);
			$excel->setActiveSheetIndex(0)->setCellValue('G'.$numrow, $r->kondisi);

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
		date_default_timezone_set('Asia/Jakarta');
		$excel->getActiveSheet()->setCellValue('F'.$row_puring, 'Puring, '.tgl_indo(date('Y-m-d')));
		$excel->getActiveSheet()->setCellValue('F'.$row_pengurus, 'Pengurus Barang');
		$excel->getActiveSheet()->setCellValue('F'.$row_nama_pengurus, $pengurus[0]->nama);
		$excel->getActiveSheet()->setCellValue('F'.$row_nip_pengurus, 'NIP. '.$pengurus[0]->nip);

        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(5); 
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(20); 
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);
        $excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $excel->getActiveSheet(0)->setTitle("Perbaikan Barang");
        $excel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="data-perbaikan-barang.xlsx"'); 
        header('Cache-Control: max-age=0');
        $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $write->save('php://output');  
	}

}
