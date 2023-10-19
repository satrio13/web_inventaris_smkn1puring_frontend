<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Gedung extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		cek_login();
    } 

    function index()
	{	
		$data['title'] = 'Gedung';
		//layout
		$this->load->view('backend/template/head');
		$this->load->view('backend/template/topbar');
		$this->load->view('backend/template/nav');
		$this->load->view('gedung/index', $data);
		$this->load->view('backend/template/js');
		$this->load->view('gedung/script');
    }

    function get_data_gedung()
	{
		$page = $_POST['start'] / $_POST['length'] + 1;
		$limit = $_POST['length'];
		$search = $_POST['search']['value'];
		if(!empty($search))
		{
			$url = api_url()."list-gedung?page=$page&limit=$limit&search=$search";
		}else
		{
			$url = api_url()."list-gedung?page=$page&limit=$limit";
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
			$row[] = $r->nama_gedung;
			$row[] = $r->luas;
			$row[] = $r->tahun_p;
			$row[] = $r->sumberdana;
			$action = '<div class="text-center">
						<a href="javascript:void(0)" class="btn btn-info btn-xs" title="EDIT DATA" onclick="edit_gedung('.$r->id_gedung.')"><i class="fa fa-edit"></i> EDIT</a>
						<a href="javascript:void(0)" class="btn btn-danger btn-xs" title="HAPUS DATA" onclick="delete_gedung('.$r->id_gedung.')"><i class="fa fa-trash"></i> HAPUS</a>
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

	function tambah_gedung()
	{ 
		$this->_validate();  
		$url = api_url().'tambah-gedung';
		$nama_gedung = $this->input->post('nama_gedung', true);
		$luas = $this->input->post('luas', true);
		$tahun_p = $this->input->post('tahun_p', true);
		$sumberdana = $this->input->post('sumberdana', true);
		
		$param = '{ "nama_gedung":"'.$nama_gedung.'", "luas":"'.$luas.'", "tahun_p":"'.$tahun_p.'", "sumberdana":"'.$sumberdana.'" }';
		
        $output = call_api_post($url, $param);
		echo $output;	
    }

	function get_gedung_by_id($id_gedung)
	{
		$url = api_url()."get_gedung_by_id/$id_gedung";
		$output = call_api_get($url);
		echo $output;
	}

	function edit_gedung()
	{ 
		$this->_validate();
		$id_gedung = $this->input->post('id_gedung', true);
		$nama_gedung = $this->input->post('nama_gedung', true);
		$luas = $this->input->post('luas', true);
		$tahun_p = $this->input->post('tahun_p', true);
		$sumberdana = $this->input->post('sumberdana', true);
		$url = api_url()."edit-gedung/$id_gedung";
		
		$param = '{ "nama_gedung":"'.$nama_gedung.'", "luas":"'.$luas.'", "tahun_p":"'.$tahun_p.'", "sumberdana":"'.$sumberdana.'" }';

		$output = call_api_put($url, $param);
		echo $output;	
    }

	function hapus_gedung($id_gedung)
	{ 
		$url = api_url()."hapus-gedung/$id_gedung";
		$output = call_api_delete($url);
		echo $output;
    }

	function cetak_gedung_pdf()
	{	
		$url = api_url()."listgedung";
		$output = call_api_get($url);
		$dt = json_decode($output);

		$data['data'] = $dt->data;
		$this->load->library('pdfgenerator');
		$html = $this->load->view('gedung/cetak_gedung_pdf', $data, true);
		$filename = 'Gedung - Aplikasi Manajemen Barang SMK N 1 Puring';
		$this->pdfgenerator->generate($html, $filename, TRUE, 'A4', 'landscape');	
	}

	function cetak_gedung()
	{	
		$url = api_url()."listgedung";
		$output = call_api_get($url);
		$dt = json_decode($output);
		
		$data['data'] = $dt->data;
		$this->load->view('gedung/cetak_gedung', $data);
	}

	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('nama_gedung') == '')
		{
			$data['inputerror'][] = 'nama_gedung';
			$data['error_string'][] = 'Nama Gedung wajib diisi';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}
 
}