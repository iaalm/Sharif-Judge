<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Sharif Judge online judge
 * @file profile.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Profile extends CI_Controller{
	var $username;
	var $assignment;
	var $user_level;
	var $form_status;
	public function __construct(){
		parent::__construct();
		if ( ! $this->session->userdata('logged_in')){ // if not logged in
			redirect('login');
		}
		$this->username = $this->session->userdata('username');
		$this->assignment = $this->assignment_model->assignment_info($this->user_model->selected_assignment($this->username));
		$this->user_level = $this->user_model->get_user_level($this->username);
		$this->form_status = "";
	}

	public function index(){

		$this->load->model('user_model');
		$user=$this->user_model->get_user($this->username);
		$data = array(
			'username'=>$this->username,
			'user_level' => $this->user_level,
			'all_assignments'=>$this->assignment_model->all_assignments(),
			'assignment' => $this->assignment,
			'title'=>'Profile',
			'style'=>'main.css',
			'email' => $user->email,
			'display_name' => $user->display_name,
			'form_status' => $this->form_status
		);

		$this->load->view('templates/header',$data);
		$this->load->view('pages/profile',$data);
		$this->load->view('templates/footer');
	}

	public function _password_check($str){
		if (strlen($str)==0 OR (strlen($str)>=6 && strlen($str)<=30))
			return TRUE;
		return FALSE;
	}

	public function _email_check($email){ // checks whether a user with this email exists (used for validating registration)
		if ($this->user_model->have_email($email,$this->username))
			return FALSE;
		return TRUE;
	}

	public function update(){
		$this->load->model('user_model');
		$this->form_validation->set_message('_email_check','User with same %s exists.');
		$this->form_validation->set_message('_password_check','Password must be between 6 and 30 characters in length.');
		$this->form_validation->set_rules('display_name','Display Name','max_length[40]|xss_clean|strip_tags');
		$this->form_validation->set_rules('email','Email Address','required|max_length[40]|valid_email|callback__email_check');
		$this->form_validation->set_rules('password','Password','callback__password_check|alpha_numeric');
		$this->form_validation->set_rules('password_again','Password Confirmation','matches[password]');
		if ($this->form_validation->run()){
			$this->user_model->update_profile();
			$this->form_status = "ok";
		}
		else
			$this->form_status = "error";
		$this->index();
	}
}