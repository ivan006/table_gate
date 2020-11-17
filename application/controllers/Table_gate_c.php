<?php
class Table_gate_c extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		// $this->load->helper(array('form', 'url'));
		// $this->load->library('form_validation');
		// // $this->load->model('trip');
		// // $this->load->library('../modules/trips/controllers/table_page_lib');
		// $this->load->library('table_page_lib');


		$this->load->database();
		$this->load->library(['ion_auth', 'form_validation']);
		$this->load->helper(['url', 'language']);
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
		$this->lang->load('auth');

	}

	public function index()
	{
		if (!$this->ion_auth->logged_in())
		{
			// redirect them to the login page
			redirect('auth/login', 'refresh');
		}

		// $data['tables'] = $this->table_page_lib->database_api();
		//
		//
		//
		//
		// $data["rows"]["visible"] = array("name"=>array());
		// $data["overview"]["table_id"] = "";
		// $data["data_endpoint"] = "database_api";
		// $data['title'] = "Database";
		// $this->load->view('table_header_v', $data);
		// $this->load->view('table_block_readonly_v', $data);
		// $this->load->view('table_footer_v');

		$this->load->view('header_v', array("title"=>"Table gate"));
		$this->load->view('table_gate_v', array());
		$this->load->view('footer_v');

	}


}
