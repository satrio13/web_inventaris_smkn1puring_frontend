<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Pengambilan extends CI_Controller 
{
	  function __construct()
    {
      parent::__construct();
      cek_login();
    } 

    function index()
	  {	
      $data['title'] = 'Transaksi Pengambilan Barang';
      //layout
      $this->load->view('backend/template/head');
      $this->load->view('backend/template/topbar');
      $this->load->view('backend/template/nav');
      $this->load->view('pengambilan/index', $data);
      $this->load->view('backend/template/js');
      $this->load->view('pengambilan/script');
    }

    function get_data_pengambilan()
	  {
      $page = $_POST['start'] / $_POST['length'] + 1;
      $limit = $_POST['length'];
      $search = $_POST['search']['value'];
      if(!empty($search))
      {
        $url = api_url()."list-baranghp?page=$page&limit=$limit&search=$search";
      }else
      {
        $url = api_url()."list-baranghp?page=$page&limit=$limit";
      }

      $output = call_api_get($url); 
      $dt = json_decode($output);

      $list = $dt->data;
      $data = array();
      $no = $_POST['start'];
      foreach ($list as $r)
      {	
        if($r->stok == 0)
        {
          $aksi = '<button class="btn btn-danger btn-xs item_add" data="'.$r->id_baranghp.'" disabled><i class="fa fa-cart-plus"></i> ADD</button>';
        }else
        {
          $aksi = '<button class="btn btn-danger btn-xs item_add" data="'.$r->id_baranghp.'"><i class="fa fa-cart-plus"></i> ADD</button>';
        }

        $no++;
        $row = array();
        $row[] = '<div class="text-center">'.$no.'</div>';
        $row[] = $r->kode_hp;
        $row[] = $r->barang;
        $row[] = $r->stok.' '.$r->satuan;
        $action = '<div class="text-center">'.$aksi.'</div>';
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
    
    function get_data_ambil()
	  {
      $page = $_POST['start'] / $_POST['length'] + 1;
      $limit = $_POST['length'];
      $search = $_POST['search']['value'];
      if(!empty($search))
      {
        $url = api_url()."list-pengambilan?page=$page&limit=$limit&search=$search";
      }else
      {
        $url = api_url()."list-pengambilan?page=$page&limit=$limit";
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
          $aksi = '<a href="javascript:void(0)" data="'.$r->kode_trans.'" class="btn bg-info btn-xs item_edit" title="EDIT DATA"><i class="fa fa-edit"></i> EDIT</a>
          <a href="javascript:void(0)" data="'.$r->kode_trans.'" class="btn bg-danger btn-xs item_hapus_pengambilan" title="HAPUS DATA"><i class="fa fa-trash"></i> HAPUS</a>';
        }else
        {
          $aksi = '';
        } 

        $no++;
        $row = array();
        $row[] = '<div class="text-center">'.$no.'</div>';
        $row[] = '<a href="javascript:void(0)" data="'.$r->kode_trans.'" class="text-bold item_detail" title="LIHAT DETAIL">'.$r->kode_trans.'</a>';
        $row[] = $r->nama;
        $row[] = $r->nama_pengambil;
        $row[] = date('d-m-Y', strtotime($r->tgl_keluar));
        $row[] = $r->jam_keluar;
        $action = '<div class="text-center"><a href="javascript:void(0)" data="'.$r->kode_trans.'" class="btn btn-primary btn-xs mr-1 item_detail" title="LIHAT DETAIL"><i class="fa fa-eye"></i> DETAIL</a>'.$aksi.'</div>';
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

    function data_cart()
    { 
      $id_user = $this->session->userdata('id_user');
      $url = api_url()."data-cart-ambil/$id_user";
      $output = call_api_get($url);
      $dt = json_decode($output);

      $data['data'] = $dt->data;
		  $this->load->view('pengambilan/data_cart', $data);
    }
    
    function get_pengambilan()
    {
      $kobar = $this->input->get('id');
      $id_user = $this->session->userdata('id_user');
      $url = api_url()."add-to-cart/$kobar/$id_user";
      $output = call_api_get($url);
      echo $output;
    }

    function get_terambil()
    {
      $kobar = $this->input->get('id');
      $id_user = $this->session->userdata('id_user');
      $url = api_url()."add-to-cart-edit/$kobar/$id_user";
      $output = call_api_get($url);
      echo $output;
    }

    function detail_pengambilan()
	  {	
      $kode_trans = $this->input->get('kode_trans');
      $url = api_url()."detail-pengambilan/$kode_trans";
      $output = call_api_get($url);
      echo $output;
    }
    
    function simpan_pengambilan()
    {
      $this->_validate();
      $url = api_url().'simpan-pengambilan';
      $kode_trans = $this->input->post('kode_trans', TRUE);
      $nama_pengambil = $this->input->post('nama_pengambil', TRUE);
      $tgl_keluar = $this->input->post('tgl_keluar', TRUE);
      $jam_keluar = $this->input->post('jam_keluar', TRUE);
      $id_user = $this->session->userdata('id_user');
      $id_baranghp = $this->input->post('id_baranghp', TRUE);
      $qty = $this->input->post('qty', TRUE); 

      $data = array(
        'kode_trans' => $kode_trans,
        'nama_pengambil' => $nama_pengambil,
        'tgl_keluar' => $tgl_keluar,
        'jam_keluar' => $jam_keluar,
        'id_user' => $id_user,
        'id_baranghp' => $id_baranghp,
        'qty' => $qty
      );

      $param = json_encode($data);

      $output = call_api_post($url, $param);
      echo $output;
    }

    function hapus_cart($id_baranghp)
    {
      $url = api_url()."hapus-cart-ambil/$id_baranghp";
			$output = call_api_delete($url);
      echo $output;
    }

    function hapus_batal()
    {
      $id_user = $this->session->userdata('id_user');
      $url = api_url()."delete-cart/$id_user";
      $output = call_api_delete($url);
      echo $output;
    }

    function cek_kode_trans($kode_trans)
    {
      $url = api_url()."cek-kode-trans/$kode_trans";
      $output = call_api_get($url);
      $dt = json_decode($output);

      return $dt->status;
    }

    function cetak_pengambilan_pdf($kode_trans)
    {	
      if($this->cek_kode_trans($kode_trans) == false)
      {
        show_404();
      }else
      {
        $url = api_url()."detail-pengambilan/$kode_trans";
        $output = call_api_get($url);
        $dt = json_decode($output, true);

        $url = api_url()."pengurusbarang";
        $output = call_api_get($url);
        $pengurus = json_decode($output);

        $data['data'] = $dt['data'];
        $data['pengurus'] = $pengurus[0];
        $this->load->library('pdfgenerator');
        $html = $this->load->view('pengambilan/cetak_pengambilan_pdf', $data, true);
        $filename = 'Detail Pengambilan Barang - Aplikasi Manajemen Barang SMK N 1 Puring';
        $this->pdfgenerator->generate($html, $filename, TRUE, 'A4', 'landscape');	
      }
    }

    function cetak_pengambilan($kode_trans)
    {	
      if($this->cek_kode_trans($kode_trans) == false)
      {
        show_404();
      }else
      {
        $url = api_url()."detail-pengambilan/$kode_trans";
        $output = call_api_get($url);
        $dt = json_decode($output, true);

        $url = api_url()."pengurusbarang";
        $output = call_api_get($url);
        $pengurus = json_decode($output);

        $data['data'] = $dt['data'];
        $data['pengurus'] = $pengurus[0];
        $this->load->view('pengambilan/cetak_pengambilan', $data);
      }
    }

    function hapus_pengambilan($kode_trans)
	  { 	
		  $url = api_url()."hapus-pengambilan/$kode_trans";
      $output = call_api_delete($url);      
      echo $output;
    }

    private function _validate()
    {
      $data = array();
      $data['error_string'] = array();
      $data['inputerror'] = array();
      $data['status'] = TRUE;

      if($this->input->post('nama_pengambil') == '')
      {
        $data['inputerror'][] = 'nama_pengambil';
        $data['error_string'][] = 'Nama Pengambil wajib diisi';
        $data['status'] = FALSE;
      }

      if($this->input->post('tgl_keluar') == '')
      {
        $data['inputerror'][] = 'tgl_keluar';
        $data['error_string'][] = 'Tgl Pengambilan wajib diisi';
        $data['status'] = FALSE;
      }

      if($this->input->post('jam_keluar') == '')
      {
        $data['inputerror'][] = 'jam_keluar';
        $data['error_string'][] = 'Jam Pengambilan wajib diisi';
        $data['status'] = FALSE;
      }

      if($data['status'] === FALSE)
      {
        echo json_encode($data);
        exit();
      }
    }
}