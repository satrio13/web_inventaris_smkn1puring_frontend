<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Ruang extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		cek_login();
    } 

    function index()
	{	
		$data['title'] = 'Ruang';
		//layout
		$this->load->view('backend/template/head');
		$this->load->view('backend/template/topbar');
		$this->load->view('backend/template/nav');
		$this->load->view('ruang/index', $data);
		$this->load->view('backend/template/js');
		$this->load->view('ruang/script');
    }
    
    function get_data_ruang()
	{
		$page = $_POST['start'] / $_POST['length'] + 1;
		$limit = $_POST['length'];
		$search = $_POST['search']['value'];
		if(!empty($search))
		{
			$url = api_url()."list-ruang?page=$page&limit=$limit&search=$search";
		}else
		{
			$url = api_url()."list-ruang?page=$page&limit=$limit";
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
			$row[] = $r->ruang;
			$row[] = $r->nomor;
			$row[] = $r->nama_pj;
			$row[] = $r->nip_pj;
			$action = '<div class="text-center">
						<a href="javascript:void(0)" class="btn btn-info btn-xs" title="EDIT DATA" onclick="edit_ruang('.$r->id_ruang.')"><i class="fa fa-edit"></i> EDIT</a>
						<a href="javascript:void(0)" class="btn btn-danger btn-xs" title="HAPUS DATA" onclick="delete_ruang('.$r->id_ruang.')"><i class="fa fa-trash"></i> HAPUS</a>
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
		exit();
    }
    
	function tambah_ruang()
	{ 
		$this->_validate();  
		$url = api_url().'tambah-ruang';
		$ruang = $this->input->post('ruang', true);
		$nomor = $this->input->post('nomor', true);
		$nama_pj = $this->input->post('nama_pj', true);
		$nip_pj = $this->input->post('nip_pj', true);

		$param = '{ "ruang":"'.$ruang.'", "nomor":"'.$nomor.'", "nama_pj":"'.$nama_pj.'", "nip_pj":"'.$nip_pj.'" }';
		
		$output = call_api_post($url, $param);
		echo $output;	
    }
    
    function get_ruang_by_id($id_ruang)
	{
		$url = api_url()."get_ruang_by_id/$id_ruang";  
		$output = call_api_get($url);
		echo $output;
	}

	function edit_ruang()
	{ 
		$this->_validate();
		$id_ruang = $this->input->post('id_ruang', true);
		$ruang = $this->input->post('ruang', true);
		$nomor = $this->input->post('nomor', true);
		$nama_pj = $this->input->post('nama_pj', true);
		$nip_pj = $this->input->post('nip_pj', true);
		$url = api_url()."edit-ruang/$id_ruang";
		
		$param = '{ "ruang":"'.$ruang.'", "nomor":"'.$nomor.'", "nama_pj":"'.$nama_pj.'", "nip_pj":"'.$nip_pj.'" }';
		
        $output = call_api_put($url, $param);
		echo $output;	
    }

	function hapus_ruang($id_ruang)
	{ 
		$url = api_url()."hapus-ruang/$id_ruang";
		$output = call_api_delete($url);		
		echo $output;
    }

	function cetak_ruang_pdf()
	{	
		$url = api_url()."listruang";
		$output = call_api_get($url);
		$dt = json_decode($output);

		$data['data'] = $dt->data;
		$this->load->library('pdfgenerator');
		$html = $this->load->view('ruang/cetak_ruang_pdf', $data, true);
		$filename = 'Ruang - Aplikasi Manajemen Barang SMK N 1 Puring';
		$this->pdfgenerator->generate($html, $filename, TRUE, 'A4', 'landscape');	
	}

	function cetak_ruang()
	{	
		$url = api_url()."listruang";
		$output = call_api_get($url);
		$dt = json_decode($output);
		
		$data['data'] = $dt->data;
		$this->load->view('ruang/cetak_ruang', $data);
	}

	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('ruang') == '')
		{
			$data['inputerror'][] = 'ruang';
			$data['error_string'][] = 'Ruang wajib diisi';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}
    
}