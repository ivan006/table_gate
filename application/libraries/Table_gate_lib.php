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


			$prime_key_index = array_search('PRI', array_column($query_result, 'Key'));
			$tables_and_fields[$key]["primary_key"] = $query_result[$prime_key_index]["Field"];

			$fields = $query_result;

			$tables_and_fields[$key]["state_indicator"] = "";

			$fields_result = array();
			foreach ($fields as $fields_key => $fields_value) {
				$tables_and_fields[$key]["state_indicator_(option_".$fields_key.")"] = $fields_value["Field"];
			}
			// ksort($fields_result);


			// $tables_and_fields[$key] = $fields_result;



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

	function configs_to_state_json()
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


			// $items = $this->CI->db->select('count(*) as allcount')->from($key)->get()->result()[0]->allcount;


			$query = array(
				"SHOW TABLE STATUS WHERE `name` LIKE '$key' ;",
			);
			$query = implode(" ", $query);

			$items = $this->CI->db->query($query)->result_array()[0]["Auto_increment"];
			// var_dump($items);
			// exit;

			$items_per_page = 100;
			$pages = ceil($items/$items_per_page);
			if ($pages == 0) {
				$pages = 1;
			}



			$query_result = $this->CI->db->select($value["state_indicator"].",".$value["primary_key"])->from($key)->get()->result_array();

			$query_result_keys = array_column($query_result, $value["primary_key"]);
			$query_result_values = array_column($query_result, $value["state_indicator"]);

			$query_result_2 = array();
			$query_result_2 = array_combine($query_result_keys,$query_result_values);

			// ->limit($items_per_page-1, $start_item-1)


			$sub_result = range(1, $pages, 1);
			$sub_result_2 = array();

			$current_page = 0;
			$max_pages = -1; // -1 = infinity
			foreach ($sub_result as $page) {
				$end_item = $page*$items_per_page;
				$start_item = $end_item-$items_per_page+1;

				$rows = array();
				if ($current_page < $max_pages OR $max_pages == -1) {
					if (1==1) {
						// $rows = $this->CI->db->select($value["state_indicator"].",".$value["primary_key"])->from($key)->limit($items_per_page-1, $start_item-1)->get()->result_array();
						//
						// $query_result_keys = array_column($rows, $value["primary_key"]);
						// $query_result_values = array_column($rows, $value["state_indicator"]);
						// $rows = array_combine($query_result_keys,$query_result_values);
					}

					$rows_range  = range($start_item, $end_item, 1);
					$rows_range = array_fill_keys($rows_range,"");
					$rows = array_intersect_key($query_result_2, $rows_range);
					ksort($rows);
				}
				// $sub_result_2[$start_item."-".$end_item]["pages"] = "$items/$items_per_page";
				$sub_result_2[$start_item."-".$end_item] = $rows;


				$current_page = $current_page+1;

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

	function state_dir_to_state_json()
	{
		$dir = APPPATH.'g_table_gate/state';
		$dir_scandir = scandir($dir);

		$database_name = $this->CI->db->database;

		// echo "<pre>";
		// var_dump($database_name);
		// exit;

		$haystack = $dir_scandir;

		// array_key_exists\s*(\s*'|$['|\S]\s*,([^)]*))
		// echo $database_name;
		// $haystack = "owlbluegpyuty_db5__sss";

		$dir_and_timestamp_for_db = $this->state_dir_to_state_json_helper($haystack, $database_name);

		if ($dir_and_timestamp_for_db == false) {

			$now = date('Y-m-d H-i-s');
			$db_dir = $database_name."_TS_".$now."_TS";
			mkdir($dir."/".$db_dir);

		} else {

			$haystack = scandir($dir."/".$dir_and_timestamp_for_db["dir"]);
			$db_dir = $dir_and_timestamp_for_db["dir"];

			$configs = $this->configs();
			$configs = json_decode($configs);


			foreach ($configs as $table => $value) {
				$dir_and_timestamp_table = $this->state_dir_to_state_json_helper($haystack, $table);

				if ($dir_and_timestamp_table == false) {

					$now = date('Y-m-d H-i-s');
					$table_dir = $table."_TS_".$now."_TS";
					mkdir($dir."/".$db_dir."/".$table_dir);
				}	else {
					// code...
				}


				// echo "<pre>";
				// var_dump($dir_scandir);
				// exit;
			}


		}

		return 123;

	}

	function state_dir_to_state_json_helper($haystack, $entity_name)
	{

		$matches  = preg_grep ('/'.$entity_name.'_TS_(.*?)_TS/i', $haystack);

		if (!empty($matches)) {
			$matches = reset($matches);
			preg_match('/'.$entity_name.'_TS_(.*?)_TS/i', $matches, $matches);
			$last_update = $matches[1];

			$db_dir = $entity_name."_TS_".$last_update."_TS";

			$result = array(
				'dir' => $db_dir,
				'last_update' => $last_update,
			);
		} else {
			$result = false;
		}

		return $result;

	}

	function state_json_to_state_dir()
	{
		$state_json = $this->configs_to_state_json();


		return $state_json;

	}



}
