<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		cek_login();
    } 
    
    function index()
	{	
        if($this->session->userdata('level') == 'superadmin')
        {
			$data['title'] = 'Master Users';	
			//layout
			$this->load->view('backend/template/head');
			$this->load->view('backend/template/topbar');
			$this->load->view('backend/template/nav');
			$this->load->view('user/index', $data);
			$this->load->view('backend/template/js');
			$this->load->view('user/script');
		}else
		{
			show_404();
		}
	}

	function get_data_users()
	{
		$page = $_POST['start'] / $_POST['length'] + 1;
		$limit = $_POST['length'];
		$search = $_POST['search']['value'];
		if(!empty($search))
		{
			$url = api_url()."listuser?page=$page&limit=$limit&search=$search";
		}else
		{
			$url = api_url()."listuser?page=$page&limit=$limit";
		}

		$output = call_api_get($url);   
		$dt = json_decode($output);

		$list = $dt->data;
		$data = array();
		$no = $_POST['start'];
        foreach($list as $r)
        {
			if($r->is_active == 1)
			{
				$status = '<span class="badge badge-primary">Aktif</span>';
			}else
			{
				$status = '<span class="badge badge-danger">Non Aktif</span>';
			}

			if($r->id_user == 1)
			{
				$aksi = '<a href="javascript:void(0)" class="btn btn-info btn-xs disabled"><i class="fa fa-edit"></i> EDIT</a>
				<a href="javascript:void(0)" class="btn btn-danger btn-xs disabled"><i class="fa fa-trash"></i> HAPUS</a>';
			}else
			{
				$aksi = '<a href="javascript:void(0)" class="btn btn-info btn-xs" title="EDIT DATA" onclick="edit_user('.$r->id_user.')"><i class="fa fa-edit"></i> EDIT</a>
				<a href="javascript:void(0)" class="btn btn-danger btn-xs" title="HAPUS DATA" onclick="delete_user('.$r->id_user.')"><i class="fa fa-trash"></i> HAPUS</a>';
			}

			$no++;
			$row = array();
			$row[] = '<div class="text-center">'.$no.'</div>';
			$row[] = $r->nama;
			$row[] = $r->nip;
			$row[] = $r->username;
			$row[] = $r->email;
			$row[] = $r->level;
			$row[] = $status;
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

		echo json_encode($output);
    }

    function tambah_user()
	{ 
		if($this->session->userdata('level') == 'superadmin')
        {
			$this->_validate_tambah();  
			$url = api_url().'tambah-user';
			$nama = $this->input->post('nama', true);
			$nip = $this->input->post('nip', true);
			$username = $this->input->post('username', true);
			$password = $this->input->post('password1', true);
			$email = $this->input->post('email', true);
			$level = $this->input->post('level', true);
			$is_active = $this->input->post('is_active', true);

			$param = '{ "nama":"'.$nama.'", "nip":"'.$nip.'", "username":"'.$username.'", "password":"'.$password.'", "email":"'.$email.'", "level":"'.$level.'", "is_active":"'.$is_active.'" }';
			
			$output = call_api_post($url, $param);
			echo $output;
		}else
		{
			show_404();
		}		
    }
    
    function get_user_by_id($id_user)
	{ 
        $url = api_url()."get_user_by_id/$id_user";
		$output = call_api_get($url);
		echo $output;
    }

    function edit_user()
	{ 
		if($this->session->userdata('level') == 'superadmin')
        {
			$id_user = $this->input->post('id_user',TRUE); 
			$username = $this->input->post('username',TRUE); 
			$email = $this->input->post('email',TRUE); 
			$this->_validate_edit($id_user, $username, $email); 
			$nama = $this->input->post('nama', true);
			$nip = $this->input->post('nip', true);
			$password = $this->input->post('password', true);
			$level = $this->input->post('level', true);
			$is_active = $this->input->post('is_active', true);

			$param = '{ "nama":"'.$nama.'", "nip":"'.$nip.'", "username":"'.$username.'", "password":"'.$password.'", "email":"'.$email.'", "level":"'.$level.'", "is_active":"'.$is_active.'" }';
			
			$url = api_url()."edit-user/$id_user";
			$output = call_api_put($url, $param);			
			echo $output;
		}else
		{
			show_404();
		}		
    }

    function hapus_user($id_user)
	{ 
		if($this->session->userdata('level') == 'superadmin')
        {
			$url = api_url()."hapus-user/$id_user";
			$output = call_api_delete($url);
			echo $output; 
		}else
		{
			show_404();
		} 
    }

	function edit_profil($id_user)
	{ 
		$username = $this->input->post('username',TRUE); 
		$email = $this->input->post('email',TRUE); 
		$this->_validate_profil($id_user, $username, $email); 
		$nama = $this->input->post('nama', true);
		$nip = $this->input->post('nip', true);
		
		$param = '{ "nama":"'.$nama.'", "nip":"'.$nip.'", "username":"'.$username.'", "email":"'.$email.'" }';
		
		$url = api_url()."edit-profil/$id_user";
		$output = call_api_put($url, $param);		
		echo $output;  
	}

	function ganti_password($id_user)
	{ 
		$pass1 = $this->input->post('pass1',TRUE); 
		$pass2 = $this->input->post('pass2',TRUE); 
		$pass3 = $this->input->post('pass3',TRUE); 
		$this->_validate_password($id_user, $pass1, $pass2, $pass3); 
		
		$param = '{ "password":"'.$pass1.'" }';
		
		$url = api_url()."ganti-password/$id_user";
		$output = call_api_put($url, $param);		
		echo $output;     
	}
	
	private function _validate_tambah()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		$username = $this->input->post('username');
		$cek_username = $this->_cek_username_tambah($username);

		$email = $this->input->post('email');
		$cek_email = $this->_cek_email_tambah($email);

		if($this->input->post('nama') == '')
		{
			$data['inputerror'][] = 'nama';
			$data['error_string'][] = 'Nama wajib diisi';
			$data['status'] = FALSE;
		}

		if($username == '')
		{
			$data['inputerror'][] = 'username';
			$data['error_string'][] = 'Username wajib diisi';
			$data['status'] = FALSE;
		}elseif($cek_username == FALSE)
		{
			$data['inputerror'][] = 'username';
			$data['error_string'][] = 'Username sudah digunakan!';
			$data['status'] = FALSE;
		}elseif(strlen($this->input->post('username')) < 5)
		{
			$data['inputerror'][] = 'username';
			$data['error_string'][] = 'Username minimal 5 karakter';
			$data['status'] = FALSE;
		}elseif(strlen($this->input->post('username')) > 30)
		{
			$data['inputerror'][] = 'username';
			$data['error_string'][] = 'Username maksimal 30 karakter';
			$data['status'] = FALSE;
		}

		if($this->input->post('password1') == '')
		{
			$data['inputerror'][] = 'password1';
			$data['error_string'][] = 'Password wajib diisi';
			$data['status'] = FALSE;
		}elseif(strlen($this->input->post('password1')) < 5)
		{
			$data['inputerror'][] = 'password1';
			$data['error_string'][] = 'Password minimal 5 karakter';
			$data['status'] = FALSE;
		}elseif(strlen($this->input->post('password1')) > 30)
		{
			$data['inputerror'][] = 'password1';
			$data['error_string'][] = 'Password maksimal 30 karakter';
			$data['status'] = FALSE;
		}

		if($this->input->post('password2') == '')
		{
			$data['inputerror'][] = 'password2';
			$data['error_string'][] = 'Ulang Password wajib diisi';
			$data['status'] = FALSE;
		}elseif(strlen($this->input->post('password2')) < 5)
		{
			$data['inputerror'][] = 'password2';
			$data['error_string'][] = 'Password minimal 5 karakter';
			$data['status'] = FALSE;
		}elseif(strlen($this->input->post('password2')) > 30)
		{
			$data['inputerror'][] = 'password2';
			$data['error_string'][] = 'Password maksimal 30 karakter';
			$data['status'] = FALSE;
		}

		if($this->input->post('password1') != $this->input->post('password2'))
		{
			$data['inputerror'][] = 'password2';
			$data['error_string'][] = 'Ulang Password harus sama';
			$data['status'] = FALSE;
		}

		if($email == '')
		{
			$data['inputerror'][] = 'email';
			$data['error_string'][] = 'Email wajib diisi';
			$data['status'] = FALSE;
		}elseif($cek_email == FALSE)
		{
			$data['inputerror'][] = 'email';
			$data['error_string'][] = 'Email sudah digunakan!';
			$data['status'] = FALSE;
		}elseif(strlen($email) < 5)
		{
			$data['inputerror'][] = 'email';
			$data['error_string'][] = 'Email minimal 5 karakter';
			$data['status'] = FALSE;
		}elseif(strlen($email) > 100)
		{
			$data['inputerror'][] = 'email';
			$data['error_string'][] = 'Email maksimal 30 karakter';
			$data['status'] = FALSE;
		}

		if($this->input->post('level') == '')
		{
			$data['inputerror'][] = 'level';
			$data['error_string'][] = 'Level User wajib diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('is_active') == '')
		{
			$data['inputerror'][] = 'is_active';
			$data['error_string'][] = 'Status Aktif wajib diisi';
			$data['status'] = FALSE;
		}
		
		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	function _cek_username_tambah($username = '')
    {
		$url = api_url()."get_user_by_username/$username";
		$output = call_api_get($url);
		$dt = json_decode($output);
		
		if($dt->status == true)
		{
			return false;
		}else
		{
			return true;
		}
	}

	function _cek_email_tambah($email = '')
    {
	    $url = api_url()."get_user_by_email/$email";
		$output = call_api_get($url);
		$dt = json_decode($output);
		
		if($dt->status == true)
		{
			return false;
		}else
		{
			return true;
		}
	}

	private function _validate_edit($id_user, $username, $email)
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		$cek_username = $this->_cek_username_edit($username, $id_user);
		$cek_email = $this->_cek_email_edit($email, $id_user);

		if($this->input->post('nama') == '')
		{
			$data['inputerror'][] = 'nama';
			$data['error_string'][] = 'Nama wajib diisi';
			$data['status'] = FALSE;
		}

		if($username == '')
		{
			$data['inputerror'][] = 'username';
			$data['error_string'][] = 'Username wajib diisi';
			$data['status'] = FALSE;
		}elseif($cek_username == FALSE)
		{
			$data['inputerror'][] = 'username';
			$data['error_string'][] = 'Username sudah digunakan!';
			$data['status'] = FALSE;
		}elseif(strlen($username) < 5)
		{
			$data['inputerror'][] = 'username';
			$data['error_string'][] = 'Username minimal 5 karakter';
			$data['status'] = FALSE;
		}elseif(strlen($username) > 30)
		{
			$data['inputerror'][] = 'username';
			$data['error_string'][] = 'Username maksimal 30 karakter';
			$data['status'] = FALSE;
		}

		if($email == '')
		{
			$data['inputerror'][] = 'email';
			$data['error_string'][] = 'Email wajib diisi';
			$data['status'] = FALSE;
		}elseif($cek_email == FALSE)
		{
			$data['inputerror'][] = 'email';
			$data['error_string'][] = 'Email sudah digunakan!';
			$data['status'] = FALSE;
		}elseif(strlen($email) < 5)
		{
			$data['inputerror'][] = 'email';
			$data['error_string'][] = 'Email minimal 5 karakter';
			$data['status'] = FALSE;
		}elseif(strlen($email) > 100)
		{
			$data['inputerror'][] = 'email';
			$data['error_string'][] = 'Email maksimal 30 karakter';
			$data['status'] = FALSE;
		}

		if($this->input->post('level') == '')
		{
			$data['inputerror'][] = 'level';
			$data['error_string'][] = 'Level User wajib diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('is_active') == '')
		{
			$data['inputerror'][] = 'is_active';
			$data['error_string'][] = 'Status Aktif wajib diisi';
			$data['status'] = FALSE;
		}
		
		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	private function _validate_profil($id_user, $username, $email)
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		$cek_username = $this->_cek_username_edit($username, $id_user);
		$cek_email = $this->_cek_email_edit($email, $id_user);

		if($this->input->post('nama') == '')
		{
			$data['inputerror'][] = 'nama';
			$data['error_string'][] = 'Nama wajib diisi';
			$data['status'] = FALSE;
		}

		if($username == '')
		{
			$data['inputerror'][] = 'username';
			$data['error_string'][] = 'Username wajib diisi';
			$data['status'] = FALSE;
		}elseif($cek_username == FALSE)
		{
			$data['inputerror'][] = 'username';
			$data['error_string'][] = 'Username sudah digunakan!';
			$data['status'] = FALSE;
		}elseif(strlen($username) < 5)
		{
			$data['inputerror'][] = 'username';
			$data['error_string'][] = 'Username minimal 5 karakter';
			$data['status'] = FALSE;
		}elseif(strlen($username) > 30)
		{
			$data['inputerror'][] = 'username';
			$data['error_string'][] = 'Username maksimal 30 karakter';
			$data['status'] = FALSE;
		}

		if($email == '')
		{
			$data['inputerror'][] = 'email';
			$data['error_string'][] = 'Email wajib diisi';
			$data['status'] = FALSE;
		}elseif($cek_email == FALSE)
		{
			$data['inputerror'][] = 'email';
			$data['error_string'][] = 'Email sudah digunakan!';
			$data['status'] = FALSE;
		}elseif(strlen($email) < 5)
		{
			$data['inputerror'][] = 'email';
			$data['error_string'][] = 'Email minimal 5 karakter';
			$data['status'] = FALSE;
		}elseif(strlen($email) > 100)
		{
			$data['inputerror'][] = 'email';
			$data['error_string'][] = 'Email maksimal 30 karakter';
			$data['status'] = FALSE;
		}
		
		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	function _cek_username_edit($username = '', $id_user = '')
    {
		$url = api_url()."validasi_edit_username/$username/$id_user";
		$output = call_api_get($url);
		$dt = json_decode($output);
		
		return $dt->status;
    }

	function _cek_email_edit($email = '', $id_user = '')
    {
		$url = api_url()."validasi_edit_email/$email/$id_user";
		$output = call_api_get($url);
		$dt = json_decode($output);
		
		return $dt->status;
	}

	private function _validate_password($id_user, $pass1, $pass2, $pass3)
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		$cek_password_lama = $this->_cek_password_lama($pass3, $id_user);

		if($pass1 == '')
		{
			$data['inputerror'][] = 'pass1';
			$data['error_string'][] = 'Password Baru wajib diisi';
			$data['status'] = FALSE;
		}elseif(strlen($pass1) < 5)
		{
			$data['inputerror'][] = 'pass1';
			$data['error_string'][] = 'Password minimal 5 karakter';
			$data['status'] = FALSE;
		}elseif(strlen($pass1) > 30)
		{
			$data['inputerror'][] = 'pass1';
			$data['error_string'][] = 'Password maksimal 30 karakter';
			$data['status'] = FALSE;
		}

		if($pass2 == '')
		{
			$data['inputerror'][] = 'pass2';
			$data['error_string'][] = 'Ulang Password Baru wajib diisi';
			$data['status'] = FALSE;
		}elseif(strlen($pass2) < 5)
		{
			$data['inputerror'][] = 'pass2';
			$data['error_string'][] = 'Password minimal 5 karakter';
			$data['status'] = FALSE;
		}elseif(strlen($pass2) > 30)
		{
			$data['inputerror'][] = 'pass2';
			$data['error_string'][] = 'Password maksimal 30 karakter';
			$data['status'] = FALSE;
		}

		if($pass3 == '')
		{
			$data['inputerror'][] = 'pass3';
			$data['error_string'][] = 'Password Lama wajib diisi';
			$data['status'] = FALSE;
		}elseif(strlen($pass3) < 5)
		{
			$data['inputerror'][] = 'pass3';
			$data['error_string'][] = 'Password minimal 5 karakter';
			$data['status'] = FALSE;
		}elseif(strlen($pass3) > 30)
		{
			$data['inputerror'][] = 'pass3';
			$data['error_string'][] = 'Password maksimal 30 karakter';
			$data['status'] = FALSE;
		}elseif($cek_password_lama == FALSE)
		{
			$data['inputerror'][] = 'pass3';
			$data['error_string'][] = 'Password Lama salah!';
			$data['status'] = FALSE;
		}

		if($this->input->post('pass1') != $this->input->post('pass2'))
		{
			$data['inputerror'][] = 'pass2';
			$data['error_string'][] = 'Ulang Password harus sama';
			$data['status'] = FALSE;
		}
		
		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	function _cek_password_lama($pass3 = '', $id_user = '')
    {
		$url = api_url()."get_user_by_id/$id_user";
		$output = call_api_get($url);
		$dt = json_decode($output);
						
		if(password_verify($pass3, $dt->password))
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
	}

}