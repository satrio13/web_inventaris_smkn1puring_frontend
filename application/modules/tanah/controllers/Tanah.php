<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Tanah extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		cek_login();
    } 

    function index()
	{	
		$data['title'] = 'Tanah';
		//layout
		$this->load->view('backend/template/head');
		$this->load->view('backend/template/topbar');
		$this->load->view('backend/template/nav');
		$this->load->view('tanah/index', $data);
		$this->load->view('backend/template/js');
		$this->load->view('tanah/script');
    }

    function get_data_tanah()
	{
		$page = $_POST['start'] / $_POST['length'] + 1;
		$limit = $_POST['length'];
		$search = $_POST['search']['value'];
		if(!empty($search))
		{
			$url = api_url()."list-tanah?page=$page&limit=$limit&search=$search";
		}else
		{
			$url = api_url()."list-tanah?page=$page&limit=$limit";
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
			$row[] = $r->tanah;
			$row[] = $r->luas;
			$row[] = $r->selatan;
			$row[] = $r->timur;
			$row[] = $r->barat;
			$row[] = $r->utara;
			$row[] = $r->tahun_p;
			$row[] = $r->sumberdana;
			$action = '<div class="text-center">
						<a href="javascript:void(0)" class="btn btn-info btn-xs" title="EDIT DATA" onclick="edit_tanah('.$r->id_tanah.')"><i class="fa fa-edit"></i> EDIT</a>
						<a href="javascript:void(0)" class="btn btn-danger btn-xs" title="HAPUS DATA" onclick="delete_tanah('.$r->id_tanah.')"><i class="fa fa-trash"></i> HAPUS</a>
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

	function tambah_tanah()
	{ 
		$this->_validate();  
		$url = api_url().'tambah-tanah';
		$tanah = $this->input->post('tanah', true);
		$luas = $this->input->post('luas', true);
		$selatan = $this->input->post('selatan', true);
		$timur = $this->input->post('timur', true);
		$barat = $this->input->post('barat', true);
		$utara = $this->input->post('utara', true);
		$tahun_p = $this->input->post('tahun_p', true);
		$sumberdana = $this->input->post('sumberdana', true);
		
		$param = '{ "tanah":"'.$tanah.'", "luas":"'.$luas.'", "selatan":"'.$selatan.'", "timur":"'.$timur.'", "barat":"'.$barat.'", "utara":"'.$utara.'", "tahun_p":"'.$tahun_p.'", "sumberdana":"'.$sumberdana.'" }';
		
		$output = call_api_post($url, $param);
		echo $output;	
    }

	function get_tanah_by_id($id_tanah)
	{
		$url = api_url()."get_tanah_by_id/$id_tanah";
		$output = call_api_get($url);
		echo $output;
	}

	function edit_tanah()
	{ 
		$this->_validate();
		$id_tanah = $this->input->post('id_tanah', true);
		$tanah = $this->input->post('tanah', true);
		$luas = $this->input->post('luas', true);
		$selatan = $this->input->post('selatan', true);
		$timur = $this->input->post('timur', true);
		$barat = $this->input->post('barat', true);
		$utara = $this->input->post('utara', true);
		$tahun_p = $this->input->post('tahun_p', true);
		$sumberdana = $this->input->post('sumberdana', true);
		$url = api_url()."edit-tanah/$id_tanah";
		
		$param = '{ "tanah":"'.$tanah.'", "luas":"'.$luas.'", "selatan":"'.$selatan.'", "timur":"'.$timur.'", "barat":"'.$barat.'", "utara":"'.$utara.'", "tahun_p":"'.$tahun_p.'", "sumberdana":"'.$sumberdana.'" }';
		
		$output = call_api_put($url, $param);
        echo $output;	
    }

	function hapus_tanah($id_tanah)
	{ 
		$url = api_url()."hapus-tanah/$id_tanah";
		$output = call_api_delete($url);		
		echo $output;
    }

	function cetak_tanah_pdf()
	{	
		$url = api_url()."listtanah"; 
		$output = call_api_get($url); 
		$dt = json_decode($output);

		$data['data'] = $dt->data;
		$this->load->library('pdfgenerator');
		$html = $this->load->view('tanah/cetak_tanah_pdf', $data, true);
		$filename = 'Tanah - Aplikasi Manajemen Barang SMK N 1 Puring';
		$this->pdfgenerator->generate($html, $filename, TRUE, 'A4', 'landscape');	
	}

	function cetak_tanah()
	{	
		$url = api_url()."listtanah"; 
		$output = call_api_get($url); 
		$dt = json_decode($output);
		
		$data['data'] = $dt->data;
		$this->load->view('tanah/cetak_tanah', $data);	
	}

	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('tanah') == '')
		{
			$data['inputerror'][] = 'tanah';
			$data['error_string'][] = 'Tanah wajib diisi';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

}