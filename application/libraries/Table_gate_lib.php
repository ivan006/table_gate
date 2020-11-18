<?php
class Table_gate_lib
{
	private $CI;

	function __construct()
	{
		// $this->load->helper(array('form', 'url'));
		// $this->load->library('form_validation');

		$this->CI =& get_instance();
		$this->CI->load->database();
	}


	function db_to_configs()
	{



		$query = array(
			"SHOW TABLES",
		);
		$query = implode(" ", $query);
		$query_result = $this->CI->db->query($query)->result_array();


		// $query_result = array_column($query_result, 'Field');

		$tables = array();
		foreach ($query_result as $key => $value) {
			$value = reset($value);
			$tables[$value] = array();
		}
		// ksort($tables);


		$tables_and_fields = array();
		foreach ($tables as $key => $value) {

			$query = array(
				"SHOW COLUMNS FROM $key",
			);
			$query = implode(" ", $query);
			$query_result = $this->CI->db->query($query)->result_array();

			$fields = $query_result;

			$fields_result = array();
			foreach ($fields as $fields_key => $fields_value) {
				$fields_result["state_indicator_(option_".$fields_key.")"] = $fields_value["Field"];
			}
			// ksort($fields_result);


			$tables_and_fields[$key] = $fields_result;


			// $query = array(
			// 	"SHOW TABLE STATUS LIKE '$key'",
			// );
			// $query = implode(" ", $query);
			// $query_result = $this->CI->db->query($query)->result_array();
			// $Auto_increment = $query_result[0]["Auto_increment"];
			//
			// $tables_and_fields[$key]["auto_increment"] = $Auto_increment;

		}

		$result = json_encode($tables_and_fields, JSON_PRETTY_PRINT);
		return $result;

	}

	function configs()
	{
		$result = APPPATH.'g_table_gate/configs.json';
		// include($erd_two_path);
		$result = file_get_contents($result);
		$result = json_decode($result, true);


		$result = json_encode($result, JSON_PRETTY_PRINT);


		return $result;
	}

	function configs_to_state()
	{
		$tables = APPPATH.'g_table_gate/configs.json';
		// include($erd_two_path);
		$tables = file_get_contents($tables);
		$tables = json_decode($tables, true);

		// foreach ($variable as $key => $value) {
		// 	// code...
		// }
		$result = array();
		foreach ($tables as $key => $value) {
			// $result = $this->CI->db->get($key)->result();


			$items = $this->CI->db->select('count(*) as allcount')->from($key)->get()->result()[0]->allcount;
			$items_per_page = 100;
			$pages = ceil($items/$items_per_page);
			if ($pages == 0) {
				$pages = 1;
			}
			$sub_result = range(1, $pages, 1);
			$sub_result_2 = array();
			foreach ($sub_result as $page) {
				$start_item = ($page*$items_per_page)-$items_per_page;
				$sub_result_2[] = $this->CI->db->select($value)->from($key)->limit($items_per_page, $start_item)->get()->result_array();
				// $sub_result_2[] = $this->CI->db->select($value)->from($key)->limit($items_per_page, $start_item)->get()->result_array();

			}
			$result[$key] = $sub_result_2;





			// ## Total number of record with filtering
			// $this->CI->db->select('count(*) as allcount');
			// if($searchQuery != '')
			// $this->CI->db->where($searchQuery);
			// $records = $this->CI->db->get('employees')->result();
			// $totalRecordwithFilter = $records[0]->allcount;
			//
			// ## Fetch records
			// $this->CI->db->select('*');
			// if($searchQuery != '')
			// $this->CI->db->where($searchQuery);
			// $this->CI->db->order_by($columnName, $columnSortOrder);
			// $this->CI->db->limit($rowperpage, $start);
			// $records = $this->CI->db->get('employees')->result();


		}


		$result = json_encode($result, JSON_PRETTY_PRINT);


		return $result;
	}



}
