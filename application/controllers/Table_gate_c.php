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
		$this->load->library('table_gate_lib');


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


		$data = array();
		$data['config_boiler_plate'] = $this->table_gate_lib->config_boiler_plate();
		$data['configs'] = "";
		$data['generate_state'] = "";

		$data['configs'] = $this->table_gate_lib->configs();
		// $data['generate_state'] = $this->table_gate_lib->generate_state();

		$data['compare_local_and_remote_states'] = $this->table_gate_lib->compare_local_and_remote_states();


		$this->load->view('header_v', array("title"=>"Table gate"));
		$this->load->view('table_gate_v', $data);
		$this->load->view('footer_v');

	}

	public function read_state_api($type, $path)
	{
		$result = $this->table_gate_lib->read_state_api($type, $path);
		$result = json_encode($result, JSON_PRETTY_PRINT);
		header('Content-Type: application/json');
		echo $result;

	}


}
