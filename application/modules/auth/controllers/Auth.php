<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Auth extends CI_Controller 
{
	function __construct()
    {
		parent::__construct();
	} 

	function index()
	{	
		redirect('auth/login');
    }
    
    function login()
    {
        $this->load->view('login');
    }

    function aksi_login()
	{	
        $url = api_url().'login';
		$username = $this->input->post('username', true);
		$password = $this->input->post('password', true);

		$param = '{ "username":"'.$username.'", "password":"'.$password.'" }';
		
    	$output = call_api_post($url, $param);
        $data = json_decode($output, true);
        if($data['status'] == true)
        {
            $datauser = [
                'id_user' => $data['data']['id_user'],
                'nama' => $data['data']['nama'],
                'level' => $data['data']['level'],
                'iat' => $data['data']['iat'],
                'exp' => $data['data']['exp'],
                'token' => $data['token'],
                'login' => TRUE
            ];
            $this->session->set_userdata($datauser); 
            redirect('backend');
        }else
        {
            $alert = '<i class="fa fa-exclamation-triangle"></i> ';
            if(is_array($data['message']))
            {
                $message = $alert.$data['message']['username'].'<br>'.$alert.$data['message']['password'];
            }else
            {
                $message = $alert.$data['message'];
            }

            $this->session->set_flashdata('msg-lg', $message); 
            redirect('auth/login');   
        }
    }

    function cek_login()
    { 
        if(!$this->session->userdata('id_user'))
        { 
            redirect('auth/login');
        }
    }

    function logout()
    {   
        $this->cek_login();
        $this->session->sess_destroy();
        redirect('auth/login');
    }

}