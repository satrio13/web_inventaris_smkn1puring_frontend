<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Backend extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		cek_login();
	} 

	function index()
	{	
		$data['title'] = 'Dashboard';
		$id_user = $this->session->userdata('id_user');
		
		$url = api_url()."get_user_by_id/$id_user";
		$output = call_api_get($url); 
		$user = json_decode($output);

		$url = api_url()."list-baranghp";
		$output = call_api_get($url); 
		$baranghp = json_decode($output);

		$url = api_url()."list-baranginv";
		$output = call_api_get($url); 
		$baranginv = json_decode($output);

		$url = api_url()."list-kategori";
		$output = call_api_get($url); 
		$kategori = json_decode($output);
		
		$url = api_url()."list-pengambilan?page=1";
		$output = call_api_get($url); 
		$pengambilan = json_decode($output);

		$url = api_url()."list-pemindahan?page=1";
		$output = call_api_get($url); 
		$pemindahan = json_decode($output);

		$data['user'] = $user;
		$data['jml_baranghp'] = $baranghp->totalRecords;
		$data['jml_baranginv'] = $baranginv->totalRecords;
		$data['jml_kategori'] = $kategori->totalRecords;
		$data['pengambilan'] = $pengambilan;
		$data['pemindahan'] = $pemindahan;
		//layout
		$this->load->view('backend/template/head');
		$this->load->view('backend/template/topbar');
		$this->load->view('backend/template/nav');
		$this->load->view('backend/home', $data);
		$this->load->view('backend/template/js');
		$this->load->view('backend/script');
	} 
	
}