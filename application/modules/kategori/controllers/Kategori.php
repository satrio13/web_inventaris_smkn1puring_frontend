<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Kategori extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		cek_login();
    } 

    function index()
	{	
		$data['title'] = 'Kategori Barang';
        //layout
		$this->load->view('backend/template/head');
		$this->load->view('backend/template/topbar');
		$this->load->view('backend/template/nav');
		$this->load->view('kategori/index', $data);
		$this->load->view('backend/template/js');
		$this->load->view('kategori/script');
    }

    function get_data_kategori()
	{
		$page = $_POST['start'] / $_POST['length'] + 1;
		$limit = $_POST['length'];
		$search = $_POST['search']['value'];
		if(!empty($search))
		{
			$url = api_url()."list-kategori?page=$page&limit=$limit&search=$search";
		}else
		{
			$url = api_url()."list-kategori?page=$page&limit=$limit";
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
			$row[] = $r->kategori;
			$row[] = $r->id_kategori;
			$action = '<div class="text-center">
						<a href="javascript:void(0)" class="btn btn-info btn-xs" title="EDIT DATA" onclick="edit_kategori('.$r->id_kategori.')"><i class="fa fa-edit"></i> EDIT</a>
						<a href="javascript:void(0)" class="btn btn-danger btn-xs" title="HAPUS DATA" onclick="delete_kategori('.$r->id_kategori.')"><i class="fa fa-trash"></i> HAPUS</a>
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

	function tambah_kategori()
	{ 
		$this->_validate();  
		$url = api_url().'tambah-kategori';
		$kategori = $this->input->post('kategori', true);

		$param = '{ "kategori":"'.$kategori.'" }';
		
		$output = call_api_post($url, $param);
		echo $output;	
    }

	function get_kategori_by_id($id_kategori)
	{
		$url = api_url()."get_kategori_by_id/$id_kategori";
		$output = call_api_get($url);
		echo $output;
	}

	function edit_kategori()
	{ 
		$this->_validate();
		$id_kategori = $this->input->post('id_kategori', true);
		$kategori = $this->input->post('kategori', true);
		$url = api_url()."edit-kategori/$id_kategori";
		
		$param = '{ "kategori":"'.$kategori.'" }';
		
		$output = call_api_put($url, $param);
		echo $output;	
    }

	function hapus_kategori($id_kategori)
	{ 
		$url = api_url()."hapus-kategori/$id_kategori";
		$output = call_api_delete($url);
		echo $output;
    }

	function cetak_kategori_pdf()
	{	
		$url = api_url()."listkategori";
		$output = call_api_get($url);
		$dt = json_decode($output);
		
		$data['data'] = $dt->data;
		$this->load->library('pdfgenerator');
		$html = $this->load->view('kategori/cetak_kategori_pdf', $data, true);
		$filename = 'Kategori Barang - Aplikasi Manajemen Barang SMK N 1 Puring';
		$this->pdfgenerator->generate($html, $filename, TRUE, 'A4', 'portrait');	
	}

	function cetak_kategori()
	{	
		$url = api_url()."listkategori";
		$output = call_api_get($url);
		$dt = json_decode($output);

		$data['data'] = $dt->data;
		$this->load->view('kategori/cetak_kategori', $data);	
	}

	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('kategori') == '')
		{
			$data['inputerror'][] = 'kategori';
			$data['error_string'][] = 'Kategori Barang wajib diisi';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

}