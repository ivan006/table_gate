<?php
class Erd_c extends CI_Controller
{

	function __construct()
	{
		parent::__construct();

		// $this->load->library('erd_lib');




		$this->load->database();
		$this->load->library(['erd_lib', 'ion_auth', 'form_validation']);
		$this->load->helper(['url', 'language']);
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
		$this->lang->load('auth');
	}

	function index()
	{
		if (!$this->ion_auth->logged_in())
		{
			// redirect them to the login page
			redirect('auth/login', 'refresh');
		}
		// echo "<pre>";
		$data["erd"] = $this->erd_lib->erd();
		$data["erd_to_db"] = $this->erd_lib->erd_to_db();
		$data["model_two"] = $this->erd_lib->model_two();
		$data["db_to_erd"] = $this->erd_lib->db_to_erd();
		$data["diff"] = $this->erd_lib->diff();


		// header('Content-Type: application/json');
		// echo json_encode($class);
		// exit;

		$this->load->view('erd_v', $data);
	}

}
