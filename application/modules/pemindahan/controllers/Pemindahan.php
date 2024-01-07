<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Pemindahan extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		cek_login();
    } 

    function index()
	{	
        $data['title'] = 'Transaksi Pemindahan Barang';
		//layout
		$this->load->view('backend/template/head');
		$this->load->view('backend/template/topbar');
		$this->load->view('backend/template/nav');
		$this->load->view('pemindahan/index', $data);
		$this->load->view('backend/template/js');
		$this->load->view('pemindahan/script'); //$this->load->view('pemindahan/script2');
	}
	
	function get_data_pemindahan()
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
			$get = $this->posisi_barang($r->id_baranginv);
			if($get)
			{
				$ruang = $get->ruang;
			}else
			{
				$ruang = '';
			}
			
			$no++;
			$row = array();
			$row[] = '<div class="text-center">'.$no.'</div>';
			$row[] = $r->kode_inv;
			$row[] = $r->barang;
			$row[] = $ruang;
			$action = '<div class="text-center"><a href="javascript:void(0)" class="btn btn-danger btn-xs item_addinv" data="'.$r->id_baranginv.'"><i class="fa fa-cart-plus"></i> PINDAH</a></div>';
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

	function posisi_barang($id_baranginv)
	{	
		$url = api_url()."cek-posisi-baranginv/$id_baranginv";
		$output = call_api_get($url);
		return json_decode($output);
	}

	function get_data_pindah()
	{
		$page = $_POST['start'] / $_POST['length'] + 1;
		$limit = $_POST['length'];
		$search = urlencode($_POST['search']['value']);
		if(!empty($search))
		{
			$url = api_url()."list-pemindahan?page=$page&limit=$limit&search=$search";
		}else
		{
			$url = api_url()."list-pemindahan?page=$page&limit=$limit";
		}

		$output = call_api_get($url);
		$dt = json_decode($output);

		$list = $dt->data;
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $r)
		{	
			if($r->id_user == $this->session->userdata('id_user'))
			{
				$aksi = '<a href="javascript:void(0)" data="'.$r->kode_pindah.'" class="btn bg-info btn-xs item_edit_pemindahan" title="EDIT DATA"><i class="fa fa-edit"></i> EDIT</a>
				<a href="javascript:void(0)" data="'.$r->kode_pindah.'" class="btn bg-danger btn-xs item_hapus_pemindahan" title="HAPUS DATA"><i class="fa fa-trash"></i> HAPUS</a>';
			}else
			{
				$aksi = '';
			} 

			$no++;
			$row = array();
			$row[] = '<div class="text-center">'.$no.'</div>';
			$row[] = '<a href="javascript:void(0)" data="'.$r->kode_pindah.'" class="text-bold item_detail" title="LIHAT DETAIL">'.$r->kode_pindah.'</a>';
			$row[] = date('d-m-Y', strtotime($r->tgl_pindah));
			$row[] = $r->ruang;
			$action = '<div class="text-center"><a href="javascript:void(0)" data="'.$r->kode_pindah.'" class="btn btn-primary btn-xs mr-1 item_detail" title="LIHAT DETAIL"><i class="fa fa-eye"></i> DETAIL</a>'.$aksi.'</div>';
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

	function data_cart_pemindahan()
	{
		$id_user = $this->session->userdata('id_user');
		$url = api_url()."data-cart-pindah/$id_user";
		$output = call_api_get($url);
		$dt = json_decode($output);

		$data['data'] = $dt->data;
		$this->load->view('pemindahan/data_cart', $data);
	}

	function get_pemindahan()
	{
		$id_baranginv = $this->input->get('id');
		$id_user = $this->session->userdata('id_user');
		$url = api_url()."add-to-cart-pemindahan/$id_baranginv/$id_user";
		$output = call_api_get($url);
		echo $output;
	}

	function get_terpindah()
	{
		$kode_pindah = $this->input->get('id');
		$id_user = $this->session->userdata('id_user');
		$url = api_url()."add-to-cart-edit-pemindahan/$kode_pindah/$id_user";
		$output = call_api_get($url);
		echo $output;
	}
	
	function simpan_pemindahan()
	{
		$this->_validate();
		$url = api_url().'simpan-pemindahan';
		$kode_pindah = $this->input->post('kode_pindah',TRUE);
		$tgl_pindah = $this->input->post('tgl_pindah',TRUE);
		$id_ruang = $this->input->post('id_ruang',TRUE);
		$id_kondisi = $this->input->post('id_kondisi',TRUE);
		$id_baranginv = $this->input->post('id_baranginv',TRUE);
		$id_user = $this->session->userdata('id_user');

		$data = array(
			'kode_pindah' => $kode_pindah,
			'tgl_pindah' => $tgl_pindah,
			'id_ruang' => $id_ruang,
			'id_kondisi' => $id_kondisi,
			'id_baranginv' => $id_baranginv,
			'id_user' => $id_user
		);
	
		$param = json_encode($data);

		$output = call_api_post($url, $param);
		echo $output;
	}

	function hapus_cart_pemindahan($id_baranginv)
	{
		$url = api_url()."hapus-cart-pindah/$id_baranginv";
		$output = call_api_delete($url);
		echo $output;
	}

	function hapus_batal_pemindahan()
	{
		$id_user = $this->session->userdata('id_user');
		$url = api_url()."delete-cart-pindah/$id_user";
		$output = call_api_delete($url);
		echo $output;
	}

    function detail_pemindahan()
	{	
		$kode_pindah = $this->input->get('kode_pindah');
		$url = api_url()."detail-pemindahan/$kode_pindah";
		$output = call_api_get($url);
		echo $output;
    }
	
	function cetak_pemindahan_pdf($kode_pindah)
    {	
		if($this->cek_kode_pindah($kode_pindah) == false)
		{
			show_404();
		}else
		{
			$url = api_url()."detail-pemindahan/$kode_pindah";
			$output = call_api_get($url);
			$dt = json_decode($output, true);

			$url = api_url()."pengurusbarang";
			$output = call_api_get($url);
			$pengurus = json_decode($output);

			$data['data'] = $dt['data'];
			$data['pengurus'] = $pengurus[0];
			$this->load->library('pdfgenerator');
			$html = $this->load->view('pemindahan/cetak_pemindahan_pdf', $data, true);
			$filename = 'Detail Pemindahan Barang - Aplikasi Manajemen Barang SMK N 1 Puring';
			$this->pdfgenerator->generate($html, $filename, TRUE, 'A4', 'landscape');	
		}
	}

	function cetak_pemindahan($kode_pindah)
    {	
		if($this->cek_kode_pindah($kode_pindah) == false)
		{
			show_404();
		}else
		{
			$url = api_url()."detail-pemindahan/$kode_pindah";
			$output = call_api_get($url);
			$dt = json_decode($output, true);

			$url = api_url()."pengurusbarang";
			$output = call_api_get($url);
			$pengurus = json_decode($output);

			$data['data'] = $dt['data'];
			$data['pengurus'] = $pengurus[0];
			$this->load->view('pemindahan/cetak_pemindahan', $data);	
		}
	}

	function hapus_pemindahan($kode_pindah)
	{ 	
		$url = api_url()."hapus-pemindahan/$kode_pindah";
		$output = call_api_delete($url);
		echo $output;
	}

	function cek_kode_pindah($kode_pindah)
    {
		$url = api_url()."cek-kode-pindah/$kode_pindah";
		$output = call_api_get($url);
		$dt = json_decode($output);

		return $dt->status;
    }

	private function _validate()
    {
      $data = array();
      $data['error_string'] = array();
      $data['inputerror'] = array();
      $data['status'] = TRUE;

      if($this->input->post('id_ruang') == '')
      {
        $data['inputerror'][] = 'id_ruang';
        $data['error_string'][] = 'Ruang wajib diisi';
        $data['status'] = FALSE;
      }

      if($this->input->post('tgl_pindah') == '')
      {
        $data['inputerror'][] = 'tgl_pindah';
        $data['error_string'][] = 'Tgl Pemindahan wajib diisi';
        $data['status'] = FALSE;
      }

      if($data['status'] === FALSE)
      {
        echo json_encode($data);
        exit();
      }
    }

}
