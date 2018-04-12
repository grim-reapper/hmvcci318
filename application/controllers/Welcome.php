<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller {

	public function index()
	{
		$this->load->view('welcome_v');
	}

	public function signup()
	{
		$this->load->library('form_validation');

		$config = array(
		        array(
		                'field' => 'user_firstname',
		                'label' => 'Nama Depan',
		                'rules' => 'trim|required'
		        ),
		        array(
		                'field' => 'user_password',
		                'label' => 'Password',
		                'rules' => 'trim|required',
		                'errors' => array(
		                        'required' => 'You must provide a %s.',
		                ),
		        ),
		        array(
		                'field' => 'passconf',
		                'label' => 'Password Confirmation',
		                'rules' => 'trim|required|matches[user_password]'
		        ),
		        array(
		                'field' => 'user_email',
		                'label' => 'Email',
		                'rules' => 'trim|required|valid_email|is_unique[users.user_email]'
		        )
		);

		$this->form_validation->set_rules($config);


		if ($this->form_validation->run() == FALSE)
        {
           	$this->session->set_flashdata('response', validation_errors());
            redirect();

        }

        $this->load->model('Users/M_Users');

        $data = $this->input->post();
        unset($data['passconf'],$data['submit']);

        $data['user_password']  = password_hash($data['user_password'], PASSWORD_BCRYPT);
        $data['user_name']		= $data['user_email'];

        $id = $this->M_Users->signup($data);

        if(!$id)
        {
        	$this->session->set_flashdata('response', 'Pendaftaran Gagal');
        	redirect();
        }

       	$this->session->set_flashdata('response', 'Pendaftaran Berhasil');
       	redirect();
        
	}

	public function login()
	{
		$this->load->library('form_validation');

		$config = array(
		       
		        array(
		                'field' => 'user_password',
		                'label' => 'Password',
		                'rules' => 'trim|required'
		        ),
		        array(
		                'field' => 'user_name',
		                'label' => 'Email',
		                'rules' => 'trim|required|valid_email'
		        )
		);

		$this->form_validation->set_rules($config);

		if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_flashdata('response', validation_errors());
        }

        $this->load->model('Users/M_Users');

        $user_name 		= $this->input->post('user_name');
        $password 		= $this->input->post('user_password');

       	$user = $this->M_Users->get_user($user_name);
		$hash = $user->user_password;

		$verified = $this->_verify_password_hash($password, $hash);

       	if(!$verified){
       		$this->session->set_flashdata('response', 'Login Gagal');
        	return redirect();
       	}

       	$ip_address = $this->input->ip_address();

        $session_user['loged_in'] 		= TRUE;
        $session_user['ip_address'] 	= $user->ip_address;
        $session_user['user_id'] 		= $user->user_id;
        $session_user['user_name'] 		= $user->user_name;
        $session_user['user_email'] 	= $user->user_email;
        $session_user['user_firstname'] = $user->user_firstname;
        $session_user['user_lastname'] 	= $user->user_lastname;


       	$this->session->set_userdata($session_user);

       	return redirect('Admin');
        
	}

	private function _verify_password_hash($password, $hash)
	{
		return password_verify($password, $hash);
	}
}
