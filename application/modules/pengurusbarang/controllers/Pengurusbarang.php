<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Pengurusbarang extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		cek_login();
    } 

    function index()
	{	
		$token = $this->session->userdata('token');
		$url = api_url()."list-pengurusbarang";
		$output = call_api_get($url);
		$dt = json_decode($output);

		$data['title'] = 'Pengurus Barang';
		$data['pengurus'] = $dt;
		//layout
		$this->load->view('backend/template/head');
		$this->load->view('backend/template/topbar');
		$this->load->view('backend/template/nav');
		$this->load->view('pengurusbarang/index', $data);
		$this->load->view('backend/template/js');
		$this->load->view('pengurusbarang/script');
    }

	function get_data_pengurusbarang()
	{
		$url = api_url()."pengurusbarang"; 
		$output = call_api_get($url);
		$dt = json_decode($output);

		$list = $dt;
		$data = array();
		$no = $_POST['start'];
        foreach($list as $r)
        {
			$no++;
			$row = array();
			$row[] = '<div class="text-center">'.$no.'</div>';
			$row[] = $r->nama;
			$action = '<div class="text-center">
						<a href="javascript:void(0)" class="btn btn-info btn-xs" title="EDIT DATA" onclick="edit_pengurusbarang()"><i class="fa fa-edit"></i> EDIT</a>
					  </div>';
			$row[] = $action;
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => count($dt),
			"recordsFiltered" => count($dt),
			"data" => $data,
		);
		//output dalam format JSON
		echo json_encode($output);
    }

    function edit_pengurusbarang()
	{ 
		$this->_validate();  
		$url = api_url().'edit-pengurusbarang';
		$id_user = $this->input->post('id_user', true);
		$param = '{ "id_user":"'.$id_user.'" }';
		$output = call_api_post($url, $param);		
		echo $output;	
    }

	function get_pengurusbarang_by_id()
	{
		$url = api_url()."get_pengurusbarang_by_id";
		$output = call_api_get($url);
		echo $output;
	}

	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('id_user') == '')
		{
			$data['inputerror'][] = 'id_user';
			$data['error_string'][] = 'Pengurus Barang wajib diisi';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

}