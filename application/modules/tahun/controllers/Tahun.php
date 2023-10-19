<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Tahun extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		cek_login();
    } 

    function index()
	{	
		$data['title'] = 'Tahun';
		//layout
		$this->load->view('backend/template/head');
		$this->load->view('backend/template/topbar');
		$this->load->view('backend/template/nav');
		$this->load->view('tahun/index', $data);
		$this->load->view('backend/template/js');
		$this->load->view('tahun/script');
    }

	function get_data_tahun()
	{
		$page = $_POST['start'] / $_POST['length'] + 1;
		$limit = $_POST['length'];
		$search = $_POST['search']['value'];
		if(!empty($search))
		{
			$url = api_url()."list-tahun?page=$page&limit=$limit&search=$search";
		}else
		{
			$url = api_url()."list-tahun?page=$page&limit=$limit";
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
			$row[] = $r->tahun;
			$action = '<div class="text-center">
						<a href="javascript:void(0)" class="btn btn-info btn-xs" title="EDIT DATA" onclick="edit_tahun('.$r->id_tahun.')"><i class="fa fa-edit"></i> EDIT</a>
						<a href="javascript:void(0)" class="btn btn-danger btn-xs" title="HAPUS DATA" onclick="delete_tahun('.$r->id_tahun.')"><i class="fa fa-trash"></i> HAPUS</a>
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

	function tambah_tahun()
	{ 
		$this->_validate();  
		$url = api_url().'tambah-tahun';
		$tahun = $this->input->post('tahun', true);
		$param = '{ "tahun":"'.$tahun.'" }';
		$output = call_api_post($url, $param);
		echo $output;	
    }

	function get_tahun_by_id($id_tahun)
	{
		$url = api_url()."get_tahun_by_id/$id_tahun";
		$output = call_api_get($url); 
		echo $output;
	}

	function edit_tahun()
	{ 
		$this->_validate();
		$id_tahun = $this->input->post('id_tahun', true);
		$tahun = $this->input->post('tahun', true);
		$url = api_url()."edit-tahun/$id_tahun";
		$param = '{ "tahun":"'.$tahun.'" }';
		$output = call_api_put($url, $param);
		echo $output;	
    }

	function hapus_tahun($id_tahun)
	{ 
		$url = api_url()."hapus-tahun/$id_tahun";
		$output = call_api_delete($url);		
		echo $output;
    }

	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('tahun') == '')
		{
			$data['inputerror'][] = 'tahun';
			$data['error_string'][] = 'Tahun wajib diisi';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

}